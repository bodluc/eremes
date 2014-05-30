<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays a overview of your top keywords
*/
$reports["_TOP_KEYWORDS"] = Array(
	"ClassName" => "TopKeywords", 
	"Category" => "_INCOMING_TRAFFIC", 
	"icon" => "images/icons/32x32/topkeywords.png",
	"Options" => "daterangeField,displaymode,trafficsource,search,limit,columnSelector",
	"Filename" => "top_keywords",
	"Distribution" => "Standard",
	"Order" => 5,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class TopKeywords extends Report {

	function Settings() {
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "table,pie";
		$this->showfields = _KEYWORDS.','._VISITORS.','._HITS;
		$this->help = _TOP_PAGES_DESC;
		$this->actionmenu_type = 'keyword';
	}
	
	function DefineQuery() {
		global $db,$nc;

	    if(isset($this->search)) {
            $searchst="and k.keywords {$this->searchmode} '%".str_replace(" ", "%",$this->search)."%'";
        } else {
			$searchst = "";
		}
		
		$emptykeyword = getID('','keyword');
		
		$subquery = subsetDataToSourceID("select {$nc} keywords, count(distinct visitorid) as visitors, count(*) as hits from {$this->profile->tablename} where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." and crawl=0 and keywords != '{$emptykeyword}' group by keywords order by visitors desc limit ". addslashes($this->limit), $this->trafficsource);
		
		$query = "select {$nc} 
			k.keywords, 
			sq.visitors, 
			sq.hits
			
			from ({$subquery}) as sq, 
			{$this->profile->tablename_keywords} as k
			where sq.keywords=k.id {$searchst}";
			
		$searchmode = "like";

		return $query;
	}
    
    
}
?>
