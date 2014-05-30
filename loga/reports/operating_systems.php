<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays the used operating systems 
*/
$reports["_OPERATING_SYSTEMS"] = Array(
	"ClassName" => "OperatingSystems", 
	"Category" => "_CLIENT_SYSTEM", 
	"icon" => "images/icons/32x32/operatingsystems.png",
	"Options" => "daterangeField,displaymode,trafficsource",
	"Filename" => "operating_systems",
	"Distribution" => "Standard",
	"Order" => 5,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class OperatingSystems extends Report {
	
	function Settings() {
		$this->DefaultDisplay = "pie";
		$this->DisplayModes = "table,pie";
		$this->showfields = _OPERATING_SYSTEM.','._VISITORS;
		$this->help = _OPERATING_SYSTEMS_DESC;
	}
	
	function DefineQuery() {
		global $db;
		$query = "SELECT ua.os, COUNT(DISTINCT a.visitorid) AS visitors FROM {$this->profile->tablename} AS a, {$this->profile->tablename_useragents} as ua WHERE a.useragentid = ua.id AND a.timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND a.status = 200 AND a.crawl = 0 AND ua.is_mobile != 1 GROUP BY ua.os ORDER BY visitors DESC";
		
		return $query;
	}
}
?>
