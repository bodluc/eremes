<?php
if(isset($chartlabel)){unset($chartlabel);}

// now we're going to do the referrer graph
if ($source!="any") {
    //get a top 10 of refferers first
	if ($sourcetype=="page") {
        $sqlst  = "u.url";
    } else if ($sourcetype=="keyword") {  
        $sqlst  = "k.keywords";
        $source=urlencode($source);  
    } 
	//$query  = "select referrer, count(distinct visitorid) as visitors from $profile->tablename where timestamp >=$from and timestamp <=$to and referrer NOT like '%$profile->confdomain/%' and $sqlst='$source' and crawl=0 and status=200 group by referrer order by visitors desc limit $limit";
    if ($sourcetype=="keyword") {   
        $query  = "select r.referrer as referrer, count(distinct visitorid) as visitor from $profile->tablename as a,$profile->tablename_keywords as k,$profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and a.referrer=r.id and a.keywords=k.id and r.referrer NOT like 'http://$profile->confdomain/%' and r.referrer != '-' and $sqlst='$source' and crawl=0 and status=200 group by a.referrer order by visitor desc limit $limit";
    } else {
        //if($period == _DAYS){
        //$query  = "select r.referrer as referrer, sum(visitors) as visitor from $profile->tablename_dailyurls as a,$profile->tablename_referrers as r,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and $sqlst='$source' and r.referrer != '-' and a.referrer=r.id and a.url=u.id group by a.referrer order by visitor desc limit $limit";
        //}else{
        $query  = "select r.referrer as referrer, count(distinct visitorid) as visitor from $profile->tablename as a,$profile->tablename_referrers as r,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and $sqlst='$source' and a.referrer=r.id and a.url=u.id group by a.referrer order by visitor desc limit $limit";
        //}
    }
    
	$q = $db->Execute($query);
	$wstring = "";
	while ($topdata=$q->FetchRow()) {
		$wstring.="r.referrer='".$topdata["referrer"]."' or ";
	}
	if ($wstring!="") {
         $wstring=" and (".substr($wstring,0,-3).")";
        //$query  = "select referrer, $qd AS days, count(distinct visitorid) as visitors from $profile->tablename where timestamp >=$from and timestamp <=$to and ($wstring) and crawl=0 and status=200 and $sqlst='$source' group by days,referrer order by referrer,timestamp asc";
        if ($sourcetype=="keyword") {   
            $query  = "select r.referrer as referrer, $qd AS days, count(distinct visitorid) as visitor from $profile->tablename as a,$profile->tablename_referrers as r,$profile->tablename_keywords as k where timestamp >=$from and timestamp <=$to $wstring and crawl=0 and status=200 and a.referrer=r.id and a.keywords=k.id and $sqlst='$source' and r.referrer != '-' group by days,a.referrer order by r.referrer,timestamp asc";
        } else {
        
        if($period == _DAYS){
            $query  = "select r.referrer as referrer, $qd AS days, sum(visitors) as visitor from $profile->tablename_dailyurls as a, $profile->tablename_urls as u,$profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and a.url=u.id and a.referrer=r.id  $wstring and $sqlst='$source' and r.referrer != '-' group by days,a.referrer order by r.referrer,timestamp asc";
        }else{
            $query  = "select r.referrer as referrer, $qd AS days, count(distinct visitorid) as visitor from $profile->tablename as a, $profile->tablename_urls as u,$profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and a.url=u.id and a.referrer=r.id  $wstring and $sqlst='$source' and r.referrer != '-' group by days,a.referrer order by r.referrer,timestamp asc";
        }
        }
    }
	
} else {
	//get a top 10 of refferers first
	//$query  = "select referrer, count(distinct visitorid) as visitors from $profile->tablename where timestamp >=$from and timestamp <=$to and referrer NOT like '%$profile->confdomain/%' and crawl=0 and status=200 group by referrer order by visitors desc limit $limit";
        if($period == _DAYS){
    $query  = "select r.referrer as referrer, sum(visitors) as visitor from $profile->tablename_dailyurls as a,$profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and a.referrer=r.id and r.referrer NOT like 'http://$profile->confdomain/%' and r.referrer != '-' group by r.referrer order by visitor desc limit $limit";
        }else{
        $query  = "select r.referrer as referrer, count(distinct visitorid) as visitor from $profile->tablename as a,$profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and a.referrer=r.id and r.referrer NOT like 'http://$profile->confdomain/%' and r.referrer != '-' group by r.referrer order by visitor desc limit $limit";
        }
	
	$q = $db->Execute($query);
	$wstring = "";
	while ($topdata=$q->FetchRow()) {
		$wstring.="r.referrer='".$topdata["referrer"]."' or ";
	}
	if ($wstring!="") {
         $wstring=" and (".substr($wstring,0,-3).")";
        //$query  = "select referrer, $qd AS days, count(distinct visitorid) as visitors from $profile->tablename where timestamp >=$from and timestamp <=$to and ($wstring) and crawl=0 and status=200 group by days,referrer order by referrer,timestamp asc";
        if($period == _DAYS){
        $query  = "select r.referrer as referrer, $qd AS days, sum(visitors) as visitor from $profile->tablename_dailyurls as a,$profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and a.referrer=r.id $wstring and r.referrer != '-' group by days,r.referrer order by r.referrer,timestamp asc";
        }else{
        $query  = "select r.referrer as referrer, $qd AS days, count(distinct visitorid) as visitor from $profile->tablename as a,$profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and a.referrer=r.id $wstring and r.referrer != '-' group by days,r.referrer order by r.referrer,timestamp asc";
        }
    }
  
}
//set up a proper date series to prevent holes
if($period == _DAYS){$n=($to-$from)/86400;}
if($period == _WEEKS){$n=(date('W',$to)-date('W',$from));}
if($period == _MONTHS){$n=(date('Ym',$to)-date('Ym',$from));} 
$i=0;
//echo $n;
while ($i <= $n)
{
	if($period == _DAYS)
	{
		$cdate=date("D, m/d/y", ($from+($i*86400)));
	}
	if($period == _WEEKS)
	{
		$fromWeek = strtotime("+$i week",$from);
		$cdate=date("Y-W", ($fromWeek));
	}
	if($period == _MONTHS)
	{
        //$cdate=date("F", ($from+($i*86400)));
        $fromWeek = strtotime("+$i month",$from);
        $cdate=date("M y", ($fromWeek));
	}
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
	if ($refdata["referrer"]!=$laststatus)
	{
		$chartlabel[] = $refdata["referrer"];
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
		$laststatus=$refdata["referrer"];
	}
	$rchart[ $range ][ ($days[$refdata["days"]]) ]=$refdata["visitor"];
	
	@$totalvisitors[($days[$refdata["days"]])]=@$totalvisitors[($days[$refdata["days"]])]+$refdata["visitor"];
	if ($thismax < $refdata["visitor"])
	{
		$thismax=$refdata["visitor"];
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
$refgraphXML = '<graph
    caption="'._TOP.' '.$limit.' '._REFERRERS_OVER_TIME.'"
    yAxisMinValue="0"
    yAxisName="Unique Visitors"';
    if ($thismax < 5) {
        $refgraphXML .= 'yAxisMaxValue="5"';   
    }
    $refgraphXML .= 'decimalPrecision="0"
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
    chartLeftMargin="4"
    chartRightMargin="5"
    chartTopMargin="5"
    chartBottomMargin="0"
    canvasBaseDepth="0"
    canvasBaseWidth="0"
    canvasBgDepth="0"
    canvasBgColor="FFFFFF"
    animation="';$refgraphXML .= $profile->animate == 1 ? "0" : "1";$refgraphXML .= '">';
$refgraphXML .= '<categories>'."\n";
$timespan = floor(($to - $from) / 86400);
$f = 0;
for ($g = 0; $g < count($days); $g++)
{
	$prevWeek = date('W',$from+($g*86400)-86400);
	$newWeek = date('W',$from+($g*86400));
	$refgraphXML .= '<category name="'.$dayArr[$g].'"';
	if($n <= 8)
	{
        $refgraphXML .= ' showName="1"/>'."\n";
	}
	else
	{
        if($f == round($n/(round($n/($n/100*6)))))
		{
			$refgraphXML .= ' showName="1"/>'."\n";
			$f = 0;
		}
		else
		{
			if($g == 0)
			{
				$refgraphXML .= ' showName="1"/>'."\n";
			}
			else
			{
				$refgraphXML .= ' showName="0"/>'."\n";
			}
		}
	$f++;
	}
}
$refgraphXML .= '</categories>'."\n";
$FC_ColorCounter=0;
unset($color);
for ($h = 0; $h < count(@$rchart); $h++)
{
	$color[] = getFCColor();
	$refgraphXML .= '<dataset seriesName="'.$chartlabel[$h].'" color="'.$color[$h].'" areaBorderColor="'.$color[$h].'" anchorAlpha="0" areaAlpha="75">'."\n";
	for ($i = 0; $i < count($rchart[$h]); $i++)
	{
		$refgraphXML .= '<set value="'.$rchart[$h][$i].'"/>'."\n";
	}
	$refgraphXML .= '</dataset>'."\n";
}
$refgraphXML .= '</graph>';

echo "<div class=\"lines\"  id=\"referrergraph\">";
	echo renderChartHTML("charts/FCF_StackedArea2D.swf", "", urlencode($refgraphXML), "refgraph", 400, 250, false, false, "opaque"); 	
echo '<ul class="graphUl">';
for($i = 0; $i < count(@$chartlabel); $i++)
{
	//echo '<li class="legend"><strong style="color: #'.$color[$i].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'.$chartlabel[$i].'</li>';
    echo '<li class="legend"><strong style="color: #'.$color[$i].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'."<a onclick=\"popupMenu(event, '".$chartlabel[$i]."', 'referrer');\" href=\"#\">".$chartlabel[$i].'</a></li>';
}
echo '</ul>';
echo "</div>";
?>
