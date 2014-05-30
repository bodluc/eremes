<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays a overview of your performance trends
*/
$reports["_PERFORMANCE_TRENDS"] = Array(
	"ClassName" => "PerformanceTrends", 
	"Category" => "_PERFORMANCE", 
	"icon" => "images/icons/32x32/performancetrends.png",
	"Options" => "daterangeField,trafficsource,period,displaymode,limit",
	"Filename" => "performance_trends",
	"Distribution" => "Standard",
	"Order" => 1,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => false
);

class PerformanceTrends extends Report {

	function Settings() {
		$this->DefaultDisplay = "linechart";
		$this->DisplayModes = "table,linechart";
		$this->help = "";
		$this->displayReportButtons = false;
		$this->actionmenu_type = 'page';
		
		if(empty($this->conversion_mode)) {
			$this->conversion_mode = _CONVERSION_RATE;
		}
	}
	
	function DefineQuery() {
		global $qd,$db;
		
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
		$query = "SELECT {$qd} AS timeunit, COUNT(DISTINCT visitorid) AS uvisitors FROM {$this->profile->tablename} WHERE timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." GROUP BY timeunit ORDER BY timestamp";
		
		return $query;
	}
	
	function ConvertData($data = "") {
		global $db;
		
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
		
		$convertdata = array();
		$query2 = "SELECT {$qd} AS timeunit, u.url AS targetfile, COUNT(DISTINCT visitorid) AS users FROM {$this->profile->tablename_conversions} AS c, {$this->profile->tablename_urls} AS u WHERE timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND c.url = u.id AND {$this->profile->targets_sql} GROUP BY targetfile, timeunit ORDER BY targetfile, timestamp";
		
		$q = $db->Execute($query2);
		while($row = $q->FetchRow()) {
			$convertdata[] = $row;
		}
		
		$newdata = array();
		foreach($convertdata as $ck => $cv) {
			foreach($data as $k => $v) {
				$newdata[$ck][0] = $cv[0]; # Date
				$newdata[$ck][1] = $cv[1]; # URL
				
				if($this->conversion_mode == _CONVERTED_VISITORS) {
					$newdata[$ck][4] = $cv[2]; # Converted Visitors
					if($v[0] == $cv[0]) {
						$newdata[$ck][3] = $v[1]; # Total Visitors
						$newdata[$ck][2] = $cv[2] / $v[1] * 100; # Conversion Rate
					}
				} else {
					$newdata[$ck][2] = $cv[2]; # Conversion Rate
					if($v[0] == $cv[0]) {
						$newdata[$ck][3] = $v[1]; # Total Visitors
						$newdata[$ck][4] = $cv[2] / $v[1] * 100; # Converted Visitors
					}
				}
			}
			
			ksort($newdata[$ck]);
		}
		
		$data = $newdata;
		
		# transform the data array to a column for each serie
		$data = $this->seriesToColumns($data);
		
		if(!$data){
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
		global $db;
		
		# get the data from the database
		$data = $this->CreateData();
		
		$data = $this->ConvertData($data);
		
		if($this->displaymode == 'table') {
			$this->Table($data);
		} elseif($this->displaymode == 'areachart') {
			$this->ReportHeader();
			echo "<h2 class='graph_title'>"._VISITORS_TO_TARGET_FILES."</h2>";
			$this->Graph($data, "area", 'page', 0, 300, array(
				"axes" => array(
					"yaxis" => array(
						"showLabel" => true,
						"label" => $this->conversion_mode,
						"rotate" => 90
					)
				)
			));
		} elseif($this->displaymode == 'linechart') {
			$this->ReportHeader();
			echo "<h2 class='graph_title'>"._VISITORS_TO_TARGET_FILES."</h2>";
			$this->Graph($data, "line", 'page', 0, 300, array(
				"axes" => array(
					"yaxis" => array(
						"showLabel" => true,
						"label" => $this->conversion_mode,
						"rotate" => 90
					)
				)
			));
		}
	}
	
	function DisplayCustomForm() {
		echo "<label for='conversion_mode'>Show conversion as</label>";
		echo "<select id='conversion_mode' name='conversion_mode'>";
			if($this->conversion_mode == _CONVERSION_RATE) {
				echo "<option selected value='"._CONVERSION_RATE."'>"._CONVERSION_RATE."</option>";
			} else {
				echo "<option value='"._CONVERSION_RATE."'>"._CONVERSION_RATE."</option>";
			}
			
			if($this->conversion_mode == _CONVERTED_VISITORS) {
				echo "<option selected value='"._CONVERTED_VISITORS."'>"._CONVERTED_VISITORS."</option>";
			} else {
				echo "<option value='"._CONVERTED_VISITORS."'>"._CONVERTED_VISITORS."</option>";
			}
		echo "</select>";
	}
}
?>