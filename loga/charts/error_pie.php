<?php
$query  = "select status, count(distinct visitorid) as visitors from $profile->tablename where timestamp >=$from and timestamp <=$to group by status order by visitors desc limit $limit";

$errorcodes  = $db->Execute("select code, concat(code,\" - \",descr) as pd from ".TBL_LGSTATUS."");
while ($ec_data=$errorcodes->FetchRow()) { 
    $ec[$ec_data["code"]]=$ec_data["pd"];
}
$ec["200"]="200 - Successful Request";

$crchart[ 0 ][ 0 ] = "";
$crchart[ 1 ][ 0 ] = "Visitors";
$i=1;
$q=$db->Execute($query);
while ($data = $q->FetchRow()) {
	$status = $data["status"];
  $crchart[ 0 ][ $i ]=@$ec[$status];
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

$epXML = '<graph showNames="0" showValues="0" pieSliceDepth="15" caption="'._TOP.' '.$limit.' '._ERROR_CODES.'" pieYScale="60" decimalPrecision="0" pieRadius="95">';
$FC_ColorCounter=0;
unset($color);
for($i = 1; $i < count($crchart[0]); $i++)
{
	$color[] = getFCColor();
	$epXML .= '<set value="'.$crchart[1][$i].'" name="'.$crchart[0][$i].'" alpha="75" color="'.$color[$i-1].'"/>';
}
$epXML .= '</graph>';

echo "<div class=\"pies\" id=\"div_Top10StatusCodes\">";
echo renderChartHTML("charts/FCF_Pie3D.swf", "", urlencode($epXML), "Top10StatusCodes", 200, 250, false, false, "opaque");

echo '<ul class="graphUl">';
for($i = 1; $i < count($crchart[0]); $i++)
{
	echo '<li class="legend"><strong style="color: #'.$color[$i-1].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'.$crchart[0][$i].'</li>';
}
echo '</ul>';
echo '</div>';
?>
