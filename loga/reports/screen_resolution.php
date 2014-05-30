<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays a overview of the screen resolutions
*/
$reports["_SCREEN_RESOLUTION"] = Array(
	"ClassName" => "ScreenResolution", 
	"Category" => "_CLIENT_SYSTEM", 
	"icon" => "images/icons/32x32/screenresolution.png",
	"Options" => "daterangeField,displaymode,trafficsource,limit,columnSelector",
	"Filename" => "screen_resolution",
	"Distribution" => "Standard",
	"Order" => 9,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => false
);

class ScreenResolution extends Report {

	function Settings() {
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "table,pie";
		$this->showfields = _SCREEN_RESOLUTION.','._VISITS;
		$this->help = _SCREEN_RESOLUTION_DESC;		
	}
	
	function DefineQuery() {
		global $db;
        $query  = "select screenres,sum(visits) as hits from {$this->profile->tablename_screenres} where timestamp >=". $db->quote($this->from) ." and timestamp <=". $db->quote($this->to) ." group by screenres order by hits desc LIMIT ". addslashes($this->limit);
        
		return $query;
	}
}
?>
