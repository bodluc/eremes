<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
error_reporting(0);
ob_start();

@include_once("common.inc.php");
?>
// <script>
$(document).ready( function() {
	$(".dropdown > li").live({
		mouseenter: function() {
			var dropdownElement = $(this);
			dropdown_timer = setTimeout(function () {
				dropdownElement.find('.dropdown-list').show();
			}, 100);
		},
		mouseleave: function() {
			clearTimeout(dropdown_timer);
			$(this).find('.dropdown-list').hide();
		}
	});
});
// </script>
<?php
if(empty($conf)) { exit(); }
/* Dashboard icons starts here */
/* This statement threads Dashboard Icon names and images together in an array and prepares that array to be used in the Report Panel. */
if (is_dir($dir = "images/icons/dashboards/")) {
    if ($dh = opendir($dir)) {
		$c = 0;
        while (($file = readdir($dh)) !== false) {
			if(substr($file,-4) == ".png") {
				$iconnames[$c]['url'] = "images/icons/dashboards/".$file;
				$iconnames[$c]['name'] = substr($file,0,-4);
				$c++;
			}
        }
        closedir($dh);
    }
}



$update_running = getProfileData($profile->profilename, "{$profile->profilename}.perlupdate_running", "no");
$update_data = getProfileData($profile->profilename, "{$profile->profilename}.update_data", "1,1");
$update_data = explode(",", $update_data);


	
$notes = @checkNotes(5, true);

# We'll get all the file-based reports here.
$class_reports = array();
if (is_dir($class_dir = "reports/")) {
    if ($dh = opendir($class_dir)) {
		$c = 0;
        while (($file = readdir($dh)) !== false) {
			if(substr($file,-4) == ".php") {
				$class_reports[] = substr($file,0,-4);
			}
        }
        closedir($dh);
    }
}

# Fetching the downloaded reports here...
$downloaded_reports = getProfileData($profile->profilename, "{$profile->profilename}.downloaded_reports");
$downloaded_reports = unserialize($downloaded_reports);

# We'll fetch all dashboards for this profile here
$sql = "SELECT * FROM `".TBL_GLOBAL_SETTINGS."` WHERE `Name` LIKE '{$profile->profilename}.dashboards.%' AND `Profile` = '{$profile->profilename}'";
$result = $db->Execute($sql);
function getDashboardDataArray($dashboardstr) { return json_decode($dashboardstr); } //return $subarr = explode("||",$dashboardstr); }
$dashboards = array();

ob_end_clean();
?>

// <script>

var notes = {};
<?php
	# Define the notes array in javascript
	if(!empty($notes)) {
		$i = 0;
		foreach($notes as $note) { ?>
			notes['<?php echo $i; ?>'] = {};
			notes['<?php echo $i; ?>']['day'] = "<?php echo $note['day']; ?>";
			notes['<?php echo $i; ?>']['note'] = "<?php echo $note['note']; ?>";
			notes['<?php echo $i; ?>']['shown'] = 0;
<?php $i++; } } ?>

var overall_update_perc = 0;

// Set the default daterange
var defaultOptions = {
	minimumDate: "<?php echo date(GetCustomDateFormat(),strtotime("01 ".date("F")." ".date("Y"))); ?>",
	maximumDate: "<?php echo date(GetCustomDateFormat(),time()); ?>"
};

var ReportPanelShadingTimer;
var dropdown_timer;

var class_reports = {};
var classified_reports = {};
var classed_reports = {};
// Define all file-based reports
<?php if(!empty($class_reports)) { echo 'class_reports = ';echo json_encode($class_reports); echo ';'; } ?>
// Define ALL reports; downloaded and file-based
<?php if(!empty($reports)) { echo 'classified_reports = ';echo json_encode($reports); echo ';'; } ?>

// Create a reports array with an integer key
var report_counter = 0;
for(var classified_report in classified_reports) {
	classed_reports[report_counter] = classified_reports[classified_report]['ClassName'];
	report_counter++;
}

// Define the downloaded reports
var downloaded_reports = {};
<?php
	if(!empty($downloaded_reports)) {
		$i = 0;
		foreach($downloaded_reports as $downloaded_report) { ?>
			downloaded_reports['<?php echo $i; ?>'] = "<?php echo $downloaded_report; ?>";
<?php $i++; } } ?>

var shop_reports;

var startup_dashboard = undefined;
var autosave_timer;
var current_drag;
var reportOptions;
clearReportOptionsCache();

var ajax_requests = {};

/* This is an array which will contain any graphs. */
var plots = {};//new Array();

var fromIsSelected = false;
var toIsSelected = false;

var monthnames = new Array(
	"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
);

var monthnumbers = {
	Jan: 1, Feb: 2, Mar: 3, Apr: 4, May: 5, Jun: 6, Jul: 7, Aug: 8, Sep: 9, Oct: 10, Nov: 11, Dec: 12
};




/* This is an object containing all the default settings for graphs. */

// This is needed to gain full access to all features of JQplot.
$.jqplot.config.enablePlugins = true;

/** Here we'll define all types of JQplot graphs available in Logaholic; Area, Line, Bar, Bar & Line **/
graph = {

	areaChart: function (graphContainer, data, graphOptions) {
		graphOptions.seriesDefaults.renderer = $.jqplot.LineRenderer;
		graphOptions.seriesDefaults.markerRenderer = $.jqplot.MarkerRenderer;
		graphOptions.axes.xaxis.renderer = $.jqplot.CategoryAxisRenderer;
		graphOptions.axes.xaxis.tickRenderer = $.jqplot.CanvasAxisTickRenderer;
		graphOptions.axes.yaxis.tickRenderer = $.jqplot.CanvasAxisTickRenderer;
		graphOptions.axes.yaxis.labelRenderer = $.jqplot.CanvasAxisLabelRenderer;
		
		return $.jqplot(graphContainer, data, graphOptions);
	},
	
	barChart: function (graphContainer, data, graphOptions) {
		graphOptions.seriesDefaults.renderer = $.jqplot.BarRenderer;
		graphOptions.seriesDefaults.markerRenderer = $.jqplot.MarkerRenderer;
		graphOptions.axes.xaxis.renderer = $.jqplot.CategoryAxisRenderer;
		graphOptions.axes.xaxis.tickRenderer = $.jqplot.CanvasAxisTickRenderer;
		graphOptions.axes.yaxis.tickRenderer = $.jqplot.CanvasAxisTickRenderer;
		graphOptions.axes.yaxis.labelRenderer = $.jqplot.CanvasAxisLabelRenderer;
		
		return $.jqplot(graphContainer, data, graphOptions);
	},
	
	barLineChart: function (graphContainer, data, graphOptions) {
		graphOptions.seriesDefaults.markerRenderer = $.jqplot.MarkerRenderer;
		graphOptions.axes.xaxis.renderer = $.jqplot.CategoryAxisRenderer;
		graphOptions.axes.xaxis.tickRenderer = $.jqplot.CanvasAxisTickRenderer;
		graphOptions.axes.yaxis.tickRenderer = $.jqplot.CanvasAxisTickRenderer;
		graphOptions.axes.yaxis.labelRenderer = $.jqplot.CanvasAxisLabelRenderer;
		
		return $.jqplot(graphContainer, data, graphOptions);
	},
	
	lineChart: function (graphContainer, data, graphOptions) {
		graphOptions.seriesDefaults.renderer = $.jqplot.LineRenderer;
		graphOptions.seriesDefaults.markerRenderer = $.jqplot.MarkerRenderer;
		graphOptions.axes.xaxis.renderer = $.jqplot.CategoryAxisRenderer;
		graphOptions.axes.xaxis.tickRenderer = $.jqplot.CanvasAxisTickRenderer;
		graphOptions.axes.yaxis.labelRenderer = $.jqplot.CanvasAxisLabelRenderer;
		graphOptions.axes.yaxis.tickRenderer = $.jqplot.CanvasAxisTickRenderer;
		
		return $.jqplot(graphContainer, data, graphOptions);
	}
};



var contentScrollY = $("#content").scrollTop();


<?php if(!empty($debug)) { ?>
DebugWindow = window.open(
	'',
	'mywindow',
	'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,copyhistory=no,resizable=yes'
)
DebugWindowContent = DebugWindow.document;
clearbutton = "<input type=\'button\' value=\'Clear Debug window\' onclick=\'debugclearlink()\'> ";
DebugWindowContent.write("<style type='text/css'>body, html { margin: 0; padding: 0; } * { font-family: Lucida Console, Sans-Serif; font-size: 12px; } .debug { background-color: #000; color: #0F0; padding-bottom: 26px; } .mysql_debug { background-color: #333; color: #FFF; } </style>");
DebugWindowContent.write('<script type="text/javascript">function debugclearlink() { document.body.innerHTML="";	document.write("<input style=\'position:fixed;\' type=\'button\' value=\'Clear Debug window\' onclick=\'debugclearlink()\'>"); top.parent.opener.focus(); }</script>');
DebugWindowContent.write('<input type=\'button\' value=\'Clear Debug window\' onclick=\'debugclearlink()\'>');

<?php } ?>


$(document).ready(function() {
	applyJQlisteners();
	FinishInitialization();
	debugToConsole("Initialization");
});





/** <-------------------------------------------------------------------------------- Data --------------------------------------------------------------------------------> **/


function updateUnpurchased() {
	var StoreScript = document.createElement('script');
	StoreScript.type = 'text/javascript';
	StoreScript.src = 'http://www.logaholic.com/logadl/report_delivery/getUnpurchased.php' + "?already_obtained=" + JSON.stringify(downloaded_reports);
	document.getElementsByTagName('head')[0].appendChild(StoreScript);
	// this script returns a serialized array and fires getStoreReports(jsonstring) 
}


