<?php
if(isset($crchart)){unset($crchart);} 
   
$query  = "select k.keywords as keywords, count(distinct visitorid) as visitors from $profile->tablename as a,$profile->tablename_keywords as k where timestamp >=$from and timestamp <=$to and a.keywords=k.id and k.keywords!='' and crawl=0 group by a.keywords order by visitors desc limit $limit";

    
$crchart[ 0 ][ 0 ] = "";
$crchart[ 1 ][ 0 ] = "Visitors";
$i=1;
$q=$db->Execute($query);
while ($data = $q->FetchRow()) {
	$status = $data["keywords"];
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

$sepXML = '<graph showNames="0" showValues="0" pieSliceDepth="15" caption="'._TOP_KEYWORDS.'" pieYScale="60" decimalPrecision="0" pieRadius="95">';
for($i = 1; $i < count($crchart[0]); $i++)
{
	$color[] = getFCColor();
	$sepXML .= '<set value="'.$crchart[1][$i].'" name="'.$crchart[0][$i].'" alpha="75" color="'.$color[$i-1].'"/>';
}
$sepXML .= '</graph>';

echo "<div class=\"pies\" id=\"topkeywords_pie\">";
echo renderChartHTML("charts/FCF_Pie3D.swf", "", urlencode($sepXML), "TopKeywords", 200, 250, false, false, "opaque");

echo '<ul class="graphUl">';
for($i = 1; $i < count($crchart[0]); $i++)
{
    echo '<li class="legend"><strong style="color: #'.$color[$i-1].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'."<a onclick=\"popupMenu(event, '".$crchart[0][$i]."', 'keyword');\" href=\"#\">".urldecode($crchart[0][$i]).'</a></li>';
}
echo '</ul>';
echo '</div>';
?>
