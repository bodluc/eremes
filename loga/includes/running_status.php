<?php
include "../common.inc.php";
if (isset($_REQUEST['status'])) {
	setProfileData($conf,"$conf.update_running",$_REQUEST['status']);	
}
if (isset($conf)) {
	echo "current status for $conf is: " .getProfileData($conf,"$conf.update_running");
}

?>