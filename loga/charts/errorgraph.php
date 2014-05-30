<?php
if(isset($chartlabel)){unset($chartlabel);}
$period="Days";

if ($period=="Days") { 
	$qd="FROM_UNIXTIME(timestamp,'%a, %m/%d/%y')"; 
} else if ($period=="Weeks") { 
	$qd="FROM_UNIXTIME(t.timestamp,'%Y-%V')"; 
} else if ($period=="Months") { 
	$qd="FROM_UNIXTIME(t.timestamp,'%M')"; 
} 


  $query  = "select $qd AS days, t.status as status, concat(t.status,\" - \",p.descr) as ecode, count(t.visitorid) as requests from $profile->tablename as t, ".TBL_LGSTATUS." as p where t.timestamp >=$from and t.timestamp <=$to and t.status!=200 and t.status=p.code group by t.status,days order by t.status,t.timestamp asc";

//set up a proper date series to prevent holes
$n=($to-$from)/86400;
$i=0;
while ($i <= $n)
{
	$cdate=date("D, m/d/y", ($from+($i*86400)));
	$days[$cdate]=$i;
	
	$dayArr[$i] = $cdate;
	$i++;
}
  
$q = $db->Execute($query);
$i=0;
$range=0;
$laststatus = "";
$thismax = 0;
while ($refdata=$q->FetchRow())
{
	if ($refdata["ecode"]!=$laststatus)
	{
		$chartlabel[] = $refdata["ecode"];
		if($laststatus != "")
		{
			$range++;
		}
		
		//prefill with 0 to avoid holes
		$pi=0;
		while ($pi <= $n)
		{
			$rchart[ $range ][ $pi ]=0;
			$pi++;
		}
		$laststatus=$refdata["ecode"];
	}
	$rchart[ $range ][ ($days[$refdata["days"]]) ]=$refdata["requests"];
	
	$totalvisitors[($days[$refdata["days"]])]=@$totalvisitors[($days[$refdata["days"]])]+$refdata["requests"];
	if ($thismax < $refdata["requests"])
	{
		$thismax=$refdata["requests"];
	}
	$i++;
}
$i=0;
while (list ($key, $val) = each ($days))
{	
	//add a trend line with linear regression
	//$y=$a * $x + $b
	//required variables
	/*
	$a; calculated
	$b; calculated
	$x; in this case days (or months), just an iterating number in this case
	$y; the number of visitors, pages or whatever on $x day
	$n; Total number of datapoints
	$xy; x times y
	$xsq;  x squared
	$axb; $a * $x +$b
	*/

	// start trend;

	$x[$i]=$i;
	$y[$i]=@(@$totalvisitors[$i]/($range+1)); //this is average of all error code requests
	$xy[$i]=$x[$i] * $y[$i];
	$xsq[$i]=$x[$i] * $x[$i];

	$i++;
}

// $n has already been vetted to be <> 0, so we can divide by $n with impunity here
$a_part=array_sum($xy)-((array_sum($x)*array_sum($y))/$n);

// array_sum($x) isn't vetted, so we need to protect from a division by 0 here.
$a=@($a_part/(array_sum($xsq)-(1/$n)*(array_sum($x)*array_sum($x))));
$b=(array_sum($y)/$n)-($a * (array_sum($x)/$n));

//now we have eveything to plot our 2 regression lines

reset($days);
$i = 0;
while (list ($key, $val) = each ($days))
{
	$rchart[ $range+1 ][ ($val) ]=($a * $x[($i)]) + $b;
	$i++;
}

if ($debug)
{
	echo "Thismax:$thismax";
	echo "<pre>";
		print_r($rchart);
	echo "</pre>";
}
$chartlabel[] = ""._ERROR_TREND;
// end data build

// start chart build
$errorXML = '<graph
    caption="'._ERROR_CODES_OVER_TIME.'"
    yAxisMinValue="0"
    decimalPrecision="0"
    formatNumberScale="0"
    showNames="1"
    showValues="0"
    showLegend="0"
    showAlternateHGridColor="1"
    AlternateHGridColor="CCCCCC"
    divLineColor="EEEDED"
    divLineAlpha="100"
    alternateHGridAlpha="5"
    canvasBorderThickness="0"
    rotateNames="1"
    canvasBorderColor="EEEDED"
    chartLeftMargin="0"
    chartRightMargin="5"
    chartTopMargin="5"
    chartBottomMargin="0"
    canvasBaseDepth="0"
    canvasBaseWidth="0"
    canvasBgDepth="0"
    canvasBgColor="FFFFFF"
    animation="';$errorXML .= $profile->animate == 1 ? "0" : "1";$errorXML .= '">';
$errorXML .= '<categories>'."\n";
$timespan = floor(($to - $from) / 86400);
$f = 0;
for ($g = 0; $g < count($days); $g++)
{
	$prevWeek = date('W',$from+($g*86400)-86400);
	$newWeek = date('W',$from+($g*86400));
	$errorXML .= '<category name="'.$dayArr[$g].'"';
	if($timespan <= 8)
	{
		$errorXML .= ' showName="1"/>'."\n";
	}
	else
	{
		if($f == round($n/(round($n/($n/100*6)))))
		{
			$errorXML .= ' showName="1"/>'."\n";
			$f = 0;
		}
		else
		{
			if($g == 0)
			{
				$errorXML .= ' showName="1"/>'."\n";
			}
			else
			{
				$errorXML .= ' showName="0"/>'."\n";
			}
		}
	$f++;
	}
}
$errorXML .= '</categories>'."\n";
$FC_ColorCounter=0;
unset($color);
for ($h = 0; $h < count($rchart); $h++)
{
	$color[] = getFCColor();
	$errorXML .= '<dataset seriesName="'.$chartlabel[$h].'" color="'.$color[$h].'" anchorAlpha="0">'."\n";
	for ($i = 0; $i < count($rchart[$h]); $i++)
	{
		$errorXML .= '<set value="'.$rchart[$h][$i].'" alpha="75"/>'."\n";
	}
	$errorXML .= '</dataset>'."\n";
}
$errorXML .= '</graph>';

echo "<div class=\"lines\"  id=\"errorgraph\">";
	echo renderChartHTML("charts/FCF_MSLine.swf", "", urlencode($errorXML), "ergraph", 400, 250, false, false, "opaque"); 	
echo '<ul class="graphUl">';
for($i = 0; $i < count($chartlabel); $i++)
{
    echo '<li class="legend"><strong style="color: #'.$color[$i].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'."<a onclick=\"popupMenu(event, '".$chartlabel[$i]."', 'statuscode');\" href=\"#\">".$chartlabel[$i].'</a></li>';
}
echo '</ul>';
echo "</div>";
?>
