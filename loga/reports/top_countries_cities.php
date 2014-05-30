<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays a overview of your top countries/cities
*/
$reports["_TOP_COUNTRIES_CITIES"] = Array(
	"ClassName" => "TopCountriesCities", 
	"Category" => "_INCOMING_TRAFFIC", 
	"icon" => "images/icons/32x32/topcountriescities.png",
	"Options" => "daterangeField,trafficsource,limit,columnSelector",
	"Filename" => "top_countries_cities",
	"Distribution" => "Standard",
	"Order" => 8,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class TopCountriesCities extends Report {

	function Settings() {
		$this->showfields = _COUNTRIES.','._VISITORS.','._PAGEVIEWS.','._PAGES_PER_USER;
		$this->help = _TOP_COUNTRIES_CITIES_DESC;	
	}
	
	function DefineQuery() {
		global $db;
		$query  = "select country, count(distinct visitorid) as ips,count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from {$this->profile->tablename} where timestamp >=". $db->quote($this->from) ." and timestamp <=". $db->quote($this->to) ." and crawl=0 and country!='' group by country order by ips desc limit ". addslashes($this->limit);
		
		return $query;
	}
	
	function CreateData() {
		global $db, $applytrafficsource, $cnames;
		$q = $this->DefineQuery();
		if ($q!==false) {
			if ($applytrafficsource) { $q = subsetDataToSourceID($q,$this->trafficsource);  }
			$db->SetFetchMode(ADODB_FETCH_NUM);
			$result = $db->Execute($q);
			$data = $result->GetArray();
			$db->SetFetchMode(ADODB_FETCH_BOTH);
			
			return $data;
		} else {
			# if the report is not based on a single database query, this is where the data will be created
			return false;
		}
	}
	
	function DisplayReport() {
		global $cnames;
		
		if (isset($this->outputmode)) {
			$link = "&outputmode=".$this->outputmode;
		} else {
			$link="";
		}
		
		$data = $this->CreateData();
		
		foreach($data as $key => $row) {
			$countryname = @$cnames[$row[0]];
			$ccode = strtolower($row[0]);
			$image= "<img hspace=3 width=14  height=11 src=\"images/flags/{$ccode}.png\" border=0 alt=\"{$ccode}\">";
			
			# without @ you get error: Creating default object from empty value, if empty do statement does not fix this.
			@$tmp_code->country = $ccode;
			
			$data[$key][0] = $image.'<a class=\'open_in_new_dialog quickopen\' options=\''.json_encode($tmp_code).'\' href=\'reports.php?conf='.$this->profile->profilename.'&labels=_TOP_CITIES&statstable_only=1&country='.$row[0].$link.'\' rel=\'TopCities\' type=\'_TOP_CITIES\' name=\''._TOP_CITIES.'\'>'.$countryname.'</a>';
		}
		
		$this->Table($data);
	}
}
?>
