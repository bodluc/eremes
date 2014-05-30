<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays the Top click paths
*/
$reports["_TOP_CLICK_PATHS"] = Array(
	"ClassName" => "TopClickPaths", 
	"Category" => "_POPULAR_CONTENT", 
	"icon" => "images/icons/32x32/topclickpaths.png",
	"Options" => "daterangeField,displaymode,trafficsource,limit,columnSelector",
	"Filename" => "top_click_paths",
	"Distribution" => "Standard",
	"Order" => 10,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class TopClickPaths extends Report {
	
	function Settings() {
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "table,pie";
		$this->showfields = _PATH.','._HITS;
		$this->help = _TOP_CLICK_PATHS_DESC;
	}
	
	function DefineQuery() {
		global $db;
        
        @$db->Execute("SET SESSION group_concat_max_len = 65535");
		
		$ServerInfo = $db->ServerInfo(); 
        if ($ServerInfo["version"] < 4.1) {
            echo "<script>  loading.style.visibility=\"hidden\";</script> "._ONLY_WORKS_WITH_MYSQL_4_PLUS.":" .$ServerInfo["description"].$ServerInfo["version"];
            exit();
        } else {
            $query="select trail, count(*) as hits from ";
            $query.=subsetDataToSourceID("(SELECT visitorid, Group_Concat(DISTINCT u.url order by timestamp SEPARATOR ' <img src=images/icons/arrow_right.gif> ') trail FROM {$this->profile->tablename} as a,{$this->profile->tablename_urls} as u where timestamp >=". $db->quote($this->from) ." and timestamp <=". $db->quote($this->to) ." and crawl=0 and status=200 and a.url=u.id group by visitorid) ",$this->trafficsource);
            $query.="as paths group by trail order by hits desc limit ". addslashes($this->limit);   
            $applytrafficsource=false;
            //probably best to leave it in the temp table though cus them we can do searches on files that are in the trail, i,.e show all trails that contain url x
        }
		
		return $query;
	}
}
?>
