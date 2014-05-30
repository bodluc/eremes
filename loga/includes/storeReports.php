<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include_once("../common.inc.php");

$report_array = getProfileData($profile->profilename, "{$profile->profilename}.downloaded_reports");
$allocated_reports = getProfileData($profile->profilename, "{$profile->profilename}.allocated_reports");

if(!empty($report_array)) {
	$report_array = unserialize($report_array);
} else {
	$report_array = array();
}

if(!empty($allocated_reports)) {
	$allocated_reports = unserialize($allocated_reports);
} else {
	$allocated_reports = array();
}

if(!empty($_POST['purchased'])) {
	$posted_data = $_POST['purchased'];
	if(isset($posted_data[0])) { // Is this product a bundle?
		foreach($posted_data as $data_entry) {
			if(empty($data_entry['file'])) { continue; }
			
			$data_entry['installDate'] = time();
			setProfileData($profile->profilename, "{$profile->profilename}.report.{$data_entry['file']}", serialize($data_entry));
			
			if(!in_array($data_entry['file'], $report_array)) {
				$report_array[] = $data_entry['file'];
			}
			if(!in_array($data_entry['file'], $allocated_reports)) {
				$allocated_reports[] = $data_entry['file'];
			}
		}
		setProfileData($profile->profilename, "{$profile->profilename}.downloaded_reports", serialize($report_array));
		setProfileData($profile->profilename, "{$profile->profilename}.allocated_reports", serialize($allocated_reports));
	} else { // This product is not a bundle.
		$previous_install = getProfileData($profile->profilename, "{$profile->profilename}.report.{$posted_data['file']}", '');
		if(!empty($previous_install) && $previous_install['is_trial'] == 1 && $posted_data['is_trial'] == 1) {
			$posted_data['installDate'] = $previous_install['installDate'];
		} else {
			$posted_data['installDate'] = time();
		}
		setProfileData($profile->profilename, "{$profile->profilename}.report.{$posted_data['file']}", serialize($posted_data));
		if(!in_array($posted_data['file'], $report_array)) {
			$report_array[] = $posted_data['file'];
			setProfileData($profile->profilename, "{$profile->profilename}.downloaded_reports", serialize($report_array));
		}
		
		if(!in_array($posted_data['file'], $allocated_reports)) {
			$allocated_reports[] = $posted_data['file'];
			setProfileData($profile->profilename, "{$profile->profilename}.allocated_reports", serialize($allocated_reports));
		}
	}
}
?>