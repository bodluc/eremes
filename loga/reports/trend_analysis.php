<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays 3 graphs where you get an overview of the trends.
*/

$reports["_TREND_ANALYSIS"] = Array(
	"ClassName" => "TrendAnalysis", 
	"Category" => "_VISITOR_DETAILS", 
	"icon" => "images/icons/32x32/trendsoverview.png",
	"Options" => "daterangeField,trafficsource,source,period,displaymode",
	"Filename" => "trend_analysis",
	"Distribution" => "Standard",
	"Order" => 1,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => false
);

class TrendAnalysis extends Report {

	function Settings() {
		$this->DefaultDisplay = "barlinechart";
		$this->DisplayModes = "barlinechart,table";
		$this->help = "";
		$this->displayReportButtons = false;
		$this->allowDateFormat = false;
		$this->source = urldecode($this->source);
		$this->showfields = _DATE.","._VISITORS.","._VISITOR_TREND;
		
		if(!empty($this->source)) {
			$this->addlabel .= ucwords($this->sourcetype).": {$this->source}";
		}
	}
	
	function DefineQuery() {
		global $db, $sqlst, $qd;
		
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
		
		if (!empty($this->source)) {
			
			# get total unique visitors for time unit
			if ($this->sourcetype == "page") {
				$query = "SELECT {$qd} AS timeunit, COUNT(DISTINCT visitorid) AS uvisitors, COUNT(*) as hits FROM {$this->profile->tablename} WHERE timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND url='".getID($this->source,'urls')."' GROUP BY timeunit ORDER BY timestamp"; 
				$sqlst = "u.url";
			} else if ($this->sourcetype == "keyword") {
				$query = "SELECT {$qd} AS timeunit, COUNT(DISTINCT visitorid) AS uvisitors, COUNT(*) as hits FROM {$this->profile->tablename} AS a, {$this->profile->tablename_keywords} AS k WHERE timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND crawl=0 AND a.keywords=k.id AND k.keywords=".$db->quote($this->source)." GROUP BY timeunit ORDER BY timestamp";
				$sqlst = "k.keywords";
			} else if ($this->sourcetype == "referrer") {
				if(!empty($this->trafficsource)) {
					$query = "SELECT {$qd} AS timeunit, COUNT(DISTINCT visitorid) AS uvisitors, COUNT(*) as hits FROM {$this->profile->tablename} AS a, {$this->profile->tablename_referrers} AS r WHERE timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND a.referrer=r.id AND r.referrer=".$db->quote($this->source)." GROUP BY timeunit ORDER BY timestamp";
				} else {
					$query = "SELECT {$qd} AS timeunit, SUM(visitors) AS uvisitors, COUNT(*) as hits FROM {$this->profile->tablename_dailyurls} AS a,{$this->profile->tablename_referrers} AS r WHERE timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND a.referrer=r.id AND r.referrer=".$db->quote($this->source)." GROUP BY timeunit ORDER BY timestamp";
				}
				$sqlst = "r.referrer";
			}
		} else {
			# get total unique visitors for time unit
			if(!empty($this->trafficsource)) {
				$query = "SELECT {$qd} AS timeunit, COUNT(DISTINCT visitorid) AS uvisitors, COUNT(*) as hits FROM {$this->profile->tablename} WHERE timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND status = 200 AND crawl = 0 GROUP BY timeunit ORDER BY timestamp";
			} else {
				$query = "SELECT {$qd} AS timeunit, SUM(visitors) AS uvisitors, COUNT(*) as hits FROM {$this->profile->tablename_vpd} WHERE timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." GROUP BY timeunit ORDER BY timestamp";
			}
		}
		
		$query = subsetDataToSourceID($query);
		
		return $query;
	}
	
	function CreateData() {
		global $db;
		
		$q = $db->Execute($this->DefineQuery());
		
		if($this->period == _WEEKS) {
			$n = ceil(($this->to - $this->from) / 86400 / 7);
		} elseif($this->period == _MONTHS) {
			$n = ceil(($this->to - $this->from) / 86400 / 30);
		} else {
			$n = ceil(($this->to - $this->from) / 86400);
		}
		
		$i = 0;
		while ($i < $n) {
			if($this->period == _DAYS) {
				$cdate = date("D, m/d/Y", ($this->from + ($i * 86400)));
			}
			if($this->period == _WEEKS) {
				$fromWeek = strtotime("+$i week",$this->from);
				$cdate = date("o-\WW", ($fromWeek));
			}
			if($this->period == _MONTHS) {
				$fromWeek = strtotime("+$i month",$this->from);
				$cdate = date("M Y", ($fromWeek));
			}
			$merge[$cdate][1] = 0;  // unique visitors
			$i++;
		}
		
		while ($data = $q->FetchRow()) {
			$merge[$data["timeunit"]][1] = $data["uvisitors"];
		}
		
		return $merge;
	}
	
