<?php
include_once("common.inc.php");

if(!empty($_REQUEST['dashboardname'])) { $dashboardname = $_REQUEST['dashboardname']; }
if(!empty($_REQUEST['extra_param'])) { $extra_param = $_REQUEST['extra_param']; } else { $extra_param = false; }

$reportoptions = array("daterangeField");

$optionvalues = $_REQUEST;

foreach($reportoptions as $reportoption) {
	if($reportoption == "daterangeField") {
		if(!empty($optionvalues['minimumDate']) && !empty($optionvalues['maximumDate'])) {
			$minDate = $optionvalues['minimumDate'];
			$maxDate = $optionvalues['maximumDate'];
		} else {
			$minDate = date(GetCustomDateFormat(), strtotime($from));
			$maxDate = date(GetCustomDateFormat(), strtotime($to));
		}
		
		// $minDate = date("d M Y", strtotime($minDate));
		// $maxDate = date("d M Y", strtotime($maxDate));
		?>
		<label for='daterangeField'><?php echo _DATE_RANGE; ?></label>
		<input type='text' class='noOption' id='daterangeField' name='daterangeField' value='<?php echo $minDate; ?> - <?php echo $maxDate; ?>' />
		<?php
		// echo "<div class='dateRangePickerWrapper'><label for='{$reportoption}'>"._DATE_RANGE."</label><input type='text' class='noOption' value='".$minDate." - ".$maxDate."' id='{$reportoption}' /></div>";
		echo "<input type='hidden' class='isDefault' id='minimumDate' name='minimumDate' value='{$minDate}' />";
		echo "<input type='hidden' class='isDefault' id='maximumDate' name='maximumDate' value='{$maxDate}' />";
	}
	
	echo "<br/><br/>";
}

if(!empty($dashboardname)) { ?>
<script type='text/javascript'>$("#report_area_extension .report_icon img").attr('src', $("#report_area .dashboard.report_icon[name='<?php echo $dashboardname; ?>'] img").attr('src'));</script>
<?php }

echo "<input name='conf' id='conf' value='{$profile->profilename}' type='hidden' />";

if($extra_param == 'global') { // We are using global options
} else { // We are using dashboard options
	if(!empty($_REQUEST['dashboardname']) && $_REQUEST['dashboardname'] != 'global') {// Delete Dashboard
		echo "<a style='position: absolute; margin-top: 100px;' class='edit-dashboard' rel='{$dashboardname}'><div style='width: 16px; height: 16px; float: left; margin-right: 3px; background: url(images/icons/edit_grey.png) no-repeat top center;' /><span style='line-height: 16px; display: block; white-space: nowrap;'>"._EDIT_DASHBOARD."</span></a>";
		echo "<a style='position: absolute; margin-top: 126px;' class='delete-dashboard' rel='{$dashboardname}'><div style='width: 16px; height: 16px; float: left; margin-right: 3px; background: url(images/icons/delete_grey.png) no-repeat top center;' /><span style='line-height: 16px; display: block; white-space: nowrap;'>"._DELETE_DASHBOARD."</span></a>";
	}
}
?>