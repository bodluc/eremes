<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays a your overall performance
*/
$reports["_OVERALL_PERFORMANCE"] = Array(
	"ClassName" => "OverallPerformance", 
	"Category" => "_PERFORMANCE", 
	"icon" => "images/icons/32x32/conversion.png",
	"Options" => "daterangeField,displaymode,trafficsource,columnSelector",
	"Filename" => "overall_performance",
	"Distribution" => "Standard",
	"Order" => 2,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class OverallPerformance extends Report {

	function Settings() {
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "table,pie";
		$this->showfields = _PAGE.','._REQUESTS.','._VISITORS.','._CONVERSION;
		$this->help = _OVERALL_PERFORMANCE_DESC;
		$this->actionmenu_type = 'page';
	}
	
	function DefineQuery() {
		global $db;
		
		$query = "select count(distinct visitorid) from {$this->profile->tablename} where timestamp >=". $db->quote($this->from) ." and timestamp <=". $db->quote($this->to) ." and crawl=0 and status=200";
		
		$prequery= $db->Execute($query);
		$ptot=$prequery->FetchRow();
		$query  = "select u.url,count(*) as hits,count(distinct visitorid),((count(distinct visitorid)*1.00)/{$ptot[0]})*100 as ctr from {$this->profile->tablename_conversions} as a,{$this->profile->tablename_urls} as u where timestamp >=". $db->quote($this->from) ." and timestamp <=". $db->quote($this->to) ." and a.url=u.id group by a.url order by hits desc";
		
		return $query;
	}
}
?>
