<?php
# We want to check Perl availability and such, so we include this file.
include_once("components/import/import.php");

# This class comes from the above include
$importClass = new Import();

$canDoPerlUpdate = getGlobalSetting("canDoPerlUpdate", 'N/A');

# If set_update_pref is set in the url, we want to (re)set our update method preference (regular or perl)
if(!empty($_REQUEST['set_update_pref'])) {
	# If use_perl is set to 1, we want to set our preference to perl
	if($_REQUEST['use_perl'] == 1) {
		$update_preference = 'perl';
	} else {
		$update_preference = 'regular';
	}
	
	# We don't want to trigger the reset stuff, so we say no update is running .
	setProfileData($profile->profilename, "{$profile->profilename}.update_running", "no");
	
	# Save our preference
	setProfileData($profile->profilename, "{$profile->profilename}.updatePreference", $update_preference);
}

# Fetch our preference
$update_preference = getProfileData($profile->profilename, "{$profile->profilename}.updatePreference", "regular");

if ($update_preference == 'perl' && $canDoPerlUpdate == 'N/A') {
	$perlCheck = $importClass->AvailableCheck();

	if($perlCheck['hasError'] !== false) {
		foreach($perlErrors as $perlError) {
			echo $perlError."<br/>";
		}
		
		$canDoPerlUpdate = 0;
	} else {
		$canDoPerlUpdate = 1;
	}
	setGlobalSetting("canDoPerlUpdate", $canDoPerlUpdate);
}

# If no preference was set, we prompt the user to set one.
if(empty($update_preference) && !$running_from_command_line) {
	# We are not updating yet!
	setProfileData($profile->profilename, "{$profile->profilename}.update_running", "no");
	
	# We check whether this profiles uses Apache log files, if he does, we ask the user wants to use Perl.
	# If the this profiles does not use Apache log files, we set our update method preference to regular.
	$perl_logfiles = $importClass->getLogFilesArray();
	$detected_logformat = formatOfLogFile($perl_logfiles[0]);
	
	if($canDoPerlUpdate == 1 && ($detected_logformat['ClassName'] == 'ApacheCombinedCookieLogParser' || $detected_logformat['ClassName'] == 'ApacheCommonNoReferrerLogParser' || $detected_logformat['ClassName'] == 'LooseApacheCommonLogParser' || $detected_logformat['ClassName'] == 'ApacheCombinedLogParser')) {
		echo "<div style='margin: 0 auto; width: 400px;'>";
		echo "<h3>New Update Method Available</h3>";
		echo "<p style='margin-bottom: 24px;'>";
			echo "We have developed a very fast update process that uses Perl.<br/>However, it takes some time to set up.<br/>Would you like to try it out?<br/>";
		echo "</p>";
		echo "<a style='background-color: #333333; border-radius: 4px 4px 4px 4px; color: #FFFFFF; padding: 5px 10px; text-decoration: none;' href='update.php?conf={$profile->profilename}&use_perl=0&set_update_pref=1'>No, I prefer the regular update method</a>";
		echo "<a style='margin-left: 25px; background: none repeat scroll 0 0 #1970B4; border-radius: 4px 4px 4px 4px; color: #FFFFFF; padding: 5px 10px; text-decoration: none;' href='update.php?conf={$profile->profilename}&use_perl=1&set_update_pref=1'>Yes, please</a>";
		echo "</div>";
		exit;
	} else {
		setProfileData($profile->profilename, "{$profile->profilename}.updatePreference", 'regular');
	}
}

# If we use perl, include our perl handler and exit update.php
if ($update_preference == 'perl' && $canDoPerlUpdate == 1) {
	# Prevent a reset message for regular update method.
	setProfileData($profile->profilename, "{$profile->profilename}.update_running", "no");
	
	# Give reset message if needed for perl
	HandleRunningStatus('perl');
	
	$show_progressbar = true;
	include_once("importlog.php");
	exit;
} else {
	setProfileData($profile->profilename, "{$profile->profilename}.update_running", "yes");
	setProfileData($profile->profilename, "{$profile->profilename}.perlupdate_running", "no");
}
?>