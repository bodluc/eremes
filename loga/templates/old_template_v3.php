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
		
		$htmlheadtag .= "<html>".PHP_EOL;
		$htmlheadtag .= "<head>".PHP_EOL;
		$htmlheadtag .= "	<META NAME=\"ROBOTS\" CONTENT=\"NOINDEX, NOFOLLOW\">".PHP_EOL;
		$htmlheadtag .= "	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />".PHP_EOL;
		$htmlheadtag .= "	<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" >".PHP_EOL;
		$htmlheadtag .= "	<title>Logaholic Web Analytics - {$conf}</title>".PHP_EOL;
		if(isset($baseurl) && ($baseurl)) { $htmlheadtag .= "<base href=\"{$baseurl}\">".PHP_EOL; }
		
		$htmlheadtag .= "	<link rel=\"icon\" href=\"favicon.ico\" type=\"image/x-icon\">".PHP_EOL;
		$htmlheadtag .= "	<link rel=\"shortcut icon\" href=\"favicon.ico\" type=\"image/x-icon\">".PHP_EOL;
		
		$htmlheadtag .= "	<link type=\"text/css\" rel=\"stylesheet\" href=\"components/jquery/css/smoothness/jquery-ui-1.8.custom.css\" />".PHP_EOL;
		$htmlheadtag .= "	<link type=\"text/css\" rel=\"stylesheet\" href=\"templates/template_v3.css\" />".PHP_EOL;
		
		$htmlheadtag .= "	<link type=\"text/css\" rel=\"stylesheet\" href=\"components/jquery/jqplot/jquery.jqplot.min.css\" />".PHP_EOL;
		$htmlheadtag .= "	<link type=\"text/css\" rel=\"stylesheet\" href=\"components/jquery/css/jquery.daterangepicker.css\" />".PHP_EOL;
		$htmlheadtag .= "	<link type=\"text/css\" rel=\"stylesheet\" href=\"components/jquery/datatables/css/datatables.css\" />".PHP_EOL;
		
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/js/jquery-1.6.4.min.js\"></script>".PHP_EOL;
		
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
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/jqplot/plugins/jqplot.meterGaugeRenderer.min.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/jqplot/plugins/jqplot.funnelRenderer.min.js\"></script>".PHP_EOL;
		
		$htmlheadtag .= "	<script type=\"text/javascript\" language=\"javascript\" src=\"components/jquery/datatables/js/jquery.dataTables.min.js\"></script>".PHP_EOL;
		
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/js/jquery-ui-1.8.13.custom.min.js\"></script>".PHP_EOL;
		
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/js/jquery-scrollTo.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/js/jquery.tablesorter.min.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/js/jquery.daterangepicker.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/json/json2.js\"></script>".PHP_EOL;		
		
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
		$htmlheadtag .= "<![endif]-->";
		
		$htmlheadtag .= $headAddition;
		
		$htmlheadtag .= "</head>".PHP_EOL;
		
		echo $htmlheadtag;
	}
	
	function BodyStart() {	
		echo "<body onload='finishpage();'>".PHP_EOL;
	}
	
	function Messages() {
		global $conf;
		?>
		<div id="messages">
			<span id="next_message"></span>
			<div class="ui-state-highlight ui-corner-all">
				&nbsp;
			</div>
			
		</div>
		<script type="text/javascript" charset="utf-8">
		var messages = [];
		messages.push('<a href="http://www.logaholic.com/video/30.php" target="_blank"><?php echo _WATCH_VIDEO_TUT; ?></a> <?php echo strtolower(_FOR); ?> Logaholic 3.0');
		<?php if (isset($conf)) { $u = "index.php?new_ui=0&conf=$conf"; } else { $u = "profiles.php?new_ui=0"; } ?>
		messages.push('<a href="<?php echo $u; ?>"><?php echo _CLICK_HERE; ?></a> <?php echo addslashes(_IF_USE_OLD_UI); ?>');
		<?php if (_LOGAHOLIC_REGISTERED!=1) { ?>
		messages.push('<a href="getlicense.php"><?php echo _REGISTER_NOW; ?></a> <?php echo _FOR_MORE_FEATURES; ?></a>');
		<?php } ?>
		$("#messages > div").html(messages[1]);
		</script>
		
		<?php
	}
	function ReportNavigation() {
		global $validUserRequired, $session, $conf, $dashboards, $reports, $from, $to, $available_langs, $lang,$db;
		
		$currentFile = $_SERVER["SCRIPT_NAME"];
		$parts = Explode('/', $currentFile);
		$currentFile = $parts[count($parts) - 1];
		
		echo $this->Notifications();
		
		echo "<div id='north'>";
				$this->CoBranding(); // When to display (partner) logo or not ?>
				<div id="top_button_bar">
					<div id="top_menubar_left">						
						<ul class="dropdown top_navigation">
							<li class="manage-profiles-list">
								<a class="leave_page" title="Profiles" id="profiles" href="profiles.php?&editconf=<?php echo $conf;?>"><?php echo _MANAGE_PROFILES;?></a>
								<ul>
									<?php
										$q = $db->Execute("SELECT profilename FROM ". TBL_PROFILES . " ORDER BY lastused DESC LIMIT 6");
										while($data = $q->fetchRow()){
											if($data["profilename"] != $conf){
												echo "<li><a href='?conf={$data["profilename"]}&new_ui=1'>".$data["profilename"]."</a></li>";
											}
										}
									?>
								</ul>
							</li>
							<li><a href="profiles.php?editconf=<?php echo $conf;?>&edit=1"><?php echo $conf; ?></a>
								<ul>
								<?php if (!$validUserRequired || @$session->canEditProfiles()) { ?>
								<li><a class="leave_page" title="Settings" id="settings" href="profiles.php?editconf=<?php echo $conf;?>&edit=1"><?php echo _EDIT_PROFILE;?></a></li>
								<?php } if (!$validUserRequired || @$session->canUpdateLogs()) { ?>
								<li><a class="leave_page" href="profiles.php?editconf=<?php echo $conf; ?>&amp;del=1"><?php echo _MAINTENANCE; ?></a></li>
								<li><a class="leave_page" href="update.php?conf=<?php echo $conf; ?>"><?php echo _UPDATE_NOW;?></a></li>								
								<?php } ?>
								</ul>
							</li>
							
							<?php if(@$currentFile == 'v3.php') { ?>
							<li class='summon-reports'><a href="#report-area"><?php echo _REPORTS; ?></a></li>
							<?php } else { ?>
							<li><a href="v3.php?conf=<?php echo $conf; ?>"><?php echo _REPORT_PANEL; ?></a></li>
							<?php } ?>
							
							<li><a class="leave_page" href='notes.php?conf=<?php echo $conf; ?>&timestamp=<?php echo time(); ?>&donote=create'><?php echo _NOTES; ?></a></li>
						</ul>
					</div>
					
					<?php $this->Messages(); ?>
					
					<div id="top_menubar_right">
						<ul class="dropdown top_navigation">
							<li class='first language-picker'>
							<?php echo "<a href=\"#\"><img src='images/flags/".ucwords($lang).".png' style='border:0px;' />". _LANGUAGE . "</a>"; ?>
							<?php
							echo "<ul>";
								foreach($available_langs as $value) {  
									$l_value = "_" . strtoupper($value);
									$query_string = $_SERVER['QUERY_STRING'];
									$query_str_arr = explode("&", $query_string);
									if(count($query_str_arr) > 1) {
										foreach($query_str_arr as $q_str_key => $q_str_val) {
											$tmp_str = explode("=", $q_str_val);
											if($tmp_str[0] == 'lang' || $tmp_str[0] == 'nocache') {
												unset($query_str_arr[$q_str_key]);
											}
										}
									}
									$query_string = implode("&", $query_str_arr);
									echo "<li class='language'><a href=\"".$_SERVER['PHP_SELF']."?".$query_string."&amp;lang=$value&amp;nocache=1&amp;conf={$conf}\"><img src='images/flags/".ucwords($value).".png' style='border:0px;' />" . constant($l_value) ."</a></li>";
								}
							echo "</ul>";							
							?>
							</li>
							<li class="help-list">
								<a target="_blank" title="<?php showVersion(); ?>" id="help" href="http://logaholic.com/manual/"><?php echo _HELP;?></a>
								<ul>
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
		<?php
	}
	
	function reportPanel() { 
		global $dashboards, $reports, $profile;
		
		$affiliate_id = getGlobalSetting('affiliate_id');
		
		$pagename = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
		$return_address = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") { $return_address .= "s"; }
			$return_address .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$return_address .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$return_address .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		$return_address = urlencode(str_replace($pagename . "?" . $_SERVER["QUERY_STRING"], "", $return_address)."get_reports.php")."&conf={$profile->profilename}";
		?>
		<div id="report_panel">
			<div id="report_area_extension">
				<!--
				<div class='report_info'>
					<span></span>
					<img />
					<p></p>
				</div>
				-->
				<div class='report_options'>
					<span class='report_icon'><img src='images/pixel.gif'/></span>
					<div class='current_report'></div>
					<div class='current_options'></div>
				</div>
				<div class='form_buttons'>
					<input id='closeReportOptions' class='grey_submit' type='submit' value='Cancel' />
				</div>
			</div>
			
			<div id="report_area">
				<input class='search_in_reports' type='text' />
				<a target='_blank' class='reportstore_button' href='http://www.logaholic.com/get_reports.php?logaholic_url=<?php echo $return_address; if(!empty($affiliate_id)) { echo "&affiliate_id={$affiliate_id}"; } ?>'><span><span><?php echo _GET_MORE_REPORTS; ?></span></span></a>
				<?php echo DashboardList($dashboards); ?>
				<h2 class='search_results'><?php echo _SEARCH_RESULT; ?></h2>
				<div class='no_search_results'><?php echo _NO_SEARCH_RESULTS; ?></div>
				<?php echo ReportArea($reports); ?>
				<div class='clear'></div>
				<div class='store_list_wrapper'>
					<?php echo StoreList(); ?>
					<a target='_blank' style='margin-top: 22px;' class='reportstore_button' href='http://www.logaholic.com/get_reports.php?logaholic_url=<?php echo $return_address; if(!empty($affiliate_id)) { echo "&affiliate_id={$affiliate_id}"; } ?>'><span><span><?php echo _GET_MORE_REPORTS; ?></span></span></a>
					<div class='clear'></div>
				</div>
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
	
	function GlobalNavigation() {
		global $loginSystemExists, $userAuthenticationType, $session, $validUserRequired, $conf, $available_langs, $lang, $editconf;
		
		?>
			<div id="north">
				<?php echo $this->Notifications(); ?>
				<?php echo $this->CoBranding(); // When to display (partner) logo or not ?>
				<div id="top_button_bar">
					<div id="top_menubar_left">
						<ul class="dropdown top_navigation">
							<li><a href="profiles.php<?php echo "?editconf=$editconf"; ?>"><?php echo _MANAGE_PROFILES;?></a></li>
							<?php if (!empty($editconf)) { ?>
							<li><a href="#"><?php echo $editconf; ?></a>
								<ul>
								<li><a href="index.php?conf=<?php echo $editconf;?>"><?php echo _VIEW_STATS;?></a></li>
								<?php if (!$validUserRequired || @$session->canUpdateLogs()) { ?>
								<li><a href="profiles.php?editconf=<?php echo $editconf; ?>&amp;del=1"><?php echo _MAINTENANCE; ?></a></li>
								<li><a href="update.php?conf=<?php echo $editconf; ?>"><?php echo _UPDATE_NOW;?></a></li>
								<?php } ?>
								</ul>
							</li>
							<?php } ?>
							<?php if ((($loginSystemExists) && ($userAuthenticationType == USER_AUTHENTICATION_NONE)) ||  (($validUserRequired) && $session->isAdmin())) { ?>
							<li><a href="user_login/admin.php"><?php echo _USER_ADMINISTRATION;?></a></li>
							<?php }
							if (LOGAHOLIC_VERSION_STATUS != "release" && file_exists("user_login/sendmail.php")) {
								if ($loginSystemExists && $session->isAdmin()) { ?>
							<li><a href="user_login/sendmail.php"><?php echo _MAIL_MANAGEMENT;?></a></li>
							<?php }
							}
							if (!$validUserRequired || $session->isAdmin()) { ?>      
							<li><a href="settings.php<?php echo "?conf=$conf"; ?>"><?php echo _GLOBAL_SETTINGS;?></a></li>
							<?php } ?>
						</ul>
					</div>
					
					<?php $this->Messages(); ?>
					
					<div id="top_menubar_right">
						<ul class="dropdown top_navigation">
						<li class='first language-picker'>
							<?php echo "<a href=\"#\"><img src='images/flags/".ucwords($lang).".png' style='border:0px;' />". _LANGUAGE . "</a>"; ?>
							<?php
							echo "<ul>";
								foreach($available_langs as $value) {  
									$l_value = "_" . strtoupper($value);
									$query_string = $_SERVER['QUERY_STRING'];
									$query_str_arr = explode("&", $query_string);
									if(count($query_str_arr) > 1) {
										foreach($query_str_arr as $q_str_key => $q_str_val) {
											$tmp_str = explode("=", $q_str_val);
											if($tmp_str[0] == 'lang' || $tmp_str[0] == 'nocache') {
												unset($query_str_arr[$q_str_key]);
											}
										}
									}
									$query_string = implode("&", $query_str_arr);
									echo "<li class='language'><a href=\"".$_SERVER['PHP_SELF']."?".$query_string."&amp;lang=$value&amp;nocache=1&amp;conf={$conf}\"><img src='images/flags/".ucwords($value).".png' style='border:0px;' />" . constant($l_value) ."</a></li>";
								}
							echo "</ul>";							
							?>
							</li>
							<li class="help-list">
								<a target="_blank" title="<?php showVersion(); ?>" id="help" href="http://logaholic.com/manual/"><?php echo _HELP;?></a>
								<ul>
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
		<?php
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
}
?>