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
  
//$query  = "select status, count(distinct visitorid) as visitors from $profile->tablename where timestamp >=$from and timestamp <=$to group by status order by visitors desc limit $limit";
    //echo "<P>$query<P>";
	
	$query = "SELECT ua.os, COUNT(DISTINCT a.visitorid) AS visitors FROM {$profile->tablename} AS a, {$profile->tablename_useragents} as ua WHERE a.useragentid = ua.id AND a.timestamp BETWEEN {$from} AND {$to} AND a.status = 200 AND a.crawl = 0 AND ua.is_mobile != 1 GROUP BY ua.os ORDER BY visitors DESC";
    
    // $query= "select \"Windows\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"%Windows%\") union ";

    // $query.= "select \"Linux\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"%Linux%\") union ";
    
    // $query.= "select \"Apple\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name  like \"%Apple%\") union ";
    
    // $query.= "select \"Other/Unknown OS\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name  NOT like \"%Windows%\" and AGENTS.name  not like \"%linux%\" and AGENTS.name not like \"%apple%\")";

    // $query.= " order by  visitors desc"; 



$crchart = array();
$crchart[ 0 ][ 0 ] = "";
$crchart[ 1 ][ 0 ] = "Visitors";
$i=1;
$q=$db->Execute($query);
while ($data = $q->FetchRow()) {
	$status = $data["os"];
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

$ospXML = '<graph showNames="0" showValues="0" pieSliceDepth="15" caption="'._TOP_OS_SHARE.'" pieYScale="60" decimalPrecision="0" pieRadius="95">';
$FC_ColorCounter=0;
unset($color);
for($i = 1; $i < count($crchart[0]); $i++)
{
	$color[] = getFCColor();
	$ospXML .= '<set value="'.$crchart[1][$i].'" name="'.$crchart[0][$i].'" alpha="75" color="'.$color[$i-1].'"/>';
}
$ospXML .= '</graph>';

echo "<div class=\"bpies\" id=\"div_TopOperatingSystemShare\">";
echo renderChartHTML("charts/FCF_Pie3D.swf", "", urlencode($ospXML), "TopOperatingSystemShare", 200, 250, false, false, "opaque");

echo '<ul class="graphUl">';
for($i = 1; $i < count($crchart[0]); $i++)
{
	echo '<li class="legend"><strong style="color: #'.$color[$i-1].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'.$crchart[0][$i].'</li>';
}
echo '</ul>';
echo '</div>';
?>
