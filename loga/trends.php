<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 

require_once 'core_factory.php';
Logaholic_sessionStart();

$_SESSION['schart'] = "";
$_SESSION['tchart'] = "";

@set_time_limit(86400);

/* 
................................................
.......... Main program starts here ............
................................................
*/
require "top.php";
//require "queries.php";
//include "charts.php";
include_once("charts/includes/FusionCharts.php");

//error_reporting(0);
$submit = @$_REQUEST["submit"];
$avgperiod = @$_REQUEST["avgperiod"];
$agent = @$_REQUEST["agent"];
$limit = @$_REQUEST["limit"];
$period = @$_REQUEST["period"];
$source = @$_REQUEST["source"];
$sourcetype = @$_REQUEST["sourcetype"];
$filter = @$_REQUEST["filter"];
$status = @$_REQUEST["status"];
$roadto = @$_REQUEST["roadto"];
$labels = @$_REQUEST["labels"];
$drilldown="";
$tabletotalcolor="silver";
$tableheadercolor="d5ffd5";
$tableheaderfontcolor="black";
$tablemaincolor="white";
$starttime=getmicrotime();

if ($submit) {
		if ($filter==false) {
			$filter=0;
		} else {
			$filter=0;
			$checked="checked";
    }  
    if (!$from) {
      $from   = mktime(0,0,0,$fmonth,$fday,$fyear);
      $to     = mktime(23,59,59,$tmonth,$tday,$tyear);
    }
} else {
    $filter = false;
	$checked="";
	if (!$from) {
        $from   = mktime(0,0,0,date("m"),01,date("Y"));
        $to     = mktime(23,59,59,date("m"),date("d"),date("Y"));
    }
}
if (!$avgperiod) {
	$avgperiod=7; //in periods
}
if (!$sourcetype) {
    $sourcetype="page"; 
}
if (!$labels) {
    $labels=_TRENDS_OVERVIEW; 
}

//$debug=1;
    ?>
    <div id="loading" style="position:absolute;left:100px;display:block;z-index:20;">
    &nbsp;<p>
    <table bgcolor=white cellspacing=0 cellpadding=3>
    <tr><td class=toplinegreen><font size="+1"><?php echo _BUILDING_TREND_REPORTS;?>...</font></td></tr>
    <tr><td bgcolor="#f0f0f0" class=dotline valign=middle><img src="images/Hourglass_icon.gif" width="31" height="31" alt="Please Wait" border="0" align="left" vspace="4" hspace="4">
    &nbsp;<br>
    <?php echo _WAIT_WHILE_REPORT_IS_BEING_CREATED;?>&nbsp;<br>
    
    </td></tr>
    </table>
    </div>
    <?php

$tr["_PERFORMANCE_TRENDS"] = _PERFORMANCE_TRENDS;
if ($tr["_PERFORMANCE_TRENDS"]==$labels) {
    //
} 
//begin page
echo "<div class='form1-wrap'>";
echo "<form method=\"get\" action=\"trends.php\" id=\"form1\" name=\"form1\"><table border=\"0\"><tr><td width=\"72\"><b>"._DATE_RANGE.": </b></td><td>";
QuickDate($from,$to);
echo "</td><td>";
newDateSelector($from,$to);
echo "<input type=\"hidden\" name=\"conf\" value=\"$conf\">";
echo "<input type=\"hidden\" name=\"labels\" value=\"$labels\">";
echo "</td><td title=\""._MAX_NUMBER_OF_RESULTS_TO_SHOW."\">";
if (!$limit) {
 	 $limit=10;
}
echo ""._ANALYZE_TOP." <input type=\"text\" size=\"3\" name=\"limit\" value=\"$limit\"  class=\"small\">";
echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"submit\" value=\"Report\">";

