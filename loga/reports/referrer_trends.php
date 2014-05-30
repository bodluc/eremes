<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays a overview of referrrer conversion.
*/

$reports["_REFERRER_TRENDS"] = Array(
	"ClassName" => "ReferrerTrends", 
	"Category" => "_INCOMING_TRAFFIC", 
	"icon" => "images/icons/32x32/topreferrerstrends.png",
	"Options" => "daterangeField,trafficsource,search,period,limit,displaymode",
	"Filename" => "referrer_trends",
	"Distribution" => "Standard",
	"Order" => 4,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => false
);

class ReferrerTrends extends Report {

	function Settings() {
		$this->DefaultDisplay = "areachart";
		$this->DisplayModes = "areachart,linechart,table";
		$this->help = "";
		$this->displayReportButtons = false;
		$this->allowDateFormat = false;
	}
	
	function DefineQuery() {
		global $db, $qd;
		
		# Define the chosen period / time unit
		if ($this->period == _DAYS) { 
			$qd = "FROM_UNIXTIME(timestamp,'%a, %m/%d/%Y')";
		} else if ($this->period == _WEEKS) {
			$qd = "FROM_UNIXTIME(timestamp,'%x-W%v')";
			$this->allowDateFormat = false;
		} else if ($this->period == _MONTHS) {
			$qd = "FROM_UNIXTIME(timestamp,'%b %Y')";
			$this->allowDateFormat = false;
		}
		
		if(!empty($this->search)) {
			if($this->searchmode == 'not like') {
				$search_query = "r.referrer NOT LIKE '%{$this->search}%' AND ";
			} else {
				$search_query = "r.referrer LIKE '%{$this->search}%' AND ";
			}
		} else {
			$search_query = "";
		}
		
		# We might want to include Local Referrers
		if(!empty($this->includeLocalReferrers)) {
			$include_confdomain = "";
		} else {
			$include_confdomain = " AND r.referrer NOT LIKE 'http://{$this->profile->confdomain}/%' ";
		}
		
		# Get a top 10 of referrers first
		if($this->period == _DAYS && empty($this->trafficsource)) {
			$query = "SELECT r.referrer AS referrer, SUM(visitors) AS visitor FROM {$this->profile->tablename_dailyurls} AS a, {$this->profile->tablename_referrers} AS r, {$this->profile->tablename_urls} AS u WHERE timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND a.referrer = r.id AND {$search_query} a.url = u.id {$include_confdomain} AND r.referrer != '-' GROUP BY r.referrer ORDER BY visitor DESC LIMIT ". addslashes($this->limit);
		} else {
			$query = "SELECT r.referrer AS referrer, COUNT(DISTINCT visitorid) AS visitor FROM {$this->profile->tablename} AS a, {$this->profile->tablename_referrers} AS r, {$this->profile->tablename_urls} AS u WHERE timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND a.referrer = r.id AND {$search_query} a.url = u.id {$include_confdomain} AND r.referrer != '-' GROUP BY r.referrer ORDER BY visitor DESC LIMIT ". addslashes($this->limit);
		}
		
		$query = subsetDataToSourceID($query);
		
		$q = $db->Execute($query);
		
		$wstring = "";
		while ($topdata = $q->FetchRow()) {
			$wstring .= "r.referrer = '".$topdata["referrer"]."' OR ";
		}
		
		# Now, get the data of the top 10 referrers
		if ($wstring != "") {
			$wstring = " AND (".substr($wstring,0,-3).")";
			if($this->period == _DAYS && empty($this->trafficsource)) {
				$query = "SELECT {$qd} AS days, r.referrer AS referrer, SUM(visitors) AS visitor FROM {$this->profile->tablename_dailyurls} AS a, {$this->profile->tablename_referrers} AS r, {$this->profile->tablename_urls} AS u WHERE timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND a.referrer = r.id {$wstring} AND r.referrer != '-' AND {$search_query} a.url = u.id GROUP BY days, r.referrer ORDER BY r.referrer, timestamp ASC";
			} else {
				$query = "SELECT {$qd} AS days, r.referrer AS referrer, COUNT(DISTINCT visitorid) AS visitor FROM {$this->profile->tablename} AS a, {$this->profile->tablename_referrers} AS r, {$this->profile->tablename_urls} AS u WHERE timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND a.referrer = r.id {$wstring} AND r.referrer != '-' AND {$search_query} a.url = u.id GROUP BY days, r.referrer ORDER BY r.referrer, timestamp ASC";
			}
			
			$query = subsetDataToSourceID($query);
		} else {
			return false;
		}
		
		return $query;
	}
	
	function ConvertData($data = "") {
		# transform the data array to a column for each serie
		$data = $this->seriesToColumns($data);
		
		if(empty($data)) {
			echoWarning(_NO_DATA_TO_DISPLAY, "margin:5px;");
			die();
		}
		
		$this->showfields = implode(",", $data['fields']);
		
		# create an empty seed array with the right dimensions
		$ncols = count($data['fields']);
		$nrows = $this->dateNumber($this->from, $this->to, $this->period);
		
		$seed_data = $this->newReportArray($nrows, $ncols);
		
		# merge the actual results with the seed_data array
		foreach ($data['data'] as $key => $val) {
			
			$day_id = $this->dateNumber($this->from, strtotime($key), $this->period);
			
			for ($i = 0; $i < $ncols; $i++) {
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
		global $db, $qd;
		
		# get the data from the database
		$data = $this->CreateData();
		
		$data = $this->ConvertData($data);
		
		if(!empty($this->source)) { $this->addlabel = "Search for {$this->sourcetype}: {$this->source}"; }
		
		if($this->displaymode == 'table') {
			$this->Table($data);
		} elseif($this->displaymode == 'linechart') {
			if($hide_header != true) {
				$this->ReportHeader();
			}
			
			echo "<h2 class='graph_title'>"._TOP." {$this->limit} "._REFERRERS_OVER_TIME."</h2>";
			$this->Graph($data, "line", 'referrer', 0, 300, array(
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
			
			echo "<h2 class='graph_title'>"._TOP." {$this->limit} "._REFERRERS_OVER_TIME."</h2>";
			$this->Graph($data, "area", 'referrer', 0, 300, array(
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
	
	function DisplayCustomForm() {
		@$this->source = urldecode($this->source);
		
		if(!empty($this->includeLocalReferrers)) {
			$checked = " checked ";
		} else {
			$checked = "";
		}
		
		echo "<input style='width: 25px; float: left;' {$checked} id='includeLocalReferrers' name='includeLocalReferrers' type='checkbox' value='1' />";
		echo "<label for='includeLocalReferrers'>Include Local Referrers</label>";
	}

}
?>