<?php
$query  = "select country, count(distinct visitorid) as visitors from $profile->tablename as a,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and crawl=0 and status=200 and a.url=u.id and u.url='$roadto' and country!='' group by country order by visitors desc limit $limit";
  //echo $query;
	$q = $db->Execute($query);
	$i=1;
	$ccchart[ 0 ][ 0 ] = "";
	$ccchart[ 1 ][ 0 ] = "Visitors";
    $restq="";
	while ($topdata=$q->FetchRow()) {
		$ccchart[ 0 ][ $i ]=$cnames[$topdata["country"]]; 
		$ccchart[ 1 ][ $i ]=$topdata["visitors"];
        $restq.="and country!='".$topdata["country"]."' ";
    $i++;
  }
  $query  = "select \"Other Countries\", count(distinct visitorid) as visitors from $profile->tablename as a,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and crawl=0 and status=200 and a.url=u.id and u.url='$roadto' and country!='' $restq";
    $q=$db->Execute($query);
    $data = $q->FetchRow();
    $ccchart[ 0 ][ $i ]="Other Countries";
    $ccchart[ 1 ][ $i ]=$data["visitors"];
  //echo "<pre>";
  //print_r($cchart);
  //echo "</pre>";
	
$ccpXML = '<graph showNames="0" showValues="0" pieSliceDepth="15" caption="'._COUNTRIES_OF_CONVERTED.'" pieYScale="60" decimalPrecision="0" pieRadius="95">';
$FC_ColorCounter=0;
unset($color);
for($i = 1; $i < count($ccchart[0]); $i++)
{
	$color[] = getFCColor();
	$ccpXML .= '<set value="'.$ccchart[1][$i].'" name="'.$ccchart[0][$i].'" alpha="75" color="'.$color[$i-1].'"/>';
}
$ccpXML .= '</graph>';
echo "<div class=\"pies\" id=\"chart6\">";
echo renderChartHTML("charts/FCF_Pie3D.swf", "", urlencode($ccpXML), "Countries of Converted Visitors", 200, 250, false, false, "opaque");

echo '<ul class="graphUl">';
for($i = 1; $i < count($ccchart[0]); $i++)
{
	echo '<li class="legend"><strong style="color: #'.$color[$i-1].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'.$ccchart[0][$i].'</li>';
}
echo '</ul>';
echo "</div>";
?>
