<?php
if(isset($_REQUEST['limit']) && is_numeric($_REQUEST['limit']) === false) {
	$_REQUEST['limit'] = 10;
}

include_once("common.inc.php");

function columnSelector() {
	global $r, $optionvalues;
	
	$fields = explode(",", $r->showfields);
	
	if(!empty($optionvalues['displaymode']) && $optionvalues['displaymode'] != 'table') {
		$display = " style='display: none;'";
	} else {
		$display = "";
	}
	
	echo "<div class='column-selector'{$display}>";
	echo "<a class='optionlink column-selector-togler' style='color: #333;'>Show/Hide Columns</a>";
	echo "<div class='column-selector-form'>";
		echo "<table>";
		$i = 0;
		$first = true;
		foreach($fields as $field) {
			$name = "showColumn".$i;
			if(!isset($optionvalues[$name])) {
				$first = true;
			}else{
				$first = false;
				break;
			}
			$i ++;
		}
		$i = 0;
		foreach($fields as $field){
			$name = "showColumn".$i;
			if(isset($optionvalues[$name])) {
				$selected = "checked='yes'";
			} else {
				$selected = "";
			}
			if($first == true) {
				$selected = "checked='yes'";
			}
			echo "<tr><td><input class='noTextInput' type='checkbox' {$selected} name='showColumn{$i}' /></td><td><label for='showColumn{$i}'>{$field}</label></td></tr>";
			$i ++;
		}
		echo "</table>";
	echo "</div>";
	echo "</div>";
}


if(!empty($_REQUEST['labels'])) { $labels = $_REQUEST['labels']; }

if(!empty($labels) && $labels != 'global' && empty($_REQUEST['is_dashboard'])) {
	$report_label = $labels;
	
	$r = new $reports[$report_label]["ClassName"]();
	
	$optionvalues = $_REQUEST;
	$reportoptions = explode(",",$reports[$report_label]["Options"]);
	
	if(empty($optionvalues['period'])) {
		$optionvalues['period'] = $r->period;
	}
} else {
	$reportoptions = array("daterangeField");
}

if(empty($_REQUEST['is_dashboard']) && empty($reports[$report_label]['icon'])) {
	$reports[$get_constant[$labels]]['icon'] = "images/icons/32x32/unknown.png";
}

