<?php
if(isset($chartlabel)){unset($chartlabel);}

if ($sourcetype=="page") {
        $sqlst  = "u.url";
} else if ($sourcetype=="keyword") {  
        $sqlst  = "k.keywords";
        $source=urlencode($source);
} else if ($sourcetype=="referrer") {  
        $sqlst  = "r.referrer";
}

// now we're going to do the country graph
$xlabels="";
$wstring="";

if ($source!="any") {
//get a top 10 of refferers first
	$query  = "select country, count(distinct visitorid) as visitors from $profile->tablename as a,$profile->tablename_urls as u,$profile->tablename_referrers as r,$profile->tablename_keywords as k where timestamp >=$from and timestamp <=$to and a.url=u.id and a.referrer=r.id and a.keywords=k.id and crawl=0 and status=200 and country!='' and $sqlst='$source' group by country order by visitors desc limit $limit";
	//echo $query;
  $q = $db->Execute($query);
	while ($topdata=$q->FetchRow()) {
		$wstring.="country='".$topdata["country"]."' or ";
	}
    if ($wstring!="") {
         $wstring=" and (".substr($wstring,0,-3).")";
    }
	$query  = "select country, $qd AS days, count(distinct visitorid) as visitors from $profile->tablename as a,$profile->tablename_urls as u,$profile->tablename_referrers as r,$profile->tablename_keywords as k where timestamp >=$from and timestamp <=$to and a.url=u.id and a.referrer=r.id and a.keywords=k.id $wstring and crawl=0 and status=200 and $sqlst='$source' group by days,country order by country,timestamp asc"; 
	//echo $query;
	
} else {  
	//get a top 10 of countries first
	$query  = "select country, count(distinct visitorid) as visitors from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 and status=200 and country!='' group by country order by visitors desc limit $limit";
	$q = $db->Execute($query);
	while ($topdata=$q->FetchRow()) {
		$wstring.="country='".$topdata["country"]."' or ";
	}
	if ($wstring!="") {
         $wstring=" and (".substr($wstring,0,-3).")";
    }
    $query  = "select country, $qd AS days, count(distinct visitorid) as visitors from $profile->tablename where timestamp >=$from and timestamp <=$to $wstring and crawl=0 and status=200 group by days,country order by country,timestamp asc"; 
}
if ($wstring=="") {
     // there is no data
     echo "<div class=\"lines\"  id=\"countgraph\">"; 
     echo "<b>Top Countries / Geographic information is not available</b><br>Please download MaxMind's <a href=\"http://www.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz\">GeoLite City Database</a>, <b>UNZIP</b> the file and copy the uncompressed file to the \"geoip\" folder in your Logaholic install.";
     echo "</div>";
} else {
    //set up a proper date series to prevent holes
    if($period == _DAYS){$n=($to-$from)/86400;}
    if($period == _WEEKS){$n=(date('W',$to)-date('W',$from));}
    if($period == _MONTHS){$n=(date('Ym',$to)-date('Ym',$from));}
    $i=0;
    while ($i <= $n)
    {
	if($period == _DAYS)
	{
		$cdate=date("D, m/d/y", ($from+($i*86400)));
	}
	if($period == _WEEKS)
	{
		$fromWeek = strtotime("+$i week",$from);
		$cdate=date("Y-W", ($fromWeek));
	}
	if($period == _MONTHS)
	{
		$fromWeek = strtotime("+$i month",$from);
        $cdate=date("M y", ($fromWeek));
	}
	$days[$cdate]=$i;
	    
	$dayArr[$i] = $cdate;
	$i++;
    }
      
    $q = $db->Execute($query);
    $i=0;
    $range=0;
    $laststatus = "";
    $thismax = 0;
    $rchart = "";
    while ($refdata=$q->FetchRow())
    {
	    if ($refdata["country"]!=$laststatus)
	    {
		    $chartlabel[] = $cnames[$refdata["country"]];
		    if($laststatus != "")
		    {
			    $range++;
		    }
		    
		    //prefill with 0 to avoid holes
		    $pi=0;
		    while ($pi <= $n)
		    {
			    $rchart[ $range ][ $pi ]=0;
			    $pi++;
		    }
		    $laststatus=$refdata["country"];
	    }
	    $rchart[ $range ][ ($days[$refdata["days"]]) ]=$refdata["visitors"];
	    
	    @$totalvisitors[($days[$refdata["days"]])]=@$totalvisitors[($days[$refdata["days"]])]+$refdata["visitors"];
	    if ($thismax < $refdata["visitors"])
	    {
		    $thismax=$refdata["visitors"];
	    }
	    $i++;
    }

    if ($debug)
    {
	    echo "Thismax:$thismax";
	    echo "<pre>";
		    print_r($rchart);
	    echo "</pre>";
    }
    // end data build

    // start chart build
    $countryXML = '<graph
        caption="'._TOP.' '.$limit.' '._COUNTRIES_OVER_TIME.'"
        yAxisMinValue="0"
        decimalPrecision="0"
        formatNumberScale="0"
        showNames="1"
        showValues="0"
        showLegend="0"
        showAlternateHGridColor="1"
        AlternateHGridColor="CCCCCC"
        divLineColor="EEEDED"
        divLineAlpha="100"
        alternateHGridAlpha="5"
        canvasBorderThickness="0"
        rotateNames="1"
        canvasBorderColor="EEEDED"
        chartLeftMargin="0"
        chartRightMargin="5"
        chartTopMargin="5"
        chartBottomMargin="0"
        canvasBaseDepth="0"
        canvasBaseWidth="0"
        canvasBgDepth="0"
        canvasBgColor="FFFFFF"
        canvasBaseColor="CCCCCC"
        animation="';$countryXML .= $profile->animate == 1 ? "0" : "1";$countryXML .= '">';
    $countryXML .= '<categories>'."\n";
    $timespan = floor(($to - $from) / 86400);
    $f = 0;
    for ($g = 0; $g < count($days); $g++)
    {
	    $prevWeek = date('W',$from+($g*86400)-86400);
	    $newWeek = date('W',$from+($g*86400));
	    $countryXML .= '<category name="'.$dayArr[$g].'"';
	    if($n <= 8)
	    {
		    $countryXML .= ' showName="1"/>'."\n";
	    }
	    else
	    {
		    if($f == round($n/(round($n/($n/100*6)))))
		    {
			    $countryXML .= ' showName="1"/>'."\n";
			    $f = 0;
		    }
		    else
		    {
			    if($g == 0)
			    {
				    $countryXML .= ' showName="1"/>'."\n";
			    }
			    else
			    {
				    $countryXML .= ' showName="0"/>'."\n";
			    }
		    }
	    $f++;
	    }
    }
    $countryXML .= '</categories>'."\n";
    $FC_ColorCounter=0;
    unset($color);
    for ($h = 0; $h < count($rchart); $h++)
    {
	    $color[] = getFCColor();
	    @$countryXML .= '<dataset seriesName="'.$chartlabel[$h].'" color="'.$color[$h].'" areaBorderColor="'.$color[$h].'" anchorAlpha="0">'."\n";
	    for ($i = 0; $i < count($rchart[$h]); $i++)
	    {
		    $countryXML .= '<set value="'.$rchart[$h][$i].'" alpha="75"/>'."\n";
	    }
	    $countryXML .= '</dataset>'."\n";
    }
    $countryXML .= '</graph>';


    echo "<div class=\"lines\"  id=\"countgraph\">";
	    echo renderChartHTML("charts/FCF_StackedArea2D.swf", "", urlencode($countryXML), "countrygraph", 400, 250, false, false, "opaque"); 	
    echo '<ul class="graphUl">';
    for($i = 0; $i < count($chartlabel); $i++)
    {
	    echo '<li class="legend"><strong style="color: #'.$color[$i].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'.$chartlabel[$i].'</li>';
    }
    echo '</ul>';
    echo "</div>";
}
?>
