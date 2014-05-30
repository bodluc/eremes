<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
require_once 'upload_dir.php';
class Upgrade_logaholic extends UploadDir {
	//runs on local server.
	var $Main_directory, $Backup_directory, $ZIP_array;
	
	function Upgrade_logaholic(){
		$this->Main_dir();
		$this->Backup_directory = $this->getUploadDir();
	}
	
	function Directory_scan($path){
		return scandir($path);
	}
	
	//Gets the current active directory.
	function Main_dir(){
		$this->Main_directory = getcwd();
	}
	
	function Make_directory($Directory){
		return @mkdir($Directory, 0777);
	}
	
	function File_isDirectory($path){
		return is_dir($path);
	}
	
	function Copy_file($Source, $Destination){
		return @copy($Source, $Destination);
	}
	
	function Recursive_walk($Directory = '', $Copy_directory = '', $Create_directory = ''){
		if($Directory == "" && $Copy_directory == ""){
			if($Create_directory != ''){
				$this->Backup_directory .= 'backup';
				$this->Make_directory($this->Backup_directory);
				$this->Backup_directory .= '/'.$Create_directory;
				$this->Make_directory($this->Backup_directory);
			}
			$Directory = $this->Main_directory;
			$Copy_directory = $this->Backup_directory;
		}
		
		$File_array = preg_grep('#.svn#', $this->Directory_scan($Directory), PREG_GREP_INVERT);
		foreach($File_array as $Key => $Value){
			if($Value != '.' && $Value != '..'){
				$File_path = $Directory.'/'.$Value;
				$Copy_file_path = $Copy_directory.'/'.$Value;
				if($this->File_isDirectory($File_path) && $Value != $Create_directory){
					$this->Make_directory($Copy_file_path);
					$this->Recursive_walk($File_path, $Copy_file_path, $Create_directory);
				}else{
					$this->Copy_file($File_path, $Copy_file_path);
				}
			}
		}
	}//end of function
	function zipaddGlob($zip, $dir){
		$bckpDir = logaholic_dir() . "files/backup/". LOGAHOLIC_VERSION_NUMBER . "/";
		$ignoreFolders = array("files","data");
		foreach(glob($dir . '/*') as $file) {
			$newname = str_replace($bckpDir,"",$file);
			if(is_dir($file)) {
				$zip->addEmptyDir($newname);
				if(!in_array($newname,$ignoreFolders)) {
					$this->zipaddGlob($zip,$file);
				}
			} else {
				$zip->addFile($file, $newname);
			}
		}		
	}
	
	# recursively remove a directory
	function rrmdir($dir) {
		foreach(glob($dir . '/*') as $file) {
			if(is_dir($file))
				$this->rrmdir($file);
			else
				unlink($file);
		}
		rmdir($dir);
	}
	
	function zipBackup($backupDir, $version){
		set_time_limit(86400);
		$zip = new ZipArchive();
		$zipfile = $backupDir . $version . ".zip";
		
		if ($zip->open($zipfile, ZIPARCHIVE::CREATE)===TRUE) {
			$this->zipaddGlob($zip,$backupDir . $version);
			if($zip->close()){
				$this->rrmdir($backupDir . $version);
			}
		} else {
			//
		}
	}
}
?>