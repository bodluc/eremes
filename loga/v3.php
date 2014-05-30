<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include_once("common.inc.php");

if(empty($conf) && empty($profile)) {
	# If no profile is set, redirect to the profiles page.
	header("Location: profiles.php");
}

if($new_ui == false) {
	# If no new_ui is set, redirect to the old index page.
	header("Location: index.php?conf={$conf}&new_ui=0");
	exit;
}

# This function returns an array containing data needed to build dashboards
function loadDashboards() {
	global $db, $profile;
	
	# Fetch the dashboard data from the database.
	$sql = "SELECT * FROM `".TBL_GLOBAL_SETTINGS."` WHERE `Name` LIKE '{$profile->profilename}.dashboards.%' AND `Profile` = '{$profile->profilename}'";

	$result = $db->Execute($sql);

	$dashboards = array();

	$c = 0;
	while($dashboarddata = $result->FetchRow()) {
		foreach($dashboarddata as $k=>$v) { if(is_numeric($k)) { unset($dashboarddata[$k]); }} // This cleans the returned data (It deletes mysql generated data).
		
		# Give the first dashboard icon the CSS/JS class first.
		if($c == 0) { $first_class = "first"; } else { $first_class = ""; }
		
		$dashboard_data_array = json_decode(stripslashes($dashboarddata['Value'])); # JSON decode the fetched data, so we can use it in PHP.
		if(isset($dashboard_data_array->icon)) { $iconval = $dashboard_data_array->icon; } else { $iconval = "01"; }
		if(isset($dashboard_data_array->daterange_lock)) { $daterange_lock = $dashboard_data_array->daterange_lock; } else { $daterange_lock = 0; }
		
		# Create the html of the dashboard icon
		$dashboardicon = "<li daterange_lock=\"{$daterange_lock}\" startup=\"{$dashboard_data_array->startup}\" src=\"dashboard\" title=\"{$dashboard_data_array->description}\" rel=\"".md5($dashboard_data_array->name)."\" name=\"{$dashboard_data_array->name}\" class=\"dashboard report_icon {$first_class}\">".PHP_EOL;
		$dashboardicon .= "<img src='images/icons/dashboards/{$iconval}.png' />".PHP_EOL;
		$dashboardicon .= "<span>{$dashboard_data_array->name}</span>".PHP_EOL;
		$dashboardicon .= "</li>".PHP_EOL;
		
		# Add the dashboard data and icons to the dashboard array
		$dashboards[md5($dashboard_data_array->name)]['name'] = $dashboard_data_array->name;
		$dashboards[md5($dashboard_data_array->name)]['value'] = $dashboarddata['Value'];
		$dashboards[md5($dashboard_data_array->name)]['icon'] = $dashboardicon;
		$c++;
	}
	
	return $dashboards;
}

# This function returns an HTML unordered list of all the dashboards.
# It gets called in v3.php
function DashboardList($dashboards = array()) {
	$report_area = "";
	$report_area .= "<h2 class='dashboard_list report-category'>Dashboards<img src='images/arrow_down_darkgrey_square.png' class='report-category-arrow'/></h2>".PHP_EOL;
	$report_area .= "<ul>".PHP_EOL;
		if(!empty($dashboards)) {
			foreach($dashboards as $dash) {
				$report_area .= $dash['icon'];
			}
		} else {
			$report_area .= "<div style='display: block; margin-left: 20px; color: #B5B5B5; line-height: 37px;'>"._THERE_ARE_NO_DASHBOARDS."</div>";
		}
	$report_area .= "</ul>".PHP_EOL;
	
	return $report_area;
}

# Returns the unordered list that functions as a container for still purchaseable reports/bundles.
# Gets filled by script inject in jq-functions.php
function StoreList() {
	$report_area = "";
	
	$report_area .= "<h2 class='dashboard_list store_list report-category'>"._GET_MORE_REPORTS_IN_THE_LOGAHOLIC_REPORT_STORE."...<img src='images/arrow_down_darkgrey_square.png' class='report-category-arrow'/></h2>".PHP_EOL;
	$report_area .= "<ul>".PHP_EOL;
	$report_area .= "</ul>".PHP_EOL;
	
	return $report_area;
}

