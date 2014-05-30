<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include_once "common.inc.php";
include_once("components/geoip/open_geoip.php");
include_once "includes/editvisitors_class.php";
$template->HTMLheadTag();
$template->BodyStart();
$edit = new editVisitor();
if(isset($_REQUEST["visid"])){
	$edit->updateLabel($_REQUEST["visid"],$_REQUEST["label"]);
	
	if($_REQUEST["setting"] == "bot"){ $crawl = 1; }else{ $crawl = 0; }
	$edit->updateSetting($crawl,$_REQUEST["visid"]);
}
if($_REQUEST["visitorid"]){
	$edit->EditVisitorId($_REQUEST["visitorid"]);
}else{
	if($_REQUEST["ipnumber"]){
		$edit->EditIpnumber($_REQUEST["ipnumber"]);
	}
}
?>