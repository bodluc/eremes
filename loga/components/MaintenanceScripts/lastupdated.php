<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
//PHPCIPHER NO ENCRYPT
// PHPLOCKITOPT NOENCODE
/*
  Logaholic Last Updated script
  This script will display when a profile has last been updated 
  by displaying the last found timestamp in the database.
  
  Usage:
  php -q lastupdated.php

*/

// --------------------------------------------------------------------------------
// Please change Default values below
// --------------------------------------------------------------------------------

$notifyadmin=true; // set to true if you want an email with the output from this script
$adminemail =""; // enter your email address
$hours = "24"; // if you are only interested in profiles that have not been updated for more than, say 24 hours, enter 24 here 
$running_from_command_line=true; // if you set this to false, you can only use this script in a broswer if you are logged in as admin

// --------------------------------------------------------------------------------
// Dont't edit beyond this point
// --------------------------------------------------------------------------------

// Initialize

if ($_SERVER['DOCUMENT_ROOT']=="" && $running_from_command_line==false) {
    echo "\nPlease check the script settings\n";
    exit();        
}
set_time_limit(86400);
$now=time(); 
$output="";
$mailmess="";
$quiet="";
include("../../common.inc.php");



if ($running_from_command_line==false) {
    if (!$session->isAdmin())
    {
        echo "Sorry. You don't have permission to run this script.<br>Terminating.";
        exit();
    }
    $b="<br>";
} else {
    if ($_SERVER['DOCUMENT_ROOT'])
    {
        echo "Sorry. This script should be run from the command line only.<br>Terminating.";
        exit();
    }
    $b="\n";    
}

$output = $b."Profiles that have not been updated in the last $hours hours".$b."Current time is: " . date("d M Y (H:i)",$now) . "$b";

// --------------------------------------------------------------------------------
// Start Script
// --------------------------------------------------------------------------------

$start=time(); 
$q= $db->Execute("select profilename,tablename from ".TBL_PROFILES);
$i=0;
$ignoretill=($now-($hours*3600));
while ($p=$q->FetchRow()) {
    //echo "Checking {$data[0]}  ...$b";
    $pq= $db->Execute("select max(timestamp) from {$p[1]}");
    if ($pdata=$pq->FetchRow()) {
        if ($pdata[0] < $ignoretill) {
            $last_updated[$p[0]]=$pdata[0];       
        }
        $i++;
    }   
}
asort($last_updated);
$i=0;
foreach($last_updated as $profile => $d) {
    $line = date("d M Y (H:i)",$d)." => $profile".$b;
    $output.=$line;
    $data[$i][0]=$profile;
    $data[$i][1]=date("d M Y (H:i)",$d);
    $i++; 
}
$took = number_format(((time() - $start) / 60),2);
$output .= $b.$b."Done!\nChecked $i profiles took $took minutes".$b;
if ($running_from_command_line==true) {
    echo $output;    
} else {    
    ArrayStatsTable(time(),time(),"Profilename,Date",$labels="Profiles not updated in the last $hours hours",$query="");    
}
if ($notifyadmin==true) {
    mail($adminemail,"Logaholic last updated status report",$output);
} 
?>