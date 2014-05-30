<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include_once "upload_dir.php";

class FTP_log extends UploadDir {	
	var $FTP_connection;
	var $Local_pathname;
	var $RemotePathname;
	var $FTP_files_accept;
	var $FTP_files_deny;
	var $Seektime;
	var $username;
	var $password;
	var $servername;
	var $current_file_size;
	/* function FTP_log($servername, $username, $password, $pathname)
	 * $servername is used to identify the target machiene.
	 * $username is used to identify the user on the target machine.
	 * $password is used to verify the user on targeted machine.
	 * $pathname is used to use the specified path on target machine.
	 * 
	 * $pathname is split into an array.
	 */
	function FTP_log($servername, $username, $password, $Remote_pathname, $files_accept, $files_deny, $local_pathname, $seektime){
		
		$this->Seektime = $seektime;
		$this->Local_pathname = $local_pathname;
		$this->FTP_files_accept = $files_accept;
		$this->FTP_files_deny = $files_deny;
		$this->RemotePathname = $Remote_pathname;
		$this->username = $username;
		$this->password = $password;
		$this->servername = $servername;
		$this->FTP_connect();
        if (!empty($files_accept)) {
		    if($files_accept[0] != "("){
			    $this->FTP_files_accept = "[".$files_accept."]";
		    }
        }
        if (!empty($files_deny)) { 
		    if($files_deny[0] != "("){
			    $this->FTP_files_deny = "[".$files_deny."]";
		    }
        }
		
	}
    /**
    * @desc This is called first if the user wants to download
    */
	function Files_Download($path = "", $subdir= "") {
		if($path == ""){
			$path = $this->RemotePathname;
		} else {			
			$subdir = $subdir.$this->GetFilename($path)."/";
		}
		if($this->Local_pathname[(strlen($this->Local_pathname)-1)] != "/"){
			$this->Local_pathname .= "/";
		}
		if (substr($path,-1)!="/") {
			$path .="/";
		}
		if(!is_dir($this->Local_pathname.$subdir)){
				mkdir($this->Local_pathname.$subdir);
		}

		$filelist = $this->FilterFileList($this->GetFileList($path));
		if(!is_array($filelist)){ $filelist = array(); }
		
		foreach($filelist as $key => $filepath){
			if ($filepath== '..' || $filepath == '.') {
				continue;
			}
			if($this->IsDirectory($filepath)){
				$this->Files_Download($filepath,$subdir);
				continue;
			}			
			if($this->ShouldDownload($filepath,$subdir)){
				$this->DownloadFile($filepath,$subdir);
			}else{
				//Skip file
				continue;
			}
		}
	}
	function FTP_connect(){
		if(!$this->FTP_connection = @ftp_connect($this->servername, 21, 90)){
			die("The specified remote FTP host doesn't exist.");
		}
		if(!@ftp_login($this->FTP_connection, $this->username, $this->password)){
			die("The username or password is incorrect.");
		}
	}
	/* function FilterFileList($list)
	 * $list The list that needs to be filtered 
	 * 
	 * This function is called to filter out the list which is neded an not neded.
	 * 
	 * */
	function FilterFileList($list){
        if ($list==false) {
            return false;    
        }
		
		$filelist = array();
		foreach($list as $file) {
			$filelist[] = str_replace($this->RemotePathname, "", $file);
		}
		
        if (!empty($this->FTP_files_accept)) {
            $list = preg_grep($this->FTP_files_accept, $filelist);
        }
		
        if (!empty($this->FTP_files_deny)) {
		    $list = preg_grep($this->FTP_files_deny, $list, PREG_GREP_INVERT);
        }
		
		foreach($list as $key => $file) {
			$list[$key] = $this->RemotePathname.$file;
		}
		
		return $list;
	}
	/* function GetFileList()
	 * 
	 * this function gets a list of availible files and directory's on the remote machine.
	 * */	
	function GetFileList($remotePath){
		$list = ftp_nlist($this->FTP_connection, $remotePath);
		return $list;
	}
	/* function ChangeRemotepath($remotePath)
	 * $remotePath The path directory to change to.
	 * 
	 * This function changes the remote path on the target machine.
	 */
	function ChangeRemotepath($remotePath){
		return @ftp_chdir($this->FTP_connection, $remotePath);
	}
	/* function IsDirectory($directory)
	 * $directory The directory to check
	 * 
	 * This function checks if the thing is a directory
	 */
	function IsDirectory($directory){
		if($this->ChangeRemotepath($directory)){
			$this->ChangeRemotepath("..");			
			return true;
		}
		return false;
	}
	/* function ShouldDownload($filename)
	 * $filename the file that needs checking in order to be downloaded.
	 * 
	 * this function checks if a file should be downloaded.
	 * */
	function ShouldDownload($filepath,$subdir=""){
		$filename=$this->GetFilename($filepath);
		if(($this->Seektime - ftp_mdtm($this->FTP_connection, $filepath)) > 86400){
			return false;
		}
		if(ftp_size($this->FTP_connection, $filepath) === @filesize($this->Local_pathname.$subdir.$filename)){
			return false;
		}
		return true;	
	}
	/* function DownloadFile($filename)
	 * $filename The file that will be downloaded.
	 * 
	 * This function does the actual downloading of the file.
	 * */
	function DownloadFile($filepath,$subdir){
		$filename=$this->GetFilename($filepath);		
		$resumepos = $this->GetResumePos($filename,$subdir);
		$destination = $this->Local_pathname.$subdir.$filename;
		//echo $destination;
        //exit();
        $source = $filepath;
		$this->current_file_size = ftp_size($this->FTP_connection, $source);
		echoConsoleSafe('Downloading '.$source.'  ('.$this->PrettySize($this->current_file_size).')... <span class="progress_tracker" rel="'.md5($source).'">(0%)</span><br>', true);
		$ajaxurl = "includes/FTP_progress_class.php?destination=".urlencode($destination)."&current_file_size={$this->current_file_size}";
        echoConsoleSafe("<script type='text/javascript'>
var progressTimer = setInterval(function() {
	$.get('{$ajaxurl}', function(result) {
        $('.progress_tracker[rel=".md5($source)."]').html(result);
	});
}, 2000);
		</script>\n");        
		lgflush();
		ftp_get($this->FTP_connection, $destination, $source, FTP_BINARY, $resumepos);
		echoConsoleSafe("<script type='text/javascript'>clearInterval(progressTimer);$('.progress_tracker[rel=".md5($source)."]').html('(100%)');</script>");
	}
	/* function GetResumePos($filename)
	 * $filename The file from which to get the resume position.
	 * 
	 * This function gets the resume postition for the named file.
	 * */
	function GetResumePos($filename, $subdir){
		if(!file_exists($this->Local_pathname.$subdir.$filename)){
			return 0;
		}
		if($this->IsSameFile($filename, $subdir)){
			return @filesize($this->Local_pathname.$subdir.$filename);
		}
		return 0;
	}
	/* function IsSameFile($filename)
	 * $filename The file to be checked.
	 * 
	 * This function checks if the remote file is the same as the local file.
	 */
	function IsSameFile($filename, $subdir){
		$this->DownloadPart($filename,$subdir);
		$file_pointer = fopen($this->Local_pathname.$subdir.$filename, 'r');
		$file_pointer_temp = fopen($this->Local_pathname.$subdir.$filename.".temp", 'r');
		$MD5_file = md5(fgets($file_pointer));
		$MD5_file_temp = md5(fgets($file_pointer_temp));
		fclose($file_pointer_temp);
        fclose($file_pointer);
		
        unlink($this->Local_pathname.$subdir.$filename.".temp");
		
		if($MD5_file == $MD5_file_temp){
            //echoDebug("the files are equal");
			return true;
		}
		return false;
	}
	/* function DownloadPart($filename)
	 * $filename The file that needs to be downlaoded for a little part.
	 * 
	 * This function checks if the actual remote and local file are still the same.
	 */
	function DownloadPart($filename,$subdir){
		ftp_set_option($this->FTP_connection, FTP_TIMEOUT_SEC, 1);
		$fp = fopen($this->Local_pathname.$subdir.$filename.".temp",'w');
        $remote_data = ftp_nb_fget($this->FTP_connection, $fp, $this->RemotePathname.$subdir.$filename, FTP_BINARY, 0);
        //$remote_data = ftp_nb_get($this->FTP_connection, $this->Local_pathname.$subdir.$filename.".temp", $this->RemotePathname.$subdir.$filename, FTP_BINARY, 0);
		while($remote_data == FTP_MOREDATA){
			if(filesize($this->Local_pathname.$subdir.$filename.".temp") >= 1024){	
				break;
			}
			$remote_data = ftp_nb_continue($this->FTP_connection);
		}
        ftp_close($this->FTP_connection);
        fclose($fp);
        $this->FTP_connect();
		return;
	}
	/* function PathSeparator($filename)
	 * $filename The path and file that needs to be separated.
	 * 
	 * This function separates the pathh from the file and returns it.
	 */
	function GetFilename($filename){
		$filename = pathinfo($filename);
		$filename = $filename['basename'];
		return $filename;
	}
    
    /**                                 
    * @desc This function takes the byte size and returns a string that is human readable 
    */
    function PrettySize($bytes) {
        if ($bytes > (1024*1024*1024)) {
            # we're gonna report gigabytes
            $size = number_format(($bytes/(1024*1024*1024)),2) . " GB";
        } else if ($bytes > (1024*1024)) {
            $size = number_format(($bytes/(1024*1024)),2) . " MB";
        } else if ($bytes > 1024) {
            $size = number_format(($bytes/1024),2) . " KB";
        } else {
            $size = $bytes . " Bytes";
        }
        return $size;   
    }
}
?>