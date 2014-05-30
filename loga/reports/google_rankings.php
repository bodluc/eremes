<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays the google rankings
*/
$reports["_GOOGLE_RANKINGS"] = Array(
	"ClassName" => "GoogleRankings", 
	"Category" => "_INCOMING_TRAFFIC", 
	"icon" => "images/icons/32x32/google.png",
	"Options" => "daterangeField,displaymode,trafficsource,search,limit,columnSelector",
	"Filename" => "google_rankings",
	"Distribution" => "Standard",
	"Order" => 14,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class GoogleRankings extends Report {

	function Settings() {
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "table,pie";
		$this->showfields = _KEYWORDS.','._VISITORS.','._SEARCH_RESULT_PAGE;
		$this->help = _GOOGLE_RANKINGS_DESC;
		$this->actionmenu_type = 'keyword';
	}
	
	function DefineQuery() {
		if (!empty($this->search)) {
			$searchst="and k.keywords {$this->searchmode} '%".str_replace(" ", "%",$this->search)."%'";
		} else {
			$searchst="";
		}

		$query  = "select k.keywords,count(distinct visitorid) as visitors,(SUBSTR(rp.params,(LOCATE('start',rp.params)+6),2))/10+1 as page, rp.params from {$this->profile->tablename} as a,{$this->profile->tablename_keywords} as k,{$this->profile->tablename_urls} as u,{$this->profile->tablename_urlparams} as up,{$this->profile->tablename_refparams} as rp, {$this->profile->tablename_referrers} as r where timestamp >= {$this->from} and timestamp <= {$this->to} and a.keywords=k.id and a.url=u.id and a.params=up.id and a.refparams=rp.id and a.referrer=r.id and k.keywords!='' and up.params not like '?gclid%' and r.referrer like 'http://www.google.%' and crawl=0 {$searchst} group by a.keywords,page order by visitors desc limit {$this->limit}";

		return $query;
	}
}
?>
