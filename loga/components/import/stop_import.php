<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2012 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
require_once("../../common.inc.php");
if (isset($profile)) {
	if (@$_REQUEST['start']==1) {
		setProfileData($profile->profilename, "{$profile->profilename}.stop_update", false);
	} else {
		setProfileData($profile->profilename, "{$profile->profilename}.stop_update", true);
		echo "Update Stopped";
	}
}
?>