foreach($reportoptions as $reportoption) {
	if($reportoption == "daterangeField") {
		if(!empty($optionvalues['minimumDate']) && !empty($optionvalues['maximumDate'])) {
			$minDate = $optionvalues['minimumDate'];
			$maxDate = $optionvalues['maximumDate'];
		} else {
			$minDate = date(GetCustomDateFormat(), $from);
			$maxDate = date(GetCustomDateFormat(), $to);
		}
		?>
		<label for='daterangeField'><?php echo _DATE_RANGE; ?></label>
		<input type='text' class='noOption' id='daterangeField' name='daterangeField' value='<?php echo $minDate; ?> - <?php echo $maxDate; ?>'/>
		<?php
		echo "<input type='hidden' class='isDefault' id='minimumDate' name='minimumDate' value='{$minDate}' />";
		echo "<input type='hidden' class='isDefault' id='maximumDate' name='maximumDate' value='{$maxDate}' />";
	}
	
	if($reportoption == "displaymode") {
		if(!empty($optionvalues['displaymode'])) {
			$default_displaymode = $optionvalues['displaymode']; 
		} else {
			if(!empty($r->DefaultDisplay)) {
				$default_displaymode = $r->DefaultDisplay;
			} else {
				$default_displaymode = "table";
			}
		}
		
		$displaymodes = explode(",", $r->DisplayModes);
		
        echo "<label for='{$reportoption}'>"._DISPLAY_MODE.":</label>";
		echo "<select id=\"displaymode\" name=\"displaymode\">";
			foreach($displaymodes as $displaymode) {
				if($default_displaymode == $displaymode) { $sel = "selected"; } else { $sel = ""; }
				echo "<option {$sel} value=\"{$displaymode}\">".ucwords($displaymode)."</option>";
			}
		echo "</select>";
	}
	
	if($reportoption == "limit") {
		if(!empty($optionvalues['limit'])) {
			$limit = $optionvalues['limit'];
		}
		if (!$limit) {
		   $limit = 10;
		}
		echo "<label for='{$reportoption}'>"._LIMIT."<span class='small'> ("._MAX_NUMBER_OF_RESULTS_TO_SHOW.")</span></label><input class='isDefault' type='text' size='3' name='{$reportoption}' id='limit' value='{$limit}' class='small'>";
	}
	
	if($reportoption == "trafficsource") {
		echo "<label for='{$reportoption}' style='color: #333;'>"._FILTERS."</label>";
		if(empty($optionvalues['trafficsource'])) { $optionvalues['trafficsource'] = 0; }
		echo printTrafficSourceSelectUI($optionvalues['trafficsource']);
	}
	
	if($reportoption == "roadto") {
		if(!empty($optionvalues['roadto'])) {
			$roadto = $optionvalues['roadto'];
		}
        echo "<label for='{$reportoption}'>"._TARGET.":</label>";
        echo "<select class='isDefault' id=\"roadto\" name=\"roadto\">";

        if ($profile->targetfiles) {
			$targets=explode(",",$profile->targetfiles);
			foreach ($targets as $thistarget) {
				if ($thistarget) {
					if(isset($roadto)) {
						if (trim($thistarget)==$roadto) { $sel="selected"; } else { $sel=""; }
					} else {
						$sel="";
					}
					echo "<option $sel value=\"".trim($thistarget)."\">".trim($thistarget)."\n";
				}
			}
        } else {
			echo "<option selected=\"selected\" value=\"\">"._NONE."</option>";
		}
        echo "</select>";
	}
	
	if($reportoption == "internal_site_search") {
		if(!empty($optionvalues['internal_site_search'])) {
			$search = $optionvalues['internal_site_search'];
		} else {
			$search = getProfileData($profile->profilename, $profile->profilename.".sitesearch");
			if(empty($search)) {
				$search = "";
			}
		}
        echo "<label for='{$reportoption}'>"._SEARCH."&nbsp;"._PAGE.":</label>";
        echo "<input id=\"internal_site_search\" type=text size=40 name=internal_site_search value=\"$search\" class='small'> <span class=small> "._SEARCH_PAGE_EXPLAIN." </span>";
        echo "<input type=hidden name=searchmode value=\"like\">";
	}
	
	if($reportoption == "search") {
		if(!empty($optionvalues['search'])) {
			$search = htmlentities($optionvalues['search'], ENT_QUOTES);
		} else {
            $search = "";    
        }
		
		if(!empty($optionvalues['searchmode'])) {
			$searchmode = $optionvalues['searchmode'];
		}
		
		switch($labels) {
			case _GOOGLE_RANKINGS:
			case _KEYWORD_CONVERSION:
			case _KEYWORD_TRENDS:
			case _TOP_KEYWORDS:
			case _TOP_KEYWORDS_DETAILS:
				$metric = _KEYWORDS;
				break;
			case _REFERRER_CONVERSION:
			case _REFERRER_TRENDS:
			case _TOP_REFERRERS:
			case _TOP_REFERRERS_DETAILS:
				$metric = _REFERRERS;
				break;
			case _ERROR_REPORT:
			case _MOST_CRAWLED_PAGES:
			case _PAGE_CONVERSION:
			case _PAGE_TRENDS:
			case _TOP_PAGES:
			case _TOP_PAGES_DETAILS:
				$metric = _PAGES;
				break;
			case _MOST_ACTIVE_CRAWLERS:
				$metric = _CRAWLERS;
				break;
			case _MOST_ACTIVE_USERS:
			case _RECENT_VISITORS:
				$metric = _IP_NUMBERS;
				break;
			case _TOP_FEEDS:
				$metric = _FEEDS;
				break;
			default:
				$metric = $labels;
		}
		
		echo _SEARCH." {$metric} "._THAT." ";
		if (!empty($searchmode)) {
			echo "<select class='isDefault' id='searchmode' name='searchmode'>";
			if($searchmode == "not like") {
				echo "<option value='like'>"._MATCH."</option>";
				echo "<option value='not like' selected>"._DONT_MATCH."</option>";
			} else {
				echo "<option value='like' selected>"._MATCH."</option>";
				echo "<option value='not like'>"._DONT_MATCH."</option>";
			}
			echo "</select>";
		} else {
			echo _MATCH." <input class='isDefault' id='searchmode' type='hidden' name='searchmode' value='like'>";
		}
		echo "<input type='text' size='35' name='search' id='search' value='{$search}' class='isDefault small' title=\""._YOU_MAY_USE_AND_OR_SELECTORS."\"><span class='small'>";
		echo "</span>";
	}
	
	if($reportoption == 'source') {
		echo "<label for='sourcetype'>"._SEARCH."</label>";
		echo "<select class='report_option_field' id='sourcetype'>";
			echo "<option value=\"page\" "; if (@$optionvalues['sourcetype'] == "page") { echo "selected=\"selected\""; } echo ">"._PAGE.": </option>";
			echo "<option value=\"keyword\" "; if (@$optionvalues['sourcetype'] == "keyword") { echo "selected=\"selected\""; } echo ">"._KEYWORD.": </option>";
			echo "<option value=\"referrer\" ";     if (@$optionvalues['sourcetype'] == "referrer") { echo "selected=\"selected\""; } echo ">"._REFERRER.": </option>";
		echo "</select>";
		
		echo "<div>";
			echo "<input class='report_option_field' type=\"text\" name=\"source\" id=\"source\" value=\"".@urldecode($optionvalues['source'])."\" onkeyup=\"popupActionMenu(event, this.value+'@'+this.id+'@'+$('#sourcetype').val(), 'forminput');\" onclick=\"popupMenu(event, this.value+'@'+this.id+'@'+$('#sourcetype').val(), 'forminput');\" autocomplete=\"off\">";
		echo "</div>";
	}
	
	if($reportoption == 'period') {
		# Period selector
		$sel1 = "";
		$sel2 = "";
		$sel3 = "";
		$sel4 = "";
		
		if ($optionvalues['period'] == _DAYS) {
			$sel1 = "selected";
		} else if ($optionvalues['period'] == _WEEKS) { 
			$sel2 = "selected";
		} else if ($optionvalues['period'] == _MONTHS) { 
			$sel3 = "selected";
		} else {
			$sel4 = "selected";
		}
		
		echo "".strtoupper(substr(_REPORT,0,1)).substr(_REPORT,1).' '._PERIOD.": ";
		
		echo "<select id='period' name='period'>";
			echo "<option {$sel4} value='auto'>Auto</option>";
			echo "<option {$sel1} value='"._DAYS."'>"._DAYS."</option>";
			echo "<option {$sel2} value='"._WEEKS."'>"._WEEKS."</option>";
			echo "<option {$sel3} value='"._MONTHS."'>"._MONTHS."</option>";
		echo "</select>";
	}
	
	if($reportoption != 'columnSelector') {
		echo "<br/><br/>";
	}
}

