<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays the days of the week
*/
$reports["_DAYS_OF_THE_WEEK"] = Array(
	"ClassName" => "DaysOfTheWeek", 
	"Category" => "_VISITOR_DETAILS", 
	"icon" => "images/icons/32x32/daysoftheweek.png",
	"Options" => "daterangeField,displaymode,trafficsource,columnSelector",
	"Filename" => "days_of_the_week",
	"Distribution" => "Standard",
	"Order" => 6,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class DaysOfTheWeek extends Report {
	
	function Settings() {
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "table,pie,linechart,barchart,areachart";
		$this->showfields = _DATE.','._VISITORS.','._PAGEVIEWS;
		$this->help = _DAYS_OF_THE_WEEK_DESC;
		$this->allowDateFormat = false;
	}
	
	function DefineQuery() {
		global $db;
		$query  = "SELECT FROM_UNIXTIME(timestamp,'%W') AS days, COUNT(distinct visitorid), COUNT(*) AS hits FROM {$this->profile->tablename} WHERE timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." AND crawl = 0 GROUP BY days ORDER BY FROM_UNIXTIME(timestamp, '%w')";
		
		return $query;
	}
}
?>
