<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays the browser trends
*/

$reports["_BROWSER_TRENDS"] = Array(
	"ClassName" => "BrowserTrends", 
	"Category" => "_CLIENT_SYSTEM", 
	"icon" => "images/icons/32x32/clientbrowsertrends.png",
	"Options" => "daterangeField,trafficsource,period,displaymode,limit",
	"Filename" => "browser_trends",
	"Distribution" => "Standard",
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"Order" => 3,
	"EmailAlerts" => false
);

class BrowserTrends extends Report {
	
	function Settings() {
		$this->DefaultDisplay = "linechart";
		$this->DisplayModes = "table,linechart,areachart";
		$this->help = "";
		$this->displayReportButtons = false;
	}
	
	function DefineQuery() {
		global $db;
		
		if ($this->period == _DAYS) {
			$qd = "FROM_UNIXTIME(timestamp,'%a, %m/%d/%Y')";
		} else if ($this->period == _WEEKS) {
			$this->allowDateFormat = false;
			$qd = "FROM_UNIXTIME(timestamp,'%Y-W%V')";
		} else if ($this->period == _MONTHS) {
			$this->allowDateFormat = false;
			$qd = "FROM_UNIXTIME(timestamp,'%b %Y')";
		}
		
		$query = "SELECT ua.name AS useragent, COUNT(DISTINCT a.visitorid) AS visitors FROM {$this->profile->tablename} AS a, {$this->profile->tablename_useragents} AS ua WHERE a.timestamp BETWEEN " .$db->quote($this->from) ." AND ". $db->quote($this->to) ." AND a.status = 200 AND a.crawl = 0 AND a.useragentid = ua.id AND ua.is_mobile = 0 GROUP BY ua.name ORDER BY visitors DESC LIMIT {$this->limit}";
		
		$query = subsetDataToSourceID($query);
		
		$q = $db->Execute($query);
		
		$wstring = "";
		while ($topdata = $q->FetchRow()) {
			$wstring .= "ua.name = '".$topdata["useragent"]."' OR ";
		}
		$wstring = substr($wstring,0,-3);
		
		if (!empty($wstring)) {
			# empty: variable is null, or has no data
			$wstring = " AND ({$wstring}) ";
		}
		
        $query = "SELECT {$qd} AS days, ua.name AS useragent, COUNT(distinct a.visitorid) AS visitors FROM {$this->profile->tablename} as a, {$this->profile->tablename_useragents} AS ua WHERE a.timestamp BETWEEN {$this->from} AND {$this->to} {$wstring} AND a.status = 200 AND a.useragentid = ua.id AND a.crawl = 0 AND ua.is_mobile = 0 GROUP BY ua.name, days ORDER BY ua.name ASC, visitors DESC, a.timestamp ASC";
		
		$query = subsetDataToSourceID($query);
		
		$this->applytrafficsource = false;
		
		return $query;
	}
	
	function ConvertData($data = "") {
		if(empty($data)) { $data = $this->CreateData(); }
		# transform the data array to a column for each serie
		$data = $this->seriesToColumns($data);
		if(!$data){
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
		global $db, $qd;
		
		if(empty($this->displaymode)) { $this->displaymode = $this->DefaultDisplay; }
		
		# get the data from the database
		$data = $this->CreateData();
		
		$data = $this->ConvertData($data);
		
		if($this->displaymode == 'table') {
			$this->Table($data);
		} elseif($this->displaymode == 'areachart') {
			$this->ReportHeader();
			echo "<h2 class='graph_title'>" . _BROWSER_USAGE . "({$this->period})</h2>";
			$this->Graph($data, "area", '', 0, 300, array(
				"axes" => array(
					"yaxis" => array(
						"showLabel" => true,
						"label" => _UNIQUE_VISITORS,
						"rotate" => 90
					)
				)
			));
		} elseif($this->displaymode == 'linechart') {
			$this->ReportHeader();
			echo "<h2 class='graph_title'>" . _BROWSER_USAGE . "({$this->period})</h2>";
			$this->Graph($data, "line", '', 0, 300, array(
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
?>