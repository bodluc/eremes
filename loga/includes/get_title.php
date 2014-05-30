<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include "../common.inc.php";
$profile = new SiteProfile($conf);
// this should not be able to run for a long time ... just in case
@set_time_limit(10);

function GetTitle($url) {
    global $profile,$db;
    $q = $db->Execute("Select title from $profile->tablename_urls where url=".$db->Quote($url));
    $data = $q->FetchRow();
    return $data['title'];
}

if (!empty($_REQUEST['url'])) {
    echo GetTitle($_REQUEST['url']);    
}

?>