	function ConvertData($merge = array()) {
		$n = count(@$merge);
		
		if ($n == 0) {
			echo "&nbsp;<P><h3>"._NO_DATA_FOUND_FOR." <font color=\"red\">{$this->source}</font></h3>"._NO_DATA_FOUND_TIPS."</body></html>";
			exit();
		}
		
		reset($merge);
		$i = 1;
		
		while (list ($day, $row) = each ($merge)) {
			$traffic_x[$i] = $i; // trend line for total traffic

			$traffic_y[$i] = $row[1];

			//begin calculation
			$traffic_xy[$i] = $traffic_x[$i] * $row[1];

			$traffic_xsq[$i] = $traffic_x[$i] * $traffic_x[$i];

			$i++;
		}

		// $n has already been vetted to be <> 0, so we can divide by $n with impunity here
		$traffic_a_part=array_sum($traffic_xy)-((array_sum($traffic_x)*array_sum($traffic_y))/$n);

		// array_sum($x) isn't vetted, so we need to protect from a division by 0 here.
		$traffic_a=@($traffic_a_part/(array_sum($traffic_xsq)-(1/$n)*(array_sum($traffic_x)*array_sum($traffic_x))));

		$traffic_b=(array_sum($traffic_y)/$n)-($traffic_a * (array_sum($traffic_x)/$n));
		//now we have eveything to plot our 2 regression lines

		//add rolling average and plot point for trend
		reset($merge);
		$ndays = count($merge);
		$maxval = 0;
		$cstr = 0;
		
		$i=0;
		while (list ($day, $row) = each ($merge)) {
			while (list ($skey, $sval) = each ($row)) {
				switch ($skey) {
					case 1:
						$traffic_regval[$i]=($traffic_a * $traffic_x[($i+1)]) + $traffic_b;
					break;
				}
			}
			
			$i++;
		}
		
		$looptime = getmicrotime();
		
		// now merge it in the stats table format
		$i = 0;
		reset($merge);
		while (list ($key, $val) = each ($merge)) {
			$data[$i][0] = $key; //date
			while (list ($skey, $sval) = each ($val)) {
				$data[$i][$skey] = $sval;
			}
			$data[$i][2] = number_format($traffic_regval[$i], 0);
			$i++;
		}
		
		reset($merge);
		$i = 0;
		while (list ($day, $row) = each ($merge)) {
			if ($maxval < $row[1]) {
				$maxval = $row[1];
			}
			
			$graphdata[$i][0] = $day;
			$graphdata[$i][1] = $row[1];
			if($traffic_regval[($i)] <= 0) {
				$graphdata[$i][2] = 0;
			} else {
				$graphdata[$i][2] = round($traffic_regval[($i)], 2);
			}
			
			$i++;
		}
		
		return $graphdata;
	}
	
	function DisplayReport() {
		global $db, $sqlst, $qd, $n, $cdate, $cnames, $databasedriver;
		
		$data = $this->CreateData();
		
		$data = $this->ConvertData($data);
		
				
		if($this->displaymode == 'barlinechart') {
			$this->ReportHeader();
			
			echo "<h2 class='graph_title'>"._VISITORS."</h2>";
			$this->Graph($data, array('', 'bar', 'line'), '', 0, 300, array(
				"seriesDefaults" => array(
					"markerOptions" => array(
						"show" => false
					)
				),
				"axes" => array(
					"yaxis" => array(
						"showLabel" => true,
						"label" => "Unique Visitors",
						"rotate" => 90
					)
				)
			), 'south', 'inline');
			
			echo "<div style='margin-bottom: 40px;'></div>";
		} else {
			$this->Table($data);
		}
	}
	
	function DisplaySparkline() {
        $data = $this->CreateData();
		$data = $this->ConvertData($data);
        $this->Sparkline($data);

    }
	
}
?>