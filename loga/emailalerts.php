<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include_once "includes/emailalerts.php";

?>
<html>
<head>
<title>Email Alerts</title>
</head>
<link type="text/css" href="templates/template_v2.css" rel="stylesheet">
<link type="text/css" href="components/jquery/css/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet">
<style>
    BODY { margin-right:8px; }
    H3 { font-size: 16px;}    
    #emailalerts DIV{ padding:5px; } 
    #emailalerts label{ vertical-align:top;float:left;width:120px; }
    #emailalerts #submit { font-size:14px;margin:10px 5px 5px 60px;padding-left:20px;padding-right:20px; }
    #alerttable { border: 1px solid silver; border-collapse: collapse; width:100%;}
    #alerttable TH { background-color: #E0E0E0; border: 1px solid silver; text-align: left; padding:4px 4px 4px 8px; }
    #alerttable TD { border: 1px solid silver; padding:8px; vertical-align: top; } 
</style>
<body>

<?php
// if ($validUserRequired && @$session->canEditProfiles()===false) {
	// echo "<div class=indentbody>";
	// echoWarning(_NO_PERMISSION_EDIT_PROFILE);
	// echo "<a class=\"extrabuttons ui-state-default ui-corner-all\" href=\"javascript:history.back();\">Go Back</a>";
	// echo "</div>";
	
	// return;
// }  
$e = new EmailAlerts(); 

if (isset($_GET['testalerts'])) {
    $test=true;
	$salerts = $e->SendAlerts($test);
	if(!empty($salerts)){
		foreach ($salerts as $message) {
			echo $message;
		}
	}else{
		echoNotice(_NOTHING_TO_SEND);
	}
}
if ($validUserRequired && !$session->isAdmin() ){
	if(!in_array($conf, $session->user_profiles) ){
		echoWarning("This user cannot edit this profile", "margin:5px;");
		exit;
	}
}
if (isset($_GET['del'])) {
    $e->DeleteAlert($_GET['del']);    
}

if (!empty($_POST)) {
    $e->StoreAlert($_POST);        
}
$e->ShowAlerts();
$e->CreateAlert();

$e->FootNote(); 
?>
