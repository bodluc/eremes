<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include_once("components/geoip/open_geoip.php");
include_once "common.inc.php";


function askUser() {
	global $content, $profile;
	?>
	<div class="make-pdf">
	<h1>Your encoded content will be sent to www.logaholic.com and converted to a PDF document.</h1>
	<form method=post id="pdfform" action="http://www.logaholic.com/pdf/download.php">
	<input type=hidden name="content" value="<?php echo base64_encode($content); ?>">
	<input type=hidden name="name" value="<?php echo $profile->confdomain.".pdf"; ?>">
	<input type=hidden name="version" value="<?php echo LOGAHOLIC_VERSION_NUMBER; ?>">
	<input type=submit id="submitbutton" value="Download PDF">
	</form>
	<br />
	<br />
	<br />
	Alternatively, you can locally generate a printer friendly HTML version of your workspace reports<br />
	<form method=post id="htmlform" action="pdf.php?conf=<?php echo $profile->profilename;?>" target="_blank">
	<input type=hidden name="content" value="<?php echo base64_encode($content); ?>">
	<input type=hidden name="name" value="<?php echo $profile->confdomain.".pdf"; ?>">
	<input type=submit value="HTML version">
	</form>
	</div>
	<?php
}


$template->HTMLheadTag();
$template->BodyStart();
$template->LoginForm();

if (isset($_POST['content'])) {
	# we want to print the content locally
	echo base64_decode($_POST['content']);
} else {
	?>
	<script type="text/javascript">
	$(document).ready(function() {
		$("#creating").remove();
	});
	</script>
	<h1 id="creating">Encoding Content... please wait</h1>
	<?php
	flush();	
	ob_start();
	?>
	<style type='text/css'>
@page :left {
  margin-left: 2cm;
  margin-right: 2cm;
}

@page :right {
  margin-left: 2cm;
  margin-right: 2cm;
}
	</style>
	<script type="text/javascript">
	$(document).ready(function() {
		$("a").removeAttr("href");
		$("a").removeAttr("onclick");
		$("#mapArea").remove();
		
		var totalHeight = 0;
		var allowedHeight = 1280;
		
		$(".readheight").each(function() {
			//$(this).append($(this).height());
			if(($(this).outerHeight() + totalHeight) >= allowedHeight) {
				$(this).css('page-break-before','always');
				totalHeight = $(this).outerHeight();
			} else {
				totalHeight += $(this).outerHeight();
			}
		});
		
	});
	</script>
	<?php
	$dashboard = json_decode(stripslashes(getProfileData($profile->profilename,$profile->profilename.".dashboards.Autosaved Workspace")),true);
	echo "<div id='print'>";
	echo "<h1 class='h1-title'>Web Analytics Report for {$profile->confdomain}</h1>";
	foreach($dashboard["reports"] as $grid) {		
		foreach($grid as $report) {
			$labels = $report["name"];
			$r="";
			$r = new $reports[$report["label"]]["ClassName"]();
			parse_str($report["url"],$options);
			foreach($options as $option => $value) {
				$r->$option = $value;
				// echo "$option = $value<br>";
			}
			if(isset($r->minimumDate)) {
				$r->from = strtotime($r->minimumDate);
			}
			if(isset($r->maximumDate)) {
				$r->to = strtotime($r->maximumDate);
			}
			if(!isset($r->trafficsource)){
			$r->trafficsource = null;
				$r->applytrafficsource = false;
			}else{
				$r->applytrafficsource = true;
			}
			$r->displayReportLabel = true;
			$r->displayReportButtons = false;
			$r->sortTable=false;
			echo "<div class='report readheight'>";
			$r->DisplayReport();
			echo "</div>";
		}	
	}

	echo "</div>";
	echo "<div style='text-align:center;padding:10px;font-size:10px;'>Report created by Logaholic Web Analytics http://www.logaholic.com/</div>";
	$content = ob_get_clean();
	askUser();
}
echo "</body></html>";
?>