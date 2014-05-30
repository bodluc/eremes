<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include_once "../common.inc.php";

$profile = new SiteProfile($conf); 
$sort = @$_REQUEST['sort'];
$newposition = @$_REQUEST['newposition'];  


if ($sort) {

    $cachetrail = getProfileData($profile->profilename, $profile->profilename."cache_trail","");
    //echo "we start with: $cachetrail<br>";
    $reports = explode(",",$cachetrail);
    $cachetrail="";
    
    //first remove the line we are after    
    foreach ($reports as $report) {
        if ($report!=$sort) {
           $cachetrail.="$report,"; 
        }
    }
    if (substr($cachetrail,-1)==",") {
       $cachetrail= substr($cachetrail,0,-1);    
    }
    //now insert it back in at the desired position
    $reports = explode(",",$cachetrail);
    $cachetrail=""; 
    $i=0;
    foreach ($reports as $report) { 
        if ($i == $newposition) {
            $cachetrail.="$sort,";     
        }
        $cachetrail.="$report,"; 
        $i++;
    }
    if (substr($cachetrail,-1)==",") {
       $cachetrail= substr($cachetrail,0,-1);    
    }
    //echo "we end with: $cachetrail<br>";
    setProfileData($profile->profilename, $profile->profilename."cache_trail",$cachetrail);
    //echo "Repostioned $sort to position $newposition";               
}
?>
