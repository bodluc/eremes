<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/* this file is intended to be included in a file in the logaholic root folder */

$lq = "Select profilename,tablename from ".TBL_PROFILES;

// If we're limiting the visible profiles, then put a filter on to only pull those records.
if (($validUserRequired) && (!$session->isAdmin())) {
    // Can't use implode here because we need to escape the entries.
    $validprofiles = "";
    for ($i = count($session->user_profiles)-1; $i >= 0; $i--) {
        if ($validprofiles != "") { $validprofiles .= "\",\""; }
        $validprofiles .= $db->escape($session->user_profiles[$i]);
    }
    $lq .= " where profilename in (\"$validprofiles\")";
}    

$q= $db->Execute($lq);

$global_stats = array();
$daily_stats = array();
$i=0;
while ($data=$q->FetchRow()) {
    $pname=$data[0];
    $tablename=$data[1];
    
    $query ="select \"<a title='"._CLICK_TO_PROFILE_MAINTENANCE."' href='profiles.php?editconf=$pname&del=1'>$pname</a>\", sum(visitors) as visitors, sum(pages), (select count(*) from $tablename), (select FROM_UNIXTIME(timestamp,'%Y-%m-%d') from $tablename order by timestamp asc limit 1), (select FROM_UNIXTIME(timestamp,'%Y-%m-%d') from $tablename order by timestamp desc limit 1) from ".$tablename."_vpm";
    $tmp = $db->Execute($query);
    $result=$tmp->FetchRow();
    $global_stats[$i][0] = $result[0];
    $global_stats[$i][1] = $result[1];
    $global_stats[$i][2] = $result[2];
    $global_stats[$i][3] = $result[3];
    $global_stats[$i][4] = $result[4];
    $global_stats[$i][5] = $result[5];
    
    $query="select \"<a title='"._CLICK_TO_PROFILE_MAINTENANCE."' href='profiles.php?editconf=$pname&del=1'>$pname</a>\", avg(visitors) as avgvisitors, avg(pages), avg(visits) from ".$tablename."_vpd";         
    $tmp = $db->Execute($query);
    $result=$tmp->FetchRow();
    $daily_stats[$i][0] = $result[0];
    $daily_stats[$i][1] = $result[1];
    $daily_stats[$i][2] = $result[2];
    $daily_stats[$i][3] = $result[3];
    
    $i++;   
}

$labels=_ALL_TIME_ALL_PROFILES;
//$showfields=_PROFILENAME.','._VISITORS.','._PAGEVIEWS.','._RECORDS.','._DATE_RANGE;
$showfields=_PROFILENAME.','._VISITORS.','._PAGEVIEWS.','._RECORDS.','._FROM.','._DATE_TO;
$help=_ALL_TIME_ALL_PROFILES_HELP;

$from=0;
$to=time();
$data = DataSort($global_stats,1); 

ArrayStatsTable($from,$to,$showfields,$labels,$query="");

echo "<hr noshade size=1 style=\"margin-top:25px;margin-bottom:25px;\">";

$labels=_AVERAGE_DAILY_STATS_ALL_PROFILES;
$showfields=_PROFILENAME.','._VISITORS.','._PAGEVIEWS.','._VISITS;
$help=_AVERAGE_DAILY_STATS_ALL_PROFILES_HELP;
$data = $daily_stats;
ArrayStatsTable($from,$to,$showfields,$labels,$query="");


?>
