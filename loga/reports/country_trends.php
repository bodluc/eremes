<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays the country trends
*/
$reports["_COUNTRY_TRENDS"] = Array(
	"ClassName" => "CountryTrends", 
	"Category" => "_INCOMING_TRAFFIC", 
	"icon" => "images/icons/32x32/countrytrends.png",
	"Options" => "daterangeField,trafficsource,period,displaymode,limit",
	"Filename" => "country_trends",
	"Distribution" => "Standard",
	"Order" => 9,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => false
);

class CountryTrends extends Report {
	
	function Settings() {
		$this->DefaultDisplay = "areachart";
		$this->DisplayModes = "table,linechart,areachart";
		$this->help = "";
		$this->displayReportButtons = false;
	}
	
	function DefineQuery() {
		global $db;
		
		if ($this->period == _DAYS) {
			$qd = "FROM_UNIXTIME(timestamp, '%a, %m/%d/%Y')";
		} else if ($this->period == _WEEKS) {
			$this->allowDateFormat = false;
			$qd = "FROM_UNIXTIME(timestamp, '%Y-W%V')";
		} else if ($this->period == _MONTHS) {
			$this->allowDateFormat = false;
			$qd = "FROM_UNIXTIME(timestamp, '%b %Y')";
		}
		
		$wstring = "";
		if(!empty($this->source)) {
			$this->addlabel = "Search for ". addslashes($this->sourcetype) .": ". $db->quote($this->source);
			if ($this->sourcetype == "page") {
				$search_query = "u.url = ". $db->quote($this->source) ." AND ";
			} else if ($this->sourcetype == "keyword") {
				$search_query = "k.keywords = ". $db->quote($this->source) ." AND ";
			} else if ($this->sourcetype == "referrer") {
				$search_query = "r.referrer = ". $db->quote($this->source) ." AND ";
			}
		} else {
			$search_query = "";
		}
		
		if(!empty($this->source)) {
			//get a top 10 of refferers first
			$query = "SELECT country, COUNT(DISTINCT visitorid) AS visitors FROM {$this->profile->tablename} AS a, {$this->profile->tablename_urls} AS u, {$this->profile->tablename_referrers} AS r, {$this->profile->tablename_keywords} AS k WHERE timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND a.url = u.id AND a.referrer = r.id AND a.keywords = k.id AND crawl = 0 AND status = 200 AND {$search_query} country != '' GROUP BY country ORDER BY visitors DESC LIMIT ". addslashes($this->limit);
			
			$query = subsetDataToSourceID($query);
			
			$q = $db->Execute($query);
			
			while ($topdata = $q->FetchRow()) {
				$wstring .= "country = '".$topdata["country"]."' OR ";
			}
			
			if ($wstring != "") {
				$wstring = " AND (".substr($wstring,0,-3).")";
			}
			
			$query = "SELECT {$qd} AS days, country, COUNT(DISTINCT visitorid) AS visitors FROM {$this->profile->tablename} AS a, {$this->profile->tablename_urls} AS u, {$this->profile->tablename_referrers} AS r, {$this->profile->tablename_keywords} AS k WHERE timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND a.url = u.id AND a.referrer = r.id AND a.keywords = k.id {$wstring} AND crawl = 0 AND {$search_query} status = 200 GROUP BY days, country ORDER BY country, timestamp ASC"; 
			
			$query = subsetDataToSourceID($query);
		} else {
			//get a top 10 of countries first
			$query = "SELECT country, COUNT(DISTINCT visitorid) AS visitors FROM {$this->profile->tablename} WHERE timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND crawl = 0 AND status = 200 AND country != '' GROUP BY country ORDER BY visitors DESC limit ". addslashes($this->limit);
			
			$query = subsetDataToSourceID($query);
			
			$q = $db->Execute($query);
			
			while ($topdata = $q->FetchRow()) {
				$wstring .= "country = '".$topdata["country"]."' OR ";
			}
			
			if ($wstring != "") {
				$wstring = " AND (".substr($wstring,0,-3).")";
			}
			
			$query = "SELECT {$qd} AS days, country, COUNT(DISTINCT visitorid) AS visitors FROM {$this->profile->tablename} WHERE timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." {$wstring} AND crawl = 0 AND status = 200 GROUP BY days, country ORDER BY country, timestamp ASC";

			$query = subsetDataToSourceID($query);
		}
		
		$this->applytrafficsource = false;
		
		return $query;
	}
	
