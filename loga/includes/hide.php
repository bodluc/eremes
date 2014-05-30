<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
// PHPLOCKITOPT NOENCODE  
// --------------------------------------------------------------------------
// HIDE AND DISABLE LOGAHOLIC ELEMENTS
// Only use this script if you want to 'open' your statistics for the world too see.
// In this case, you'll probably want to disable the things that will allow 
// visitors to destroy your stats or change your settings.
// Just uncomment the parts you need.
// -------------------------------------------------------------------------- 


// Disable files
// Uncomment this block of code if you want to disable certain files:
// settings.php and profiles.php should probably be disabled if you want 
// to stay on the safe side
// -------------------------------------------------------------------------- 

//$hide = array();
//$hide[1] = "profiles.php";
//$hide[2] = "page.php";
//$hide[3] = "clicktrail.php";
//$hide[4] = "funnels.php";
//$hide[5] = "testcenter.php";
//$hide[6] = "update.php";
//$hide[7] = "trends.php";
//$hide[8] = "admin.php";
//$hide[9] = "splittest.php"; 
//$hide[10] = "settings.php"; 

/*
if (in_array(basename($_SERVER['PHP_SELF']), $hide)) {
    
    //if (!@$argc) { //uncomment this if condition if you want to still be able to update via the command line
        
        echo "Sorry, this feature has been disabled in the Live demo. <a href=\"javascript:history.back();\">Go Back</a>";
        exit;
    
    //}
}
*/


// Forbidden parameters
// Uncomment this part if you want to keep things like Funnels, Notes, Filters but want to prevent visitors to add delete or edit your settings
// -------------------------------------------------------------------------- 
/*
if (@$del || @$funnel=="delete" || @$funnel=="save" || @$save || @$spt=="close" || @$spt=="delete" || @$spt=="publish" || @$donote=="Save"  || @$donote=="del" || @$donote=="edit" || @$command=="save" || @$command=="delete") {
     echo "Sorry, this feature has been disabled in the Live demo. <a href=\"javascript:history.back();\">Go Back</a>";
     exit();
}
*/
?>
