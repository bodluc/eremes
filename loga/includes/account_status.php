<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
if (!empty($session->username)) {
    # First, check if this account has not expired yet
    if ($session->expires!='0' && $session->expires < time()) {
        echoNotice("We're sorry. Your account ({$session->username}) expired on ".date("d M Y",$session->expires).". Please contact the system administrator. <a href=\"user_login/logout.php\">Logout</a>");
        $database->updateUserField($session->username, "active", 0);
        exit();
    }
    
    # Now, check if the account is inactive
    if ($session->active!='1') {
        echoNotice("We're sorry. Your account ({$session->username}) is currently inactive. Please contact the system administrator. <a href=\"user_login/logout.php\">Logout</a>");
        exit();
    }   
}
?>
