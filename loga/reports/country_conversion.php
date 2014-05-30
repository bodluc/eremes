<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays a overview of referrrer conversion.
*/

$reports["_COUNTRY_CONVERSION"] = Array(
	"ClassName" => "CountryConversion", 
	"Category" => "_PERFORMANCE", 
	"icon" => "images/icons/32x32/country_conversion.png",
	"Options" => "daterangeField,trafficsource,roadto,limit,displaymode,columnSelector",
	"Filename" => "country_conversion",
	"Distribution" => "Standard",
	"Order" => 7,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => false
);

class CountryConversion extends Report {
	
	function Settings() {
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "pie,table";
		$this->showfields = _COUNTRY.','._VISITORS.','.'Converted Visitors'.','._CONVERSION_RATE;
		$this->help = "";
		$this->displayReportButtons = false;
		$this->allowDateFormat = false;
		$this->bchart = array(_VISITORS, 'Converted Visitors');
		
		if(empty($this->displaymode)) { $this->displaymode = $this->DefaultDisplay; }
	}
	
	function DefineQuery() {
		global $db, $nc;
		
		# Select all countries and visitors per each country
		# We can't rely on a limit here
		$query = "SELECT
			country,
			COUNT(DISTINCT visitorid) AS ips
		FROM {$this->profile->tablename}
		WHERE
			timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND
			crawl = 0 AND
			country != ''
		GROUP BY country
		ORDER BY ips DESC";
		
		return $query;
	}
	
	function ConvertData($data) {
		global $db, $cnames;
		
		$cdata = array();
		foreach($data as $entry) {
			if($this->displaymode == 'pie') {
				$cdata[$entry[0]][0] = $cnames[$entry[0]];
				$cdata[$entry[0]][2] = $entry[1];
			} else {
				$cdata[$entry[0]][0] = $entry[0];
				$cdata[$entry[0]][1] = $entry[1];
			}
		}
		
		# Select top [limit] converted countries
		$query = "SELECT
			country,
			COUNT(DISTINCT visitorid) AS visitors
		FROM
			{$this->profile->tablename} AS a,
			{$this->profile->tablename_urls} AS u
		WHERE
			timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND
			crawl = 0
			AND status = 200
			AND a.url = u.id
			AND u.url = ". $db->quote($this->roadto) ."
			AND country != ''
		GROUP BY country
		ORDER BY visitors DESC
		LIMIT ". addslashes($this->limit);
		
		$q = $db->Execute($query);
		
		# Prepare a data array, with country as key and insert countries and visitors
		while($row = $q->FetchRow()) {
			if($this->displaymode == 'pie') {
				$cdata[$row[0]][1] = $row[1];
			} else {
				$cdata[$row[0]][2] = $row[1];
			}
			$cdata[$row[0]][3] = number_format((($row[1] / $cdata[$row[0]][1]) * 100), 2)."%";
		}
		
		# Merge prepared data array with converted data array
		# Use an integer as key; $this->Table function expects it to be
		# We also skip countries that didn't convert
		$c = 0;
		$seed_data = array();
		foreach($cdata as $key => $entry) {
			if($this->displaymode == 'pie') {
				if(!empty($entry[1])) {
					$seed_data[$c] = $cdata[$key];
					$c++;
				}
			} else {
				if(!empty($entry[2])) {
					$seed_data[$c] = $cdata[$key];
					$c++;
				}
			}
		}
		
		# Sort the data by conversion rate
		$seed_data = DataSort($seed_data, 2);
		
		return $seed_data;
	}
	
	function DisplayReport() {
		$data = $this->CreateData();
		
		$data = $this->ConvertData($data);
		
		if($this->displaymode == 'pie') {
			$this->ReportHeader();
			$this->PieChart($data);
		} else {
			$this->Table($data);
		}
	}
}
?>