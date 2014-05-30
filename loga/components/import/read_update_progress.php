<?php
# This file will be called through AJAX

include_once("../../common.inc.php");

# If there is no conf, we stop output for update.
if(empty($conf)) {
	die(_ERROR_LOST_PROFILE_NAME);
}

# Get the last action's timestamp
$last_update_action = getProfileData($profile->profilename, $profile->profilename.".perlupdate_modtime", 0);

# If the current time minus the last action's timestamp is greater than 3600 seconds (1 hour), give an error and set perlupdate_running to 'no'.
if((time() - $last_update_action) > 3600) {
	setProfileData($profile->profilename, "{$profile->profilename}.perlupdate_running", "no");
	die(str_replace("%s", $profile->profilename, _PERL_UPDATE_STOPPED_UNEXPECTEDLY));
}

# Get the contents of the update log.
$updatelog = file_get_contents(logaholic_dir()."files/{$profile->profilename}_update_progress.lwa.log");

# Explode so we have an array containing each line.
$updatelog = explode("\n", $updatelog);

# Print the last line (We print the second-last line, since the last line is always empty).
echo $updatelog[(count($updatelog) - 2)];
?>