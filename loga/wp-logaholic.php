<?php
/*
Plugin Name: Logaholic Web Analytics - Advanced web stats
Plugin URI: http://www.logaholic.com/
Description: The Logaholic Wordpress plugin installs a free full featured web stats solution on your blog with a single click. It provides in-depth traffic statistics, visitor analytics, navigation analysis, keyword and referrer stats, visit trends, custom website statistics dashboards and much more.
Version: 3.0
Author: Logaholic
Author URI: http://www.logaholic.com/
License: Commercial
*/
?>
<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
?>
<?php
function logaholic_install_warning() {
	echo "<div id='logaholic-warning' class='updated fade'><p><strong>".__('Logaholic failed to install.')."</strong> ".sprintf(__('Set allow_url_fopen to true in the PHP ini file, or install cURL.'), "plugins.php")."</p></div>";
}
function installLogaholic() {
	if(createGlobalPHP() == true) {
		SetupLogaholic();
	}
}

function createTablenamePHP($profile) {
	if(!empty($profile->tablename)) {
		$real_path = realpath(__FILE__);
		$path = dirname($real_path);
		$gfile = $path . "/files/{$profile->profilename}_tables.php";
		
		if(!file_exists($gfile)) {
			$fp = fopen ($gfile,"w+");
			fwrite ($fp, "<?php \n");
			fwrite ($fp, "\$lg_tablename=\"{$profile->tablename}\";\n");
			fwrite ($fp, "?>");
			fclose ($fp);

			if(ini_get('allow_url_fopen') == true) {
				file_get_contents(WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));
				return true;
			} elseif(function_exists('curl_init')) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				$exec = curl_exec($ch);
				curl_close($ch);
				return true;
			} else {
				add_action('admin_notices', 'logaholic_install_warning');
				return false;
			}
		}
	}
	
	return false;
}

function createGlobalPHP() {
	$myErrors = new WP_Error();
	
	$real_path = realpath(__FILE__);
	$path = dirname($real_path);
	$gfile = $path . "/files/global.php";
	
	if(is_writable($path."/files/") == true) {
		if(!file_exists($gfile)) {
			$fp = fopen ($gfile,"w+");
			fwrite ($fp, "<?php \$DatabaseName=\"".DB_NAME."\";\n");
			fwrite ($fp, "\$databasedriver=\"mysql\";\n");
			fwrite ($fp, "\$mysqlserver=\"".DB_HOST."\";\n");
			fwrite ($fp, "\$mysqluname=\"".DB_USER."\";\n");
			fwrite ($fp, "\$mysqlpw=\"".DB_PASSWORD."\";\n");
			fwrite ($fp, "\$mysqlprefix=\"LWA_\";\n");
			fwrite ($fp, "?>");
			fclose ($fp);

			if(ini_get('allow_url_fopen') == true) {
				file_get_contents(WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));
				return true;
			} elseif(function_exists('curl_init')) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				$exec = curl_exec($ch);
				curl_close($ch);
				return true;
			} else {
				add_action('admin_notices', 'logaholic_install_warning');
				return false;
			}
		}
	} else {
		die("<span style='font-family: Helvetica, Arial, Sans-Serif;'>The files directory is not writable. Please go to your wordpress plugins folder on your server, then go to logaholic and make sure the files directory is writable.</span>");
	}
	return false;
}

function SetupLogaholic() {
	global $db, $databasedriver, $mysqlprefix;
	
	include("common.inc.php");
	
	# set up the new profile
	$profile = new SiteProfile();
	
	$profile->profilename = $_SERVER['HTTP_HOST'];
	if (empty($profile->profilename)) {
		echo "Invalid profilename: $profile->profilename\n";
		exit();
	}
	if (profileExists($profile->profilename)==true) {
		echo "This profile already exists.";
		exit();
	}
	
	$profile->confdomain = $_SERVER['HTTP_HOST'];
	// $profile->equivdomains = $this->equivdomains;
	
	$profile->trackermode=1;
	$profile->logfilefullpath = "";    
	$profile->splitlogs = 0;
	$profile->visitoridentmethod = 3;

	$profile->Save();
	createDataTable($profile);
	
	createTablenamePHP($profile);
	
	echo "Success: Created profile {$profile->profilename} for {$profile->confdomain} \n";
	
	setGlobalSetting("UserAuthenticationType", "logaholic");
	setGlobalSetting("UserAuthenticationType_Var", "");
	setGlobalSetting("IsWordPressInstall", "true");
}

