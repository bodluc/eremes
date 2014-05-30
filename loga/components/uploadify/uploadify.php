<?php
/*
Uploadify v2.1.4
Release Date: November 8, 2010

Copyright (c) 2010 Ronnie Garcia, Travis Nickels

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

if (!empty($_FILES)) {
	include_once "../../common.inc.php";
    $basedir = getGlobalSetting("upload_dir", logaholic_dir()."files/");
    if (!is_writable($basedir)) {
        echo "Error, the target directory ($basedir) is not writable by the webserver user (".shell_exec('whoami')."). Please check permissions on this folder and try again";
        exit();    
    }
    $tempFile = $_FILES['Filedata']['tmp_name'];
    $targetPath = $basedir.$_REQUEST['folder'];
    if (!is_dir($targetPath)) {
        mkdir($targetPath);            
    }
	$targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];

    if ($tempFile=="") {
        echo "Error: There seem to be a problem with the temporary upload location (it's empty)";
        exit();    
    }
    
	if(function_exists("finfo_file")){
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$Mimetype = finfo_file($finfo, $tempFile);
		finfo_close($finfo);
	} else if (function_exists("mime_content_type")){
	    $Mimetype = mime_content_type($tempFile);
	}
	if(empty($Mimetype)){
		$Mimetype = system('file -ib '. $tempFile);
        //echo "system('file -ib '. $tempFile)";
	}
	//echo "We found mime type $Mimetype";
    
	$allowedMimes = array('text/plain', 'application/x-compressed', 'application/x-gzip');

    $Mimetype = explode(';',$Mimetype);
    $Mimetype = $Mimetype[0];
    
    if(in_array($Mimetype, $allowedMimes)){
        if(move_uploaded_file($tempFile,$targetFile)){
    		echo "File: ".$_FILES['Filedata']['name']." has been saved.";
        } else { 
        	echo "An error occurred while saving file: ".$_FILES['Filedata']['name'].", please try again.";
        }
    } else {
    	echo "The file '".$_FILES['Filedata']['name']."' is not allowed, unknown mime type ($Mimetype)";
    }	
}

?>