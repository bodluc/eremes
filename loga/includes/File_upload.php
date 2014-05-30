<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include_once "upload_dir.php";

class Files_upload extends UploadDir {
    var $basedir;
	
    function Files_upload(){
        $this->basedir = $this->getUploadDir();
	}
    
    function UploadSettings() {
        $s = array();
        if (ini_get("file_uploads")==1) {
            $s['file_uploads'] = "On";
        } else if (ini_get("file_uploads")==0) {
            $s['file_uploads'] = "Off";
        } else {
            $s['file_uploads'] = ini_get("file_uploads");        
        }
        $s['post_max_size'] = ini_get("post_max_size");
        $s['upload_max_filesize'] = ini_get("upload_max_filesize");
        $s['target directory'] = $this->basedir;
        return $s;    
    }
    
    function isUploadEnabled() {
        if (ini_get("file_uploads")==1 || strtolower(ini_get("file_uploads"))=="on") {
            return true;
        }
        return false;        
    }
    
    function isMimeTypeSupported() {
        if(function_exists("finfo_file")){
            return true;
        } else if (function_exists("mime_content_type")){
            return true;
        } else if (system('file -ib '. logaholic_dir().'index.php')!=false) {
            return true;
        }
        return false;
    }
    
    function canWeUseUploadify() {
        if ($this->isUploadEnabled()==false) {
            return false; 
        }
        if ($this->isMimeTypeSupported()==false) {
            return false; 
        }
        return true;    
    }
    
    function MaxUploadSize() {
        $p = substr(ini_get("post_max_size"),0,-1);
        $u = substr(ini_get("upload_max_filesize"),0,-1);
        return min($p,$u);    
    }

	function UploadifyScripts(){
        global $profile;
		$output=
        '<link href="./components/uploadify/uploadify.css" type="text/css" rel="stylesheet" />
        <script type="text/javascript" src="./components/uploadify/swfobject.js"></script>
        <script type="text/javascript" src="./components/uploadify/jquery.uploadify.v2.1.4.min.js"></script>
        <script type="text/javascript">
        $(document).ready(function() {
          '."$('#file_upload').uploadify({
            'uploader'  : './components/uploadify/uploadify.swf',
            'script'    : './components/uploadify/uploadify.php',
            'cancelImg' : './components/uploadify/cancel.png',
            'folder'    : '/$profile->profilename/',
            'auto'      : true,
            'multi'		: true,
            'removeCompleted' : true,
            'onError'     : function (event,ID,fileObj,errorObj) {
             alert(errorObj.type + ' Error: ' + errorObj.info);
             },
            'onComplete' : function(a, b, c, d, e){
             if (d !== '1')
                   document.getElementById(\"uploadify_feedback\").innerHTML=document.getElementById(\"uploadify_feedback\").innerHTML+'<br />'+d;
                   //alert(d);
             },
          });
        });
        </script>";
        return $output;
	}
    
    function SelectFilesButton() {
        return '<input id="file_upload" name="file_upload" type="file" /><div id="uploadify_feedback" style="padding:4px;"></div>';        
    }
                                   
}
?>