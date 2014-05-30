<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc Definitions for this report: Top Pages

    Hits: The number of times someone clicked to your site using this keyword search.
    Keywords: The keywords that were used to find your site.
    Landing Page: A page on your site that was the target of the external link.
    CAUTION: the Landing Page listed below does not have to be the only page the referrer can link to. A more acturate description would be 'A recent landing page for this keyword'. For more accurate landing pages, check the Top Keyword Details report.
*/
$reports["_TOP_PAGES"] = Array(
	"ClassName" => "TopPages", 
	"Category" => "_POPULAR_CONTENT", 
	"icon" => "images/icons/32x32/toppages.png",
	"Options" => "daterangeField,displaymode,trafficsource,search,limit,columnSelector",
	"Filename" => "top_pages",
	"Distribution" => "Standard",
	"Order" => 1,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class TopPages extends Report {
	
	function Settings() {
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "table,bubble,pie";
		$this->showfields = _PAGE.','._VISITORS.','._PAGEVIEWS.','._PAGES_PER_USER;
		$this->bubblefields = _VISITORS.','._PAGES_PER_USER.','._PAGEVIEWS.','._PAGE;
		$this->help = _TOP_KEYWORDS_DESC;
		$this->actionmenu_type = 'page';
	}
	
	function DefineQuery() {
		global $nc, $db;

		if (isset($old)) {
            if ($this->search) {
                $searchst = $this->MakeSearchString($this->search,"r.url",$this->searchmode);
            } else {
				$searchst = "";
			}
            $query  = "select r.url,count(distinct visitorid) as visitors,count(*) as hits from {$this->profile->tablename}, {$this->profile->tablename_urls} as r where timestamp >= {$this->from} and timestamp <= {$this->to} {$searchst} and r.id={$this->profile->tablename}.url and crawl=0 group by {$this->profile->tablename}.url order by visitors desc limit ". addslashes($this->limit);
        } else {
            if (!empty($this->search)) {
                $searchst = $this->SearchMatchingIDs($this->search, "url", $this->searchmode, $this->profile->tablename_urls);
            } else {
				$searchst = "";
			}
            $subquery = subsetDataToSourceID("select {$nc} url, count(distinct visitorid) as visitors, count(*) as hits from {$this->profile->tablename} where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." and crawl=0 {$searchst} group by url order by visitors desc limit " . addslashes($this->limit), $this->trafficsource);
			
            $query = "select {$nc} CONCAT(r.url,'##',r.title) as urlinfo, sq.visitors, sq.hits, (sq.hits/sq.visitors) as pv  from ({$subquery}) as sq, {$this->profile->tablename_urls} as r where sq.url=r.id";
			
            $this->applytrafficsource = false;
        }

		return $query;
	}
    
    
}
?>
