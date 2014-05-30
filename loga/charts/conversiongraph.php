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


  $prequery= "select $qd AS days, count(distinct visitorid) as requests from $profile->tablename where timestamp >=$from and timestamp <=$to group by days order by timestamp asc";
  $q = $db->Execute($prequery);
  $i=0;
  while ($pqdata=$q->FetchRow()) {
       $uniquevisitors[$i]=$pqdata["requests"];
       $i++;   
  }
  
  $query  = "select $qd AS days, u.url as url, count(distinct visitorid) as requests from $profile->tablename as a,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and a.url=u.id and $profile->targets_sql group by a.url,days order by u.url,timestamp asc";
  //echo $query . "<P>";

//set up a proper date series to prevent holes
$n=($to-$from)/86400;
$i=0;
// make a safefrom starts starts at noon, to calculate the day range because otherwise shifts in DST can fuck us up
$safefrom=mktime(12,0,0,date("m",$from),date("d",$from),date("Y",$from));
while ($i <= $n)
{
	$cdate=date("D, m/d/y", ($safefrom+($i*86400)));
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
	if ($refdata["url"]!=$laststatus)
	{
		$chartlabel[] = $refdata["url"];
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
		$laststatus=$refdata["url"];
	}
    /*
	$ctr=@(($refdata["requests"]/$uniquevisitors[($days[$refdata["days"]])])*100);
    
	$rchart[ $range ][ (@$days[$refdata["days"]]+1) ]=$ctr;
	
	$totalvisitors[(@$days[$refdata["days"]]+1)]=@$totalvisitors[(@$days[$refdata["days"]]+1)]+$ctr;
	*/
    $ctr=@(($refdata["requests"]/$uniquevisitors[($days[$refdata["days"]])])*100);
    $rchart[ $range ][ (@$days[$refdata["days"]]) ]=$ctr;
    
    $totalvisitors[(@$days[$refdata["days"]])]=@$totalvisitors[(@$days[$refdata["days"]])]+$ctr;
    
    if ($thismax < $ctr)
	{
		$thismax = $ctr;
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
$chartlabel[] = ""._CONVERSION_TREND;
// end data build

// start chart build
$congraphXML = '<graph
    caption="'._CONVERSION_RATE_AS_PERCENTAGE.'"
    yAxisMinValue="0"';
    if (max($rchart[1]) < 5) {
        $congraphXML .= 'yAxisMaxValue="5"';   
    }
    $congraphXML .= 'decimalPrecision="0"
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
    animation="';$congraphXML .= $profile->animate == 1 ? "0" : "1";$congraphXML .= '">';
$congraphXML .= '<categories>'."\n";
$timespan = floor(($to - $from) / 86400);
$f = 0;
for ($g = 0; $g < count($days); $g++)
{
	$prevWeek = date('W',$from+($g*86400)-86400);
	$newWeek = date('W',$from+($g*86400));
	$congraphXML .= '<category name="'.$dayArr[$g].'"';
	if($timespan <= 8)
	{
		$congraphXML .= ' showName="1"/>'."\n";
	}
	else
	{
		if($f == round($n/(round($n/($n/100*6)))))
		{
			$congraphXML .= ' showName="1"/>'."\n";
			$f = 0;
		}
		else
		{
			if($g == 0)
			{
				$congraphXML .= ' showName="1"/>'."\n";
			}
			else
			{
				$congraphXML .= ' showName="0"/>'."\n";
			}
		}
	$f++;
	}
}
$congraphXML .= '</categories>'."\n";
$FC_ColorCounter=0;
unset($color);
for ($h = 0; $h < count($rchart); $h++)
{
	$color[] = getFCColor();
	$congraphXML .= '<dataset seriesName="'.$chartlabel[$h].'" color="'.$color[$h].'" anchorAlpha="0">'."\n";
	for ($i = 0; $i < count($rchart[$h]); $i++)
	{
		$congraphXML .= '<set value="'.$rchart[$h][$i].'" alpha="75"/>'."\n";
	}
	$congraphXML .= '</dataset>'."\n";
}
$congraphXML .= '</graph>';

echo "<div class=\"lines\"  id=\"convergraph\">";
	echo renderChartHTML("charts/FCF_MSLine.swf", "", urlencode($congraphXML), "congraph", 400, 250, false, false, "opaque"); 	
echo '<ul class="graphUl">';
for($i = 0; $i < count($chartlabel); $i++)
{
    echo '<li class="legend"><strong style="color: #'.$color[$i].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'."<a onclick=\"popupMenu(event, '".$chartlabel[$i]."', 'page');\" href=\"#\">".$chartlabel[$i].'</a></li>';
}
echo '</ul>';
echo "</div>";
?>
