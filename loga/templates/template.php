<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
Class Template {
	function _construct() {
		//hmm, this doesn't work
		//$this->affiliate_id = getGlobalSetting('affiliate_id');
	}

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
			
		$htmlheadtag .= "	<script type=\"text/javascript\" src=\"jsfunctions.js\"></script>".PHP_EOL;

		if ($profiles==1) {
			$htmlheadtag .=  "	<script type=\"text/javascript\" src=\"{$ourpath}loghint.js\"></script>".PHP_EOL;
		}
		$htmlheadtag .= "	<script type=\"text/javascript\">";
		$htmlheadtag .= "var conf_name = '{$conf}';";
		$htmlheadtag .= "var from_date = '{$from}';";
		$htmlheadtag .= "var to_date = '{$to}';";
		$htmlheadtag .= "var new_ui = '{$new_ui}';";
		$htmlheadtag .= "	</script>".PHP_EOL;
		
		$htmlheadtag .= $headAddition;
		
		$htmlheadtag .= "</head>".PHP_EOL;
		
		echo $htmlheadtag;
	}
	
	function BodyStart() {
		echo "<body onload='finishpage();'>".PHP_EOL;
	}
	
	function CoBranding() {
		global $noheader,$session,$database;
		
		if (file_exists("mylogo.php") && !isset($noheader)) {
			include_once "mylogo.php";
		}
		
		if (_LOGAHOLIC_EDITION==2) {
			if(file_exists("includes/account_status.php")){
				include_once "includes/account_status.php";
			}
		}
	}
	
	function LoginForm() {
		global $validUserRequired, $skiploginform, $session, $userAuthenticationType, $form,$conf;
		
		if (($validUserRequired) && (!@$skiploginform)) {
			if (!$session->logged_in) { 
				if ((!$session->logged_in) && ($userAuthenticationType == USER_AUTHENTICATION_LOGAHOLIC)) { 
					$this->CoBranding();
					echo "<div id=\"loginform\"><h2>Logaholic Web Analytics</h2>\n";
					if($form->num_errors > 0){
						 echo "<font size=\"2\" color=\"#ff0000\">".$form->num_errors." "._ERRORS_FOUND."</font>";
					}
					?>
					<form method="POST">
					<table  border="0" cellspacing="0" cellpadding="3">
					<tr><td><?php echo _USERNAME;?>:</td><td><input type="text" name="login_user" maxlength="100" value="<?php echo $form->value("login_user"); ?>"></td><td><?php echo $form->error("login_user"); ?></td></tr>
					<tr><td><?php echo _PASSWORD;?>:</td><td><input type="password" name="login_pass" maxlength="100" value="<?php echo $form->value("login_pass"); ?>"></td><td><?php echo $form->error("login_pass"); ?></td></tr>
					<tr><td colspan="2" align="left"><input type="checkbox" name="login_remember" checked>
					<font size="2"><?php echo _REMEMBER_ME_NEXT_TIME;?> &nbsp;&nbsp;&nbsp;&nbsp;
					<input type="hidden" name="sublogin" value="1">
					<input type="submit" value="Login"></td></tr>
					</table>
					</form>
					<p><a class="smalllinks2" href="user_login/forgotpass.php">Forgot Password ?</a></p>
					</div>
					
					<?php
					exit();  // abort because login is required.
				}
			} else {				
				if (isset($conf) && !empty($conf) && ($conf != "Profiles") && ($conf != "newcnf") && (!$session->isAdmin()) && (!$session->canAccessProfile($conf))) {					
					?>
					<html><body>
							<p class="indentbody"><font color="red"><?php echo _SORRY;?>, "<?php echo $conf; ?>" <?php echo _ISNT_A_VALID_PROFILE;?></font></p>
					</body></html>
					<?php
					exit();
				}
			}
		}
	}
	
	function Notifications() {
		global $system_warning, $lang, $session, $validUserRequired, $database;
		
		echo "<div id='notifications_and_warnings'>";
		
		echo "<div class='close_warning'></div>";
				
		
		// If we have some kind of an error message from another area before including top, then push it out here.
		if (isset($_SESSION["errormessage_init"])) {
			echo "<div class=\"warning ui-state-error ui-corner-all\">".$_SESSION["errormessage_init"]."</div>\n";
			unset($_SESSION["errormessage_init"]);
		}
		
		if (isset($system_warning)) {
			echoWarning($system_warning."\n");
		}
		
		if ($lang=="italian") {
			echoNotice("Beta Translation: The $lang translation has been mostly made by humans. Please suggest improvements by using the <a href=\"javascript:poptranslator()\" title=\"http://www.logaholic.com/tools/translator/\">Logaholic Translation Tool</a>. Thank you!");    
		}
		
		if ($lang=="french" || $lang=="spanish" || $lang=="portuguese") {
			echoWarning("Alpha Translation: The $lang translation was automatically generated via machine translation tools. Please suggest improvements by using the <a href=\"javascript:poptranslator()\" title=\"http://www.logaholic.com/tools/translator/\">Logaholic Translation Tool</a>. Thank you!");    
		}
		
		//if (!$validUserRequired || $session->isAdmin()) { echo "<iframe frameborder=\"0\" class=\"check4updates\" id=\"c4u\" src=\"http://updates.logaholic.com/check4updates.php?ver=".LOGAHOLIC_VERSION_NUMBER."&product="._LOGAHOLIC_EDITION."&host=".$_SERVER['HTTP_HOST']."\"></iframe>"; }
		
		housekeeping();
		
		echo "</div>";
		
		if (strpos($_SERVER['PHP_SELF'],"profiles.php")!==false) {
			if(($validUserRequired) && $session->isAdmin()) {
				echo "<script type='text/javascript'>"; ?>
					var notifications_url = "<?php echo "http://updates.logaholic.com/check4updates.php?ver=".LOGAHOLIC_VERSION_NUMBER."&product="._LOGAHOLIC_EDITION."&host=".$_SERVER['HTTP_HOST']; ?>";
					var NotificationsScript = document.createElement('script');
					NotificationsScript.type = 'text/javascript';
					NotificationsScript.src = notifications_url;
					document.getElementsByTagName('head')[0].appendChild(NotificationsScript);
				<?php echo "</script>";
			}
		}
	}
	
	function ReportNavigation() {
		return false;
	}
	
	function GlobalNavigation() {
		return false;
	}
}
?>