<?php
//do a converting referrers pie chart
  //load the converting ip's
	$q = $db->Execute("select visitorid,timestamp from $profile->tablename as a,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and a.url=u.id and u.url='$roadto' and status=200 and crawl=0 group by visitorid");
	$i=0;
	$entry = array();
	while ($data = $q->FetchRow()) {
		$cips[$i]=$data["visitorid"];
		//get entry point
		$nq=$db->Execute("select u.url,r.referrer,k.keywords,timestamp from $profile->tablename as a,$profile->tablename_urls as u,$profile->tablename_referrers as r,$profile->tablename_keywords as k where visitorid='$cips[$i]' and timestamp >=$from and timestamp <=$to and a.url=u.id and a.referrer=r.id and a.keywords=k.id and r.referrer!='' and (r.referrer NOT like 'http://$profile->confdomain/%' or r.referrer NOT like 'https://$profile->confdomain/%')  order by timestamp asc limit 1");
		$ndata=$nq->FetchRow();
		$entry[$i]=str_replace("http://", "", trim($ndata["referrer"]));
		$entrypage[$i]=trim($ndata["url"]);
		$entrykw[$i]=trim($ndata["keywords"]);
		$i++;
	}
	$urls = array();
	while (list ($key, $val) = each ($entry)) {
		@$urls[$val]++;
	}
	$crchart[ 0 ][ 0 ] = "";
	$crchart[ 1 ][ 0 ] = "Visitors";
	arsort($urls);
	$i=1;
	while (list ($key, $val) = each ($urls)) {
		$crchart[ 0 ][ $i ]=$key;
		$crchart[ 1 ][ $i ]=$val;
		if ($i >= $limit) {
			break;
		}
		$i++;
		
	}

//end data build

$rpXML = '<graph showNames="0" showValues="0" pieSliceDepth="15" caption="'._REFERRERS_OF_CONVERTED.'" pieYScale="60" decimalPrecision="0" pieRadius="95">';
$FC_ColorCounter=0;
unset($color);
for($i = 1; $i < count($crchart[0]); $i++)
{
	$color[] = getFCColor();
	$rpXML .= '<set value="'.$crchart[1][$i].'" name="'.$crchart[0][$i].'" alpha="75" color="'.$color[$i-1].'"/>';
}
$rpXML .= '</graph>';

echo "<div class=\"pies\" id=\"div_ReferrersofConvertedVisitors\">";
echo renderChartHTML("charts/FCF_Pie3D.swf", "", urlencode($rpXML), "ReferrersofConvertedVisitors", 200, 250, false, false, "opaque");

echo '<ul class="graphUl">';
for($i = 1; $i < count($crchart[0]); $i++)
{
    echo '<li class="legend"><strong style="color: #'.$color[$i-1].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'."<a onclick=\"popupMenu(event, '".$crchart[0][$i]."', 'referrer');\" href=\"#\">".$crchart[0][$i].'</a></li>';
}
echo '</ul>';
echo '</div>';
?>
