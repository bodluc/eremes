<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include_once('../common.inc.php');

if(!empty($conf)) {
	$downloads = getProfileData("{$profile->profilename}", "{$profile->profilename}.downloaded_reports");
	$allocated = getProfileData("{$profile->profilename}", "{$profile->profilename}.allocated_reports");
	
	$downloads = unserialize($downloads);
	$allocated = unserialize($allocated);
	
	if(!empty($_REQUEST['delete_download'])) {
		foreach($downloads as $download_key => $download) {
			if($_REQUEST['delete_download'] == $download) {
				unset($downloads[$download_key]);
				break;
			}
		}
		foreach($allocated as $allocated_key => $allocate) {
			if($_REQUEST['delete_download'] == $allocate) {
				unset($allocated[$allocated_key]);
				break;
			}
		}

		$report = getProfileData("{$profile->profilename}", "{$profile->profilename}.report.{$download}");
		$report = unserialize($report);
		if(!empty($report)) {
			$unlock_key = $report['unlock_key'];
			$productID = $report['productID'];
			echo "<script type='text/javascript'>
				var injectScript = document.createElement('script');
				injectScript.type = 'text/javascript';
				var source = 'http://www.logaholic.com/logadl/report_delivery/UninstalReport.php';
				injectScript.src = source;
				injectScript.src += '?unlock_key={$unlock_key}';
				injectScript.src += '&product_id={$productID}';
				
				document.getElementsByTagName('head')[0].appendChild(injectScript);
			</script>";
		}
		
		$downloads = serialize($downloads);
		setProfileData("{$profile->profilename}", "{$profile->profilename}.downloaded_reports", $downloads);
		
		$allocated = serialize($allocated);
		setProfileData("{$profile->profilename}", "{$profile->profilename}.allocated_reports", $downloads);
		
		deleteProfileData("{$profile->profilename}", "{$profile->profilename}.report.{$download}");		
	}
}
?>