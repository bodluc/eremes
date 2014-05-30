<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc Definitions for this report: Top referrers

    Hits: The number of times someone clicked to your site via this referring URL.
    Referrer: The external referring page, excluding parameters (This page has a link to your site!).
    Landing Page: A page on your site that was the target of the external link.
*/
$reports["_TOP_REFERRERS"] = Array(
	"ClassName" => "TopReferrers", 
	"Category" => "_INCOMING_TRAFFIC", 
	"icon" => "images/icons/32x32/topreferrers.png",
	"Options" => "daterangeField,displaymode,trafficsource,search,limit,columnSelector",
	"Filename" => "top_referrers",
	"Distribution" => "Standard",
	"Order" => 2,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class TopReferrers extends Report {

	function Settings() {
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "table,pie";
		$this->showfields = _REFERRER.','._VISITORS.','._HITS;
		$this->help = _TOP_REFERRERS_DESC;
		$this->actionmenu_type = 'referrer';
	}
	
	function DefineQuery() {
		global $nc,$db;
		
        if (isset($old)) {
            if (!empty($this->search)) {
                $searchst = $this->MakeSearchString($this->search,"r.referrer",$this->searchmode); 
            } else {
				$searchst = "";
			}
            $query  = "select {$nc} r.referrer,count(distinct visitorid) as visitors, count(*) as hits from {$this->profile->tablename},{$this->profile->tablename_referrers} as r  where timestamp >= ".$db->quote($this->from)." and timestamp <= ".$db->quote($this->to)." and {$this->profile->tablename}.referrer=r.id and r.referrer NOT like '%{$this->profile->confdomain}/%' and crawl=0 and status=200 {$searchst} group by r.referrer order by visitors desc limit ". addslashes($this->limit);
        } else {
            if (!empty($this->search)) {
                $searchst = $this->SearchMatchingIDs($this->search,"referrer",$this->searchmode,$this->profile->tablename_referrers);
            } else {
				$searchst = "";
			}
            $subquery = subsetDataToSourceID("select {$nc} referrer, count(distinct visitorid) as visitors,count(*) as hits from {$this->profile->tablename} where timestamp >= ".$db->quote($this->from)." and timestamp <= ".$db->quote($this->to)." and crawl=0 and status=200 {$searchst} and referrer IN (select id from {$this->profile->tablename_referrers} where referrer NOT like 'http://{$this->profile->confdomain}%' and referrer NOT like 'https://{$this->profile->confdomain}%') group by referrer order by visitors desc limit ". addslashes($this->limit),$this->trafficsource);           
            $query = "select {$nc} r.referrer, sq.visitors, sq.hits from ($subquery) as sq,{$this->profile->tablename_referrers} as r where sq.referrer=r.id";
            $this->applytrafficsource=false;
        }

		return $query;
	}
    
    
}
?>
