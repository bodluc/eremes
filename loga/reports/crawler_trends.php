<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays the crawler trends
*/

$reports["_CRAWLER_TRENDS"] = Array(
	"ClassName" => "CrawlerTrends", 
	"Category" => "_TRAFFIC", 
	"icon" => "images/icons/32x32/crawlertrends.png",
	"Options" => "daterangeField,trafficsource,period,displaymode,limit",
	"Filename" => "crawler_trends",
	"Distribution" => "Standard",
	"Order" => 7,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => false
);

class CrawlerTrends extends Report {
	
	function Settings() {
		$this->DefaultDisplay = "linechart";
		$this->DisplayModes = "table,linechart,areachart";
		$this->help = "";
		$this->displayReportButtons = false;
	}
	
	function DefineQuery() {
		global $db, $qd;
		
		//get a top 10 of crawlers first
		$query = "SELECT ua.name AS useragent, COUNT(distinct a.visitorid) AS visitors FROM {$this->profile->tablename} AS a, {$this->profile->tablename_useragents} AS ua WHERE a.timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND a.useragentid = ua.id AND a.crawl = 1 AND ua.is_mobile = 0 GROUP BY ua.name ORDER BY visitors DESC LIMIT ". addslashes($this->limit);
		
		$query = subsetDataToSourceID($query);
		
		$q = $db->Execute($query);
		
		$wstring = "";
		while ($topdata = $q->FetchRow()) {
			$wstring .= "ua.name='".$topdata["useragent"]."' or ";
		}
		
		$wstring = substr($wstring,0,-3);
		if ($wstring > "") {
			$wstring = "and ($wstring) ";
		}
		
        $query = "SELECT {$qd} AS days, ua.name AS useragent, COUNT(DISTINCT a.visitorid) AS visitors FROM {$this->profile->tablename} AS a, {$this->profile->tablename_useragents} AS ua WHERE a.timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." {$wstring} AND a.useragentid = ua.id AND a.crawl = 1 AND ua.is_mobile = 0 GROUP BY ua.name, days ORDER BY ua.name ASC, visitors DESC, a.timestamp ASC";
		
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
		
		# get the data from the database
		$data = $this->CreateData();
		
		$data = $this->ConvertData($data);
		
		if($this->displaymode == 'table') {
			$this->Table($data);
		} elseif($this->displaymode == 'areachart') {
			$this->ReportHeader();
			echo "<h2 class='graph_title'>"._TOP." {$this->limit} "._CRAWLERS_OVER_TIME." ({$this->period})</h2>";
			$this->Graph($data, "area", '', 0, 300, array(
				"axes" => array(
					"yaxis" => array(
						"showLabel" => true,
						"label" => "Visits",
						"rotate" => 90
					)
				)
			));
		} elseif($this->displaymode == 'linechart') {
			$this->ReportHeader();
			echo "<h2 class='graph_title'>"._TOP." {$this->limit} "._CRAWLERS_OVER_TIME." ({$this->period})</h2>";
			$this->Graph($data, "line", '', 0, 300, array(
				"axes" => array(
					"yaxis" => array(
						"showLabel" => true,
						"label" => "Visits",
						"rotate" => 90
					)
				)
			));
		}
	}
}
?>