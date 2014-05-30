<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc Definitions for this report: Visitors per Hour

    Visitors: Unique Visitors that accessed your site (excluding any bots and crawlers).
    Pageviews: The total number of pages requested (excluding any bots and crawlers).
    Pages per user: The total number of pages devided by the number of visitors.

Tip: When viewing more than one day, keep in mind that you are seeing summed up totals for that hour for each day in your date range. 
*/
$reports["_VISITORS_PER_HOUR"] = Array(
	"ClassName" => "VisitorsPerHour", 
	"Category" => "_VISITOR_DETAILS", 
	"icon" => "images/icons/32x32/visitorsperhour.png",
	"Options" => "daterangeField,displaymode,trafficsource,columnSelector",
	"Filename" => "visitors_per_hour",
	"Distribution" => "Standard",
	"Order" => 3,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class VisitorsPerHour extends Report {
	function Settings() {
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "table,linechart,barchart";
		$this->showfields = _HOUR.','._VISITORS.','._PAGEVIEWS.','._PAGES_PER_USER;
		$this->help = _VISITORS_PER_HOUR_DESC;
	}
	
	function DefineQuery() {
		global $db;
		if (!empty($this->trafficsource)) {
			// If we're using a traffic source, then we can't use the summary tables.            
			$query  = "SELECT FROM_UNIXTIME(timestamp, '%H') AS hours, COUNT(DISTINCT visitorid) AS visitors, COUNT(*) AS pages, (COUNT(*) / COUNT(DISTINCT visitorid)) AS ppu FROM {$this->profile->tablename} WHERE timestamp >= ". $db->quote($this->from) ." AND timestamp <= ". $db->quote($this->to) ." AND crawl = 0 GROUP BY hours ORDER BY hours";
			
		} else {
			// Use the summary table.
			$query  = "SELECT FROM_UNIXTIME(timestamp, '%H') AS hours, COUNT(DISTINCT visitorid) AS visitors, COUNT(*) AS pages, (COUNT(*) * 1.00) / COUNT(DISTINCT visitorid) as ppu FROM {$this->profile->tablename} WHERE timestamp >= ". $db->quote($this->from) ." AND timestamp <= ". $db->quote($this->to) ." AND crawl = 0 GROUP BY hours ORDER BY hours";
		}

		return $query;
	}
}
?>
