<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 

if (!file_exists("files/global.php")) {
        header("Location: install.php");
        exit();
}
@set_time_limit(900);

require_once "common.inc.php";

// if(isset($_SESSION['new_ui'])) { $new_ui = $_SESSION['new_ui']; }
// if(isset($_REQUEST['new_ui'])) { $new_ui = $_REQUEST['new_ui']; }
// if(!isset($new_ui)) { $new_ui = false; }

if(isset($_REQUEST['conf'])) { $conf = $_REQUEST['conf']; }

$filter = @$_REQUEST["filter"];
$query = @$_REQUEST["query"];
$nocache = @$_REQUEST["nocache"];
$showcharturl = @$_REQUEST["showcharturl"];
$savecustom = @$_POST["savecustom"];
$_SESSION["trafficsource"]=0;

// IF we've not set up our initial database stuff, then redirect to install
if (!isset($conf)) {
	if (!file_exists("files/global.php")) {
		header("Location: install.php?".@$_SERVER["QUERY_STRING"]);
		exit();
	} else {
		if ((!$validUserRequired) || ($session->logged_in)) {
			$conf = getDefaultProfileName();
			if (!$conf) {
				header("Location: profiles.php?".@$_SERVER["QUERY_STRING"]);
			}
		}
	}
}

if(isset($new_ui) && $new_ui == 1) {
	header("Location: v3.php?conf={$conf}");
	exit;
}          

if (@$labels!="" && @$labels!=_TODAY) {
    //redirect to main reports
    echo "redirect";
    exit();
    header("Location: reports.php?".@$_SERVER["QUERY_STRING"]);   
}


// If it's demo mode, check with the server and see if the demo has expired.
/* let just trust the phplockit stuff for now, since this thing causes to many support questions
if ((!defined("_LICENSE_MODE_")) || (_LICENSE_MODE_ == 0)) {    
	if ($_SERVER['SERVER_NAME']!='localhost') {
		$regurl="http://www.logaholic.com/trialregistration.php?ip=".$_SERVER['SERVER_ADDR']."&site=".$_SERVER['SERVER_NAME']."&t=".time();
		$reg_response=file_get_contents($regurl) or die(_TRIAL_CHECK_FAILED);
		if ($reg_response!="1") {
			echo $reg_response;
			exit();
		}
	}
}
*/                                                   
// end check

require "top.php";
require "queries.php"; 

$langcachename = $profile->profilename . ".lastlang";
$lastlang = getProfileData($profile->profilename, $langcachename, "");
if ($lastlang != $lang) {
    setProfileData($profile->profilename, $langcachename, $lang);
    deleteProfileData($profile->profilename, "cache_%");
    deleteProfileData($profile->profilename, $profile->profilename."cache_trail");
}

// todaysdate is the current date/time used to calculate everything and run queries.  To have this set to
// end of day yesterday (for example, if you use a cron job), use this:
if ($to) {
    $todaysdate = $to;
} else {
    $todaysdate = time();
}

// if today is not actually today, we need to warn people
$real_today = date("m") . date("d") . date("Y");
$gen_today = date("m", $todaysdate) . date("d", $todaysdate) . date("Y", $todaysdate);
if ($real_today!=$gen_today) {
    $warning = "<br><span style=\"height:18px;\"><font color=gray size=1 face=\"ms sans serif, arial\">&nbsp;"._WARNING_NOT_TODAY." (". date("l - d M Y", time()) . ")</font></span>";
    $todaylabel="<font color=red>"._TODAY_OVERVIEW." "._FOR.":</font> ";
    $nocache=1;
} else {
    $todaylabel=_TODAY.", ";
}   

// $do_today_as_yesterday *might* be defined in user_settings.php.  This is an "undocumented" setting, though.
if (@$do_today_as_yesterday) {
	$todaysdate = mktime(23, 59, 59, date("m") , date("d") - 1, date("Y"));
}

