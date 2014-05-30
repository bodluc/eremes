<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays the browser market share
*/

$reports["_BROWSER_MARKET_SHARE"] = Array(
	"ClassName" => "BrowserMarketShare", 
	"Category" => "_CLIENT_SYSTEM", 
	"icon" => "images/icons/32x32/browsers.png",
	"Options" => "daterangeField,displaymode,trafficsource",
	"Filename" => "browser_market_share",
	"Distribution" => "Standard",
	"Order" => 2,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class BrowserMarketShare extends Report {
	
	function Settings() {
		$this->DefaultDisplay = "pie";
		$this->DisplayModes = "table,pie";
		$this->showfields = _BROWSER.','._VISITORS;
		$this->help = _BROWSER_MARKET_SHARE_HELP;
	}
	
	function DefineQuery() {
		global $db;
	
        $query = "SELECT ua.name AS useragent, COUNT(distinct a.visitorid) AS visitors FROM {$this->profile->tablename} as a INNER JOIN {$this->profile->tablename_useragents} as ua ON a.useragentid = ua.id WHERE a.timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND a.status = 200 AND a.crawl = 0 AND ua.is_mobile = 0 GROUP BY ua.name ORDER BY visitors DESC LIMIT 10";	
		
		return $query;
	}
}
?>
