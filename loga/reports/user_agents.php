<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc Definitions for this report: User Agents

    Visitors: The number of visitors with browsers of this type.
    Browser: the Browser name.

Move your mouse over the number of requests, a tooltip will appear containing the percentage share of the selected browser/os. 
(This works in all reports, by the way ;-) Also check theReport for more browser details.
*/
$reports["_USER_AGENTS"] = Array(
	"ClassName" => "UserAgents", 
	"Category" => "_CLIENT_SYSTEM", 
	"icon" => "images/icons/32x32/user_agents.png",
	"Options" => "daterangeField,trafficsource,limit,columnSelector",
	"hidden" => true,
	"Distribution" => "Standard",
	"Filename" => "user_agents",
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class UserAgents extends Report {

	function Settings() {
		$this->showfields = _BROWSER.','._VISITORS;
		$this->help = _BROWSERS_DESC;
	}
	
	function DefineQuery() {
		global $db;
		
		if(!empty($this->limit)) {
			$limit = $this->limit;
		} else {
			$limit = 100;
		}		
        // $query  = "select AGENTS.name useragent, count(distinct visitorid) as visitors from {$this->profile->tablename} left outer join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >={$this->from} and timestamp <={$this->to} and crawl=0 group by useragent order by visitors desc limit {$limit}";
        $query  = "SELECT ua.name AS useragent, COUNT(DISTINCT visitorid) AS visitors FROM {$this->profile->tablename} AS a, {$this->profile->tablename_useragents} as ua WHERE a.useragentid = ua.id AND a.timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND a.crawl = 0 GROUP BY useragent ORDER BY visitors DESC LIMIT ".addslashes($this->limit);
        
		return $query;
	}
}
?>
