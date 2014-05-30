<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays a detailed crawler report
*/
$reports["_DETAILED_CRAWLER_REPORT"] = Array(
	"ClassName" => "DetailedCrawlerReport", 
	"Category" => "",
	"icon" => "images/icons/32x32/detailed_crawler_report.png",
	"Options" => "daterangeField,trafficsource,limit,columnSelector",
	"Filename" => "detailed_crawler_report",
	"Distribution" => "Standard",
	"hidden" => true,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => false
);

class DetailedCrawlerReport extends Report {
	
	function Settings() {
		$this->showfields = _PAGE.','._PARAMETERS.','._REQUESTS;
		$this->help = "";
	}
	
	function DefineQuery() {
		global $db;
		$query  = "select u.url, p.params, count(*) AS hits FROM {$this->profile->tablename} AS a, {$this->profile->tablename_urls} AS u, {$this->profile->tablename_urlparams} AS p, {$this->profile->tablename_useragents} AS ua WHERE md5(ua.name)='{$this->agent}' AND timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND a.url = u.id AND a.params = p.id AND a.useragentid = ua.id GROUP BY concat(u.url, p.params) ORDER BY hits DESC LIMIT ". addslashes($this->limit);
		
		return $query;
	}
	
	function DisplayReport() {
		$data = $this->CreateData();
		
		if(!empty($this->agent)) {
			$this->addlabel .= "<span>"._CRAWLER.": {$this->agent_string}</span>";
		}
		
		$this->Table($data);
	}
	
	function DisplayCustomForm() {
		echo "<input type='hidden' id='agent' name='agent' value='{$this->agent}' />";
		echo "<input type='hidden' id='agent_string' name='agent_string' value='{$this->agent_string}' />";
	}
}
?>
