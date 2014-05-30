<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays a overview of search engines
*/
$reports["_SEARCH_ENGINES"] = Array(
	"ClassName" => "SearchEngines", 
	"Category" => "_INCOMING_TRAFFIC", 
	"icon" => "images/icons/32x32/searchengines.png",
	"Options" => "daterangeField,displaymode,trafficsource,limit,columnSelector",
	"Filename" => "search_engines",
	"Distribution" => "Standard",
	"Order" => 11,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class SearchEngines extends Report {

	function Settings() {
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "table,pie";
		$this->showfields = _SEARCH_ENGINE.','._VISITORS.','._HITS.','._SEARCHES_PER_USER;
		$this->help = "";
		if(empty($this->limit)){ $this->limit = 100; }
	}
	
	function DefineQuery() {
		global $db;
        if ($this->from < mktime(12,0,0,6,3,2009)) { // This is when Bing was launched. If report is in an earlier date, we still display these old buggers 
            $query= subsetDataToSourceID(" select r.referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r,{$this->profile->tablename_keywords} as k where timestamp >=". $db->quote($this->from) ." and timestamp <=". $db->quote($this->to) ." and a.keywords=k.id and k.keywords!='' and r.referrer not like \"http://www.google.%\" and r.referrer not like \"%search.yahoo.%\" and r.referrer not like \"%.search.msn.%\" and r.referrer not like \"%.live.com%\" and r.referrer not like \"%.search.aol.%\" and r.referrer not like \"%.ask.com%\" and r.referrer not like \"http://www.dogpile.com%\" and crawl=0 and a.referrer=r.id group by a.referrer union ", $this->trafficsource);
            $query.= subsetDataToSourceID("select \"MSN Search\" referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r where timestamp >=". $db->quote($this->from) ." and timestamp <=". $db->quote($this->to) ." and (r.referrer like \"%search.msn.%\" or r.referrer like  \"%.live.com%\") and crawl=0 and a.referrer=r.id union ", $this->trafficsource);
        } else {
            $query= subsetDataToSourceID(" select r.referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r,{$this->profile->tablename_keywords} as k where timestamp >=". $db->quote($this->from) ." and timestamp <=". $db->quote($this->to) ." and a.keywords=k.id and k.keywords!='' and r.referrer not like \"http://www.google.%\" and r.referrer not like \"%search.yahoo.%\" and r.referrer!=\"http://www.bing.com/search\" and r.referrer not like \"%.search.aol.%\" and r.referrer not like \"%.ask.com%\" and r.referrer not like \"http://www.dogpile.com%\" and crawl=0 and a.referrer=r.id group by a.referrer union ", $this->trafficsource);   
        }
        
        $query.= subsetDataToSourceID("select \"Google (Natural Search)\" referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r,{$this->profile->tablename_urlparams} as up where timestamp >=". $db->quote($this->from) ." and timestamp <=". $db->quote($this->to) ." and (r.referrer like \"http://www.google.%\" and up.params NOT like \"?gclid=%\") and crawl=0 and a.referrer=r.id and a.params=up.id union ", $this->trafficsource);

        $query.= subsetDataToSourceID("select \"Google (Paid Search)\" referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r,{$this->profile->tablename_urlparams} as up where timestamp >=". $db->quote($this->from) ." and timestamp <=". $db->quote($this->to) ." and r.referrer like \"http://www.google.%\" and up.params like \"?gclid=%\" and crawl=0 and a.referrer=r.id and a.params=up.id union ", $this->trafficsource);

        $query.= subsetDataToSourceID("select \"Yahoo\" referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from  {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r where timestamp >=". $db->quote($this->from) ." and timestamp <=". $db->quote($this->to) ." and r.referrer like \"%search.yahoo.%\" and crawl=0 and a.referrer=r.id union ", $this->trafficsource);

        $query.= subsetDataToSourceID("select \"Bing\" referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r where timestamp >=". $db->quote($this->from) ." and timestamp <=". $db->quote($this->to) ." and r.referrer = \"http://www.bing.com/search\" and crawl=0 and a.referrer=r.id union ", $this->trafficsource);
        
        $query.= subsetDataToSourceID("select \"AOL Search\" referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r where timestamp >=". $db->quote($this->from) ." and timestamp <=". $db->quote($this->to) ." and r.referrer like \"%search.aol.%\" and crawl=0 and a.referrer=r.id union ", $this->trafficsource);

        $query.= subsetDataToSourceID("select \"Ask.com\" referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r where timestamp >=". $db->quote($this->from) ." and timestamp <=". $db->quote($this->to) ." and r.referrer like \"%.ask.com%\" and crawl=0 and a.referrer=r.id union ", $this->trafficsource);

        $query.= subsetDataToSourceID("select \"Dogpile.com\" referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r where timestamp >=". $db->quote($this->from) ." and timestamp <=". $db->quote($this->to) ." and r.referrer like \"http://www.dogpile.com%\" and crawl=0 and a.referrer=r.id ", $this->trafficsource);

        $query.= " order by  visitors desc LIMIT ". addslashes($this->limit);
		
		
        
        $this->applytrafficsource = false;
		
		return $query;
	}
	
	function DisplayReport() {
		$data = $this->CreateData();
		
		foreach($data as $key => $row) {
			$data[$key][0] = '<a class=\'open_in_new_dialog quickopen\' href=\'reports.php?conf='.$this->profile->profilename.'&labels=_DETAILED_SEARCH_ENGINES&se='.$row[0].'\' rel=\'DetailedSearchEngines\' type=\'_DETAILED_SEARCH_ENGINES\' name=\''._DETAILED_SEARCH_ENGINES.'\'>'.str_replace("[G]", "<img src='images/google.png' border='0' />", $row[0]).'</a>';
		}
		
		$this->Table($data);
	}
}
?>