if ($labels==_TRENDS_OVERVIEW) {
    echo "&nbsp; <a id=\"moreoptions\" class=\"graylink\" href=\"javascript:moreoptions();\">"._MORE_OPTIONS."</a>";
    echo "</td></tr></table>"; 
    echo "<table border=\"0\"><tr><td width=\"70\">";
    if (!$source) {
	    $source="any";
    }
    
    echo "<b>"._SEARCH.":</b></td><td><select name=\"sourcetype\"><option value=\"page\" ";
    if ($sourcetype=="page") { echo "selected=\"selected\""; } echo ">"._PAGE.":"; 
    echo "<option value=\"keyword\" ";
    if ($sourcetype=="keyword") { echo "selected=\"selected\""; } echo ">"._KEYWORD.":"; 
    echo "<option value=\"referrer\" ";                                      
    if ($sourcetype=="referrer") { echo "selected=\"selected\""; } echo ">"._REFERRER.":";  
    echo "</select>";

    echo " <input type=\"text\" name=\"source\" id=\"source\" value=\"$source\" onkeyup=\"popupMenu(event, this.value+'@'+this.id+'@'+document.form1.elements['sourcetype'].value, 'forminput');\" onclick=\"PageHelpForms(this.id,this.value, event, '@'+this.id+'@'+document.form1.elements['sourcetype'].value, 'forminput');\" autocomplete=\"off\"></td><td valign=\"top\">";
    
    echo "<b>"._TARGET.":</b>&nbsp;&nbsp;<select name=\"roadto\">";
    if ($profile->targetfiles) {
	    $targets=explode(",",$profile->targetfiles);
	    $tl="";
        foreach ($targets as $thistarget) {
		    if ($thistarget) {
			    if (trim($thistarget)==$roadto) { $sel="SELECTED"; } else { $sel=""; }
			    $tl.= "<option $sel value=\"".trim($thistarget)."\">".substr(trim($thistarget),0,50)."\n";
		    }
	    }
	    if (!$sel && !$roadto) {
		    $tl.= "<option selected=\"selected\" value=\"\">"._NONE."</option>\n";
	    } else {
		    $tl.= "<option value=\"\">"._NONE."</option>\n";
	    }
    }
    echo "</select>";
    echo "<br>";
    
    //echo "<b>"._TARGET.":</b>";
    //echo " <input type=\"text\" name=\"roadto\" id=\"roadto\" value=\"$roadto\" onkeyup=\"popupMenu(event, this.value+'@'+this.id+'@'+'kpi', 'forminput');\" onclick=\"PageHelpForms(this.id,this.value, event, '@'+this.id+'@'+'kpi', 'forminput');\" autocomplete=\"off\"> ";
    
    echo "</td></tr></table><div id=\"advancedUI\" style=\"display:none;\">\n  <table border=\"0\">\n    <tr>\n      <td width=\"70\"><b>"._PERIOD.":</b></td>";
    echo "<td>"._ROLLING_AVERAGE_PERIODS.": <input type=\"text\" size=\"2\" name=\"avgperiod\" value=\"$avgperiod\"></td><td>";
    if (!$period) {
        $period=_DAYS;
    }
    $sel1 = "";
    $sel2 = "";
    $sel3 = "";
    if ($period==_DAYS) { 
        $sel1="checked";
        $qd="FROM_UNIXTIME(timestamp,'%a, %m/%d/%y')"; 
    } else if ($period==_WEEKS) { 
        $sel2="checked";
        $qd="FROM_UNIXTIME(timestamp,'%Y-%u')"; 
    } else if ($period==_MONTHS) { 
        $sel3="checked"; 
        $qd="FROM_UNIXTIME(timestamp,'%b %y')"; 
    } 
    if ($labels==_TRENDS_OVERVIEW) {
        echo "".strtoupper(substr(_REPORT,0,1)).substr(_REPORT,1).' '._PERIOD.": <input type=\"radio\" name=\"period\" value=\""._DAYS."\" $sel1> "._DAYS." <input type=\"radio\" name=\"period\" value=\""._WEEKS."\" $sel2> "._WEEKS." <input type=\"radio\" name=\"period\" value=\""._MONTHS."\" $sel3> "._MONTHS;
    }
    //echo "</td></tr>";
    //echo "<tr><td colspan=\"3\"> </td></tr><tr><td><b>"._FILTERS.":</b></td><td colspan=\"2\">";
    //echo printTrafficSourceSelect();
    //echo "coming soon.";
}
echo "</td></tr></table>";
echo $labels == _TRENDS_OVERVIEW ? "</div>" : "";
echo "</form></div>";
echo "<div class=\"breaker\"></div>";
//echo "<P>";

echo "<div id=\"reportmenu\" style=\"position:absolute;left:10px;width:160px;margin-top:2px;border:0px solid red;\">";
//QuerySelectorHTML($labels);

