<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
//PHPCIPHER NO ENCRYPT
// PHPLOCKITOPT NOENCODE
/*
  Logaholic Delete range from all script
  This script should be started from the command line only.
  It will delete a date range for all profiles in your system.
  It is intended to be used as a script that can automatically
  keep you database size under control.
  
  Usage:
  php -q deleterange.php 24
  
  Options:
  number of months to hold in the database, i.e. '24' for 2 years.  

*/

// --------------------------------------------------------------------------------
// Please change Default values below
// --------------------------------------------------------------------------------
/*
    Which table do we want to delete from ?
    what = 1: only delete detail data from main table (recommended)
    what = 2: delete all
    what = 3: only delete from summary tables 
*/
$what=1;

$notifyadmin=false; // set to true if you want an email with the output from this script
$adminemail =""; // enter your email address


// --------------------------------------------------------------------------------
// Dont't edit beyond this point
// --------------------------------------------------------------------------------

if ($_SERVER['DOCUMENT_ROOT'])
{
    echo "This script should be started from the command line only.<br>Terminating.";
    exit();
}

// Initialize

set_time_limit(86400);
$running_from_command_line=true; 
$output="";
$mailmess="";
$quiet="";
include("../../common.inc.php");

if (@isset($argv[1])) {
    $HoldMonths=$argv[1];
}  else {
    if (isset($_REQUEST["HoldMonths"])) {
        $HoldMonths=$_REQUEST["HoldMonths"];
    } else { 
        echo "\nYou must specify a number of months to hold in the database\n\ne.g. php deleterange.php 24\n\n(to hold no more than the last 24 months of data in the table)\n";
        exit();
    }   
}
$from=0;
$to=strtotime("$HoldMonths months ago");
echo "\nWe're going to delete everything prior to " . date("m/d/Y",$to) . "\nPlease be patient ....\n";

// --------------------------------------------------------------------------------
// Start Script
// --------------------------------------------------------------------------------

$start=time(); 
$q= $db->Execute("select profilename from ".TBL_PROFILES);
$i=0;
while ($data=$q->FetchRow()) {
    $pname=$data[0];
    echo "\n\nDeleting from $pname...\n";
    $profile = new SiteProfile($pname);
    DeleteRange($profile,$what);  
    $i++;   
}

$took = number_format(((time() - $start) / 60),2);
$endstatus = "\n\nAll Finished!\nDeleting from $i profiles took $took minutes\n";
if ($notifyadmin==true) {
    $mailmess.=$endstatus;
    mail($adminemail,"Logaholic Delete All status",$mailmess);
}
echo $endstatus;  
?>