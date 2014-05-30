<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include_once "../common.inc.php";
include_once "../queries.php";

$profile = new SiteProfile($conf); 
$reset = @$_REQUEST['reset'];
$add = @$_REQUEST['add'];
$remove = @$_REQUEST['remove'];
$type = @$_REQUEST['type']; 
//$sort = str_replace("-","'",str_replace("_"," ",@$_REQUEST['sort']));
$sort = @$_REQUEST['sort'];
$newposition = @$_REQUEST['newposition'];
$savecustom = @$_REQUEST['savecustom']; 
$cachename = "cache_index.$profile->profilename.%";
deleteProfileData($profile->profilename, $cachename);
$default_dashboard_reports = "_TODAY_TRENDS::600::_DASHBOARD_REPORTS,_PERFORMANCE_TODAY::300::_DASHBOARD_REPORTS,_PERFORMANCE_THIS_MONTH::300::_DASHBOARD_REPORTS,_VISITORS_PER_DAY::600::_SUMMARY_REPORTS,_TODAYS_TOP_PAGES::600::_DASHBOARD_REPORTS,_TODAYS_TOP_KEYWORDS::300::_DASHBOARD_REPORTS,_TODAYS_TOP_COUNTRIES::300::_DASHBOARD_REPORTS,_TODAYS_TOP_REFERRERS::600::_DASHBOARD_REPORTS";

if ($reset) {
    $dashboard_reports = $default_dashboard_reports;
    setProfileData($profile->profilename, "$profile->profilename.dashboard_reports",$dashboard_reports);    
}
$dashboard_reports = getProfileData($profile->profilename, "$profile->profilename.dashboard_reports",$default_dashboard_reports);
$dashboard_reports = stripslashes($dashboard_reports);

if ($add) {
    if ($type=="_DASHBOARD_REPORTS" || $type=="_FUNNEL_REPORTS") {
        $w = 300;
        if (strpos($add,"_PAGES")!=FALSE || strpos($add,"_REFERRERS")!=FALSE || strpos($add,"_TRENDS")!=FALSE) {
            $w=600;   
        }
    } else {
        $w = 600;
    }
    //$add=urldecode($add);
    $dashboard_reports=$dashboard_reports.",$add::$w::$type";
    setProfileData($profile->profilename, "$profile->profilename.dashboard_reports",$dashboard_reports);
    //echo $dashboard_reports;       
}

if ($remove) {
    $reports = explode(",",$dashboard_reports);
    $dashboard_reports=""; 
    foreach ($reports as $report) {
        $report = explode("::",$report);
        if ($report[0]!=$remove) {
           $dashboard_reports.="$report[0]::$report[1]::$report[2],"; 
        }
    }
    if (substr($dashboard_reports,-1)==",") {
       $dashboard_reports= substr($dashboard_reports,0,-1);    
    }
    setProfileData($profile->profilename, "$profile->profilename.dashboard_reports",$dashboard_reports);       
}

if ($sort) {
    
    $reports = explode(",",$dashboard_reports);
    $dashboard_reports=""; 
    //first remove the line we are after    
    foreach ($reports as $report) {
        if ($report!=$sort) {
           $dashboard_reports.="$report,"; 
        }
    }
    if (substr($dashboard_reports,-1)==",") {
       $dashboard_reports= substr($dashboard_reports,0,-1);    
    }
    //now insert it back in at the desired position
    $reports = explode(",",$dashboard_reports);
    $dashboard_reports=""; 
    $i=0;
    foreach ($reports as $report) { 
        if ($i == $newposition) {
            $dashboard_reports.="$sort,";     
        }
        $dashboard_reports.="$report,"; 
        $i++;
    }
    if (substr($dashboard_reports,-1)==",") {
       $dashboard_reports= substr($dashboard_reports,0,-1);    
    }
    setProfileData($profile->profilename, "$profile->profilename.dashboard_reports",$dashboard_reports);
    //echo $dashboard_reports;    
               
}

//start output
?>

