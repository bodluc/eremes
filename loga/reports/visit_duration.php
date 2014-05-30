<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc Definitions for this report:

    Time Spent: How long do your visitors stay on your site in one session/visit.
    Visit Share: The percentage of visitors that fall into this visit time range.
    Average Duration in minutes: The average duration for this visit time range.
*/
$reports["_VISIT_DURATION"] = Array(
	"ClassName" => "VisitDuration", 
	"Category" => "_VISITOR_DETAILS", 
	"icon" => "images/icons/32x32/visitduration.png",
	"Options" => "daterangeField,displaymode,trafficsource,columnSelector",
	"Filename" => "visit_duration",
	"Distribution" => "Standard",
	"Order" => 8,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class VisitDuration extends Report {

	function Settings() {
		global $db;
		
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "table,pie";
		$this->showfields = _TIME_SPENT.','._VISITS.','._AVERAGE_DURATION_IN_MINUTES.','._VISIT_SHARE;
		$this->help = _VISIT_DURATION_DESC;
		
		$ServerInfo = $db->ServerInfo(); 
        if ($ServerInfo["version"] < 4 && $databasedriver!="sqlite") {
            echo _ONLY_WORKS_WITH_MYSQL_4_PLUS.":" .$ServerInfo["description"].$ServerInfo["version"];
            exit();   
        } else {
			//we can't do a temporary table in mysql 5, so drop and 
			$prequery = "drop table ".$this->profile->tablename."_vlength";
			@$db->Execute($prequery);
			
			$prequery = "create table ".$this->profile->tablename."_vlength (length int(11), visitorid char(32)) ENGINE=MyISAM CHARSET=utf8";
			$db->Execute($prequery);
		}
	}
	
	function DefineQuery() {
		global $db;
		
		$query = subsetDataToSourceID("insert into ".$this->profile->tablename."_vlength select (max(timestamp)-min(timestamp)), visitorid from {$this->profile->tablename} force index (timestamp) where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." and status=200 and crawl=0 group by sessionid",$this->trafficsource);
		 
		$db->Execute($query);
		
		$range = @$db->Execute("select min(length), max(length),count(*) from ".$this->profile->tablename."_vlength");
		if($range == false) { return false; }
		
		$range_data = $range->FetchRow();
		$min = $range_data[0];
		$max = $range_data[1];
		$total_visitors=$range_data[2];
		$blocksize=($max-$min)/8;
		$query  = subsetDataToSourceID("select \"0 to 10 seconds            \",count(*), avg(length)/60, ((count(*)*1.0)/{$total_visitors}*100), \"1\" as ord from ".$this->profile->tablename."_vlength where length >=0 and length <=10 union ",$this->trafficsource);
		$query  .= subsetDataToSourceID("select \"10 to 60 seconds            \",count(*), avg(length)/60, ((count(*)*1.0)/{$total_visitors}*100), \"2\" as ord from ".$this->profile->tablename."_vlength where length >=10 and length <=60 union ",$this->trafficsource);
		$query  .= subsetDataToSourceID("select \"1 to 5 minutes            \",count(*), avg(length)/60, ((count(*)*1.0)/{$total_visitors}*100), \"3\" as ord from ".$this->profile->tablename."_vlength where length >=60 and length <=300 union ",$this->trafficsource);
		$query  .= subsetDataToSourceID("select \"5 to 15 minutes            \",count(*), avg(length)/60, ((count(*)*1.0)/{$total_visitors}*100), \"4\" as ord from ".$this->profile->tablename."_vlength where length >=300 and length <=900 union ",$this->trafficsource);
		$query  .= subsetDataToSourceID("select \"15 to 30 minutes            \",count(*), avg(length)/60, ((count(*)*1.0)/{$total_visitors}*100), \"5\" as ord from ".$this->profile->tablename."_vlength where length >=900 and length <=1800 union ",$this->trafficsource);
		$query  .= subsetDataToSourceID("select \"30 to 1 hour            \",count(*), avg(length)/60, ((count(*)*1.0)/{$total_visitors}*100), \"6\" as ord from ".$this->profile->tablename."_vlength where length >=1800 and length <=3600 union ",$this->trafficsource);
		$query  .= subsetDataToSourceID("select \"more than 1 hour            \",count(*), avg(length)/60, ((count(*)*1.0)/{$total_visitors}*100), \"7\" as ord from ".$this->profile->tablename."_vlength where length >=3600",$this->trafficsource);
		
		$query .= " order by ord";
		
		$this->applytrafficsource = false;        

		return $query;
	}
}
?>