echo "<input name='conf' id='conf' value='{$profile->profilename}' type='hidden' />";
if(!empty($labels) && $labels != 'global' && empty($_REQUEST['is_dashboard'])) {
	echo "<input name='labels' id='labels' value='{$report_label}' type='hidden' />";
}

if(!empty($r)) {
	echo $r->DisplayCustomForm();
}
/* 
if(!empty($labels) && !empty($_REQUEST['is_dashboard']) && $labels != "Global Date Range") {// Delete Dashboard
	echo "<a style='position: absolute; margin-top: 100px;' class='edit-dashboard' rel='{$labels}'><div style='width: 16px; height: 16px; float: left; margin-right: 3px; background: url(images/icons/edit_grey.png) no-repeat top center;' /><span style='line-height: 16px; display: block; white-space: nowrap;'>"._EDIT_DASHBOARD."</span></a>";
	echo "<a style='position: absolute; margin-top: 126px;' class='delete-dashboard' rel='{$labels}'><div style='width: 16px; height: 16px; float: left; margin-right: 3px; background: url(images/icons/delete_grey.png) no-repeat top center;' /><span style='line-height: 16px; display: block; white-space: nowrap;'>"._DELETE_DASHBOARD."</span></a>";
}
 */

if(in_array("columnSelector", $reportoptions) !== false) {
	if($r->displaymode != 'pie' && $r->displaymode != 'bubble') {
		columnSelector();
	}
}

if(empty($_REQUEST['is_dashboard'])) { ?>
<script type='text/javascript'>$("#report_area_extension .report_icon img").attr('src', '<?php echo $reports[$get_constant[$labels]]['icon']; ?>');</script>
<?php }

if(!empty($labels) && empty($_REQUEST['is_dashboard'])) { // Delete Report
	if(!empty($downloaded_reports)) {
		if(array_key_exists($reports[$report_label]['ClassName'], $is_download) == true) {
			echo "<a style='position: absolute; margin-top: 100px; left: 15px;' class='delete_download' href='includes/deletedownload.php?conf={$conf}&delete_download={$is_download[$reports[$report_label]['ClassName']]}'>"._DELETE_DOWNLOADED_REPORT."</a>";
		}
	}
}

if(!empty($labels) && !empty($_REQUEST['is_dashboard']) && $labels != 'global') {// Delete Dashboard
	echo "<a style='position: absolute; margin-top: 100px;' class='edit-dashboard' rel='{$labels}'><div style='width: 16px; height: 16px; float: left; margin-right: 3px; background: url(images/icons/edit_grey.png) no-repeat top center;' /><span style='line-height: 16px; display: block; white-space: nowrap;'>"._EDIT_DASHBOARD."</span></a>";
	echo "<a style='position: absolute; margin-top: 126px;' class='delete-dashboard' rel='{$labels}'><div style='width: 16px; height: 16px; float: left; margin-right: 3px; background: url(images/icons/delete_grey.png) no-repeat top center;' /><span style='line-height: 16px; display: block; white-space: nowrap;'>"._DELETE_DASHBOARD."</span></a>";
}
?>