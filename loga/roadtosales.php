<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
@set_time_limit(86400);

$nocache = isset($_REQUEST["nocache"]) ? $_REQUEST["nocache"] : "";
$submit = @$_REQUEST["submitbut"];
$filter = @$_REQUEST["filter"];
$report = @$_REQUEST["report"];
$status = @$_REQUEST["status"];
$agent = @$_REQUEST["agent"];
$roadto = @$_REQUEST["roadto"];
$labels = @$_REQUEST["labels"];
//$labels = "The Road To Sales";
$drilldown = @$_REQUEST["drilldown"];

require "top.php";

$nicefrom = date("D, d M Y / H:i",$from);
$niceto = date("D, d M Y / H:i",$to);
?>
<div id="loading" style="position:absolute;left:100px;visibility:visible;">
&nbsp;<p>
<table bgcolor=white cellspacing=0 cellpadding=3>
<tr><td class=toplinegreen><font size="+1"><?php echo _BUILDING_REPORT;?>...</font></td></tr>
<tr><td bgcolor="#f0f0f0" class=dotline valign=middle><img src="images/Hourglass_icon.gif" width=32 height=32 alt="" border="0" align=left vspace=4 hspace=4>
<?php echo _WAIT_WHILE_REPORT_IS_BEING_CREATED;?><br>
<?php echo _NOW_CALCULATING;?> <b><?php echo "$labels</b> "._DATE_FROM."<br> $nicefrom "._DATE_TO." $niceto"; ?></b>
</td></tr>
</table>
</div>
<?php
flush();

if ($nocache) {
	$nc=strpos($_SERVER['QUERY_STRING'],"&nocache");
	$pstring=$_SERVER['PHP_SELF'] ."?". substr($_SERVER['QUERY_STRING'],0,$nc);
} else {
	$pstring=$_SERVER['PHP_SELF'] ."?". $_SERVER['QUERY_STRING'];
}
//echo $pstring;

$cachename = 'cache_' . md5($pstring);
if ($profile->usepagecache) {
	if ($nocache) {
		deleteProfileData($profile->profilename, $cachename);		
  	    echo "<span style=\"position:relative;margin-top:-28px;line-height:18px;text-align:right;z-index:200;float:right;font-size:10px;color:red;\">"._DELETED_CACHED_FILE."</span>";
	} elseif ($content = getProfileData($profile->profilename, $cachename, "")) {
		$cached="yes";
		echo "<span style=\"position:relative;margin-top:-28px;line-height:18px;text-align:right;z-index:200;float:right;font-size:10px;color:gray;\">[ "._CACHED_REPORT."]<br><a class=graylink href=$pstring&nocache=$cachename>"._RECALCULATE."</a></span>";
  	echo $content;
		echo "<script>  loading.style.visibility=\"hidden\";  </script>";
		exit();
	}
}
	
ob_start();
/* 
................................................
.......... Main program starts here ............
................................................
*/
require "queries.php";
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
		$from   = mktime(0,0,0,date("m"),01,date("Y"));
		$to     = mktime(23,59,59,date("m"),date("d"),date("Y"));
		$labels = "The Road To Sales";
	}


// Build Screen

// Create Report Area
	
