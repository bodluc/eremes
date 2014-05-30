<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc Definitions for this report: Visitors per Day

    Visitors: Unique Visitors that accessed your site (excluding any bots and crawlers).
    Pageviews: The total number of pages requested (excluding any bots and crawlers).
    Average Pages per user: The total number of pages devided by the number of visitors.
*/
$reports["_VISITORS_PER_DAY"] = Array(
	"ClassName" => "VisitorsPerDay", 
	"Category" => "_VISITOR_DETAILS", 
	"icon" => "images/icons/32x32/visitorsperday.png",
	"Options" => "daterangeField,displaymode,trafficsource,columnSelector",
	"Filename" => "visitors_per_day",
	"Distribution" => "Standard",
	"Order" => 4,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class VisitorsPerDay extends Report {
	function Settings() {
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "table,linechart,barchart";
		$this->showfields = _DATE.','._VISITORS.','._PAGEVIEWS.','._PAGES_PER_USER;
		$this->help = _VISITORS_PER_DAY_DESC;
	}
	
	function DefineQuery() {
		global $db;
		if (!empty($this->trafficsource)) {
			// If we're using a traffic source, then we can't use the summary tables.
			$query = "SELECT FROM_UNIXTIME(timestamp, '%d-%b-%Y %a') AS days, COUNT(DISTINCT visitorid) AS visitors, COUNT(*) AS pages, (COUNT(*) / COUNT(DISTINCT visitorid)) AS ppu FROM {$this->profile->tablename} WHERE timestamp >= ". $db->quote($this->from) ." AND timestamp <= ". $db->quote($this->to) ." AND crawl = 0 GROUP BY days ORDER BY timestamp";
			
		} else {
			// Use the summary table.
			$query = "SELECT days, visitors, pages, (pages / visitors) ppu FROM {$this->profile->tablename_vpd} WHERE timestamp >= ". $db->quote($this->from) ." AND timestamp <= ". $db->quote($this->to) ." ORDER BY timestamp";
		}
		return $query;
	}
}
?>
