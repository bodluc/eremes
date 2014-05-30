<?php
if(isset($crchart)){unset($crchart);} 

    $query= ("select \"Google (Natural Search)\" referrer, count(distinct visitorid) as visitors from $profile->tablename as a, $profile->tablename_referrers as r,$profile->tablename_urlparams as up where timestamp >=$from and timestamp <=$to and (r.referrer like \"http://www.google.%\" and up.params NOT like \"?gclid=%\") and crawl=0 and a.referrer=r.id and a.params=up.id union ");

    $query.= ("select \"Google (Paid Search)\" referrer, count(distinct visitorid) as visitors from $profile->tablename as a, $profile->tablename_referrers as r,$profile->tablename_urlparams as up where timestamp >=$from and timestamp <=$to and r.referrer like \"http://www.google.%\" and up.params like \"?gclid=%\" and crawl=0 and a.referrer=r.id and a.params=up.id union ");

    $query.= ("select \"Yahoo\" referrer, count(distinct visitorid) as visitors from  $profile->tablename as a, $profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and r.referrer like \"%search.yahoo.%\" and crawl=0 and a.referrer=r.id union ");

    $query.= ("select \"Bing\" referrer, count(distinct visitorid) as visitors from $profile->tablename as a, $profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and r.referrer = \"http://www.bing.com/search\" and crawl=0 and a.referrer=r.id union ");
    
    $query.= ("select \"AOL Search\" referrer, count(distinct visitorid) as visitors from $profile->tablename as a, $profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and r.referrer like \"%search.aol.%\" and crawl=0 and a.referrer=r.id union ");

    //$query.= ("select \"My Way Search\" referrer, count(distinct visitorid) as visitors from $profile->tablename as a, $profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and r.referrer like \"http://search.myway.com%\" and crawl=0 and a.referrer=r.id union ");

    $query.= ("select \"Ask.com\" referrer, count(distinct visitorid) as visitors from $profile->tablename as a, $profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and r.referrer like \"%.ask.com%\" and crawl=0 and a.referrer=r.id union ");

    $query.= ("select \"Dogpile.com\" referrer, count(distinct visitorid) as visitors from $profile->tablename as a, $profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and r.referrer like \"http://www.dogpile.com%\" and crawl=0 and a.referrer=r.id union ");

    $query.= ("select \"Others\" referrer, count(distinct visitorid) as visitors from $profile->tablename as a, $profile->tablename_referrers as r,$profile->tablename_keywords as k where timestamp >=$from and timestamp <=$to and a.keywords=k.id and k.keywords!='' and r.referrer not like \"http://www.google.%\" and r.referrer not like \"%search.yahoo.%\" and r.referrer!=\"http://www.bing.com/search\" and r.referrer not like \"%.search.aol.%\" and r.referrer not like \"%.ask.com%\" and r.referrer not like \"http://www.dogpile.com%\" and crawl=0 and a.referrer=r.id ");   
    
    $query.= " order by  visitors desc";

    
$crchart[ 0 ][ 0 ] = "";
$crchart[ 1 ][ 0 ] = "Visitors";
$i=1;
$q=$db->Execute($query);
while ($data = $q->FetchRow()) {
	$status = $data["referrer"];
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

$sepXML = '<graph showNames="0" showValues="0" pieSliceDepth="15" caption="'._TOP_REFERRING_SE.'" pieYScale="60" decimalPrecision="0" pieRadius="95">';
for($i = 1; $i < count($crchart[0]); $i++)
{
	$color[] = getFCColor();
	$sepXML .= '<set value="'.$crchart[1][$i].'" name="'.$crchart[0][$i].'" alpha="75" color="'.$color[$i-1].'"/>';
}
$sepXML .= '</graph>';

echo "<div class=\"pies\" id=\"searchengine_pie\">";
echo renderChartHTML("charts/FCF_Pie3D.swf", "", urlencode($sepXML), "TopReferringSearchEngines", 200, 250, false, false, "opaque");

echo '<ul class="graphUl">';
for($i = 1; $i < count($crchart[0]); $i++)
{
	echo '<li class="legend"><strong style="color: #'.$color[$i-1].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'.$crchart[0][$i].'</li>';
}
echo '</ul>';
echo '</div>';
?>
