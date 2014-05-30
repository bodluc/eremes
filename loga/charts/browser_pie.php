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
    
	$query  = "select ua.name AS useragent, count(distinct visitorid) as visitors from $profile->tablename AS a left outer join {$profile->tablename_useragents} AS ua on (useragentid = ua.id) where timestamp >=$from and timestamp <=$to and a.crawl=0 and a.status=200 and ua.is_mobile != 1 AND ua.name !=\"-\" group by ua.name order by visitors desc limit $limit";
	
    // $query= "select \"Internet Explorer\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Internet Explorer%\") union ";

    // $query.= "select \"Firefox\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Firefox%\") union ";
    
    // $query.= "select \"Opera\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Opera%\") union ";
    
    // $query.= "select \"Safari\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"%Safari%\") union ";
    
    // $query.= "select \"Mozilla Gecko Based\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Mozilla%\" and AGENTS.name like \"%Gecko%\" and AGENTS.name not like \"%Safari%\") union ";
    
    // $query.= "select \"Mozilla Other (Non Gecko)\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Mozilla%\" and AGENTS.name not like \"%Gecko%\" and AGENTS.name not like \"%Safari%\") union ";
    
    // $query.= "select \"Other Browsers\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name NOT like \"%Safari%\" and AGENTS.name not like \"Internet Explorer%\" and AGENTS.name not like \"Firefox%\" and AGENTS.name not like \"Opera%\" and AGENTS.name!=\"-\") and AGENTS.name not like \"Mozilla%\"";

    // $query.= " order by  visitors desc"; 



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

$bpXML = '<graph showNames="0" showValues="0" pieSliceDepth="15" caption="'._TOP_BROWSER_SHARE.'" pieYScale="60" decimalPrecision="0" pieRadius="95">';
$FC_ColorCounter=0;
unset($color);
for($i = 1; $i < count($crchart[0]); $i++)
{
	$color[] = getFCColor();
	$bpXML .= '<set value="'.$crchart[1][$i].'" name="'.$crchart[0][$i].'" color="'.$color[$i-1].'"/>';
}
$bpXML .= '</graph>';

echo "<div class=\"pies\" id=\"browser_pie\">";
echo renderChartHTML("charts/FCF_Pie3D.swf", "", urlencode($bpXML), "Top Browser Share", 200, 250, false, false, "opaque");

echo '<ul class="graphUl">';
for($i = 1; $i < count($crchart[0]); $i++)
{
	echo '<li class="legend"><strong style="color: #'.$color[$i-1].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'.$crchart[0][$i].'</li>';
}
echo '</ul>';
echo '</div>';
?>
