<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* Included by top.php to check if a user is valid and also to manage login / logout duties.
**/

// Ways of authentication a user.
define("USER_AUTHENTICATION_NONE", "none");
define("USER_AUTHENTICATION_LOGAHOLIC", "logaholic");
define("USER_AUTHENTICATION_WEBSERVER", "webserver");
define("USER_AUTHENTICATION_OTHER", "other");

// This can only be included by top.php - it relies on routines in top (getGlobalSettings).

// What authentication system is in use.
$userAuthenticationType = getGlobalSetting("UserAuthenticationType", USER_AUTHENTICATION_NONE);
// If "Other" is in use, what veriable name should we use?
$userAuthenticationOther_Var = getGlobalSetting("UserAuthenticationType_Var", "");

// If you changed the authentication type and can no longer log in, uncomment this line
// to disable it, then update your settings and re-comment it when you think you have
// things working.
// $userAuthenticationType = USER_AUTHENTICATION_NONE;

$loginSystemExists = true;
if ($userAuthenticationType == USER_AUTHENTICATION_NONE) {
  $validUserRequired = false;
  
  //we added session include so it always loads because otherwise the user table does not get created on install of SPE version
  //keep an eye on this in case iot causes unexpected problems..
  include_once("session.php");  
} else {

  $validUserRequired = true;
  $hasValidUser = false;

  include_once("session.php");

  if ($session->logged_in) {
    $hasValidUser = true;
  }
}