	function ConvertData($data = "") {
		global $db, $qd, $cnames;
		
		if(empty($data)) {
			$data = $this->CreateData();
		}
		
		# transform the data array to a column for each serie
		$data = $this->seriesToColumns($data);
		
		if(!$data) {
			echoWarning(_NO_DATA_TO_DISPLAY, "margin: 5px;");
			die();
		}
		
		foreach($data['fields'] as $key => $countrycode) { // Convert country codes to countries
			if(isset($cnames[$countrycode])) {
				$countryname = $cnames[$countrycode];
				$data['fields'][$key] = "<a class=\"open_in_new_dialog quickopen\" options=\"".json_encode($countrycode)."\" href=\"reports.php?labels=_TOP_CITIES&country={$countrycode}&from={$this->from}&to={$this->to}\" rel=\"TopCities\" type=\"_TOP_CITIES\" name=\""._TOP_CITIES."\">".str_replace(",","&#44;",$countryname)."</a>";
			}
		}
		
		$this->showfields = implode(",", $data['fields']);
		
		# create an empty seed array with the right dimensions
		$ncols = count($data['fields']);
		$nrows = $this->dateNumber($this->from, $this->to, $this->period);
		
		$seed_data = $this->newReportArray($nrows, $ncols);
		
		# merge the actual results with the seed_data array
		foreach ($data['data'] as $key => $val) {
			
			$day_id = $this->dateNumber($this->from,strtotime($key),$this->period);
			
			for ($i=0;$i<$ncols;$i++) {
				if (isset($val[$i])) {
					$seed_data[$day_id][$i] = $val[$i];
				}
			}
		}	
		ksort($seed_data);
		
		# now make sure the date column is properly filled
		foreach($seed_data as $key => $value) {
			$value[0] = $this->getFormatDate($this->period,($this->from+(($key)*$this->getSeconds($this->period))));
			$seed_data[$key] = $value;
		}
		
		return $seed_data;
	}
	
	function DisplayReport($hide_header = false) {
		global $db, $qd, $cnames, $gi;
		
		if(!empty($this->source)) {
			$this->addlabel = "Search for {$this->sourcetype}: {$this->source}";
		}
		
		if (!isset($gi)) {
			// there is no data
			echo "<div class=\"lines\" id=\"countgraph\">"; 
			echoWarning(_NEED_GEO,"margin:10px;");
			echo "</div>";
		} else {
			# get the data from the database
			$data = $this->CreateData();
			
			$data = $this->ConvertData($data);
			
			if($this->displaymode == 'table') {
				$this->Table($data);
			} elseif($this->displaymode == 'linechart') {
				if($hide_header != true) {
					$this->ReportHeader();
				}
				echo "<h2 class='graph_title'>"._TOP." {$this->limit} "._COUNTRIES_OVER_TIME." ({$this->period})</h2>";
				$this->Graph($data, "line", '', 0, 300, array(
					"axes" => array(
						"yaxis" => array(
							"showLabel" => true,
							"label" => _UNIQUE_VISITORS,
							"rotate" => 90
						)
					)
				));
			} elseif($this->displaymode == 'areachart') {
				if($hide_header != true) {
					$this->ReportHeader();
				}
				echo "<h2 class='graph_title'>"._TOP." {$this->limit} "._COUNTRIES_OVER_TIME." ({$this->period})</h2>";
				$this->Graph($data, "area", '', 0, 300, array(
					"axes" => array(
						"yaxis" => array(
							"showLabel" => true,
							"label" => _UNIQUE_VISITORS,
							"rotate" => 90
						)
					)
				));
			}
		}
	}
	
	function DisplayCustomForm() {
		echo "<label for='sourcetype'>"._SEARCH."</label>";
		echo "<select class='report_option_field' id='sourcetype'>";
			echo "<option value=\"page\" "; if (@$this->sourcetype == "page") { echo "selected=\"selected\""; } echo ">"._PAGE.": </option>";
			echo "<option value=\"keyword\" "; if (@$this->sourcetype == "keyword") { echo "selected=\"selected\""; } echo ">"._KEYWORD.": </option>";
			echo "<option value=\"referrer\" ";     if (@$this->sourcetype == "referrer") { echo "selected=\"selected\""; } echo ">"._REFERRER.": </option>";
		echo "</select>";
		
		echo "<div>";
			echo "<input class='report_option_field' type=\"text\" name=\"source\" id=\"source\" value=\"".@$this->source."\" onkeyup=\"popupActionMenu(event, this.value+'@'+this.id+'@'+$('#sourcetype').val(), 'forminput');\" onclick=\"popupMenu(event, this.value+'@'+this.id+'@'+$('#sourcetype').val(), 'forminput');\" autocomplete=\"off\">";
		echo "</div>";
	}
}
?>