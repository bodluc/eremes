<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays the browser breakdown
*/

if(basename($_SERVER['PHP_SELF']) != "trends.php") {
	// include_once("charts/includes/FusionCharts.php");
}

$reports["_BROWSER_BREAKDOWN"] = Array(
	"ClassName" => "BrowserBreakdown", 
	"Category" => "_CLIENT_SYSTEM", 
	"icon" => "images/icons/32x32/browser_breakdown.png",
	"Options" => "daterangeField,limit,trafficsource",
	"Filename" => "browser_breakdown",
	"Distribution" => "Standard",
	"Order" => 1,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => false
);

class BrowserBreakdown extends Report {
	
	function Settings() {
		$this->help = "";
	}
	
	function DisplayReport() {
		global $db;
		$this->displayReportButtons = false;
		$limit = $this->limit;
		
		$this->ReportHeader(true);
		
		$this->current_plotting_graph = 1;
		echo "<div style='width: 33%; float: left;'>";
		$this->ExplorerPie();
		echo "</div>";
		
		$this->current_plotting_graph = 2;
		echo "<div style='width: 33%; float: left;'>";
		$this->FirefoxPie();
		echo "</div>";
		
		$this->current_plotting_graph = 3;
		echo "<div style='width: 33%; float: left;'>";
		$this->OtherBrowsersPie();
		echo "</div>";
	}
	
	function ExplorerPie() {
		global $db;
		
		$color = array
		(
			"00DD22",
			"FDFD66",
			"C84239",
			"FCD381",
			"F971F7",
			"3C1F6E",
			"2E880A",
			"DD1840",
			"6DF9FB",
			"FCDA00" 
		);

// $query="select AGENTS.name useragent, count(distinct visitorid) as visitors from
// {$this->profile->tablename} left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >= {$this->from} and timestamp <= {$this->to} and status=200 and crawl=0 and (AGENTS.name like \"Internet Explorer%\") group by useragentid order by visitors desc limit {$this->limit}";

        $query = "SELECT concat(ua.name, ' ', ua.version) AS useragent, COUNT(distinct a.visitorid) AS visitors FROM {$this->profile->tablename} AS a INNER JOIN {$this->profile->tablename_useragents} as ua ON a.useragentid = ua.id WHERE a.timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND a.status = 200 AND a.crawl = 0 AND ua.is_mobile = 0 AND (ua.name LIKE \"%Internet Explorer%\") GROUP BY ua.name, ua.version ORDER BY visitors DESC LIMIT ".addslashes($this->limit);	

		$query = subsetDataToSourceID($query);
		
		$piedata = array();
		$i = 1;
		$q = $db->Execute($query);
		while ($data = $q->FetchRow()) {
			$status = $data["useragent"];
			$piedata[$i][0] = $status;
			$piedata[$i][1] = $data["visitors"];
			$i++;
		}
		
		echo "<h2 class='graph_title'>"._TOP." {$this->limit} "._IE_CLIENTS."</h2>";
		$this->PieChart($piedata, 'south');
	}
	
	function FirefoxPie() {
		global $db;
		
		$color = array
		(
			"00DD22",
			"FDFD66",
			"C84239",
			"FCD381",
			"F971F7",
			"3C1F6E",
			"2E880A",
			"DD1840",
			"6DF9FB",
			"FCDA00" 
		);

		// $query = "select AGENTS.name useragent, count(distinct visitorid) as visitors from
		// {$this->profile->tablename} left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >= {$this->from} and timestamp <= {$this->to} and status=200 and crawl=0 and (AGENTS.name like \"Firefox%\") group by useragentid order by visitors desc limit {$this->limit}";
		$query = "SELECT concat(ua.name, ' ', ua.version) AS useragent, COUNT(distinct a.visitorid) AS visitors FROM {$this->profile->tablename} AS a INNER JOIN {$this->profile->tablename_useragents} as ua ON a.useragentid = ua.id WHERE a.timestamp BETWEEN ". $db->quote($this->from) ." AND ". $db->quote($this->to) ." AND a.status = 200 AND a.crawl = 0 AND ua.is_mobile = 0 AND (ua.name LIKE \"%Firefox%\") GROUP BY ua.name, ua.version ORDER BY visitors DESC LIMIT ".addslashes($this->limit);
		$query = subsetDataToSourceID($query);
		$crchart[ 0 ][ 0 ] = "";
		$crchart[ 1 ][ 0 ] = "Visitors";
		$i=1;
		$q=$db->Execute($query);
		$piedata = array();
		while ($data = $q->FetchRow()) {
			$status = $data["useragent"];
			$piedata[$i][0] = $status;
			$piedata[$i][1] = $data["visitors"];
			$i++;
		}
		
		echo "<h2 class='graph_title'>"._TOP." {$this->limit} "._FF_CLIENTS."</h2>";
		$this->PieChart($piedata, 'south');
	}
	
	function OtherBrowsersPie() {
		global $db;
		
		$color = array
		(
			"00DD22",
			"FDFD66",
			"C84239",
			"FCD381",
			"F971F7",
			"3C1F6E",
			"2E880A",
			"DD1840",
			"6DF9FB",
			"FCDA00"
		);

		// $query = "select AGENTS.name useragent, count(distinct visitorid) as visitors from {$this->profile->tablename} left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >= {$this->from} and timestamp <= {$this->to} and status=200 and crawl=0 and (AGENTS.name not like \"Firefox%\") and (AGENTS.name not like \"Internet Explorer%\") and (AGENTS.name != \"-\") group by useragentid order by visitors desc limit {$this->limit}";
		$query = "SELECT concat(ua.name, ' ', ua.version) AS useragent, COUNT(distinct a.visitorid) AS visitors FROM {$this->profile->tablename} AS a INNER JOIN {$this->profile->tablename_useragents} as ua ON a.useragentid = ua.id WHERE a.timestamp >= ". $db->quote($this->from) ." AND a.timestamp <=". $db->quote($this->to) ." AND a.status = 200 AND a.crawl = 0 AND ua.is_mobile = 0 AND (ua.name NOT LIKE \"%Internet Explorer%\") AND (ua.name NOT LIKE \"%Firefox%\") GROUP BY ua.name, ua.version ORDER BY visitors DESC LIMIT ".addslashes($this->limit);

		$crchart[ 0 ][ 0 ] = "";
		$crchart[ 1 ][ 0 ] = "Visitors";
		$i=1;
		
		$query = subsetDataToSourceID($query);
		
		$q=$db->Execute($query);
		$piedata = array();
		while ($data = $q->FetchRow()) {
			$status = $data["useragent"];
			$piedata[$i][0] = $status;
			$piedata[$i][1] = $data["visitors"];
			$i++;
		}
		
		echo "<h2 class='graph_title'>"._TOP." {$this->limit} "._OTHER_CLIENTS."</h2>";
		$this->PieChart($piedata, 'south');
	}
}
?>