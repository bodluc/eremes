<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays the error report
*/
$reports["_ERROR_REPORT"] = Array(
	"ClassName" => "ErrorReport", 
	"Category" => "_POPULAR_CONTENT", 
	"icon" => "images/icons/32x32/errorreport.png",
	"Options" => "daterangeField,displaymode,trafficsource,search,limit",
	"Filename" => "error_report",
	"Distribution" => "Standard",
	"Order" => 8,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class ErrorReport extends Report {
	
	function Settings() {
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "table,pie";
		$this->showfields = _STATUS.','._VIEWED_PAGES.','._CRAWLED_PAGES.','._TOTAL_HITS;
		$this->help = _ERROR_REPORT_DESC;
		$this->actionmenu_type = 'status';
	}
	
	function DefineQuery() {
		global $db;
		if(!isset($this->searchmode)){
			$this->searchmode = "like";
		}
		if (!empty($this->search)) {
			if(empty($this->status)) {		
				$searchst = $this->MakeSearchString($this->search,"u.url",$this->searchmode);
			}else{
				$searchst = $this->MakeSearchString($this->search,"u.url",$this->searchmode);
			}
		} else {
			$searchst = "";
		}
		
		if(!empty($this->status)) {
			$this->addlabel .= _ERROR_CODE.": ".$this->status;
			$this->showfields = _PAGE.','._VIEWED_PAGES.','._CRAWLED_PAGES.','._TOTAL_HITS;
			$query  = "select u.url,(count(*)-sum(crawl)) as viewed, sum(crawl),count(*) from {$this->profile->tablename} as a,{$this->profile->tablename_urls} as u where status=". $db->quote($this->status) ." and timestamp >=". $db->quote($this->from) ." and timestamp <=". $db->quote($this->to) ." and a.url=u.id {$searchst} group by a.url order by viewed desc limit ". addslashes($this->limit);
		} else {
			$query  = "select concat(l.status,' - ',s.descr),(count(*)-sum(crawl)) as viewed, sum(crawl),count(*) from {$this->profile->tablename} as l, {$this->profile->tablename_urls} as u, ".TBL_LGSTATUS." as s where l.timestamp >=". $db->quote($this->from) ." and l.timestamp <=". $db->quote($this->to) ." and l.status=s.code and l.url = u.id and l.status!=200 {$searchst} group by l.status order by viewed desc";
		}
		
		return $query;
	}
	
	function DisplayCustomForm() {
		if(empty($this->status)) { $this->status = ''; }
		echo "<label for='status'>"._STATUS."</label>";
		echo "<input class='report_option_field' id='status' type='text' value='{$this->status}' />";
	}
}
?>
