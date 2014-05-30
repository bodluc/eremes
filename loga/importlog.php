<?php
set_time_limit(86400);
ob_start();

# These are the files we need to include.
include_once("common.inc.php");

include_once("logparser.inc.php");
include_once("includes/mysql-functions.php");

include_once("components/import/import.php");

# Startup the class
$import = new Import();
$import->manage_keys=true;

# Do the availability checks; if one of them fails, the script will die and return the error message.
$canDoPerlUpdate = getGlobalSetting("canDoPerlUpdate", 'N/A');
if($canDoPerlUpdate == 'N/A') {
	$perlCheck = $import->AvailableCheck();
	if($perlCheck['hasError'] !== false) {
		foreach($perlErrors as $perlError) {
			echo $perlError."<br/>";
		}
		
		setGlobalSetting("canDoPerlUpdate", 0);
		
		exit;
	} else {
		setGlobalSetting("canDoPerlUpdate", 1);
	}
}

# We are running a perl update, so we set a flag.
setProfileData($profile->profilename, "{$profile->profilename}.perlupdate_running", 'yes');

# We want to pass someone to v3.php, so updating is out of the user's face.
/*
echo "<script type='text/javascript'>setTimeout(function() {
	var loc = window.location.href;
	var parts = loc.split('/');
	parts[parts.length - 1] = 'v3.php?conf={$profile->profilename}';
	loc = parts.join('/');
	
	window.location = loc;
}, 500);</script>";
lgflush();
*/
# We want to be able to update while the user is somewhere else, or is gone.
ignore_user_abort(true);

# Open the Update Progress Log.
$updatelog = fopen(logaholic_dir()."files/{$profile->profilename}_update_progress.lwa.log", "a+");

# We want to know how long an update takes.
$st_time = time();

# Set force, if needed.
if(!empty($_REQUEST['force'])) {
	$force = true;
} else {
	$force = false;
}

//$import->AlterTablesAgain();

# Get the log files in array format
$files = $import->GetLogFilesArray();

# lets get started
$import->LogProcess("this one has original indexes", false);

$import->correctProfileTablename();

$import->EnableKeys($profile->tablename, false);

