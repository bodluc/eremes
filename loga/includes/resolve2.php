<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include_once "../common.inc.php";

@set_time_limit(6);
$q=$_GET["q"];
$vid = $_GET["vid"];

$response = @gethostbyaddr($q);
$que = "UPDATE {$profile->tablename_visitorids} SET customlabel='$response' WHERE id='$vid'";
$db->Execute($que);
echo $response;

?>
