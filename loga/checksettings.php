<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
$am="";
 if (strtolower(ini_get('output_buffering'))!='off' && ini_get('output_buffering')!='') {
	$am.= "<li>"._PHP_HAS_DEFAULT_OUTPUTBUFFERING_SETTING_P1." (".ini_get('output_buffering')."), "._PHP_HAS_DEFAULT_OUTPUTBUFFERING_SETTING_P2."<br><br>";   
 }
 if (strtolower(ini_get('display_errors'))=='off' || ini_get('display_errors')=='') {
	$am.= "<li>"._PHP_HAS_DISPLAYERRORS_TURNED_OFF_P1." (".ini_get('display_errors')."), "._PHP_HAS_DISPLAYERRORS_TURNED_OFF_P2."<br><br>";   
 } else {
	//$am.= "<li>Display errors: (".ini_get('display_errors').")<br><br>";   
 }
 if (ini_get('allow_url_fopen')!='1' && strtolower(ini_get('allow_url_fopen'))!='on') {
	$am.= "<li>"._PHP_HAS_ALLOWURLFOPEN_DISABLED_BY_DEFAULT_P1." (".ini_get('allow_url_fopen')."), "._PHP_HAS_ALLOWURLFOPEN_DISABLED_BY_DEFAULT_P2."<br><br>";   
 }
 if (strtolower(ini_get('safe_mode'))=='on' || ini_get('safe_mode')=='1') {
	$am.= "<li>"._PHP_IS_RUNNING_IN_SAFE_MODE_P1." ".ini_get('safe_mode')."). "._PHP_IS_RUNNING_IN_SAFE_MODE_P2."<br><br>"; 
 }
 
 $am.="<li>"._PHP_HAS_A_OPENBASEDIR_SETTING_P1." ".ini_get('open_basedir').".<br>"._PHP_HAS_A_OPENBASEDIR_SETTING_P2."<br><br>";
 

 if ($am!="") {
	echo _ADDITIONAL_NOTES.":<ul>$am</ul>";
 }
 
?>
