<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
 * 
 * This is an example file. Please make sure to name your file: updateall.php for correct useage.
 *
 */ 
//PHPCIPHER NO ENCRYPT
// PHPLOCKITOPT NOENCODE
/*
  Logaholic Update All Script
  This script should be started from the command line only.
  It will update statistics for all profiles in your system.
  
  Usage:
  php -q updateall.php
  
  Example:
  In a cron job, you could set this up to run evey hour at minute 55 like this:
  55    *       *       *       * cd /path_to_your_logaholic/;php -q updateall.php -email
  
  Options:
  php -q updateall.php -reset profilename
  This will reset the 'profilename' running status to 'no'
  
  php -q updateall.php -email
  This will email a summary status of each updated profile to the admin
  
  php -q updateall.php -emailproblems
  This will email only if any problems were encountered 

*/


// --------------------------------------------------------------------------------
// Please change settings below
// --------------------------------------------------------------------------------
// Admin email address:
define('ADMINEMAIL','');  
// the email address to send email from
define('FROMEMAIL','mysystem@logaholic.com');
// the path to the actual php command on the machine (e.g. C:\PHP\php-cgi.exe)
define('PHPCOMMAND','php'); 
// the absolute path to your logaholic directory
define('LOGAHOLIC_DIR','../../');


// --------------------------------------------------------------------------------
// Don't edit beyond this point
// --------------------------------------------------------------------------------

if ($_SERVER['DOCUMENT_ROOT'])
{
    if ($_REQUEST['key']!="4d5a5f95fa0a09d19ba898c4f4bb8b08") {
		echo "This script should be started from the command line only.<br>Terminating.";
		exit();
	}
}

set_time_limit(86400);
$running_from_command_line=true;
$notifyadmin=false;
$notifyadmin_of_problems=false;
$output="";
$mailmess="";
$problem_message="";
$quiet="1";
include(LOGAHOLIC_DIR."/common.inc.php");

if (@isset($argv[1])) {
    if ($argv[1] == "-resetall") {
        $db->Execute("update ".TBL_GLOBAL_SETTINGS." set value='no' where name like '%.update_running' and value='yes'");
        echo "Reset Update running status to 'no' for all profiles.\n";
        exit(); 
    }
    if ($argv[1] == "-reset") {
        if ($argv[2]!="") {
            setProfileData($argv[2],$argv[2].".update_running","no");
            echo "Reset Update running status to 'no' for {$argv[2]}\n";
            sleep(1);
        } else {
            echo "Reset failed, don't know what profile to reset\nyou can reset by starting the command with the reset parameter,\ni.e:\nphp -q updateall.php -reset PROFILENAME\n";
        } 
    }
    if ($argv[1] == "-email") {
        echo "Email mode - sending summary output to email: ".ADMINEMAIL."\n";
        $notifyadmin=true;    
    }
    if ($argv[1] == "-emailproblems") {
        echo "Email mode - sending any problems to email: ".ADMINEMAIL."\n";
        $notifyadmin_of_problems=true;    
    }
}


function RunUpdate($pname) {
    global $notifyadmin,$mailmess,$quiet,$problem_message,$notifyadmin_of_problems;
    if ($notifyadmin==true || $notifyadmin_of_problems==true) {
        unset($output);
        $last = exec("cd ".LOGAHOLIC_DIR."; ".PHPCOMMAND." -q ".LOGAHOLIC_DIR. "/update.php $pname", $output);
        // get last 3 lines and put them in the email message
        $count = (count($output)-1);
        $last="\n\n$pname: \n";
        $last.=$output[($count-2)]."\n";
        $last.=$output[($count-1)]."\n";
        $last.=$output[$count]."\n";
        echo $last."<br>";
        if (strpos($last,"0 log lines")!==FALSE) {
            if ($quiet=="") {
                $mailmess.="\n\n$pname: Done, processed 0 log lines.";        
            }
        } else {
            if (strpos($last,"Done! Last recorded request")!==FALSE) {
                $mailmess.="\n\n$pname: Done, " . $output[$count];
            } else {
                $mailmess.=$last;
                $problem_message.=$last;
            }
        }
    } else {
        system("cd ".LOGAHOLIC_DIR."; ".PHPCOMMAND." -q ".LOGAHOLIC_DIR."/update.php $pname");
    }   
}

$start=time(); 
$q= $db->Execute("select profilename from ".TBL_PROFILES);
$i=0;
while ($data=$q->FetchRow()) {
    $pname=$data[0];
    echo "\n\n$pname...\n";
    $running = getProfileData($pname,"$pname.update_running","no");
    if ($running=="yes") {
        // we're already running, terminate and warn administrator
        echo $message = "An Update process for '$pname' started while one was still marked as running,\nif you are sure the update is not running anymore,\nyou can reset it's status by starting the command with the reset parameter,\ni.e:\nphp -q updateall.php -reset $pname\n";
        $mailmess.= "\n\n$pname (skipping)\n".$message;
        $problem_message.= "\n\n$pname (skipping)\n".$message;  
    } else {
        RunUpdate($pname);
    }    
    $i++;   
}

$headers = 'From: '.FROMEMAIL;
$took = number_format(((time() - $start) / 60),2);
$endstatus = "\n\nAll Finished!\nUpdating $i profiles took $took minutes\n";
if ($notifyadmin==true) {
    $mailmess.=$endstatus;
    mail(ADMINEMAIL,"Logaholic Update All status",$mailmess,$headers,'-f'.FROMEMAIL);
}
if ($notifyadmin_of_problems==true && $problem_message!="") {
    mail(ADMINEMAIL,"Logaholic Update All Problem status",$problem_message,$headers,'-f'.FROMEMAIL);
}
echo $endstatus;  
?>