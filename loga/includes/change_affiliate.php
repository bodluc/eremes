<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
if (isset($_POST['new_affiliate_id'])) {
    include_once "../common.inc.php";
	setGlobalSetting("affiliate_id",$_POST['new_affiliate_id']);
	
	echoNotice("Your affiliate ID has been changed.");
    
} else {
	echoWarning("Changing the affiliate ID was failed.");
}
?>