# Return the HTML of the whole Report Area
# The given reports variable is an array, sorted by category
function ReportArea($reports) {
	global $profile;
    $prev_cat = "";
    $report_area="";
	
	# For each report the user has
    foreach ($reports as $report_label => $report) {
		# Check whether we want to hide this icon (if the category was set to hidden mode, before)
		$hidden_style = "";
        if(isset($_COOKIE[$report['Category']])){
			$cookie = $_COOKIE[$report['Category']];
			if($cookie == 'true') {
				$hidden_style = "display:none;";
			} else {
				$hidden_style = "display:block;";
			}
		
		}
		
		# If this report isn't supposed to show up in the report area; skip it.
		if (isset($report['hidden']) && $report['hidden'] == true) {
            continue;    
        }
		
		# If we have a new category
        if ($report['Category'] != $prev_cat) {
			# close the previously opened UL element.
            if ($prev_cat != "") {
                $report_area .= "</ul>";
            }
			
			# Set the right class (whether the category is in hidden mode or not)
			$c = 0;
			$category_extra_text = "";
			if($hidden_style == "display:none;") {
				$category_extra_class = " hidden";
				$arrow_img = "arrow_right_darkgrey_square.png";
			} else {
				$category_extra_class = "";
				$arrow_img = "arrow_down_darkgrey_square.png";
			}
			
			# Open the new category
			$report_area .= "<h2 class='report-category{$category_extra_class}' rel='{$report['Category']}'>"."<img src='images/{$arrow_img}' class='report-category-arrow'/>".constant($report['Category']).$category_extra_text."<span title='"._OPEN_ALL_REPORTS_IN_THIS_CATEGORY_AS_YOUR_WORKSPACE."' class='open_category'>"._OPEN_REPORTS."</span></h2>".PHP_EOL;
			$report_area .= "<ul>".PHP_EOL;
			
			# Set first_class, so the first icon in the category has the first class.
			$first_class = "first";
        }
		
		# Prevent undefined variables, and such
        if(empty($report['icon'])) { $report['icon'] = "images/icons/32x32/unknown.png"; } # if there is no icon image set, use a preset "unknown" image.
        if(empty($report['Description'])) { $report['Description'] = ""; }
        if(empty($report['url'])) { $report['url'] = "reports.php"; }
		
		# Add a report icon to the HTML
        $report_area .= "<li href=\"{$report['url']}\" id=\"{$report['Category']}{$c}\" style='{$hidden_style}' rel=\"{$report['ClassName']}\" type=\"{$report_label}\" name=\"".constant($report_label)."\" class=\"report_icon {$first_class}\">".PHP_EOL;
			$report_area .= "<img src='{$report['icon']}' />".PHP_EOL;
			$report_area .= "<span>".constant($report_label)."</span>".PHP_EOL;
        $report_area .= "</li>".PHP_EOL;
		
		$first_class = "";
        $prev_cat = $report['Category'];
		$c++;
    }
    $report_area .= "</ul>".PHP_EOL;
	
    return $report_area;
}

$dashboards = loadDashboards();

/*
* -------------------------------------------------------------------------------------------------------
* OUTPUT STARTS HERE
* -------------------------------------------------------------------------------------------------------
*/

