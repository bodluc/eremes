<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 

include_once("components/geoip/open_geoip.php");

$reporting = true;

require_once "common.inc.php";
$start = getmicrotime();

if ($validUserRequired && (!$session->logged_in && $userAuthenticationType == USER_AUTHENTICATION_LOGAHOLIC)) { 
	echo "<script>window.location.href='index.php';</script>";
	$template->LoginForm();
	exit();
}

if (empty($conf)) { 
	echo "No profile selected";	
	exit();
}

@set_time_limit(86400);

if(empty($new_ui)) {
	include("reports_old.php");
	exit;
}

// Pull in variables that may have been passed in with the URL.
$print = @$_REQUEST["print"];
$nocache = @$_REQUEST["nocache"];
$notrail = @$_REQUEST["notrail"];
$formemail = @$_REQUEST["formemail"];
$submit = @$_REQUEST["submitbut"];
$showfields = @$_REQUEST["showfields"];
$filter = @$_REQUEST["filter"];
$item = @$_REQUEST["item"];
$item2 = @$_REQUEST["item2"];
$drilldown = @$_REQUEST["drilldown"];
$labels = @urldecode($_REQUEST["labels"]);
$status = @$_REQUEST["status"];
$agent = @$_REQUEST["agent"];
$csvpreview = @$_REQUEST["csvpreview"];
$csvexport = @$_REQUEST["csvexport"];
$country = @$_REQUEST["country"];
$search = @$_REQUEST["search"];
$searchmode = @$_REQUEST["searchmode"];
$statstable_only = @$_REQUEST["statstable_only"];
$xml = @$_REQUEST["xml"];
$old = @$_REQUEST["old"]; // this is just to be able to compare the speed to the old labels
$outputmode = @$_REQUEST["outputmode"];

if (@$_SESSION["trafficsource"]) { $trafficsource = $_SESSION["trafficsource"]; $applytrafficsource = true; } else { $applytrafficsource = false; }
    
if (isset($print)) { $noheader=1; }
$roadto=@$_REQUEST["roadto"];


/* 
................................................
.......... Main program starts here ............
................................................
*/

if(defined($labels)) {
	$clabel = $labels;
	$labels = constant($labels);
} else {
	$clabel = $get_constant[$labels];
}

