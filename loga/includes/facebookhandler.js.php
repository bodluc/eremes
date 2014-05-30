<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
$dont_close_session = true;
include "../common.inc.php";

// include the class file for the checks
include_once "checkfacebookconf.php";

// Check if we got a username or email saved on the profiles global settings.
if($_GET['action']=="checkloginexists") {
	$fb = new checkFacebookConf($_GET["conf"],$_GET["email"]);
	$fb->CheckProfileCurrentLogin();
}

// Saves the login settings found on the facebook.logaholic.com database in a 'buffer'
if($_GET['action']=="save_fb_login_data") {
	$data = json_decode(stripslashes($_GET["serverdata"]));
	$save["id"] = $data->userid;
	$save["token"] = $data->token;
	$save = serialize($save);
	
	setProfileData($_GET["conf"], $_GET["conf"].".FacebookServerApi.".$_GET["email"] , $save);
	echo true;
}

// Check if your the access token of your email or username has the correct permissions that are needed for collecting data from Facebook.
if($_GET['action']=="check_perms_data") {
	$logdata = stripslashes($_REQUEST["logdata"]);
	$logdata = json_decode($logdata);
	$json = file_get_contents("https://graph.facebook.com/{$logdata->userid}/permissions?access_token={$logdata->token}");
	$fb = new checkFacebookConf($_GET["conf"],$_GET["email"]);
	$fb->CheckPermissons($json);
}

// Compares the data from your profile with that from the faceboook database.
if($_GET['action'] == "compareData"){
	$fb = new checkFacebookConf($_GET["conf"],$_GET["email"]);
	$fb->compareLoginData();
}

// If your saved data is different then that from the facebook database this data must be updated.
if($_GET['action'] == "updateData"){
	$fb = new checkFacebookConf($_GET["conf"],$_GET["email"]);
	$fb->updateLoginData();
}

// delete the 'buffer' data.
if($_GET['action'] == "deleteFacebookDatabaseData"){
	$fb = new checkFacebookConf($_GET["conf"],$_GET["email"]);
	$fb->deleteFacebookDatabaseData();
	$profile->Load($_REQUEST["conf"]);	
}

// If there are no saves on the current profile. Save the login settings.
if($_GET['action'] == "firstGlobalSettingsSave"){
	$fb = new checkFacebookConf($_GET["conf"],$_GET["email"]);
	$fb->firstGlobalSettingsSave($_GET["serverdata"]);
}

// save the login the users uses. Can be the username or email adress to access the facebook data.
if($_GET['action'] == "save_login_input"){
	$fb = new checkFacebookConf($_GET["conf"],$_GET["email"]);
	$fb->saveLogin();
}
?>