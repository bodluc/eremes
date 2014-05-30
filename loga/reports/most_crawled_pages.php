<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays the most crawled pages
*/
$reports["_MOST_CRAWLED_PAGES"] = Array(
	"ClassName" => "MostCrawledPages", 
	"Category" => "_POPULAR_CONTENT", 
	"icon" => "images/icons/32x32/mostcrawledpages.png",
	"Options" => "daterangeField,displaymode,trafficsource,search,limit,columnSelector",
	"Filename" => "most_crawled_pages",
	"Distribution" => "Standard",
	"Order" => 6,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class MostCrawledPages extends Report {

	function Settings() {
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "table,pie";
		$this->showfields = _PAGE.','._BOTS.','._REQUESTS;
		$this->help = _MOST_CRAWLED_PAGES_DESC;
		$this->actionmenu_type = 'page';
	}
	
	function DefineQuery() {
		
        if (!empty($this->search)) {
            $searchst = $this->MakeSearchString($this->search,"u.url",$this->searchmode);
        } else {
			$searchst = "";
		}
		
        $query  = "SELECT u.url, COUNT(DISTINCT visitorid) AS visitors, count(*) AS hits FROM {$this->profile->tablename} AS a, {$this->profile->tablename_urls} AS u WHERE timestamp >= {$this->from} and timestamp <= {$this->to} AND a.url = u.id {$searchst} and crawl = 1 GROUP BY a.url ORDER BY visitors DESC LIMIT {$this->limit}";
		
		return $query;
	}
}
?>
