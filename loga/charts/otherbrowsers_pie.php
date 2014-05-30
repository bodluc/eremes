<?php
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
  
$query  = "select ua.name AS useragent, count(distinct visitorid) as visitors from $profile->tablename left outer join {$profile->tablename_useragents} AS ua on (useragentid = ua.id) where timestamp >=$from and timestamp <=$to and crawl=0 and status=200 and ua.name NOT LIKE \"Internet Explorer%\" and ua.name NOT LIKE \"Firefox%\" group by ua.name order by visitors desc limit $limit";
// $query="select AGENTS.name useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name not like \"Firefox%\") and (AGENTS.name not like \"Internet Explorer%\") and (AGENTS.name != \"-\") group by useragentid order by visitors desc limit $limit";
  $crchart = array();
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

$obpXML = '<graph showNames="0" showValues="0" pieSliceDepth="15" caption="'._TOP.' '.$limit.' '._OTHER_CLIENTS.'" pieYScale="60" decimalPrecision="0" pieRadius="95">';
$FC_ColorCounter=0;
unset($color);
for($i = 1; $i < count($crchart[0]); $i++)
{
	$color[] = getFCColor();
	$obpXML .= '<set value="'.$crchart[1][$i].'" name="'.$crchart[0][$i].'" alpha="75" color="'.$color[$i-1].'"/>';
}
$obpXML .= '</graph>';

echo "<div class=\"bpies\" id=\"div_Top10OtherClients\">";
echo renderChartHTML("charts/FCF_Pie3D.swf", "", urlencode($obpXML), "Top10OtherClients", 200, 250, false, false, "opaque");

echo '<ul class="graphUl">';
for($i = 1; $i < count($crchart[0]); $i++)
{
	echo '<li class="legend"><strong style="color: #'.$color[$i-1].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'.$crchart[0][$i].'</li>';
}
echo '</ul>';
echo "</div>";
?>
