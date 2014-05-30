<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays a overview of your page conversion
*/
$reports["_PAGE_CONVERSION"] = Array(
	"ClassName" => "PageConversion", 
	"Category" => "_PERFORMANCE", 
	"icon" => "images/icons/32x32/pageconversion.png",
	"Options" => "daterangeField,trafficsource,roadto,search,limit,columnSelector",
	"Filename" => "page_conversion",
	"Distribution" => "Standard",
	"Order" => 4,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class PageConversion extends Report {

	function Settings() {
		$this->showfields = ""._PAGE.","._VISITORS.","._CONVERTED_VISITORS.","._CONVERSION_RATE."";
		$this->help = _PAGE_CONVERSION_DESC;
	}
	
	function DefineQuery() {
		global $db, $nc;
		
		# DROP The temporary tables so that there wont be any duplication errors
		$db->Execute("DROP TABLE IF EXISTS {$this->profile->tablename}_top_entry_converted");
		$db->Execute("DROP TABLE IF EXISTS {$this->profile->tablename}_entrypages");
		$db->Execute("DROP TABLE IF EXISTS {$this->profile->tablename}_top_entry");
	
		if(empty($this->roadto)){ 
			if ($this->profile->targetfiles) {
				$targets=explode(",",$this->profile->targetfiles);
				$this->roadto = $targets[0];		
			}else{
				$this->ReportHeader(); echoNotice(_CHOOSE_KPI_FROM_SETTINGS.".","margin:5px;"); die();
			}			
		}
		if (!$this->roadto) {
            $this->showfields = _INFO;
            $query = "select \""._CHOOSE_TARGET_FILE."\"";
            return;
        }
        // $this->addlabel= " for {$this->roadto}";
        
        # first, get the ID of the target page
        $kpi = getID($this->roadto,"urls");
        
        $top_entry_converted_search="";
        if (!empty($this->search)) {        
            $searchst = $this->SearchMatchingIDs($this->search,"url",$this->searchmode,$this->profile->tablename_urls);
            $top_entry_converted_search = str_replace("and url IN", "where entry IN", $searchst);            
        } else {
			$searchst = "";
		}
		
        # get the entry page of each converted user for a certain KPI, then count the ocurrences
        $entrypages_for_conversion = subsetDataToSourceID("SELECT $nc a.visitorid as cvisitor, SUBSTRING_INDEX(Group_Concat(a.url order by a.timestamp),',',1) as entry FROM {$this->profile->tablename} as a, {$this->profile->tablename_conversions} as c where (c.timestamp >=". $db->quote($this->from) ." and c.timestamp <=". $db->quote($this->to) .") and (a.timestamp >=". $db->quote($this->from) ." and a.timestamp <=". $db->quote($this->to) .") and c.url='{$kpi}' and c.visitorid=a.visitorid and crawl=0 and (status=200 or status=302) group by a.visitorid", $this->trafficsource);
        $converted = "create temporary table {$this->profile->tablename}_top_entry_converted SELECT $nc entry, count(distinct cvisitor) as conversions from ($entrypages_for_conversion) as cvisitors $top_entry_converted_search group by entry";
        $db->Execute($converted);        
        
        # next, get the entry page for each visitor
        $entrypages = "create temporary table {$this->profile->tablename}_entrypages ". subsetDataToSourceID("SELECT $nc visitorid, SUBSTRING_INDEX(Group_Concat(url order by timestamp),',',1) as url FROM {$this->profile->tablename} where (timestamp >=". $db->quote($this->from) ." and timestamp <=". $db->quote($this->to) .") and crawl=0 and (status=200 or status=302) group by visitorid", $this->trafficsource);
        $db->Execute($entrypages);
         
        # now count the top entry pages
        $toppages = "create temporary table {$this->profile->tablename}_top_entry SELECT $nc url, count(distinct visitorid) as visitors from {$this->profile->tablename}_entrypages, {$this->profile->tablename}_top_entry_converted where entry=url $searchst group by url order by visitors desc limit ". addslashes($this->limit);       
        $db->Execute($toppages);
		
        # now join it all together
        $query = "select $nc u.url, visitors, conversions, CONCAT(FORMAT((conversions/visitors)*100,2),' %') as crate from {$this->profile->tablename}_top_entry as a LEFT JOIN {$this->profile->tablename}_top_entry_converted as b on (b.entry=a.url) LEFT JOIN {$this->profile->tablename_urls} as u on (u.id=a.url) order by visitors desc";
        $applytrafficsource = false;
		return $query;
	}
}
?>