// check if we need to dave custom reports
if ($savecustom) {
        while(list($varname, $varvalue) = each($_POST)) {
            if ($varvalue!=$conf && $varname!="savecustom") {  
                //echo "$$varname = $varvalue";
                if (strpos($varname,"order")!=FALSE) {
                    $dash[$varvalue]="$lastlabel::$lastwidth::$lasttype";
                }
                if (strpos($varname,"label")!=FALSE) {
                    $lastlabel=$varvalue;
                }
                if (strpos($varname,"width")!=FALSE) {
                    $lastwidth=$varvalue;
                }
                if (strpos($varname,"type")!=FALSE) {
                    $lasttype=$varvalue;
                }
            }  
        }
        ksort($dash);
        $dashboard_reports= "";
        foreach ($dash as $d) {
            $dashboard_reports .= "$d,";    
        }
        $dashboard_reports = substr($dashboard_reports,0,-1);
        setProfileData($profile->profilename,"$profile->profilename.dashboard_reports",$dashboard_reports);
        //echo "saved $dashboard_reports";
}

	?>
	<div id="loading" style="position:absolute;left:100px;display:block;z-index:10;">
	&nbsp;<p>
	<table bgcolor=white cellspacing=0 cellpadding=3>
	<tr><td class=toplinegreen><font size="+1"><?php echo _BUILDING_TODAY_SCREEN;?>...</font></td></tr>
	<tr><td bgcolor="#f0f0f0" class=dotline valign=middle><img src="images/Hourglass_icon.gif" width="31" height="31" alt="<?php echo _PLEASE_WAIT_ALTTEXT;?>" border="0" align="left" vspace="4" hspace="4">
	&nbsp;<br>
	<?php echo _PLEASE_WAIT_REPORT_IS_CREATED;?>&nbsp;<br>
	
	</td></tr>
	</table>
	</div>
	<?php
	flush();
	
if ($nocache) {
	$nc=strpos($_SERVER['QUERY_STRING'],"&nocache");
	$pstring=$_SERVER['PHP_SELF'] ."?". substr($_SERVER['QUERY_STRING'],0,$nc);
} else {
	$pstring=$_SERVER['PHP_SELF'] ."?". @$_SERVER['QUERY_STRING'];
}	

$cachename = "cache_index.$profile->profilename.$to";
$content = "";
	
//start of what used to be today.php
$pagestart=getmicrotime();

