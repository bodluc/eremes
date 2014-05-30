<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays the used os versions
*/

$reports["_OS_VERSIONS"] = Array(
	"ClassName" => "OSVersions", 
	"Category" => "_CLIENT_SYSTEM", 
	"icon" => "images/icons/32x32/os_versions.png",
	"Options" => "daterangeField,displaymode,trafficsource,limit,columnSelector",
	"Filename" => "os_versions",
	"Distribution" => "Standard",
	"Order" => 6,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class OSVersions extends Report {

	function Settings() {
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "table,pie";
		$this->showfields = _OPERATING_SYSTEM.','._VISITORS;
		$this->help = _OS_VERSIONS_HELP;
	}
	
	function DefineQuery() {
		global $db;
		$query = "SELECT IF(ua.os='','"._UNKNOWN."',CONCAT(ua.os,' ',ua.os_version)) AS os_name, COUNT(DISTINCT a.visitorid) AS visitors FROM {$this->profile->tablename} AS a, {$this->profile->tablename_useragents} as ua WHERE a.useragentid = ua.id AND  a.timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND a.status = 200 AND a.crawl = 0 GROUP BY os_name ORDER BY visitors DESC limit ". addslashes($this->limit);
		return $query;
	}
}
?>