# For each log file, we'll parse it, and insert it into the database, among other stuff; keep reading to discover more.
foreach($files as $file) {

	$load = sys_getloadavg();
	$import->LogProcess("System load {$load[0]} {$load[1]} {$load[2]}");
	$start_file_processing = time();
	
	# We set a profile setting so we can keep track how long update is running for the current file.
	setProfileData($profile->profilename, $profile->profilename.".perlupdate_modtime", time());
	
	# This variable is being set to let the user know which file we're currently updating.
	$human_readable_file = explode("/", $file);
	$human_readable_file = $human_readable_file[(count($human_readable_file) - 1)];
	
	# Set the configuration file location
	$config_file = logaholic_dir()."files/{$profile->profilename}_config";
	
	# Set the perl script command
	$perl_script = "perl ".logaholic_dir()."components/import/preparse.plx {$config_file}";
	//echo $perl_script;
	
	# Set the preparsed log file location (The file where Perl writes to).
	$new_log_file = "{$mysqltmp}{$profile->profilename}_lwa_log";
	
	# Create the configuration file.
	$import->WriteConfigFile($config_file, $file, $new_log_file);
	$size = round((filesize($file)/1024)/1024,2);
	$import->LogProcess(_PARSING_LOG_FILE." ($size mb) ...", false);
	
	# Execute the Perl script
	exec($perl_script, $output);
	
	$took = $import->thisTook($start_file_processing);
	
	# Get the already existing bandwidth data, if any.
	$bandwidth = getProfileData($profile->profilename, $profile->profilename.".bandwidthData", array());
	if(!empty($bandwidth)) {
		# Unserialize the data we just fetched.
		$bandwidth = unserialize($bandwidth);
	}

	# For each line of output coming from our perl script, we'll parse it.
	foreach($output as $val) {
		# If the line defines the last known log position, set it in a PHP variable.
		if(strpos($val, 'lastlogpos:') !== false) {
			$v = explode(':', $val);
			$lastlogpos = $v[1];
		}
		
		# If the line defines the first log line, set it in a PHP variable.
		if(strpos($val, 'firstlogline:') !== false) {
			$v = explode(':', $val);
			$firstlogline = $v[1];
		}
		
		# Merge the bandwidth data coming from Perl with the existing data.
		if(strpos($val, 'bandwidth:') !== false) {
			$v = explode(':', $val);
			$v = explode('=', $v[1]);
			
			# If we already have bandwidth data for the current date/time, add it.
			if(isset($bandwidth[$v[0]])) {
				$bandwidth[$v[0]] = $bandwidth[$v[0]] + $v[1];
			} else {
				# Else we want to set it for this date/time.
				$bandwidth[$v[0]] = $v[1];
			}
		}
	}

	# If there is no last log position, set it to zero.
	if(!isset($lastlogpos)) {
		$lastlogpos = 0;
	}
	
	# If there is no first log line, make it empty.
	if(!isset($firstlogline)) {
		$firstlogline = '';
	}

	# Serialize the bandwidth data.
	$bandwidth = serialize($bandwidth);
	
	# Save the bandwidth data, first log line and last log position.
	setProfileData($profile->profilename, $profile->profilename.".bandwidthData", $bandwidth);
	setProfileData($profile->profilename, "lastlogpos.".md5($file), $lastlogpos);
	setProfileData($profile->profilename, "firstlogline.".md5($file), $firstlogline);
	setProfileData($profile->profilename, "lastmodtime.".md5($file), filemtime($file));
	
	# Import the data parsed by perl with Load Data Infile in MySQL
	$import->ImportLog($new_log_file);
	$took = $import->thisTook($took);
	
	# Insert the data in the corresponding tables.
	$import->insertIDs();
	$took = $import->thisTook($took);
	
	# Insert the visitor ID's
	$import->insertVisitorIDs();
	
	# Insert the ID's in the main table.
	$import->insertNormalized();
	$took = $import->thisTook($took);
	
	# We're done with our log table; delete it.
	$db->Execute("DROP TABLE IF EXISTS {$profile->tablename}log");
	
	if($force === true) {
		# If force is true, move the original log file.
		
		if(is_dir($profile->logfilefullpath)) {
			$at = $profile->logfilefullpath.str_replace($profile->logfilefullpath, '', $file);
			$donefile = $profile->logfilefullpath.'done/'.str_replace($profile->logfilefullpath, '', $file);
		} else {
			$at = $file;
			$x = explode("/", $file);
			$fname = $x[count($x) - 1];
			unset($x[count($x) - 1]);
			$donefile = implode("/", $x).'/done/'.$fname;
			unset($x, $fname);
		}
		
		if (rename("{$at}", "{$donefile}")) {
			echoConsoleSafe(_MOVED_FILE_TO." $donefile<P>\n", true);
		} else {
			echo "<span style='color: #F00;'>".str_replace("%s2", $donefile, str_replace("%s1", $at, _MOVING_OF_LOG_FILE_FAILED_DONE_NOT_WRITABLE))."</span>";
		}
	}	
	unlink($new_log_file);
	$import->LogProcess("Finished procssing {$human_readable_file}", false);
	$tottime = time() - $start_file_processing;
	$took = $import->thisTook($start_file_processing);
	$speed = $import->num_import_lines/$tottime;
	$import->LogDuration("Done!", $speed, $tottime);
	$import->LogProcess("Speed was $speed lines per second");
	$import->LogProcess("---------------------------------------------------------", false);
}
$import->LogProcess("Rebuilding index on main table...", false);
$import->EnableKeys($profile->tablename, true);
$import->correctProfileTablename();
$import->EnableKeys($profile->tablename, true);
$took = $import->thisTook($took);



$import->LogProcess(_FINALIZING_UPDATE."...", false);


if ($import->num_import_lines > 0) {
	# Do update summaries for all the data we have just imported.
	include_once("update_summaries.php");
}

# We are done with Perl update; unflag it.
setProfileData($profile->profilename, "{$profile->profilename}.perlupdate_running", 'no');
$took = $import->took($st_time);
$totaltook = $import->sec2time($took);
$import->LogProcess("[Finished]".str_replace("%s1", $took, _DONE_UPDATE_TOOK_SECONDS." (".$totaltook.")"), false);
lgflush();
?>