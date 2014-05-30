<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays all traffic by day
*/
$reports["_ALL_TRAFFIC_BY_DAY"] = Array(
	"ClassName" => "AllTrafficByDay", 
	"Category" => "_TRAFFIC", 
	"icon" => "images/icons/32x32/alltrafficbyday.png",
	"Options" => "daterangeField,trafficsource,columnSelector",
	"Filename" => "all_traffic_by_day",
	"Distribution" => "Standard",
	"Order" => 4,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class AllTrafficByDay extends Report {
	
	function Settings() {
		$this->showfields = _DATE.','._UNIQUE_IPS.','._TOTAL_PAGES.','._VIEWED_PAGES.','._CRAWLED_PERC.','._PAGES_PER_IP;
		$this->help = _ALL_TRAFFIC_BY_DAY_DESC;
	}
	
	function DefineQuery() {
		global $db;
		$query = "SELECT FROM_UNIXTIME(timestamp, '%d-%b-%Y %a') AS days, COUNT(distinct visitorid) as visitors, COUNT(*) as requests, (COUNT(*) - SUM(crawl)) AS viewed, (SUM(crawl) / (COUNT(*) * 1.00) * 100) AS crawled, (COUNT(*) / (COUNT(DISTINCT visitorid) * 1.00)) AS ppu FROM {$this->profile->tablename} WHERE timestamp >= ". $db->quote($this->from) ." AND timestamp <= ". $db->quote($this->to) ." GROUP BY days ORDER BY timestamp";

		return $query;
	}
}
?>