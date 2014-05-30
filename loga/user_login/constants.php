<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
 * Constants.php
 *
 * This file is intended to group all constants to
 * make it easier for the site administrator to tweak
 * the login script.
*/
 
//ini_set("include_path", ini_get("include_path") . ";../"); 
//include_once "../files/global.php";
 
/**
 * Database Constants - these constants are required
 * in order for there to be a successful connection.  They come from the
 * "global" setup.
 */
 
define("DB_SERVER", $mysqlserver);
define("DB_USER", $mysqluname);
define("DB_PASS", $mysqlpw);
define("DB_NAME", $DatabaseName);

/**
 * Special Names and Level Constants - the admin
 * page will only be accessible to the user with
 * the admin name and also to those users at the
 * admin user level. 
 */
define("ADMIN_NAME", "admin");

/**
 * Timeout Constants - these constants refer to
 * the maximum amount of time (in minutes) after
 * their last page fresh that a user and guest
 * are still considered active visitors.
 */
define("USER_TIMEOUT", 15);

/**
 * Cookie Constants - these are the parameters
 * to the setcookie function call, change them
 * if necessary to fit your website. If you need
 * help, visit www.php.net for more info.
 * <http://www.php.net/manual/en/function.setcookie.php>
 */
define("COOKIE_EXPIRE", 60*60*24*100);  //100 days by default
//$path = explode("/",$_SERVER['SCRIPT_NAME']);
//$path = $path[1];
$path = dirname($_SERVER['SCRIPT_NAME']);
//echo "path:$path"; 
$path = str_replace("/user_login","",$path); //take off the user_login dir
if ($path =="\\" || $path=="") {
    $path= "/";    
}

define("COOKIE_PATH", "$path");  //Avaible in install dir

/**
 * Email Constants - these specify what goes in
 * the from field in the emails that the script
 * sends to users, and whether to send a
 * welcome email to newly registered users.
 */
define("EMAIL_FROM_NAME", "Logaholic");
define("EMAIL_FROM_ADDR", "noreply@".$_SERVER['HTTP_HOST']); // youremail@address.com - this should be filled in.
define("EMAIL_WELCOME", false);

/**
 * This constant forces all users to have
 * lowercase usernames, capital letters are
 * converted automatically.
 */
define("ALL_LOWERCASE", false);

define("GUEST_NAME", "Guest");
?>
