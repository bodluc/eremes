<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
//PHPCIPHER NO ENCRYPT
// PHPLOCKITOPT NOENCODE
/*
  This script is inteded to be used only when migrating an existing 
  Logaholic database from a windows machine to a linux machine.
  
  Problem: Windows is case insensitive, so all mysql table names are lower case.
  On linux, this is not the case. When you use mysqldump on windows and import 
  the dump file on linux, all table names will be lower case and Logaholic will break.
  
  Run this script AFTER importing the dump file on linux to rename the tables back 
  to the original casing.  

*/

# enter a password here, to prevent this script from running unintentionally
$password_for_this_script="";

// --------------------------------------------------------------------------------
// Dont't edit beyond this point
// --------------------------------------------------------------------------------

if ($password_for_this_script == "") {
    echo "Edit this script and provide a password first!";
    exit();    
}
if (!isset($_REQUEST['pass'])) {
    echo "Password: <form action=\"windows2linuxDBmigration.php\"><input type=text name=pass><input type=submit></form>";
    exit();
} else if ($_REQUEST['pass']!=$password_for_this_script) {
    echo "Access Denied";
    exit();    
}

// Initialize

set_time_limit(86400);
include("../../common.inc.php");

$db->Execute("RENAME TABLE _logaholic_profiles to _logaholic_Profiles");
$db->Execute("RENAME TABLE _logaholic_globalsettings to _logaholic_GlobalSettings");
$db->Execute("RENAME TABLE _logaholic_profile_url_params to _logaholic_Profile_URL_Params");
$db->Execute("RENAME TABLE _logaholic_traffic_sources to _logaholic_Traffic_Sources");
$db->Execute("RENAME TABLE _user_agents to _user_Agents");

$profile_names = $db->Execute("select profilename from _logaholic_Profiles");
while ($data=$profile_names->FetchRow()) {
    $p = $data[0];
    $lp = strtolower($p);
    
    # first rename the main table
    $q = "RENAME TABLE $lp to $p";
    $db->Execute($q);
    echo "<hr>".$q."<br>"; 
    
    # now get the other tables and rename them
    $tables = $db->Execute("show tables like '{$lp}\_%'");
    while ($table=$tables->FetchRow()) {
        # old table name
        $ot = $table[0];
        # new table name
        $nt = str_replace($lp,$p,$ot);
        # rename it
        $q = "RENAME TABLE $ot to $nt"; 
        $db->Execute($q);
        echo $q."<br>";    
    }    
}



?>