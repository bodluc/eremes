<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays the browser versions
*/

$reports["_BROWSER_VERSIONS"] = Array(
	"ClassName" => "BrowserVersions", 
	"Category" => "_CLIENT_SYSTEM", 
	"icon" => "images/icons/32x32/browser_versions.png",
	"Options" => "daterangeField,trafficsource,limit,columnSelector",
	"Filename" => "browser_versions",
	"Distribution" => "Standard",
	"Order" => 4,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class BrowserVersions extends Report {
	
	function Settings() {
		$this->showfields = _BROWSER.','._BROWSER_VERSION.','._VISITORS;
		$this->help = _BROWSER_VERSIONS_HELP;
	}
	
	function DefineQuery() {
		global $db;
		
		$query = "SELECT ua.name AS useragent, ua.version, COUNT(distinct a.visitorid) AS visitors FROM {$this->profile->tablename} as a,{$this->profile->tablename_useragents} as ua where a.useragentid = ua.id and a.timestamp >= ". $db->quote($this->from) ." AND a.timestamp <= ". $db->quote($this->to) ." AND a.status = 200 AND a.crawl = 0 AND ua.is_mobile = 0 GROUP BY ua.name, ua.version ORDER BY visitors DESC LIMIT {$this->limit}";
    	
		return $query;
	}
}
?>