if(!$from) {
	$from   = mktime(0,0,0,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
	$to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
	$wipe=1;
}					

  // this is the customizer div, must be at the top so it can still be opened even if the page expodes somewhere along the line
  echo '<DIV id="dialog" title="'._CUSTOMIZE_PAGE.'" style="display:none;"></DIV>';
// date selection
  echo "<div class='form1-wrap'>";
  /*echo "<div class=\"controls\">";  
  //echo "<span style=\"float:right\" class=small><a href=\"javascript:AjaxGet('includes/customizer.php?conf=$conf','customizer');\">Customize page</a> | <a href=\"javascript:delpositions()\">Remove custom table positions</a></span>";
  //echo "<form method=get action=index.php id=\"form1\" name=\"form1\"><table border=0><tr><td width=70><b>Date Range: </b></td><td>";
  */
  echo "<form method=get action=index.php id=\"form1\" name=\"form1\"><table border=0><tr><td width=72><b>"._DATE_RANGE." </b></td><td>";
  QuickDate($from,$to);
  echo "</td><td>";
  newDateSelector($from,$to);
  echo "<input type=hidden name=conf value=\"$conf\">";
  echo "<input type=hidden name=labels value=\"$labels\">";
	
  echo "</td><td><input type=submit name=submitbut value=Report class=small><input type=hidden name=but value=Report> <a id=\"moreoptions\" class=graylink href=\"javascript:moreoptions();\">"._MORE_OPTIONS."</a> &nbsp;<a class=\"graylink\" href=\"javascript:customizepage('includes/customizer.php?conf=$conf');\">"._CUSTOMIZE_PAGE."</a>"; 
 
  echo "</td></tr></table>";
  echo "<div id=\"advancedUI\" style=\"display:none;position:relative;\">";
  
  echo "<table border=0>";
  echo "<tr><td width=70><b>"._FILTERS.":</b></td><td>";
  $available="no";
  echo printTrafficSourceSelect();
  //echo "Not available on Today report";
  echo "</td></tr><tr><td colspan=2> </td></tr><tr>";
  echo "<td title=\""._MAX_NUMBER_OF_RESULTS_TO_SHOW."\">";
  if (!$limit) {
       $limit=100;
  }
  
  echo "<b>Limit:</b> </td><td>";
  
  echo "<input type=text size=3 name=limit value=\"$limit\"  class=small> ("._MAX_NUMBER_OF_RESULTS_TO_SHOW.") ";

  echo "</td></tr></table>";  
  echo "</div></form>";
  echo "</div><div class=\"breaker\"></div>";
  ?>
       <script type="text/javascript">
  $(document).ready(function(){
    $("#progressbar").progressbar({ value: 37 });
  });
  </script>

  
  <?php


if ($profile->usepagecache) {
    if ($nocache) {
        deleteProfileData($profile->profilename, $cachename);
      echo "<span style=\"position:relative;margin-top:-40px;line-height:18px;text-align:right;z-index:200;float:right;font-size:10px;color:red;border:0px solid red;\">"._DELETED_CACHED_FILE."</span>";
    } elseif ($content = getProfileData($profile->profilename, $cachename, "")) {
        echo "<span style=\"position:relative;margin-top:-50px;line-height:18px;text-align:right;z-index:200;float:right;font-size:10px;color:gray;border:0px solid red;\">["._CACHED_REPORT."]<br><a class=graylink href=\"$pstring&amp;nocache=$cachename\">"._RECALCULATE."</a></span>";
      echo $content;
    }
}
 
 ob_start(); 
 
if (!$content) {
    
echo "<div id=\"reportmenu\" style=\"position:absolute;left:10px;width:160px;margin-top:2px;border:0px solid red;\">";
//newQuerySelectorHTML($labels);
SummaryMenu($labels);
echo "</div>\n";
  
//exit();
if (@$wipe==1) {
	 $from="";                                   
	 $to="";
}

if(!$from) {
	$from   = mktime(0,0,0,date("m", $todaysdate)-1,01,date("Y", $todaysdate));
	$to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
	//CheckNotes();
	$from   = mktime(0,0,0,date("m", $todaysdate),01,date("Y", $todaysdate));
	$to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
} else {
	//CheckNotes();
}

/*echo "<div style=\"position:absolute;top:150px;left:175px;background:yellow;\">\n";    */
if (empty($debug)) {
?>
<script type="text/javascript">     

$(document).ready(function() {
    $('#ReportContainer').sortable(
    {
        items: '.todayreports', // the type of element to drag
        handle: '.MoveableToplinegreen', // the element that should start the drag event
        opacity: 0.3, // opacity of the element while draging
        placeholder: 'tobox', // class of the element displaying areas to move the element to
        scroll: true, // don't scroll the page while draging
        cursor: 'move',
        forcePlaceholderSize: true ,
        forceHelperSize: true ,
        cursorAt: 'top',
        tolerance: 'pointer' ,
        helper: 'clone',
        update: function(event, ui) {    
            var articles = $('.todayreports'); // select the list of articles
            var newPosition = articles.index(ui.item); // get the items new position in the article list
            //alert(ui.item.attr('id') + ' was moved to position ' + (newPosition)); // alert the item that was moved and the new position        
            //$("#dialog").dialog('open');
            AjaxGet('includes/customizer.php?conf=<?php echo $conf;?>&sort='+ui.item.attr('id')+'&newposition='+newPosition,'dialog');
        } // event to run when the order of the elemnts is updated
    }
    );
    /* $("#ReportContainer").disableSelection(); */
    $("#dialog").dialog({
		width:640,
		position: [150,80],
		modal: true,
		autoOpen: false
		
	}); 
});

</script>
<?php
}
/*echo "<div id=\"ReportContainer\" style=\"position:absolute;background:red;left:175px;right:10px;float:left;min-width:860px;\">\n";   */
echo "<div id=\"ReportContainer\" style=\"padding-left:165px;min-width:640px;\">\n";    

function todaytrends() {
    global $todaylabel,$todaysdate,$profile,$db,$from,$to,$warning,$vnum,$databasedriver,$gen_today,$real_today,$tableheaderfontcolor,$conf,$showcharturl;
    ?>
    <div id="_TODAY_TRENDS::600::_DASHBOARD_REPORTS" class="todayreports" style="float:left;">
    <table id="todaybox" cellspacing=0 cellpadding=0 border=0 width=605>
    <tr><td colspan=15 class="MoveableToplinegreen" style="padding:2px;cursor:move;">
	    <font size="+1" color="<?php echo $tableheaderfontcolor; ?>">
        <?php echo $todaylabel; ?> 
        <?php
        if (@$warning) {
            echo date("l - d M Y (H:i:s)", $todaysdate); 
            //echo "  ". @$warning;
        } else {
            echo date("l - d M Y (H:i:s)", time());  
        }
        ?>
        </font>
    </td></tr>
    <tr><td valign=top>

    <table cellspacing=0 cellpadding=2 border=0 width="100%">
	    
    <?php
    $t=getmicrotime();
    $labels=_TODAY;
    $realfrom=$from;
    $from   = mktime(0,0,0,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
    $to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
    $qstart=getmicrotime();
    if ($vnum < 4) {
	    $sqlmethod="use";
	    
    } else {
	    $sqlmethod="force";
    }
    //$query  = "select count(distinct visitorid) as uniq,count(*) as pages,(count(*)/(count(distinct visitorid)*1.00)) as ppu from $profile->tablename ".($databasedriver == "mysql" ? $sqlmethod." index (timestamp)":"")." where timestamp >=$from and timestamp <=$to and crawl=0";
    $query = "select visitors, pages, (pages/visitors) as ppu from $profile->tablename_vpd where days = FROM_UNIXTIME('$to','%d-%b-%Y %a')";
    //echo $query;
    $result = $db->Execute($query);
    echo "<tr bgcolor=silver><th>&nbsp;</th><th>"._VISITORS."</th><th>"._PAGES."</th><th title=\""._PAGES_PV_LONG."\">"._PAGES_PV_SHORT."</th><th title=\""._NEW_VS_RETURNING_LONG."\">"._NEW_VS_RETURNING_SHORT."</th></tr><tr><td><nobr>"._TODAY."</nobr></td>";
    if ($data = $result->FetchRow()) {
			    echo "<td><b>".number_format($data["visitors"],0)."</b></td>";
			    $uvtoday=$data["visitors"];
			    echo "<td><b>".number_format($data["pages"],0)."</b></td>";
			    echo "<td><b>".number_format($data["ppu"],2)."</b></td>";		
    } else {
                echo "<td><b>0</b></td>";
                $uvtoday=0;
                echo "<td><b>0</b></td>";
                echo "<td><b>0</b></td>";
    }
    //ReturnVisitors();
    if (($return_visitors = getProfileData($profile->profilename, "$profile->profilename.rv_today", 0)) && ($real_today==$gen_today)) { 
        $return_percentage = @intval(($return_visitors / $uvtoday) * 100);
        $new_percentage = 100 - $return_percentage;
        //echo "$return_percentage = @intval(($return_visitors / $uvtoday) * 100);";
    } else {
        $new_percentage = "-";
        $return_percentage = "-";
    }

    echo "<td><b>$new_percentage%</b> - <font color=green>$return_percentage%</font></td>";
    echo "</tr>";
    $qend=getmicrotime();
    //echo "query 1 took". ($qend-$qstart);

    $labels="last 2 months";
    $realfrom=$from;
    $from   = mktime(0,0,0,date("m", $todaysdate)-1,01,date("Y", $todaysdate));
    $to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
    $qstart=getmicrotime();
    //$query  = "select FROM_UNIXTIME(timestamp,'%M') AS month, count(distinct visitorid) as hits,count(*) as pages,(count(*)/count(distinct visitorid)) as ppu from $profile->tablename $sqlmethod index(timestamp) where timestamp >=$from and timestamp <=$to and crawl=0 group by month order by timestamp desc";
    $query="select month,visitors,pages,(pages/visitors) as ppu from ".$profile->tablename_vpm." where timestamp >=$from and timestamp <=$to order by timestamp desc";
    //echo $query;
    $result = $db->Execute($query);
    $uvthismonth = 0;
    $uvlastmonth = 0;
    if (!$result) {
	    echo "<tr><td>"._ERROR_QUERYING_SUMMARY_TABLES."</td></tr>";
    } else {
	    while ($data = $result->FetchRow()) {
		    echo "<tr><td><nobr>".$data["month"]."</nobr></td>";
		    echo "<td><b>".number_format($data["visitors"],0)."</b></td>";
		    if ($data["month"]==(date("F Y", $todaysdate))) {
		    //if (!$uvthismonth) {
			    $uvthismonth=$data["visitors"];
                $from   = mktime(0,0,0,date("m", $todaysdate),01,date("Y", $todaysdate));
                $to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
                //ReturnVisitors();
                if (($return_visitors = getProfileData($profile->profilename, "$profile->profilename.rv_thismonth", 0)) && ($real_today==$gen_today)) { 
                $return_percentage = intval(($return_visitors / $uvthismonth) * 100);
                //echo "$return_percentage = intval(($return_visitors / $uvthismonth) * 100);";
                $new_percentage = 100 - $return_percentage;
                $printstr="<b>$new_percentage%</b> - <font color=green>$return_percentage%</font>";  
                } else {
                    $printstr="<b>- %</b> - <font color=green>- %</font>"; 
                }
                
            } else {
			    $uvlastmonth=$data["visitors"];
                $printstr="";                                        
		    }
		    echo "<td><b>".number_format($data["pages"],0)."</b></td>";
		    echo "<td><b>".number_format($data["ppu"],2)."</b></td>";		
            echo "<td>$printstr</td></tr>";            
	    }
    }
    echo "</table>";
    //echo "<br>";
    $qend=getmicrotime();
    //echo "query 2 took". ($qend-$qstart);

    //make a new graph
    ?>
    <?php
    
    echo "<div style=\"height:170px; width:365px;\" id=\"todaychart\">";
        /*
        echo "<a href=\"javascript:TodayGraphSelect('weeks','$to','$from','$conf','$todaysdate');\">Weeks</a> | ";
        echo "<a href=\"javascript:TodayGraphSelect('months','$to','$from','$conf','$todaysdate');\">Months</a>"; 
        include("charts/includes/FusionCharts.php");  
        $charturl=urlencode("charts/monthcompare.php?to=$to&from=$from&conf=$conf&todaysdate=$todaysdate");
        echo renderChartHTML("charts/FCF_MSColumn3D.swf", "$charturl", "", "monthcompare", 365, 170, false, false, "opaque"); 
        */
    echo "</div>";
    
    if (@$showcharturl==1) {
        echo "<a href=".urldecode($charturl).">chart url</a>";
    }
    
    echo "</td><td valign=top width=\"100%\">";

    //print the trend info box
    /*echo "<table cellspacing=0 cellpadding=2 border=0 width=\"100%\" height=100%><tr><th bgcolor=silver>&nbsp;</th></tr><tr height=100%><td height=100%><table cellpadding=4 width=\"100%\" height=100% border=0><tr height=100%><td valign=top height=100% class=smallborder><b>Trend:</b><br>";*/
    echo "<table cellspacing=0 cellpadding=2 border=0 width=\"100%\" ><tr><th bgcolor=silver>&nbsp;</th></tr><tr ><td ><table cellpadding=3 width=\"100%\"  border=0><tr ><td valign=top  class=smallborder><b>"._TRENDS.":</b><br>";


    //new stuff
    if (date("m", $todaysdate)=="1") { 
        // its january, so go back one year also
        
        $prevyear=(date("Y", $todaysdate)-1);
        $prevmonth=12; 
    } else { 
        $prevyear=date("Y", $todaysdate);
        $prevmonth=(date("m", $todaysdate)-1); 
    }
    $from   = mktime(0,0,0,$prevmonth,01,$prevyear);
    $to     = mktime(23,59,59,date("m", $todaysdate),(date("d", $todaysdate)-1),date("Y", $todaysdate));
    $query="select FROM_UNIXTIME(timestamp,'%m') as month,avg(visitors * 1.00) as avgvisitors,avg(visits * 1.00) as avgvisits from $profile->tablename_vpd where timestamp >=$from and timestamp <=$to group by month order by timestamp";
    $lmavgvisitors = 0;
    $tmavgvisitors = 0;
    $lmavgvisits = 0;
    $tmavgvisits = 0;
    $q=$db->Execute($query);
    if ($q) {
	    while ($data=$q->FetchRow()){
            
		    if ($data["month"]==$prevmonth) {
			    $lmavgvisitors=$data["avgvisitors"];
                $lmavgvisits=$data["avgvisits"];
		    } 
		    else if ($data["month"]==(date("m", $todaysdate))) {
			    $tmavgvisitors=$data["avgvisitors"]; 
                $tmavgvisits=$data["avgvisits"];
		    } else if ((date("m", $todaysdate)-1)==0) {  //exception for january
                $tmavgvisitors=$data["avgvisitors"];
                $tmavgvisits=$data["avgvisits"];
            }
	    }
    }
    

    if ($profile->targetfiles) {
	    //get average conversion rate this month last month
	    $from   = mktime(0,0,0,date("m", $todaysdate),01,date("Y", $todaysdate));
	    $to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate)-1,date("Y", $todaysdate));
	    $query  = "select ((count(distinct visitorid)*1.00)/$uvthismonth)*100 as ctr from $profile->tablename_conversions where timestamp >=$from and timestamp <=$to group by url";
	    //echo $query;
	    $a=0;
	    $totctr = 0;
	    $result=$db->Execute($query);
	    if ($result) {
		    while ($tmctr = $result->FetchRow()) {
				    $totctr=$totctr+$tmctr["ctr"];
				    $a++;
		    }
	    }
	    if ($a <> 0) {
		    $tmctr=@number_format(($totctr/$a),2);
	    } else {
		    $tmctr=@number_format(0,2);
	    }
	    $m = mktime(0,0,0,$prevmonth,01,$prevyear);
	    $m = date("MY",$m);
	    $query  = "select ((count(distinct visitorid)*1.00)/$uvlastmonth)*100 as ctr from $profile->tablename_conversions where FROM_UNIXTIME(timestamp,'%b%Y')='$m' group by url";
	    //echo $query;
	    $result=$db->Execute($query);
	    $a=0;
	    $totctr=0;
	    if ($result) {
		    while ($lmctr = $result->FetchRow()) {
				    $totctr=$totctr+$lmctr["ctr"];
				    $a++;
		    }
	    }
	    if ($a <> 0) {
		    $lmctr=@number_format(($totctr/$a),2);
	    } else {
		    $lmctr=@number_format(0,2);
	    }
	    
    } else {
	    $tmctr = 0;
	    $lmctr = 0;
    }
    $qend=getmicrotime();
    //echo "query 4,5 (conversion) took". ($qend-$qstart);


    $from   = mktime(0,0,0,$prevmonth,01,$prevyear);
    $to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
    //$query="select FROM_UNIXTIME(timestamp,'%b%Y') as month,sum(visitors) as visits from $profile->tablename_vpd where timestamp >=$from and timestamp <=$to group by month order by timestamp desc";
    //$query="select FROM_UNIXTIME(timestamp,'%m') as month,sum(visitors) as visits from $profile->tablename_vpd where timestamp >=$from and timestamp <=$to group by month order by timestamp desc";
    $query="select FROM_UNIXTIME(timestamp,'%m') as month,visitors,visits,(visits/visitors) as vpm from $profile->tablename_vpm where timestamp >=$from and timestamp <=$to order by timestamp desc";
    //echo $query;
    $result = $db->Execute($query);
    $ntthismonth = 0;
    $ntlastmonth = 0;
    $avgvisitspuser1=0;
    $avgvisitspuser2=0;

    if ($result) {
	    while ($data = $result->FetchRow()) {
		    if ($data["month"]==(date("m", $todaysdate))) {
			    $ntthismonth=$data["visitors"];
                $avgvisitspuser2=@number_format($data["vpm"],2);
		    } else if ($data["month"]==(date("m", $todaysdate)-1)) {
			    $ntlastmonth=$data["visitors"];
                $avgvisitspuser1=@number_format($data["vpm"],2);
		    } else if ((date("m", $todaysdate)-1)==0) {  //exception for january
                $ntlastmonth=$data["visits"];
                $avgvisitspuser1=@number_format($data["vpm"],2);
            }		
	    }
    }
    //$avgvisitspuser1=@number_format(($ntlastmonth/$uvlastmonth),2);
    //$avgvisitspuser2=@number_format(($ntthismonth/$uvthismonth),2);

    //old stuff
	    $diff=$tmavgvisitors-$lmavgvisitors;
	    $diff=intval($diff);
	    //echo $diff;
	    if (($diff < 2) && ($diff > -2) && ($diff !=0)) {
		    echo "<font color=orange>"._PRETTY_STABLE."</font><br>";
		    $color="orange";
		    $smile="smile-flat.gif";
	    } else if ($lmavgvisitors > $tmavgvisitors) {
		     $color="red";	
		    $smile="smile-shame.gif";
		     echo "<font color=red>"._LOSING_VISITORS."</font><br>";
	    } else if ($lmavgvisitors == $tmavgvisitors) {
		    $color="orange";
		    $smile="smile-flat.gif";
		    echo "<font color=orange>"._STABLE."</font><br>";
	    } else {
		    $color="green";
		    if ($diff > 300) {
				    $smile="smile-fast.gif";
		    } else {
				    $smile="smile.gif";
		    }
		    echo "<font color=green>"._GAINING_VISITORS."</font><br>";
	    }
	    $otmctr=$tmctr;
	    if ($lmctr > $tmctr) {
		     $tmctr="<font color=red>$tmctr %</font>";
		     echo "<font color=red>"._PERF_DROPPING."</font><br>";
	    } else if ($lmctr==$tmctr) {
		     $tmctr="<font color=orange>$tmctr %</font>";
		     echo "<font color=orange>"._PERF_STABLE."</font><br>";
	    } else {
		     $tmctr="<font color=green>$tmctr %</font>";
		     echo "<font color=green>"._PERF_INCREASING."</font><br>";
	    }	
	    $ctrdiff=$otmctr-$lmctr;	
	    //echo $ctrdiff;
	    $diff=@number_format($diff,0);
	    echo "<div style=\"padding:14px;margin-left:6px;\">";
        if ($diff > 0) {
		     echo "<table cellpadding=3><tr><td><img src=\"images/$smile\" width=\"23\" height=\"23\" alt=\"\"></td><td><font color=$color size=3><b>+$diff</b></font></td>";
	    } else {
		     echo "<table cellpadding=3><tr><td><img src=\"images/$smile\" width=\"23\" height=\"23\" alt=\"\"></td><td><font color=$color size=3><b>$diff</b></font></td>";
	    }
	    if ($ctrdiff > 0) {
		     $smile="smile-relax.gif";
		     echo "<td class=sider><img src=\"images/$smile\" width=\"23\" height=\"23\" alt=\"\"></td><td><font color=green size=3><b>+$ctrdiff %</b></font></td>";
	    } else if ($ctrdiff==0) {
		     $smile="smile-flat2.gif";
		     echo "<td class=sider><img src=\"images/$smile\" width=\"23\" height=\"23\" alt=\"\"></td><td><font color=orange size=3><b>$ctrdiff %</b></font></td>";
	    } else {
		     $smile="smile-red.gif";
		     echo "<td class=sider><img src=\"images/$smile\" width=\"23\" height=\"23\" alt=\"\"></td><td><font color=red size=3><b>$ctrdiff %</b></font></td>";
	    }
	    echo "</tr></table></div>";
	    
	    if ($avgvisitspuser1 > $avgvisitspuser2) {
		     $avgvisitspuser2="<font color=red>$avgvisitspuser2</font>";
	    } else {
		     $avgvisitspuser2="<font color=green>$avgvisitspuser2</font>";
	    }
        if ($lmavgvisits > $tmavgvisits) {
             $color2="red";
        } else {
             $color2="green";
        }	
	    ?>
	    <table cellpadding=2 cellspacing=0 border=0><tr><td class=grayline><font color=gray><?php echo _AVERAGES;?></font></td><td class=grayline2><font color=gray><?php echo _LAST_MONTH;?></font></td><td class=grayline2><font color=gray><?php echo _THIS_MONTH;?></font></td></tr>
	    <tr><td><?php echo _VISITORS_PER_DAY;?></td><td align=center class=sider><?php echo number_format($lmavgvisitors); ?></td><td align=center class=sider><?php echo "<font color=$color>".number_format($tmavgvisitors); ?></font></td></tr>
	    <tr><td><?php echo _VISITS_PER_DAY;?></td><td align=center class=sider><?php echo number_format($lmavgvisits); ?></td><td align=center class=sider><?php echo "<font color=$color2>".number_format($tmavgvisits); ?></font></td></tr>
        <tr><td><?php echo _VISITS_PER_USER;?></td><td align=center class=sider><?php echo $avgvisitspuser1; ?></td><td align=center class=sider><?php echo $avgvisitspuser2; ?></td></tr>
	    
        <tr><td><?php echo _CONVERSION_RATE;?></td><td align=center class=sider><?php echo $lmctr; ?> %</td><td align=center class=sider><?php echo $tmctr; ?></td></tr>
        
        <tr><td title="<?php echo _BOUNCE_RATE_EXPLAIN;?>"><?php echo _BOUNCE_RATE;?></td>
        
        <?php 
        $from   = mktime(0,0,0,$prevmonth,01,$prevyear);
        $lday=get_month_lastday($prevmonth,$prevyear);
        $to     = mktime(23,59,59,$prevmonth,$lday,$prevyear);
        $archn = ($prevmonth) . $prevyear;
        //$bounce1= BounceRate($archn);
        $bounce1 = number_format(@((BounceVisitors($archn) / $uvlastmonth) * 100),2); 
        $from   = mktime(0,0,0,date("m", $todaysdate),01,date("Y", $todaysdate));
        $to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
        //$bounce2= BounceRate("redo");
        $bounce2 = number_format(@((BounceVisitors("redo") / $uvthismonth) * 100),2);
        
        if ($bounce1 < $bounce2) {
              $bounce2="<font color=red>$bounce2 %</font>";    
        } else {
              $bounce2="<font color=green>$bounce2 %</font>";    
        }
        ?>
        <td align=center class=sider><?php echo $bounce1; ?> %</td>
        <td align=center class=sider><?php echo $bounce2; ?> </td></tr> 
        </table>
	    <?php
	    //	echo "Average Visits per day:<br>Last Month: $lmavgvisits&nbsp;&nbsp;&nbsp; This Month: $tmavgvisits<br>";
	    //  echo "Average Visits per user:<br>Last Month: $avgvisitspuser1&nbsp;&nbsp;&nbsp; This Month: $avgvisitspuser2<br>"; 	
	    echo "</td></tr></table>";
	    echo "</td></tr></table>";
    //echo "</td></tr><tr><td align=left colspan=35>";
    //echo "<table cellspacing=0 cellpadding=0 border=0 height=10><tr><td><font size=1 color=gray>Visits Per Day:&nbsp;&nbsp;&nbsp;</font></td><td class=graphborder bgcolor=A7ADFE><img src=images/pixel.gif width=20 height=1></td><td><font size=1 color=gray>&nbsp;&nbsp;&nbsp;". date("M", $todaysdate). "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td class=graphborder bgcolor=F0F0F0><img src=images/pixel.gif width=20 height=1></td><td><font size=1 color=gray>&nbsp;&nbsp;&nbsp;". date("M",mktime(0,0,0,date("m", $todaysdate)-1,05,date("Y", $todaysdate))). "</font></td></tr></table> ";
    echo "</td></tr></table></div>\n";
}


// ----------------------------------------------------------------------------------------------------------------------
//     Main Report blocks start below this point
// ----------------------------------------------------------------------------------------------------------------------

$mini=1;
$jscommands=""; // this will collect javascript commands to be executed at the end of the page
$limit=10;
$dashboard_reports = stripslashes(getProfileData($profile->profilename, "$profile->profilename.dashboard_reports"));
if (substr($dashboard_reports,0,1)!="_") { //old style
    $reset=1;
}
if (!@$dashboard_reports || @$reset==1) {
    // set the default 
    //setProfileData($profile->profilename, $cachename, $contents);
    $dashboard_reports = "_TODAY_TRENDS::600::_DASHBOARD_REPORTS,_PERFORMANCE_TODAY::300::_DASHBOARD_REPORTS,_PERFORMANCE_THIS_MONTH::300::_DASHBOARD_REPORTS,_VISITORS_PER_DAY::600::_SUMMARY_REPORTS,_TODAYS_TOP_PAGES::600::_DASHBOARD_REPORTS,_TODAYS_TOP_KEYWORDS::300::_DASHBOARD_REPORTS,_TODAYS_TOP_COUNTRIES::300::_DASHBOARD_REPORTS,_TODAYS_TOP_REFERRERS::600::_DASHBOARD_REPORTS";
       
    setProfileData($profile->profilename, "$profile->profilename.dashboard_reports", $dashboard_reports);
    //echo "set dashboard reports";     
}
//echo '<hr>'.$dashboard_reports.'<hr><br>';
echoDebug("$dashboard_reports");
$reports = explode(",",$dashboard_reports);
//var_dump($reports);echo '<hr>';
$i=0;
foreach ($reports as $report) {
    //echo $report;
    $report_string= $report;
    //$divlabels[$i]=str_replace("'","-",str_replace(" ","_",$report_string));
    $divlabels[$i]=$report_string;
//    echo $report.'<hr>';
    $report = explode("::",$report);
//    var_dump($report);echo '<hr>';
    $labels = @constant($report[0]);
//    echo $labels.'<hr>';
    $width = $report[1];
    $type= @constant($report[2]);
//    var_dump(constant($report[2]));

    if (strpos($report[0],"PERFORMANCE")!==FALSE) {
        if ($profile->targetfiles=="") {
            continue;
        }
    }
    
    //$divlabels[$i]=$profile->profilename."_".str_replace("'","",str_replace(" ","",$labels));
    //funnels
    if ($type == _FUNNEL_REPORTS) {
        $funnelid = getProfileData($profile->profilename, "$profile->profilename.last_funnel", 0);
        //echo "got funnel $funnelid";
        $url = "funnels.php?conf=$conf&to=$to&from=$from&funnelid=$funnelid&graphinclude=1&divlabel=$divlabels[$i]";
        $jscommands.= " AjaxGet('$url', '$divlabels[$i]');\n";
        $ajax=1;  
    } else if ($type == _TEST_RESULTS) {
        $target = explode(",",$profile->targetfiles);
        $target = getProfileData($profile->profilename, "$profile->profilename.last_targetfile", $target[0]);        
        $testid = getProfileData($profile->profilename, "$profile->profilename.last_testid", 0);
        $url = "testcenter.php?conf=$conf&testid=$testid&roadto=$target&spt=report&from=$from&to=$to&includereport=1&divlabel=$divlabels[$i]";
        $jscommands.= " AjaxGet('$url', '$divlabels[$i]');\n";
        $ajax=1;  
    } else { 
        $url = "reports.php?conf=$conf&to=$to&from=$from&labels=".urlencode($labels)."&limit=$limit&submit=Report&statstable_only=1";
        $ajax=0;
    }
    
    if ($labels==_TODAY_TRENDS) {
        todaytrends();   
    } else {
        echo "<div id=\"$divlabels[$i]\" class=\"todayreports\" style=\"width:".$width."px;\">\n";
        //echo "<div id=\"$report_string\" class=\"todayreports\" style=\"width:".$width."px;\">\n";
        
        if ($ajax==0) {
            $from   = mktime(0,0,0,date("m", $todaysdate),01,date("Y", $todaysdate));
            $to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
            GetQuery($labels,@$showfields,$from,$to);
            StatsTable($from,$to,$showfields,$labels,$query);
        } else {
            echo "<p class=\"small\" style=\"color:gray;line-height:32px;\"><img src=\"images/Hourglass_icon.gif\" width=\"31\" height=\"31\" alt=\""._PLEASE_WAIT_ALTTEXT."\" border=\"0\" vspace=\"0\" hspace=\"5\" align=\"left\">Getting $labels ... </p>\n";
        }
        echo "</div>\n";
    } 
    
    $i++;       
}
               
//DisplayPlugins("8");

echo "</div> <!-- end of main reports div //-->"; // end of main report div 

$e=getmicrotime();
$took=$e-$pagestart;
echoFooter("<a class=nodec href=\"credits.php?conf=$conf\">&copy; 2005-".date('Y')." Logaholic BV</a><br>"._ORIGINAL_REPORT_CREATED_IN." ".number_format($took,3)." "._SECONDS);
?>
<script language="javascript" type="text/javascript"> 
<?php
// this prints the list of divs we need to update on page load
echo "function FillDivs() {\n";
    echo $jscommands;
    echo "return;\n";
echo "}\n";
echo "TodayGraphSelect('','$to','$from','$conf','$todaysdate');";

?>
</script>

<?php
// end of today
	$contents = ob_get_contents();
	ob_end_clean();
	echo $contents;
	if ($profile->usepagecache) {
		setProfileData($profile->profilename, $cachename, $contents);
	}
}


?>
</body>
</html>
