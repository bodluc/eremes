<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays the color palette
*/
$reports["_COLOR_PALETTE"] = Array(
	"ClassName" => "ColorPalette", 
	"Category" => "_CLIENT_SYSTEM", 
	"icon" => "images/icons/32x32/colorpalette.png",
	"Options" => "daterangeField,displaymode,trafficsource,columnSelector",
	"Filename" => "color_palette",
	"Distribution" => "Standard",
	"Order" => 10,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => false
);

class ColorPalette extends Report {
	
	function Settings() {
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "table,pie";
		$this->showfields = _COLOR_DEPTH.','._VISITS;
		$this->help = _COLOR_PALETTE_DESC;
	}
	
	function DefineQuery() {
		global $db;
		
        $query  = "select concat(colordepth, ' bit color'),sum(visits) as hits from {$this->profile->tablename_colordepth} where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." group by colordepth order by hits desc";
		
		return $query;
	}
}
?>
