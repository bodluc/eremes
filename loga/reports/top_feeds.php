<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays a overview of your top Feeds
*/
$reports["_TOP_FEEDS"] = Array(
	"ClassName" => "TopFeeds", 
	"Category" => "_SOCIAL_MEDIA", 
	"icon" => "images/icons/32x32/rssfeeds.png",
	"Options" => "daterangeField,search,limit,columnSelector",
	"Filename" => "top_feeds",
	"Distribution" => "Standard",
	"Order" => 1,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class TopFeeds extends Report {

	function Settings() {
		$this->showfields = _PAGE.','._VISITORS.','._PAGEVIEWS;
		$this->help = _TOP_FEEDS_DESC;
	}
	
	function DefineQuery() {
		global $db;

		if(empty($this->limit)) {
			$this->limit = 100;
		}		
		
        if (!empty($this->search)) {
            $searchst = $this->MakeSearchString($this->search,"r.url",$this->searchmode); 
        } else {
			$searchst = "";
		}
        $query  = "select r.url,count(distinct visitorid) as visitors,count(*) as hits from {$this->profile->tablename}, {$this->profile->tablename_urls} as r where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." {$searchst} and r.id={$this->profile->tablename}.url and crawl=2 group by {$this->profile->tablename}.url order by visitors desc limit ". addslashes($this->limit);
		
		return $query;
	}
}
?>
