<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays the error trends
*/
$reports["_ERROR_TRENDS"] = Array(
	"ClassName" => "ErrorTrends", 
	"Category" => "_POPULAR_CONTENT", 
	"icon" => "images/icons/32x32/errortrends.png",
	"Options" => "daterangeField,trafficsource,period,displaymode,limit",
	"Filename" => "error_trends",
	"Distribution" => "Standard",
	"Order" => 9,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => false
);

class ErrorTrends extends Report {
	
	function Settings() {
		$this->DefaultDisplay = "linechart";
		$this->DisplayModes = "table,linechart,areachart";
		$this->help = "";
		$this->displayReportButtons = false;
	}
	
	function DefineQuery() {
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
		
		$query = "SELECT {$qd} AS days, CONCAT(t.status, \" - \", p.descr) AS ecode, t.status AS status, COUNT(t.visitorid) AS requests FROM {$this->profile->tablename} AS t, ".TBL_LGSTATUS." AS p WHERE t.timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND t.status != 200 AND t.status = p.code GROUP BY t.status, days ORDER BY t.status, t.timestamp ASC";
		
		subsetDataToSourceID($query);
		
		return $query;
	}
	
	function ConvertData($data = "") {
		global $db;
		if(empty($data)) { $data = $this->CreateData(); }
		# transform the data array to a column for each serie
		$data = $this->seriesToColumns($data);
		
		if(!$data){
			$this->ReportHeader();
			echoWarning(_NO_DATA_TO_DISPLAY, "margin:5px;");
			die();
		}
		
		foreach($data['fields'] as $key => $val) { // Add link to error report
			$data['fields'][$key] = "<td><a class=\"small open_in_new_dialog quickopen\" name=\"{$val} "._ERROR_REPORT."\" type=\"_ERROR_REPORT\" rel=\"ErrorReport\" href=\"reports.php?labels=_ERROR_REPORT&amp;status=".urlencode($val)."&amp;from=". $db->quote($this->from) ."&amp;to=". $db->quote($this->to) ."&amp;conf={$this->profile->profilename}&amp;status={$val}\">{$val}</a></td>";
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
		
		# get the data from the database
		$data = $this->CreateData();
		
		$data = $this->ConvertData($data);
		
		if($this->displaymode == 'table') {
			$this->Table($data);
		} elseif($this->displaymode == 'areachart') {
			$this->ReportHeader();
			echo "<h2 class='graph_title'>"._ERROR_CODES_OVER_TIME." ({$this->period})</h2>";
			$this->Graph($data, "area", '', 0, 300, array(
				"axes" => array(
					"yaxis" => array(
						"showLabel" => true,
						"label" => _VISITORS,
						"rotate" => 90
					)
				)
			));
		} elseif($this->displaymode == 'linechart') {
			$this->ReportHeader();
			echo "<h2 class='graph_title'>"._ERROR_CODES_OVER_TIME." ({$this->period})</h2>";
			$this->Graph($data, "line", '', 0, 300, array(
				"axes" => array(
					"yaxis" => array(
						"showLabel" => true,
						"label" => _VISITORS,
						"rotate" => 90
					)
				)
			));
		}
	}
}
?>