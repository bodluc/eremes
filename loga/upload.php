<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include_once 'common.inc.php';
if ($_REQUEST['conf']=="newcnf" || $_REQUEST['conf']=="") {
    echo("You need to give this profile a name and save it before you can use this function.");
    exit();    
}
include_once 'includes/File_upload.php';
$upload = new Files_upload();
$noheader=1; 
$addhead = $upload->UploadifyScripts();
$template->HTMLheadTag($addhead);
$template->BodyStart();
$template->LoginForm(); // Display a Login Form, if needed.

echo _UPLOAD_IFRAME_INFO;

if ($upload->isUploadDirSafe()==false) {
    echoWarning($upload->SecurityWarning());    
}
if ($upload->canWeUseUploadify()==false) {
    if ($upload->isUploadEnabled()==false) {
        echoWarning(_UPLOAD_IFRAME_WARNING_1);
    }
    if ($upload->isMimeTypeSupported()==false) {
        echoWarning(_UPLOAD_IFRAME_WARNING_2);
    }    
    echoNotice(ShowSettings());   
} else {
    $msg = _FILE_UPLOAD_LIMIT . " ". $upload->MaxUploadSize() ." MB. ";
    if (!$validUserRequired || $session->isAdmin()) {
        $msg.= "<p>".ShowSettings() ."</p>";
    }
    echoNotice($msg); 
    echo $upload->SelectFilesButton();
}

function ShowSettings() {
    global $upload;
    $o= _UPLOAD_SETTINGS . ":<ul>";
    foreach ($upload->UploadSettings() as $key => $val) {
        if ($key == "target directory") {
            $o.= "<li>". _UPLOAD_TARGET_DIRECTORY .": $val (". _CHANGE_GLOBAL_SETTINGS_TAB .")</li>";        
        } else {
            $o.= "<li>$key is: $val (". _CHANGE_PHP_INI .")</li>";
        }        
    }
    $o.= "</ul>";
    return $o;    
}

?>
</body>
</html>

