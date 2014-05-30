<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
require_once "../common.inc.php";

if(!empty($_GET["fb_id"]) && !empty($_GET["fb_token"]) && !empty($_GET["fb_email"])){

	$facebookApi["id"] = $_GET["fb_id"];
	$facebookApi["token"] = $_GET["fb_token"];
	
	setProfileData($_GET["conf"], $_GET["conf"]."facebookApi".$_GET["fb_email"] , serialize($facebookApi));
}else{

	echo "Your facebook login was not found. Please authorize logaholic to your data. <a href='http://apps.facebook.com/logaholic/' target='_blank'>Follow this link</a>. After authorization come back and save your profile again.";
}
?>