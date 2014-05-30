<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
class UploadDir {
    
    function getUploadDir() {
        return getGlobalSetting("upload_dir", logaholic_dir()."files/");    
    }
    
    function isUploadDirSafe() {
        if (strpos($this->getUploadDir(),$_SERVER['DOCUMENT_ROOT'])!==false) {
            return false;
        }
        return true;    
    }
    
    function SecurityWarning() {
        return _SECURITY_WARNING_PART1 . $this->getUploadDir() . _SECURITY_WARNING_PART2 . $_SERVER['DOCUMENT_ROOT'] . _SECURITY_WARNING_PART3;    
    }
    
    function ChangeDir() {
        # print the link that launches the dialog to change the upload directory  ?> 
        <a id="open_change_upload_dir" href="#"><img  width=16 height=16 src=images/icons/folder_edit.png border=0 align=left><?php echo _CHANGE_UPLOAD_DIR; ?></a><P>
        <?php # print the div containing the form ?> 
        <div id="change_upload_dir" title="Change Upload Directory">
            <?php $this->ChangeForm();?>
        </div>
        <?php # make the div a dialog and add javascript to change it 
        $this->ChangeJS();
    }
    
    function ChangeForm() {
        ?>
        <P><?php echo _CHANGE_FORM; ?></p>
        <div id="upload_feedback">
            <?php echoNotice(_UPLOAD_FEEDBACK_NOTICE);?>
        </div>
        <form id="upload_dir_form">
        <input type=text name="upload_dir" id="upload_dir" onkeyup="unixslash(this.value,this.id)" size="60" value="<?php echo $this->getUploadDir();?>">
        <button id="save_new_uploaddir"><?php echo _SAVE; ?></button>
        </form>
        <?php    
    }
    
    function ChangeJS() {
        ?>
        <script language="javascript" type="text/javascript">

        $( "#change_upload_dir" ).dialog({
            autoOpen: false,
            modal: true,
            width:600,
            height:350
        });

        $( "#open_change_upload_dir" ).click(function() {
            $("#change_upload_dir").dialog("open");
            return false;
        });
        
        $( "#save_new_uploaddir" ).click(function() { 
            $.ajax({
               type: "POST",
               url: "includes/upload_dir.php",
               data: "new_upload_dir="+document.getElementById("upload_dir").value,
               success: function(msg){
                 if (msg!="") {
                     document.getElementById("upload_feedback").innerHTML=msg;
                 }
               }
             });
             return false;
        });

        </script>
        <?php    
    }  
}

# when a post request is made to change the upload dir, do this
if (isset($_POST['new_upload_dir'])) {
    include_once "../common.inc.php";
    if (is_dir($_POST['new_upload_dir'])) {
        $_POST['new_upload_dir'] = properSlash($_POST['new_upload_dir']);
        setGlobalSetting("upload_dir",$_POST['new_upload_dir']);
        $up = new UploadDir();
        if ($up->isUploadDirSafe()==false) {
            echoWarning($up->SecurityWarning());    
        }
        echoNotice(_NEW_UPLOAD_DIR_NOTICE . $_POST['new_upload_dir'],"background: url(images/icons/accept.png) no-repeat; background-position: 5px;padding-left:25px;");
    } else {
        echoWarning(_NEW_UPLOAD_DIR_WARNING_PART1 . $_POST['new_upload_dir'] . _NEW_UPLOAD_DIR_WARNING_PART2);    
    }
}
?>