function profileExists($name) {
	global $db;
	
	$q = $db->Execute("select profileid from ".TBL_PROFILES." where profilename=\"$name\"");
	if ($q->NumRows() > 0) {
		return true;    
	} else {
		return false;    
	}
}

function addLogaholicJavascriptTracker() {
	$w_path = WP_PLUGIN_URL."/".str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	
	echo "<!-- /* Logaholic Web Analytics Code */ -->
<script type=\"text/javascript\">
var lwa_profile = \"{$_SERVER[HTTP_HOST]}\";
var lwa_server = \"{$w_path}\";
document.write(unescape(\"%3Cscript type='text/javascript' src='\" + lwa_server + \"lwa.js'%3E%3C/script%3E\"));
</script>
<script type=\"text/javascript\">
var lwa = trackPage();
</script>";
	
	echo "<noscript><a href=\"{$w_path}logaholictracker.php?conf={$_SERVER['HTTP_HOST']}\"><img src=\"{$w_path}logaholictracker.php?conf={$_SERVER[HTTP_HOST]}\" alt=\"web stats\" border=\"0\" /></a> <a href=\"http://www.logaholic.com/\">Web Analytics</a> by Logaholic</noscript>";
	
}

function addLogaholicButton() {
	$wp_lg_user = wp_get_current_user();
	syncLogaholicUser($wp_lg_user);
	
	if($wp_lg_user->wp_capabilities['administrator'] == 1) {
		$lgpkey = md5($wp_lg_user->data->user_login) . ":" . md5($wp_lg_user->data->user_pass);
		echo "<li id='menu-logaholic' class='wp-first-item wp-has-submenu menu-top menu-top-first menu-icon-logaholic menu-top-last'>
			<div class='wp-menu-image' style='background: url(\"".WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__))."/images/icons/logaholiclogo.png\") no-repeat scroll center center transparent;'>
				<a href='".WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__))."?lgpkey={$lgpkey}'><br></a>
			</div>
			<div class='wp-menu-toggle'><br></div>
			<a tabindex='1' class='wp-first-item wp-has-submenu menu-top menu-top-first menu-icon-logaholic menu-top-last' href='".WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__))."?lgpkey={$lgpkey}' target='_blank'>Logaholic Stats</a>
		</li>";
	}
}

function syncLogaholicUser($wp_lg_user) {
	global $db, $databasedriver, $mysqlprefix;
	if(empty($wp_lg_user)) {
		$wp_lg_user = wp_get_current_user();
	}
	
	include_once("common.inc.php");
	if($wp_lg_user->wp_capabilities['administrator'] == 1) {
		// var_dump($wp_lg_user); // user_login    user_pass     user_nicename     user_email
		$db->Execute("INSERT INTO ".TBL_USERS." (`username`, `name`, `password`, `email`, `isAdmin`) VALUES (".$db->Quote($wp_lg_user->data->user_login).", ".$db->Quote(md5($wp_lg_user->data->user_pass)).", ".$db->Quote($wp_lg_user->data->user_nicename).", ".$db->Quote($wp_lg_user->data->user_email).", 1) ON DUPLICATE KEY UPDATE `password` = ".$db->Quote(md5($wp_lg_user->data->user_pass)).";");
		// var_dump("INSERT INTO ".TBL_USERS." (`username`, `name`, `password`, `email`, `isAdmin`) VALUES (".$db->Quote($wp_lg_user->data->user_login).", ".$db->Quote(md5($wp_lg_user->data->user_pass)).", ".$db->Quote($wp_lg_user->data->user_nicename).", ".$db->Quote($wp_lg_user->data->user_email).", 1) ON DUPLICATE KEY UPDATE userid = LAST_INSERT_ID(userid);");
	} else {
		$db->Execute("DELETE FROM ".TBL_USERS." WHERE `username` = ".$db->Quote($wp_lg_user->data->user_login));
	}
}

add_action ( 'wp_head', 'addLogaholicJavascriptTracker' );
add_action ( 'plugins_loaded', 'installLogaholic' );
add_action ( 'adminmenu', 'addLogaholicButton' );
?>