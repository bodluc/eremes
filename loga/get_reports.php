<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 

include_once("common.inc.php");

# This is the email address where mailto anchors will link to.
$sales_email_address = "sales@logaholic.com";

include_once("top.php");

# Get all reports by a certain profile.
function getReportsByProfile($profilename) {
	# Fetch the default reports.
	$reports = getReportArray();
	
	# Fetch the downloaded/purchased reports.
	$downloaded_reports = getProfileData($profilename, "{$profilename}.downloaded_reports", "");
	$is_download = array();
	if(!empty($downloaded_reports)) {
		$downloaded_reports = unserialize($downloaded_reports);
		
		# For each downloaded report
		foreach($downloaded_reports as $downloaded_report) {
			# If that downloaded report doesn't have a class file
			if (!file_exists("reports/".$downloaded_report.".php")) {
				$dld_report_data = unserialize(getProfileData($profilename, "{$profilename}.report.{$downloaded_report}"));
				
				# If the report can expire
				if(!empty($dld_report_data['expires_after'])) {
					# If the expire timestamp is bigger than the current timestamp, it isn't expired yet
					if($dld_report_data['installDate'] + $dld_report_data['expires_after'] > time()) {
						$not_expired = true;
					} else {
						# The report has expired
						$not_expired = false;
					}
				} else {
					# The report can not expire, so it isn't expired.
					$not_expired = true;
				}
				
				# If the report is not expired
				if($not_expired == true) {
					$dl_report = base64_decode($dld_report_data['code']);
					$dl_report = "?>".$dl_report;
					
					# Do some voodoo to only grab the contents of the $reports declaration.
					$dl_report = substr($dl_report, strpos($dl_report, "\$reports["));
					$dl_report = substr($dl_report, 0, (strpos($dl_report, ");") + 2));
					
					eval($dl_report);
					# Add the data to the array
					$is_download[$dld_report_data['classname']] = $downloaded_report;
				}
			}
		}
	}
	
	# Fetch the allocated reports.
	$alloc_reports = getProfileData($profilename, "{$profilename}.downloaded_reports", array());
	
	if(!empty($alloc_reports)) {
		# If the got allocated reports, unserialize them
		$alloc_reports = unserialize($alloc_reports);
	}
	
	# We want to fetch the Labels here.
	foreach($alloc_reports as $key => $alloc_report) {
		foreach($reports as $rep_label => $rep) {
			# If the allocated report matches the filename, we can fetch a label
			if($rep['Filename'] == $alloc_report) {
				$alloc_reports[$alloc_report]['name'] = $rep_label;
				$alloc_reports[$alloc_report]['canUpdate'] = true;
				$alloc_reports[$alloc_report]['canUninstall'] = true;
			}
		}
		
		unset($alloc_reports[$key]);
	}
	
	foreach($reports as $rep_label => $rep) {
		# If the report is Premium, we want to add it to allocated reports.
		if($rep['Distribution'] == 'Premium') {
			$alloc_reports[$rep['Filename']]['name'] = $rep_label;
			$alloc_reports[$rep['Filename']]['canUpdate'] = false;
		}
	}
	
	return $alloc_reports;
}

# Create an array with all valid profiles. If the user is admin, add all profiles to the array
$allocated_reports = array();
$validprofiles = array();
if (($validUserRequired) && (!$session->isAdmin())) { # If the user is not admin
	# Can't use implode here because we need to escape the entries.
	for ($i = count($session->user_profiles) - 1; $i >= 0; $i--) {
		$profilename = $db->escape($session->user_profiles[$i]);
		
		# Add the reports of this profile to the profiles array.
		$validprofiles[$profilename] = getReportsByProfile($profilename);
		
		$alloc_reports = getProfileData($profilename, "{$profilename}.allocated_reports", array());
		if(!empty($alloc_reports)) {
			$alloc_reports = unserialize($alloc_reports);
		}
		
		# Get all allocated reports of this profile.
		$allocated_reports[$profilename] = $alloc_reports;
	}
} else { # The user is admin, so fetch all profiles.
	$query = "SELECT profilename FROM ".TBL_PROFILES;
	$q = $db->Execute($query);
	
	while($row = $q->FetchRow()) {
		$profilename = $row[0];
		
		# Add the reports of this profile to the profiles array.
		$validprofiles[$profilename] = getReportsByProfile($profilename);
		
		$alloc_reports = getProfileData($profilename, "{$profilename}.allocated_reports", array());
		if(!empty($alloc_reports)) {
			$alloc_reports = unserialize($alloc_reports);
		}
		
		# Get all allocated reports of this profile.
		$allocated_reports[$profilename] = $alloc_reports;
	}
}

