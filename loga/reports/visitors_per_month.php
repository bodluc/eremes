<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc Definitions for this report:

    Visitors: Unique Visitors that accessed your site (excluding any bots and crawlers).
    Pageviews: The total number of pages requested (excluding any bots and crawlers).
    Average Pages per user: The total number of pages devided by the number of visitors.

Tip: Make sure you set a proper date range (e.g. start from the first day of a month).
*/
$reports["_VISITORS_PER_MONTH"] = Array(
	"ClassName" => "VisitorsPerMonth", 
	"Category" => "_VISITOR_DETAILS", 
	"icon" => "images/icons/32x32/visitorspermonth.png",
	"Options" => "daterangeField,displaymode,trafficsource,columnSelector",
	"Filename" => "visitors_per_month",
	"Distribution" => "Standard",
	"Order" => 5,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class VisitorsPerMonth extends Report {
	
	function Settings() {
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "table,linechart,barchart";
		$this->showfields = _DATE.','._VISITORS.','._PAGEVIEWS.','._PAGES_PER_USER;
		$this->help = _VISITORS_PER_MONTH_DESC;
		$this->allowDateFormat = false;
	}
	
	function DefineQuery() {
		global $db;
		if (!empty($this->trafficsource)) {
			// # If we're using a traffic source, then we can't use the summary tables.
			$query = "SELECT FROM_UNIXTIME(timestamp, '%M %Y') AS month, COUNT(DISTINCT visitorid) as visitors, COUNT(*) as pages, (COUNT(*) / COUNT(DISTINCT visitorid)) AS ppu FROM {$this->profile->tablename} WHERE timestamp >= ". $db->quote($this->from) ." AND timestamp <= ". $db->quote($this->to) ." AND crawl = 0 GROUP BY month ORDER BY timestamp";

		} else {
			# Use the summary table.
			$query = " SELECT month, visitors, pages, (pages / visitors) AS ppu FROM {$this->profile->tablename_vpm} WHERE timestamp >= ". $db->quote($this->from) ." AND timestamp <= ". $db->quote($this->to) ." ORDER BY timestamp";
		}

		return $query;
	}
}
?>
