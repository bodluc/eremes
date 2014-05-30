<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include "common.inc.php";
$filename="logaholic.php";
$profile = new SiteProfile($conf); 
$real_path = realpath("index.php");
$path = dirname($real_path);
$logfile=$path."/files/$conf.log";
ob_start();
header("Window-target: _blank");
header("Content-type: application/x-download");
header("Content-Disposition: attachment; filename=$filename");
header("Content-Transfer-Encoding: binary");
$fname=realpath("logaholic.php");
$content = file_get_contents($fname);
$content=str_replace("replace_conf_name",$conf,$content);
$content=str_replace("replace_skipips",$profile->skipips,$content);
$content=str_replace("replace_databasedriver",$databasedriver,$content);
$content=str_replace("replace_DatabaseName",$DatabaseName,$content);
$content=str_replace("replace_mysqlserver",$mysqlserver,$content);
$content=str_replace("replace_mysqluname",$mysqluname,$content);
$content=str_replace("replace_mysqlpw",$mysqlpw,$content);
$content=str_replace("replace_trackermode",$profile->trackermode,$content);

print $content;
ob_end_flush();
?>
