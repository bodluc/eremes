<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc Definitions for this report: Visitors and Visists

    Visitors: Unique Visitors that accessed your site (excluding any bots and crawlers).
    Visits: The total number of visits people made to your site
    Visits per user: The average number of times one users visits your site (the total number of visits devided by the number of visitors).
*/
$reports["_VISITORS_AND_VISITS"] = Array(
	"ClassName" => "VisitorsAndVisits",
	"Category" => "_VISITOR_DETAILS",
	"icon" => "images/icons/32x32/visitorsandvisits.png",
	"Options" => "daterangeField,displaymode,trafficsource,columnSelector",
	"Filename" => "visitors_and_visits",
	"Distribution" => "Standard",
	"Order" => 7,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class VisitorsAndVisits extends Report {

	function Settings() {
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "table,linechart,barchart";
		$this->showfields = _DATE.','._VISITORS.','._VISITS.','._VISITS_PER_USER; 
		$this->help = _VISITORS_AND_VISITS_DESC;
	}
	
	function DefineQuery() {
		global $db;
		if (!empty($this->trafficsource)) {
			// If we're using a traffic source, then we can't use the summary tables.            
			$query  = "SELECT FROM_UNIXTIME(timestamp, '%d-%b-%Y %a') AS days, COUNT(DISTINCT visitorid) AS visitors,COUNT(DISTINCT sessionid) AS visits, (COUNT(DISTINCT sessionid) / COUNT(DISTINCT visitorid)) AS vpu FROM {$this->profile->tablename} WHERE timestamp >= ". $db->quote($this->from) ." AND timestamp <= ". $db->quote($this->to) ." AND crawl = 0 GROUP BY days ORDER BY timestamp";
		} else {
			// Use the summary table.
			$query = "SELECT days, visitors, visits, (visits / visitors) vpu FROM {$this->profile->tablename_vpd} WHERE timestamp >= ". $db->quote($this->from) ." AND timestamp <= ". $db->quote($this->to) ." ORDER BY timestamp";
		}
		return $query;
	}
}
?>