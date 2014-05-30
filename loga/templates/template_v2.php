<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
Class Template_v2 extends Template {
	function HTMLheadTag($headAddition = "") {
		global $conf, $baseurl, $profiles, $ourpath, $from, $to, $new_ui;
		
		$htmlheadtag = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">".PHP_EOL;
		$htmlheadtag .= "<html>".PHP_EOL;
		$htmlheadtag .= "<head>".PHP_EOL;
		$htmlheadtag .= "	<META NAME=\"ROBOTS\" CONTENT=\"NOINDEX, NOFOLLOW\">".PHP_EOL;
		$htmlheadtag .= "	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />".PHP_EOL;
		$htmlheadtag .= "	<title>Logaholic Web Analytics - {$conf}</title>".PHP_EOL;
		if(isset($baseurl) && ($baseurl)) { $htmlheadtag .= "<base href=\"{$baseurl}\">".PHP_EOL; }
		
		$htmlheadtag .= "	<link rel=\"icon\" href=\"favicon.ico\" type=\"image/x-icon\">".PHP_EOL;
		$htmlheadtag .= "	<link rel=\"shortcut icon\" href=\"favicon.ico\" type=\"image/x-icon\">".PHP_EOL;
		
		$htmlheadtag .= "	<link type=\"text/css\" rel=\"stylesheet\" href=\"templates/template_v2.css\">".PHP_EOL;
		
		$htmlheadtag .= "	<link type=\"text/css\" rel=\"stylesheet\" href=\"components/jquery/css/smoothness/jquery-ui-1.7.2.custom.css\">".PHP_EOL;
		
		$htmlheadtag .= "	<link type=\"text/css\" rel=\"stylesheet\" href=\"components/jquery/jqplot/jquery.jqplot.min.css\" />".PHP_EOL;
        $htmlheadtag .= "	<link type=\"text/css\" rel=\"stylesheet\" href=\"components/jquery/css/tablesorter/style.css\" />".PHP_EOL;
		
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/js/jquery-1.5.1.min.js\"></script>".PHP_EOL;
		
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/js/jquery-ui-1.7.2.custom.min.js\"></script>".PHP_EOL;
		
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/js/jquery-scrollTo.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/jquery/js/jquery.tablesorter.min.js\"></script>".PHP_EOL;
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"components/json/json2.js\"></script>".PHP_EOL;
		
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"jsfunctions.js\"></script>".PHP_EOL;

		if ($profiles==1) {
			$htmlheadtag .=  "	<script type=\"text/javascript\" src=\"{$ourpath}loghint.js\"></script>".PHP_EOL;
		}
		$htmlheadtag .= "	<script type=\"text/javascript\">";
		$htmlheadtag .= "var conf_name = '{$conf}';";
		$htmlheadtag .= "var from_date = '{$from}';";
		$htmlheadtag .= "var to_date = '{$to}';";
		$htmlheadtag .= "var new_ui = '{$new_ui}';";
		$htmlheadtag .= "var actionmenu_url = 'includes/actionmenu.php';";
		$htmlheadtag .= "	</script>".PHP_EOL;
		
		$htmlheadtag .= $headAddition;
		
		$htmlheadtag .= "</head>".PHP_EOL;
		
		echo $htmlheadtag;
	}
	
	function BodyStart() {
		global $new_ui, $conf;
		
		$currentFile = $_SERVER["SCRIPT_NAME"];
		$parts = Explode('/', $currentFile);
		$currentFile = $parts[count($parts) - 1];
		
		echo "<body onload='finishpage();'>".PHP_EOL;
	}
	function Navigation(){
		global $profile;
		if(isset($profile) && $profile->profileloaded == true) {
			$this->ReportNavigation(); // We'll print the top report navigation here. We also decide which navigation to print in this function.
		} else {
			$this->GlobalNavigation(); // We'll print the top global navigation here. We also decide which navigation to print in this function.
		}			
	}
	function ReportNavigation() {
		global $validUserRequired, $session, $conf, $dashboards, $reports, $from, $to, $new_ui, $lang, $available_langs;
		
		$currentFile = $_SERVER["SCRIPT_NAME"];
		$parts = Explode('/', $currentFile);
		$currentFile = $parts[count($parts) - 1];
		
		echo "<div id='north'>";
		
				echo $this->Notifications();
				echo $this->CoBranding(); // When to display (partner) logo or not ?>
				<div id="top_button_bar">
					<div id="top_menubar_left">
						<ul class="dropdown top_navigation">
							<li><a title="Profiles" id="profiles" href="profiles.php?editconf=<?php echo $conf;?>"><?php echo _MANAGE_PROFILES;?></a></li>
							<li><a href="#"><?php echo $conf; ?></a>
								<ul>
								<?php if (!$validUserRequired || @$session->canEditProfiles()) { ?>
								<li><a class="leave_page" title="Settings" id="settings" href="profiles.php?editconf=<?php echo $conf;?>&edit=1"><?php echo _EDIT_PROFILE;?></a></li>
								<?php } if (!$validUserRequired || @$session->canUpdateLogs()) { ?>
								<li><a class="leave_page" href="profiles.php?editconf=<?php echo $conf; ?>&amp;del=1"><?php echo _MAINTENANCE; ?></a></li>
								<li><a class="leave_page" href="update.php?conf=<?php echo $conf; ?>"><?php echo _UPDATE_NOW;?></a></li>								
								<?php } ?>
								</ul>
							</li>
							
							<li><a class="leave_page" href='notes.php?conf=<?php echo $conf; ?>&timestamp=<?php echo time(); ?>&donote=create'>Notes</a></li>
						</ul>
					</div>
					
					<div id="messages">
						<div class="ui-state-highlight ui-corner-all">
							<a href="index.php?new_ui=1&conf=<?php echo $conf; ?>"><?php echo _CLICK_HERE; ?></a><?php echo " to go to the new interface"; ?>.
						</div>
					</div>
					
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
									echo "<li class='language'><a href=\"".$_SERVER['PHP_SELF']."?".$query_string."&amp;lang=$value&amp;nocache=1&amp;conf={$_REQUEST["conf"]}\"><img src='images/flags/".ucwords($value).".png' style='border:0px;' />" . constant($l_value) ."</a></li>";
								}
							echo "</ul>";							
							?>
							</li>
							<li><a target="_blank" title="<?php showVersion(); ?>" id="help" href="http://logaholic.com/manual/"><?php echo _HELP;?></a></li>
							<?php if ($validUserRequired==true && $session->logged_in==true) { ?>
							<li><a href="user_login/logout.php" title="<?php echo _CURRENTLY_LOGGED_IN_AS.' '.$session->username ?>"><?php echo "Logout";?></a></li>
							<?php } ?>
						</ul>
					</div>
				</div>
			</div>
				<div class="menubar">
					<ul id="menu">
						<?php if ((!$validUserRequired) || $session->logged_in) { ?>

						<!--<li <?php newselec("index.php");?>><a href="index.php<?php echo "?from=$from&amp;to=$to&amp;conf=$conf"; ?>"><span <?php newrselec("index.php");?>><?php iconselect("index.php");?> Today</span></a></li>-->

						<li <?php newselec("index.php");?>><a href="index.php<?php echo "?from=$from&amp;to=$to&amp;conf=$conf"; ?>"><span <?php newrselec("index.php");?>><?php iconselect("index.php"); echo _SUMMARY_REPORTS;?> </span></a></li>

						<li <?php newselec("trends.php");?>><a href="trends.php<?php echo "?from=$from&amp;to=$to&amp;conf=$conf"; ?>"><span <?php newrselec("trends.php");?>><?php iconselect("trends.php"); echo _TRENDS;?>  </span></a></li>

						<li <?php newselec("page.php");?>><a href="page.php<?php echo "?from=$from&amp;to=$to&amp;conf=$conf"; ?>"><span <?php newrselec("page.php");?>><?php iconselect("page.php"); echo _PAGE_ANALYSIS?> </span></a></li>

						<li <?php newselec("clicktrail.php");?>><a href="clicktrail.php<?php echo "?from=$from&amp;to=$to&amp;conf=$conf"; ?>"><span <?php newrselec("clicktrail.php");?>><?php iconselect("clicktrail.php"); echo _CLICK_TRAILS;?> </span></a></li>

						<li <?php newselec("funnels.php");?>><a href="funnels.php<?php echo "?from=$from&amp;to=$to&amp;conf=$conf"; ?>"><span <?php newrselec("funnels.php");?>><?php iconselect("funnels.php"); echo _FUNNEL_ANALYSIS;?> </span></a></li>
						<?php
						if (file_exists("testcenter.php") && _LOGAHOLIC_EDITION!=4) { ?> 
						<li <?php newselec("testcenter.php");?>><a href="testcenter.php<?php echo "?from=$from&amp;to=$to&amp;conf=$conf"; ?>"><span <?php newrselec("testcenter.php");?>><?php iconselect("testcenter.php"); echo _TEST_CENTER;?> </span></a></li>
						<?php } 
						if (file_exists("surveys.php")) { ?>
						<li <?php newselec("surveys.php");?>><a href="surveys.php<?php echo "?from=$from&amp;to=$to&amp;conf=$conf"; ?>"><span <?php newrselec("surveys.php");?>><?php iconselect("surveys.php"); echo _SURVEYS;?> </span></a></li>
						<?php } ?>

						<?php if ((!$validUserRequired) || ($session->canUpdateLogs())) { ?>
						<li <?php newselec("update.php");?>><a href="update.php<?php echo "?conf=$conf&amp;page=".$_SERVER['PHP_SELF']; ?>"><span <?php newrselec("update.php");?>><?php iconselect("update.php"); echo _UPDATE_NOW;?> </span></a></li>
						<?php } ?>
						<?php }?>
					</ul>
					<?php if (strpos($_SERVER['SCRIPT_NAME'], "filters.php")===FALSE) {
						echo"<div class=\"controls\" style=\"border:0px solid red;height:50px;\"></div>";
					} ?>
				</div>
			<div class="controls" style="border:0px solid red;height:50px;"></div>
		<?php
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

					<div id="messages">
						<div class="ui-state-highlight ui-corner-all">
							<a href="profiles.php?new_ui=1&editconf=<?php echo $editconf; ?>"><?php echo _CLICK_HERE; ?></a><?php echo " to go to the new interface"; ?>.
						</div>
					</div>
					
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
									echo "<li class='language'><a href=\"".$_SERVER['PHP_SELF']."?".$query_string."&amp;lang=$value&amp;nocache=1&amp;conf={$_REQUEST["conf"]}\"><img src='images/flags/".ucwords($value).".png' style='border:0px;' />" . constant($l_value) ."</a></li>";
								}
							echo "</ul>";							
							?>
							</li>
							<li><a target="_blank" title="Help" id="help" href="http://logaholic.com/manual/"><?php echo _HELP;?></a></li>
							<?php if ($validUserRequired==true && $session->logged_in==true) { ?>
							<li><a href="user_login/logout.php" title="<?php echo _CURRENTLY_LOGGED_IN_AS.' '.$session->username ?>"><?php echo "Logout";?></a></li>
							<?php } ?>
						</ul>
					</div>
				</div>
			</div>
		<?php
	}
}
?>