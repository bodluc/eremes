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

//get a top 10 of brosers first
    $query  = "select ua.name AS agent, count(distinct visitorid) as visitors from $profile->tablename left outer join {$profile->tablename_useragents} AS ua on (useragentid = ua.id) where timestamp >=$from and timestamp <=$to and crawl=0 and status=200 and ua.name !=\"-\" group by useragentid order by visitors desc limit $limit";
    //echo $query . "<P>";
    $q = $db->Execute($query);
    $wstring = "";
    while ($topdata=$q->FetchRow()) {
        $wstring.="ua.name='".$topdata["agent"]."' or ";
    }
    $wstring=substr($wstring,0,-3);
    
    if (!empty($wstring)) {
    //empty : variable is null, or has no data
    $wstring="and ($wstring)";
    }
    
  
  $query  = "select $qd AS days, ua.name AS useragent, count(distinct visitorid) as visitors from $profile->tablename left outer join {$profile->tablename_useragents} AS ua on (useragentid = ua.id) where timestamp >=$from and timestamp <=$to $wstring and status=200 and crawl=0 group by useragentid,days order by useragent desc,timestamp asc";

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
	if ($refdata["useragent"]!=$laststatus)
	{
		$chartlabel[] = $refdata["useragent"];
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
		$laststatus=$refdata["useragent"];
	}
	$rchart[ $range ][ ($days[$refdata["days"]]) ]=$refdata["visitors"];
	
	$totalvisitors[($days[$refdata["days"]])]=@$totalvisitors[($days[$refdata["days"]])]+$refdata["visitors"];
	if ($thismax < $refdata["visitors"])
	{
		$thismax=$refdata["visitors"];
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
$brographXML = '<graph
    caption="'._BROWSER_TRENDS.'"
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
    animation="';$brographXML .= $profile->animate == 1 ? "0" : "1";$brographXML .='"
>'."\n";
$brographXML .= '<categories>'."\n";
$timespan = floor(($to - $from) / 86400);
$f = 0;
for ($g = 0; $g < count($days); $g++)
{
	$prevWeek = date('W',$from+($g*86400)-86400);
	$newWeek = date('W',$from+($g*86400));
	$brographXML .= '<category name="'.$dayArr[$g].'"';
	if($timespan <= 8)
	{
		$brographXML .= ' showName="1"/>'."\n";
	}
	else
	{
		if($f == round($n/(round($n/($n/100*6)))))
		{
			$brographXML .= ' showName="1"/>'."\n";
			$f = 0;
		}
		else
		{
			if($g == 0)
			{
				$brographXML .= ' showName="1"/>'."\n";
			}
			else
			{
				$brographXML .= ' showName="0"/>'."\n";
			}
		}
	$f++;
	}
}
$brographXML .= '</categories>'."\n";
$FC_ColorCounter=0;
unset($color);
for ($h = 0; $h < count($rchart); $h++)
{
	$color[] = getFCColor();
	$brographXML .= '<dataset seriesName="'.$chartlabel[$h].'" color="'.$color[$h].'" anchorAlpha="0">'."\n";
	for ($i = 0; $i < count($rchart[$h]); $i++)
	{
		$brographXML .= '<set value="'.$rchart[$h][$i].'" alpha="75"/>'."\n";
	}
	$brographXML .= '</dataset>'."\n";
}
$brographXML .= '</graph>';

echo "<div class=\"lines\"  id=\"browsergraph\">";
	echo renderChartHTML("charts/FCF_MSLine.swf", "", urlencode($brographXML), "brograph", 400, 250, false, false, "opaque"); 	
echo '<ul class="graphUl">';
for($i = 0; $i < count($chartlabel); $i++)
{
	echo '<li class="legend"><strong style="color: #'.$color[$i].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'.$chartlabel[$i].'</li>';
}
echo '</ul>';
echo "</div>";
?>