# This array will be used in javascript, so JSON encode it
$allocated_reports = json_encode($allocated_reports);

ksort($validprofiles);

if(!empty($_REQUEST['unlock_key'])) { # There is an unlock key given in the URL
	$unlock_key = $_REQUEST['unlock_key'];
} else {
	$unlock_key = "";
}

?>
	<style type='text/css'>
#InstallGrid { width: 960px; color: #333; margin: 0 auto; text-align: center; }
#InstallGrid #ReportGrid,
#InstallGrid #ProfileGrid { text-align: left; border: 1px solid silver; width: 470px; float: left; margin-bottom: 20px; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; }
#InstallGrid #ReportGrid .list-header,
#InstallGrid #ProfileGrid .list-header { width: 100%; height: auto; border-bottom: 1px solid silver; }

#InstallGrid #ProfileGrid { margin-left: 16px; }

#unlock_input,
#profile_input { height: 28px; line-height: 28px; padding: 0 5px; }
#unlock_key,
#profile-filter { background-color: #F9F9F9; margin-left: 5px; border: 1px solid silver; padding: 4px; width: 36ex; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; box-shadow: 1px 1px 3px #C1C1C1 inset; -moz-box-shadow: 1px 1px 3px #C1C1C1 inset; -webkit-box-shadow: 1px 1px 3px #C1C1C1 inset; }

#Reports,
#Profiles { height: 450px; overflow-x: hidden; overflow-y: auto; padding: 0; margin: 0; width: 100%; list-style-type: none; background: #999; text-shadow: 0 1px 0 #FFFFFF; -moz-text-shadow: 0 1px 0 #FFFFFF; -webkit-text-shadow: 0 1px 0 #FFFFFF; }
#Reports li,
#Profiles li { width: 470px; height: 28px; line-height: 28px; border-top: 1px solid #FFF; border-bottom: 1px solid silver; background-color: #E0E0E0; }
#Reports li:last-child,
#Profiles > li:last-child { box-shadow: 0 5px 5px #666; -moz-box-shadow: 0 5px 5px #666; -webkit-box-shadow: 0 5px 5px #666; border-bottom-left-radius: 4px; border-bottom-right-radius: 4px; -moz-border-bottomleft-radius: 4px; -moz-border-bottomright-radius: 4px; -webkit-border-bottom-left-radius: 4px; -webkit-border-bottom-right-radius: 4px; }

