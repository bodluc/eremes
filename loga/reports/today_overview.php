<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays a overview of todays status
*/

$reports["_TODAY_BOX"] = Array(
	"ClassName" => "TodayOverview", 
	"Category" => "_VISITOR_DETAILS", 
	"icon" => "images/icons/32x32/todayoverview.png",
	"Options" => "daterangeField",
	"Filename" => "today_overview",
	"Distribution" => "Standard",
	"Order" => 2,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class TodayOverview extends Report {

	function Settings() {
		$this->help = "";
		$this->allowDateFormat = false;
	}
	
	function CreateData() {
		global $db;
		
		$data = array();
		$import = array();
		$today = $this->to;
		$tor = date("F Y",$this->to);
		$this->from = strtotime("01 ".$tor);
		$range = $today - $this->from;
		$prevTo = $today - $range - 1;
		$prevFrom = $this->from - $range;
		$p = ""; // Prev Range
		$t = ""; // This Range
		$prevFrom = mktime(0,0,0,date("m", $prevTo),01,date("Y", $prevTo));
		$t = date("F Y",$today);	
		$p = date("F Y",$prevTo);
		// Today
		$query = "select visitors, pages, (pages/visitors) as ppu from {$this->profile->tablename_vpd} where days = FROM_UNIXTIME('{$today}','%d-%b-%Y %a')";
		$result = $db->Execute($query);
		while($row = $result->fetchRow()){
			$import["visitors"]["today"] = $row["visitors"];
			$import["pages"]["today"] = $row["pages"];
			$import["pagespervisitors"]["today"] = number_format($row["ppu"],2);
		}
		
		$import["visitors"][$t] = 0;
		$import["pages"][$t] = 0;
		$import["pagespervisitors"][$t] = 0.00;
		$import["visitsPerUser"][$t] = 0.00;
		$import["visitorsperday"][$t] = 0;
		$import["visitsperday"][$t] = 0;
		
		// This Range
		$query="select month,visitors,pages,(pages/visitors) as ppu,visits,(visits/visitors) as vpu from ".$this->profile->tablename_vpm." where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". $db->quote($this->to) ." order by timestamp desc";
		$result = $db->Execute($query);
		while($row = $result->fetchRow()){
			$import["visitors"][$t] = $row["visitors"];
			$import["pages"][$t] = $row["pages"];
			$import["pagespervisitors"][$t] = number_format($row["ppu"],2);
			$import["visitsPerUser"][$t] = number_format($row["vpu"],2);
		}
		
		$query="select FROM_UNIXTIME(timestamp,'%m') as month,avg(visitors * 1.00) as avgvisitors,avg(visits * 1.00) as avgvisits from {$this->profile->tablename_vpd} 
		where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". ($this->to - 86400)." group by month order by timestamp";
		$result = $db->Execute($query);
		while($row = $result->fetchRow()){
			$import["visitorsperday"][$t] = $row["avgvisitors"];
			$import["visitsperday"][$t] = $row["avgvisits"];
		}
		if(!empty($import["visitors"][$p]) && $import["visitors"][$t] != 0){
			$query  = "select ((count(distinct visitorid) * 1.00) / {$import["visitors"][$t]}) * 100 as ctr from {$this->profile->tablename_conversions} where timestamp >= ". $db->quote($this->from) ." and timestamp <= ". ($this->to - 86400)." group by url";
			$a=0;
			$totctr = 0;
			$result=$db->Execute($query);
			if ($result) {
				while ($row = $result->FetchRow()) {
					$totctr=$totctr+$row["ctr"];
					$a++;
				}
			}
			if ($a <> 0) {
				$import["conversionRate"][$t] = number_format(($totctr/$a),2);
			} else {
				$import["conversionRate"][$t] = number_format(0,2);
			}
		}else{
			$import["conversionRate"][$t] = number_format(0,2);
		}
		// Prev Range
		$query="select month,visitors,pages,(pages/visitors) as ppu,visits,(visits/visitors) as vpu from ".$this->profile->tablename_vpm." where timestamp >= {$prevFrom} and timestamp <= {$prevTo} order by timestamp desc";
		$result = $db->Execute($query);
		while($row = $result->fetchRow()){
			$import["visitors"][$p] = $row["visitors"];
			$import["pages"][$p] = $row["pages"];
			$import["pagespervisitors"][$p] = number_format($row["ppu"],2);
			$import["visitsPerUser"][$p] = number_format($row["vpu"],2);
		}
		$query="select FROM_UNIXTIME(timestamp,'%m') as month,avg(visitors * 1.00) as avgvisitors,avg(visits * 1.00) as avgvisits from {$this->profile->tablename_vpd} 
		where timestamp >= {$prevFrom} and timestamp <= {$prevTo} group by month order by timestamp";
		$result = $db->Execute($query);
		while($row = $result->fetchRow()){
			$import["visitorsperday"][$p] = $row["avgvisitors"];
			$import["visitsperday"][$p] = $row["avgvisits"];
		}
		if(!empty($import["visitors"][$p]) && $import["visitors"][$p] != 0){
			$query  = "select ((count(distinct visitorid) * 1.00) / {$import["visitors"][$p]}) * 100 as ctr from {$this->profile->tablename_conversions} where timestamp >= {$prevFrom} and timestamp <= {$prevTo} group by url";
			$a=0;
			$totctr = 0;
			$result=$db->Execute($query);
			if ($result) {
				while ($row = $result->FetchRow()) {
					$totctr=$totctr+$row["ctr"];
					$a++;
				}
			}
			if ($a <> 0) {
				$import["conversionRate"][$p] = number_format(($totctr/$a),2);
			} else {
				$import["conversionRate"][$p] = number_format(0,2);
			}
		}else{
			$import["conversionRate"][$p] = number_format(0,2);
		}
		if (date("m", $this->to)=="1") {
			$prevyear=(date("Y", $this->to)-1);
			$prevmonth=12; 
		} else { 
			$prevyear=date("Y", $this->to);
			$prevmonth=(date("m", $this->to)-1);
		}
		$archn = ($prevmonth) . $prevyear;
		if(isset($import["visitors"][$t]) && (!empty($import["visitors"][$t]) || $import["visitors"][$t] != 0)){
			$import["bounceRate"][$t] = number_format(((BounceVisitors("redo") / $import["visitors"][$t]) * 100),2);
		}else{
			$import["bounceRate"][$t] = number_format(0,2);
		}
		if(isset($import["visitors"][$p]) && (!empty($import["visitors"][$p]) || $import["visitors"][$p] != 0)){
			$import["bounceRate"][$p] = number_format(((BounceVisitors($archn) / $import["visitors"][$p]) * 100),2);	
		}else{
			$import["bounceRate"][$p] = number_format(0,2);
		}
		$i = 0;
		foreach($import as $k => $v){
			$data[$i][0] = $k;
			$data[$i][1] = (isset($v["today"])) ? number_format($v["today"]) : 0;
			$data[$i][2] = (isset($v[$t])) ? number_format($v[$t]) : 0;
			$data[$i][3] = (isset($v[$p])) ? number_format($v[$p]) : 0;
			$i++;
		}
		
		$this->showfields = "item,"._TODAY.",{$t},{$p}";
		
		// dump($data);
		return $data;
	}
	
	function DisplayReport() {		
		global $todaylabel,$db,$warning,$vnum,$databasedriver,$real_today,$tableheaderfontcolor,$showcharturl,$lang;
		$d = $this->CreateData();
		$data = array();
		foreach($d as $k){
			$data[$k[0]][1] = $k[1];
			$data[$k[0]][2] = $k[2];
			$data[$k[0]][3] = $k[3];
		}
		$fields = explode(",",$this->showfields);
		
		if ($this->to) {
			$this->todaysdate = $this->to;
		} else {
			$this->todaysdate = time();
		}
		if(!isset($data["visitors"])){
			echoNotice(_NO_DATA_TO_DISPLAY,"margin:5px;");die();
		}
		$uvtoday=$data["visitors"][1];
		$uvthismonth = $data["visitors"][2];
		$uvlastmonth = $data["visitors"][3];
		$tmctr = $data["conversionRate"][2];
		$lmctr = $data["conversionRate"][3];
		$avgvisitspuser2 = $data["visitsPerUser"][2];
		$avgvisitspuser1 = $data["visitsPerUser"][3];
		$bounce1 = $data["bounceRate"][3];
		$bounce2 = $data["bounceRate"][2];
		if ($bounce1 < $bounce2) {
			$bounce2="<font color=red>$bounce2 %</font>";    
		} else {
			$bounce2="<font color=green>$bounce2 %</font>";    
		}
		if ($avgvisitspuser1 > $avgvisitspuser2) {
			 $avgvisitspuser2="<font color=red>$avgvisitspuser2</font>";
		} else {
			 $avgvisitspuser2="<font color=green>$avgvisitspuser2</font>";
		}
		if ($avgvisitspuser1 > $avgvisitspuser2) {
			 $color2="red";
		} else {
			 $color2="green";
		}
		ob_start();
		
		$this->displayReportButtons = false;
		$this->customHeaderContent = _TODAY_CONTEXT.": ".date("l - d M Y (H:i:s)", $this->todaysdate);
		$this->ReportHeader();
		?>		
			<table id="todaybox" style='margin-bottom: 10px;' cellspacing=0 cellpadding=0 border=0 width=100%>
				<tr>
					<td valign=top>
						<table cellspacing=0 cellpadding=2 border=0 width="100%">
							<?php
							echo "<tr bgcolor=silver>";
								echo "<th>&nbsp;</th>";
								echo "<th>"._VISITORS."</th>";
								echo "<th>"._PAGES."</th>";
								echo "<th title=\""._PAGES_PV_LONG."\">"._PAGES_PV_SHORT."</th>";
								echo "<th title=\""._NEW_VS_RETURNING_LONG."\">"._NEW_VS_RETURNING_SHORT."</th>";
							echo "</tr>";
							echo "<tr>";
								echo "<td>";
									echo "<nobr>".$fields[1]."</nobr>";
								echo "</td>";
								echo "<td>";
									echo "<b>".$data["visitors"][1]."</b>";
								echo "</td>";								
								echo "<td>";
									echo "<b>".$data["pages"][1]."</b>";
								echo "</td>";
								echo "<td>";
									echo "<b>".$data["pagespervisitors"][1]."</b>";
								echo "</td>";        		
								if (($return_visitors = getProfileData($this->profile->profilename, "{$this->profile->profilename}.rv_today", 0))) { 
									$return_percentage = @intval(($return_visitors / $uvtoday) * 100);
									$new_percentage = 100 - $return_percentage;
								} else {
									$new_percentage = "-";
									$return_percentage = "-";
								}		
								echo "<td>";
									echo "<b>{$new_percentage}%</b> - <font color=green>{$return_percentage}%</font>";
								echo "</td>";
							echo "</tr>";
							echo "<tr>";
								echo "<td>";
									echo "<nobr>".$fields[2]."</nobr>";
								echo "</td>";
								echo "<td>";
									echo "<b>".$data["visitors"][2]."</b>";
								echo "</td>";
								if (($return_visitors = getProfileData($this->profile->profilename, "{$this->profile->profilename}.rv_thismonth", 0))) { 
									$return_percentage = @intval(($return_visitors / $uvthismonth) * 100);
									$new_percentage = 100 - $return_percentage;
								} else {
									$new_percentage = "-";
									$return_percentage = "-";
								}
								echo "<td>";
									echo "<b>".$data["pages"][2]."</b>";
								echo "</td>";
								echo "<td>";
									echo "<b>".$data["pagespervisitors"][2]."</b>";
								echo "<td>";
									echo "<b>{$new_percentage}%</b> - <font color=green>{$return_percentage}%</font>";
								echo "</td>";
							echo "</tr>";   
							echo "<tr>";
								echo "<td>";
									echo "<nobr>".$fields[3]."</nobr>";
								echo "</td>";
								echo "<td>";
									echo "<b>".$data["visitors"][3]."</b>";
								echo "</td>";
								echo "<td>";
									echo "<b>".$data["pages"][3]."</b>";
								echo "</td>";
								echo "<td>";
									echo "<b>".$data["pagespervisitors"][3]."</b>";
								echo "</td>";        
							echo "</tr>"; 
						echo "</table>";				
						if (empty($this->gname)) {
							$this->gname = "months";
						}						
						$this->graphTypeMenu($this->gname);
						echo "<div style=\"height:170px; width:360px; margin-bottom: 20px;\" id=\"todaychart\">";
							
							if ($this->gname == "weeks") {
								$this->TodayOverviewWeek();
							} if ($this->gname == "oldmonths") {
								$this->TodayOverviewMonthBar();
							} else if ($this->gname == "months") {
								$this->TodayOverviewMonthArea();
							} else if ($this->gname == "years") {
								$this->TodayOverviewYearArea();
							} else if ($this->gname == "more") {
								echo "<div class=\"MoreTodayGraphOptions\">"._MORE_GRAPHS.":<ul>";
								echo "<li><a href=\"javascript:TodayGraphSelect('oldmonths','". $db->quote($this->to) ."','". $db->quote($this->from) ."','{$this->conf}','". $db->quote($this->to) ."');\">"._MONTH_COMPARE_BAR."</a></li>";
								echo "<li><a href=\"javascript:TodayGraphSelect('years','". $db->quote($this->to) ."','". $db->quote($this->from) ."','{$this->conf}','". $db->quote($this->to) ."');\">"._THISYEAR_VS_LASTYEAR."</a></li>";
								echo "</ul></div>";
							}
						echo "</div>";
					echo "</td>";
					echo "<td valign=top width=\"100%\">";
						echo "<table cellspacing=0 cellpadding=2 border=0 width=\"100%\" >";
							echo "<tr>";
								echo "<th bgcolor=silver>&nbsp;</th>";
							echo "</tr>";
							echo "<tr>";
								echo "<td>";
									echo "<table cellpadding=3 width=\"100%\"  border=0>";
										echo "<tr>";
											echo "<td valign=top  class=smallborder>";
												echo "<b>"._TRENDS.":</b><br>";
												$diff=($data["visitorsperday"][2]-$data["visitorsperday"][3]);
												$diff=intval($diff);		
												if (($diff < 2) && ($diff > -2) && ($diff !=0)) {
													echo "<font color=orange>"._PRETTY_STABLE."</font><br>";
													$color="orange";
													$smile="smile-flat.gif";
												} else if ($data["visitorsperday"][3] > $data["visitorsperday"][2]) {
													$color="red";    
													$smile="smile-shame.gif";
													echo "<font color=red>"._LOSING_VISITORS."</font><br>";
												} else if ($data["visitorsperday"][3] == $data["visitorsperday"][2]) {
													$color="orange";
													$smile="smile-flat.gif";
													echo "<font color=orange>"._STABLE."</font><br>";
												} else {
													$color="green";
													if ($diff > 300) {
															$smile="smile-fast.gif";
													} else {
															$smile="smile.gif";
													}
													echo "<font color=green>"._GAINING_VISITORS."</font><br>";
												}
												$otmctr=$tmctr;
												if ($lmctr > $tmctr) {
													$tmctr="<font color=red>$tmctr %</font>";
													echo "<font color=red>"._PERF_DROPPING."</font><br>";
												} else if ($lmctr==$tmctr) {
													$tmctr="<font color=orange>$tmctr %</font>";
													echo "<font color=orange>"._PERF_STABLE."</font><br>";
												} else {
													$tmctr="<font color=green>$tmctr %</font>";
													echo "<font color=green>"._PERF_INCREASING."</font><br>";
												}    
												$ctrdiff=$otmctr-$lmctr;    
												$diff=number_format($diff,0);
												echo "<div style=\"padding:14px;margin-left:6px;\">";
												if ($diff > 0) {
													echo "<table cellpadding=3>";
														echo "<tr>";
															echo "<td>";
																echo "<img src=\"images/$smile\" width=\"23\" height=\"23\" alt=\"\">";
															echo "</td>";
															echo "<td>";
																echo "<font color=$color size=3><b>+$diff</b></font>";
															echo "</td>";
												} else {
													echo "<table cellpadding=3>";
														echo "<tr>";
															echo "<td>";
																echo "<img src=\"images/$smile\" width=\"23\" height=\"23\" alt=\"\">";
															echo "</td>";
															echo "<td>";
																echo "<font color=$color size=3><b>$diff</b></font>";
															echo "</td>";
												}
														if ($ctrdiff > 0) {
															$smile="smile-relax.gif";
															echo "<td class=sider>";
																echo "<img src=\"images/$smile\" width=\"23\" height=\"23\" alt=\"\">";
															echo "</td>";
															echo "<td>";
																echo "<font color=green size=3><b>+$ctrdiff %</b></font>";
															echo "</td>";
														} else if ($ctrdiff==0) {
															$smile="smile-flat2.gif";
															echo "<td class=sider>";
																echo "<img src=\"images/$smile\" width=\"23\" height=\"23\" alt=\"\">";
															echo "</td>";
															echo "<td>";
																echo "<font color=orange size=3><b>$ctrdiff %</b></font>";
															echo "</td>";
														} else {
															$smile="smile-red.gif";
															echo "<td class=sider>";
																echo "<img src=\"images/$smile\" width=\"23\" height=\"23\" alt=\"\">";
															echo "</td>";
															echo "<td>";
																echo "<font color=red size=3><b>$ctrdiff %</b></font>";
															echo "</td>";
														}
													echo "</tr>";
												echo "</table>";
											echo "</div>";												 
											?>
											<table cellpadding=2 cellspacing=0 border=0>
												<tr>
													<td class=grayline>
														<font color=gray><?php echo _AVERAGES;?></font>
													</td>
													<td class=grayline2>
														<font color=gray><?php echo _LAST_MONTH;?></font>
													</td>
													<td class=grayline2>
														<font color=gray><?php echo _THIS_MONTH;?></font>
													</td>
												</tr>
												<tr>
													<td><?php echo _VISITORS_PER_DAY;?></td>
													<td align=center class=sider><?php echo number_format($data["visitorsperday"][3]); ?></td>
													<td align=center class=sider><?php echo "<font color=$color>".number_format($data["visitorsperday"][2])."</font>";?></td>
												</tr>
												<tr>
													<td><?php echo _VISITS_PER_DAY;?></td>
													<td align=center class=sider><?php echo number_format($data["visitsperday"][3]); ?></td>
													<td align=center class=sider><?php echo "<font color=$color2>".number_format($data["visitsperday"][2]); ?></font></td>
												</tr>
												<tr>
													<td><?php echo _VISITS_PER_USER;?></td>
													<td align=center class=sider><?php echo $avgvisitspuser1; ?></td>
													<td align=center class=sider><?php echo $avgvisitspuser2; ?></td>
												</tr>
												<tr>
													<td><?php echo _CONVERSION_RATE;?></td>
													<td align=center class=sider><?php echo $lmctr; ?> %</td>
													<td align=center class=sider><?php echo $tmctr; ?></td>
												</tr>
												<tr>
													<td title="<?php echo _BOUNCE_RATE_EXPLAIN;?>"><?php echo _BOUNCE_RATE;?></td>													
													<td align=center class=sider><?php echo $bounce1; ?> %</td>
													<td align=center class=sider><?php echo $bounce2; ?> </td>
												</tr> 
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?php
		$todaytrends = ob_get_clean();
		echo $todaytrends;
	}
	function TodayOverviewMonthArea() {
		global $db;
		
		$graphdata = array();
		
		$color[0]="EEEEEE";
		$color[1]="9999FF";
		$color[2]="0080C0";
		$color[3]="FF3399";
		$color[4]= "FF8040";
		$color[5]="FFFF00";
		$color[6]= "FF0080";
		$color[7]= "00FF00";
		$color[8]= "FFFFCC";
		$color[9]= "FF0000";
		$color[10]="00CCFF";
		$color[11]="CCCCCC"; 
		$color[12]="993300";
		
		$this->from = mktime(0,0,0,(date("m", $this->todaysdate)-1), 01, date("Y", $this->todaysdate));
		$this->to = mktime(23,59,59,date("m", $this->todaysdate), date("d", $this->todaysdate), date("Y", $this->todaysdate));
		$lastmonth_q = date("M Y", $this->from);
		$thismonth_q = date("M Y", $this->to);
		$lmn = get_month_lastday(date("m", $this->from), date("Y", $this->from));
		$tmn = get_month_lastday(date("m", $this->to), date("Y", $this->to)); 
		if ($lmn > $tmn) {
			$n = $lmn;   
		} else {
			$n = $tmn;
		}

		if ($n > 8) {
			$step = round(($n/5), 0);
		} else {
			$step = 1;
		}
		if ($step == 0) {
			$step = 1;   
		}
		$i=1;

		while ($i <= $n) {
			$graphdata[($i - 1)][0] = $i;
			$graphdata[($i - 1)][1] = 0;
			$graphdata[($i - 1)][2] = 0;
			$graphdata[($i - 1)][3] = 0;
			$i++;
		}

		$query = "select FROM_UNIXTIME(timestamp,'%d') as day,visitors from {$this->profile->tablename_vpd} where FROM_UNIXTIME(timestamp,'%b %Y')='{$lastmonth_q}' order by timestamp";
		$q = $db->Execute($query);
		
		while ($cdata = $q->FetchRow()) {
			$d = intval($cdata[0]);
			$graphdata[($d - 1)][2] = $cdata[1];
			$trenddata[] = $cdata[1];
		}

		$query = "select FROM_UNIXTIME(timestamp,'%d') as day,visitors from {$this->profile->tablename_vpd} where FROM_UNIXTIME(timestamp,'%b %Y')='{$thismonth_q}' order by timestamp";
		$q = $db->Execute($query);
		
		while ($cdata = $q->FetchRow()) {
			$d = intval($cdata[0]);
			$thismonth[$d] = "  <set value=\"".$cdata[1]."\" />\n";
			$graphdata[($d - 1)][3] = $cdata[1];
			$trenddata[] = $cdata[1];
		}
		
		// $start_trend = ($graphdata[0][2] + $graphdata[0][3]) / 2;
		if(!empty($trenddata)) {
			$start_trend = array_sum($trenddata) / count($trenddata);
		} else {
			$start_trend = 0;
		}
		if(!isset($trenddata)){ 
			echoWarning(_NO_DATA_TO_DISPLAY, "width:150px;");
			die();
		}
		
		$trend_step = getTrend($trenddata);
		
		$step = $start_trend + $trend_step;
		for($c = 0; $c <= 30; $c++) {
			$graphdata[$c][1] = $step;
			$step = $step + $trend_step;
		}
		
		$this->showfields = _DATE.","._VISITOR_TREND.","._VISITORS." ".date("F", $this->from).","._VISITORS." ".date("F", $this->to);
		$custom_graphoptions = array(
			// "series" => array(
				// 0 => array(
					// "lineWidth" => 1,
					// "markerOptions" => array(
						// "show" => false
					// )
				// )
			// ),
			'seriesDefaults' => array(
				"lineWidth" => 3,
				"markerOptions" => array(
					"show" => false
				)
			)
		);
		
		$this->Graph($graphdata, 'line', '', 0, 150, $custom_graphoptions, 'south', 'inline');
	}
	
	function TodayOverviewWeek() {
		global $db;
		
		$graphdata = array();
		
		$color[0] = "BEBFBF";
		$color[1] = "89A1B6";
		$color[2] = "5182AC";
		$color[3] = "FF0000";
		$color[4] = "FF8040";
		$color[5] = "FFFF00";
		$color[6] = "FF0080";
		$color[7] = "00FF00";
		$color[8] = "FFFFCC";
		$color[9] = "FF0000";
		$color[10] = "00CCFF";
		$color[11] = "CCCCCC";
		$color[12] = "993300";
		
		$from_3weeks_ago = strtotime("-3 weeks", $this->todaysdate);
		$from_3weeks_ago = mktime(0, 0, 0, date("m", $from_3weeks_ago), date("d", $from_3weeks_ago), date("Y", $from_3weeks_ago));
		$to_now = mktime(23,59,59,date("m", $this->todaysdate),date("d", $this->todaysdate),date("Y", $this->todaysdate));
		$to_week = date("W Y", $to_now);
		$from_week = date("W Y", $from_3weeks_ago);
		
		$n = 7;
		if ($n > 8) {
			$step = round(($n / 5), 0);
		} else {
			$step = 1;
		}
		if ($step == 0) {
			$step = 1;
		}

		$s = 1;
		$thisweek = date("W Y", $from_3weeks_ago);
		$thisfrom = $from_3weeks_ago;
		
		$prefill = array();
		$prefill[1] = "Monday";
		$prefill[2] = "Tuesday"; 
		$prefill[3] = "Wednesday"; 
		$prefill[4] = "Thursday"; 
		$prefill[5] = "Friday"; 
		$prefill[6] = "Saturday"; 
		$prefill[7] = "Sunday";
			
		while ($s <= 4) {
			$query  ="select FROM_UNIXTIME(timestamp,'%W') as day,visitors from {$this->profile->tablename_vpd} where FROM_UNIXTIME(timestamp,'%u %Y')='{$thisweek}' order by timestamp";
			$q = $db->Execute($query);           
			
			$i = 1;
			while ($i <= 7) {
				$series[$i][0] = 0;
				$series[$i][$s] = 0;
				$i++;   
			}
			$i = 1; 
			while ($cdata = $q->FetchRow()) {
				while ($prefill[$i] != $cdata[0]) {
					$i++;
				} 
				if ($prefill[$i] == $cdata[0]) {
					$series[$i][$s] = $cdata[1];
				} else {
					echoDebug("NOOOO match for {$cdata[0]},  {$cdata[1]}");
				} 
				$i++;
			}
			$thisfrom = strtotime("+1 week", $thisfrom);
			$thisweek = date("W Y", $thisfrom);    
			$s++;
		}
		$graphdata = $series;
		$custom_graphoptions = array(
			"series" => array(
				0 => array(
					"color" => "#50A5D3"
				),
				1 => array(
					"color" => "#1970B4"
				),
				2 => array(
					"color" => "#0E4067"
				),
				3 => array(
					"color" => "#F00"
				)
			),
			"seriesDefaults" => array(
				"rendererOptions" => array(
					"barWidth" => 6,
					"barPadding" => 3
				)
			),
			"axes" => array(
				"xaxis" => array(
					"ticks" => array(
						"Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"
					),
					"tickOptions" => array(
						"angle" => 0
					)
				)
			)
		);
		
		$this->showfields = _DATE.",3 "._WEEKS_AGO.",2 "._WEEKS_AGO.",1 "._WEEK_AGO.","._THIS_WEEK;
		$this->showfields = _DATE.",3 "._WEEKS_AGO.",2 "._WEEKS_AGO.",1 "._WEEK_AGO.","._THIS_WEEK;
		$this->Graph($graphdata, 'bar', '', 0, 150, $custom_graphoptions, 'south', 'inline');
	}
	
	function TodayOverviewMonthBar() {
		global $db;
		
		$graphdata = array();
		
		$color[0]="EEEEEE";
		$color[1]="9999FF";
		$color[2]="0080C0";
		$color[3]="FF3399";
		$color[4]= "FF8040";
		$color[5]="FFFF00";
		$color[6]= "FF0080";
		$color[7]= "00FF00";
		$color[8]= "FFFFCC";
		$color[9]= "FF0000";
		$color[10]="00CCFF";
		$color[11]="CCCCCC"; 
		$color[12]="993300";
		
		$this->from = mktime(0,0,0,(date("m", $this->todaysdate)-1), 01, date("Y", $this->todaysdate));
		$this->to = mktime(23,59,59,date("m", $this->todaysdate), date("d", $this->todaysdate), date("Y", $this->todaysdate));
		$lastmonth_q = date("M Y", $this->from);
		$thismonth_q = date("M Y", $this->to);
		$lmn = get_month_lastday(date("m", $this->from), date("Y", $this->from));
		$tmn = get_month_lastday(date("m", $this->to), date("Y", $this->to)); 
		if ($lmn > $tmn) {
			$n = $lmn;   
		} else {
			$n = $tmn;
		}

		if ($n > 8) {
			$step = round(($n/5), 0);
		} else {
			$step = 1;
		}
		if ($step == 0) {
			$step = 1;   
		}
		$i=1;

		while ($i <= $n) {
			$graphdata[($i - 1)][0] = $i;
			$graphdata[($i - 1)][1] = 0;
			$graphdata[($i - 1)][2] = 0;
			$graphdata[($i - 1)][3] = 0;
			$i++;
		}

		$query = "select FROM_UNIXTIME(timestamp,'%d') as day,visitors from {$this->profile->tablename_vpd} where FROM_UNIXTIME(timestamp,'%b %Y')='{$lastmonth_q}' order by timestamp";
		$q = $db->Execute($query);
		
		while ($cdata = $q->FetchRow()) {
			$d = intval($cdata[0]);
			$graphdata[($d - 1)][2] = $cdata[1];
			$trenddata[1][] = $cdata[1];
		}

		$query = "select FROM_UNIXTIME(timestamp,'%d') as day,visitors from {$this->profile->tablename_vpd} where FROM_UNIXTIME(timestamp,'%b %Y')='{$thismonth_q}' order by timestamp";
		$q = $db->Execute($query);
		
		while ($cdata = $q->FetchRow()) {
			$d = intval($cdata[0]);
			$thismonth[$d] = "  <set value=\"".$cdata[1]."\" />\n";
			$graphdata[($d - 1)][3] = $cdata[1];
			$trenddata[1][] = $cdata[1];
		}
		
		$start_trend = ($graphdata[0][2] + $graphdata[0][3]) / 2;
		$trend_step = getTrend($trenddata);
		$step = $start_trend;
		
		for($c = 0; $c <= 30; $c++) {
			$graphdata[$c][1] = $step;
			$step = $step + $trend_step;
		}
		
		$this->showfields = _DATE.","._VISITOR_TREND.","._VISITORS." ".date("F", $this->from).","._VISITORS." ".date("F", $this->to);
		$custom_graphoptions = array(
			"series" => array(
				0 => array(
					"lineWidth" => 1,
					"markerOptions" => array(
						"show" => false
					)
				)
			),
			"seriesDefaults" => array(
				"rendererOptions" => array(
					"barWidth" => 3,
					"barPadding" => 1,
					"barMargin" => 10
				)
			)
		);
		
		$this->Graph($graphdata, array('','line','bar','bar'), '', 0, 150, $custom_graphoptions);
	}
	
	function TodayOverviewYearArea() {
		global $db;
		
		$graphdata = array();
		
		$color[0]="EEEEEE";
		$color[1]="9999FF";
		$color[2]="0080C0";
		$color[3]="FF3399";
		$color[4]= "FF8040";
		$color[5]="FFFF00";
		$color[6]= "FF0080";
		$color[7]= "00FF00";
		$color[8]= "FFFFCC";
		$color[9]= "FF0000";
		$color[10]="00CCFF";
		$color[11]="CCCCCC"; 
		$color[12]="993300";
		
		$this->from = mktime(0,0,0,01,01,date("Y", $this->todaysdate)-1);
		$this->to = mktime(23,59,59,date("m", $this->todaysdate),date("d", $this->todaysdate),date("Y", $this->todaysdate));
		
		$lastmonth_q = date("Y", $this->from);
		$thismonth_q = date("Y", $this->to);
		
		$n = 12;
		
		if ($n > 8) {
			$step = round(($n/5),0);
		} else {
			$step=1;
		}
		if ($step==0) {
			$step=1;   
		}
		
		$i = 1;
		while ($i <= $n) {
			$graphdata[($i - 1)][0] = $i;
			$graphdata[($i - 1)][1] = 0;
			$graphdata[($i - 1)][2] = 0;
			$graphdata[($i - 1)][3] = 0;
			$i++;
		}
		
		$query = "select FROM_UNIXTIME(timestamp,'%m') as day,visitors from {$this->profile->tablename_vpm} where FROM_UNIXTIME(timestamp,'%Y')='{$lastmonth_q}' order by timestamp";
		$q = $db->Execute($query);
		while ($cdata = $q->FetchRow()) {
			$d = intval($cdata[0]);
			$graphdata[($d - 1)][2] = $cdata[1];
			$trenddata[1][] = $cdata[1];
			$tmp_trenddata[0][] = $cdata[1];
		}
		if(!empty($tmp_trenddata[0])) {
			$avg_first = round(array_sum($tmp_trenddata[0]) / count($tmp_trenddata[0]));
		} else {
			$avg_first = 0;
		}
		
		$query = "select FROM_UNIXTIME(timestamp,'%m') as day,visitors from {$this->profile->tablename_vpm} where FROM_UNIXTIME(timestamp,'%Y')='{$thismonth_q}' order by timestamp";
		$q = $db->Execute($query);
		
		while ($cdata = $q->FetchRow()) {
			$d = intval($cdata[0]);
			$graphdata[($d - 1)][3] = $cdata[1];
			$trenddata[1][] = $cdata[1];
			$tmp_trenddata[1][] = $cdata[1];
		}
		if(!empty($tmp_trenddata[1])) {
			$avg_second = round(array_sum($tmp_trenddata[1]) / count($tmp_trenddata[1]));
		} else {
			$avg_second = 0;
		}
		
		if($avg_first == 0 && $avg_second == 0) {
			$start_trend = 0;
		} else {
			$start_trend = ($avg_first + $avg_second) / 2;
		}
		if(!isset($trenddata)){
			echoWarning(_NO_DATA_TO_DISPLAY, "width:150px;");
			die();
		}	
		$trend_step = getTrend($trenddata);
		$step = $start_trend + $trend_step;
		for($c = 0; $c <= 11; $c++) {
			$graphdata[$c][1] = $step;
			$step = $step + $trend_step;
		}
		
		$this->showfields = _DATE.","._VISITOR_TREND.","._VISITORS." ".date("Y", $this->from).","._VISITORS." ".date("Y", $this->to);
		$custom_graphoptions = array(
			"series" => array(
				0 => array(
					"lineWidth" => 1,
					"markerOptions" => array(
						"show" => false
					)
					// "color" => "#333333"
				)
			),
			'seriesDefaults' => array(
				"lineWidth" => 3,
				"markerOptions" => array(
					"show" => false
				)
			),
			"axes" => array(
				"xaxis" => array(
					"tickOptions" => array(
						"angle" => 0
					)
				)
			)
		);
		
		$this->Graph($graphdata, 'line', '', 0, 150, $custom_graphoptions, 'south', 'inline');
	}
	
	function DisplayCustomForm() {
		if(empty($this->gname)) { $this->gname = "months"; }
		$selected = $this->gname;
		
		echo "<label for='gname'>"._CHART_TYPE."</label>";
		echo "<select class='report_option_field' id='gname' name='gname'>";
			if($this->gname == 'months') {
				echo "<option value='months'>"._MONTH."</option>";
			} else {
				echo "<option value='months'>"._MONTH."</option>";
			}
			
			if($this->gname == 'weeks') {
				echo "<option value='weeks'>"._WEEK."</option>";
			} else {
				echo "<option value='weeks'>"._WEEK."</option>";
			}
			
			if($this->gname == 'years') {
				echo "<option value='years'>"._YEAR."</option>";
			} else {
				echo "<option value='years'>"._YEAR."</option>";
			};
		echo "</select>";
	}
	
	function graphTypeMenu($selected) {
		global $todaysdate;
		
		$r[0] = "<a class='graylink open_in_this_dialog quickopen' href=\"reports.php?labels=_TODAY_BOX&statstable_onl=1&gname=months\">"._MONTH."</a>";
		$r[1] = "<a class='graylink open_in_this_dialog quickopen' href=\"reports.php?labels=_TODAY_BOX&statstable_onl=1&gname=weeks\">"._WEEK."</a>";
		$r[2] = "<a class='graylink open_in_this_dialog quickopen' href=\"reports.php?labels=_TODAY_BOX&statstable_onl=1&gname=years\">"._YEAR."</a>";
		
		for ($i=0;$i < count($r);$i++) {
			if (strpos($r[$i],$selected)!==FALSE) {
				$r[$i] = str_replace("graylink","graylinkselected",$r[$i]);
			}
		}
		
		echo "<span style=\"position:absolute;margin-top: 7px; margin-left:230px;\">";
		foreach ($r as $row) {
			echo $row;   
		}
		echo "</span>";
	}
}
?>
