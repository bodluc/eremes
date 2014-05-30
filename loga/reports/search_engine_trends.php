<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays a trend of your search engines
*/
$reports["_SEARCH_ENGINE_TRENDS"] = Array(
	"ClassName" => "SearchEngineTrends", 
	"Category" => "_INCOMING_TRAFFIC", 
	"icon" => "images/icons/32x32/searchtrends.png",
	"Options" => "daterangeField,trafficsource,period,displaymode,limit",
	"Filename" => "search_engine_trends",
	"Distribution" => "Standard",
	"Order" => 13,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => false
);

class SearchEngineTrends extends Report {

	function Settings() {
		$this->DefaultDisplay = "linechart";
		$this->DisplayModes = "table,linechart,areachart";
		$this->help = "";
		$this->displayReportButtons = false;
	}
	
	function DefineQuery() {
		global $qd, $db;
		
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
		
		$query = subsetDataToSourceID("select {$qd} AS days, \"Google (Natural Search)\" referrer, count(distinct visitorid) as visitors from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r,{$this->profile->tablename_urlparams} as up where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." and (r.referrer like \"http://www.google.%\" and up.params NOT like \"?gclid=%\") and crawl=0 and a.referrer=r.id and a.params=up.id group by days union ", $this->trafficsource);
		
		$query .= subsetDataToSourceID("select {$qd} AS days, \"Google (Paid Search)\" referrer, count(distinct visitorid) as visitors from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r,{$this->profile->tablename_urlparams} as up where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." and r.referrer like \"http://www.google.%\" and up.params like \"?gclid=%\" and crawl=0 and a.referrer=r.id and a.params=up.id group by days union ", $this->trafficsource);
		
		$query .= subsetDataToSourceID("select {$qd} AS days, \"Yahoo\" referrer, count(distinct visitorid) as visitors from  {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." and r.referrer like \"%search.yahoo.%\" and crawl=0 and a.referrer=r.id group by days union ", $this->trafficsource);
		
		$query .= subsetDataToSourceID("select {$qd} AS days, \"Bing\" referrer, count(distinct visitorid) as visitors from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." and r.referrer = \"http://www.bing.com/search\" and crawl=0 and a.referrer=r.id group by days union ", $this->trafficsource);
		
		$query .= subsetDataToSourceID("select {$qd} AS days, \"AOL Search\" referrer, count(distinct visitorid) as visitors from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." and r.referrer like \"%search.aol.%\" and crawl=0 and a.referrer=r.id group by days union ", $this->trafficsource);
		
		$query .= subsetDataToSourceID("select {$qd} AS days, \"Ask.com\" referrer, count(distinct visitorid) as visitors from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." and r.referrer like \"%.ask.com%\" and crawl=0 and a.referrer=r.id group by days union ", $this->trafficsource);
		
		$query .= subsetDataToSourceID("select {$qd} AS days, \"Dogpile.com\" referrer, count(distinct visitorid) as visitors from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." and r.referrer like \"http://www.dogpile.com%\" and crawl=0 and a.referrer=r.id group by days union ", $this->trafficsource);
		
		$query .= subsetDataToSourceID("select {$qd} AS days, \"Others\" referrer, count(distinct visitorid) as visitors from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r,{$this->profile->tablename_keywords} as k where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." and a.keywords=k.id and k.keywords!='' and r.referrer not like \"http://www.google.%\" and r.referrer not like \"%search.yahoo.%\" and r.referrer!=\"http://www.bing.com/search\" and r.referrer not like \"%.search.aol.%\" and r.referrer not like \"%.ask.com%\" and r.referrer not like \"http://www.dogpile.com%\" and crawl=0 and a.referrer=r.id group by days ", $this->trafficsource);
		
		$query .= " order by referrer,days asc";
		
		$this->applytrafficsource = false;
		
		return $query;
	}
	
	function ConvertData($data = "") {
		if(empty($data)) { $data = $this->CreateData(); }
		
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
	
	function DisplayReport() {
		global $db, $qd;
		
		if ($this->period == _DAYS) {
			$qd = "FROM_UNIXTIME(timestamp, '%a, %m/%d/%Y')";
		} else if ($this->period == _WEEKS) {
			$qd = "FROM_UNIXTIME(timestamp, '%Y-W%V')";
		} else if ($this->period == _MONTHS) {
			$qd = "FROM_UNIXTIME(timestamp, '%b %Y')";
		}
		
		if(empty($this->displaymode)) { $this->displaymode = $this->DefaultDisplay; }
		
		# get the data from the database
		$data = $this->CreateData();
		
		$data = $this->ConvertData($data);
		
		if($this->displaymode == 'table') {
			$this->Table($data);
		} elseif($this->displaymode == 'areachart') {
			$this->ReportHeader();
			echo "<h2 class='graph_title'>"._TOP_REFERRING_SE."</h2>";
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
			echo "<h2 class='graph_title'>"._TOP_REFERRING_SE."</h2>";
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
	
	function CreateGraphData() {
		global $db, $qd;
		
		//get a top 10 of search engines first
		$query = ("select {$qd} AS days, \"Google (Natural Search)\" referrer, count(distinct visitorid) as visitors from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r,{$this->profile->tablename_urlparams} as up where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." and (r.referrer like \"http://www.google.%\" and up.params NOT like \"?gclid=%\") and crawl=0 and a.referrer=r.id and a.params=up.id group by days union ");

		$query .= ("select {$qd} AS days, \"Google (Paid Search)\" referrer, count(distinct visitorid) as visitors from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r,{$this->profile->tablename_urlparams} as up where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." and r.referrer like \"http://www.google.%\" and up.params like \"?gclid=%\" and crawl=0 and a.referrer=r.id and a.params=up.id group by days union ");

		$query .= ("select {$qd} AS days, \"Yahoo\" referrer, count(distinct visitorid) as visitors from  {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." and r.referrer like \"%search.yahoo.%\" and crawl=0 and a.referrer=r.id group by days union ");

		$query .= ("select {$qd} AS days, \"Bing\" referrer, count(distinct visitorid) as visitors from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." and r.referrer = \"http://www.bing.com/search\" and crawl=0 and a.referrer=r.id group by days union ");

		$query .= ("select {$qd} AS days, \"AOL Search\" referrer, count(distinct visitorid) as visitors from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." and r.referrer like \"%search.aol.%\" and crawl=0 and a.referrer=r.id group by days union ");

		$query .= ("select {$qd} AS days, \"Ask.com\" referrer, count(distinct visitorid) as visitors from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." and r.referrer like \"%.ask.com%\" and crawl=0 and a.referrer=r.id group by days union ");

		$query .= ("select {$qd} AS days, \"Dogpile.com\" referrer, count(distinct visitorid) as visitors from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." and r.referrer like \"http://www.dogpile.com%\" and crawl=0 and a.referrer=r.id group by days union ");

		$query .= ("select {$qd} AS days, \"Others\" referrer, count(distinct visitorid) as visitors from {$this->profile->tablename} as a, {$this->profile->tablename_referrers} as r,{$this->profile->tablename_keywords} as k where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." and a.keywords=k.id and k.keywords!='' and r.referrer not like \"http://www.google.%\" and r.referrer not like \"%search.yahoo.%\" and r.referrer!=\"http://www.bing.com/search\" and r.referrer not like \"%.search.aol.%\" and r.referrer not like \"%.ask.com%\" and r.referrer not like \"http://www.dogpile.com%\" and crawl=0 and a.referrer=r.id group by days ");

		$query .= " order by referrer,days asc";
		
		//set up a proper date series to prevent holes
		$n = ($this->to - $this->from) / 86400;
		if($this->period == _WEEKS) {
			$n = ceil(($this->to - $this->from) / 86400 / 7);
		} elseif($this->period == _MONTHS) {
			$n = ceil(($this->to - $this->from) / 86400 / 30);
		} else {
			$n = ceil(($this->to - $this->from) / 86400);
		}
		$i = 0;
		while ($i <= $n) {
			if($this->period == _DAYS) {
				$cdate=date("D, m/d/y", ($this->from + ($i * 86400)));
			}
			if($this->period == _WEEKS) {
				$fromWeek = strtotime("+$i week", $this->from);
				$cdate=date("Y-W", ($fromWeek));
			}
			if($this->period == _MONTHS) {
				$fromWeek = strtotime("+$i month", $this->from);
				$cdate=date("M y", ($fromWeek));
			}
			$days[$cdate] = $i;

			$dayArr[$i] = $cdate;
			$i++;
		}

		$q = $db->Execute($query);
		$i = 0;
		$range = 0;
		$laststatus = "";
		$thismax = 0;
		$chartlabel = array();
		$graphdata = array();
		while ($refdata=$q->FetchRow()) {
			if ($refdata["referrer"]!=$laststatus) {
				$chartlabel[] = $refdata["referrer"];
				if($laststatus != "") {
					$range++;
				}

				//prefill with 0 to avoid holes
				$pi=0;
				while ($pi <= $n) {
					$rchart[ $range ][ $pi ]=0;
					$pi++;
				}
				$laststatus = $refdata["referrer"];
			}
			// $rchart[ $range ][ ($days[$refdata["days"]]) ] = $refdata["visitors"];
			$graphdata[$i][0] = $refdata["days"];
			$graphdata[$i][1] = $refdata["referrer"];
			$graphdata[$i][2] = $refdata["visitors"];

			// $totalvisitors[($days[$refdata["days"]])] = @$totalvisitors[($days[$refdata["days"]])] + $refdata["visitors"];
			if ($thismax < $refdata["visitors"]) {
				$thismax = $refdata["visitors"];
			}
			$i++;
		}
		
		$this->tmp_fields = implode(",", $chartlabel);
		
		return $graphdata;
	}
}
?>