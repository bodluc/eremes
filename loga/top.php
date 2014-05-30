<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include_once "common.inc.php";

if(!isset($profile) && isset($conf)) {
	$profile = new SiteProfile($conf);
}

if(!isset($headAddition)) { $headAddition = ''; }

$template->HTMLheadTag($headAddition); // The default content of the <head> tag, including an optional addition.

$template->BodyStart();

$template->LoginForm(); // Display a Login Form, if needed.

$template->Navigation();
?>