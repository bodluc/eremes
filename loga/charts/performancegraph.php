<?php
if(isset($chartlabel)){unset($chartlabel);}
$period=_DAYS;

if ($period==_DAYS){$qd="FROM_UNIXTIME(timestamp,'%a, %m/%d/%y')";}
else if ($period==_WEEKS){$qd="FROM_UNIXTIME(t.timestamp,'%Y-%V')";}
else if ($period==_MONTHS){$qd="FROM_UNIXTIME(timestamp,'%b %y')";} 

$query  = "select $qd AS days, u.url as url, count(distinct visitorid) as requests from $profile->tablename as a,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and a.url=u.id and $profile->targets_sql and crawl=0 group by a.url,days order by u.url,timestamp asc";

//set up a proper date series to prevent holes
$n=($to-$from)/86400;
$i=0;
// make a safefrom that starts at noon, to calculate the day range because otherwise shifts in DST can mess us up
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
$chartlabel[] = ""._PERFORMANCE_TREND;
// end data build

// start chart build
$pergraphXML = '<graph
    caption="'._VISITORS_TO_TARGET_FILES.'"
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
    animation="';$pergraphXML .= $profile->animate == 1 ? "0" : "1";$pergraphXML .= '">';
$pergraphXML .= '<categories>'."\n";
$timespan = floor(($to - $from) / 86400);
$f = 0;
for ($g = 0; $g < count($days); $g++)
{
	$prevWeek = date('W',$from+($g*86400)-86400);
	$newWeek = date('W',$from+($g*86400));
	$pergraphXML .= '<category name="'.$dayArr[$g].'"';
	if($timespan <= 8)
	{
		$pergraphXML .= ' showName="1"/>';
	}
	else
	{
		if($f == round($n/(round($n/($n/100*6)))))
		{
			$pergraphXML .= ' showName="1"/>';
			$f = 0;
		}
		else
		{
			if($g == 0)
			{
				$pergraphXML .= ' showName="1"/>';
			}
			else
			{
				$pergraphXML .= ' showName="0"/>';
			}
		}
	$f++;
	}
}
$pergraphXML .= '</categories>';
$FC_ColorCounter=0;
unset($color);
for ($h = 0; $h < count($rchart); $h++)
{
	$color[] = getFCColor();
	$pergraphXML .= '<dataset seriesName="'.$chartlabel[$h].'" color="'.$color[$h].'" anchorAlpha="0">';
	for ($i = 0; $i < count($rchart[$h]); $i++)
	{
		
        $pergraphXML .= '<set value="'.$rchart[$h][$i].'" alpha="75"/>';
	}
	$pergraphXML .= '</dataset>';
}
$pergraphXML .= '</graph>';

echo "<div class=\"lines\"  id=\"perfgraph\">";
	echo renderChartHTML("charts/FCF_MSLine.swf", "", urlencode($pergraphXML), "pergraph", 400, 250, false, false, "opaque"); 	
echo '<ul class="graphUl">';
for($i = 0; $i < count($chartlabel); $i++)
{
	echo '<li class="legend"><strong style="color: #'.$color[$i].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'."<a onclick=\"popupMenu(event, '".$chartlabel[$i]."', 'page');\" href=\"#\">".$chartlabel[$i].'</a></li>';
}
echo '</ul>';
echo "</div>";
?>
