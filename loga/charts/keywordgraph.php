<?php
if(isset($chartlabel)){unset($chartlabel);}
$period="Days";
$start=time();

if ($period=="Days") { 
	$qd="FROM_UNIXTIME(timestamp,'%a, %m/%d/%y')"; 
} else if ($period=="Weeks") { 
	$qd="FROM_UNIXTIME(t.timestamp,'%Y-%V')"; 
} else if ($period=="Months") { 
	$qd="FROM_UNIXTIME(t.timestamp,'%M')"; 
} 


  //get a top 10 of keywords first
    $query  = "select k.keywords as keywords, count(distinct visitorid) as visitors from $profile->tablename as a,$profile->tablename_keywords as k where timestamp >=$from and timestamp <=$to and a.keywords=k.id and k.keywords!='' and crawl=0 group by a.keywords order by visitors desc limit $limit";
    //echo $query . "<P>";
    $q = $db->Execute($query);
    $wstring = "";
    while ($topdata=$q->FetchRow()) {
        $wstring.="k.keywords='".$topdata["keywords"]."' or ";
    }
    $wstring=substr($wstring,0,-3);
    if ($wstring) {
        $wstring = "and ($wstring)";
    }
  
  $query  = "select $qd AS days, k.keywords as keywords, count(distinct visitorid) as requests from $profile->tablename as a,$profile->tablename_keywords as k where timestamp >=$from and timestamp <=$to $wstring and crawl=0 and a.keywords=k.id group by days,a.keywords order by k.keywords,timestamp asc";

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
	if ($refdata["keywords"]!=$laststatus)
	{
		$chartlabel[] = $refdata["keywords"];
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
		$laststatus=$refdata["keywords"];
	}
	$rchart[ $range ][ ($days[$refdata["days"]]) ]=$refdata["requests"];
	
	$totalvisitors[($days[$refdata["days"]])]=@$totalvisitors[($days[$refdata["days"]])]+$refdata["requests"];
	if ($thismax < $refdata["requests"])
	{
		$thismax=$refdata["requests"];
	}
	$i++;
}

if ($debug)
{
	echo "Thismax:$thismax";
	echo "<pre>";
		print_r($rchart);
	echo "</pre>";
}
// end data build

// start chart build
$keygraphXML = '<graph
    caption="'._TOP.' '.$limit.' '._KEYWORDS_OVER_TIME.'"
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
    animation="';$keygraphXML .= $profile->animate == 1 ? "0" : "1";$keygraphXML .='"
>'."\n";
$keygraphXML .= '<categories>'."\n";
$timespan = floor(($to - $from) / 86400);
$f = 0;
for ($g = 0; $g < count($days); $g++)
{
	$prevWeek = date('W',$from+($g*86400)-86400);
	$newWeek = date('W',$from+($g*86400));
	$keygraphXML .= '<category name="'.$dayArr[$g].'"';
	if($timespan <= 8)
	{
		$keygraphXML .= ' showName="1"/>'."\n";
	}
	else
	{
		if($f == round($n/(round($n/($n/100*6)))))
		{
			$keygraphXML .= ' showName="1"/>'."\n";
			$f = 0;
		}
		else
		{
			if($g == 0)
			{
				$keygraphXML .= ' showName="1"/>'."\n";
			}
			else
			{
				$keygraphXML .= ' showName="0"/>'."\n";
			}
		}
	$f++;
	}
}
$keygraphXML .= '</categories>'."\n";
$FC_ColorCounter=0;
unset($color);
for ($h = 0; $h < count($rchart); $h++)
{
	$color[] = getFCColor();
	$keygraphXML .= '<dataset seriesName="'.$chartlabel[$h].'" color="'.$color[$h].'" anchorAlpha="0">'."\n";
	for ($i = 0; $i < count($rchart[$h]); $i++)
	{
		$keygraphXML .= '<set value="'.$rchart[$h][$i].'" alpha="75"/>'."\n";
	}
	$keygraphXML .= '</dataset>'."\n";
}
$keygraphXML .= '</graph>';

echo "<div class=\"lines\"  id=\"keywordgraph\">";
	echo renderChartHTML("charts/FCF_MSLine.swf", "", urlencode($keygraphXML), "keygraph", 400, 250, false, false, "opaque"); 	
echo '<ul class="graphUl">';
for($i = 0; $i < count($chartlabel); $i++)
{
	//echo '<li class="legend"><strong style="color: #'.$color[$i].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'.$chartlabel[$i].'</li>';
    echo '<li class="legend"><strong style="color: #'.$color[$i].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'."<a onclick=\"popupMenu(event, '".$chartlabel[$i]."', 'keyword');\" href=\"#\">".urldecode($chartlabel[$i]).'</a></li>';
}
echo '</ul>';
echo "</div>";
?>