switch($outputmode) {
	case "csv":
		// this creates the Report area only, no headers or interface needed 
		$profile = new SiteProfile($conf);
		
		if (class_exists('Report')) {
			if (isset($clabel) && isset($reports[$clabel])) {
				# there is a class file for this report, let's use it
				$r = new $reports[$clabel]["ClassName"]();
				$r->DisplayCSV();
			}
		}	
		break;
		
	case "xml":
		// this creates the Report area only, no headers or interface needed 
		$profile = new SiteProfile($conf);
		
		if (class_exists('Report')) {
			if (isset($clabel) && isset($reports[$clabel])) {
				# there is a class file for this report, let's use it
				$r = new $reports[$clabel]["ClassName"]();
				$r->DisplayXML();
			}
		}	
		break;
	
	case "html":
				
		if (isset($clabel) && isset($reports[$clabel])) {
			# there is a class file for this report, let's use it
			$r = new $reports[$clabel]["ClassName"]();
			$r->DisplaySimpleTable();
		}
		break;	
	
	case "print":
		
		echo $template->HTMLheadTag();
		echo $template->BodyStart();	
		
		$icon="<img src=images/icons/logaholiclogo.gif width=16 height=16 align=left style='margin-top:5px;'>";
		echo "<div id='print'>";
		echo "<h1 class='h1-title'>$icon Web Analytics Report for {$profile->confdomain}</h1>";
		
		if (isset($clabel) && isset($reports[$clabel])) {
			# there is a class file for this report, let's use it
			$r = new $reports[$clabel]["ClassName"]();
			$r->displayReportLabel = true;
			$r->displayReportButtons = false;
			echo "<div class='report'>";
			$r->DisplayReport();
			echo "</div>";
		}
		echo "</div></body></html>";
		break;
		
	case "email":
		
		echo $template->HTMLheadTag();
		echo $template->BodyStart();
		
		if (isset($clabel) && isset($reports[$clabel])) {
			# there is a class file for this report, let's use it
			$r = new $reports[$clabel]["ClassName"]();
			ob_start();
			$r->DisplaySimpleTable();
			$contents = ob_get_clean();
		}
		
		include_once "includes/emailalerts.php";
		$email = new EmailAlerts();
		# set up the message
		$mail_contents = "<html><head>";
		$mail_contents.= $email->ApplyReportStyles();
		$mail_contents.= "</head><body>";
		$mail_contents.= $_REQUEST['message']."<br />\n";
		# create all the selected reports
		$mail_contents.= $contents;   
		$mail_contents.= "<br /><br />Powered by <a href=\"http://www.logaholic.com/\">Logaholic Web Analytics</a><br /><br /></body></html>";
		# now send it
		$email->HtmlEmail($_REQUEST['email'],$_REQUEST['fromemail'],$_REQUEST['subject'],$mail_contents);
		# send a copy to the sender (seperate cus it looks like outlook messes it up)
		$email->HtmlEmail($_REQUEST['fromemail'],$_REQUEST['fromemail'],$_REQUEST['subject'],$mail_contents);
		echoNotice("Sent email to {$_REQUEST['email']} with subject: {$_REQUEST['subject']}","margin:10px;");
		echo "</div></body></html>";
		break;
	
	case "sparkline":	
	
		// echo $template->HTMLheadTag();
		// echo $template->BodyStart();	
		if (isset($clabel) && isset($reports[$clabel])) {
			# there is a class file for this report, let's use it
			$r = new $reports[$clabel]["ClassName"]();			
			$r->DisplaySparkline();
		}
		break;
		// echo "</body></html>";
		
	default:
		// this creates the Report area only, no headers or interface needed 
		if (!isset($profile)) {
			$profile = new SiteProfile($conf);
		}
		
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			//this is an ajax request
		} else {
			header("Location: v3.php?".$_SERVER['QUERY_STRING']."");
			exit();
		}
		
		$cachename = md5(str_replace("&nocache=1","",$_SERVER['QUERY_STRING']));
		
		if(!empty($nocache)) {
			deleteProfileData($profile->profilename, "{$profile->profilename}.cache_{$cachename}");
		}
		
		if(!empty($profile->usepagecache)) {
			$contents = getProfileData($profile->profilename, "{$profile->profilename}.cache_{$cachename}", '');
		}
		
		if(!empty($contents)) {
			echo "<span class='cached'></span>";
			echo $contents;
		} else {
			ob_start();
			if (isset($clabel) && isset($reports[$clabel])) {
				# there is a class file for this report, let's use it
				$r = new $reports[$clabel]["ClassName"]();
				$r->DisplayReport();
			}
			$contents = ob_get_clean();
			
			if(!empty($profile->usepagecache)) {
				setProfileData($profile->profilename, "{$profile->profilename}.cache_{$cachename}", $contents);
			}
			echo $contents;
			echo "<div style='display: none;'>Report took:". (getmicrotime() - $start) . " seconds</div>";
		}
		break;
}
if ($debug) {
	echoDebug("Report took:". (getmicrotime() - $start) . " seconds");
	echoDebug("<a onclick=\"document.body.innerHTML=''; return false;\" href=\"#\">leeg</a>");
}
if(!isset($reports[$clabel])) {
	echoNotice(_MISSING_REPORT_PART_1 ." <a href='".LOGAHOLIC_REPORT_STORE_LOCATION."index.php?tracking=".$template->AffiliateID()."&return_url=".$template->ReturnURL()."'>"._GO_TO_STORE."</a> "._MISSING_REPORT_PART_2,"margin:5px;");
}