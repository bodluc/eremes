<?php
if ($sourcetype=="page") {
        $sqlst  = "u.url";
} else if ($sourcetype=="keyword") {  
        $sqlst  = "k.keywords";
        $source=urlencode($source);  
} else if ($sourcetype=="referrer") {  
        $sqlst  = "r.referrer";
}

//do a referrers pie chart
if ($source!="any") {
//get a top 10 of refferers first
	//$query  = "select referrer, count(distinct visitorid) as visitors from $profile->tablename where timestamp >=$from and timestamp <=$to and referrer NOT like '%$profile->confdomain/%' and $sqlst='$source' and crawl=0 and status=200 group by referrer order by visitors desc limit $limit";
    if ($sourcetype=="keyword") {   
        $query  = "select r.referrer as referrer, count(distinct visitorid) as visits from $profile->tablename as a,$profile->tablename_keywords as k,$profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and a.referrer=r.id and a.keywords=k.id and (r.referrer NOT like 'http://$profile->confdomain/%' and r.referrer NOT like 'https://$profile->confdomain/%') and $sqlst='$source' and crawl=0 and status=200 group by a.referrer order by visits desc limit $limit";
    } else {
        $query  = "select r.referrer as referrer, sum(visitors) as visits from $profile->tablename_dailyurls as a,$profile->tablename_urls as u,$profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and a.url=u.id and a.referrer=r.id and $sqlst='$source' group by a.referrer order by visits desc limit $limit";
    }
		//echo $query;
	
} else {
	//get a top 10 of refferers first
	//$query  = "select referrer, count(distinct visitorid) as visitors from $profile->tablename where timestamp >=$from and timestamp <=$to and referrer NOT like '%$profile->confdomain/%' and crawl=0 and status=200 group by referrer order by visitors desc limit $limit";
    //$query  = "select r.referrer, sum(visitors) as visitors from $profile->tablename_dailyurls where timestamp >=$from and timestamp <=$to and referrer NOT like '%$profile->confdomain/%' group by referrer order by visitors desc limit $limit";
     $query  = "select r.referrer as referrer, sum(visitors) as visits from $profile->tablename_dailyurls as a,$profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and a.referrer=r.id and (r.referrer NOT like 'http://$profile->confdomain/%' and r.referrer NOT like 'https://$profile->confdomain/%') and r.referrer!='-' group by a.referrer order by visits desc limit $limit";
     //echoDebug("doing this");
}

$crchart[ 0 ][ 0 ] = "";
$crchart[ 1 ][ 0 ] = "Visitors";
$i=1;
$q=$db->Execute($query);
while ($data = $q->FetchRow()) {
	 $crchart[ 0 ][ $i ]=$data["referrer"];
	 $crchart[ 1 ][ $i ]=$data["visits"];
	 $i++;
}

if ($debug){
  echo "Thismax:$thismax";
  //exit();
  echo "<pre>";
  print_r($crchart);
  echo "</pre>";
  }
//end data build


//start chart build
$rpXML = '<graph showNames="0" showValues="0" pieSliceDepth="15" caption="'._TOP.' '.$limit.' '._REFERRERS.'" pieYScale="70" decimalPrecision="0" pieRadius="95">';
$FC_ColorCounter=0;
unset($color);
for($i = 1; $i < count($crchart[0]); $i++)
{
	$color[] = getFCColor();
	$rpXML .= '<set value="'.$crchart[1][$i].'" name="'.$crchart[0][$i].'" alpha="75" color="'.$color[$i-1].'"/>';
}
$rpXML .= '</graph>';
echo '<div class="pies" id="refpie2">';
echo renderChartHTML("charts/FCF_Pie3D.swf", "", urlencode($rpXML), "Top10Referrers", 200, 250, false, false, "opaque");

echo '<ul class="graphUl">';
for($i = 1; $i < count($crchart[0]); $i++)
{
    echo '<li class="legend"><strong style="color: #'.$color[$i-1].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'."<a onclick=\"popupMenu(event, '".$crchart[0][$i]."', 'referrer');\" href=\"#\">".$crchart[0][$i].'</a></li>';
}
echo '</ul>';
echo "</div>";
?>
