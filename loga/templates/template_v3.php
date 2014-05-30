<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
Class Template_v3 extends Template {
	function HTMLheadTag($headAddition = "") {
		global $conf, $baseurl, $profiles, $ourpath, $from, $to, $new_ui;
		
		$htmlheadtag = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">".PHP_EOL;
		//$htmlheadtag = "<!DOCTYPE html>".PHP_EOL;
		
		//$htmlheadtag .= "<html>".PHP_EOL;
		$htmlheadtag .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">".PHP_EOL;
		$htmlheadtag .= "<head>".PHP_EOL;
		$htmlheadtag .= "	<meta name=\"ROBOTS\" content=\"NOINDEX, NOFOLLOW\" />".PHP_EOL;
		$htmlheadtag .= "	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />".PHP_EOL;
		$htmlheadtag .= "	<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />".PHP_EOL;
		if(isset($baseurl) && ($baseurl)) { $htmlheadtag .= "<base href=\"{$baseurl}\">".PHP_EOL; }
		
		$htmlheadtag .= "	<title>Logaholic Web Analytics - {$conf}</title>".PHP_EOL;
		
		$htmlheadtag .= "	<link rel=\"icon\" href=\"favicon.ico\" type=\"image/x-icon\" />".PHP_EOL;
		$htmlheadtag .= "	<link rel=\"shortcut icon\" href=\"favicon.ico\" type=\"image/x-icon\" />".PHP_EOL;
		
		$htmlheadtag .= "	<link type=\"text/css\" rel=\"stylesheet\" href=\"components/jquery/css/lightness/jquery-ui-1.8.custom.css\" />".PHP_EOL;
		
		$htmlheadtag .= "	<link type=\"text/css\" rel=\"stylesheet\" href=\"components/jquery/jqplot/jquery.jqplot.min.css\" />".PHP_EOL;
		$htmlheadtag .= "	<link type=\"text/css\" rel=\"stylesheet\" href=\"components/jquery/datatables/css/datatables.css\" />".PHP_EOL;
		
		$htmlheadtag .= "	<link type=\"text/css\" rel=\"stylesheet\" href=\"templates/template_v3.css\" />".PHP_EOL;
		
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/js/jquery-1.7.1.min.js\"></script>".PHP_EOL;
		
		$htmlheadtag .= "	<!--[if IE]><script type=\"text/javascript\" src=\"components/jquery/jqplot/excanvas.min.js\"></script><![endif]-->".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/jqplot/jquery.jqplot.min.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/jqplot/plugins/jqplot.canvasTextRenderer.min.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/jqplot/plugins/jqplot.dateAxisRenderer.min.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/jqplot/plugins/jqplot.barRenderer.min.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/jqplot/plugins/jqplot.categoryAxisRenderer.min.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/jqplot/plugins/jqplot.pieRenderer.min.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/jqplot/plugins/jqplot.trendline.min.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/jqplot/plugins/jqplot.meterGaugeRenderer.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/jqplot/plugins/jqplot.bubbleRenderer.min.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/jqplot/plugins/jqplot.cursor.min.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/jqplot/plugins/jqplot.highlighter.js\"></script>".PHP_EOL;		
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/jqplot/plugins/jqplot.smoothFunnelRenderer.js\"></script>".PHP_EOL;
		
		$htmlheadtag .= "	<script type=\"text/javascript\" language=\"javascript\" src=\"components/jquery/datatables/js/jquery.dataTables.min.js\"></script>".PHP_EOL;
		
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/js/jquery-ui-1.8.13.custom.min.js\"></script>".PHP_EOL;
		
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/js/jquery-scrollTo.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/js/jquery.tablesorter.min.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/json/json2.js\"></script>".PHP_EOL;	
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/js/jquery.sparkline.min.js\"></script>".PHP_EOL;	
		
		
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"jsfunctions.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"corejs.js.php\"></script>".PHP_EOL;
		
		// if(strpos($_SERVER['PHP_SELF'], 'v3.php') !== false) {
			if(!empty($_REQUEST['editconf'])) {
				$htmlheadtag .= "	<script type=\"text/javascript\" src=\"jq-functions.js.php?conf={$_REQUEST['editconf']}\" charset=\"utf-8\"></script>".PHP_EOL;
			} else {
				$htmlheadtag .= "	<script type=\"text/javascript\" src=\"jq-functions.js.php?conf={$conf}\" charset=\"utf-8\"></script>".PHP_EOL;
			}
		// }
		
		if ($profiles==1) {
			$htmlheadtag .=  "	<script type=\"text/javascript\" src=\"{$ourpath}loghint.js\"></script>".PHP_EOL;
		}
		$htmlheadtag .= "	<script type=\"text/javascript\">";
		$htmlheadtag .= "var conf_name = '{$conf}';";
		$htmlheadtag .= "var from_date = '{$from}';";
		$htmlheadtag .= "var to_date = '{$to}';";
		$htmlheadtag .= "var new_ui = '{$new_ui}';";
		$htmlheadtag .= "var actionmenu_url = 'includes/actionmenu_v3.php';";
		
		
		$htmlheadtag .= "	</script>".PHP_EOL;
		
		$htmlheadtag .= "<!--[if lte IE 8]>";
		$htmlheadtag .= "	<link type=\"text/css\" rel=\"stylesheet\" href=\"templates/iefix_v3.css\" />".PHP_EOL;
		$htmlheadtag .= "<![endif]-->\n";
		
		$htmlheadtag .= $headAddition;
		
		$htmlheadtag .= "</head>".PHP_EOL;
		
		echo $htmlheadtag;
	}
	
	function BodyStart() {	
		echo "<body onload='finishpage();'>".PHP_EOL;
	}
	
	function Navigation(){
		global $loginSystemExists, $userAuthenticationType, $session, $validUserRequired,$available_langs, $lang, $editconf, $conf, $dashboards, $reports, $from, $to, $db,$profile, $show_progressbar;
		
		$this->affiliate_id = getGlobalSetting('affiliate_id');
		
		if(!isset($show_progressbar)) { $show_progressbar = true; }
		
		$currentFile = $_SERVER["SCRIPT_NAME"];
		$parts = Explode('/', $currentFile);
		$currentFile = $parts[count($parts) - 1];
		
		echo $this->Notifications();	
				
		echo "<div id='manage_profiles_list'>";
			echo"<ul>";	
			// When your licence says you can see all.
			$where = "";
			if (($validUserRequired) && (!$session->isAdmin())) {
				// Can't use implode here because we need to escape the entries.
				$validprofiles = "";
				for ($i = count($session->user_profiles)-1; $i >= 0; $i--) {
					if ($validprofiles != "") { $validprofiles .= "\",\""; }
					$validprofiles .= $db->escape($session->user_profiles[$i]);
				}
				$where = " where profilename in (\"$validprofiles\")";
			}
			$limitsql = "LIMIT 5";
			if (defined("_LIMIT_PROFILES") != 0 && _LIMIT_PROFILES < 5) {
				$limitsql = "LIMIT "._LIMIT_PROFILES;
			} 
			
			$q = $db->Execute("SELECT profilename FROM ". TBL_PROFILES . " $where ORDER BY lastused DESC $limitsql");
			$urlx = $_SERVER["PHP_SELF"]."?".$_SERVER["QUERY_STRING"]; 
			while($data = $q->fetchRow()){
				if(empty($editconf)){ $c= $conf; }else{ $c = $editconf; }	
				$urlx = explode("?",$urlx);
				$urlx[1] = str_replace($c,$data["profilename"],$urlx[1]);
				$urlx = implode("?",$urlx);
				
				if($data["profilename"] != $conf){
					echo "<li><a href='".$urlx."'>".$data["profilename"]."</a></li>";
				}
			}
			echo"</ul>";
		echo "</div>";
		echo "<div id='north'>";
				$this->CoBranding(); // When to display (partner) logo or not
				
				echo "<div class='clear'></div>";
				
				if(!empty($profile)) {
					$update_is_running = getProfileData($profile->profilename, "{$profile->profilename}.perlupdate_running", 'no');
				} else {
					$update_is_running = false;
				}
				
				if($update_is_running == 'yes') {
					$status = '';
					echo "<div class='updateProgressText'>";
						echo "<img src=\"images/icons/database.gif\" align=\"left\"> ";
						echo _UPDATING." ";
						echo " <span class='progress_status'>{$status}</span>";
						echo " <span class='stop_import' style=\"float:right;padding:0 10px;border-left:1px solid gray;cursor:pointer;\"> Cancel Update </span>";
						echo " <span class='view_import_log' style=\"float:right;padding:0 10px;border-left:1px solid gray;\"><a href=files/{$profile->profilename}_update_progress.lwa.log class=nodec4> View update log </a></span>";
					echo "</div>";
					echo "<div class='clear'></div>";
					echo "<div class='updating_progress'>";
						echo "<div class=\"ui-progressbar-value ui-widget-header\"></div>";
					echo "</div>";
				}
				?>
				<div id="top_button_bar">
					<div id="top_menubar_left">
						<ul class="dropdown top_navigation">
							<li>
								<a class="leave_page manage-profiles-list-link" title="Profiles" id="profiles" href="profiles.php?&editconf=<?php echo $conf;?>"><?php echo _MANAGE_PROFILES;?></a>
							</li>
							<!-- SWITCH ON REPORT NAV STUFF! -->
							<?php if(isset($profile) && $profile->profileloaded == true) { ?>
								<li>
									<a style="cursor:default;"><?php echo $conf; ?></a>
									<ul class='dropdown-list'>
										<li><a href="index.php?conf=<?php echo $conf;?>"><?php echo _VIEW_STATS;?></a></li>
									<?php if (!$validUserRequired || @$session->canEditProfiles()) { ?>
									<li><a class="leave_page" title="Settings" id="settings" href="profiles.php?editconf=<?php echo $conf;?>&edit=1"><?php echo _EDIT_PROFILE;?></a></li>
									<li><a class="leave_page" href="profiles.php?editconf=<?php echo $conf; ?>&amp;del=1"><?php echo _MAINTENANCE; ?></a></li>
									<?php } if (!$validUserRequired || @$session->canUpdateLogs()) { ?>
									<li><a class="leave_page" href="update.php?conf=<?php echo $conf; ?>"><?php echo _UPDATE_NOW;?></a></li>								
									<?php } ?>
									</ul>
								</li>
							<!-- ELSE GLOBAL NAV STUFF! -->
							<?php } else {
								 if (!empty($editconf)) { ?>
								<li><a style="cursor:default;"><?php echo $editconf; ?></a>
									<ul class='dropdown-list'>
									<li><a href="index.php?conf=<?php echo $editconf;?>"><?php echo _VIEW_STATS;?></a></li>
									<?php if (!$validUserRequired || @$session->canEditProfiles()) { ?>
									<li><a class="leave_page" title="Settings" id="settings" href="profiles.php?editconf=<?php echo $editconf;?>&edit=1"><?php echo _EDIT_PROFILE;?></a></li>
									<li><a href="profiles.php?editconf=<?php echo $editconf; ?>&amp;del=1"><?php echo _MAINTENANCE; ?></a></li>
									<?php } if (!$validUserRequired || @$session->canUpdateLogs()) { ?>
									<li><a href="update.php?conf=<?php echo $editconf; ?>"><?php echo _UPDATE_NOW;?></a></li>
									<?php } ?>
									</ul>
								</li>
								<?php }
							}
							// <!-- SWITCH ON REPORT NAV STUFF! -->
							if(isset($profile) && $profile->profileloaded == true) { ?>
								<?php if(@$currentFile == 'v3.php') { ?>
								<li class='summon-reports'><a href="#report-area"><?php echo _REPORTS; ?></a></li>
								<?php } else { ?>
								<li><a href="v3.php?conf=<?php echo $conf; ?>"><?php echo _REPORTS; ?></a></li>
								<?php } ?>
								
							<!-- ELSE GLOBAL NAV STUFF! -->
							<?php } else { ?>
								<?php if ((($loginSystemExists) && ($userAuthenticationType == USER_AUTHENTICATION_NONE)) ||  (($validUserRequired) && $session->isAdmin())) { ?>
								<li><a href="user_login/admin.php"><?php echo _USER_ADMINISTRATION;?></a></li>
								<?php }
								if (!$validUserRequired || $session->isAdmin()) { ?>      
								<li><a href="settings.php<?php echo "?conf=$editconf"; ?>"><?php echo _GLOBAL_SETTINGS;?></a></li>
								<?php } ?>						
						<?php } ?>
						</ul>
					</div>
					
					
					<?php if(@$currentFile == 'v3.php') { ?>
						<span class='vline'>
							<ul class='workspace-actions'>
								<li class='change-daterange' title='<?php echo _WORKBAR_CHANGE_DATERANGE;?>'></li>
								<li class='notes-button open_iframe_window' href='notes.php?conf=<?php echo $conf; ?>&timestamp=<?php echo time(); ?>&donote=create' title='<?php echo _NOTES; ?>'></li>
								<li class='save-screen' title='<?php echo _SAVE_WORKSPACE_AS_DASHBOARD; ?>'></li>
								
								<li class='pdf-workspace' href="pdf.php?conf=<?php echo $conf;?>" title='<?php echo _EXPORT_WORKSPACE_AS_PDF;?>'></li>
								
								<li class='delete-dashboard' title='<?php echo _DELETE_OPENED_DASHBOARD; ?>'></li>
								
							</ul>
						</span>
						<span class='vline'>						
							<ul class='workspace-actions'>
								<li class='minimize-all' title='<?php echo _MINIMIZE_ALL_REPORTS;?>'></li>
								<li class='restore-all' title='<?php echo _RESTORE_ALL_REPORTS;?>'></li>
								<li class='close-all' title='<?php echo _CLOSE_ALL_REPORTS_IN_CURRENT_WORKSPACE;?>'></li>
							</ul>
						</span>
					<?php } ?>
					
					<?php
					/* if($update_is_running == 'yes' && @$session->canUpdateLogs() && !empty($show_progressbar)) {
						$status = "";
						echo "<div class='updateProgressText' style=\"float: left; line-height: 16px; margin: 4px 0 0 5px; font-weight: normal; font-size: 11px; color: #915608; background-color: #FFC; border: 1px solid #FCD113; padding: 4px; border-radius: 4px; \">";
						echo "<img src=\"images/icons/updating_notifi_icon.gif\" align=\"left\"> ";
						echo "Updating {$conf}: <strong class='progress_percentage'>{$perc}%</strong> complete";
						echo " <span class='progress_status'>{$status}</span>";
						echo "</div>";
					} else {
						// $this->Messages();
					} */
					?>
					<div id="top_menubar_right">
						<ul class="dropdown top_navigation">
							<li class='first language-picker'>
							<?php echo "<a href=\"#\"><img src='images/flags/".ucwords($lang).".png' style='border:0px;' />". _LANGUAGE . "</a>"; ?>
							<?php
							echo "<ul class='dropdown-list'>";
								foreach($available_langs as $value) {  
									$l_value = "_" . strtoupper($value);
									$query_string = $_SERVER['QUERY_STRING'];
									$query_str_arr = explode("&", $query_string);
									if(count($query_str_arr) > 1) {
										foreach($query_str_arr as $q_str_key => $q_str_val) {
											$tmp_str = explode("=", $q_str_val);
											if($tmp_str[0] == 'lang' || $tmp_str[0] == 'nocache' || $tmp_str[0] == 'conf') {
												unset($query_str_arr[$q_str_key]);
											}
										}
									}
									$query_string = implode("&", $query_str_arr);
									echo "<li class='language'><a href=\"".$_SERVER['PHP_SELF']."?".$query_string."&amp;lang=$value&amp;nocache=1&amp;conf={$conf}\"><img src='images/flags/".ucwords($value).".png' style='border:0px;' alt='".ucwords($value)."' />" . constant($l_value) ."</a></li>";
								}
							echo "</ul>";							
							?>
							</li>
							<li class="help-list">
								<a target="_blank" title="<?php showVersion(); ?>" id="help" href="http://logaholic.com/manual/"><?php echo _HELP;?></a>
								<ul class='dropdown-list'>
									<li><a target="_blank" href="http://logaholic.com/manual/"><img style='border:0px; float:left;' src="images/icons/book.png" alt="" /><?php echo _MANUAL; ?></a></li>
									<li><a target="_blank" href="http://logaholic.com/help/"><img style='border:0px; float:left;' src="images/icons/balloon.png" alt=""/><?php echo _ASK_A_QUESTION; ?></a></li>
									
								</ul>
							</li>
							<?php if ($validUserRequired==true && $session->logged_in==true) { ?>
							<li><a href="user_login/logout.php" title="<?php echo _CURRENTLY_LOGGED_IN_AS.' '.$session->username ?>"><?php echo _LOGOUT;?></a></li>
					<?php } ?>
					</ul>
				</div>
			</div>
		</div>
		<div class="clear"></div>
		<?php if(@$currentFile == 'v3.php') { ?>
		<div class='lwa_datepicker'>
			<div class='from-range'>
				<h3>From</h3>
				<div id='fromRange' name='fromRange'></div>
			</div>
			<div class='to-range'>
				<h3>To</h3>
				<div id='toRange' name='toRange'></div>
			</div>
			<ul class='quickDateRange'>
				<li class='set-lwa-datepicker lwa_today'>Today</li>
				<li class='set-lwa-datepicker lwa_yesterday'>Yesterday</li>
				<li class='set-lwa-datepicker lwa_last7days'>Last 7 Days</li>
				<li class='set-lwa-datepicker lwa_thismonth'>This Month</li>
				<li class='set-lwa-datepicker lwa_lastmonth'>Last Month</li>
				<li class='set-lwa-datepicker lwa_last3months'>Last 3 Months</li>
				<li class='set-lwa-datepicker lwa_thisyear'>This Year</li>
				<li class='set-lwa-datepicker lwa_lastyear'>Last Year</li>
				<li class='set-lwa-datepicker lwa_alltime'>All Time</li>
			</ul>
		</div>
		<div class='lwa_datepicker_overlay'></div>
		<?php } ?>
	<?php	
	}
	
	function reportPanel() { 
		global $db, $dashboards, $reports, $profile, $session, $validUserRequired;
		
		$q = $db->Execute("SELECT COUNT(*) FROM {$profile->tablename}");
		$rimt = $q->FetchRow(); # (R)ecords (I)n (M)ain (T)able
		if(!empty($rimt)) {
			$rimt = $rimt[0];
		} else {
			$rimt = 0;
		}
		
		$regip = $_SERVER['HTTP_HOST'];
		if (!$regip) {
			if ($HTTP_SERVER_VARS["SERVER_NAME"] != "") {
				$regip = $HTTP_SERVER_VARS["SERVER_NAME"];
			} else {
				$regip = $_SERVER['SERVER_ADDR'];
			}
		}
		
		$fullp = "http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
		$fullp = str_replace("register.php", "getlicense.php", $fullp);
		?>

		<iframe id='info_frame' style='' src='<?php echo LOGAHOLIC_INFO_URL; ?>?domain=<?php echo $profile->confdomain; ?>&version=<?php echo LOGAHOLIC_VERSION_NUMBER; ?>&server=<?php echo $_SERVER['HTTP_HOST']; ?>&edition=<?php echo LOGAHOLIC_BASE_EDITION; ?>&rimt=<?php echo $rimt; ?>&ir=<?php echo _LOGAHOLIC_REGISTERED; ?>&ia=<?php echo $session->isAdmin(); ?>&vur=<?php echo $validUserRequired; ?>&regip=<?php echo $regip; ?>&fullp=<?php echo $fullp; ?>&status=<?php echo LOGAHOLIC_VERSION_STATUS; ?>&ru=<?php echo $this->ReturnURL(); ?>&rand=<?php echo rand(0, 999); ?>&affiliate_id=<?php echo $this->affiliate_id; ?>'></iframe>
		
		<div id="report_panel">
			<div id="report_area_extension">
				<div class='report_options'>
					<span class='report_icon'><img src='images/pixel.gif'/></span>
					<div class='current_report'><label rel=''></label></div>
					<div class='current_options'></div>
				</div>
				<div class='report_area_usage_info' style='text-align: center; margin-top: 50px;'>
					Click a report icon to begin<br/>
					<img src='images/arrow_to_right.png' style='border: 0; margin-top: 50px; margin-bottom: 40px;' />
				</div>
				<div class='form_buttons'>
					<input id='closeReportOptions' class='noOption grey_submit' type='submit' value='Cancel' />
				</div>
				<?php	######### Parameters Explained #########
						# rimt = (R)ecords (I)n (M)ain (T)able #
						# ia = (I)s (A)dmin					   #
						# ir = (I)s (R)egistered			   #
						# vur = (V)alid (U)ser (R)equired	   #
						######################################## ?>
				<?php /* <iframe id='info_frame' style='background: transparent; display: none; width: 312px; height: 300px; border: 0; padding: 0; margin: 0; overflow: hidden;' src='<?php echo LOGAHOLIC_INFO_URL; ?>?domain=<?php echo $profile->confdomain; ?>&version=<?php echo LOGAHOLIC_VERSION_NUMBER; ?>&server=<?php echo $_SERVER['HTTP_HOST']; ?>&edition=<?php echo LOGAHOLIC_BASE_EDITION; ?>&rimt=<?php echo $rimt; ?>&ir=<?php echo _LOGAHOLIC_REGISTERED; ?>&ia=<?php echo $session->isAdmin(); ?>&vur=<?php echo $validUserRequired; ?>&regip=<?php echo $regip; ?>&fullp=<?php echo $fullp; ?>&status=<?php echo LOGAHOLIC_VERSION_STATUS; ?>&rand=<?php echo rand(0, 999); ?>'></iframe> */ ?>
			</div>
			
			<div id="report_area">
				<input class='search_in_reports' type='text' />
				<div class='clear_search_reports'></div>
				
				<?php echo DashboardList($dashboards); ?>
				
				<h2 class='search_results'><?php echo _SEARCH_RESULT; ?></h2>
				<div class='no_search_results'><?php echo _NO_SEARCH_RESULTS; ?></div>
				<ul class='s_results'></ul>
				<?php echo ReportArea($reports); ?>
				<div class='clear'></div>
				
				<h2 class='store_featured report-category' style='display: none;'><img src='images/arrow_down_darkgrey_square.png' class='report-category-arrow'/>Report Store Recommendations<a href='<?php echo LOGAHOLIC_REPORT_STORE_LOCATION; ?>'><?php echo _GET_MORE_REPORTS; ?></a></h2>
				<ul class='featured_icons' style='display: none;'></ul>
				
				<div class='clear'></div>
				
				<a target='_blank' class='reportstore_button' href='<?php echo LOGAHOLIC_REPORT_STORE_LOCATION; ?>?logaholic_url=<?php echo $this->ReturnURL(); if(!empty($this->affiliate_id)) { echo "&tracking={$this->affiliate_id}"; } ?>'><span><span><?php echo _GET_MORE_REPORTS; ?></span></span></a>
			</div>
			<div class='clear'></div>
		</div>
			
			<?php			
			if(isset($_GET["login"])){
				$fb_frame_src = "facebook_conf_iframe.php?conf={$profile->profilename}&login={$_GET["login"]}";
			}else{
				$fb_frame_src = "facebook_conf_iframe.php?conf={$profile->profilename}";
				
			}
			echo "<div id='facebookconf' title='Facebook configuration' style='margin:0px;padding:0px;'>
						<iframe id='facebookconf_iframe' src='$fb_frame_src' width='100%' height='100%' scrolling='auto' marginWidth='0' marginHeight='0' frameBorder='0'></iframe>
				  </div>";
			if(isset($_GET["fbframe"])){
				$this->create_fb_iframe("true");
			?>
			<script type="text/javascript">
				 $( "#facebookconf" ).dialog({
					autoOpen: true,
					modal: true,
					width:600,
					height:500,
					beforeClose: function(){
					url = "<?php echo $fb_frame_src; ?>";
					$("#facebookconf_iframe").attr('src',url);
					}
				});
			</script>
			<?php
			}else{
			?>
			<script type="text/javascript">
				 $( "#facebookconf" ).dialog({
					autoOpen: false,
					modal: true,
					width:600,
					height:500,
					beforeClose: function(){
						url = "<?php echo $fb_frame_src; ?>";
						$("#facebookconf_iframe").attr('src',url);
					}
				});
			</script><?php
			}
	}
	
	function create_fb_iframe($open = "false"){ 
		global $profile;
		if($open != "true"){
			echo "<button class='open_facebookconf' style='margin:5px;'>"._FACEBOOK_LOGIN_SETTINGS_WARNING."</button>";
		}
		
		?>
		<script type="text/javascript">
			<?php if($open == "false"){ ?>
			$( ".open_facebookconf" ).click(function() {
				$("#facebookconf").dialog("open");
				return false;
			});
			<?php }else{ ?>
				$("#facebookconf").dialog("open");
			<?php } ?>
		</script>
		<?php				
	}
	
	function getUpdateStatus() {
		global $profile;
		
		$updateprogress = fopen(logaholic_dir()."files/{$profile->profilename}_update.lwa.log", "r");
		$progress = 0;
		$current_file = '';
		$estimate_time = 0;
		while($line = fgets($updateprogress)) {
			if(strpos($line, '#') === false) {
				continue;
			}
			
			$line_parts = explode("|", str_replace(array(";", "\n", "\r"), array("", "", ""), $line));
			
			$progress += str_replace("#", "", $line_parts[0]);
			
			if(!empty($line_parts[2])) {
				$progress = 0;
				$current_file = str_replace(")...", "", str_replace("filename=", "", substr($line_parts[1], strpos($line_parts[1], "filename="))));
				$estimate_time = $line_parts[2];
			}
		}
		
		$filecounter = explode(",", getProfileData($profile->profilename, "{$profile->profilename}.update_data", "1,1"));
		$filecounter = $filecounter[1];
		$totalprogress = ((count($filecounter) - 1) * 100 + $progress) / count($filecounter);
		
		return array(
			'percentage' => $progress,
			'total_percentage' => $totalprogress,
			'estimate_time' => $estimate_time,
			'action' => $line_parts[1],
			'file' => $current_file
		);
	}
	
	function ReturnURL() {
		global $profile;
		$pagename = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
		$return_address = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") { $return_address .= "s"; }
			$return_address .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$return_address .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$return_address .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		$return_address = urlencode(str_replace($pagename . "?" . $_SERVER["QUERY_STRING"], "", $return_address)."get_reports.php?conf=".$profile->profilename);
		return $return_address;
	}
	
	function AffiliateID() {
		if (!isset($this->affiliate_id)) {
			$this->affiliate_id = getGlobalSetting('affiliate_id');
		}
		return $this->affiliate_id;	
	}
}
?>