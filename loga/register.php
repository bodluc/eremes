<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
error_reporting(E_ALL);
ini_set('display_errors','on');
 
require_once 'core_factory.php';
Logaholic_sessionStart();

$lang = Logaholic_setLang();

//include "languages/$lang/getlicense.php";
include_once "languages/$lang.php";
include "includes/version.php"; 

if (@$_POST['license']) {
    if (isset($_COOKIE['update_license'])) {
        $isupgrade="1";
        setcookie("update_license", "", time() - 86400);
    }
}

$regip=$_SERVER['HTTP_HOST'];
if (!$regip) {
  if ($HTTP_SERVER_VARS["SERVER_NAME"] !="") {
      $regip=$HTTP_SERVER_VARS["SERVER_NAME"];
  } else {
      $regip=$_SERVER['SERVER_ADDR'];
  }
}
if ($_SERVER['HTTP_HOST']!=$regip) {

} else {
    
}
$fullp="http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
$fullp = str_replace("register.php","getlicense.php",$fullp);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Register Logaholic</title>
<link rel="stylesheet" type="text/css" href="templates/template_v2.css">
<style>
BODY {
    font-family:arial;
    font-size:12px;
    margin-top:0px;
    margin-left:0px;
}
H2 {
	margin: 4px 0px 4px 0px;
}
.ui-corner-all {
	padding:4px; border-radius:4px; -moz-border-radius: 4px; -webkit-border-radius: 4px;
}
.highlight {
	border: 1px solid gold;
	background-color: #FFFFCC;	
}
.border {
	border: 1px solid silver;	
}
</style>
</head>
<body>


<table cellpadding=2 width=100% border=0 cellspacing=0>
<tr><td class=dotline bgcolor=#f0f0f0><font face=arial size=3><b>Register Logaholic Software</b></font></td>
<td class=dotline bgcolor=#f0f0f0 align=right></td>
</tr>
<tr>
<td>
<div class="indentbody" style='line-height:22px;max-width:800px;'> 

<div style='float:left;width:45%;padding:4px;margin:11px;'>
<h2>Your Information</h2>

<b><?php echo _LICENSE_HEADER;?></b><P>
<form method=post action="http://www.logaholic.com/install/makelicense.php">
<table><tr><td>
<?php echo _FIRST_NAME;?>:</td><td> <input type=text name=licname></td></tr><tr><td>
<?php echo _EMAIL_ADDRESS;?>:</td><td> <input type=text name=e></td></tr><tr><td colspan=2 align=center>
<input type=hidden name="regip" value="<?php echo $regip; ?>">
<input type=hidden name="fullp" value="<?php echo $fullp; ?>">
<input type=hidden name="version" value="<?php echo LOGAHOLIC_VERSION_NUMBER .",". LOGAHOLIC_BASE_EDITION; ?>">
<input type=submit value="<?php echo _GET_LICENSE;?>">
</td></tr></table>
</form>
<p class='border ui-corner-all'>
After you complete the process, check your email for a confirmation message from Logaholic.
</p>
 
<p class="highlight ui-corner-all">To receive the unlock key for your free report, confirm your subscription to our
newsletter and it will be sent to you immediately.</div>

</p>


<div class='border ui-corner-all' style='float:left;width:45%;padding:4px;margin:10px;line-height:28px;height:300px;background: url(http://www.logaholic.com/images/register-gift.png) no-repeat bottom right;'>
<h2>Why Register? </h2>
<ol>
<li> Register your copy of Logaholic now and unlock the <strong>"Traffic Breakdown"</strong> Report <font color=red>for free!</font></li>
<li> Allow us to notify you of bug fixes and new features</li>
<li> Receive freebies and special offers from the Logaholic Report Store</li>
</ol>
</div>


</div></td></tr>
</table>  
</body>