#Reports.error_message { width: 458px; padding: 5px; color: #900; background-color: #F09090; height: auto; text-shadow: none; -moz-text-shadow: none; -webkit-text-shadow: none; border: 1px solid #900; }
.error_message { width: 458px; padding: 5px; color: #900; background-color: #F09090; height: auto; text-shadow: none; -moz-text-shadow: none; -webkit-text-shadow: none; border: 1px solid #900; }

#Reports li { cursor: move; width: 460px; padding: 0 5px; }
#Reports li .quantity { float: right; }
#Reports li.disabled { cursor: default; }

#Profiles { height: 420px; }
#Profiles > li { height: auto; }
#Profiles .profilename { cursor: pointer; height: 28px; line-height: 28px; background-image: url(images/arrow_right_darkgrey_square.png); background-repeat: no-repeat; background-position: 5px center; background-color: #E0E0E0; padding: 0 5px 0 15px; border-bottom: 1px solid silver; }

#Profiles li.open .profilename { background-image: url(images/arrow_down_darkgrey_square.png); background-repeat: no-repeat; background-position: 5px center; }

.all-profiles .checkbox,
#Profiles .profilename .checkbox { cursor: pointer; width: 12px; height: 12px; float: right; margin: 8px 20px 0 0; border: 1px solid silver; background-color: #F9F9F9; border-radius: 2px; -moz-border-radius: 2px; -webkit-border-radius: 2px; }
.all-profiles .checkbox:hover,
#Profiles .profilename .checkbox:hover { border: 1px solid #333; background-color: #FFF; }
.all-profiles .checkbox .checked,
#Profiles .profilename .checkbox .checked { width: 10px; height: 10px; margin: 1px 0 0 1px; border-radius: 1px; -moz-border-radius: 1px; -webkit-border-radius: 1px; }
.all-profiles .checkbox:hover .checked,
#Profiles .profilename .checkbox:hover .checked { background-color: #FFBB00; }
.all-profiles.selected .checkbox .checked,
#Profiles .profilename.selected .checkbox .checked { background: url(images/gradient_bg_blue.png) repeat-x top left #1970B4; }
#Profiles li.includes { border-top: 1px solid #FF0; border-bottom: 1px solid #CC0; }
#Profiles li.includes .profilename { background-color: #FFBB00; text-shadow: none; -moz-text-shadow: none; -webkit-text-shadow: none; }
#Profiles li.includes.open { border-bottom: 0; }

#Profiles .profilecontent { display: none; width: 100%; padding: 0; margin: 0; list-style-type: none; }
#Profiles li.open .profilecontent { display: block; }
#Profiles .profilecontent li { cursor: move; width: 450px; background-color: #F0F0F0; padding: 0 5px 0 15px; }
#Profiles .profilecontent li.added { background-color: #C0FFC0; }
#Profiles .profilecontent li.disabled { cursor: default; }
#Profiles .profilecontent li .update-report { float: right; color: #1970B4; text-decoration: none; margin-right: 20px; }
#Profiles .profilecontent li .uninstall-report { float: right; color: #1970B4; text-decoration: none; margin-right: 20px; }
#Profiles .profilecontent li .update-report:hover { text-decoration: underline; }
#Profiles .profilecontent li .uninstall-report:hover { text-decoration: underline; }

#Profiles .profilecontent li .deleted { margin-left: 1ex; }
#Profiles .profilecontent li .reinstall { float: right; color: #1970B4; text-decoration: none; margin-right: 20px; }
#Profiles .profilecontent li .reinstall:hover { text-decoration: underline; }

#Profiles > li:last-child .profilecontent li:last-child { border-bottom-left-radius: 4px; border-bottom-right-radius: 4px; -moz-border-bottomleft-radius: 4px; -moz-border-bottomright-radius: 4px; -webkit-border-bottom-left-radius: 4px; -webkit-border-bottom-right-radius: 4px; }

.button_wrapper { width: 100px; border: 1px solid #24364C; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; float: right; margin: 0 5px; }
.button_wrapper.cancel-reset { border: 1px solid #BBC4CC; }
.button_wrapper.cancel-reset a { background-color: #E0E2E5; display: block; color: #333; padding: 5px 10px; text-align: center; text-decoration: none; border: 1px solid #E0E1E4; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; }
.button_wrapper.cancel-reset:hover a { background-color: #C1C3C9; }
.button_wrapper.commit-install a { background-color: #2B4C74; display: block; color: #FFF; padding: 5px 10px; text-align: center; text-decoration: none; border: 1px solid #2D4F7D; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; }
.button_wrapper.commit-install:hover a { background-color: #14274A; }
	</style>
	<script type='text/javascript'>
var ReportList = {};
var unlockKey;

<?php # A javascript array containing all allocated reports per profile ?>
var allocated_reports = <?php echo $allocated_reports; ?>;

function generateUnlockScript() { // Get the reports by injecting a script tag (uses unlock_key field)
	var unlockScript = document.createElement('script');
	unlockScript.type = 'text/javascript';
	var source = 'http://www.logaholic.com/logadl/report_delivery/getInvoice.php';
	unlockScript.src = source;
	unlockScript.src += "?unlock_key=" + $("#unlock_key").val();
	unlockKey = $("#unlock_key").val();
	
	// When we add this script to the head, the request is sent off.
	document.getElementsByTagName('head')[0].appendChild(unlockScript);
	// on return the script fires a function: unlock_list();
}

function generateInjectScript(productID, profileName, doUpdate, reloadOnFinish) { // Let's install a report
	if(doUpdate == undefined) {
		doUpdate = false;
	}
	
	if(reloadOnFinish == undefined) {
		reloadOnFinish = false;
	}
	
	var injectScript = document.createElement('script');
	injectScript.type = 'text/javascript';
	var source = 'http://www.logaholic.com/logadl/report_delivery/getReport2.php';
	injectScript.src = source;
	injectScript.src += "?product=" + productID + "&profilename=" + profileName;
	
	if(unlockKey == undefined) {
	} else {
		injectScript.src += "&unlock_key=" + unlockKey;
	}
	
	if(doUpdate == true) {
		injectScript.src += "&<?php echo md5('reinstall'); ?>=<?php echo md5("1"); ?>";
	}
	
	if(reloadOnFinish == true) {
		injectScript.src += "&reload=1";
	}
	
	// When we add this script to the head, the request is sent off.
	document.getElementsByTagName('head')[0].appendChild(injectScript);
	// on return the script fires a function: getReport();
}

function unlock_list(received) { // Receiver for the reports
	unlockJSON = received;
	makeReportList();
}

function makeReportList() { // Creates the list of reports
	if(unlockJSON.order_status != undefined && unlockJSON.order_status != 'Complete') {
		$("#Reports").addClass("error_message");
		
		<?php # If the order status is Pending or Processing, we want to give a different type of message ?>
		if(unlockJSON.order_status == 'Pending' || unlockJSON.order_status == 'Processing') {
			$("#Reports").html("<?php echo _YOUR_ORDER_IS_PENDING_PLEASE_WAIT; ?>");
			$("#Reports").append("<br/><br/><?php echo _IF_YOU_CAN_NOT_DOWNLOAD_CONTACT_SALES; ?> <a href='mailto:<?php echo $sales_email_address; ?>'><?php echo $sales_email_address; ?></a>");
		} else {
			<?php # Something probably went wrong with the order, so we give a message to the user ?>
			$("#Reports").html("<?php echo _YOUR_ORDER_HAS_THE_FOLLOWING_STATUS; ?>: " + unlockJSON.order_status + ".<br/><?php echo _YOU_ARE_NOT_YET_ALLOWED_TO_DOWNLOAD; ?>.");
			$("#Reports").append("<br/><br/><?php echo _IF_YOU_HAVE_QUESTIONS_CONTACT_SALES; ?> <a href='mailto:<?php echo $sales_email_address; ?>'><?php echo $sales_email_address; ?></a>");
		}
		
		return;
	}
	
	delete unlockJSON.order_status;
	
	$("#Reports").removeClass("error_message");
	$("#Reports").html("");
	
	var c = 0;
	
	<?php # download_id is the SKU of purchased reports ?>
	for(download_id in unlockJSON) {
		<?php # Add the report to ReportList ?>
		ReportList[unlockJSON[download_id]['sku']] = {
			'q': unlockJSON[download_id]['quantity']
		};
		
		<?php # if this report has a product_id (reports in the store all have a product_id) we want to set it in the src ?>
		if(unlockJSON[download_id]['product_id'] == undefined) {
			var product_src = unlockJSON[download_id]['sku'];
		} else {
			var product_src = unlockJSON[download_id]['product_id'];
		}
		
		<?php # create the HTML list item of each purchased report ?>
		var reportLink = "<li rel='" + unlockJSON[download_id]['sku'] + "' src='" + product_src + "' key='"+unlockJSON[download_id]['key']+"'>";
				reportLink += "<span class='reportname'>" + unlockJSON[download_id]['name'] + "</span>";
				reportLink += "<span class='quantity'>";
					if(unlockJSON[download_id]['quantity'] != undefined) {
						reportLink += "(x<span class='quantity_val'>" + unlockJSON[download_id]['quantity'] + "</span>)";
					}
				reportLink += "</span>";
			reportLink += "</li>";
		
		<?php # Add the just created HTML element to the reports element ?>
		$("#Reports").append(reportLink);
		
		<?php # If allocated reports match the reports from this invoice, and it is deleted, a user can reinstall it ?>
		$("#Profiles > li").each(function() {
			var allocated_in_profile = allocated_reports[$(this).find(".profilename > .name").html()];
			for(var key in allocated_in_profile) {
				if(allocated_in_profile[key] == unlockJSON[download_id]['sku'] && $(this).find(".profilecontent").find("li[rel='" + unlockJSON[download_id]['sku'] + "']").length < 1) {
					$(this).find('.profilecontent').append("<li class='disabled' rel='" + unlockJSON[download_id]['sku'] + "' src='" + product_src + "'>" + 
							"<span class='reportname'>" + unlockJSON[download_id]['name'] + "</span>" + 
							"<span class='deleted'>(<?php echo _DELETED; ?>)</span>" + 
							"<a class='reinstall' href='#'><?php echo _REINSTALL; ?></a>" + 
						"</li>");
				}
			}
		});
		
		<?php # Apply the draggable component to the last added report ?>
		$("#Reports li:last").draggable({
			helper: 'clone',
			revert: "invalid",
			stop: function() {
				<?php # If the report's quantity reaches 0, disable dragging this report ?>
				if(ReportList[$(this).attr('rel')].q != undefined && ReportList[$(this).attr('rel')].q <= 0) {
					$(this).addClass("disabled");
					$(this).draggable('destroy');
				}
			}
		}).disableSelection();
		
		c++;
	}
	
	if (c == 0) {
		<?php # If nothing has been found for this unlock key, give an error message ?>
		echoError("<div style='padding:10px;margin: 0 auto;'><p><?php echo _NOTHING_FOUND_FOR_THIS_UNLOCK_KEY; ?>.</p><?php echo _YOU_CAN_OBTAIN_UNLOCK_KEYS_BY_PURCHASING_REPORTS; ?> <a href='http://store.logaholic.com/index.php?tracking=<?php echo $template->AffiliateID(); ?>&return_url=<?php echo $template->ReturnURL(); ?>' target='_blank'>Logaholic Report Store</a><p><?php echo _WHERE_TO_FIND_YOUR_UNLOCK_KEY; ?></p></div>");
	}
}

function getReport(received) { // Receiver of the installing report
	if(received.bundlename != undefined) {
		reportJSON = received.reports;
	} else {
		reportJSON = received;
	}
	
	profilename = received.profilename;
	
	applyReportsToProfile(profilename, function() {
		// We are setting some classes here, so all looks the same as if the user refreshed the page.
		$("#Profiles .profilecontent > li[rel='" + received.file + "']").removeClass('added');
		$("#Profiles .profilecontent > li[rel='" + received.file + "']").addClass('disabled');
		$("#Profiles .profilecontent > li[rel='" + received.file + "']").parent().parent().addClass('includes');
		$("#Profiles .profilecontent > li[rel='" + received.file + "']").draggable('destroy');
		
		// If we want to reload
		if(received.reload != undefined && received.reload == 1) {
			var reload_location = window.location.href;
			var reload_params = reload_location.split('?');
			
			// Split the url, and set the unlock_key to the current inserted unlock_key.
			if(reload_params.length > 1) {
				var params = reload_params[1].split('&');
				
				var new_params = {};
				
				for(var c = 0; c < params.length; c++) {
					var tmp = params[c].split('=');
					new_params[tmp[0]] = tmp[1];
				}
				
				new_params['unlock_key'] = $("#unlock_key").val();
				
				// Unsplit the urlparameters
				var c = 0;
				var reload_urlparams = "";
				for(var key in new_params) {
					if(c > 0) { reload_urlparams += "&"; }
					reload_urlparams += key + '=' + new_params[key];
					c++;
				}
			} else {
				var reload_urlparams = "unlock_key=" + $("#unlock_key").val();
			}
			
			// Reload
			var reload_location = reload_location.split('?')[0] + '?' + reload_urlparams;
			window.location = reload_location;
		}
	});
}

function applyReportsToProfile(profilename, callback_func) { // This is where we actually allocate the installing report to a profile, and insert the code in the database.
	if(callback_func == undefined) {
		callback_func = function() {};
	}
	$.post("includes/storeReports.php", {"purchased" : reportJSON, "conf" : profilename, "unlock_key" : unlockKey}, function(result) {
		// Do something on success
		callback_func();
	});
}

function echoError(errorMsg) { // We'll echo an error message in the reports area
	$("#Reports").html("<div class='error_message'>" + errorMsg + "</div>");
}

$(document).ready(function() {
	$("#Reports > li:not(.disabled)").draggable({ // Apply the draggable to reports in the report list
		helper: 'clone',
		revert: "invalid",
		stop: function() {
			if(ReportList[$(this).attr('rel')].q <= 0) { // When there's no quantity, the report should not be dragged.
				$(this).addClass("disabled");
				$(this).draggable('destroy');
			}
		}
	}).disableSelection();
	
	$(".profilecontent > li:not(.disabled)").draggable({ // Apply the draggable to reports in the profiles list
		helper: 'clone',
		revert: "invalid"
	}).disableSelection();
	
	$("#Profiles > li").droppable({ // Make it possible to drop reports in the profiles list
		accept: "#Reports > li, .profilecontent > li",
		drop: function(ev, ui) {
			if($(this).find(".profilecontent").find("li[rel='" + ui.draggable.attr('rel') + "']").length >= 1) { // If the report already exists for this profile, we don't want to do anything (the drag will be reverted)
				return false;
			} else {
				$(this).addClass("open"); // Show the contents of the profile
				
				var cloned_object = ui.draggable.clone();
				
				if(ui.draggable.parent().hasClass('profilecontent') == true) { // If the drag was done from another profile
					ui.draggable.remove();
				} else { // If the drag was done from the reports list
					ReportList[ui.draggable.attr('rel')].q = ReportList[ui.draggable.attr('rel')].q - 1; // Substract 1 from the quantity
					ui.draggable.find(".quantity_val").html(ReportList[ui.draggable.attr('rel')].q); // Print the new quantity in the HTML
				}
				
				cloned_object.find(".quantity").remove(); // We don't want to show the quantity in profile-allocated reports.
				
				cloned_object.addClass('added'); // Add a CSS class so we know this is a to-be-added report.
				
				$(cloned_object).draggable({ // Reapply the draggable to the cloned object
					helper: 'clone',
					revert: "invalid"
				}).disableSelection();
				
				cloned_object.prependTo($(this).find(".profilecontent")); // Prepend the cloned object to the profile's list
			}
		}
	});
	
	$("#Reports").droppable({ // Make it possible to drop reports in the reports list
		accept: ".profilecontent > li", // We only accept reports that come from profiles
		drop: function(ev, ui) {
			var report = $(this).find("li[rel='" + ui.draggable.attr('rel') + "']");
			
			if(ui.draggable.siblings().length - 1 == 0) { // If there are no reports left in the parent profile, we want to close the contents
				ui.draggable.closest('.open').removeClass('open');
			}
			
			ReportList[ui.draggable.attr('rel')].q = ReportList[ui.draggable.attr('rel')].q + 1; // Add to the quantity
			
			report.find(".quantity_val").html(ReportList[ui.draggable.attr('rel')].q); // Print the quantity in the quantity value
			
			report.removeClass('disabled');
			
			$(report).draggable({ // Reapply the draggable, so we can drag again
				helper: 'clone',
				revert: "invalid",
				stop: function() {
					if(ReportList[$(this).attr('rel')].q <= 0) {
						$(this).addClass("disabled");
						$(this).draggable('destroy');
					}
				}
			}).disableSelection();
			
			ui.draggable.remove(); // Remove the dragging object
		}
	});
	
	$(".all-profiles").droppable({ // Let's make the All Profiles area droppable.
		accept: "#Reports > li",
		drop: function(ev, ui) {
			var confirmation = confirm("<?php echo _WARNING_INSERT_REPORT_IN_ALL_PROFILES; ?>"); // Let's give a nice warning, so people won't get annoyed (?).
			if(confirmation == true) {
				var quantity = ReportList[ui.draggable.attr('rel')].q;
				
				$("#Profiles > li").each(function() {
					if($(this).find(".profilename").hasClass("selected") == false) { // We only want to add reports to selected profiles
						return true;
					}
					
					if($(this).find(".profilecontent").find("li[rel='" + ui.draggable.attr('rel') + "']").length >= 1) { // We don't want to add a report to a profile that already contains that report.
						return true;
					} else {
						if(quantity > 0 || quantity == undefined) { // We will only add a report if there is enough quantity
							if($(this).is(':hidden') == true) {
								$(this).show();
							}
							
							var cloned_object = ui.draggable.clone();
							
							$(this).addClass('open');
							
							ReportList[ui.draggable.attr('rel')].q = ReportList[ui.draggable.attr('rel')].q - 1; // Substract one of the quantity
							ui.draggable.find(".quantity_val").html(ReportList[ui.draggable.attr('rel')].q);
							
							cloned_object.find(".quantity").remove();
							
							cloned_object.addClass('added');
							
							$(cloned_object).draggable({
								helper: 'clone',
								revert: "invalid"
							}).disableSelection();
							
							cloned_object.prependTo($(this).find(".profilecontent"));
							
							if(quantity != undefined) {
								quantity--;
							}
						} else {
							return;
						}
					}
				});
				
				$("#Reports > li[rel='" + ui.draggable.attr('rel') + "']").find(".quantity_val").html(quantity);
			} else {
				return false;
			}
		}
	});
	
	$(".profilename:not(.checkbox, .checked)").click(function(ev) { // Show / Hide a profile's content
		if($(this).next(".profilecontent").is(":hidden") == true) {
			$(this).parent().addClass("open"); // Show the content
		} else {
			$(this).parent().removeClass("open"); // Hide the content
		}
	});
	
	$(".all-profiles .checkbox").live("click", function(ev) { // Check / Uncheck all profiles
		if($(this).parent().hasClass("selected") == true) { // Uncheck all profiles
			$(this).parent().removeClass("selected");
			$(".profilename").removeClass("selected");
		} else {
			$(this).parent().addClass("selected"); // Check all profiles
			$(".profilename:visible").addClass("selected");
		}
	});
	
	$(".profilename .checkbox").live("click", function(ev) { // Check / Uncheck a profile
		if($(this).parent().hasClass("selected") == true) {
			$(this).parent().removeClass("selected"); // Check a profile
		} else {
			$(this).parent().addClass("selected"); // Check a profile
		}
	});
	
	$("#profile-filter").keyup(function() { // Filter profiles by the user's input
		if($("#profile-filter").val() != "") {
			$(".profilename").each(function() {
				if($(this).text().toLowerCase().indexOf($("#profile-filter").val().toLowerCase()) >= 0) {
					$(this).parent().show();
					$(this).addClass("selected");
				} else {
					$(this).parent().hide();
					$(this).removeClass("selected");
				}
			});
			
			$(".all-profiles").removeClass("selected");
		} else {
			$("#Profiles > li").show();
			$("#Profiles > li > .profilename").addClass("selected");
			$(".all-profiles").addClass("selected");
		}
	});
	
	$("#install").live("click", function() {
		generateUnlockScript();
	
		$("#Reports").removeClass("error_message");
		
		$("#Reports").html("<div style='width: 100%; padding: 0 5px; background-color: #E0E0E0; height: 30px; line-height: 30px;'><?php echo _RETRIEVING_DATA_LOGAHOLIC_REPORT_STORE; ?>...</div>");
	});
	
	$("#unlock_key").live("keyup", function(ev) {
		if(ev.keyCode == '13') {
			$("#install").click();
		}
	});
	
	$(".cancel-reset").click(function() {
		$("#Profiles").find(".profilecontent > li.added").each(function() {
			if(ReportList[$(this).attr('rel')].q == undefined || ReportList[$(this).attr('rel')].q == undefined) {
				// don't do anything with quantity
			} else {
				// add quantity
				ReportList[$(this).attr('rel')].q = parseFloat(ReportList[$(this).attr('rel')].q) + 1;
				$("#Reports > li[rel='" + $(this).attr('rel') + "']").find(".quantity_val").html(ReportList[$(this).attr('rel')].q);
			}
			
			$(this).closest(".open").removeClass("open");
			
			$(this).remove();
		});
		
		return false;
	});
	
	$(".commit-install").click(function() {
		var c = 1;
		$("#Profiles").find(".profilecontent > li.added").each(function() {
			if($("#Profiles").find(".profilecontent > li.added").length == c) {
				generateInjectScript($(this).attr('src'), $(this).closest("#Profiles > li").find(".profilename .name").html(), false, true);
			} else {
				generateInjectScript($(this).attr('src'), $(this).closest("#Profiles > li").find(".profilename .name").html(), false);
			}
			c++;
		});
		
		return false;
	});
	
	$(".update-report, .reinstall").live("click", function() {
		generateInjectScript($(this).closest('li').attr('rel'), $(this).closest('li.open').find(".profilename .name").html(), true, true);
		
		if($(this).hasClass('update-report') == true) {
			$(this).html("<?php echo _UPDATING; ?>...");
		} else {
			$(this).html("<?php echo _REINSTALLING; ?>...");
		}
		
		return false;
	});
	
	
	
	/* Remove the downloaded report from the database */
	$(".uninstall-report").live("click", function() {
		var confirmation = confirm("<?php echo _DO_YOU_REALLY_WANT_TO_DELETE_DOWNLOAD; ?>");
		if(confirmation == true) {
			$.ajax({
				url: $(this).attr("href"),
				success: function(result) {
					$(this).append(result);
				}
			});
		}
		return false;
	});
	
	
	<?php if(!empty($unlock_key)) { ?>$("#install").click();<?php } ?>
});
	</script>
	<div id='InstallGrid'>
		<h2 style='text-align: left;'>Install Reports</h2>
		<p style='text-align: left;'>
			<?php echo _TO_INSTALL_REPORTS_DRAG_AND_DROP; ?><br/>
			<?php echo _POSSIBLE_TO_DRAG_TO_ALL_PROFILES; ?>
		</p>
		<p style='text-align: left;'>
			<?php echo _IF_NOT_ENOUGH_QUANTITY_DISTRIBUTED_TOP_DOWN; ?>
		</p>
		<div id='ReportGrid'>
			<div class='list-header'>
				<div id='getReportsContainerWrapper'>
					<div id="getReportsContainer">
						<div id='unlock_input'>
							<label for='unlock_key'><?php echo _UNLOCK_KEY; ?>:</label>
							<input maxlength='32' type='text' name='unlock_key' id='unlock_key' value='<?php echo $unlock_key; ?>' />
							<input type='button' id='install' value='<?php echo _GET_INVOICE; ?>' />
						</div>
					</div>
				</div>
			</div>
			<ul id='Reports'>
			</ul>
		</div>
		<div id='ProfileGrid'>
			<div class='list-header'>
				<div id='profile_input'>
					<label for='profile-filter'><?php echo _SEARCH; ?>:</label><input type='text' id='profile-filter' name='profile-filter' />
				</div>
			</div>
			<div style='height: 28px; line-height: 28px;' class='list-header'>
				<div style='padding: 0 5px; background-color: #E0E0E0; font-weight: bold;' class='all-profiles selected'>
					<?php echo _ALL_SELECTED_PROFILES; ?>
					<div class='checkbox'>
						<div class='checked'></div>
					</div>
				</div>
			</div>
			<ul id='Profiles'>
<?php foreach($validprofiles as $profilename => $pr) { ?>
				<li class='<?php if(!empty($pr)) { echo " includes"; } ?>'>
					<div class='profilename selected'>
						<span class='name'><?php echo $profilename; ?></span>
						<div class='checkbox'>
							<div class='checked'></div>
						</div>
					</div>
					<ul class='profilecontent'>
<?php 		foreach($pr as $rel => $rep) { ?>
						
						<li class='disabled' rel='<?php echo $rel; ?>'>
							<span class='reportname'><?php echo constant($rep['name']); ?></span>
							<?php if($rep['canUninstall'] == true) { ?>							
							<a class='uninstall-report' href='includes/deletedownload.php?conf=<?php echo $profilename; ?>&delete_download=<?php echo $rel; ?>'><?php echo "Uninstall"; ?></a>
							<?php } ?>	
							<?php if($rep['canUpdate'] == true) { ?>
							<a class='update-report' href='#'><?php echo _UPDATE; ?></a>
							<?php } ?>
						</li>
<?php 		} ?>
					</ul>
				</li>
<?php } ?>
			</ul>
		</div>
		<br class='clear'/>
		<div class='button_wrapper commit-install'>
			<a href='#commit'><?php echo _COMMIT; ?></a>
		</div>
		<div class='button_wrapper cancel-reset'>
			<a href='#cancel'><?php echo _CANCEL; ?></a>
		</div>
	</div>
</body>
</html>