?>
<SCRIPT language="JavaScript">
function submitform()
{
  document.report.submit();
}
</SCRIPT> 
<?php
	echo "<div style=\"position:relative;width:100%;z-index:10;float:left; margin-top: -35px;\">";
  echo "<form method=get action=roadtosales.php id=\"form1\" name=\"form1\"><table border=0><tr><td width=70><b>"._DATE_RANGE." </b></td><td>";
  QuickDate($from,$to);
  echo "</td><td>";
  newDateSelector($from,$to);
  echo "<input type=hidden name=report value=\"$report\">";
  echo "<input type=hidden name=conf value=\"$conf\">";
  echo "<input type=hidden name=status value=\"$status\">";
  echo "<input type=hidden name=agent value=\"$agent\">";
  echo "<input type=hidden name=labels value=\"$labels\">";
    
  echo "</td><td><input type=submit name=submitbut value=Report class=small><input type=hidden name=but value=Report> <a id=\"moreoptions\" class=graylink href=\"javascript:moreoptions();\">"._MORE_OPTIONS."</a>"; 
 
  echo "</td></tr></table>";
  if (!$limit) {
       $limit=10;
  }
  echo "<table><tr><td width=70><b>"._TARGET.":</b></td><td>";
  echo "<select name=roadto>";

  if ($profile->targetfiles) {
        $targets=explode(",",$profile->targetfiles);
        $tl="";
        foreach ($targets as $thistarget) {
            if ($thistarget) {
                if (trim($thistarget)==$roadto) { $sel="SELECTED"; } else { $sel=""; }
                $tl.= "<option $sel value=\"".trim($thistarget)."\">".trim($thistarget)."\n";
            }
        }
        
        if (!$sel && !$roadto) {
            $roadto=$targets[0];
        }
    }
  echo "</select>";
  
  
  echo "</td><td> ";
  //echo "<b>Limit:</b> </td><td><input type=text size=3 name=limit value=\"$limit\"  class=small> (Maximum results)";
  echo "</td></tr></table>";
  
  echo "<div id=\"advancedUI\" style=\"display:none;position:relative;\">";
  
  echo "<table border=0>";
  //echo "<tr><td width=70><b>Filters:</b></td><td>";
  //if (getTrafficSources()) { echo printTrafficSourceSelect() . "<a class=graylink href=\"filters.php?conf=$conf\">create new filter</a>"; } else { echo "Filters are only supported on Mysql > 4.1 servers"; }
  //echo "</td></tr><tr><td colspan=2> </td></tr><tr>";
  echo "<tr><td width=70 title=\""._MAX_NUMBER_OF_RESULTS_TO_SHOW."\">";
  
  echo " <b>"._LIMIT.":</b> </td><td><input type=text size=3 name=limit value=\"$limit\"  class=small> ("._MAX_RESULTS.")";
  
  echo "</td></tr></table>";
  ?>  
  </div></td></tr></table>
  <?php
  echo "</div><div class=\"breaker\"></div>";
  
  //echo "<table cellpadding=2 cellspacing=2 border=1 style=\"float:left;\"><tr><td colspan=2>";
  //echo "</td></tr><tr><td rowspan=6 valign=top>";
  //newQuerySelectorHTML($labels); 
  //echo "<br></td><td valign=top>";
  echo "<div id=\"reportmenu\" style=\"position:absolute;left:10px;width:160px;margin-top:2px;\">";
  SummaryMenu($labels);
  echo "</div>\n";

  echo "<div id=\"ReportContainer\" style=\"padding-left:167px;min-width:630px;\">\n";
  
if (!$roadto) {
	echo "<strong>"._NO_TARGET_FILE_FOUND.".</strong> "._SPECIFY_TARGET_FILE_IN_PROFILE.".";
    echo "<script>  loading.style.visibility=\"hidden\";  </script>";

	exit();
}

echo "<h3 style=\"margin-top:0px;\">"._THE_ROAD_TO_SALES."</h3>";
$nicefrom = date("D, d M Y / H:i",$from);
$niceto = date("D, d M Y / H:i",$to);
echo _THE_ROAD_TO.": <b><font color=red>$roadto</font></b>";
echo " - <font size=-1><b>"._DATE_FROM."</b> $nicefrom <b>"._DATE_TO."</b> $niceto</font><P>";

$query = $db->Execute("select visitorid,timestamp from $profile->tablename as a,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and crawl=0 and a.url=u.id and u.url='$roadto' group by visitorid");
$i=1;
$qpart = "0";
$notqpart = "0";
while ($data = $query->FetchRow()) {
			//echo $data["ipnumber"] . "<br>";
			if ($i==1) {
				 $qpart = "(visitorid='".$data["visitorid"]."' and timestamp < ".$data["timestamp"] . ") ";
				 $notqpart = "visitorid!='".$data["visitorid"]."' ";
			} else {
				 $qpart .= "or (visitorid='".$data["visitorid"]."' and timestamp < ".$data["timestamp"] . ") ";
				 $notqpart .= "and visitorid!='".$data["visitorid"]."' ";
			}
			$i++;
}
$totbuy=$i-1;
//echo "select count(distinct visitorid) as uniq from $profile->tablename force index (timestamp) where timestamp >=$from and timestamp <=$to and crawl=0 and ($notqpart)";
$query = $db->Execute("select count(distinct visitorid) as uniq from $profile->tablename " . ($databasedriver == "mysql" ? "force index (timestamp)" : "") ." where timestamp >=$from and timestamp <=$to and crawl=0 and ($notqpart)");
$data = @$query->FetchRow();
$tot=$data["uniq"];

