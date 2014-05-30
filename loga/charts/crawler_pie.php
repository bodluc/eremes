<?php
$query  = "select ua.name useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".$profile->tablename_useragents." ua on (useragentid = ua.id) where timestamp >=$from and timestamp <=$to and crawl=1 group by useragentid order by visitors desc limit $limit";

$crchart[ 0 ][ 0 ] = "";
$crchart[ 1 ][ 0 ] = "Visitors";
$i=1;
$q=$db->Execute($query);
while ($data = $q->FetchRow()) {
	$status = $data["useragent"];
    $crchart[ 0 ][ $i ]=$status;
	$crchart[ 1 ][ $i ]=$data["visitors"];
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

$cpXML = '<graph showNames="0" showValues="0" pieSliceDepth="15" caption="'._TOP.' '.$limit.' '._CRAWLERS.'" pieYScale="60" decimalPrecision="0" pieRadius="95">';
$FC_ColorCounter=0;
unset($color);
for($i = 1; $i < count($crchart[0]); $i++)
{
	$color[] = getFCColor();
	$cpXML .= '<set value="'.$crchart[1][$i].'" name="'.$crchart[0][$i].'" alpha="75" color="'.$color[$i-1].'"/>';
}
$cpXML .= '</graph>';

echo "<div class=\"pies\" id=\"crawler_pie\">";
echo renderChartHTML("charts/FCF_Pie3D.swf", "", urlencode($cpXML), "Top10Crawlers", 200, 250, false, false, "opaque");

echo '<ul class="graphUl">';
for($i = 1; $i < count($crchart[0]); $i++)
{
	echo '<li class="legend"><strong style="color: #'.$color[$i-1].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'.$crchart[0][$i].'</li>';
}
echo '</ul>';
echo '</div>';
?>
