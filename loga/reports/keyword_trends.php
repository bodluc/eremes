<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc shows a trend of the most popular keyword 
*/

$reports["_KEYWORD_TRENDS"] = Array(
	"ClassName" => "KeywordTrends", 
	"Category" => "_INCOMING_TRAFFIC",
	"icon" => "images/icons/32x32/keywordtrends.png",
	"Options" => "daterangeField,trafficsource,search,period,displaymode,limit",
	"Filename" => "keyword_trends",
	"Distribution" => "Standard",
	"Order" => 7,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => false
);

class KeywordTrends extends Report {

	function Settings() {
		$this->DefaultDisplay = "linechart";
		$this->DisplayModes = "table,linechart,areachart";
		$this->help = "";
		$this->displayReportButtons = false;
		$this->actionmenu_type = 'keyword';
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
			$search_query = "k.keywords LIKE '%{$this->search}%' AND ";
		} else {
			$search_query = "";
		}
		
		# get a top 10 of keywords first
		$query = "SELECT k.keywords AS keywords, COUNT(DISTINCT visitorid) AS visitors FROM {$this->profile->tablename} AS a, {$this->profile->tablename_keywords} AS k WHERE timestamp BETWEEN {$this->from} AND {$this->to} AND a.keywords = k.id AND {$search_query}  k.keywords != '' AND crawl = 0 GROUP BY a.keywords ORDER BY visitors DESC LIMIT {$this->limit}";
		
		$query = subsetDataToSourceID($query);
		
		$q = $db->Execute($query);
		$wstring = "";
		
		while ($topdata = $q->FetchRow()) {
			$wstring .= "k.keywords = '".$topdata["keywords"]."' OR ";
		}
		
		$wstring = substr($wstring, 0, -3);

		if($wstring != "") {
			$query = "SELECT {$qd} AS days, k.keywords AS keywords, COUNT(DISTINCT visitorid) AS requests FROM {$this->profile->tablename} AS a, {$this->profile->tablename_keywords} AS k WHERE timestamp BETWEEN {$this->from} AND {$this->to} AND ({$wstring}) AND crawl = 0 AND a.keywords = k.id GROUP BY days, a.keywords ORDER BY k.keywords, timestamp ASC";
		}
		
		$query = subsetDataToSourceID($query);
		
		$this->applytrafficsource = false;
		
		return $query;
	}
	
	function ConvertData($data = "") {
		if(empty($data)) {
			$data = $this->CreateData();
		}
		
		# transform the data array to a column for each serie
		$data = $this->seriesToColumns($data);
		
		if(empty($data)) {
			$this->ReportHeader();
			echoWarning(_NO_DATA_TO_DISPLAY, "margin:5px;");
			die();
		}	
		
		foreach($data['fields'] as $key => $countrycode) { // Convert country codes to countries
			if(isset($cnames[$countrycode])) {
				$data['fields'][$key] = $cnames[$countrycode];
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
	
	function DisplayReport() {
		global $db;
		
		# get the data from the database
		$data = $this->CreateData();
		
		$data = $this->ConvertData($data);
		
		if($this->displaymode == 'table') {
			$this->Table($data);
		} elseif($this->displaymode == 'areachart') {
			$this->ReportHeader();
			echo "<h2 class='graph_title'>"._TOP." {$this->limit} "._KEYWORDS_OVER_TIME."</h2>";
			$this->Graph($data, "area", 'keyword', 0, 300, array(
				"axes" => array(
					"yaxis" => array(
						"showLabel" => true,
						"label" => "Visitors",
						"rotate" => 90
					)
				)
			));
		} elseif($this->displaymode == 'linechart') {
			$this->ReportHeader();
			echo "<h2 class='graph_title'>"._TOP." {$this->limit} "._KEYWORDS_OVER_TIME."</h2>";
			$this->Graph($data, "line", 'keyword', 0, 300, array(
				"axes" => array(
					"yaxis" => array(
						"showLabel" => true,
						"label" => "Visitors",
						"rotate" => 90
					)
				)
			));
		}
	}
}
?>