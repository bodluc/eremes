<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
# Define the Logaholic software version number
define("LOGAHOLIC_VERSION_NUMBER", "3.1.8");

# PHPLOCKITOPT START
# set up an array of possible editions
$edition[0] = "Free Edition";
$edition[1] = "Self Hosted Edition";
$edition[2] = "Service Provider Edition";
$edition[3] = "Windows Desktop Edition";
$edition[4] = "cPanel Edition";

# what is this version intended to be ? (Needed to know so we can make trail version for the right edition)
define("LOGAHOLIC_BASE_EDITION", $edition[0]); // or SPE or WDE or CPA

# Is this an oficial release ? Enter release or dev  
define("LOGAHOLIC_VERSION_STATUS","release");

# What database version numbers do we need?
define("CURRENT_DB_VERSION", 2.29);
define("MINIMUM_DB_VERSION", 2.29);
define("CURRENT_PROFILE_STRUCTURE_VERSION", 2.29);
define("USER_DB_VERSION", 2.20);

# Logaholic Report Store Location
if (@_LOGAHOLIC_EDITION == 4 || @LOGAHOLIC_BASE_EDITION == "cPanel Edition") {
	define("LOGAHOLIC_REPORT_STORE_LOCATION", "http://cpanelstore.logaholic.com/");
} else {
	define("LOGAHOLIC_REPORT_STORE_LOCATION", "https://store.logaholic.com/");
}

define("LOGAHOLIC_INFO_URL", "http://www.logaholic.com/insoftware_info/info.php");
?>
