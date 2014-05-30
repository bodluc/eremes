<?php
include_once("common.inc.php");

include_once("top.php");

Class ReportUpdater {
	public $profile;
	
	function __construct() {
		global $profile;
		
		$this->profile = $profile;
	}
	
	function getAllocatedReports() {
		$allocated_reports = getProfileData($this->profile->profilename, "{$this->profile->profilename}.allocated_reports", serialize(array()));
		$allocated_reports = unserialize($allocated_reports);
		
		$reports = array();
		foreach($allocated_reports as $ar) {
			$r = getProfileData($this->profile->profilename, "{$this->profile->profilename}.report.{$ar}", serialize(""));
			$r = unserialize($r);
			
			if(isset($r['code']) == false) {
				continue;
			}
			
			$r = base64_decode($r['code']);
			$r = substr($r, strpos($r,"\$reports["));
			$r = substr($r,0,(strpos($r,");")+2));
			
			$r = eval($r);
		}
		
		return $reports;
	}
	
	function CheckIfUpdate($reportArray) {
		global $storeReports;
		
		$doUpdate = false;
		
		if(isset($reportArray['MinimumVersion']) && isset($reportArray['ReportVersion'])) {
			if($reportArray['MinimumVersion'] < LOGAHOLIC_VERSION_NUMBER) {
				$doUpdate = true;
			}
		}
		
		return $doUpdate;
	}
	
	function Update($reportFilename) {
		
	}
}

$ru = new ReportUpdater();

$allocated_reports = $ru->getAllocatedReports();

$reports_to_update = array();

$updatetable = "<table id='Updateable'>";

foreach($allocated_reports as $label => $r) {
	if(!isset($r['Filename'])) {
		continue;
	}
	
	if($ru->CheckIfUpdate($r) == true) {
		$updatetable .= "<tr class='matchesMinimumVersion' version='{$r['ReportVersion']}' rel='{$r['Filename']}' >";
	} else {
		$updatetable .= "<tr src='{$r['ReportVersion']}' rel='{$r['Filename']}' >";
	}
	
	$updatetable .= "<td>".constant($label)."</td><td><a class='update-report' style='display: none;' href='#update-report'>Update</a><textarea style='display: none;' class='reportcode'></textarea></td>";
	
	// $updatescripts .= "".PHP_EOL;
	
	$updatetable .= "</tr>";
}
$updatetable .= "</table>";

// $updatescripts .= "});".PHP_EOL;
?>

<script type='text/javascript'>
$(document).ready(function() {
reportVersions = {};
<?php foreach($allocated_reports as $label => $r) {
	if(!isset($r['Filename'])) {
		continue;
	}
?>
	reportVersions['<?php echo $r['Filename']; ?>'] = <?php echo $r['ReportVersion']; ?>;

	var scriptTag = document.createElement('script');
	scriptTag.type = 'text/javascript';
	var source = 'http://www.logaholic.com/logadl/report_delivery/getReport.php';
	scriptTag.src = source;
	scriptTag.src += "?product=<?php echo $r['Filename']; ?>&profilename=<?php echo $profile->profilename; ?>&<?php echo md5('reinstall'); ?>=<?php echo md5("1"); ?>";
	
	// When we add this script to the head, the request is sent off.
	document.getElementsByTagName('head')[0].appendChild(scriptTag);
<?php } ?>

	$(".update-report").live("click", function() {
		$.post("includes/storeReports.php", {"purchased" : $(this).next('textarea').val(), "conf" : '<?php echo $profile->profilename; ?>'}, function(result) {});
		
		return false;
	});
});

function getReport(received) { // Receiver of the installing report
	if(received.bundlename != undefined) {
		reportJSON = received.reports;
	} else {
		reportJSON = received;
	}
	
	ShowHideUpdateButton(reportJSON.reportname, reportJSON.code);
	
	$("#Updateable tr[rel='" + reportJSON.file + "']").find(".reportcode").html(JSON.stringify(reportJSON));
}

function ShowHideUpdateButton(reportname, reportcode) {
	$.ajax({
		url: 'includes/getReportVersionFromCode.php?conf=<?php echo $profile->profilename; ?>',
		type: 'POST',
		async: false,
		data: {
			code: reportcode
		},
		success: function(result) {
			var name = result.split("\n")[0];
			var version = result.split("\n")[1];
			
			if(version > reportVersions[name]) {
				$("#Updateable tr[rel='" + name + "']").find(".update-report").show();
			} else {
				$("#Updateable tr[rel='" + name + "']").find(".update-report").remove();
				$("#Updateable tr[rel='" + name + "']").find("textarea").before("No update available.");
			}
		}
	});
}
</script>

<style type='text/css'>
h2, .updateReportDesc { width: 600px; margin: 0 auto; padding-bottom: 1em; }
#Updateable { width: 600px; margin: 0 auto; padding: 6px 10px; background-color: #E0E0E0; border: 1px solid silver; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; }
.update-report { display: block; background: url("images/gradient_bg_blue.png") repeat-x scroll left top #1970B4; width: 100px; height: 26px; line-height: 26px; font-size: 14px; color: #FFF; text-decoration: none; padding: 0 6px; text-align: center; float: right; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; }
</style>

<h2>Update Reports</h2>
<p class='updateReportDesc'>
If there are report updates available, the button will show next to the corresponding report.
Simply click the button, and the report will be updated.
</p>
<?php
echo $updatetable;
?>