function applyJQlisteners () {
	/**
		This function will apply all listeners to their respective HTML object.
		
		Arguments:
			None
	**/
	/* This is needed for the profile custom date formating */
	$(".restore-default-date-format-settings").click( function(){
		$.ajax({
		  url: 'includes/dateReturner.php',
		  data: {defaultFormat : '1'},
		  success: function(result) {
				r = result.split(',');
				$('.format1 option[value='+r[0]+']').attr('selected', 'selected');
				$('.seperator1 option[value='+r[1]+']').attr('selected', 'selected');
				$('.format2 option[value='+r[2]+']').attr('selected', 'selected');
				$('.seperator2 option[value='+r[3]+']').attr('selected', 'selected');
				$('.format3 option[value='+r[4]+']').attr('selected', 'selected');
				$('.seperator3 option[value='+r[5]+']').removeAttr('selected');
				$('.format4 option[value='+r[6]+']').attr('selected', 'selected');
				$.ajax({
				  url: 'includes/dateReturner.php',
				  data: {dateString : $(".format1").val()+$(".seperator1").val()+$(".format2").val()+$(".seperator2").val()+$(".format3").val()+$(".seperator3").val()+$(".format4").val()},
				  success: function(result) {
						$(".date-format-settings-preview").html(result);
				  }
				});	
			}
		});			
	});		
	$(".date-format-settings select").change( function(){
		$.ajax({
		  url: 'includes/dateReturner.php',
		  data: {dateString : $(".format1").val()+$(".seperator1").val()+$(".format2").val()+$(".seperator2").val()+$(".format3").val()+$(".seperator3").val()+$(".format4").val()},
		  success: function(result) {
				$(".date-format-settings-preview").html(result);
		  }
		});				
	});		
	
	/* When an object with the class opendialog is being clicked, we'll open an JQuery UI Dialog. */
	$('.opendialog').live("click", function() {
		openDialog($(this));
		return false;
	});
	
	/* In order to prevent weird stuff happening after a window resize, we need to fix some stuff. */
	$(window).resize(function() {
		fixAfterWindowResize();
	});	
	
	/* When the Reports button is being clicked, we'll toggle the Report Area. */
	$(".summon-reports").live("click", function() {
		if($("#report_area").is(":hidden") == true) {
			
			$(".summon-reports").addClass("active");
			$("#report_panel").show();
			$("#report_area").show();
			$("#report_area_extension").show();
			$("#report_area .search_in_reports").focus();
			$("#info_frame").css('height', '300px');
			
			$("#north").addClass('shadowless');
			$(".workbar").addClass('shadowless');
		} else {
			hideReportArea();
		}
		
		fixAfterWindowResize();
		
		return false;
	});
	
	$(".dropdown > li").live({
		mouseenter: function() {
			var dropdownElement = $(this);
			dropdown_timer = setTimeout(function () {
				dropdownElement.find('.dropdown-list').show();
			}, 100);
		},
		mouseleave: function() {
			clearTimeout(dropdown_timer);
			$(this).find('.dropdown-list').hide();
		}
	});
	
	/* Hide/Show the column checkboxes in the reports options pane. */
	$(".column-selector-togler").live("click", function() {
		if($(".column-selector-form").css("display") == "none"){
			$(".column-selector").addClass('active');
			$(".column-selector-form").css("display","block");
		} else {
			$(".column-selector").removeClass('active');
			$(".column-selector-form").css("display","none");
		}
	});
	
	/* When a report or dashboard icon is being clicked, we'll open the Option Window for that report or dashboard. */
	$("#report_area .report_icon").live("click",function(e) {
		if($(this).hasClass('dashboard') == true) {
			showMultiOptions($(this).attr('name'));
		} else {
			showOptions($(this).attr('name'), $(this).attr('type'), $(this).attr('href'));
		}
		return false;
	});
	
	$("#report_area .reportstore_icon").live("mouseenter", function() {
		$(this).find(".store_tooltip").html($(this).attr('title'));
		$(this).attr('title', '');
	});
	$("#report_area .reportstore_icon").live("mouseleave", function() {
		$(this).attr('title', $(this).find(".store_tooltip").html());
		$(this).find(".store_tooltip").html('');
	});
	
	/* When a report or dashboard icon is being clicked, we'll open the Option Window for that report or dashboard. */
	$("#report_area .open_category").live("click",function(e) {
		openCategory($(this).closest(".report-category").attr("rel"));
		
		return false;
	});
	
	/* When the help button is being clicked, we'll do as the button says. */
	$(".dialog * .help_btn").live("click",function() {
		toggleReportHelp($(this));
		return false;
	});
	
	/* When the mail button is being clicked, we'll do as the button says. */
	$(".dialog * .mail_btn").live("click",function() {
		toggleReportMail($(this));
		return false;
	});
	
	/* When the small gears icon in a report is being clicked, we'll open the Report Option Window for this report. */
	$(".dialog-controls a.dialogsettings").live("click",function() {
		var url = $(this).closest('.dialog').attr('href');
		
		if(url.indexOf("?") > -1) { url = url.split("?")[0]; }
		
		showOptions(
			$(this).closest('.dialog').attr('name'),
			$(this).closest('.dialog').find('.dialog-label').attr('rel'),
			url,
			$(this).closest('.dialog').attr('id'),
			urlToOptions($(this).closest('.dialog').attr('href'))
		);
		
		return false;
	});
	
	/* When the reload icon in a report is being clicked, we'll reload this report. */
	$(".dialog-controls a.reload").live("click",function() {
		$("#" + $(this).closest(".dialog").attr('id') + " .dialog-content").prepend("<div class='reloading_msg'><?php echo _RELOADING; ?> &nbsp;<img src='images/loader.gif'/></div>");
		
		loadReport($(this).closest(".dialog").attr('id'), $(this).closest(".dialog").attr('href'), false);
		
		return false;
	});
	
	/* When the close button in a report is being clicked, we'll close the report. */
	$(".dialog-controls a.close").live("click",function() {
		if($(this).closest("#large-grid").length > 0) {
			var dialogID = $(this).closest(".dialog").attr("id").substr(0, $(this).closest(".dialog").attr("id").length - 6);
			
			$("#" + dialogID).show();
			
			$(this).closest(".dialog").remove();
		} else {
			closeReport($(this).closest(".dialog").attr('id'), function() {
				autosaveDashboard();
			});
		}
		return false;
	});
	
	/* When the Expand button in a report is being clicked, we'll expand the report. */
	$(".dialog-controls a.expand_switch").live("click", function() {
		if($(this).hasClass("expand") == true) {
			expandReport($(this).closest(".dialog").attr('id'));
		} else {
			smallifyReport($(this).closest(".dialog").attr('id'));
		}
		
		return false;
	});
	
	/* When the minimize button in a report is being clicked, we'll minimize the report. */
	$(".dialog-controls a.minimize").live("click",function() {
		minimizeReport($(this).closest(".dialog").attr('id'));
		return false;
	});
	
	/* When an icon in the Work Bar is being clicked, we'll minimize or restore the corresponding report. (If it's minimized, we'll restore it, and vice versa) */
	$(".workbar .workicons .icon").live("click",function() {
		if($("#" + $(this).attr('rel')).is(":hidden")) {
			restoreReport($(this).attr('rel'));
		} else {
			minimizeReport($(this).attr('rel'));
		}
		
		return false;
	});
	
	/* When an icon in the Work Bar is hover, we'll scroll to that part of the dash) */
	$(".workbar .workicons .icon").live("mouseenter", function(event) {
		if ($('#'+$(this).attr('rel')+'_large').length > 0) {
			var tico = $(this).attr('rel')+'_large';
		} else {
			var tico = $(this).attr('rel');
		}
		
		hoverWorkbarIconTimer = setTimeout(function() {
			hoverWorkbarIcon(tico);
		}, 400);
		
		$('#'+tico).css("box-shadow", "1px 1px 5px 0 red");
		
		return false;
	});
	
	$(".workbar .workicons .icon").live("mouseleave", function(event) {
		clearTimeout(hoverWorkbarIconTimer);
		
		if ($('#'+$(this).attr('rel')+'_large').length > 0) {
			var tico = $(this).attr('rel')+'_large';
		} else {
			var tico = $(this).attr('rel');
		}
		
		$('#'+tico).css("border", "0px");
		$('#'+tico).css("box-shadow", "1px 1px 5px 0 silver");
	});
	
	/* When the work bar is being clicked, we'll close the reports pane */
	$(".workbar").live("click", function(event) {
		clearTimeout(ReportPanelShadingTimer);
		if($("#report_area").is(":visible") == true && $("#report_area_extension").is(":visible") == true) {
			hideReportArea();
		}
	});
	
	/* When you hover over the workbar, and the report area has been opened, it will fade out. */
	$(".workbar").live("mouseenter", function(event) {
		if($("#report_area").is(":visible") == true && $("#report_area_extension").is(":visible") == true) {
			ReportPanelShadingTimer = setTimeout(function() {
				$("#report_area, #report_area_extension").animate({
						opacity: 0
					}, 750
				);
			}, 500);
		}
		
		return false;
	});
	/* When you hover over the workbar, and the report area has been opened, it will fade in. */
	$(".workbar").live("mouseleave", function(event) {
		clearTimeout(ReportPanelShadingTimer);
		
		$("#report_area, #report_area_extension").stop();
		
		if($("#report_area").is(":visible") == true && $("#report_area_extension").is(":visible") == true) {
			$("#report_area, #report_area_extension").animate({
					opacity: 1
				}, 750
			);
		}
		
		return false;
	});
	
	/* When you click the button in the workbar to open/close the actions menu we will show/hide it. */
	$(".workbar-popout-activator").live("click",function(event) {
		event.stopPropagation(); // This has to do with closing when clicked on something else than this element
		if($(this).hasClass('activated')) {	// The menu is open, so close it.
			$(this).removeClass('activated');
			$(".workbar-popout").removeClass('visible');
		} else { // The menu is closed, so open it.
			$(this).addClass('activated');
			$(".workbar-popout").addClass('visible');
		}
		return false;
	});
	
	/* When the workbar's action menu is open and there's being clicked outside the menu, we'll close the menu. */
	$(".workbar-popout").live("click",function(event) {
		event.stopPropagation(); // This has to do with closing when clicked on something else than this element
	});
	
	/* If the workbar popout is opened, and there's clicked outside it, it'll close */
	$("html").live("click", function() {
		if($(".workbar-popout-activator").hasClass('activated') == true) {
			$(".workbar-popout-activator").removeClass('activated');
			$(".workbar-popout").removeClass('visible');
		}
	});
	
	/* Close the dialog when you click on the modal */
	$(".ui-widget-overlay").live("click", function() {
		$(".ui-dialog-titlebar-close").trigger('click');
	});
	
	/* Open the option pane for 'Change global daterange' */
	$(".change-daterange").live("click",function() {
		$("#report_area_extension .report_icon img").attr('src', 'images/icons/32x32/globalsettings.png');
		$("#report_area_extension .current_report").html('Current Workspace');
		
		showMultiOptions("global");
		
		if($(".workbar-popout-activator").hasClass('activated')) {
			$(".workbar-popout-activator").click();
		}
		
		return false;
	});
	
	/* When the Minimize All button is being clicked, we'll minimize all open reports. */
	$(".minimize-all").live("click",function() {
		minimizeAll();
		return false;
	});
	
	/* When the Restore All button is being clicked, we'll restore all minimized reports. */
	$(".restore-all").live("click",function() {
		restoreAll();
		return false;
	});
	
	/* When the Close All button is being clicked, we'll close all open AND minimized reports. */
	$(".close-all").live("click",function() {
		closeAll();
		clearReportOptionsCache();
		return false;
	});
	
	/* When the Save this Screen button is being clicked, we'll pop up the Save Dashboard Window. */
	$(".save-screen").live("click",function() {
		if($(".dashboard.report_icon.active").length > 0) {
			var dashboardOptions = {
				name: $(".dashboard.report_icon.active").attr('name'),
				description: $(".dashboard.report_icon.active").attr('title'),
				icon: $(".dashboard.report_icon.active > img").attr('src').split('/')[($(".dashboard.report_icon.active > img").attr('src').split('/').length - 1)].replace('.png', ''),
				startup: $(".dashboard.report_icon.active").attr('startup'),
				daterange_lock: $(".dashboard.report_icon.active").attr('daterange_lock'),
				save_reports: "1"
			};
		} else {
			var dashboardOptions = {
				name: "",
				description: "",
				icon: "00",
				startup: "0",
				save_reports: "1",
				daterange_lock: "0"
			};
		}
		
		openDashboardOptions(dashboardOptions);
		
		if($(this).closest(".workbar-popout").length > 0) {
			$(".workbar-popout-activator").click();
		}
		return false;
	});
	
	/* When a dashboard has been selected when saving a dashboard, we'll select that dashboard to be overwritten. */
	$("#overwriteDashboardPicker").live("change", function() {
		$("#screenname").attr('value', $(this).val());
	});
	
	/* When the Edit this Dashboard button is being clicked, a dialog will pop up. */
	$(".edit-dashboard").live("click",function() {
		openDashboardOptions({
			name: $(".dashboard.report_icon[name='" + $(this).attr('rel') + "']").attr('name'),
			description: $(".dashboard.report_icon[name='" + $(this).attr('rel') + "']").attr('title'),
			icon: $(".dashboard.report_icon[name='" + $(this).attr('rel') + "']").find('img').attr('src').split('/')[($(".dashboard.report_icon > img").attr('src').split('/').length - 1)].replace('.png', ''),
			startup: $(".dashboard.report_icon[name='" + $(this).attr('rel') + "']").attr('startup'),
			daterange_lock: $(".dashboard.report_icon[name='" + $(this).attr('rel') + "']").attr('daterange_lock'),
			save_reports: 0
		});
	});
	
	/* When the Delete this Dashboard button is being clicked, we'll ask for confirmation, and then delete the dashboard. */
	$(".delete-dashboard").live("click",function() {
		deleteDashboard($(this).attr('rel'));
		return false;
	});
	
	/* Whenever another icon for a dashboard is being chosen, we'll check the right checkbox. */
	$(".iconpicker div input").live("change",function() {
		$(".iconpicker div input").removeAttr("checked");
		$(this).attr("checked","checked");
	});
	
	/* If the user doesn't want to save the screen, we will close the Save Dashboard Window. */
	$("#savescreenoptions .decline").live("click",function() {
		$(".ui-widget-overlay").remove();
		$("#savescreenoptions").remove();
		return false;
	});
	
	/* Apparently, the user wants to save the dashboard, so let's be nice, and save the dashboard. */
	$("#savescreenoptions .agree").live("click",function() {
		var dashboard_data = prepareSaveDashboard();
		saveDashboard($("#savescreenoptions #screenname").val(), dashboard_data);
		
		$(".ui-widget-overlay").remove();
		$("#savescreenoptions").remove();
		return false;
	});
	
	/* When the 'Add' button is being clicked, we want to add the report to the workspace, but stay in the report area. */
	$("#addToDashboard").live("click", function() {
		$("#report_area_extension .report_options").hide();
		$("#report_area_extension .form_buttons #addToDashboard").remove();
		$("#report_area_extension .form_buttons #addToDashboardAndClose").remove();
		
		addToWorkspace();
		
		return false;
	});
	
	/* When the 'Go' button is being clicked, we want to add the report to the workspace, and go to the workspace ourselves. */
	$("#addToDashboardAndClose").live("click", function() {
		if($("#report_area").is(":visible") == true) {
			$.scrollTo(0, 0);
		}
		$("#report_area_extension .form_buttons #addToDashboard").remove();
		$("#report_area_extension .form_buttons #addToDashboardAndClose").remove();
		
		if($(".current_report").attr('rel') == 'dashboard') {
			openMultiReport($(".current_report").attr('name'));
		} else {
			addToWorkspace();
		}
		
		hideReportArea();
		
		return false;
	});
	
	/* Remove the downloaded report from the database */
	$(".delete_download").live("click", function() {
		var confirmation = confirm("<?php echo _DO_YOU_REALLY_WANT_TO_DELETE_DOWNLOAD; ?>");
		if(confirmation == true) {
			try {
				var report_label = $(this).attr('type');
				doAjaxRequest(".delete_download", $(this).attr("href"), function() {
					closeReport($(".dialog[type=" + report_label + "]").attr('id'), function() {
						//
					});
				});
			} catch(err) {
				
			}
		}
		
		return false;
	});
	
	/* When the 'Cancel' button is being clicked, we want to close the Report Options and, if open, the Reports Area. */
	$("#closeReportOptions").live("click", function() {
		hideReportArea();
	});
	
	/* When a link in a report with the class open_in_new_dialog is being clicked, we want to open the URL in a new report window */
	$(".dialog-content").find(".open_in_new_dialog").live("click", function(ev) {
		if(ev.ctrlKey == true) {
			$(this).removeClass("open_in_new_dialog");
			$(this).addClass("open_in_this_dialog");
			$(this).click();
			
			return false;
		}
		
		if($(this).hasClass("quickopen") == true) {
			var reportID = newReport(undefined, $(this).attr('name'), $(this).attr('type'), $(this).attr('href'), Loading($(this).attr('name')), 'prepend', undefined, false);
			
			loadReport(reportID, optionsToURL($(this).attr('href')));
			
			$.scrollTo(0, 0);
			
			return false;
		} else {
			$("#report_area_extension .report_icon img").attr('src', $(this).find("img").attr("src"));

			var urloptions = urlToOptions($(this).attr('href'));
			urloptions.minimumDate = defaultOptions.minimumDate;
			urloptions.maximumDate = defaultOptions.maximumDate;
			
			showOptions($(this).attr('name'), $(this).attr('type'), $(this).attr('href'), $(this).attr('rel'), urloptions);
			
			return false;
		}
		
		return false;
	});
	
	/* When a link in a report with the class open_in_new_dialog is being clicked, we want to open the URL in the parent report window */
	$(".dialog-content").find(".open_in_this_dialog").live("click", function(ev) {
		if(ev.ctrlKey == true) {
			$(this).removeClass("open_in_this_dialog");
			$(this).addClass("open_in_new_dialog");
			$(this).click();
			
			return false;
		}
		if($(this).hasClass("quickopen") == true) {
			loadReport($(this).closest(".dialog").attr("id"), optionsToURL($(this).attr('href')));
			
			return false;
		} else {
			showOptions($(this).attr('name'), $(this).attr('type'), $(this).attr('href'), $(this).attr('rel'), urlToOptions($(this).attr('href')));
		}
		
		return false;
	});
	
	/* When a HTML-element with the class open_in_iframe_window is being clicked, we want to open a JQuery dialog, based on the href attribute of the HTML-element */
	$(".open_iframe_window").live("click", function() {
		createIframeWindow($(this).attr('href'));
		
		return false;
	});
	
	/* Open a query builder drop down */
	$(".qbuilderhelp").live("keyup", function(event) {
		QBuilderHelpForms($(this).val(), event, $(this).val() + '@' + 'cvalue' + $(this).attr('rel') + '@' + $("#field" + $(this).attr('rel')).val(), 'forminput');
	});
	
	/* Open a query builder drop down */
	$(".qbuilderhelp").live("click", function(event) {
		QBuilderHelpForms($(this).val(), event, $(this).val() + '@' + 'cvalue' + $(this).attr('rel') + '@' + $("#field" + $(this).attr('rel')).val(), 'forminput');
	});
	
	/* Add the active class to the search field in the reports pane. */
	$(".search_in_reports").live("focus", function(event) {
		$(this).addClass("active");
	});
	
	/* Remove the active class from the search field in the reports pane. */
	$(".search_in_reports").live("blur", function(event) {
		$(this).removeClass("active");
	});
	
	/* When you click on the X button in the search reports field, you will clear the search results. */
	$(".clear_search_reports").live("click", function() {
		$(this).hide();
		$(".search_in_reports").attr('value', '');
		$(".report_icon.dashboard").show();
		$("#report_area .reportstore_icon").show();
		$("#report_area .report_icon").show();
		$("#report_area h2").show();
		$("#report_area ul.featured_icons").show();
		$("#report_area h2.search_results").hide();
		$(".no_search_results").hide();
		$("#report_area ul.s_results").children().remove();
	});
	
	/* Adjust the Reports' Search Results to the input. */
	$(".search_in_reports").live("keyup", function(event) {
		if($(".search_in_reports").val() != "") {
			/* Hide dashboard icons */
			$(".report_icon.dashboard").hide();
			
			/* Hide reports that don't match the criteria, and show the ones that do */
			$("#report_area .report_icon:not(.dashboard), #report_area .reportstore_icon").each(function() {
				if($(this).attr('name').toLowerCase().indexOf($(".search_in_reports").val().toLowerCase()) >= 0) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
			
			/* Show the clear search results button */
			$("#report_area .clear_search_reports").show();
			
			/* Hide the categories */
			$("#report_area h2").hide();
			$("#report_area ul.featured_icons").hide();
			
			/* Show the search results */
			$("#report_area h2.search_results").show();
			
			if($(".report_icon:not(.dashboard):visible").length <= 0) {
				/* Show the No Search Results message */
				$(".no_search_results").show();
			} else {
				/* Remove the previously added search results */
				$("#report_area ul.s_results").children().remove();
				
				/* Hide the No Search Results message */
				$(".no_search_results").hide();
				
				/* Add each matching icon to an array */
				var listitems = [];
				$(".report_icon:not(.dashboard):visible").each(function() {
					listitems.push($(this));
				});
				
				/* Now, sort that array on alphabetical order */
				listitems.sort(function(a, b) {
				   var compA = $(a).attr('name').toUpperCase();
				   var compB = $(b).attr('name').toUpperCase();
				   return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
				});
				
				/* Loop through the array, and append the icons to s_results, the container of search results */
				for(var i = 0; i < listitems.length; i++) {
					if($("#report_area ul.s_results").children('#' + listitems[i].attr('name')).length < 1) {
						/* We need to clone the icon, because if we don't, the icon gets moved while we need a copy */
						listitems[i].clone().appendTo("#report_area ul.s_results");
						listitems[i].hide();
					}
				}
			}
		} else {
			$(".report_icon.dashboard").show();
			$("#report_area .reportstore_icon").show();
			$("#report_area .report_icon").show();
			$("#report_area h2").show();
			$("#report_area ul.featured_icons").show();
			$("#report_area h2.search_results").hide();
			$(".no_search_results").hide();
			$(".clear_search_reports").hide();
			$("#report_area ul.s_results").children().remove();
		}
		
		fixAfterWindowResize();
	});
	
	/* Add the active class to an input field in the reports options pane. */
	$(".report_options").find("input").live("focus", function(event) {
		$(this).addClass("active");
	});
	
	/* Remove the active class from an input field in the reports options pane. */
	$(".report_options").find("input").live("blur", function(event) {
		$(this).removeClass("active");
	});
	
	/* When a legend item is being clicked, we'll hide the corresponding line or bar. */
	$(".legend_bullet.switchable").live("click", function() {
		plots;
		
		if($(this).hasClass('inactive_plot') == false) {
			$(this).addClass("inactive_plot");
		} else {
			$(this).removeClass("inactive_plot");
		}
		
		var reporturl = $(this).closest(".dialog").attr("href");
		
		var opts = urlToOptions(reporturl);
		
		var hiddenLegends = [];
		$(this).closest(".dialog").find(".glegend").each(function() {
			var currentLegend = [];
			$(this).find(".switchable").each(function() {
				if($(this).hasClass('inactive_plot') != false) {
					currentLegend.push($(this).parent().index());
				}
			});
			hiddenLegends.push(currentLegend);
		});
		
		opts['hiddenlegends'] = JSON.stringify(hiddenLegends);
		
		// if($(this).closest(".dialog").find(".graph_area").length > 1) {
			// opts['selectedgraph'] = $(this).closest('.graph_area').parent().index();
		// }
		
		// $(this).closest(".glegend").find(".switchable").each(function() {
			// var showcolumnKey = "showColumn" + $(this).parent().index();
			
			// if($(this).hasClass('inactive_plot') == false) {
				// opts[showcolumnKey] = 'on';
			// } else {
				// opts[showcolumnKey] = 'off';
			// }
		// });
		
		var url = optionsToURL(reporturl, opts);

		$(this).closest(".dialog").attr("href", url);
		
		$("#" + $(this).closest(".dialog").attr('id') + " .dialog-content").prepend("<div class='reloading_msg'><?php echo _RELOADING; ?> &nbsp;<img src='images/loader.gif'/></div>");
		
		loadReport($(this).closest(".dialog").attr('id'), $(this).closest(".dialog").attr('href'), false);
	});
	
	/* Hide/Show a report category in the reports pane */
	$(".report-category").live("click", function() {
		if($(this).next().find('.report_icon, .reportstore_icon').is(":hidden") == true) {
			$(this).next().find('.report_icon, .reportstore_icon').show();
			$(this).find(".report-category-arrow").attr("src","images/arrow_down_darkgrey_square.png");
			$(this).removeClass("hidden");
			if($(this).hasClass("dashboard-list") == false) {
				setCategoryCookie($(this).attr("rel"), false);
			}
		} else {
			$(this).next().find('.report_icon, .reportstore_icon').hide();
			$(this).find(".report-category-arrow").attr("src","images/arrow_right_darkgrey_square.png");
			$(this).addClass("hidden");
			if($(this).hasClass("dashboard-list") == false) {
				setCategoryCookie($(this).attr("rel"), true);
			}
		}
		
		fixAfterWindowResize();
	});
	
	/* let's be super friendly and start comments again. */
	/* send a user to the pdf workspace download */
	$(".pdf-workspace").live("click", function() {			
		autosaveDashboard(function() {
			createIframeWindow($(".pdf-workspace").attr('href'),"<?php echo _CREATE_PDF; ?>", false);
		}, true);
		$(".workbar-popout-activator").click();
		return false;		
	});
	
	
	/* Top 5 Profiles list */	
	$(".manage-profiles-list-link").live({
		mouseenter: function() {
			manage_profile_timer = setTimeout(function () {
				$("#manage_profiles_list").css("display","block");
			}, 300);
        },
		mouseleave: function() {
			clearTimeout(manage_profile_timer);
			$("#manage_profiles_list").css("display","none");
			$("#manage_profiles_list").live({
				mouseenter: function() {
					$("#manage_profiles_list").css("display","block");
				},
				mouseleave: function() {
					$("#manage_profiles_list").css("display","none");
				}
			});
        }
    });
	
	/* _SURVEYS accordion functionality listener */
	$(".survey_table_header").live("click", function() {
		if($(".survey_table"+$(this).attr("rel")).is(':hidden') == true){
			$(".survey_table"+$(this).attr("rel")).show();
		} else {
			$(".survey_table"+$(this).attr("rel")).hide();
		}				
	});
	
	$("#displaymode").live("change", function() {
		if($(this).val() == 'table') {
			$(".column-selector").show();
		} else {
			$(".column-selector").find("input").attr('checked', 'checked');
			$(".column-selector").hide();
		}
	});
	
	/* Show/Hide our datepicker. */
	$("#daterangeField").live("click", function(event) {
		if($(".lwa_datepicker").is(":hidden")) {
			resetDaterangeInput();
			fromIsSelected = false;
			toIsSelected = false;
			$("#fromRange").datepicker('setDate', $('#daterangeField').val().split(' - ')[0]);
			$("#toRange").datepicker('setDate', $('#daterangeField').val().split(' - ')[1]);
			$(".lwa_datepicker").show();
			$(".lwa_datepicker_overlay").show();
		} else {
			$(".lwa_datepicker").hide();
			$(".lwa_datepicker_overlay").hide();
		}
		return false;
	});
	
	/* Hide our datepicker whenever the user clicks on the overlay */
	$(".lwa_datepicker_overlay").live("click", function() {
		$(".lwa_datepicker").hide();
		$(".lwa_datepicker_overlay").hide();
	});
	
	/* This is to prevent users from entering an incorrect format in the datepicker. */
	$("#daterangeField").live("focus", function() {
		resetDaterangeInput();
		fromIsSelected = false;
		toIsSelected = false;
	});
	
	/* This is to prevent users from entering an incorrect format in the datepicker. */
	$("#daterangeField").live("blur", function() {
		resetDaterangeInput();
		fromIsSelected = false;
		toIsSelected = false;
	});
	
	
	/* We'll handle the Quick Date options here */
	$(".set-lwa-datepicker").live("click", function() {
		var DateObject = new Date();
		
		// Today
		if($(this).hasClass('lwa_today')) {
			
			var from_date = '<?php echo date(GetCustomDateFormat(),time()); ?>';
			var to_date = '<?php echo date(GetCustomDateFormat(),time()); ?>';
		}
		
		// Yesterday
		if($(this).hasClass('lwa_yesterday')) {
			var from_date = '<?php echo date(GetCustomDateFormat(),strtotime('-1 day')); ?>';
			var to_date = '<?php echo date(GetCustomDateFormat(),time()); ?>';
		}
		
		// Last 7 Days
		if($(this).hasClass('lwa_last7days')) {
			var from_date = '<?php echo date(GetCustomDateFormat(),strtotime('-1 week')); ?>';
			var to_date = '<?php echo date(GetCustomDateFormat(),time()); ?>';
		}
		
		// This Month
		if($(this).hasClass('lwa_thismonth')) {
			var from_date = '<?php echo date(GetCustomDateFormat(),strtotime("01 ".date("F")." ".date("Y"))); ?>';
			var to_date = '<?php echo date(GetCustomDateFormat(),time()); ?>';
		}
		
		// Last Month
		if($(this).hasClass('lwa_lastmonth')) {
			var from_date = '<?php echo date(GetCustomDateFormat(),mktime(0,0,0,date("m")-1,01,date("Y"))); ?>';
			var to_date = '<?php echo date(GetCustomDateFormat(),mktime(23,59,59,date("m")-1,30,date("Y"))); ?>';
		}
		
		// Last 3 Months
		if($(this).hasClass('lwa_last3months')) {
			var from_date = '<?php echo date(GetCustomDateFormat(),mktime(0,0,0,date("m")-2,01,date("Y"))); ?>';
			var to_date = '<?php echo date(GetCustomDateFormat(),time()); ?>';
		}
		
		// This Year
		if($(this).hasClass('lwa_thisyear')) {
			var from_date = '<?php echo date(GetCustomDateFormat(),strtotime("01 January ".date("Y"))); ?>';
			var to_date = '<?php echo date(GetCustomDateFormat(),time()); ?>';
		}
		
		// Last Year
		if($(this).hasClass('lwa_lastyear')) {
			var from_date = '<?php echo date(GetCustomDateFormat(),strtotime("01 January ".(date("Y")-1))); ?>';
			var to_date = '<?php echo date(GetCustomDateFormat(),strtotime("31 December ".(date("Y")-1))); ?>';
		}
		
		// All Time
		if($(this).hasClass('lwa_alltime')) {
			var from_date = '<?php echo date(GetCustomDateFormat(), getStartDate($conf));?>';
			var to_date = '<?php echo date(GetCustomDateFormat(), time()); ?>';
		}
		
		$("#fromRange").datepicker('setDate', from_date);
		$("#toRange").datepicker('setDate', to_date);
		$("#daterangeField").attr('value', from_date + ' - ' + to_date);
		$('#minimumDate').attr('value', from_date);
		$('#maximumDate').attr('value', to_date);
		$(".lwa_datepicker").hide();
		$(".lwa_datepicker_overlay").hide();
	});
	
	<?php if($update_running == 'yes') { ?>
	$(".stop_import").live("click", function() {
		$.ajax({
			url: 'components/import/stop_import.php',
			async: true,
			data: {
				conf: conf_name
			},
			success: function(result, status) {
					changeUpdateText(result);
					clearInterval(updateInterval);
			}
		});
	});
	<?php } ?>
}

function resetDaterangeInput(dateToSet, daterangePart) {
	var tmp_date = $('#daterangeField').val().split(' - ');
	
	if(dateToSet == undefined) {
		if(daterangePart == 'from') {
			dateToSet = '<?php echo date(GetCustomDateFormat(),strtotime("01 ".date("F")." ".date("Y"))); ?>';
		} else if (daterangePart == 'to'){
			dateToSet = '<?php echo date(GetCustomDateFormat(),time()); ?>';
		}
	}
	if(daterangePart == 'from') {
		tmp_date = dateToSet + " - " + tmp_date[1];
	} else if (daterangePart == 'to'){
		tmp_date = tmp_date[0] + " - " + dateToSet;
	}else {
		tmp_date = tmp_date[0] + " - " + tmp_date[1];
	}
	$('#daterangeField').attr('value', tmp_date);
	$('#minimumDate').attr('value', tmp_date.split(' - ')[0]);
	$('#maximumDate').attr('value', tmp_date.split(' - ')[1]);
}

function applyDatepicker() {
	$("#fromRange, #toRange").datepicker({
		dateFormat: '<?php echo GetCustomDateFormat("JS"); ?>',
		showAnim: '',
		changeMonth: true,
		changeYear: true,
		onSelect: function(selectedDate) {
			if(this.id == 'fromRange') {
				resetDaterangeInput(selectedDate, 'from');
				fromIsSelected = true;
			} else {
				resetDaterangeInput(selectedDate, 'to');
				toIsSelected = true;
			}
			
			if(fromIsSelected == true && toIsSelected == true) {
				$(".lwa_datepicker").hide();
				$(".lwa_datepicker_overlay").hide();
			}
			
			$('#minimumDate').attr('value', $("#daterangeField").val().split(' - ')[0]);
			$('#maximumDate').attr('value', $("#daterangeField").val().split(' - ')[1]);
		}
	});
	
	$("#fromRange, #toRange").find(".ui-datepicker-month").addClass('noOption');
	$("#fromRange, #toRange").find(".ui-datepicker-year").addClass('noOption');
}





<?php # Set a cookie whenever a category gets hidden or shown ?>
function setCategoryCookie(label, hidden) {
	var currentTime = new Date();
	var month = currentTime.getMonth();
	var day = currentTime.getDate();
	var year = currentTime.getFullYear();
	if(month < 11) {
		month = month + 1;
	} else {
		month = 0;
		year = year + 1;
	}
	var expire_date = new Date (year, month, day);
	
	document.cookie = label + "=" + hidden + ";expires=" + expire_date.toGMTString();
}



function attachReportEvents (reportID) {
	/**
		This function attaches any events to newly created or reloaded reports.
		
		Arguments:
		- reportID
			The report that's getting its events attached.
	**/
	
	$("#report-grid").sortable({
		items: '.dialog',
		handle: '.dialog-header',
		connectWith: '.grid',
		opacity: 0.3, // opacity of the element while draging
		placeholder: 'ui-state-highlight',
        helper: 'clone',
        forcePlaceholderSize: true,
        forceHelperSize: true,
		tolerance: 'pointer',
		distance: 10,
		start: function(event, ui) {
			var c = 0;
			$("#report-grid").find(".grid").each(function() {
				$(this).css('height', $("#report-grid .grid:eq(" + getLargestGrid() + ")").height() + c);
				c++;
			});
			
			current_drag = ui.item.index();
		},
		stop: function(event, ui) {
			var dialogID = ui.item.attr('id');
			
			if(ui.item.index() > current_drag) {
				var neighbour = ui.item.prev().attr('id');
				var new_icon = $(".workbar .workicons .workbar_grid .icon[rel=" + dialogID + "]").clone();
				$(".workbar .workicons .workbar_grid .icon[rel=" + dialogID + "]").remove();
				new_icon.insertAfter(".workbar .workicons .workbar_grid:eq(" + ui.item.parent().index() + ") .icon[rel='" + neighbour + "']");
			} else if(ui.item.index() <= current_drag) {
				var neighbour = ui.item.next().attr('id');
				var new_icon = $(".workbar .workicons .workbar_grid .icon[rel=" + dialogID + "]").clone();
				$(".workbar .workicons .workbar_grid .icon[rel=" + dialogID + "]").remove();
				if(neighbour != undefined) {
					new_icon.insertBefore(".workbar .workicons .workbar_grid:eq(" + ui.item.parent().index() + ") .icon[rel='" + neighbour + "']");
				} else {
					if(ui.item.index() > 0) {
						$(".workbar .workicons .workbar_grid:eq(" + ui.item.parent().index() + ")").append(new_icon);
					} else {
						$(".workbar .workicons .workbar_grid:eq(" + ui.item.parent().index() + ")").prepend(new_icon);
					}
				}
			}
			
			autosaveDashboard();
		}
	});
	
	$("#" + reportID + " .dialog-header").disableSelection();
}



function prepareURL (reportURL, reportURLOptions) {
	/**
		The name says it all; We'll prepare an URL for loading a report.
		
		Arguments:
		- reportURL
			The URL that we're about to prepare.
		- reportURLOptions
			The options of the report whose URL we're about to prepare.
	**/
	
	conf_name;
	var URLoptions = "";
	var URLparametersSet = false;
	
	if(reportURL.indexOf("?") > -1) {
		URLparametersSet = true;
	}
	
	if(reportURLOptions.minimumDate != undefined) {
		if(URLparametersSet == false) {
			URLoptions += "?";
		} else {
			URLoptions += "&";
		}
		URLoptions += "minimumDate=" + reportURLOptions.minimumDate;
		URLparametersSet = true;
	}
	
	if(reportURLOptions.maximumDate != undefined) {
		if(URLparametersSet == false) {
			URLoptions += "?";
		} else {
			URLoptions += "&";
		}
		URLoptions += "maximumDate=" + reportURLOptions.maximumDate;
		URLparametersSet = true;
	}
	
	for(var option in reportURLOptions) {
		if(option == "minimumDate" || option == "maximumDate") { continue; }
		
		if(URLparametersSet == false) {
			URLoptions += "?";
		} else {
			URLoptions += "&";
		}
		
		URLoptions += option + "=" + reportURLOptions[option];
		URLparametersSet = true;
	}
	
	reportURL = reportURL + URLoptions;
	
	if(URLparametersSet == false) {
		reportURL += "?";
	} else {
		reportURL += "&";
	}
	reportURL += "conf=" + conf_name;
	
	return completeURL = reportURL;
}



function getUniqueReportID (reportName) {
	/**
		This function will create a unique ID for a report, based on the report's name.
		
		Arguments:
		- reportName
			The name of the report; we'll use this name for the unique ID.
	**/
	
	//var id = replaceAll(" ","",reportName.toLowerCase());
	// var id = replaceAll(" ","",reportName);
	// id = replaceAll("/","",id.split("--")[0]);
	// var tstamp = new Date();
	// var chosenID = id + "--" + tstamp.getTime();
	
	var id = replaceAll("/","",reportName);
	var tstamp = new Date();
	var chosenID = id + "--" + tstamp.getTime();
	
	return chosenID;
}



function applyOptionsToReport (reportID, currentReportOptions) {
	/**
		We'll apply the options to the report, saving the options in a JQuery Data Object.
		
		Arguments:
		- reportID
			The ID of the report.
		- currentReportOptions
			The options of the report.
	**/
	
	$("#" + reportID).attr("options", JSON.stringify(currentReportOptions));
}



function clearReportOptionsCache () {
	/**
		We'll clear/reset the global reportoptions.
		
		Arguments:
			None.
	**/
	
	if(reportOptions != undefined) {
		var tmp_minimumDate = reportOptions.minimumDate;
		var tmp_maximumDate = reportOptions.maximumDate;
		
		delete reportOptions;
		reportOptions = {};
		reportOptions.minimumDate = tmp_minimumDate; // "<?php echo date("m")."/01/".date("Y"); ?>";
		reportOptions.maximumDate = tmp_maximumDate; // "<?php echo date("m")."/".date("d")."/".date("Y"); ?>";
	} else {
		reportOptions = {};
		reportOptions.minimumDate = "<?php echo "01"." ".date("M")." ".date("Y"); ?>";
		reportOptions.maximumDate = "<?php echo date("d")." ".date("M")." ".date("Y"); ?>";
	}
}



function doAjaxRequest (elementSelector, loadURL, callback_func, error_func, isAsynchronous) {
	/**
		The name says it all; We're going to do an AJAX Request.
		
		Arguments:
		- elementSelector
			A JQuery selector of the container of the result of the request. This can be anything - as long as the element exists.
		- loadURL
			The URL which we're going to load through AJAX.
		- callback_func [optional]
			The function we're going to call once the requests succeeds.
		- error_func [optional]
			The function we're going to call, should the request fail.
		- isAsynchronous [optional]
			A boolean containing whether we'll do an asynchronous request, or a synchronous request.
			By default we'll do a synchronous request.
	**/
	
	if(callback_func == undefined) {
		callback_func = function() {};
	}
	if(error_func == undefined) {
		error_func = function() {};
	}
	if(isAsynchronous == undefined) {
		isAsynchronous = false;
	}
	
	ajax_requests[elementSelector] = $.ajax({
		url: loadURL,
		async: isAsynchronous,
		success: function(result, status) {
			try {
				$(elementSelector).html(result);
			} catch(err) {
				try {
					console.log(err);
				} catch(err) {
					/* hoer ie */
				}
			}
			callback_func();
			
			debugToConsole("Succeeded AJAX Request from " + loadURL + " to " + elementSelector);
		}, error: function(result, status) {
			error_func();
			
			debugToConsole("Failed AJAX Request from " + loadURL + " to " + elementSelector);
		}
	});
}



function autosaveDashboard (callback_func, instantly) {
	autosave_timer;
	
	if(instantly == undefined) {
		instantly = false;
	}
	
	<?php # Prevent undefined error ?>
	if(callback_func == undefined) {
		callback_func = function() {};
	}
	
	clearTimeout(autosave_timer);
	
	if($("#report_area .report_icon[name='Autosaved Workspace'][startup=1]").length > 0) {
		var dashboard_startup = 1;
	} else {
		var dashboard_startup = 0;
	}
	
	if(instantly == false) {
		autosave_timer = setTimeout(function() {
			var current_time = new Date();
			current_time = current_time.getMonth() + "/" + current_time.getDate() + "/" + current_time.getFullYear() + ", " + current_time.getHours() + ":" + current_time.getMinutes() + ":" + current_time.getSeconds();
			
			var dashboard_name = "Autosaved Workspace";
			var dashboard_description = "This is a dashboard that has been autosaved on " + current_time + ".";
			var dashboard_icon = "autosave";
			
			var dashboard_data = prepareSaveDashboard(dashboard_name, dashboard_description, dashboard_icon, dashboard_startup, "1");
			
			saveDashboard(dashboard_name, dashboard_data, callback_func);
		}, 5000);
	} else {
		var current_time = new Date();
		current_time = current_time.getMonth() + "/" + current_time.getDate() + "/" + current_time.getFullYear() + ", " + current_time.getHours() + ":" + current_time.getMinutes() + ":" + current_time.getSeconds();
		
		var dashboard_name = "Autosaved Workspace";
		var dashboard_description = "This is a dashboard that has been autosaved on " + current_time + ".";
		var dashboard_icon = "autosave";
		var dashboard_startup = 0;
		
		var dashboard_data = prepareSaveDashboard(dashboard_name, dashboard_description, dashboard_icon, dashboard_startup, "1");
		saveDashboard(dashboard_name, dashboard_data, callback_func);
	}
}



function prepareSaveDashboard(dashboard_name, dashboard_description, dashboard_icon, dashboard_startup, save_reports) {
	/**
		This function will prepare to save the dashboard.
		
		Arguments:
		- dashboard_name [optional]
			The name of the dashboard. If not given, we'll take it from the Save Dashboard Form.
		- dashboard_description [optional]
			The description of the dashboard. If not given, we'll take it from the Save Dashboard Form.
		- dashboard_icon [optional]
			The icon of the dashboard. If not given, we'll take it from the Save Dashboard Form.
		- dashboard_startup [optional]
			Whether to display the dashboard at startup or not. If not given, we'll take it from the Save Dashboard Form.
	**/
	
	if(dashboard_name == undefined) { dashboard_name = $("#savescreenoptions #screenname").val(); }
	if(dashboard_description == undefined) { dashboard_description = $("#savescreenoptions #dashboardDescription").val(); }
	if(dashboard_icon == undefined) { dashboard_icon = $("#savescreenoptions .iconpicker div input:checked").val(); }
	if(dashboard_startup == undefined) { dashboard_startup = $("#startup").is(':checked') == true ? "1" : "0"; }
	if(save_reports == undefined) { save_reports = $("#save_reports").is(':checked') == true ? "1" : "0"; }
	daterange_lock = $("#daterange_lock").is(':checked') == true ? "1" : "0";
	
	if($("#savescreenoptions #original_name").val() != undefined) { var original_name = $("#savescreenoptions #original_name").val(); } else { var original_name = ""; }
	
	var storage = {};
	storage.name = dashboard_name;
	storage.description = dashboard_description;
	storage.icon = dashboard_icon;
	storage.startup = dashboard_startup;
	storage.daterange_lock = daterange_lock;
	
	
	tmp_description = "Contains:\n";
	
	
	if(save_reports == "1") {
		storage.reports = [];
		$(".grid").each(function() {
			var current_grid = [];
			$(this).find(".dialog:not(.note)").each(function() {
				var current_dialog = {};
				current_dialog.label = $(this).attr('id').split("--")[0];
				current_dialog.name = $(this).attr('name');
				current_dialog.url = $(this).attr('href');
				current_grid.push(current_dialog);				
				tmp_description += "- " + current_dialog.name + "\n";			
			});
			storage.reports.push(current_grid);
		});
		if(storage.description.indexOf("Contains:") > -1) {
		} else { 
			storage.description += tmp_description; 
		}
		

	} else {
		if(data_of_all_dashboards[original_name]['reports'] != undefined) {
			storage.reports = data_of_all_dashboards[original_name]['reports'];
		} else {
			storage.reports = [];
		}
	}
	
	return JSON.stringify(storage);
}



function saveDashboard (dashboard_name, dashboard_data, callback_func) {
	/**
		This function will save the dashboard.
		
		Arguments:
		- dashboard_name
			The name of the dashboard.
		- dashboard_data
			The data of the dashboard.
	**/
	
	if(callback_func == undefined) {
		callback_func = function() {};
	}
	
	$.post('includes/savedashboard.php', { saveDashboard: dashboard_data, dashboardname: dashboard_name, conf:'<?php echo $conf;?>' }, function(result) {
		dashboard_data = JSON.parse(dashboard_data);
		data_of_all_dashboards[dashboard_name] = dashboard_data;
		
		if($(".report_icon.dashboard[name='" + dashboard_name + "']").is(":hidden") == true) {
			var iconStyling = 'display: none;';
		} else {
			var iconStyling = 'display: block;';
		}
		
		$(".report_icon.dashboard[name='" + dashboard_name + "']").remove();
		
		var dashboardicon = "<li style='" + iconStyling + "' startup=\"" + dashboard_data['startup'] + "\" src=\"dashboard\" rel='" + new Date().getTime() + "' name=\"" + dashboard_name + "\" title=\"" + dashboard_data['description'] + "\" class=\"dashboard report_icon\">";
		dashboardicon += "<img src='images/icons/dashboards/" + dashboard_data['icon'] + ".png' />";
		dashboardicon += "<span>" + dashboard_name + "</span>";
		dashboardicon += "</li>";
		
		$("#report_area ul:first").append(dashboardicon);
		
		var sorted_icons = {};
		$("#report_area ul:first li").each(function() {
			sorted_icons[$(this).find('span').html()] = $(this).clone();
		});
		
		sorted_icons = sortObject(sorted_icons);
		
		$("#report_area ul:first li").remove();
		
		var c = 0;
		for(var sorted_icon in sorted_icons) {
			if(c == 0) { sorted_icons[sorted_icon].addClass("first"); }
			sorted_icons[sorted_icon].appendTo("#report_area ul:first");
			c++;
		}
		
		$(".report_icon.dashboard").removeClass("active");
		$(".report_icon.dashboard[name='" + dashboard_name + "']").addClass("active");
		$('.delete-dashboard').show(); // We have opened a dashboard, so we can delete it, if we want to.
		
		callback_func();
	});
}



function deleteDashboard(dashboard_to_delete, skip_confirm) {
	if(dashboard_to_delete == undefined) {
		var dashboard_to_delete = $("#report_area .dashboard.report_icon.active").attr('name');
	}
	
	if(skip_confirm == undefined) {
		var skip_confirm = false;
	}
	
	if($("#report_area .dashboard.report_icon.active").attr('name')) {
		var opened_dashboard = $("#report_area .dashboard.report_icon.active").attr('name');
	} else {
		var opened_dashboard = "";
	}
	
	if(skip_confirm === true) {
		var really_delete_dashboard = true;
	} else {
		var really_delete_dashboard = confirm("<?php echo _ARE_YOU_SURE_YOU_WANT_TO_DELETE_THE; ?> '" + dashboard_to_delete + "' <?php echo _DASHBOARD; ?>?");
	}
	if(really_delete_dashboard == true) {
		if(dashboard_to_delete == opened_dashboard) {
			closeAll();
		}
		
		clearReportOptionsCache();
		$.get('includes/savedashboard.php',{ dashboardname: dashboard_to_delete, saveDashboard: "delete", conf:'<?php echo $conf;?>' },function(result) {
			$(".report_icon.dashboard[name='" + dashboard_to_delete + "']").remove();
			createBalloon(dashboard_to_delete + " <?php echo _HAS_BEEN_DELETED; ?>!");
			
			$(".report_options .report_icon img").attr("src","images/pixel.gif");
			$(".report_options .current_report").html("<label rel=''></label>");
			$(".report_options .current_options").html("");
			$("#report_area_extension .form_buttons #addToDashboard").remove();
			$("#report_area_extension .form_buttons #addToDashboardAndClose").remove();
			$("#report_area_extension .report_area_usage_info").show();
			$("#info_frame").css('height', '300px');
		});
	}
}



function sortObject(o) {
    var sorted = {},
    key, a = [];

    for (key in o) {
        if (o.hasOwnProperty(key)) {
                a.push(key);
        }
    }

    a.sort();

    for (key = 0; key < a.length; key++) {
        sorted[a[key]] = o[a[key]];
    }
    return sorted;
}



function checkIfGridExists(gridIndex) {
	/**
		This function checks if a grid exists and returns that grid's index. If it doesn't exist, this function will count down, untill it has found an existing grid.
		
		Arguments:
		- gridIndex
			The index of the grid that's being checked for existence.
	**/
	
	if($("#report-grid .grid:eq(" + gridIndex + ")").length > 0) {
		return gridIndex;
	} else {
		return checkIfGridExists(gridIndex - 1);
	}
}



function getSmallestGrid() {
	/**
		This function will return the index of the smallest grid.
		
		Arguments:
			None.
	**/
	
	var SmallestGrid = 0;
	var tallest = 100000000;
	$("#report-grid .grid").each(function(index) {
		var thisHeight = $(this).height();
		if(thisHeight < tallest) {
			tallest = thisHeight;
			SmallestGrid = index;
		}
	});
	
	return SmallestGrid;
}



function getLargestGrid() {
	/**
		This function will return the index of the smallest grid.
		
		Arguments:
			None.
	**/
	
	var LargestGrid = 0;
	var tallest = 0;
	$("#report-grid .grid").each(function(index) {
		var thisHeight = $(this).height();
		if(thisHeight > tallest) {
			tallest = thisHeight;
			LargestGrid = index;
		}
	});
	
	return LargestGrid;
}





/** <-------------------------------------------------------------------------------- Actions --------------------------------------------------------------------------------> **/





function createGrids () {
	/* Let's decide how many grids we have to create. */
	var amountOfGridsUnclean = ($(window).width() / 600);
	var amountOfGrids = Math.floor($(window).width() / 600);
	if(amountOfGrids == 0) { amountOfGrids = 1; }
	
	for(var count = 0; count < amountOfGrids; count++) {
		$("#report-grid").append("<div style='width: " + (100 / amountOfGrids) + "%;' class='grid'></div>");
		$(".workbar .workicons").append("<div rel='" + count + "' class='workbar_grid'></div>");
	}
	
	$(".workbar .workicons .workbar_grid:first").css("border","0");
	
	$(".workbar .workicons .workbar_grid").sortable({
		items: '.icon',
		connectWith: '.workbar_grid',
		opacity: 0.3, // opacity of the element while draging
		placeholder: 'ui-state-highlight',
        helper: 'clone',
        forcePlaceholderSize: true,
        forceHelperSize: true,
		tolerance: 'pointer',
		distance: 10,
		start: function(event, ui) {
			current_drag = ui.item.index();
		},
		stop: function(event, ui) {
			var dialogID = ui.item.attr('rel');
			
			if(ui.item.index() > current_drag) {
				var neighbour = ui.item.prev().attr('rel');
				var new_dialog = $("#" + dialogID).clone();
				$("#" + dialogID).remove();
				new_dialog.insertAfter("#content #report-grid .grid:eq(" + ui.item.parent().index() + ") #" + neighbour);
			} else if(ui.item.index() <= current_drag) {
				var neighbour = ui.item.next().attr('rel');
				var new_dialog = $("#" + dialogID).clone();
				$("#" + dialogID).remove();
				if(neighbour != undefined) {
					new_dialog.insertBefore("#content #report-grid .grid:eq(" + ui.item.parent().index() + ") #" + neighbour);
				} else {
					if(ui.item.index() > 0) {
						$("#content #report-grid .grid:eq(" + ui.item.parent().index() + ")").append(new_dialog);
					} else {
						$("#content #report-grid .grid:eq(" + ui.item.parent().index() + ")").prepend(new_dialog);
					}
				}
			}
			
			if(new_dialog.find(".graphcontainer").length > 0) {
				loadReport(new_dialog.attr("id"), new_dialog.attr("href"));
			}
			
			$("#" + new_dialog.attr("id")).css('box-shadow', 0);
			
			autosaveDashboard();
		}
	});
}

function hoverWorkbarIcon(element) {
	if($("#report_area").is(":hidden") == true) {
		var p = $('#' + element);
		var position = p.position();
		$("html").scrollTop(position.top - ($("#north").outerHeight()+10));
	}
}

function getStoreReports (received_reports) {
	for(var report_key in received_reports) {
		if(received_reports[report_key]['package'] != undefined) {
			var bundle = received_reports[report_key]['package'].split(",");
			
			for(var bundle_key in bundle) {
				if($.inArray(bundle[bundle_key], class_reports) >= 0) {
					delete received_reports[report_key];
				}
			}
		} else {
			if($.inArray(received_reports[report_key]['sku'], class_reports) >= 0) {
				delete received_reports[report_key];
			}
		}
	}
	shop_reports = received_reports;
	createStoreList();
	fixAfterWindowResize();
}



function createStoreList () {
	shop_reports;
	
	for(var key in shop_reports) {
		if($(".report-category[rel=" + shop_reports[key]['category'] + "]").hasClass("hidden") == true) {
			var styleDeclaration = " style='display: none;' ";
		} else {
			var styleDeclaration = " style='display: block;' ";
		}
		
		$(".report-category[rel=" + shop_reports[key]['category'] + "]").next('ul').append("<li " + styleDeclaration + " name='" + shop_reports[key]['name'] + "' title='<?php echo _YOU_HAVE_NOT_UNLOCKED_THIS_REPORT_CLICK_TO_UNLOCK; ?>' rel='" + shop_reports[key]['product_id'] + "' class='reportstore_icon'><a style='display: block; width: 100%; height: 100%; text-decoration: none;' href='<?php echo LOGAHOLIC_REPORT_STORE_LOCATION; ?>index.php?route=product/product&product_id=" + shop_reports[key]['product_id'] + "&tracking=<?php echo $template->AffiliateID(); ?>&return_url=<?php echo $template->ReturnURL(); ?>'><img src='<?php echo LOGAHOLIC_REPORT_STORE_LOCATION; ?>image/" + shop_reports[key]['image'] + "' /><span>" + shop_reports[key]['name'] + "</span></a><img class='locked_icon' src='images/icons/16x16/object-locked.png' /><div class='store_tooltip'></div><span class='report_price'>&euro; " + shop_reports[key]['price'] + "</span></li>");
	}
}



function getRandomStoreReport () {
	var tmp_shop_reports = new Array();
	for(var shop_report in shop_reports) {
		tmp_shop_reports.push(shop_reports[shop_report]);
	}
	
	var randomReport = tmp_shop_reports[Math.floor(Math.random() * parseFloat(tmp_shop_reports.length))];
	
	if($(".reportstore_icon[rel='" + randomReport['product_id'] + "']").length >= 1) {
		randomReport = getRandomStoreReport();
	}
	
	return randomReport;
}



function FinishInitialization () {
	/**
		We'll finish the initialization of the interface.
		
		Arguments:
			None.
	**/
	
	// $(".reloadiframethingy").live("click", function() {
		// $("#info_frame").attr('src', $("#info_frame").attr('src'));
	// });
	
	// $(".hideiframethingy").live("click", function() {
		// if($("#info_frame").css('height') == '0px') {
			// $("#info_frame").css('height', '300px');
		// } else {
			// $("#info_frame").css('height', '0px');
		// }
	// });
	
	fixAfterWindowResize(); // We'll mimic a window resize, just in case.
	
	createGrids();
	
	$(".workbar").show();
	
	$(".reportstore_button > span > span").html("Get More Reports");
	
	
	<?php if($update_running == 'yes') { ?>
	/* Update Log */
	var updateInterval = setInterval(function() {
		$.ajax({
			url: 'components/import/read_update_progress.php',
			async: true,
			data: {
				conf: conf_name
			},
			success: function(result, status) {
				if(result.substr(0, "Error:".length) == "Error:") {
					$(".updateProgressText").addClass('error');
					changeUpdateText(result);
					
					$(".updating_progress").remove();
					clearInterval(updateInterval);
					
					fixAfterWindowResize();
				} else if(result.substr(0, "[Finished]".length) == "[Finished]") {
					$(".updateProgressText, .updating_progress").remove();
					clearInterval(updateInterval);
					
					fixAfterWindowResize();
				} else {
					changeUpdateText(result);
				}
			}
		});
		
		$(".updating_progress .ui-progressbar-value").css('marginLeft', "0");
		
		$(".updating_progress .ui-progressbar-value").animate({
			marginLeft: "100%"
		}, 4000, "linear");
	}, 5000);
	<?php } ?>
	
	if(openStartupDashboard(startup_dashboard) == false) {
		$(".summon-reports").addClass("active");
		$("#report_panel").show();
		$("#report_area").show();
		$("#report_area_extension").show();
		$("#report_area .search_in_reports").focus();
		
		if($("body#v3").length > 0) {
			$("#north").addClass('shadowless');
			$(".workbar").addClass('shadowless');
		}
		
		$(".delete-dashboard").hide();
		
		$("#info_frame").ready(function() {
			$("#info_frame").css('height', '300px');
		});
	}
	
	createStoreList();
	
	var StoreScript = document.createElement('script');
	StoreScript.type = 'text/javascript';
	StoreScript.src = 'http://www.logaholic.com/logadl/report_delivery/getFeatured.php';
	document.getElementsByTagName('head')[0].appendChild(StoreScript);
	
	fixAfterWindowResize();
}



function debugToConsole (NewBlockDebugMessage) {
	<?php if(!empty($debug)) { ?>
	if(NewBlockDebugMessage == undefined) { NewBlockDebugMessage = ""; }
	if(DebugWindow.closed == true) {
		delete DebugWindow;
		
		DebugWindow = window.open(
			'',
			'mywindow',
			'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,copyhistory=no,resizable=yes'
		)
		DebugWindowContent = DebugWindow.document;
		DebugWindowContent.write("<style type='text/css'>body, html { margin: 0; padding: 0; } * { font-family: Lucida Console, Sans-Serif; font-size: 12px; } .debug { background-color: #000; color: #0F0; padding-bottom: 26px; } .mysql_debug { background-color: #333; color: #FFF; } </style>");
	}
	
	if(NewBlockDebugMessage != "") {
		DebugWindowContent.writeln("<div style='width: 100%; background-color: #FFF; color: #333; min-height: 30px; font-size: 16px; margin: 5px 0; border: 1px solid red; border-left: 0; border-right: 0;'><div style='padding: 5px 10px;'>" + NewBlockDebugMessage + "</div></div>");
	}
	
	$(".err_msg, .debug:not('#DebugConsole .debug')").each(function() {
		DebugWindowContent.writeln("<div style='width: 100%;' class='debug'>" + $(this).html() + "</div>");
		if($(this).hasClass('error') == false && $(this).is('.err_msg') == false) {
			$(this).remove();
		}
	});
	<?php } ?>
}



function hideReportArea () {
	/**
		This function hides the Report Area and Report Options.
		
		Arguments:
			None.
	**/
	
	$("#report_area, #report_area_extension").stop();
	$("#report_area").css("opacity", 1);
	$("#report_area_extension").css("opacity", 1);
	$(".summon-reports").removeClass("active");
	$("#report_panel").hide();
	$("#report_area").hide();
	$("#report_area_extension").hide();
	$("#report_area_extension .report_options").hide();
	$("#report_area_extension .form_buttons #addToDashboard").remove();
	$("#report_area_extension .form_buttons #addToDashboardAndClose").remove();
	$("#report_area_extension .report_area_usage_info").show();
	$("#info_frame").css('height', '0px');
	$("#info_frame").ready(function() {
			$("#info_frame").css('height', '0px');
	});
	var info_frame_url = $("#info_frame").attr('src', $("#info_frame").attr('src'));
	
	
	$("#north").removeClass('shadowless');
	$(".workbar").removeClass('shadowless');
}



function addToWorkspace () {
	/**
		This function adds the selected report to the Workspace.
		
		Arguments:
			None
	**/
	
	/* If we don't want any of the above options, we'll assume we want to apply the options to a report. */
	var handled_ids = {};
	var reportoptions = {};
	$(".report_options").find('input:not(.noOption), select:not(.noOption), textarea:not(.noOption)').each(function() {
		if($(this).attr('id') == undefined || $(this).attr('id') == '') {
			var optionID = $(this).attr('name');
		} else {
			var optionID = $(this).attr('id');
		}
		if($(this).attr('type') != undefined && ($(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio')) {
			if($(this).is(":checked") == true) {
				if($(this).attr('value') != undefined) {
					reportoptions[optionID] = $(this).attr('value');
				} else {
					reportoptions[optionID] = true;
				}
			}
		} else if($(this).is('select') == true) {
			reportoptions[optionID] = $(this).val();
		} else {
			if(optionID.indexOf("[]") != -1) {
				var tmp_id = optionID.replace("[]","");
				if(handled_ids[tmp_id] != undefined) { handled_ids[tmp_id] ++; } else { handled_ids[tmp_id] = 1; }
				reportoptions[tmp_id + handled_ids[tmp_id]] = $(this).val();
			} else if(optionID == 'minimumDate' || optionID == 'maximumDate') {
				reportoptions[optionID] = $(this).val();
			} else {
				reportoptions[optionID] = php_urlencode($(this).val());
			}
		}
	});
	
	setDefaultOptions(reportoptions);
	
	if($(".report_options .current_report label").attr('rel') == "global") {
		$(".dialog").each(function() {
			var reportID = $(this).attr('id');
			var reporturl = $(this).attr('href');
			
			fillReport(reportID, reporturl, Loading($(this).attr('name')));
			
			$.ajax({
				url: optionsToURL(reporturl, reportoptions),
				async: false,
				success: function(result) {
					autosaveDashboard();
				},
				error: function() {
					fillReport(reportID, reporturl, result);
					autosaveDashboard();
				}
			});
		});
	} else {
		openReport($(".report_options .current_report").attr('src'), $(".report_options .current_report").attr('name'), $(".report_options .current_report > label").attr('rel'), reportoptions);
		autosaveDashboard();
	}
}



function openCategory (category_label) {
	closeAll();
	
	var reporturl = "";
	var tmp_options = defaultOptions;
	
	hideReportArea();
	
	for(var i = 0; i < $("#report_area > .report-category[rel=" + category_label + "] + ul > li").length; i++) {
		tmp_options['labels'] = $("#" + category_label + i).attr('type');
		
		openReport(
			$("#" + category_label + i).attr('href'),
			$("#" + category_label + i).attr('name'),
			$("#" + category_label + i).attr('type'),
			tmp_options,
			true
		);
	}
	
	return;
}



function openStartupDashboard (dashboard_name) {
	/**
		We'll open the dashboard that is configured to open at startup.
		
		Arguments:
		- dashboard_name [optional]
			The name of the dashboard that needs to open on startup
	**/
	
	if(dashboard_name == undefined) {
		if($("#report_area .dashboard.report_icon[startup=1]:first").attr("name") != undefined) {
			dashboard_name = $("#report_area .report_icon[startup=1]:first").attr("name");
		} else {
			cleanStartup = true;
		}
	}
	if(cleanStartup != true) {
		openMultiReport(dashboard_name);
	} else {
		return false;
	}
}



function openDialog (eventTarget) {
	/**
		This function opens a JQuery Dialog and fills it with the eventTarget's HREF attribute.
		
		Arguments:
		- eventTarget
			The element which caused this function to be called.
	**/
	
	if($('#' + eventTarget.attr('id') + 'Dialog').length == 0) { // If the dialog exists, we'll bring it to the front, if it doesn't exist, we'll create it.
		var addClass = '';
		if(eventTarget.attr('id') == 'notebook') { // If we want to open the notes, we'll add a class, so the dialog has its own styling.
			addClass = 'notebook';
		}
		$('#content').prepend('<div id=\'' + eventTarget.attr('id') + 'Dialog' + '\'></div>');
		var dialogID = eventTarget.attr('id') + 'Dialog';
		var dialog = $('#' + dialogID);
		
		if(eventTarget.attr('title') == undefined || eventTarget.attr('title') == "") { // Either the name or the title of the selected element will be used as the header of the new dialog.
			var dialogtitle = eventTarget.attr('name');
		} else {
			var dialogtitle = eventTarget.attr('title');
		}
		
		var url = eventTarget.attr('href');
		$.ajax({
			url: url,
			success: function(result, status) {
				dialog.html(result); // We'll fill the dialog with the contents of the AJAX request.
				dialog.dialog({ close: function() { $(this).remove() }, width: 'auto', height: 'auto', position: ['center','middle'], title: dialogtitle, dialogClass: addClass });
			},
			error: function(result, status) {
				dialog.html(Error("this Window"));
				dialog.dialog({ close: function() { $(this).remove() }, width: 'auto', height: 'auto', position: ['center','middle'], title: dialogtitle, dialogClass: addClass });
			}
		});
	} else {
		bringToFront($('#' + eventTarget.attr('id') + 'Dialog'));
	}
	return false;
}



function openDashboardOptions (editingDashboard) {
	/**
		When you click "save screen" in the menu, we will open a window allowing you to save all reports in the current workspace as a dashboard.
		
		Arguments:
			None.
	**/
	
	if($("#savescreenoptions").length > 0) {
		$("#savescreenoptions").parent().show();
		bringToFront($("#savescreenoptions").parent());
	} else {
		dashboardname = editingDashboard.name;
		dashboarddesc = editingDashboard.description;
		dashboardicon = editingDashboard.icon;
		startup_checked = editingDashboard.startup == "1" ? "checked " : "";
		save_reports = editingDashboard.save_reports == "1" ? "checked " : "";
		daterange_lock = editingDashboard.daterange_lock == "1" ? "checked " : "";
		
		if($(".report_icon.dashboard").length == 0) {
			var startup_checked = "checked ";
		} else if($(".report_icon.dashboard[name=" + dashboardname + "]").attr('startup') == 1) {
			var startup_checked = "checked ";
		} else {
			var startup_checked = "";
		}
		
		/* We'll create the form here */
		var DashboardOptionWindowContents = "<?php echo _ENTER_A_NAME_FOR_THIS_SCREEN;?>:<br/>";
		DashboardOptionWindowContents += "<input type='hidden' id='original_name' value='" + dashboardname + "' /><br/>";
		DashboardOptionWindowContents += "<input type='text' id='screenname' value='" + dashboardname + "' /><br/>";
		DashboardOptionWindowContents += "<select id='overwriteDashboardPicker'>";
		DashboardOptionWindowContents += "<option><?php echo _PICK_AN_EXISTING_DASHBOARD_TO_OVERWRITE; ?></option>";
<?php /* We need to get all dashboard names. */
$c = 0;
while($dashboarddata = $result->FetchRow()) {
$dashboardname = explode(".", $dashboarddata['Name']);
$dashboardname = $dashboardname[2];
?>
		DashboardOptionWindowContents += "<option value='<?php echo $dashboardname; ?>'><?php echo $dashboardname; ?></option>";
<?php } ?>
		DashboardOptionWindowContents += "</select><br/>";
		DashboardOptionWindowContents += "<label for='dashboardDescription'><?php echo _DESCRIPTION; ?>:</label><br/><textarea style='width:475px;' id='dashboardDescription' name='dashboardDescription'>" + dashboarddesc + "</textarea><br/>";
		DashboardOptionWindowContents += "<?php echo _PICK_AN_ICON;?>:<br/>";
		DashboardOptionWindowContents += "<div class='iconpicker' style='overflow-y:hidden;overflow:auto;'>";
			DashboardOptionWindowContents += "<div style='width:<?php echo ((count($iconnames) + 1) * 43);?>px'>";
				DashboardOptionWindowContents += "<?php $c = 0; foreach($iconnames as $iconname) { echo "<div style='float:left;text-align:center;margin:5px;'><img src='{$iconname['url']}' /><br/><input value='{$iconname['name']}' type='radio'"; if(++$c == 1) { echo " checked"; } echo " /></div>"; }?>";
				DashboardOptionWindowContents += "<div style='clear:both;'></div>";
			DashboardOptionWindowContents += "</div>";
		DashboardOptionWindowContents += "</div><br/>";
		DashboardOptionWindowContents += "<input " + daterange_lock + "type='checkbox' id='daterange_lock' /><label for='daterange_lock'>Lock the daterange of reports contained in this dashboard.</label><br/>";
		DashboardOptionWindowContents += "<input " + startup_checked + "type='checkbox' id='startup' /><label for='startup'><?php echo _OPEN_THIS_DASHBOARD_AT_STARTUP;?></label><br/>";
		DashboardOptionWindowContents += "<input " + save_reports + "type='checkbox' id='save_reports' /><label for='save_reports'><?php echo _SAVE_OPEN_WORKSPACE_REPORTS_IN_DASHBOARD; ?></label><br/>";
		
		$("#content").prepend("<div id='savescreenoptions'>" + DashboardOptionWindowContents + "<br/><br/><a class='agree' href='#'><?php echo _SAVE;?></a><a class='decline' href='#'><?php echo _CANCEL;?></a></div>");
		
		// $("#screenname").focus();
		
		$("#savescreenoptions").dialog({
			title: "<?php echo _SAVE_THIS_SCREEN_AS_A_DASHBOARD;?>",
			position: 'center',
			width: 500,
			// modal: true,
			draggable: false,
			resizable: false,
			close: function() {
				$(".ui-widget-overlay").remove();
				$("#savescreenoptions").remove();
			}
		});
		
		$("body").append("<div class='ui-widget-overlay' style='width: 100%; height: 100%; z-index: 120;'></div>");
		
		$("#savescreenoptions .agree").button();
		$("#savescreenoptions .decline").button();
		
		if(dashboardicon != "") {
			$(".iconpicker").find("input").removeAttr("checked");
			$(".iconpicker").find("img").each(function() {
				if($(this).attr('src').indexOf(dashboardicon) >= 0) {
					$(this).parent().find('input').attr("checked","checked");
				}
			});
		}
	}
}



function setDefaultOptions (reportoptions) {
	for(var option_key in reportoptions) {
		if($("#report_area_extension .report_options").find("*[id=" + option_key + "]").hasClass("isDefault") == true) {
			defaultOptions[option_key] = reportoptions[option_key];
		}
	}
}

/* This function will open the option pane with default options */
function showOptions (reportname, reportlabel, reporturl, reportID, optionvalues) {
	if(optionvalues == undefined) { optionvalues = defaultOptions; }
	
	if(optionvalues['minimumDate'] != undefined) {
		if(optionvalues['minimumDate'].split('/').length > 1) {
			// optionvalues['minimumDate'] = frontendDate(optionvalues['minimumDate']);
		}
	}
	if(optionvalues['maximumDate'] != undefined) {
		if(optionvalues['maximumDate'].split('/').length > 1) {
			// optionvalues['maximumDate'] = frontendDate(optionvalues['maximumDate']);
		}
	}
	
	optionvalues['labels'] = reportlabel;
	
	$("#report_panel").show();
	$("#report_area_extension").show();
	$("#info_frame").css('height', '0px');
	$("#report_area_extension .report_options").show();
	$("#report_area_extension .report_area_usage_info").hide();
	
	$("#report_area_extension .form_buttons #addToDashboard").remove();
	$("#report_area_extension .form_buttons #addToDashboardAndClose").remove();
	
	$(".report_options .current_report").html("<label rel='" + reportlabel + "'>" + reportname + "</label>");
	$(".report_options .current_report").attr('name', reportname);
	$(".report_options .current_report").removeAttr('rel');
	
	if($("#report_area .report_icon[type=" + reportlabel + "] img").attr('src') != undefined) {
		var iconsource = $("#report_area .report_icon[type=" + reportlabel + "] img").attr('src');
	} else {
		var iconsource = "images/icons/32x32/unknown.png";
	}
	$(".report_options .report_icon img").attr('src', iconsource);
	
	if(reportID != undefined) {
		$(".report_options .current_report").attr('rel', reportID);
	}
	if(reporturl != undefined) {
		$(".report_options .current_report").attr('src', reporturl);
	}
	
	$("#report_area_extension .report_options .current_options").html(Loading("options for " + reportname, "loader.gif"));
	
	$.ajax({
		type: 'POST',
		url: optionsToURL('reportoptions.php', optionvalues),
		async: true,
		success: function(result) {
			/* We'll print the received options form. */
			$("#report_area_extension .current_options").html(result);
			
			/* We'll print the buttons we need. */
			if($("#report_area").is(":hidden") == true) {
				/* The Report Area is not opened, so we'll only have the 'Go' button. */
				if($("#report_area_extension .form_buttons #addToDashboardAndClose").length == 0) {
					$("#report_area_extension .form_buttons").prepend("<input id='addToDashboardAndClose' class='noOption green_submit' type='submit' value='Go' />");
				}
			} else {
				/* We can both 'Add' and Add & 'Go'. */
				if($("#report_area_extension .form_buttons #addToDashboardAndClose").length == 0) {
					$("#report_area_extension .form_buttons").prepend("<input id='addToDashboardAndClose' class='noOption green_submit' type='submit' value='Go' />");
				}
				if($("#report_area_extension .form_buttons #addToDashboard").length == 0) {
					$("#report_area_extension .form_buttons").append("<a id='addToDashboard' href='#'>+ Add and continue</a>");
				}
			}
			
			if($(".report_options").find("#daterangeField").length > 0) {
				applyDatepicker();
			}
		}
	});
}

function openReport(reporturl, reportname, reportlabel, reportoptions, running_multiple) {
	if(running_multiple == undefined) { running_multiple = false; }
	if(running_multiple == true) { var animate = false; } else { var animate = true; }
	
	var reportID = getUniqueReportID(reportlabel);
	
	createNote(reportoptions.minimumDate, reportoptions.maximumDate);
	
	url = optionsToURL(reporturl, reportoptions);
	
	if(running_multiple == true) {
		$(".current_report").attr('rel', reportID);
	}
	
	if($("#" + $(".current_report").attr('rel')).length == 0) {
		$(".current_report").attr('rel', reportID);
		newReport(reportID, reportname, reportlabel, url, Loading(reportname), 'prepend', undefined, animate);
	}
	
	loadReport($(".current_report").attr('rel'), url, true);
}

function newReport(reportID, reportname, reportlabel, url, content, addingTechnique, addToGrid, animate) {
	if(addToGrid == undefined) {
		var printToGrid = getSmallestGrid();
	} else {
		var printToGrid = checkIfGridExists(addToGrid);
	}
	
	if(animate == undefined) { var animate = true; }
	
	if(reportID == undefined) {
		var reportID = getUniqueReportID(reportlabel);
	}
	
	if($("#report_area .report_icon[type=" + reportID.split("--")[0] + "] img").attr('src') != undefined) {
		var iconsource = $("#report_area .report_icon[type=" + reportID.split("--")[0] + "] img").attr('src');
	} else {
		var iconsource = "images/icons/32x32/unknown.png";
	}
	
	var dialog = "<div id='" + reportID + "' name='" + reportname + "' href='" + url + "' class='dialog'>";
		dialog += "<div class='dialog-header'>";
            dialog += "<label rel='" + reportlabel + "' class='dialog-label'>" + reportname + "</label>";
			dialog += "<?php echo UIDIalogButtons(); ?>";
		dialog += "</div>";
		dialog += "<div class='dialog-content'>" + content + "</div>";
	dialog += "</div>";
	
	if(addingTechnique == 'prepend') {
		$("#content #report-grid .grid:eq(" + printToGrid + ")").prepend(dialog);
		$(".workbar .workicons .workbar_grid:eq(" + printToGrid + ")").prepend("<div class='icon' name='" + reportname + "' title='" + reportname + "' rel='" + reportID + "'><img src='" + iconsource + "'/></div>");
	} else {
		$("#content #report-grid .grid:eq(" + printToGrid + ")").append(dialog);
		$(".workbar .workicons .workbar_grid:eq(" + printToGrid + ")").append("<div class='icon' name='" + reportname + "' title='" + reportname + "' rel='" + reportID + "'><img src='" + iconsource + "'/></div>");
	}
	
	attachReportEvents(reportID);
	
	if(animate == true) {
		/* Animate the icon of this report to the workbar. */
		$("#interface-overlay .interface_anims").html("<img class='anim' src='" + iconsource + "'/>");
		$("#interface-overlay .interface_anims .anim").css("top", $("#report_area .report_icon[type=" + reportID.split("--")[0] + "]").offset().top);
		$("#interface-overlay .interface_anims .anim").css("left", $("#report_area .report_icon[type=" + reportID.split("--")[0] + "]").offset().left);
		$("#interface-overlay").show();
		
		icon_position = {
			top: $(".workbar .workicons .workbar_grid:eq(" + printToGrid + ") .icon[rel='" + reportID + "']").offset().top,
			left: $(".workbar .workicons .workbar_grid:eq(" + printToGrid + ") .icon[rel='" + reportID + "']").offset().left
		};
		
		$(".workbar .workicons .workbar_grid:eq(" + printToGrid + ") .icon[rel='" + reportID + "']").css("display", "none");
		
		$("#interface-overlay .interface_anims .anim").animate({
				top: icon_position.top,
				left: icon_position.left
			}, 750, function() {
				$(".workbar .workicons .workbar_grid:eq(" + printToGrid + ") .icon[rel='" + reportID + "']").show();
				$(".workbar .workicons .workbar_grid:eq(" + printToGrid + ") .icon[rel='" + reportID + "']").effect("bounce");
				$("#interface-overlay .interface_anims .anim").remove();
				$("#interface-overlay").css("display", "none");
			}
		);
	}
	
	return reportID;
}

function fillReport(reportID, url, content) {
	$("#" + reportID).attr("href", url);
	$("#" + reportID + " .dialog-content").html(content);
}

function showMultiOptions(dashboardname) {
	var optionvalues = {
		minimumDate: defaultOptions.minimumDate,
		maximumDate: defaultOptions.maximumDate,
		dashboardname: dashboardname
	}
	
	$("#report_panel").show();
	$("#report_area_extension").show();
	$("#info_frame").css('height', '0px');
	$("#report_area_extension .report_options").show();
	$("#report_area_extension .report_area_usage_info").hide();
	
	$("#report_area_extension .form_buttons #addToDashboard").remove();
	$("#report_area_extension .form_buttons #addToDashboardAndClose").remove();
	
	if(dashboardname == 'global') {
		$(".report_options .current_report").html("Current Workspace");
	} else {
		$(".report_options .current_report").html(dashboardname);
	}
	$(".report_options .current_report").attr('name', dashboardname);
	$(".report_options .current_report").attr('rel', 'dashboard');
	
	$.ajax({
		url: optionsToURL('dashboardoptions.php', optionvalues),
		async: true,
		success: function(result) {
			/* We'll print the received options form. */
			$("#report_area_extension .current_options").html(result);
			
			if($("#report_area_extension .form_buttons #addToDashboardAndClose").length == 0) {
				$("#report_area_extension .form_buttons").prepend("<input id='addToDashboardAndClose' class='noOption green_submit' type='submit' value='Go' />");
			}
			
			if($(".report_options").find("#daterangeField").length > 0) { // Do we have a date range field?
				// Let's add a Daterange Picker.
				applyDatepicker();
			}
			
			$("#ui-datepicker-div").remove(); // We need to fix a stupid glitch in the daterangepicker.
		}
	});
}

function openMultiReport (dashboardname) {
	var reportoptions = {};
	
	if($(".report_options #daterangeField").val() != undefined) {
		var tmp_date = $(".report_options #daterangeField").val().split(" - ");
		if(tmp_date.length < 2) {
			reportoptions.minimumDate = tmp_date[0];
			reportoptions.maximumDate = tmp_date[0];
		} else {
			reportoptions.minimumDate = tmp_date[0];
			reportoptions.maximumDate = tmp_date[1];
		}
		
		setDefaultOptions(reportoptions);
	} else {
		reportoptions.minimumDate = defaultOptions.minimumDate;
		reportoptions.maximumDate = defaultOptions.maximumDate;
	}
	
	if(dashboardname != 'global') {
		closeAll();
		
		for(var c in data_of_all_dashboards[dashboardname]['reports']) {
			for(var x in data_of_all_dashboards[dashboardname]['reports'][c]) {
				var cur_report = data_of_all_dashboards[dashboardname]['reports'][c][x];
				
				var cur_id = getUniqueReportID(cur_report['label']);
				newReport(cur_id, cur_report['name'], cur_report['label'], cur_report['url'], Loading(cur_report['name']), 'append', c, false);
			}
		}
		
		$("#report_area .report_icon").removeClass('active');
		$("#report_area .dashboard.report_icon[name='" + dashboardname + "']").addClass('active');
		$('.delete-dashboard').show();
	}
	
	$(".dialog").each(function() {
		if(dashboardname != 'global') {
			if(data_of_all_dashboards[dashboardname]['daterange_lock'] == 1) {
				var tmp_options = urlToOptions($(this).attr('href'));
				
				reportoptions.minimumDate = tmp_options.minimumDate;
				reportoptions.maximumDate = tmp_options.maximumDate;
			}
		}
		
		loadReport($(this).attr('id'), optionsToURL($(this).attr('href'), reportoptions));
	});
	
	createNote(reportoptions.minimumDate, reportoptions.maximumDate);
}

function loadReport(reportID, reportURL, showLoading) {
	if(showLoading == undefined) { showLoading = true; }
	
	if(showLoading == true) {
		var options = urlToOptions(reportURL);
		
		fillReport(reportID, reportURL, windowHeader(options['minimumDate'], options['maximumDate']) + Loading($("#" + reportID).attr('name')));
	}
	
	$.ajax({
		type: 'POST',
		url: reportURL + "&nocache=1",
		async: true,
		success: function(result) {
			fillReport(reportID, reportURL, result);
		}
	});
}

function optionsToURL(url, options) {
	var url_params = {
		conf: conf_name
	};
	
	var url_parts = url.split("?")[1];
	
	if(url_parts != undefined) {
		url_parts = url_parts.split("&");
		
		for(var c in url_parts) {
			var tmp_url_parts = url_parts[c].split("=");
			url_params[tmp_url_parts[0]] = tmp_url_parts[1];
		}
	}
	
	var c = 0;
	for(var option_key in options) {
		if(option_key == 'minimumDate' || option_key == 'maximumDate') {
			// options[option_key] = backendDate(options[option_key]);
			options[option_key] = options[option_key];
		}
		
		url_params[option_key] = options[option_key];
		c++;
	}
	
	var c = 0;
	var parsed_url = url.split("?")[0] + "?";
	for(var param_key in url_params) {
		if(c > 0) { parsed_url += "&"; }
		parsed_url += param_key + "=" + url_params[param_key];
		c++;
	}
	
	return parsed_url;
}

function urlToOptions(reporturl) {
	var options = {};
	var tmp_url = reporturl.split("?");
	tmp_url = tmp_url[1];
	tmp_url = tmp_url.split("&");
	
	for(var x in tmp_url) {
		options[tmp_url[x].split("=")[0]] = tmp_url[x].split("=")[1];
	}
	
	return options;
}

/** Return the date as, for example, "12 Feb 2012" if the date is 12th of February 2012  **/
function frontendDate(dateToFormat) {
	if(dateToFormat.split(' ').length > 1) {
		return dateToFormat;
	} else {
		return dateToFormat.split('/')[1] + ' ' + monthnames[dateToFormat.split('/')[0] - 1] + ' ' + dateToFormat.split('/')[2];
	}
}

/** Return the date as, for example, "02/12/2012" if the date is 12th of February 2012 **/
function backendDate(dateToFormat) {
	if(dateToFormat.split('/').length > 1) {
		return dateToFormat;
	} else {
		if(monthnumbers[dateToFormat.split(' ')[1]].length == 1) {
			return '0' + monthnumbers[dateToFormat.split(' ')[1]] + '/' + dateToFormat.split(' ')[0] + '/' + dateToFormat.split(' ')[2];
		} else {
			return monthnumbers[dateToFormat.split(' ')[1]] + '/' + dateToFormat.split(' ')[0] + '/' + dateToFormat.split(' ')[2];
		}
	}
}



function fixAfterWindowResize () {
	/**
		This function fixes some height/width issues when the screen is being resized, or a scrollbar (dis)appears.
		
		Arguments:
			None.
	**/
	
	$("body").css("margin-bottom", $("#north").outerHeight());
	$("#report_panel").css("margin-top", $("#north").outerHeight());
	$("#report_area").css("width", ($(window).width() - ($("#report_area_extension").outerWidth() + $(".workbar").outerWidth())));
	$("#report_area").css("height", ($(document).height() - $("#north").height()));
	$("#report_area_extension").css("height", ($(window).height() - $("#north").height() - 5));
	$("#report_area_extension").css("top", $("#north").height());
	$("#report_area_extension").css("left", $(".workbar").width());
	$("#v3 #notifications_and_warnings").css("margin-top", $("#north").outerHeight() + 10 - 100); // -100 is for body margin compensation
	
	if($("#v3 #notifications_and_warnings").outerHeight() > 0) {
		$("#v3 #interface_content").css("margin-top", $("#notifications_and_warnings").outerHeight() + 10 - 75); // -100 is for body margin compensation
	} else if($("#v3 .updateProgressText").outerHeight() > 0) {
		$("#v3 #interface_content").css("margin-top", $("#north").outerHeight() + 10);
	} else {
		$("#v3 #interface_content").css("margin-top", $("#north").outerHeight() + 10 - 100); // -100 is for body margin compensation
	}
	
	if(($(window).height() - ($("#info_frame").height() + 10)) <= ($("#north").outerHeight() + 290)) {
		$("#info_frame").css("top", ($("#north").outerHeight() + 290));
	} else {
		$("#info_frame").css("top", ($(window).height() - ($("#info_frame").height() + 10)));
	}
	
	$("#interface_content").css("margin-left", $(".workbar").outerWidth() + 10 - 60); // -100 is for body margin compensation
	$(".workbar").css("top", $("#north").outerHeight());
	$(".workbar-popout").css("top", $("#north").outerHeight());
	
	$(".lwa_datepicker").css("top", $("#north").outerHeight() + 105);
	
	//$(".lwa_datepicker .quickDateRange").css("top", $("#north").outerHeight() + 150);
	$(".lwa_datepicker_overlay").css("height", $(document).height());
	
	$(".workbar").css("top", $("#north").outerHeight());
	$("#manage_profiles_list").css("top",$("#north").outerHeight());
}



function keepWorkBarTitleUpToDate (reportID, currentReportOptions) {
	/**
		We want to keep the icons in the workbar up to date, so we'll do that.
		
		Arguments:
		- reportID
			This is the ID of the report whose title we'll keep up to date.
		- currentReportOptions
			This contains all options for this dialog.
	**/
	
	currentReportOptions = JSON.parse(currentReportOptions);
	
	var newtitle = $(".workbar .workicons .icon[rel=" + reportID + "]").attr('name');
	if(currentReportOptions.trafficsource != undefined && currentReportOptions.trafficsource != 0) {
		newtitle += " (<?php echo _SEGMENT; ?>: " + $(".current_options #trafficsource option[value='" + currentReportOptions.trafficsource + "']").html() + ")";
	}
	if(currentReportOptions.minimumDate != undefined) {
		newtitle += " from " + currentReportOptions.minimumDate + " to " + currentReportOptions.maximumDate;
	}
	
	$(".workbar .workicons .icon[rel=" + reportID + "]").attr('title', newtitle);
}



function toggleReportHelp (eventTarget) {
	/**
		When the Open Help button of a report is being clicked, we will show or hide the Help of that report.
		
		Arguments:
		- eventTarget
			The element which caused this function to be called.
	**/
	
	if(eventTarget.closest(".dialog").find(".help_content").is(':visible') == true) {
		eventTarget.closest(".dialog").find(".help_content").removeClass("opened");
	} else {
		eventTarget.closest(".dialog").find(".help_content").addClass("opened");
	}
	return false;
}



function toggleReportMail (eventTarget) {
	/**
		When the Open Mail button of a report is being clicked, we will show or hide the Mail form of that report.
		
		Arguments:
		- eventTarget
			The element which caused this function to be called.
	**/

	if(eventTarget.closest(".dialog").find(".mailer").is(':visible') == true) {
		eventTarget.closest(".dialog").find(".mailer").removeClass("opened");
	} else {
		eventTarget.closest(".dialog").find(".mailer").addClass("opened");
	}
	return false;
}



function smallifyReport(largeDialogID) {
	$("#" + largeDialogID).remove();
	
	var dialogID = largeDialogID.substr(0, largeDialogID.length - 6);
	
	$("#" + dialogID).show();
	
	$("#" + dialogID).find('.expand_switch').addClass('expand');
	$("#" + dialogID).find('.expand_switch').removeClass('smallify');
}



function expandReport (dialogID) {
	if($("#large-grid").find("#" + dialogID + "_large").length == 0) {
		var tmp = $("#" + dialogID).clone().attr('id', $("#" + dialogID).clone().attr('id') + '_large');
		
		$("#" + dialogID).hide();
		
		tmp.appendTo("#large-grid");
		
		tmp.find('.expand_switch').addClass('smallify');
		tmp.find('.expand_switch').removeClass('expand');
		
		if(tmp.find('.graph_area').length > 0) {
			tmp.find(".dialog-content").prepend("<div class='reloading_msg'><?php echo _RELOADING; ?> &nbsp;<img src='images/loader.gif'/></div>");
			
			loadReport(tmp.attr('id'), tmp.attr('href'), false);
		}
		$("html").scrollTop(0);
		fixAfterWindowResize();
	}
}



function closeReport (dialogID, callback_func) {
	/**
		This function will remove a report from the DOM.
		
		Arguments:
		- dialogID
			The ID of the report that needs to be closed.
		- callback_func [optional]
			The function that needs to be called, once the report has been removed.
	**/
	
	if(ajax_requests["#" + dialogID + " .dialog-content"] != undefined) {
		ajax_requests["#" + dialogID + " .dialog-content"].abort();
	}
	
	if(dialogID != undefined) {
		/* When the close button of a Dialog is being clicked, we will remove that Dialog. */
		$("#" + dialogID).remove();
		
		$(".workbar .workicons .icon[rel=" + dialogID + "]").remove();
	}
	
	/* If there's no report opened anymore, we'll close the dashboard that was opened. */
	if($(".dialog").length < 1) {
		$("#report_area").removeData("opened_dashboard");
		$("#report_area .dashboard.report_icon").removeClass("active");
		$('.delete-dashboard').hide();
	}
	
	/* If there is a callback function, we'll do that function when we're done closing the report. */
	if(callback_func == undefined) {
		callback_func = function() {};
	}
	callback_func();
}



function minimizeReport (dialogID) {
	/**
		This function will minimize a report.
		
		Arguments:
		- dialogID
			The ID of the report that needs to be minimized.
	**/
	
	var windata = $("#" + dialogID).attr('id').split('--');
	
	var iconsource = $("#report_area .report_icon[rel=" + windata[0] + "] img").attr('src');
	
	$(".workbar .workicons .icon[rel=" + dialogID + "]").addClass("minimized");
	$("#" + dialogID).hide();
}



function restoreReport (dialogID) {
	/**
		This function will unminimize a minimized report.
		
		Arguments:
		- dialogID
			The ID of the report that needs to be restored.
	**/
	
	$(".workbar .workicons .icon[rel=" + dialogID + "]").removeClass("minimized");
	$("#" + dialogID).fadeIn(500);
}



function closeAll () {
	/**
		This function will remove all reports from the DOM.
		
		Arguments:
			None.
	**/
	
	$(".grid").remove();
	$(".workbar_grid").remove();
	
	createGrids();
	
	if($(".workbar-popout-activator").hasClass('activated')) {
		$(".workbar-popout-activator").click();
	}
	
	/* We also want to close the opened dashboard */
	$("#report_area").removeData("opened_dashboard");
	$("#report_area .dashboard.report_icon").removeClass("active");
	$('.delete-dashboard').hide();
	
	fixAfterWindowResize();
}



function minimizeAll () {
	/**
		This function will minimize all reports.
		
		Arguments:
			None.
	**/
	
	$(".workbar .workicons .icon").addClass("minimized");
	$('.dialog:not(:hidden,#notebook)').hide();
	$(".workbar-popout-activator").click();
}



function restoreAll () {
	/**
		This function will unminimize all minimized reports.
		
		Arguments:
			None.
	**/
	
	$('.workbar .workicons .icon').each(function() {
		restoreReport($(this).attr("rel"));
	});
	$(".workbar-popout-activator").click();
}



function bringToFront (object) {
	/**
		This function changes the z-index, so that the object is in front of other objects.
		
		Arguments:
		- object
			The JQuery object that needs it z-index changed.
	**/
	
	$(".dialog, .ui-dialog").css('z-index',0);
	object.css('z-index',99999);
}





/** <-------------------------------------------------------------------------------- Creation --------------------------------------------------------------------------------> **/






function createGauge (gauge_container, gauge_label, gauge_current, gauge_target) {
	if(gauge_label == undefined) { gauge_label = ""; }
	s1 = [gauge_current];
	
	if(gauge_current >= gauge_target) {
		var ticks = [0, (gauge_current * 0.25), (gauge_current * 0.5), (gauge_current * 0.75), gauge_current];
		var intervals = [(gauge_target * 0.25), (gauge_target * 0.75), gauge_target, gauge_current];
	} else {
		var ticks = [0, (gauge_target * 0.25), (gauge_target * 0.5), (gauge_target * 0.75), gauge_target];
		var intervals = [(gauge_target * 0.25), (gauge_target * 0.75), gauge_target];
	}
	
	plots[gauge_container] = $.jqplot(gauge_container,[s1],{
		seriesDefaults: {
			renderer: $.jqplot.MeterGaugeRenderer,
			rendererOptions: {
				label: gauge_label,
				labelPosition: 'bottom',
				labelHeightAdjust: -5,
				ringColor: "#FF8C00",
				tickColor: "#333",
				background: "#FFF",
				intervalOuterRadius: 85,
				ticks: ticks,
				intervals: intervals,
				// intervalColors:['#66cc66', '#E7E658', '#cc6666']
				intervalColors:['#50A5D3', '#1970B4', '#0E4067', '#FF0000']
			}
		}
	});
}

function createFunnel (funnel_container, funnel_labels, funnel_data, funnel_options) {
	for(var key in funnel_data) {
		funnel_data[key] = parseFloat(funnel_data[key]);
	}
	var s1 = funnel_labels;
	var s2 = funnel_data;
	
	applyGraphTooltip(funnel_container);
	
	funnel_options.seriesDefaults.renderer = $.jqplot.FunnelRenderer;
	
	plots[funnel_container] = $.jqplot(funnel_container, [s2], funnel_options);
}

function createWindow (reportLabel, reportName, reportID, content, url, creationMethod, animate, addToGrid) {
	/**
		This function will create a window for reports with all needed attributes.
		
		Arguments:
		- reportName
			The name of the report we're creating this window for.
		- reportID
			The id of the report.
		- content
			The content to be shown in the window (Usually, this is being rewritten once the AJAX request succeeds).
		- url
			The url of the report.
		- creationMethod [optional]
			Whether to append or prepend the window.
		- animate
			Whether to animate the workbar icon or not.
		- addToGrid [optional]
			We can force a grid we want to create our window.
	**/
	
	if(creationMethod == undefined) {
		creationMethod = 'append';
	}
	
	if(animate == undefined) {
		animate = true;
	}
	
	var dialog = "<div type='" + reportLabel + "' name='" + reportName + "' id='" + reportID + "' href='" + url + "' class='dialog'>";
		dialog += "<div class='dialog-header'>";
            dialog += "<div class='dialog-label'>" + reportName + "</div>";
			dialog += "<?php echo UIDIalogButtons(); ?>";
		dialog += "</div>";
		dialog += "<div class='dialog-content'>" + content + "</div>";
	dialog += "</div>";
	
	if(addToGrid == undefined) {
		var printToGrid = getSmallestGrid();
	} else {
		var printToGrid = checkIfGridExists(addToGrid);
	}
	
	$("#content #report-grid").queue(function() { // We use .queue() because it waits for other dialogs to finish their work before we re-use variables.
		if(creationMethod == 'prepend') {
			$("#content #report-grid .grid:eq(" + printToGrid + ")").prepend(dialog);
		} else {
			$("#content #report-grid .grid:eq(" + printToGrid + ")").append(dialog);
		}
		$("#content #report-grid").dequeue();
	});
	
	attachReportEvents(reportID);
	if($("#report_area .report_icon[rel=" + reportID.split("--")[0] + "] img").attr('src') != undefined) {
		var iconsource = $("#report_area .report_icon[rel=" + reportID.split("--")[0] + "] img").attr('src');
	} else {
		var iconsource = "images/icons/32x32/unknown.png";
	}
	
	if(creationMethod == 'prepend') {
		$(".workbar .workicons .workbar_grid:eq(" + printToGrid + ")").prepend("<div class='icon' name='" + reportName + "' title='" + reportName + "' rel='" + reportID + "'><img src='" + iconsource + "'/></div>");
	} else {
		$(".workbar .workicons .workbar_grid:eq(" + printToGrid + ")").append("<div class='icon' name='" + reportName + "' title='" + reportName + "' rel='" + reportID + "'><img src='" + iconsource + "'/></div>");
	}
	
	if(animate == true) {
		/* Animate the icon of this report to the workbar. */
		$("#interface-overlay .interface_anims").html("<img class='anim' src='" + iconsource + "'/>");
		$("#interface-overlay .interface_anims .anim").css("top", $("#report_area .report_icon[rel=" + reportID.split("--")[0] + "]").offset().top);
		$("#interface-overlay .interface_anims .anim").css("left", $("#report_area .report_icon[rel=" + reportID.split("--")[0] + "]").offset().left);
		$("#interface-overlay").show();
		
		icon_position = {
			top: $(".workbar .workicons .workbar_grid:eq(" + printToGrid + ") .icon[rel='" + reportID + "']").offset().top,
			left: $(".workbar .workicons .workbar_grid:eq(" + printToGrid + ") .icon[rel='" + reportID + "']").offset().left
		};
		
		$(".workbar .workicons .workbar_grid:eq(" + printToGrid + ") .icon[rel='" + reportID + "']").css("display", "none");
		
		$("#interface-overlay .interface_anims .anim").animate({
				top: icon_position.top,
				left: icon_position.left
			}, 750, function() {
				$(".workbar .workicons .workbar_grid:eq(" + printToGrid + ") .icon[rel='" + reportID + "']").show();
				$(".workbar .workicons .workbar_grid:eq(" + printToGrid + ") .icon[rel='" + reportID + "']").effect("bounce");
				$("#interface-overlay .interface_anims .anim").remove();
				$("#interface-overlay").css("display", "none");
			}
		);
	}
	
	return reportID; // We'll return the ID of the dialog we just created, so we can call the dialog from other scopes.
}



function createNote (minimumDate, maximumDate) {
	minimumDate = strtotime(minimumDate + " 00:00:00");
	maximumDate = strtotime(maximumDate + " 23:59:59");
	for(var note_num in notes) {		
		var note_date = Math.round(strtotime(notes[note_num]['day']));
		if(note_date >= minimumDate && note_date <= maximumDate && $("#" + note_date + note_num).length == 0 && notes[note_num]['shown'] == 0) {
			var note = "<div id='" + note_date + note_num + "' class='dialog note'>";
				note += "<div class='dialog-header'>";
					note += "<div class='dialog-label'>Note: " + notes[note_num]['day'] + "</div>";
					note += "<?php echo UIDIalogButtons(); ?>";
				note += "</div>";
				note += "<div class='dialog-content'>";
					note += "<div style='margin: 5px;'>" + notes[note_num]['note'] + "</div>";
				note += "</div>";
			note += "</div>";
			
			$("#content #report-grid .grid:eq(0)").prepend(note);
			
			notes[note_num]['shown'] = 1;
		}
	}
}



function createIframeWindow (iframeURL, iframeTitle, reloadreportsettings) {
	if(iframeTitle == undefined) { iframeTitle = ""; }
	if(reloadreportsettings == undefined) { reloadreportsettings = true; }
	
	$("#iframe_window .iframe_content").html("<iframe src='" + iframeURL + "' frameborder=0></iframe>");
	$("#iframe_window").dialog({
		modal: true,
		draggable: false,
		resizable: false,
		width: 800,
		height: 600,
		title: iframeTitle,
		beforeClose: function() {
			if (reloadreportsettings == true && $("#report_area_extension").is(":visible") == true) {
				var reportlabel = $(".current_report label").attr("rel");
				var reportname = $(".current_report").attr("name");
				var reportID = $(".current_report").attr("rel");
				var reporturl = $(".current_report").attr("src");
				showOptions (reportname, reportlabel, reporturl, reportID);
			}
		}
	});
}



function plotGraph (graphcontainer, graphdata, actionmenu_type, graphOptions, legendOrientation, legendDisplay) {
	/**
		This function will create a window for reports with all needed attributes.
		
		Arguments:
		- graphcontainer
			The ID of the container of the graph we're about to create.
		- graphdata
			The data we use to plot our graph.
			
		Additional Info:
			Here'll be a list of built-in JQplot Events:
			jqplotClick
			jqplotDblClick
			jqplotMouseDown
			jqplotMouseUp
			jqplotMouseMove
			jqplotMouseEnter
			jqplotMouseLeave
			
			Add the event like this:
			$.jqplot.eventListenerHooks.push(['jqplotClick', FUNCTION);
			
			Please note that this event has to be initialized BEFORE the graph.
	**/
	
	plots; // This is a global variable, initialized at the top of this file.
	
	var seriedata = []; // This array will contain all data about the series/plots, such as lines, bars, etc.
	var graphtype = []; // For each dataset, we'll have an individual type.
	var graphlabel = []; // For each dataset, we'll have an individual label.
	var plotdata = []; // This will contain all the data we need to plot a graph.
	var new_xaxis_labels = []; // This array will contain the X axis labels to be used in the graph.
	graphdata[0].shift();
	var xaxis_labels = graphdata[0]; // This array will contain the X axis labels to be used in the graph, before they are converted correctly.
	var yaxis_labels = []; // This array will contain the Y axis labels to be used in the graph.
	
	if(legendOrientation == undefined) {
		legendOrientation = 'south_legend'; // can be either 'south_legend', 'east_legend' or 'west_legend'
	}
	if(legendDisplay == undefined) {
		legendDisplay = 'block'; // can be either 'block', 'inline' or 'none'
	}
	
	/* This barf is to create padding between the X axis (and allies) labels. */
	for(var i = 0; i < xaxis_labels.length; i++) {
		new_xaxis_labels.push(xaxis_labels[i]);
	}
	
	var barcount = 0;
	var linecount = 0;
	var maxNumbers = [];
	var graphtrend = {};
	
	for(i = 1; i < graphdata.length; i++) {
		graphlabel[i] = graphdata[i][0];
		
		if(graphlabel[i].split("|")[2] != undefined) {
			graphtrend[i] = true;
		} else {
			graphtrend[i] = false;
		}
		graphtype[i] = graphlabel[i].split("|")[1];
		graphlabel[i] = graphlabel[i].split("|")[0];
		graphdata[i].shift();
		
		for(var array_entry in graphdata[i]) {
			if(is_numeric(graphdata[i][array_entry])) {
				if(graphdata[i][array_entry] == " ") {
					graphdata[i][array_entry] = 0;
				} else {
					graphdata[i][array_entry] = parseFloat(graphdata[i][array_entry]);
				}
			}
		}
		
		/* We'll count the graphtype and prepare our datasets. */
		if(graphtype[i] == 'bar') {
			barcount++;
			seriedata.push({
				label: graphlabel[i], // This is the type of data (Visitors, for example).
				renderer: $.jqplot.BarRenderer,
				pointLabels: { show: true },
				trendline: { show: graphtrend[i] }
			});
		} else if(graphtype[i] == 'line') {
			linecount++;
			seriedata.push({
				label: graphlabel[i], // This is the type of data (Visitors, for example).
				renderer: $.jqplot.LineRenderer,
				trendline: { show: graphtrend[i] }
			});
		} else {
			seriedata.push({
				label: graphlabel[i], // This is the type of data (Visitors, for example).
			});
		}
		
		maxNumbers.push(maxInArray(graphdata[i]));
		plotdata.push(graphdata[i]);
	}
	
	// We don't want values like 32752.43 in our Y axis, so we'll calculate our own Y axis.
	yaxis_labels = [];
	for(var c = 0; c <= 5; c++) {
		var maxNum = maxInArray(maxNumbers);
		
		var num = (maxNum * (c / 4.9));
		if(maxNum < 10) {
			num = parseFloat(num.toFixed(2));
		}
		yaxis_labels.push(num);
	}
	
	for(var serie_id in seriedata) {
		if(graphOptions.series[serie_id] != undefined) {
			for(var serie_key in seriedata[serie_id]) {
				if(graphOptions.series[serie_id][serie_key] == undefined) {
					graphOptions.series[serie_id][serie_key] = seriedata[serie_id][serie_key];
				}
			}
		} else {
			graphOptions.series[serie_id] = seriedata[serie_id];
		}
	}
	
	if(graphOptions.axes.xaxis.ticks.length == 0) {
		graphOptions.axes.xaxis.ticks = new_xaxis_labels;
	}
	
	if(graphOptions.axes.yaxis.ticks) {
		graphOptions.axes.yaxis.ticks = yaxis_labels;
	}
	
	applyGraphTooltip(graphcontainer);
	
	if(barcount == 0 && linecount > 0) {
		var graphplotted = 'line';
		plots[graphcontainer] = graph.lineChart(graphcontainer, plotdata, graphOptions);
	} else if(linecount == 0 && barcount > 0) {
		var graphplotted = 'bar';
		
		plots[graphcontainer] = graph.barChart(graphcontainer, plotdata, graphOptions);
	} else if(barcount > 0 && linecount > 0) {
		var graphplotted = 'barline';
		
		plots[graphcontainer] = graph.barLineChart(graphcontainer, plotdata, graphOptions);
	} else {
		var graphplotted = 'area';
		 
		var yaxis_base = 0;
		for (var i = 0; i < maxNumbers.length; i++) {
			yaxis_base = yaxis_base + maxNumbers[i];
		}
		
		yaxis_labels = [];
		for(var c = 0; c <= 5; c++) {
			var num = (yaxis_base * (c / 4.9));
			if(yaxis_base < 10) {
				num = parseFloat(num.toFixed(2));
			}
			yaxis_labels.push(num);
		}
	
		if(graphOptions.axes.yaxis.ticks) {
			graphOptions.axes.yaxis.ticks = yaxis_labels;
		} 
		plots[graphcontainer] = graph.areaChart(graphcontainer, plotdata, graphOptions);
	}
	
	if($("#" + graphcontainer).find(".jqplot-axis.jqplot-xaxis").find(".jqplot-xaxis-tick").length > 20) {
		var i = 0;
		$("#" + graphcontainer).find(".jqplot-axis.jqplot-xaxis").find(".jqplot-xaxis-tick").each(function() {
			
			if((i % Math.ceil($("#" + graphcontainer).find(".jqplot-axis.jqplot-xaxis").find(".jqplot-xaxis-tick").length / 10)) != 0) {
				$(this).css('display', 'none');
			}
			
			i++;
		});
	}
	
	$("#" + graphcontainer).closest(".graph_area").append("<div class='graph_tooltip'></div>");
}



function showGraphTooltip (ev, seriesIndex, pointIndex, data, plot) {
	if(data != null) {
		var graphcontainer = plot.targetId.substr(1, plot.targetId.length);
		
		if(plot.plugins.funnelRenderer != null) {
			$("#" + graphcontainer).parent().find(".graph_tooltip").html($("#" + graphcontainer).parent().find(".graphlegend").find("li:eq(" + (data.data[0] - 1) + ")").find("label").html() + ": " + data.data[1]);
			
			$("#" + graphcontainer).parent().find(".graph_tooltip").css("top", (ev.pageY + 25));
			
			if(ev.pageX - ($("#" + graphcontainer).parent().find(".graph_tooltip").outerWidth() / 2) < $("#" + graphcontainer).parent().find(".graph_tooltip").outerWidth() / 2) {
				var tooltip_horizon = ev.pageX;
			} else if(ev.pageX - ($("#" + graphcontainer).parent().find(".graph_tooltip").outerWidth() / 2) > $("body").outerWidth() - $("#" + graphcontainer).parent().find(".graph_tooltip").outerWidth()) {
				var tooltip_horizon = ev.pageX - $("#" + graphcontainer).parent().find(".graph_tooltip").outerWidth();
			} else {
				var tooltip_horizon = ev.pageX - ($("#" + graphcontainer).parent().find(".graph_tooltip").outerWidth() / 2);
			}
			
			$("#" + graphcontainer).parent().find(".graph_tooltip").css("left", tooltip_horizon);
			$("#" + graphcontainer).parent().find(".graph_tooltip").show();
			$("#" + graphcontainer).parent().find(".graph_tooltip").css("display","block");
		} else {
			var xaxis_labels = plots[graphcontainer]['axes']['xaxis']['ticks'];
			
			var graphlabel = $("#" + graphcontainer).parent().find(".glegend > div:eq(" + data.seriesIndex + ") > label").html();
			
			var tooltipcontents = "";
			
			if(pointIndex.xaxis == undefined) {
				$("#" + graphcontainer).parent().find(".graph_tooltip").hide();
				$("#" + graphcontainer).parent().find(".graph_tooltip").css("display","none");
			} else {
				$("#" + graphcontainer).parent().find(".graph_tooltip").css("top", (ev.pageY + 25));
				
				if(ev.pageX - ($("#" + graphcontainer).parent().find(".graph_tooltip").outerWidth() / 2) < $("#" + graphcontainer).parent().find(".graph_tooltip").outerWidth() / 2) {
					var tooltip_horizon = ev.pageX;
				} else if(ev.pageX - ($("#" + graphcontainer).parent().find(".graph_tooltip").outerWidth() / 2) > $("body").outerWidth() - $("#" + graphcontainer).parent().find(".graph_tooltip").outerWidth()) {
					var tooltip_horizon = ev.pageX - $("#" + graphcontainer).parent().find(".graph_tooltip").outerWidth();
				} else {
					var tooltip_horizon = ev.pageX - ($("#" + graphcontainer).parent().find(".graph_tooltip").outerWidth() / 2);
				}
				
				$("#" + graphcontainer).parent().find(".graph_tooltip").css("left", tooltip_horizon);
				$("#" + graphcontainer).parent().find(".graph_tooltip").show();
				$("#" + graphcontainer).parent().find(".graph_tooltip").css("display","block");
			}
			
			if(xaxis_labels[(data.data[0] - 1)] != undefined && data.data[1] != undefined) {
				tooltipcontents = "<strong>" + xaxis_labels[(data.data[0] - 1)] + "</strong><br/>" + data.data[1] + " " + graphlabel;
			} else {
				var total_for_serie = 0;
				for(var c = 0; c < data.data.length; c++) {
					total_for_serie += data.data[c][1];
				}
				
				tooltipcontents = "Total for " + graphlabel + ": " + total_for_serie;
			}
			
			$("#" + graphcontainer).parent().find(".graphlegend li").css("font-weight", "normal");
			if(data.seriesIndex != undefined && data.seriesIndex != null) {
				$("#" + graphcontainer).parent().find(".graphlegend li:eq(" + data.pointIndex + ")").css("font-weight", "bold");
			} else {
				$("#" + graphcontainer).parent().find(".graphlegend li:eq(" + data.seriesIndex + ")").css("font-weight", "bold");
			}
			$("#" + graphcontainer).parent().find(".graph_tooltip").html(tooltipcontents);
		}
	} else {
		$("#" + graphcontainer).parent().find(".graph_tooltip").hide();
		$("#" + graphcontainer).parent().find(".graph_tooltip").css("display","none");
		$("#" + graphcontainer).parent().find(".graphlegend li").css("font-weight", "normal");
	}
}



function hideGraphTooltip (ev, gridpos, datapos, neighbor, plot) {
	var graphcontainer = plot.targetId.substr(1, plot.targetId.length);
	
	$("#" + graphcontainer).parent().find(".graph_tooltip").hide();
	$("#" + graphcontainer).parent().find(".graph_tooltip").css("display","none");
	$("#" + graphcontainer).parent().find(".graphlegend li").css("font-weight", "normal");
}



function applyGraphTooltip (graphcontainer) {
	$.jqplot.eventListenerHooks.push(['jqplotMouseMove', showGraphTooltip]);
	$.jqplot.eventListenerHooks.push(['jqplotMouseLeave', hideGraphTooltip]);
}



function createBalloon (content, balloonClass, balloonType) {
	/**
		This function will create a Balloon (feedback message).
		
		Arguments:
		- content
			The content we want to show in our Balloon.
		- balloonClass [optional]
			We can add an additional class to the balloon, to use other styles, among other things.
		- balloonType [optional]
			You can also use a type, which is either "message" or "alert".
	**/
	
	if(balloonType == undefined) { balloonType = "message"; }
	if(balloonClass != undefined) { balloonClass = " " + balloonClass; } else { balloonClass = ""; }
	
	var uiclass = balloonType == "alert" ? "ui-state-error" : "ui-state-highlight";
	var balloonIcon = balloonType == "alert" ? "ui-icon-alert" : "ui-icon-info";
	
	if($(".balloon").length == 0) { // There can be only one Balloon opened, so, if a Balloon is opened, we'll rewrite the contents.
		$("#interface #content").append("<div class='balloon" + balloonClass + " " + uiclass + "'>" + "<span style='float: left; margin-right: 0.3em;' class='ui-icon " + balloonIcon + "'></span>" + content + "</div>");
	} else {
		$(".balloon").attr("class","balloon" + balloonClass + " " + uiclass);
		$(".balloon").html(content);
	}
	
	var balloonFadeTimer = setTimeout(function() {
		$(".balloon").remove();
	}, 5000);
}


/** <-------------------------------------------------------------------------------- Content --------------------------------------------------------------------------------> **/

function windowHeader (from, to) {
	/**
		This function returns a window header.
		
		Arguments:
		- from
			The 'from' date.
		- to	
			The 'to' date.
	**/
	
	// var d=new Date();
	// var month=new Array(12);
	// month[1]="Jan";
	// month[2]="Feb";
	// month[3]="Mar";
	// month[4]="Apr";
	// month[5]="May";
	// month[6]="Jun";
	// month[7]="Jul";
	// month[8]="Aug";
	// month[9]="Sep";
	// month[10]="Oct";
	// month[11]="Nov";
	// month[12]="Dec";
	
	if(is_int(from) == true) {
		from = strtotime("d m Y", from);
	}
	
	if(is_int(to) == true) {
		to = strtotime("d m Y", to);
	}
	
	// var nicefrom = unescape(from).split(' ');
	// var niceto = unescape(to).split(' ');
	// if (nicefrom[0].substr(0,1) == 0) {
		// nicefrom[0] = nicefrom[0].substr(1);
	// }
	// if (niceto[0].substr(0,1) == 0) {
		// niceto[0] = niceto[0].substr(1);
	// }
	
	if((from == undefined && to == undefined)) {
		return "<div class='report-header'>&nbsp;</div>";
	} else {
		// from = frontendDate(from);
		// to = frontendDate(to);
	}
	
	// nicefrom = nicefrom[1] + ' ' + month[nicefrom[1]] + ' ' + nicefrom[2];
	// niceto = niceto[1] + ' ' + month[niceto[1]] + ' ' + niceto[2];	
	
    // return "<div class='report-header'>" + from + " - " + to + "</div>";
    return "<div class='report-header'>&nbsp;</div>";
}



function defaultReportLoadingScreen (reportName, daterangeFrom, daterangeTo) {
	/**
		This function returns a default loading screen.
		
		Arguments:
		- reportName
			The report name.
		- daterangeFrom
			The 'from' date.
		- daterangeTo	
			The 'to' date.
	**/
	
	return windowHeader(daterangeFrom, daterangeTo) + Loading(reportName);
}



function Loading (itemBeingLoaded, loadingImage) {
	/**
		This function will return the default Loading Screen content.
		
		Arguments:
		- itemBeingLoaded [optional]
			The name of what's being loaded.
		- loadingImage [optional]
			We can use another loading image from the images/ map, if we want to.
	**/
	
	if(itemBeingLoaded == undefined) {
		var itemBeingLoaded = "";
	}
	if(loadingImage == undefined) {
		var loadingImage = "loader.gif";
	}
	return "<div class='loading_image'><img src='images/" + loadingImage + "'/><br/><?php echo _LOADING;?> " + itemBeingLoaded + "...</div>";
}



function Error (itemBeingLoaded) {
	/**
		This function will return the default Error Screen content.
		
		Arguments:
		- itemBeingLoaded [optional]
			The name of what's being loaded.
	**/
	
	if(itemBeingLoaded == undefined) {
		var itemBeingLoaded = "";
	}
	return "<div class='loading_image'><img src='images/error.png'/><br/><?php echo _AN_ERROR_OCCURED_WHILE_LOADING;?> " + itemBeingLoaded + "!</div>";
}




function changeUpdateText(statusText) {
	$(".progress_status").html("(" + statusText + ")");
}

function updateProgress(progress, setBar) {
	if(setBar == undefined) { setBar = false; }
	
	if(setBar == true) {
		$(".updating_progress .ui-progressbar-value").css("width", progress.percentage + "%");
	}
	
	$(".updating_progress .ui-progressbar-value").stop();
	
	$(".updating_progress .ui-progressbar-value").animate({
		width: '100%'
	}, (progress.estimate_time * 1000), function() {
		
	});
	
	var estimated_perc = Math.round(($(".updating_progress .ui-progressbar-value").width() / $(".updating_progress").width()) * 100);
	
	$(".updating_progress").attr("title", "Estimated progress for " + progress.file + ": " + estimated_perc + "%");
	$(".progress_percentage").html(estimated_perc + "%");
	$(".updateProgressText").attr("title", "Progress for " + progress.file + ": " + estimated_perc + "% (Total average progress: " + progress.total_percentage + "%)");
	$(".progress_status").html("(" + progress.action + ")");
	
	if(progress.action.indexOf("Update finished") > -1) {
		$(".updateProgressText > img").remove();
		$(".updateProgressText").html(progress.action);
		$(".updateProgressText").removeAttr('title');
		$(".updating_progress").remove();
		clearInterval(updateCheck);
		fixAfterWindowResize();
	}
}

function updateProgressBar(update_result, setBar) {
	if(setBar == undefined) { setBar = false; }
	var update_log = {};
	var lines = update_result.split(";");
	var c = 1;
	var current_perc = 0;
	var estimate_time = 0;
	var updating_file = '';
	
	for(var line in lines) {
		if(lines[line].substr(0, 3).indexOf('#') >= 0) {
			if(lines[line].split("|")[2] != null && lines[line].split("|")[2] != undefined) {
				estimate_time = lines[line].split("|")[2];
				current_perc = 0;
			}
			update_log[c] = {};
			update_log[c]['perc'] = lines[line].split("|")[0].replace("#","");
			current_perc += parseFloat(update_log[c]['perc']);
			update_log[c]['status'] = lines[line].split("|")[1];
			if(update_log[c]['status'].indexOf("<strong class='updating_file'>") > -1) {
				var filename_indicator_start = "<strong class='updating_file'>";
				var filename_indicator_end = "</strong>";
				updating_file = update_log[c]['status'].substr(
					update_log[c]['status'].indexOf(filename_indicator_start) + filename_indicator_start.length,
					(update_log[c]['status'].indexOf(filename_indicator_end) - (update_log[c]['status'].indexOf(filename_indicator_start)) - filename_indicator_start.length)
				);
			}
			c++;
		}
	}
	var current_status = update_log[(c - 1)]['status'];
	
	overall_update_perc = Math.round((current_perc + (<?php echo $update_data[0]; ?> * 100)) / <?php echo $update_data[1]; ?>);
	// overall_perc = Math.round(current_perc / (<?php echo $update_data[1]; ?> - (<?php echo $update_data[0]; ?> - 1)));

	if(setBar == true) {
		$(".updating_progress .ui-progressbar-value").css("width", current_perc + "%");
	}
	
	$(".updating_progress .ui-progressbar-value").stop();
	
	$(".updating_progress .ui-progressbar-value").animate({
		width: '100%'
	}, (estimate_time * 1000), function() {
		
	});
	
	var estimated_perc = Math.round(($(".updating_progress .ui-progressbar-value").width() / $(".updating_progress").width()) * 100);
	
	$(".updating_progress").attr("title", "Estimated progress for " + updating_file + ": " + estimated_perc + "%");
	$(".progress_percentage").html(estimated_perc + "%");
	$(".updateProgressText").attr("title", "Progress for " + updating_file + ": " + estimated_perc + "% (Total average progress: " + overall_update_perc + "%)");
	$(".progress_status").html("(" + current_status + ")");
	
	if(current_status.indexOf("Update finished") > -1) {
		$(".updateProgressText > img").remove();
		$(".updateProgressText").html(current_status);
		$(".updateProgressText").removeAttr('title');
		$(".updating_progress").remove();
		clearInterval(updateCheck);
		fixAfterWindowResize();
	}
	
	return current_perc;
}





/** <-------------------------------------------------------------------------------- Overall --------------------------------------------------------------------------------> **/





function is_object (mixed_var) { if (mixed_var instanceof Array) { return false; } else { return (mixed_var !== null) && (typeof(mixed_var) == 'object'); } }



function is_array (input) { return typeof(input) == 'object' && (input instanceof Array); }



function replaceAll (rottenApple, cleanApple, basketOfApples) { basketOfApples = basketOfApples.split(rottenApple).join(cleanApple); return basketOfApples; }



function maxInArray(arr) { var max = 0; for(c = 0; c < arr.length; c++) { if(parseFloat(arr[c]) >= max) { max = parseFloat(arr[c]); } } return max; }



function in_array (needle, haystack) {
	for(var straw in haystack) {
		if(haystack[straw] == needle) {
			return true;
		}
	}
	
	return false;
}



function is_numeric (mixed_var) { return (typeof(mixed_var) === 'number' || typeof(mixed_var) === 'string') && mixed_var !== '' && !isNaN(mixed_var); }



function number_format (number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');    }
    return s.join(dec);
}



function php_urlencode (str) {
	str = escape(str);
	return str.replace(
		/[*+\/@]|%20/g,
		function (s) {
			switch (s) {
				case "*": s = "%2A"; break;
				case "+": s = "%2B"; break;
				case "/": s = "%2F"; break;
				case "@": s = "%40"; break;
				case "%20": s = "+"; break;
			}
			return s;
		}
	);
}



function strtotime (str, now) {
    // Convert string representation of date and time to a timestamp  
    
    // version: 1107.2516
    // discuss at: http://phpjs.org/functions/strtotime    // +   original by: Caio Ariede (http://caioariede.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: David
    // +   improved by: Caio Ariede (http://caioariede.com)
    // +   improved by: Brett Zamir (http://brett-zamir.me)    // +   bugfixed by: Wagner B. Soares
    // +   bugfixed by: Artur Tchernychev
    // %        note 1: Examples all have a fixed timestamp to prevent tests to fail because of variable time(zones)
    // *     example 1: strtotime('+1 day', 1129633200);
    // *     returns 1: 1129719600    // *     example 2: strtotime('+1 week 2 days 4 hours 2 seconds', 1129633200);
    // *     returns 2: 1130425202
    // *     example 3: strtotime('last month', 1129633200);
    // *     returns 3: 1127041200
    // *     example 4: strtotime('2009-05-04 08:30:00');    // *     returns 4: 1241418600
    // var i, match, s, strTmp = '', parse = '';
 
    strTmp = str;
	strTmp = strTmp.replace(/\s{2,}|^\s|\s$/g, ' '); // unecessary spaces
    strTmp = strTmp.replace(/[\t\r\n]/g, ''); // unecessary chars
    if (strTmp == 'now') {
        return (new Date()).getTime() / 1000; // Return seconds, not milli-seconds
    } else if (!isNaN(parse = Date.parse(strTmp))) {
		return (parse / 1000);
    } else if (now) {
        now = new Date(now * 1000); // Accept PHP-style seconds
    } else {
        now = new Date();
	}
 
    strTmp = strTmp.toLowerCase();
 
    var __is = {
		day: {
            'sun': 0,
            'mon': 1,
            'tue': 2,
            'wed': 3,
			'thu': 4,
            'fri': 5,
            'sat': 6
        },
        mon: {
			'jan': 0,
            'feb': 1,
            'mar': 2,
            'apr': 3,
            'may': 4,
			'jun': 5,
            'jul': 6,
            'aug': 7,
            'sep': 8,
            'oct': 9,
			'nov': 10,
            'dec': 11
        }
    };
	var process = function (m) {
        var ago = (m[2] && m[2] == 'ago');
        var num = (num = m[0] == 'last' ? -1 : 1) * (ago ? -1 : 1);
 
        switch (m[0]) {
			case 'next':
				switch (m[1].substring(0, 3)) {
					case 'yea':
						now.setFullYear(now.getFullYear() + num);
						break;
					case 'mon':
						now.setMonth(now.getMonth() + num);
						break;
					case 'wee':                now.setDate(now.getDate() + (num * 7));
						break;
					case 'day':
						now.setDate(now.getDate() + num);
						break;
					case 'hou':
						now.setHours(now.getHours() + num);
						break;
					case 'min':
						now.setMinutes(now.getMinutes() + num);
						break;
					case 'sec':
						now.setSeconds(now.getSeconds() + num);
						break;
					default:
						var day;
						if (typeof(day = __is.day[m[1].substring(0, 3)]) != 'undefined') {
							var diff = day - now.getDay();
							if (diff == 0) {
								diff = 7 * num;
							} else if (diff > 0) {
								if (m[0] == 'last') {
									diff -= 7;
								}
							} else {
								if (m[0] == 'next') {
									diff += 7;
								}
							}
							now.setDate(now.getDate() + diff);
						}
				}
				break;
			
			default:
				if (/\d+/.test(m[0])) {
					num *= parseInt(m[0], 10);
	 
					switch (m[1].substring(0, 3)) {
						case 'yea':
							now.setFullYear(now.getFullYear() + num);
							break;
						case 'mon':
							now.setMonth(now.getMonth() + num);
							break;
						case 'wee':
							now.setDate(now.getDate() + (num * 7));
							break;
						case 'day':
							now.setDate(now.getDate() + num);
							break;
						case 'hou':
							now.setHours(now.getHours() + num);
							break;
						case 'min':
							now.setMinutes(now.getMinutes() + num);
							break;
						case 'sec':
							now.setSeconds(now.getSeconds() + num);
							break;
					}
				} else {
					return false;
				}
				break;
		}
        return true;
    };
 
    match = strTmp.match(/^(\d{2,4}-\d{2}-\d{2})(?:\s(\d{1,2}:\d{2}(:\d{2})?)?(?:\.(\d+))?)?$/);
	if (match != null) {
        if (!match[2]) {
            match[2] = '00:00:00';
        } else if (!match[3]) {
            match[2] += ':00';
		}
 
        s = match[1].split(/-/g);
 
        for (i in __is.mon) {
			if (__is.mon[i] == s[1] - 1) {
                s[1] = i;
            }
        }
        s[0] = parseInt(s[0], 10); 
        s[0] = (s[0] >= 0 && s[0] <= 69) ? '20' + (s[0] < 10 ? '0' + s[0] : s[0] + '') : (s[0] >= 70 && s[0] <= 99) ? '19' + s[0] : s[0] + '';
        return parseInt(this.strtotime(s[2] + ' ' + s[1] + ' ' + s[0] + ' ' + match[2]) + (match[4] ? match[4] / 1000 : ''), 10);
    }
	var regex = '([+-]?\\d+\\s' + '(years?|months?|weeks?|days?|hours?|min|minutes?|sec|seconds?' + '|sun\\.?|sunday|mon\\.?|monday|tue\\.?|tuesday|wed\\.?|wednesday' + '|thu\\.?|thursday|fri\\.?|friday|sat\\.?|saturday)' + '|(last|next)\\s' + '(years?|months?|weeks?|days?|hours?|min|minutes?|sec|seconds?' + '|sun\\.?|sunday|mon\\.?|monday|tue\\.?|tuesday|wed\\.?|wednesday' + '|thu\\.?|thursday|fri\\.?|friday|sat\\.?|saturday))' + '(\\sago)?';
 
    match = strTmp.match(new RegExp(regex, 'gi')); // Brett: seems should be case insensitive per docs, so added 'i'
    if (match == null) {
        return false;
	}
 
    for (i = 0; i < match.length; i++) {
        if (!process(match[i].split(' '))) {
            return false;
		}
    }
 
    return (now.getTime() / 1000);
}

function plusMinusForm (container_id, field_a, field_b, field_c, andor_selector_available) {
	if(andor_selector_available == undefined) { andor_selector_available = true; }
	
	$("#" + container_id + " .conditions_row:first .plus_btn").removeAttr("disabled");
	
	if($("#" + container_id + " .conditions_row").length == 1) {
		$("#" + container_id + " .min_btn").attr("disabled", "disabled");
	} else {
		if(andor_selector_available != false && $(".andor_selector").length < 1) {
			$(".condition_descriptor").before(
				"<div class='andor_selector'>" +
					"<span style='float: left; margin-right: 20px;'>Mode: </span>" +
					"<label style='float: left;'>AND</label><input type='radio' style='width: 25px; float: left;' name='andor' value='AND' checked />" +
					"<label style='float: left;'>OR</label><input type='radio' style='width: 25px; float: left;' name='andor' value='OR' />" +
				"</div>" + 
				"<div class='clear'></div>"
			);
		}
	}
	
	$("#" + container_id).find(".plus_btn").live("click", function() {
		$(this).closest(".conditions_row").after(
			"<div class='conditions_row'>" + $(this).closest(".conditions_row").html() + "</div>"
		);
		
		var c = 1;
		$("#" + container_id + " .conditions_row").each(function() {			
			$(this).find("*[src='" + field_a + "']").attr('rel', c);
			$(this).find("*[src='" + field_a + "']").attr('id', field_a + c);
			$(this).find("*[src='" + field_a + "']").attr('name', field_a + c);
			
			$(this).find("*[src='" + field_b + "']").attr('rel', c);
			$(this).find("*[src='" + field_b + "']").attr('id', field_b + c);
			$(this).find("*[src='" + field_b + "']").attr('name', field_b + c);
			
			$(this).find("*[src='" + field_c + "']").attr('rel', c);
			$(this).find("*[src='" + field_c + "']").attr('id', field_c + c);
			$(this).find("*[src='" + field_c + "']").attr('name', field_c + c);
			c++;
		});
		
		if($("#" + container_id + " .conditions_row").length > 1) {
			$("#" + container_id + " .conditions_row").find(".min_btn").removeAttr("disabled");
		}
		
		if(andor_selector_available != false && $(".andor_selector").length < 1) {
			$(".condition_descriptor").before(
				"<div class='andor_selector'>" +
					"<span style='float: left; margin-right: 20px;'>Mode: </span>" +
					"<label style='float: left;'>AND</label><input type='radio' style='width: 25px; float: left;' name='andor' value='AND' checked />" +
					"<label style='float: left;'>OR</label><input type='radio' style='width: 25px; float: left;' name='andor' value='OR' />" +
				"</div>" + 
				"<div class='clear'></div>"
			);
		}
	});

	$(".min_btn").live("click", function() {
		$(this).closest(".conditions_row").remove();
		
		var c = 1;
		$("#" + container_id + " .conditions_row").each(function() {			
			$(this).find("*[src='" + field_a + "']").attr('rel', c);
			$(this).find("*[src='" + field_a + "']").attr('id', field_a + c);
			$(this).find("*[src='" + field_a + "']").attr('name', field_a + c);
			
			$(this).find("*[src='" + field_b + "']").attr('rel', c);
			$(this).find("*[src='" + field_b + "']").attr('id', field_b + c);
			$(this).find("*[src='" + field_b + "']").attr('name', field_b + c);
			
			$(this).find("*[src='" + field_c + "']").attr('rel', c);
			$(this).find("*[src='" + field_c + "']").attr('id', field_c + c);
			$(this).find("*[src='" + field_c + "']").attr('name', field_c + c);
			
			c++;
		});
		
		if($(".conditions_row").length == 1) {
			$(".plus_btn").removeAttr("disabled");
			$(".min_btn").attr("disabled", "disabled");
			
			$(".andor_selector").remove();
		}
	});
}
function popupActionMenu(evt, str, pagetype, additional_parameters) {
	// Is the menu currently showing?  If so, hide it.
	if (contextMenuObj) {
		hide_popup_menu();
	}
	
	// clickTarget is the object clicked.  There might be things about it
	// that are interesting to discover - like a certain url (if it's an <A> tag)
	// or an ID or NAME that gives us information about what to display in the menu 
	var clickTarget;
	if (window.event && window.event.srcElement) {
		clickTarget = window.event.srcElement;
	} else if (evt && evt.target) {
		clickTarget = evt.target;
	}
	
	// Hook up to mousedown event for the whole browser.  This lets us close the menu if 
	// clicking outside of it.
	addEvent(document, 'mousedown', click_with_menu_enabled, false);
	
	// Create the top level menu
	contextMenuObj = document.createElement('span');
	contextMenuObj.id = "popupmainmenu";
	/*contextMenuObj.style.position = "absolute";*/
	if (pagetype=="forminput") {
		contextMenuObj.className = "actionmenu forminputmenu ui-corner-all";
	} else {
		contextMenuObj.className = "actionmenu ui-corner-all";
	}
	contextMenuObj.display = "block";
	contextMenuObj.overflow = "visible";
	/*contextMenuObj.innerHTML = "<font color=gray>Accessing...</font></span>"; */
	/*contextMenuObj.style.position = "absolute";*/
	
	// Add a <br> to the page first, to make sure the menu shows up on the *next* line.
	clickTarget.parentNode.appendChild(document.createElement('br'));
	// Add the menu itself to the page.
	clickTarget.parentNode.appendChild(contextMenuObj);
	
	// Create the ajax request object 
	contextMenuHTTPRequest=GetXmlHttpObject()
	if (contextMenuHTTPRequest==null) {
		alert ("Browser does not support HTTP Request")
		return;
	} 
	
	// Build the url query to the server (this, could, build the menu itself too...)
	var ajax_url = actionmenu_url;
	if (pagetype=="page") {
		ajax_url += "?url="+escape(str); 
	} else if (pagetype=="keyword") {
		ajax_url += "?keyword="+escape(str); 
	} else if (pagetype=="referrer") {
		ajax_url += "?referrer="+escape(str); 
	} else if (pagetype=="statuscode") {
		ajax_url += "?statuscode="+escape(str); 
	} else if (pagetype=="forminput") {
		ajax_url += "?forminput="+escape(str); 
	} else if (pagetype=="ipnumber") {
		ajax_url += "?ipnumber="+escape(str); 
	} else if (pagetype=="kpi") {
		ajax_url += "?kpi="+escape(str); 
	}
	ajax_url += "&conf="+escape(conf_name);
	ajax_url += "&from="+escape(from_date);
	ajax_url += "&to="+escape(to_date);
	
	if(additional_parameters != undefined) {
		ajax_url += additional_parameters;
	}
	
	// Hook up the ajax request object
	contextMenuHTTPRequest.onreadystatechange=populatePopupMenu
	contextMenuHTTPRequest.open("GET",ajax_url,true)
	contextMenuHTTPRequest.send(null)
	
	// Don't do any of the upstream "addEvent" calls - just the one we're on.  This means
	// if parent containers (spans, divs, etc) have logic, then we don't call it.
	if (evt.stopPropagation && evt.preventDefault) {
		evt.stopPropagation();
		evt.preventDefault();
	}
	if (window.event) {
		window.event.cancelBubble = true;
		window.event.returnValue = false;
	}
	return false;
}

function getFeaturedReports(result) {
	if(result != false && result != undefined) {
		var c = 0;
		for(var rep in result) {
			var printicon = true;
			for(var rep_id in class_reports) {
				if(class_reports[rep_id] == result[rep]['sku']) {
					printicon = false;
					break;
				}
			}
			
			if(printicon == true) {
				$(".featured_icons").append("<li class='reportstore_icon' rel='" + result[rep]['product_id'] + "' name='" + result[rep]['name'] + "'><a style='display: block; width: 100%; height: 100%; text-decoration: none;' href='<?php echo LOGAHOLIC_REPORT_STORE_LOCATION; ?>index.php?route=product/product&product_id=" + result[rep]['product_id'] + "&tracking=<?php echo $template->AffiliateID(); ?>&return_url=<?php echo $template->ReturnURL(); ?>'><img style='width: 32px;' src='<?php echo LOGAHOLIC_REPORT_STORE_LOCATION; ?>image/" + result[rep]['image'] + "' /><span>" + result[rep]['name'] + "</span></a></li>");
				
				c++;
			}
			
			if(c >= 5) {
				break;
			}
		}
		
		if(c == 0) {
			$(".featured_icons").remove();
			$("h2.store_featured").remove();
		} else {
			$(".featured_icons").show();
			$("h2.store_featured").show();
		}
	}
}



function is_empty (mixed_var) {
	var key;
	if (mixed_var === "" || mixed_var === 0 || mixed_var === "0" || mixed_var === null || mixed_var === false || typeof mixed_var === 'undefined') {
		return true;
	}

	if (typeof mixed_var == 'object') {
		for (key in mixed_var) {
			return false;
		}
		return true;
	} 
	return false;
}

function is_int(input){
	return typeof(input) == 'number' && parseInt(input) == input;
}

// this adds a custom data type to datatable that we use for sorting
$.fn.dataTableExt.aTypes.unshift(
	function ( sData )
	{
		if ( sData.indexOf('sort=') != -1 )
		{
			return 'sort-numeric';
			
		}
		return null;
	}
);

// this defines the sorting function we use with our custom data type
jQuery.fn.dataTableExt.oSort['sort-numeric-asc']  = function(x,y) {
	var x = x.match(/sort="(.*?)"/)[1];
	var y = y.match(/sort="(.*?)"/)[1];
	x = parseFloat( x );
	y = parseFloat( y );
	return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['sort-numeric-desc'] = function(x,y) {
	var x = x.match(/sort="(.*?)"/)[1];
	var y = y.match(/sort="(.*?)"/)[1];
	x = parseFloat( x );
	y = parseFloat( y );
	return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};