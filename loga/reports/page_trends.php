<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays a overview of referrrer conversion.
*/

$reports["_PAGE_TRENDS"] = Array(
	"ClassName" => "PageTrends", 
	"Category" => "_POPULAR_CONTENT", 
	"icon" => "images/icons/32x32/page_trends.png",
	"Options" => "daterangeField,trafficsource,search,period,displaymode,limit",
	"Filename" => "page_trends",
	"Distribution" => "Standard",
	"Order" => 3,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => false
);

class PageTrends extends Report {

	function Settings() {
		$this->DefaultDisplay = "linechart";
		$this->DisplayModes = "linechart,table";
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
				$search_query = "u.url NOT LIKE '%{$this->search}%' AND ";
			} else {
				$search_query = "u.url LIKE '%{$this->search}%' AND ";
			}
		} else {
			$search_query = "";
		}
		
		# Get a top 10 of pages first
		if($this->period == _DAYS && empty($this->trafficsource)) {
			$query = "SELECT u.url AS url, SUM(visitors) AS visitor FROM {$this->profile->tablename_dailyurls} AS a, {$this->profile->tablename_urls} AS u WHERE timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND {$search_query} a.url = u.id GROUP BY u.url ORDER BY visitor DESC LIMIT ". addslashes($this->limit);
		} else {
			$query = "SELECT u.url AS url, COUNT(DISTINCT visitorid) AS visitor FROM {$this->profile->tablename} AS a, {$this->profile->tablename_urls} AS u WHERE timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND {$search_query} a.url = u.id  GROUP BY u.url  ORDER BY visitor DESC LIMIT ". addslashes($this->limit);
		}
		
		subsetDataToSourceID($query);
		
		$q = $db->Execute($query);
		
		$wstring = "";
		while ($topdata = $q->FetchRow()) {
			$wstring .= "u.url = '".$topdata["url"]."' OR ";
		}
		
		# Now, get the data of the top 10 pages
		if ($wstring != "") {
			$wstring = " AND (".substr($wstring,0,-3).")";
			if($this->period == _DAYS && empty($this->trafficsource)) {
				$query = "SELECT {$qd} AS days, u.url as url, SUM(visitors) AS visitor FROM {$this->profile->tablename_dailyurls} AS a, {$this->profile->tablename_urls} AS u WHERE timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND {$search_query} a.url = u.id {$wstring} GROUP BY days, u.url ORDER BY a.url, timestamp ASC";
			} else {
				$query = "SELECT {$qd} AS days, u.url AS url, COUNT(DISTINCT visitorid) AS visitor FROM {$this->profile->tablename} AS a, {$this->profile->tablename_urls} AS u WHERE timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND {$search_query} a.url = u.id {$wstring} GROUP BY days, u.url ORDER BY u.url, timestamp asc";
			}
		} else {
			return false;
		}
		
		subsetDataToSourceID($query);
		
		return $query;
	}
	
	function ConvertData($data = "") {
		# transform the data array to a column for each serie
		$data = $this->seriesToColumns($data);
		
		if(!$data){
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
		
		if($this->displaymode == 'table') {
			$this->Table($data);
		} elseif($this->displaymode == 'linechart') {
			if($hide_header != true) {
				$this->ReportHeader();
			}
			
			echo "<h2 class='graph_title'>"._TOP." {$this->limit} "._PAGES."</h2>";
			$this->Graph($data, "line", 'referrer', 0, 300, array(
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