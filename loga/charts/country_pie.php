<?php
$crchart = array("");
    
//do a countries pie chart
if ($source!="any") {

	$query  = "select country, count(distinct visitorid) as visitors from $profile->tablename as a,$profile->tablename_urls as u,$profile->tablename_referrers as r,$profile->tablename_keywords as k where timestamp >=$from and timestamp <=$to and a.url=u.id and a.referrer=r.id and a.keywords=k.id and $sqlst='$source' and crawl=0 and status=200 and country!='' group by country order by visitors desc limit $limit";
		//echo $query;
  
}else {
  
	$query  = "select country, count(distinct visitorid) as visitors from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 and status=200 and country!='' group by country";
    
    $query .= " order by visitors desc limit $limit";
 
}

$crchart[ 0 ][ 0 ] = "";
$crchart[ 1 ][ 0 ] = "Visitors";
$i=1;
$restq="";
$q=$db->Execute($query);
while ($data = $q->FetchRow()) {
	$country = $data["country"];
	//if (($country) && (array_search($country, $cnames))) {
		$crchart[ 0 ][ $i ]=$cnames[$data["country"]];
        $restq.="and country!='$country' ";
	//} else {
	//	$crchart[ 0 ][ $i ]=$country;
	//}
	$crchart[ 1 ][ $i ]=$data["visitors"];
	$i++;
}
/*
if ($source=="any") {
    $query  = "select \"Other Countries\", count(distinct visitorid) as visitors from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 and status=200 and country!='' $restq";
    $q=$db->Execute($query);
    $data = $q->FetchRow();
    $crchart[ 0 ][ $i ]="Other Countries";
    $crchart[ 1 ][ $i ]=$data["visitors"];
}    
*/

if ($debug){
	echo "Thismax:$thismax";
	//exit();
  echo "<pre>";
  print_r($crchart);
  echo "</pre>";
  }
//end data build

/*$arrData[0][1] = "Product A";
$arrData[1][1] = "Product B";
$arrData[2][1] = "Product C";
$arrData[3][1] = "Product D";
$arrData[4][1] = "Product E";
$arrData[5][1] = "Product F";

//Store sales data
$arrData[0][2] = 567500;
$arrData[1][2] = 815300;
$arrData[2][2] = 556800;
$arrData[3][2] = 734500;
$arrData[4][2] = 676800;
$arrData[5][2] = 648500;*/

$cpXML = '<graph showNames="0" showValues="0" pieSliceDepth="15" caption="'._TOP.' '.$limit.' '._COUNTRIES.'" pieYScale="70" decimalPrecision="0" pieRadius="95">';
$FC_ColorCounter=0;
unset($color);
for($i = 1; $i < count($crchart[0])-1; $i++)
{
	$color[] = getFCColor();
	$cpXML .= '<set value="'.$crchart[1][$i].'" name="'.$crchart[0][$i].'" alpha="75" color="'.$color[$i-1].'"/>';
}
$cpXML .= '</graph>';

echo "<div class=\"pies\" id=\"country_pie\">";
echo renderChartHTML("charts/FCF_Pie3D.swf", "", urlencode($cpXML), "Top10Countries", 200, 250, false, false, "opaque");

echo '<ul class="graphUl">';
for($i = 1; $i < count($crchart[0])-1; $i++)
{
	echo '<li class="legend"><strong style="color: #'.$color[$i-1].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'.$crchart[0][$i].'</li>';
}
echo '</ul>';
echo '</div>';
?>
