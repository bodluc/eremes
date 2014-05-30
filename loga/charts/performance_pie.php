<?php
$crchart = array("");
$color = array
(
	"00DD22",
	"FDFD66",
	"C84239",
	"FCD381",
	"F971F7",
	"3C1F6E",
	"2E880A",
	"DD1840",
	"6DF9FB",
	"FCDA00" 
);

//do a error pie chart
  
    $query  = "select u.url as url, count(distinct visitorid) as visitors from $profile->tablename as a,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and a.url=u.id and $profile->targets_sql group by a.url order by visitors desc limit $limit";
    //echo "<P>$query<P>";

$crchart[ 0 ][ 0 ] = "";
$crchart[ 1 ][ 0 ] = "Visitors";
$i=1;
$q=$db->Execute($query);
while ($data = $q->FetchRow()) {
	$status = $data["url"];
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

$ppXML = '<graph showNames="0" showValues="0" pieSliceDepth="15" caption="'._TOP.' '.$limit.' '._TARGET_FILES.'" pieYScale="70" decimalPrecision="0" pieRadius="95">';
for($i = 1; $i < count($crchart[0]); $i++)
{
	$ppXML .= '<set value="'.$crchart[1][$i].'" name="'.$crchart[0][$i].'" alpha="75" color="'.$color[$i-1].'"/>';
}
$ppXML .= '</graph>';

echo "<div class=\"pies\" id=\"performance_pie\">";
echo renderChartHTML("charts/FCF_Pie3D.swf", "", urlencode($ppXML), "Top10TargetFiles", 200, 250, false, false, "opaque");
echo '<ul class="graphUl">';
for($i = 1; $i < count($crchart[0]); $i++)
{
	//echo '<li class="legend"><strong style="color: #'.$color[$i-1].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'.$crchart[0][$i].'</li>';
    echo '<li class="legend"><strong style="color: #'.$color[$i-1].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'."<a onclick=\"popupMenu(event, '".$crchart[0][$i]."', 'page');\" href=\"#\">".$crchart[0][$i].'</a></li>';
}
echo '</ul>';
echo "</div>";
?>
