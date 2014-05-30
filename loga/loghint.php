<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
require_once 'core_factory.php';
Logaholic_sessionStart();

if (isset($_SESSION["debug"])) {
  $debug = $_SESSION["debug"];
}

$lang = Logaholic_setLang();

if (function_exists("date_default_timezone_get")) {
    $system_timezone = @date_default_timezone_get();
    if ($system_timezone!="") {
       date_default_timezone_set("$system_timezone");    
    }
}
    
$langfile = "languages/$lang.php";
include_once "$langfile";
include_once "logparser.inc.php"; 

# if we are checking an ftp path, do this
if (isset($_REQUEST['ftpserver'])) {
    include "includes/FTP_log_class.php";
    $ftp = new FTP_log($_REQUEST['ftpserver'], $_REQUEST['ftpuser'], $_REQUEST['ftppasswd'], $_REQUEST['ftpfullpath'], $_REQUEST['splitfilter'], $_REQUEST['splitfilternegative'], "", "");
    $filelist = $ftp->GetFileList($_REQUEST['ftpfullpath']);
    $filtered = $ftp->FilterFileList($filelist);
	
	if(empty($filtered)) {
		$filtered = array();
	}
	
    if ($filelist==false) {
        //echo _CANT_SCAN_DIR;
		echo '<input class="splitlogsCheckbox" type=checkbox hidden name="splitlogs" value="0">';
		
    } else {        
		echo '<input class="splitlogsCheckbox" type=checkbox hidden name="splitlogs" checked value="1">';
        echo "<font color=green>"._DIR_PATH.": </font>".$_REQUEST['ftpfullpath']."<p>";
        echo "<strong>"._CONTENTS_OF_THIS_DIR.":</strong><ul>";
        foreach($filelist as $file) {
            if (in_array($file, $filtered)) {
                echo "<li>$file</li>";
            } else {
                echo "<li class=gray>$file</li>";
            }
        }
        echo "</ul>";
    }
    exit();    
}


# do the rest of this script in case we are checking local paths
$q=$_GET["q"];
//check log file
$q=urldecode($q);
$q=stripslashes($q);
if (!$q) {
	$real_path = realpath("../index.php");
	$rpath = dirname($real_path);
	echo _FULL_ABSOLUTE_PATH_TO_LOGAHOLIC_DIR.": <font color=green>$rpath/</font><br>"._LOGFILE_USUALLY_FEW_DIRS_BACK.".";
	exit();
}
if (@file_exists($q)) {
	$what = filetype($q);
	if ($what=="dir") {
		echo '<input class="splitlogsCheckbox" type=checkbox hidden name="splitlogs" checked value="1">';
		echo "<font color=green>"._DIR_PATH.": </font>$q<p>";
		if (substr($q,-1,1)!="/") {
			$q=$q."/";
		}
		if ($handle = @opendir($q)) {
			echo "<strong>"._CONTENTS_OF_THIS_DIR.":</strong><ul>";
			while ($file = readdir($handle)) {
				if ($file[0] != '.') {
					$fullfile="$q$file";
					if (@filetype($fullfile)=="dir") {
						echo "<li> <font color=#0A3057>/$file</font><br>";
					} else {
						
					//$_REQUEST['splitfilter'], $_REQUEST['splitfilternegative']
						if (preg_match("[".$_REQUEST['splitfilter']."]",$file) > 0) {
							if (!empty($_REQUEST['splitfilternegative']) && preg_match("[".$_REQUEST['splitfilternegative']."]",$file) > 0) {
								echo "<li class=gray> $file<br>";
							} else {
								echo "<li> $file<br>";
							}
						} else {
							echo "<li class=gray> $file<br>";
						}
					}
				}
			}
			closedir($handle);
			echo "</ul>";
		} else {
			echo _CANT_SCAN_DIR;
		}
	} else if ($what=="file") {
		echo '<input class="splitlogsCheckbox" type=checkbox hidden name="splitlogs" value="0">';
		echo "<font color=green>"._THE_FILE_EXISTS.": </font>$q<P>";
		$lfown=fileowner($q);
		$lfperm=substr(sprintf('%o', fileperms($q)), -4);	 
		$lfmb=number_format(((filesize($q)/1024)/1024),2);

		echo "<b><font color=gray>"._FILE_INFO.":</font></b><br>"._LOGFILE_LOCATION.": <font color=green>$q</font><br>";
		echo _SIZE.": $lfmb MB<br>";

		if (@$_ENV['OS']!="Windows_NT") {
			if (extension_loaded('posix')) {
						$list=posix_getpwuid($lfown);
						$lfown=$list['name'];	
			}
			echo _OWNER.": $lfown<br>";
			echo _PERMISSIONS.": $lfperm<br>";
		}
		
		echo _ACCESS_CHECK.": ";
		if (is_readable($q)) {
			echo _FILE_IS_READABLE.'<br>';
			
			$logFormat = formatOfLogFile($q);
			
			if ($logFormat) {
				echo '<font color=green>'._VALID_LOGFILE_FORMAT_OF_TYPE.' <b>'.$logFormat["Description"].'</b></font>';
			} else {
				echo '<font color=red><b>'._FILE_IS_NOT_OF_A_KNOWN_LOGFILE_TYPE.'.</b></font>';
			}
		} else {
			echo _FILE_IS_NOT_READABLE.'<br>';
		}
		echo "";
	}
} else {
	// Exact file match doesn't exist - what about a partial match?
	$dir = dirname($q);
	if (@file_exists($dir) && (@filetype($dir) == "dir")) {
		// List all matching files, if there are any.
		$partialmatch = "<font color=green>"._DIR_PATH.": </font>$dir<p>";
		$matches = 0;
		
		if ($handle = @opendir($dir)) {
			$partialmatch = "<strong>"._FILES_STARTING_WITH." ".basename($q).":</strong><ul>";
			while ($file = readdir($handle)) {
				if (($file[0] != '.') && (substr($file, 0, strlen(basename($q))) == basename($q))) {
					$fullfile="$q$file";
					if (@filetype($fullfile)=="dir") {
						$partialmatch .= "<li> <font color=#0A3057>/$file</font><br>";
					} else {
						$partialmatch .= "<li> $file<br>";
					}
					$matches++;
				}
			}
			closedir($handle);
			$partialmatch .= "</ul><br>";
		}
		
		if ($matches > 0) { echo $partialmatch; }
		
	}
	
	echo "<font color=red>"._FILE_NOT_FOUND.": </font>$q<P>";
}
?>
