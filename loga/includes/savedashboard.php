<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
$reporting = false;
include_once('../common.inc.php');

if(!empty($_REQUEST['saveDashboard'])) { $saveDashboard = $_REQUEST['saveDashboard']; }
if(!empty($_REQUEST['dashboardname'])) { $dashboardname = $_REQUEST['dashboardname']; }
	
if(!empty($saveDashboard) && $saveDashboard != "delete") {
	$saveDashboard = addslashes($saveDashboard);
	$tmp_storage = json_decode($saveDashboard);
	if($tmp_storage->startup == 1) {
		$sql = "SELECT * FROM `".TBL_GLOBAL_SETTINGS."` WHERE `Name` LIKE '{$conf}.dashboards.%' AND `Profile` = '{$conf}'";
		$result = $db->Execute($sql);
			
		while($dashboarddata = $result->FetchRow()) {
			$tmp_dashboarddata = json_decode($dashboarddata['Value']);
			$tmp_dashboarddata->startup = 0;
			$tmp_dashboarddata = json_encode($tmp_dashboarddata);
			
			setProfileData($dashboarddata['Profile'], $dashboarddata['Name'], $tmp_dashboarddata);
		}
	}
	setProfileData($conf, "{$conf}.dashboards.{$dashboardname}", $saveDashboard);
} elseif(!empty($saveDashboard) && $saveDashboard == "delete") {
	deleteProfileData($conf, "{$conf}.dashboards.{$dashboardname}");
	echo "{$dashboardname} "._HAS_BEEN_DELETED;
}
?>