<div class="small">
<?php echo _CUSTOMIZE_PAGE_EXPLAIN;?> 
<?php
echo "<form id=\"customizer_form\" method=\"post\" action=\"index.php\">";
echo "<input type=\"hidden\" name=\"conf\" value=\"$conf\">";
echo "<input type=hidden name=nocache value=\"1\">";
echo "<input type=\"hidden\" name=\"savecustom\" value=\"1\">";  
echo "<table id=\"selections\" class=\"small\" width=\"99%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">";
echo "<tr style=\"line-height:24px;\"><td class=\"toplinegreen\"><strong>"._SELECTED_REPORTS."</strong></td><td class=\"toplinegreen\"><strong>"._INCLUDE."</strong></td><td class=\"toplinegreen\"><strong>"._DEFAULT_WIDTH."</strong></td><td class=\"toplinegreen\"><strong>"._ORDER."</strong></td></tr>\n";   
$i=1;
$bgcolor="#F6F5F0";
//echo $dashboard_reports;
$reports = explode(",",$dashboard_reports);
//first list the selected reports
foreach ($reports as $report) {
    $report = explode("::",$report);
    $label = constant($report[0]);
    $width = $report[1];
    $type = constant($report[2]);
    $selected[$report[0]]=$width;
    $order[$report[0]]=$i;
    $warn="";
    if (strpos($report[0],"PERFORMANCE")!==FALSE) {
        if ($profile->targetfiles=="") {
            $warn="<font color=red> - "._PLEASE_ADD_KPIS."</font>";
        }
    }
    echo "<tr id=\"selected$i\" class=\"selections_rows\" bgcolor=\"$bgcolor\"><td>$label ($type)$warn</td>\n<td id=\"tdselected$i\"><input type=\"checkbox\" onclick=\"CustomizerRemove('selected$i','includes/customizer.php?conf=$conf&remove=".$report[0]."','dialog');\" name=\"$i.label\" value=\"$report[0]\" checked></td>\n<td><input type=text size=3 name=\"$i.width\" value=\"{$selected[$report[0]]}\"><input type=\"hidden\" name=\"$i.type\" value=\"$report[2]\"></td><td><input type=text size=3 name=\"$i.order\" value=\"{$order[$report[0]]}\"></td></tr>\n";
    if ($bgcolor=="white") {
        $bgcolor="#F6F5F0";    
    } else {
        $bgcolor="white";
    }
    $i++;
}

echo "</table><br>";
if ($add) {
    echo "<p style=\"float:left;\">"._LAST_ACTION.": <span style=\"color:red;\">"._ADDED." <b>".constant($add)."</b></span></p>";   
} else if ($remove) {
    echo "<p style=\"float:left;\">"._LAST_ACTION.": <span style=\"color:red;\">"._REMOVED." <b>".constant($remove)."</b></span></p>";  
} else {
    echo "<p style=\"float:left;\"><a href=\"javascript:AjaxGet('".urlencode("includes/customizer.php?conf=$conf&reset=1")."','dialog');\">"._RESET_REPORT_SELECTIONS."</a></p>";
}
echo "<input type=\"submit\" value=\"Update Dashboard\" style=\"float:right;\">";
echo "</form>";

echo "<div id=\"customerizernav\">";
$li=1;
echo "<br style=\"clear:both;\"><br>"._SELECT_DASHBOARD_REPORTS."<br><br><ul>";
$i=0;
while (list($key,$val) = each($t)) {
    if (@$selected[$t_constant[$key]]!="") {
        //it in the selected list above  
    } else {
        echo "<li id=\"customizer_li.$li\"><a class=nodec4 href=\"javascript:CustomizerAdd('customizer_li.$li','".urlencode("includes/customizer.php?conf=$conf&add={$t_constant[$key]}&type=_DASHBOARD_REPORTS")."','dialog');\">$val</a></li>";
        $i++;
        $li++;
    }
}

if (!@$selected["_FUNNEL_REPORTS"]) {
   echo "<li id=\"customizer_li.$li\"><a class=nodec4 href=\"javascript:CustomizerAdd('customizer_li.$li','".urlencode("includes/customizer.php?conf=$conf&add=_FUNNEL_REPORTS&type=_FUNNEL_REPORTS")."','dialog');\">"._FUNNEL_REPORTS."</a></li>";
   $i++;
   $li++; 
}   
if (!@$selected["_TEST_RESULTS"]) {
   echo "<li id=\"customizer_li.$li\"><a class=nodec4 href=\"javascript:CustomizerAdd('customizer_li.$li','".urlencode("includes/customizer.php?conf=$conf&add=_TEST_RESULTS&type=_TEST_RESULTS")."','dialog');\">"._TEST_RESULTS."</a></li>";
   $i++;
   $li++; 
}

if ($i==0) { echo "<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>"._NO_MORE_REPORTS_TO_SELECT."</i></li>";}
echo "</ul>";
// now list the other reports

//echo "<tr><td><strong>General Reports</strong></td><td><strong>Include</strong></td><td><strong>Deafult Width</strong></td><td><strong>Order</strong></td></tr>\n"; 
echo "<br><br><b>"._SELECT_SUMMARY_REPORTS.":</b><br><br><ul>";
$i=0;
while (list($key,$val) = each($l)) {
    if (@$selected[$l_constant[$key]]!="") {
  
    } else {

        echo "<li id=\"customizer_li.$li\"><a class=nodec4 href=\"javascript:CustomizerAdd('customizer_li.$li','".urlencode("includes/customizer.php?conf=$conf&add=".$l_constant[$key]."&type=_SUMMARY_REPORTS")."','dialog');\">$val</a></li>";
        $li++;
    }
    $i++;
}

echo "</ul>";
echo "</div><br><br>";
echo "<p><a href=\"javascript:AjaxGet('".urlencode("includes/customizer.php?conf=$conf&reset=1")."','dialog');\">"._RESET_REPORT_SELECTIONS."</a></p>"; 
echo "<br><br>";
echo "</div>";
?>