echo '<div id="accordion">
	<h3 class="accordion_header_first">
		<a href="#">'._TREND_REPORTS.'</a>
	</h3>
	<div class="reportmenu">
		<ul>
			<li>
				<a href="trends.php?from='.$from.'&amp;to='.$to.'&amp;conf='.$conf.'&amp;quickdate='.$quickdate.'&amp;submit=Report&amp;limit='.$limit.'" style="'; echo $labels != _TRENDS_OVERVIEW ? "":"background: #CCFFCC no-repeat left; "; echo 'background-image: url(images/icons/chart_line.gif);">'._TRENDS_OVERVIEW.'</a>
			</li>
			<li>
				<a href="trends.php?from='.$from.'&amp;to='.$to.'&amp;conf='.$conf.'&amp;quickdate='.$quickdate.'&amp;submit=Report&amp;labels='._PERFORMANCE_TRENDS.'&amp;limit='.$limit.'" class="sidelinks" style="'; echo $labels != _PERFORMANCE_TRENDS ? "":"background: #CCFFCC no-repeat left; "; echo 'background-image: url(images/icons/cart.gif);">'._PERFORMANCE_TRENDS.'</a>
			</li>
			<li>
				<a href="trends.php?from='.$from.'&amp;to='.$to.'&amp;conf='.$conf.'&amp;quickdate='.$quickdate.'&amp;submit=Report&amp;labels='._SEARCH_TRENDS.'&amp;limit='.$limit.'" class="sidelinks" style="'; echo $labels != _SEARCH_TRENDS ? "":"background: #CCFFCC no-repeat left; "; echo 'background-image: url(images/icons/searchengines.gif);">'._SEARCH_TRENDS.'</a>
			</li>
			<li>
				<a href="trends.php?from='.$from.'&amp;to='.$to.'&amp;conf='.$conf.'&amp;quickdate='.$quickdate.'&amp;submit=Report&amp;labels='._BROWSER_TRENDS.'&amp;limit='.$limit.'" class="sidelinks" style="'; echo $labels != _BROWSER_TRENDS ? "":"background: #CCFFCC no-repeat left; "; echo ' background-image: url(images/icons/computer.gif);">'._BROWSER_TRENDS.'</a>
			</li>
			<li>
				<a href="trends.php?from='.$from.'&amp;to='.$to.'&amp;conf='.$conf.'&amp;quickdate='.$quickdate.'&amp;submit=Report&amp;labels='._ERROR_TRENDS.'&amp;limit='.$limit.'" class="sidelinks" style="'; echo $labels != _ERROR_TRENDS ? "":"background: #CCFFCC no-repeat left; "; echo 'background-image: url(images/icons/error.gif);">'._ERROR_TRENDS.'</a>
			</li>
			<li>
				<a href="trends.php?from='.$from.'&amp;to='.$to.'&amp;conf='.$conf.'&amp;quickdate='.$quickdate.'&amp;submit=Report&amp;labels='._CRAWLER_TRENDS.'&amp;limit='.$limit.'" class="sidelinks" style="'; echo $labels != _CRAWLER_TRENDS ? "":"background: #CCFFCC no-repeat left; "; echo 'background-image: url(images/icons/crawler.gif);">'._CRAWLER_TRENDS.'</a>
			</li>
		</ul>
	</div>
