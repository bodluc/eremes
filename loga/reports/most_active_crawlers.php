<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays the most active crawlers
*/
$reports["_MOST_ACTIVE_CRAWLERS"] = Array(
	"ClassName" => "MostActiveCrawlers", 
	"Category" => "_TRAFFIC", 
	"icon" => "images/icons/32x32/mostactivecrawlers.png",
	"Options" => "daterangeField,displaymode,search,limit,columnSelector",
	"Filename" => "most_active_crawlers",
	"Distribution" => "Standard",
	"Order" => 6,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class MostActiveCrawlers extends Report {

	function Settings() {
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "table,pie";
		$this->showfields = _USER_AGENT.','._REQUESTS.','._COUNTRY.','._MEGABYTES;
		$this->help = _MOST_ACTIVE_CRAWLERS_DESC;
	}
	
	function DefineQuery() {
		if (!empty($this->search)) {
            $searchst = $this->MakeSearchString($this->search,"ua.name",$this->searchmode);  
        } else {
			$searchst = "";
		}
		
		$query = "SELECT ua.name AS useragent, count(*) AS hits, a.country, (sum(a.bytes * 1.00) / 1024.0) / 1024.0 AS mb FROM {$this->profile->tablename} AS a INNER JOIN {$this->profile->tablename_useragents} as ua ON a.useragentid = ua.id WHERE a.timestamp BETWEEN {$this->from} AND {$this->to} AND a.crawl = 1 {$searchst} AND ua.is_mobile = 0 GROUP BY ua.name ORDER BY hits DESC LIMIT {$this->limit}";	
		
		return $query;
	}
}
?>