$mini=1;

// make 'labeled' totals
$ltot="($tot)";
$ltotbuy="($totbuy)";


echo "<table><tr><td valign=top>";
$labels=_TOP_PAGES_AS_PERC_OF_CONV_USERS." $ltotbuy";
$showfields = _PAGE.','._USERS.','._USERS_PERC;
$query  = "select u.url as url,count(distinct visitorid) as users,((count(distinct visitorid)*1.00)/$totbuy*100) as visitors from $profile->tablename as a,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and ($qpart) and a.url=u.id and u.url!='$roadto' and status='200' group by a.url order by visitors desc limit $limit";
//$showfields = "Page,Users%";
//$query  = "select url,(count(distinct visitorid)/$totbuy*100) as visitors from $profile->tablename where timestamp >=$from and timestamp <=$to and ($qpart) and url!='$roadto' group by url order by visitors desc limit $limit";
//echo $query;
StatsTable($from,$to,$showfields,$labels,$query,$drilldown,$filter);

echo "</td><td valign=top>";

$labels=_TOP_PAGES_NON_CONV_USERS." $ltot";
$showfields = _PAGE.','._USERS.','._USERS_PERC;
$query  = "select u.url,count(distinct visitorid) as users,((count(distinct visitorid)*1.00)/$tot*100) as visitors from $profile->tablename as a,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and ($notqpart) and status='200' and a.url=u.id group by a.url order by visitors desc limit $limit";
//$showfields = "Page,Users%";
//$query  = "select url,(count(distinct visitorid)/$tot*100) as visitors from $profile->tablename where timestamp >=$from and timestamp <=$to and ($notqpart) group by url order by visitors desc limit $limit";
StatsTable($from,$to,$showfields,$labels,$query,$drilldown,$filter);

echo "</td></tr><tr><td colspan=2>&nbsp;<P></td></tr>";

echo "<tr><td valign=top>";
$labels=_TOP_KEYWORDS_AS_PERC_OF_CONV_USERS." $ltotbuy";
$showfields = _KEYWORDS.','._USERS.','._USERS_PERC;
$query  = "select k.keywords,count(distinct visitorid) as users,((count(distinct visitorid)*1.00)/$totbuy*100) as visitors, url from $profile->tablename as a,$profile->tablename_keywords as k where timestamp >=$from and timestamp <=$to and a.keywords=k.id and k.keywords!='' and crawl=0  and ($qpart) group by a.keywords order by visitors desc limit $limit";
//$showfields = "Keywords,Users%";
//$query  = "select keywords,(count(distinct visitorid)/$totbuy*100) as visitors, url from $profile->tablename where timestamp >=$from and timestamp <=$to and keywords!='' and crawl=0  and ($qpart) group by keywords order by visitors desc limit $limit";
StatsTable($from,$to,$showfields,$labels,$query,$drilldown,$filter);
//echo $query;

echo "</td><td valign=top>";

$labels=_TOP_KEYWORDS_NON_CONV_USERS." $ltot";
$showfields = _KEYWORDS.','._USERS.','._USERS_PERC;
$query  = "select k.keywords,count(distinct visitorid) as users,((count(distinct visitorid)*1.00)/$tot*100) as visitors from $profile->tablename as a,$profile->tablename_keywords as k where timestamp >=$from and timestamp <=$to and a.keywords=k.id and k.keywords!='' and crawl=0 and ($notqpart) group by a.keywords order by visitors desc limit $limit";
//$showfields = "Keywords,Users%";
//$query  = "select keywords,(count(distinct visitorid)/$tot*100) as visitors, url from $profile->tablename where timestamp >=$from and timestamp <=$to and keywords!='' and crawl=0 and ($notqpart) group by keywords order by visitors desc limit $limit";

StatsTable($from,$to,$showfields,$labels,$query,$drilldown,$filter);

echo "</td></tr><tr><td colspan=2>&nbsp;<P></td></tr>";

