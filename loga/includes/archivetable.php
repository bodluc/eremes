<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This include file handles the UI of archiving tables.
*/
$at = $_REQUEST['archivetable'];
  
// first determine if the table may be archived at all ..
function CanArchive($table) {
    $no_archive[0]= "SEGMENT";
    $no_archive[1]= "_archive";
    $no_archive[2]= "_current";
    $no_archive[3]= "_trackerlog";

    foreach($no_archive as $value) {
        if (strpos($table,$value)!==FALSE) {
            return false;
                    
        }
    }
    return true;    
}

echo "<div class=\"indentbody\">";

if (@$_REQUEST['confirm']=="true") {
   ArchiveAndMergeTable($at);       
} else {
    if (CanArchive($at)==FALSE) {
        echoWarning("You should not archive this table: $at<br>Try a different table.");        
    } else {
        echoNotice("<b>You are about to archive table '$at'</b><br>This is what will happen if you click confirm below:<br>
        <ol>
        <li> The current table will be renamed to ".$at."_archive</li>
        <li> A new empty table will created called ".$at."_current. Any new data will be inserted into this table</li>
        <li> A new MySQL MERGE table will created called ".$at.". This has the original table name and will use the 2 tables above as it's source</li>
        <li> Commands for compressing the archived part of the table with myisampack will be displayed</li>
        </ol>
        If you do this for very large tables, you can speed up Logaholic because the data will be compressed and take up less space. It only makes sense to do this if you have hundereds of MB in a table. You also need command line access and the ability to use the myisampack program.  
        <br><br>
        <a class=\"extrabuttons ui-state-default ui-corner-all\" href=profiles.php?editconf=$curprofilename&archivetable=$at&confirm=true><b>Confirm</b></a>
        ");
           
    } 
}
echo "</div></body></html>";
exit();
?>