</div></div>';
flush();
echo'<div style="padding-left: 165px; width: 640px;" id="TrendContainer">';
$startqtime=getmicrotime();
$query2 = "";
if ($labels==_TRENDS_OVERVIEW) {
	if ($source!="any") {
	 //  1. get total unique visitors for time unit
     if ($sourcetype=="page") {
        // if (isset($_REQUEST["statuscode"])) {
            $query  = $db->Execute("select $qd AS timeunit, count(distinct visitorid) as uvisitors from $profile->tablename ".($databasedriver == "mysql" ? "force index (timestamp)" : ""). " where timestamp >=$from and timestamp <=$to and url='".getID($source,'urls')."' group by timeunit order by timestamp"); 
    
         //} else {	    
         //   $query  = $db->Execute("select $qd AS timeunit, sum(visitors) as uvisitors from $profile->tablename_dailyurls as a,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and a.url=u.id and u.url='$source' group by timeunit order by timestamp");
         //}                                         
         $sqlst  = "u.url";
     } else if ($sourcetype=="keyword") {
        $query  = $db->Execute("select $qd AS timeunit, count(distinct visitorid) as uvisitors from $profile->tablename as a ".($databasedriver == "mysql" ? "force index (timestamp)" : ""). ",$profile->tablename_keywords as k where timestamp >=$from and timestamp <=$to and crawl=0 and a.keywords=k.id and k.keywords='".$source."' group by timeunit order by timestamp");
        $sqlst  = "k.keywords";
        
     } else if ($sourcetype=="referrer") {
        $query  = $db->Execute("select $qd AS timeunit, sum(visitors) as uvisitors from $profile->tablename_dailyurls as a,$profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and a.referrer=r.id and r.referrer='$source' group by timeunit order by timestamp");
         $sqlst  = "r.referrer";
        
     } 
    
		if ($roadto!="") {
		//  2. get conversions per day
			@$db->Execute("drop table if exists temptable");
			$pq="create temporary table temptable select visitorid,u.url,timestamp from $profile->tablename as a,$profile->tablename_urls as u,$profile->tablename_referrers as r,$profile->tablename_keywords as k where timestamp >=$from and timestamp <=$to and a.url=u.id and a.referrer=r.id and a.keywords=k.id and $sqlst='$source' and status=200 and crawl=0";
			$db->Execute($pq);
	        $qd = str_replace("timestamp", "a.timestamp",$qd);
			$query2  = $db->Execute("select $qd as timeunit, count(distinct a.visitorid) as users from temptable as s, $profile->tablename_conversions as a,$profile->tablename_urls as u where a.timestamp >=$from and a.timestamp <=$to and s.timestamp < a.timestamp and s.visitorid=a.visitorid and a.url=u.id and u.url='$roadto' and s.timestamp < a.timestamp group by timeunit order by a.timestamp");
		}
	
	
	} else {
        //  1. get total unique visitors for time unit
         //$query  = $db->Execute("select $qd AS timeunit, count(distinct visitorid) as visitors from $profile->tablename ".($databasedriver == "mysql" ? "force index (timestamp)" : ""). " where timestamp >=$from and timestamp <=$to and crawl=0 group by timeunit order by timestamp");
         $query  = $db->Execute("select $qd AS timeunit, sum(visitors) as uvisitors from $profile->tablename_vpd where timestamp >=$from and timestamp <=$to group by timeunit order by timestamp");
         
         //  2. get conversions per day
         if ($roadto) {
         //$query2  = $db->Execute("select $qd AS timeunit, count(distinct visitorid) as users from $profile->tablename where timestamp >=$from and timestamp <=$to and url='$roadto' and status=200 and crawl=0 group by timeunit order by timestamp");
         $query2  = $db->Execute("select $qd AS timeunit, count(distinct visitorid) as users from $profile->tablename_conversions as a,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and a.url=u.id and u.url='$roadto' group by timeunit order by timestamp");
         
         }
	}
	
	
	$endqtime=getmicrotime();
//echo "Query time:" .($endqtime-$startqtime) . "<P>";  
	$totalvisitors = 0;
	$totalconversion = 0;
	
//  3. Merge results
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
	$merge[$cdate][1]=0;  // unique visitors
	$merge[$cdate][2]=0; // hits op de target
	$merge[$cdate][3]=0;// conversion rate
	$i++;
}

	while ($data=$query->FetchRow()) {
		$merge[$data["timeunit"]][1]=$data["uvisitors"];
		$totalvisitors=$totalvisitors+$data["uvisitors"];
		$merge[$data["timeunit"]][2]=0; // hits op de target
		$merge[$data["timeunit"]][3]=0;// conversion rate
	}
	if ($query2) {
		while ($data=$query2->FetchRow()) {
			$merge[$data["timeunit"]][2]=@$data["users"];
			$merge[$data["timeunit"]][3]=number_format((($data["users"]/$merge[$data["timeunit"]][1])*100),2);
			$totalconversion=$totalconversion+number_format((($data["users"]/$merge[$data["timeunit"]][1])*100),2);
			//echo "Conversion! $data[users]<P></P>";
		}
	}
    //add a trend line with linear regression
    //$y=$a * $x + $b
    //required variables
    /*
    $a; calculated
    $b; calculated
    $x; in this case days (or months), just an iterating number in this case
    $y; the number of visitors, pages or whatever on $x day
    $n; Total number of datapoints
    $xy; x times y
    $xsq;  x squared
    $axb; $a * $x +$b
    */
    $n=count(@$merge);
    if ($n==0) {
	    echo "&nbsp;<P><h3>"._NO_DATA_FOUND_FOR." <font color=\"red\">$source</font></h3>"._NO_DATA_FOUND_TIPS."</body></html>";
	    exit();
    }
    reset($merge);
    $i=1;
    while (list ($day, $row) = each ($merge)) {
	    $x[$i]=$i; // trend line for conversion rate
	    $traffic_x[$i]=$i; // trend line for total traffic
	    $target_x[$i]=$i; // trend line for target traffic
	    
	    $y[$i]=$row[3];
	    $traffic_y[$i]=$row[1];
	    $target_y[$i]=$row[2];
	    
	    //begin calculation
	    $xy[$i]=$x[$i] * $row[3];
	    $traffic_xy[$i]=$traffic_x[$i] * $row[1];
	    $target_xy[$i]=$target_x[$i] * $row[2];
	    
	    $xsq[$i]=$x[$i] * $x[$i];
	    $traffic_xsq[$i]=$traffic_x[$i] * $traffic_x[$i];
	    $target_xsq[$i]=$target_x[$i] * $target_x[$i];
	    
	    $i++;
    }

    // $n has already been vetted to be <> 0, so we can divide by $n with impunity here
    $a_part=array_sum($xy)-((array_sum($x)*array_sum($y))/$n);
    $traffic_a_part=array_sum($traffic_xy)-((array_sum($traffic_x)*array_sum($traffic_y))/$n);
    $target_a_part=array_sum($target_xy)-((array_sum($target_x)*array_sum($target_y))/$n);

    // array_sum($x) isn't vetted, so we need to protect from a division by 0 here.
    $a=@($a_part/(array_sum($xsq)-(1/$n)*(array_sum($x)*array_sum($x))));
    $traffic_a=@($traffic_a_part/(array_sum($traffic_xsq)-(1/$n)*(array_sum($traffic_x)*array_sum($traffic_x))));
    $target_a=@($target_a_part/(array_sum($target_xsq)-(1/$n)*(array_sum($target_x)*array_sum($target_x))));

    $b=(array_sum($y)/$n)-($a * (array_sum($x)/$n));
    $traffic_b=(array_sum($traffic_y)/$n)-($traffic_a * (array_sum($traffic_x)/$n));
    $target_b=(array_sum($target_y)/$n)-($target_a * (array_sum($target_x)/$n));
    //now we have eveything to plot our 2 regression lines

    //add rolling average and plot point for trend
    reset($merge);
    $ndays=count($merge);
    $maxval = 0;
    $cstr = 0;
    //echo "Rolling Average over $avgperiod $period";
    $i=0;
    while (list ($day, $row) = each ($merge)) {
	    while (list ($skey, $sval) = each ($row)) {
		    switch ($skey) {
		    case 1:
			    $traffic_regval[$i]=($traffic_a * $traffic_x[($i+1)]) + $traffic_b;
			    break;
		    case 2:
			    $target_regval[$i]=($target_a * $target_x[($i+1)]) + $target_b;
			    break;
		    case 3:
			    // this is the conversions rate row
			    $regval[$i]=($a * $x[($i+1)]) + $b; //calculate every datapoint

			    $cr[$i]=$sval;
			    if ($maxval < $sval) {
				    $maxval=$sval;
			    }
			    $dev=0;
			    if ($i < $avgperiod) {
				    $avg=0;
			    } else {
				    while ($avgperiod > $dev) {
					    $cstr=$cstr+$cr[($i-$dev)];
					    $dev++;
				    }
				    $avg=$cstr/$avgperiod;
			    }
			    $merge[$day][4]=number_format($avg,2);
			    if ($maxval < $avg) {
				    $maxval=$avg;
			    }
			    $cstr=0;
			     break;
		    }
	    }
	    //echo "<P></P>";
	    $i++;
    }
    $looptime=getmicrotime();
    //echo "loop time:" .($looptime-$endqtime) . "<P>";  

    // now merge it in the stats table format
    $i=0;
    reset($merge);
    if ($roadto) {
      while (list ($key, $val) = each ($merge)) {
		    $data[$i][0] = $key; //date
        while (list ($skey, $sval) = each ($val)) {
          $data[$i][$skey]=$sval;
			    //echo "$skey => $sval<br>";
        }
		    $data[$i][5]=$regval[$i];
        $i++;
      }
    } else {
      while (list ($key, $val) = each ($merge)) {
		    $data[$i][0] = $key; //date
		    while (list ($skey, $sval) = each ($val)) {
          $data[$i][$skey]=$sval;
          //echo "$skey => $sval<br>";
        }
		    $data[$i][2]=number_format($traffic_regval[$i],0);
        $i++;
      }
    }
    ?>
    <script language="JavaScript" type="text/javascript">
		    function showreport(spot) {
          if (spot.style.display=="block") {
            spot.style.display="none";
            
          } else {
            spot.style.display="block";
            
          }
        }
    </script>
    <div class="todayreports" style="width:640px;">
	<div class="toplinegreen" style="padding: 2px;">
    <font size="+1" color="black">
        <?php echo""._TRENDS_OVERVIEW?> <?php if ($source!="any") { echo "<i> for $sourcetype: <font color=red>$source</font></i>"; }?></font> <font size="-1"> &nbsp;<sup><a href="javascript:helpbox('traffic')" id="greenhelplink" class="greenlink"><?php echo""._SHOW_DATA?></a></sup><br><b><?php echo""._DATE_FROM?></b> <?php echo date("D, d M Y / H:i", $from); ?> <b><?php echo""._DATE_TO?></b> <?php echo date("D, d M Y / H:i", $to); ?></font>    <div id="inv_div" style="display : none; line-height : 18px; position : relative;">
        &nbsp;<br>    <a href="javascript:helpbox();" class="graylink"><?php echo""._CLOSE_HELP_TEXT?></a>
        </div>
    </div>
    <?php
    if ($roadto) {
      $showfields=_DATE.", "._VISITORS.", "._CONVERSIONS.", "._CONVERSION_RATE.", "._R_AVERAGE.", "._TREND;
    } else {
      $showfields=_DATE.", "._VISITORS.", "._TREND;
    }
    $mini=2;
    echo "<div id=\"traffic\" style=\"position:relative; display:none; zoom:1;\">";
    ArrayStatsTable($from,$to,$showfields,$labels,$query,$drilldown,$filter);
    echo "</div>";
    reset($merge);
    $i=0;
    if ($roadto) {
	    $tchart[ 'names' ][ 0 ] = "";
	    $tchart[ 'names' ][ 1 ] = ""._VISITORS;
	    $tchart[ 'names' ][ 2 ] = ""._TARGET." x10";
	    $tchart[ 'names' ][ 3 ] = ""._VISITOR_TREND;
	    $tchart[ 'names' ][ 4 ] = ""._TARGET_TREND;
    } else {
	    $tchart[ 'names' ][ 0 ] = "";
	    $tchart[ 'names' ][ 1 ] = ""._VISITORS;
	    $tchart[ 'names' ][ 2 ] = ""._VISITOR_TREND;
    }
    while (list ($day, $row) = each ($merge)) {
	    if ($maxval < $row[1]) {
		    $maxval = $row[1];
	    }
	    if ($roadto) {
		    $tchart[ 0 ][ $i ]=$day;
		    $tchart[ 1 ][ $i ] = $row[1];
		    $tchart[ 2 ][ $i ] = $row[2] * 10;
		    //$tchart[ 3 ][ $i ] = $traffic_regval[($i-1)];
		    //$tchart[ 4 ][ $i ] = $target_regval[($i-1)] * 10;
			if($traffic_regval[($i)] <= 0)
            {
            	$tchart[ 3 ][ $i ] = 0;
            } else {
            	$tchart[ 3 ][ $i ] = $traffic_regval[($i)];
            }
	    	if($target_regval[($i)] * 10 <= 0)
            {
            	$tchart[ 4 ][ $i ] = 0;
            } else {
            	$tchart[ 4 ][ $i ] = $target_regval[($i)] * 10;
            }
      } else {
		    $tchart[ 0 ][ $i ]=$day;
            $tchart[ 1 ][ $i ] = $row[1];
            //$tchart[ 2 ][ $i ] = $traffic_regval[($i-1)];
            if($traffic_regval[($i)] <= 0)
            {
            	$tchart[ 2 ][ $i ] = 0;
            } else {
            	$tchart[ 2 ][ $i ] = $traffic_regval[($i)];
            }
      }
      $i++;
    }
    $_SESSION['tchart'] = $tchart;

    if ($roadto) {
      $chartw=300;
    } else {
      $chartw=600;
    }
    if ($debug!=1) {
    ?>
    <!-- <div id="cover" style="position:absolute;width:630px;height:1000px;z-index:10;display:block"><img src="images/pixel.gif" width="630" height="1000" border="0" alt=""></div> -->
        <?php
    }
    $unique="uniqueID=" . uniqid(rand(),true);
    $graphHeight = 250;
    if ($roadto) {
	    $charturl="charts/traffic.php";
	    if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
        include($charturl);
    } else {
	    $charturl="charts/traffic2.php";
	    if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
        include($charturl);
        
    }

    //make another chart
    if ($roadto) {
	    reset($merge);
	    $maxval=0;
	    $i=0;
	    $schart[ 'names' ][ 0 ] = "";
	    $schart[ 'names' ][ 1 ] = ""._CONVERSION_RATE;
	    $schart[ 'names' ][ 2 ] = ""._ROLLING_AVERAGE;
	    $schart[ 'names' ][ 3 ] = ""._TREND;
	    while (list ($day, $row) = each ($merge)) {
		    if ($maxval < $row[3]) {
			    $maxval = $row[3];
		    }
		    if ($maxval < $row[4]) {
			    $maxval = $row[4];
		    }
		    $schart[ 0 ][ $i ]=$day;
		    $schart[ 1 ][ $i ] = $row[3];
		    $schart[ 2 ][ $i ] = $row[4];
		    //$schart[ 3 ][ $i ] = $regval[($i-1)];
            if ($regval[($i)] < 0) {
                $schart[ 3 ][ $i ] = 0;
            } else {
                $schart[ 3 ][ $i ] = $regval[($i)];    
            }
        $i++;
	    }
	    $_SESSION['schart'] = $schart;
	    
	    if ($n > 20) {
		    $chartw=300;
		    $skip="skip=3"; 
	    } else {
		    $chartw=300;
		    $skip="skip=0";
	    }
	    $unique="uniqueID=" . uniqid(rand(),true);
	    $charturl="charts/conversion.php";
	    if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
	    include($charturl);
    }
    echo "</div>";

    /*-------------------*/
    /*-------------------*/
    /*-------------------*/
    /*-------------------*/
    /*-------------------*/

    if ($sourcetype!="referrer") {
    // now we're going to do the referrer graph
    echo "<hr noshade size=1 style=\"clear:both;\">";  
	    $unique="uniqueID=" . uniqid(rand(),true);
	    $charturl="charts/referrergraph.php";
	    if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
        include($charturl);
    $unique="uniqueID=" . uniqid(rand(),true);
    $graphHeight = 400;
    if ($roadto) {
    //do a converting referrers pie chart
	    $charturl = "charts/refpie.php";
	    if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
		include('charts/refpie.php');
    } else {
	    $charturl="charts/refpie2.php";
	    if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
		include($charturl);
      //echo $charturl;
    }
    }
    echo "<hr noshade size=1 style=\"clear:both;\">";
    /*-------------------*/
    /*-------------------*/
    /*-------------------*/
    /*-------------------*/
    /*-------------------*/
    // now we're going to do the country graph
    
      $unique="uniqueID=" . uniqid(rand(),true);
      $graphHeight = 400;
	  $charturl="charts/country.php";
	    if ($debug==1) { echo "<a href=\"charts/country.php?$unique&to=$to&from=$from&conf=$conf&period=$period&limit=$limit&source=$source&sourcetype=$sourcetype&debug=1\">chart</a><br>"; }
	    include($charturl);


    if ($roadto) {
	    // now the countries that convert
	    $unique="uniqueID=" . uniqid(rand(),true);
	    $graphHeight = 400;
	    $charturl="charts/country_convert.php";
        if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; } 
	    include($charturl);
    } else {
	    $unique="uniqueID=" . uniqid(rand(),true);
	    if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
            include('charts/country_pie.php');
    }
    echo "</div>";
	
} else if ($labels==_ERROR_TRENDS) {
    ?>
    <div class="toplinegreen" style="padding: 2px;">
    <font size="+1" color="black">
        <?php echo $labels; ?></font> <font size="-1"> &nbsp;<br><b><?php echo""._DATE_FROM?></b> <?php echo date("D, d M Y / H:i", $from); ?> <b><?php echo""._DATE_TO?></b> <?php echo date("D, d M Y / H:i", $to); ?></font>    <div style="display : none; line-height : 18px; position : relative;">
        </div>
    </div>
    <?php

    // now we're going to do the error graph
    
    $unique="uniqueID=" . uniqid(rand(),true);
    $graphHeight = 300;
    $charturl="charts/errorgraph.php";
    if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
    include($charturl);
    
    $unique="uniqueID=" . uniqid(rand(),true);
    $charturl="charts/error_pie.php";
    if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
    include($charturl);
    echo "\r\n";
    
    /* this part crashes the browser, I have no idea why
    $errorcodes  = $db->Execute("select code, concat(code,\" - \",descr) as pd from lgstatus");
    //echo    "select code, concat(code,\" - \",descr) as pd from lgstatus";
    
    while ($ec_data=$errorcodes->FetchRow()) { 
        echo  "<P>\r\n";
        $status=$ec_data["code"];
        $descr=$ec_data["pd"];
        $query  = "select count(visitorid) as hits, concat(url,params) as furl, concat(referrer,refparams) from $profile->tablename where timestamp >=$from and timestamp <=$to and status='$status' group by furl order by hits desc limit $limit";
        $showfields="Requests,Page,Referrer";
        $labels=$descr;
        ob_start();
        StatsTable($from,$to,$showfields,$labels,$query,$drilldown,$filter); 
        echo "\r\n";
        $contents = ob_get_contents();
        ob_end_clean();
        if (strpos($contents, "graphborder")!=FALSE) { // indicates we have a table with contents 
            echo $contents;      
        }
        
    }  
    */  

} else if ($labels==_BROWSER_TRENDS) {
    
    ?>
    <div class="toplinegreen" style="padding: 2px;">
        <font size="+1" color="black">
        <?php echo $labels; ?></font> <font size="-1"> &nbsp;<br><b><?php echo""._DATE_FROM?></b> <?php echo date("D, d M Y / H:i", $from); ?> <b><?php echo""._DATE_TO?></b> <?php echo date("D, d M Y / H:i", $to); ?></font>    <div style="display : none; line-height : 18px; position : relative;">
        </div>
    </div>
    <?php

    // now we're going to do the browsers graph

    $unique="uniqueID=" . uniqid(rand(),true);
    $graphHeight = 400;
    $charturl="charts/browsergraph.php";
    if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
    include($charturl);
    
    $unique="uniqueID=" . uniqid(rand(),true);
    $charturl="charts/browser_pie.php";
    if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
    include($charturl);
    
    echo "<hr noshade size=1 style=\"clear:both;\">";  
        $unique="uniqueID=" . uniqid(rand(),true);
        $charturl="charts/explorer_pie.php";
        if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
        include($charturl);
        
        $unique="uniqueID=" . uniqid(rand(),true);
        $charturl="charts/firefox_pie.php";
        if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
        include($charturl);
        
    echo "<hr noshade size=1 style=\"clear:both;\">"; 
        $unique="uniqueID=" . uniqid(rand(),true);
        $charturl="charts/otherbrowsers_pie.php";
        if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
        include($charturl);
        
        $unique="uniqueID=" . uniqid(rand(),true);
        $charturl="charts/os_pie.php";
        if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
        include($charturl);
        ?>
        <ul class="trendsborder" style="border-top:1px solid #CCCCCC;clear:both;min-width:640px; margin:0; padding:0 0 0 100px; height: 200px;">
        	<li style='list-style:none;margin:0;padding:0;'><?php echo""._OTHER_RECOURCES_THAT_MAY_BE_USEFUL?>:</li>
            <li> <a href="http://www.w3schools.com/browsers/browsers_stats.asp" target="_blank">Browser Statistics and Trends from W3Schools</a>
            <li> <a href="http://www.upsdell.com/BrowserNews/" target="_blank">Browser News Weekly</a>
            <li> <a href="http://news.com.com/2038-12_3-0-topic.html?id=6244&amp;kname=Web+browsers" target="_blank">Web Browser News from C|net</a>
            <li> <a href="http://webdesign.about.com/od/internetexplorer/a/aa082906.htm" target="_blank">Designing for IE and Firefox (Tips)</a>
            
        </ul>
        
        <?php
} else if ($labels==_PERFORMANCE_TRENDS) {
    
?>
	<div class="toplinegreen" style="padding: 2px;">
	<font size="+1" color="black">
        <?php echo $labels; ?></font> <font size="-1"> &nbsp;<br><b><?php echo""._DATE_FROM?></b> <?php echo date("D, d M Y / H:i", $from); ?> <b><?php echo""._DATE_TO?></b> <?php echo date("D, d M Y / H:i", $to); ?></font>    <div style="display : none; line-height : 18px; position : relative;">
        </div>
    </div>
    <?php
    // now we're going to do the performance graph

    $unique="uniqueID=" . uniqid(rand(),true);
    $charturl="charts/performancegraph.php";
    if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
    include($charturl);
    
    $unique="uniqueID=" . uniqid(rand(),true);
    $charturl="charts/performance_pie.php";
    if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
    include($charturl);
    
    echo "<hr noshade size=1 style=\"clear:both;\">"; 
    $unique="uniqueID=" . uniqid(rand(),true);
    $charturl="charts/conversiongraph.php";
    if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
    include($charturl);

} else if ($labels==_SEARCH_TRENDS) {
    
?>
	<div class="toplinegreen" style="padding: 2px;">
        <font size="+1" color="black">
        <?php echo $labels; ?></font> <font size="-1">&nbsp;<sup><a href="javascript:helpbox('kwtable')" id="greenhelplink" class="greenlink"><?php echo""._SHOW_DATA?></a></sup><br><b><?php echo""._DATE_FROM?></b> <?php echo date("D, d M Y / H:i", $from); ?> <b><?php echo""._DATE_TO?></b> <?php echo date("D, d M Y / H:i", $to); ?></font>    <div style="display : none; line-height : 18px; position : relative;">
        </div>
    </div>
    
    <?php
    echo "<div id=\"kwtable\" style=\"display: none;\">";
    $rlabel=_TOP_KEYWORDS;
    $showfields = _VISITORS.","._HITS.","._KEYWORDS.","._LANDING_PAGE.","._PARAMETERS;
    $query  = "select count(distinct visitorid) as visitors, count(a.keywords) as hits,k.keywords, u.url, up.params from $profile->tablename as a,$profile->tablename_urls as u,$profile->tablename_urlparams as up,$profile->tablename_keywords as k where timestamp >=$from and timestamp <=$to and a.url=u.id and a.params=up.id and a.keywords=k.id and k.keywords!='' and crawl=0 group by a.keywords order by visitors desc limit $limit";
    $help=""._HELP_TEXT;
    $mini=2;
    StatsTable($from,$to,$showfields,$rlabel,$query,$drilldown,$filter);
    echo "</div>";
    
    // now we're going to do the keyword graph

    $unique="uniqueID=" . uniqid(rand(),true);
    $charturl="charts/keywordgraph.php";
    if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
    include($charturl);
    $unique="uniqueID=" . uniqid(rand(),true);
    $charturl="charts/keyword_pie.php";
    if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
    include($charturl);
    
    echo "<div class=breaker></div>";
    
    $charturl="charts/searchenginegraph.php";
    if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
    include($charturl);
    
    $unique="uniqueID=" . uniqid(rand(),true);
    $charturl="charts/searchengine_pie.php";
    if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
    include($charturl);
    
    
    
}  else if ($labels==_CRAWLER_TRENDS) {
    
?>
	<div class="toplinegreen" style="padding: 2px;">
        <font size="+1" color="black">
        <?php echo $labels; ?></font> <font size="-1"> &nbsp;<br><b><?php echo""._DATE_FROM?></b> <?php echo date("D, d M Y / H:i", $from); ?> <b><?php echo""._DATE_TO?></b> <?php echo date("D, d M Y / H:i", $to); ?></font>    <div style="display : none; line-height : 18px; position : relative;">
        </div>
    </div>
    <?php
    // now we're going to do the crawler graph

    $unique="uniqueID=" . uniqid(rand(),true);
    $charturl="charts/crawlergraph.php";
    if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
    include($charturl);    

    $unique="uniqueID=" . uniqid(rand(),true);
    $charturl="charts/crawler_pie.php";
    if ($debug==1) { echo "<a href=\"$charturl&debug=1\">chart</a><br>"; }
    include($charturl);
}
echo "</div>";

$endtime=getmicrotime();
//echo "<P>this took:" .($endtime-$starttime) . "<P>";
/*?>
<div align="center"><font face="ms sans serif,arial" size="1" color="gray">
This report is still a "beta" version. Please give us feedback if you have suggestions.<br> 
<a class="nodec" href="credits.php<?php echo "?conf=$conf"; ?>">&copy; 2005-<?php echo date('Y');?> Logaholic BV</a></font>
</div>*/?>
</body>
</html>