echo "<tr><td valign=top>";
$labels=_TRAFFIC_SOURCES_AS_PERC_OF_CONV_USERS." $ltotbuy";
$showfields = _REFERRER.','._USERS.','._USERS_PERC;
$query  = "select r.referrer,count(distinct visitorid) as users,((count(distinct visitorid)*1.00)/$totbuy*100) as visitors from $profile->tablename as a,$profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and a.referrer=r.id and r.referrer!='-' and r.referrer NOT like '%$profile->confdomain%' and crawl=0  and ($qpart) group by a.referrer order by visitors desc limit $limit";
StatsTable($from,$to,$showfields,$labels,$query,$drilldown,$filter);
//echo $query;

echo "</td><td valign=top>";

$labels=_TRAFFIC_SOURCES_NON_CONV_USERS." $ltot";
$showfields = _REFERRER.','._USERS.','._USERS_PERC;
$query  = "select r.referrer,count(distinct visitorid) as users,((count(distinct visitorid)*1.00)/$tot*100) as visitors from $profile->tablename as a,$profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and a.referrer=r.id and r.referrer!='-' and r.referrer NOT like '%$profile->confdomain%' and crawl=0 and ($notqpart) group by a.referrer order by visitors desc limit $limit";
//$showfields = "Referrer,Users%";
//$query  = "select referrer,(count(distinct visitorid)/$tot*100) as visitors, url from $profile->tablename where timestamp >=$from and timestamp <=$to and referrer!='-' and referrer NOT like '%$profile->confdomain%' and crawl=0 and ($notqpart) group by referrer order by visitors desc limit $limit";
StatsTable($from,$to,$showfields,$labels,$query,$drilldown,$filter);

/*
echo "</td></tr><tr><td colspan=2>&nbsp;<P></td></tr>";

echo "<tr><td valign=top>";
$labels="Days of the week as % of Converted Users ($totbuy)";
$showfields = "Date,Visitors,Pageviews";
$query  = "select FROM_UNIXTIME(timestamp,'%W') AS days, count(distinct visitorid),count(*) as hits,(count(*)/count(distinct visitorid)) as ppu from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 and ($qpart) group by days order by FROM_UNIXTIME(timestamp,'%w')";
StatsTable($from,$to,$showfields,$labels,$query,$drilldown,$filter);
//echo $query;

echo "</td><td valign=top>";

$labels="Days of the week - Non-Converted Users ($tot)";
$showfields = "Date,Visitors,Pageviews";
$query  = "select FROM_UNIXTIME(timestamp,'%W') AS days, count(distinct visitorid),count(*) as hits,(count(*)/count(distinct visitorid)) as ppu from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 and ($notqpart) group by days order by FROM_UNIXTIME(timestamp,'%w')";
StatsTable($from,$to,$showfields,$labels,$query,$drilldown,$filter);
*/

echo "</td></tr></table>";



	
// Print Statistics table
//	StatsTable($from,$to,$showfields,$labels,$query,$drilldown,$filter);
//makegraph($from,$to,$query);

echo "<P><hr noshade size=1><b>"._PLEASE_NOTE.":</b><P>"._THIS_REPORT_ATTEMPS_ANSWER_THIS_QUESTION." .<P>";
echo _THESE_STATS_SHOW."</b></i> ;-)<P>";


echo "</div>";

$rend=getmicrotime();
$rtook=number_format(($rend-$rstart),2);
echoDebug("<P>&nbsp;<p>&nbsp;<p><table width=500 cellpadding=3 border=0><tr><td rowspan=2>&nbsp;&nbsp;</td><td><font face=\"ms sans serif,arial\" size=1 color=silver>MySQL query:</font></td></tr><tr><td class=dotline2 bgcolor=#F8F8F8><font face=\"ms sans serif,arial\" size=1 color=gray>$query<P>Page took $rtook sec to build</font></td></tr></table></dir>");
?>
<P>
&nbsp;
<P>
&nbsp;
<div align=center><font face="ms sans serif,arial" size=1 color=#f0f0f0>
&copy; 2005-<?php echo date('Y');?> Logaholic BV</font></div>
<P>
&nbsp;
</body>
</html>
<?php
// end of report
$contents = ob_get_contents();
ob_end_clean();
echo $contents;
if ($profile->usepagecache) {
	setProfileData($profile->profilename, $cachename, $contents);
}

?>