ob_start(); ?>
<script type="text/javascript">
	<?php # This function exists in jq-functions.php and does a script inject to fetch all not yet purchased reports ?>
	updateUnpurchased();
	
	var conf_name = "<?php echo $conf;?>";
	var from_date;
	var to_date;
	<?php
	# If v3.php is being called with the clean=1 parameter, we want to open Logaholic without loading a dashboard on startup.
	if(!empty($_REQUEST['clean'])) {
		echo "var cleanStartup = true;";
	} else {
		echo "var cleanStartup = false;";
	}
	
	if (!empty($_REQUEST['labels'])) {
		echo "var cleanStartup = true;";
		echo "$(document).ready(function() {";
		echo "var optionsfromurl = urlToOptions('reports.php?{$_SERVER['QUERY_STRING']}');\n";
		echo "openReport('reports.php', '".constant($_REQUEST['labels'])."', '{$_REQUEST['labels']}', optionsfromurl);";
		echo "hideReportArea();\n";
		echo "});\n ";
	}
	?>
	
	<?php 
	# If there's a dashboard name given in the URL, we want to open that specific dashboard.
	if(isset($_REQUEST['dashboard'])) { ?>
	var startup_dashboard = "<?php echo $_REQUEST['dashboard']; ?>";
	<?php } else { ?>
	var startup_dashboard = undefined;
	<?php }
	
	# Set the data_of_all_dashboards variable; this is a javascript object variable that contains the data of all dashboards for this profile.
	$tmp_db = "var data_of_all_dashboards = {";
	$c = 0;
	foreach($dashboards as $dashboard) {
		$cc = 0;
		
		if($c > 0) { $tmp_db .= ","; }
		
		# Use the dashboard name as a key.
		$tmp_db .= "'{$dashboard['name']}' : {";
		$dashboard_content = json_decode(stripslashes($dashboard['value']), true);
		
		if(!empty($dashboard_content['daterange_lock'])) {
			$tmp_db .= "daterange_lock: 1,";
		} else {
			$tmp_db .= "daterange_lock: 0,";
		}
		
		$tmp_db .= "reports : {";
		
		# Set which reports are in the current dashboard
		foreach($dashboard_content['reports'] as $dashboard_grid) { # For each grid
			$ccc = 0;
			
			if($cc > 0) { $tmp_db .= ","; }
			
			$tmp_db .= "{$cc} : {";
			foreach($dashboard_grid as $dashboard_report) {
				if(array_key_exists($dashboard_report['label'], $reports) == false) { # For each report in this grid
					continue;
				}
				
				if($ccc > 0) { $tmp_db .= ","; }
				
				# Use the report label as a key
				$tmp_db .= "{$dashboard_report['label']} : {";
					$tmp_db .= "name: '{$dashboard_report['name']}',";
					$tmp_db .= "label: '{$dashboard_report['label']}',";
					$tmp_db .= "url: '{$dashboard_report['url']}'";
				$tmp_db .= "}";
				
				$ccc++;
			}
			$tmp_db .= "}";
			
			$cc++;
		}
		$tmp_db .= "}"; # Close the reports entry
		$tmp_db .= "}"; # Close the entry for this dashboard
		
		$c++;
	}
	$tmp_db .= "};"; # Close the data_of_all_dashboards variable.
	echo $tmp_db;
	?>
	
	<?php # This removes all events an listeners on unload ?>
	$(document).unload(function(){
		$('*').unbind();
	});
</script>
<?php
$headAddition = ob_get_clean();

$template->HTMLheadTag($headAddition); // The default content of the <head> tag, including an optional addition.
?>
	<body id='v3' onload='finishpage();'>
	
	<?php /*
	<div class='reloadiframethingy' style='width: 50px; height: 50px; background-color: #F00;'></div>
	<div class='hideiframethingy' style='width: 50px; height: 50px; background-color: #0F0;'></div>
	*/ ?>
	
	<?php
	$template->LoginForm(); // Display a Login Form, if needed.
	?>
	
	<div id="interface-overlay">
		<div class='interface_anims'></div>
	</div>
	
	<div id='iframe_window'>
		<div class='iframe_content'></div>
	</div>
	
	<?php
	$template->Navigation();
	?>
	<div id="interface">
		<?php
		$template->reportPanel();
		?>
		<div id='interface_content' style='margin: 10px 5px 0 10px'>
			<div class="workbar">
				<div class="workicons">
				</div>
			</div>
			
			<div id="content">
				<div id="tooltip"></div>
				<div id='large-grid'></div>
				<div id="report-grid">
				</div>
			</div>
		</div>
	</div>
	</body>
</html>