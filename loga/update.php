<?php  
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 

HandleInputArguments();

require_once "common.inc.php";
// require_once "components/useragents/bdetection.php";
require_once "components/useragents/techpattern.php";
SecurityCheck($page);  
HandleRunningStatus();

$show_progressbar = false;

if (@$running_from_command_line!="true") {
    $running_from_command_line = false;
}

if (!$running_from_command_line) {
	// Standard UI mode means we show header/footer and validate that the user is logged in.
    require_once "top.php";
		
	if (($validUserRequired) && (!$session->canUpdateLogs())) {
		echoWarning(_NO_PERMISSION);
        echo "</body></html>";
        setProfileData($conf,"$conf.update_running","no");
        exit();
	}
	
} else {
	// With command line mode, we *don't* check user security (if they have shell access, then we assume they
	// have access to anything they want and causing security problems here doesn't really do anything for us).
	$profile = new SiteProfile($conf);
	if (!$profile->profileloaded) {
		die(_CONFIGURATION.' '.$conf.' '._DOESNT_EXIST);
	}
    // this below would have been loaded in top.php, but we still need to check if we have to upgrade when running from command line
    // Does the current profile need to be updated?    
    if ($profile->profileloaded) {
        // make sure the profile is of adequate version to be used.
        if ($profile->structure_version < CURRENT_PROFILE_STRUCTURE_VERSION) {
            include_once "version_check.php";
            updateDataTableForProfile($profile);
            $profile->Load($profile->profilename); // Load the profile again to apply any changes
        }
    }
    
    // when we are in command line mode, we allow certain profile parameters to be changed at run time
    OverrideOptions();
}

if (file_exists("components/geoip/GeoLiteCity.dat") || file_exists("components/geoip/GeoIPCity.dat")) {
	//include_once("components/geoip/geoipcity.inc");
	$geo=1;
} else {
	echoWarning(_NO_GEO_DATA);
}
if ($force) {
	echoWarning(_FORCING_IMPORT);
}

require_once "logparser.inc.php";

if(LOGAHOLIC_VERSION_STATUS == 'dev') {
	include_once("includes/perl_update_include.php");
}

$scriptstart=time();
//echo "started on: ".date("Y-m-d H:i:s",time());

@set_time_limit(86400);

//echo "CHARACTERSET";
//var_dump(iconv_get_encoding('all'));

if (!function_exists('memory_get_usage')) {
    function memory_get_usage() {        
        return 0;
    }
}
// CREATE A COUNT FOR THE BANDWIDTH!
$bandwidth = getProfileData($profile->profilename, $profile->profilename.".bandwidthData", false);
if($bandwidth == false){ 
	$bandwidth = array(); 
} else { 
	$bandwidth = unserialize($bandwidth); 
}
function countBandwidth($bytes, $date) {
	global $bandwidth;
	$bytes = intval($bytes);
	$d = date("YmdH", $date);
	if(isset($bandwidth[$d])) {
		$bandwidth[$d] = $bandwidth[$d] + $bytes; 
	} else {
		$bandwidth[$d] = $bytes;
	}
}
function saveBandwidth() {
	global $bandwidth, $profile;	
	$bw = serialize($bandwidth);
	setProfileData($profile->profilename, $profile->profilename.".bandwidthData", $bw);
}
// let's check if we need to pack any tables first
//ArchiveAndMergeTable($profile->tablename_urlparams);

function HandleInputArguments() {
    global $conf, $running_from_command_line, $argv, $argc, $page, $force, $movedone, $debug;
    $conf = @$_REQUEST["conf"]; 
    if (@!$running_from_command_line) {
        $running_from_command_line = false;
    }
    // In case we do a simple command line options of profilename 
    if (!$conf) {
        if (@isset($argv[1])) {    
            if ($argv[1]=="running_from_command_line") {        
                $conf = $argv[2]; 
                //echo $conf; 
            } else {
                if (strpos($argv[1],"=")!==FALSE) {
                    // If we've passed in a command line, push it into the $_GET section
                    // this mean we can support name=value pairs as command line options
                    if (@$argc > 0)
                    {
                        for ($i=1;$i < $argc;$i++)
                        {
                            parse_str($argv[$i],$tmp);        
                            $_REQUEST = array_merge($_REQUEST, $tmp);
                        }
                        $running_from_command_line = @$_REQUEST["running_from_command_line"];
                        $conf = @$_REQUEST["conf"];
                        if (@!$running_from_command_line) {
                            $running_from_command_line = true;
                        } 
                    }
                } else {
                    $conf=$argv[1];                
                }
            }
            $running_from_command_line = true;
//            echo "updating '$conf' from command line\n"; 
            //exit();
        } else {
            # attempt to read piped input
            $stdin_stream = fopen('php://stdin', 'r');
            if (is_resource($stdin_stream)) {
                stream_set_blocking($stdin_stream, 0); #should be necessary, but just in case
                if ($line = fgets($stdin_stream, 1024)) {
                    parse_str(trim($line),$tmp);
                    $_REQUEST = array_merge($_REQUEST, $tmp);
                    $running_from_command_line = true;
                }
                fclose($stdin_stream); #shouldn't be necessary, but we'll do it anyway
            }
        }
    }

    $page = @$_REQUEST["page"];  // see security check below

    if (@$_REQUEST["force"]) {
        $force = true;
        //if force is true, it still does not redo the summay tables, we have to think of something for that (maybe just delete the summary tables ?, or remember the 'oldest' that gets inserted into the database (is it worth the effort ?)?)
    } else {
        $force = false;
    }

    if (@$_REQUEST["movedone"]) {
        $movedone = true;
    } else {
        $movedone = false;
    }

    $debug="";
    if (@$_REQUEST["debug"]) {
        $debug = $_REQUEST["debug"];   
    }
}

function OverrideOptions() {
    global $profile;
    
    if (isset($_REQUEST['logfilefullpath'])) {
        $profile->logfilefullpath = $_REQUEST['logfilefullpath'];    
    }    
    if (isset($_REQUEST['splitlogs'])) {
        $profile->splitlogs = $_REQUEST['splitlogs'];    
    }
    if (isset($_REQUEST['recursive'])) {
        $profile->recursive = $_REQUEST['recursive'];    
    }
    if (isset($_REQUEST['splitfilter'])) {
        $profile->splitfilter = $_REQUEST['splitfilter'];    
    }    
    if (isset($_REQUEST['splitfilternegative'])) {
        $profile->splitfilternegative = $_REQUEST['splitfilternegative'];    
    }
    if (isset($_REQUEST['skipips'])) {
        $profile->skipips = $_REQUEST['skipips'];    
    }
    if (isset($_REQUEST['skipfiles'])) {
        $profile->skipfiles = $_REQUEST['skipfiles'];    
    }
    if (isset($_REQUEST['urlparamfilter'])) {
        $profile->urlparamfilter = $_REQUEST['urlparamfilter'];    
    }
    if (isset($_REQUEST['urlparamfiltermode'])) {
        $profile->urlparamfiltermode = $_REQUEST['urlparamfiltermode'];    
    }
    if (isset($_REQUEST['googleparams'])) {
        $profile->googleparams = $_REQUEST['googleparams'];    
    }        
}

function HandleRunningStatus($mode = 'regular') {
    global $conf, $profile, $running_from_command_line, $lang, $validUserRequired, $session, $template;
	
    if (@$_REQUEST['reset'] == 1) {
		if($mode == 'perl') {
			setProfileData($profile->profilename, "{$profile->profilename}.perlupdate_running", "no");
		} else {
			setProfileData($profile->profilename, "{$profile->profilename}.update_running", "no");
		}
    }
	
	if($mode == 'perl') {
		$running = getProfileData($profile->profilename, "{$profile->profilename}.perlupdate_running", "no");
	} else {
		$running = getProfileData($profile->profilename, "{$profile->profilename}.update_running", "no");
	}
	
    if ($running == "yes") {
		# check if update is taking longer then 24 hours
		$time_check = getProfileData($profile->profilename, "{$profile->profilename}.update_time", time());
		$time_check_diff = time() - $time_check;
		if($time_check_diff >= 86400) {
			if($mode == 'perl') {
			setProfileData($profile->profilename, "{$profile->profilename}.perlupdate_running", "no");
			} else {
				setProfileData($profile->profilename, "{$profile->profilename}.update_running", "no");
			}			
		} else {		
			# we're already running, warn usere
			if ($running_from_command_line == false) { include_once "top.php"; }
			
			$button = Button("update.php?conf=$conf&amp;reset=1", "Reset Status and Continue", "color: black;");
			$out = Warning(_HANDLE__UPDATE_RUNNING_STATUS_WARNING_PART1 . " '$conf' ". _HANDLE__UPDATE_RUNNING_STATUS_WARNING_PART2 . "<br><br>$button\n\n");
			echoConsoleSafe($out, true);
			if ($running_from_command_line == true) { echo "from the console, use this to reset:\n> php update.php \"conf=$conf&reset=1\"\n"; } 
			echoConsoleSafe("</body></html>");
			exit();
		}
    }
	
	setProfileData($profile->profilename, "{$profile->profilename}.update_time", time());
	
	if($mode == 'perl') {
		setProfileData($profile->profilename, "{$profile->profilename}.perlupdate_running", "yes");
	} else {
		setProfileData($profile->profilename, "{$profile->profilename}.update_running", "yes");
	}
}

function gzend($fh) {
    /*
    *  sets the file pointer at the end of the file
    *  and returns the number of bytes in the file.
    */
   $d   = 1<<14;
   $eof = $d;
   while ( gzseek($fh, $eof) == 0 ) $eof += $d;
   while ( $d > 1 )
   {
      $d >>= 1;
      $eof += $d * (gzseek($fh, $eof)? -1 : 1);
   }
   return $eof;
}

function EstimateLines($logfile) {
    global $lastpos,$debug,$profile,$totbytes,$firstlogline;
    echoConsoleSafe(" ("._ESTIMATING." ", true);
    
     // Open the file (gzopen will open it as a regular file if it's not a gzipped file).
    $testlogfile = gzopen($logfile, "r");
    //$testlogfile = fopen($profile->logfilefullpath, "r");
    
    $totbytes=filesize($logfile);
    if (substr($logfile,-3,3)==".gz") {     
        //estimated uncompressed byte size
        //$totbytes=$totbytes*19;         
        // this is cool and accurate, but a bit heavy
        echoConsoleSafe("<script type='text/javascript'>pstatus('Getting uncompressed file size for $logfile')</script>");
        lgflush();
         
        //echo "totbytes old style is $totbytes, ";
        $totbytes=gzend($testlogfile);
        //echo "totbytes new style is $totbytes";
        gzrewind($testlogfile);        
    }   
    
    $loglinecount=0;
    $logbytes = 0;
    $testlogdata = "";
    
    //check if file is still the same 
    $flogline=gzgets($testlogfile,5120);
    if ($firstlogline!=md5($flogline)) { 
        $lastpos=0;   
    }
    
    if ($bytesinline = getProfileData($profile->profilename, "bytesinline.".md5($logfile), 0)) {
        echoDebug(_USING_ACCURATE_ESTIMATE.' '.$bytesinline);
    } else {
    
        while ($loglinecount < 200) {
         $testlogdata .= gzgets($testlogfile,5120);
         //$testlogdata .= fgets($testlogfile,4096);
         $loglinecount++;
        }

        $actualbytes = strlen($testlogdata);
        $bytesinline = @($actualbytes / $loglinecount);
        unset($testlogdata);
    }
    
    $lines = @intval(($totbytes-$lastpos) / $bytesinline);

    echoDebug("<P>totalbytes = ". number_format($totbytes) . "<br>"."lastpos = ". number_format($lastpos) . "<br>"."$lines = @intval(($totbytes-$lastpos) / $bytesinline);"."about $lines lines.(lastpos=$lastpos and firstlogline=$firstlogline)<p>");
    
    if ($lines <= 0) {
        $lines = 1;
    }
    gzclose($testlogfile);
    
    if (!$lines) {
        echoConsoleSafe('<b>'._UNKNOWN.'</b>)<br>'."\n", true);
    } else {
        if ($lines==1) {
            echoConsoleSafe("<b>"._ZERO_NEW_LINES_TO_PROCESS."</b>)<br>\n", true);
        } else {
            $prettylines=number_format($lines);
            echoConsoleSafe('<b>'.$prettylines.' '._LINES_TO_PROCESS.'</b>)<br>'."\n", true);
        } 
    }
    lgflush();
    return $lines;   
}    

function ReadLogDir($logfilefullpath) {
	global $seektime,$st,$seektimestamp,$grepv,$httpget,$lines,$firstlogline,$lastpos,$startpos,$update_php_mode;
	global $profile, $movedone;
	global $printed_pbar;
    $seektimestamp=$st;
    //echo "ST is $st";
	$handle = opendir($logfilefullpath);
	
    if (!isset($printed_pbar)) {
        echoConsoleSafe('<form name=progress>'._PROGRESS.':<br><input type=text name=perc size=1 value="0" class=pbar>% &nbsp;');
        echoConsoleSafe('<input type=text name=progbar size=46 value="|" class=progbar><P>');
        echoConsoleSafe(''._STATUS.': <input type=text name=ptext size=85 value="'._READING_DIRECTORY.'" class="pbar progbar"><P>');
        $printed_pbar=1;
    }
    lgflush();
  
    while ($file = readdir($handle)) {
        if ($file[0] != '.') {
            $rfn="$logfilefullpath$file";
            $ls[$file] = filemtime($rfn);
            //echo $file."<br>";
        }
    }
    if (!isset($ls)) {
       echoConsoleSafe("<script type='text/javascript'>pstatus('$handle "._IS_EMPTY."')</script>");
       closedir($handle);
       lgflush();
       return; 
    }  
    
	$sql_logfiles = array();
	
    asort($ls);
    $skippedfiles=0;  
	foreach($ls as $file => $modtime) {
		if ($file[0] != '.') {
			//fix voor Niels, recursive directory
			$fullfile= $logfilefullpath.$file;
			if (filetype($fullfile)=="dir") {
                //don't do resursive by default
				if ($profile->recursive==1) {
                    //do this directory too
				    echoConsoleSafe("\n<font color=silver>"._READING_DIRECTORY.": $fullfile</font><br>\n", true);
				    $fullfile=$fullfile."/";
                    ReadLogDir($fullfile);
                }
				continue;
			}
			echoConsoleSafe("<script type='text/javascript'>pstatus('"._READING." $file')</script>"); 
            //echoConsoleSafe("reading $file .... \n", true);
            if ($profile->splitfilternegative) {
                if (strpos($file, $profile->splitfilternegative)!==FALSE) {
                    echoConsoleSafe("<script type='text/javascript'>pstatus('"._SKIPPING." $file')</script>");
                    $skippedfiles++;
                    lgflush();
                    continue;                
                }
            }
			if ($profile->splitfilter) {
                if (strpos($profile->splitfilter, '(') !== FALSE) {
                    //we have a regex splitfilter
                    if (preg_match($profile->splitfilter, $file)) {
                        //it matched, so we want to analyze it
                    } else {
                        //it didn't match, skip it
                        echoConsoleSafe("<script type='text/javascript'>pstatus('"._SKIPPING." $file')</script>");
                        $skippedfiles++;
                        lgflush();
                        continue;                        
                    }
                } else if (strpos($file, $profile->splitfilter)===FALSE) {
					echoConsoleSafe("<script type='text/javascript'>pstatus('"._SKIPPING." $file')</script>");
                    $skippedfiles++;
                    lgflush();
                    //usleep(250000);
					continue;				
				}
			}
            
            if (strpos($file,"ftp")!==FALSE || strpos($file,"error")!==FALSE) {
                    echoConsoleSafe("<script type='text/javascript'>pstatus('"._SKIPPING." $file, "._PROBABLY_NOT_AN_ACCESS_LOG."')</script>");
                    $skippedfiles++;
                    lgflush();
                    continue;                
            }
            
			$rfn="$logfilefullpath$file";
			if (($seektimestamp - filemtime($rfn)) > 86400) {
                echoConsoleSafe("<script type='text/javascript'>pstatus('"._SKIPPING." $file ("._ALREADY_DONE.")')</script>");
                $skippedfiles++;
                lgflush();                                                                                  
                //usleep(250000);
                continue;
			}
            		
			if ($file!="") {
				$at = $logfilefullpath . $file;
                $logFormat = formatOfLogFile($at);
                echoConsoleSafe("\n "._READING." <b>$at</b><br> ", true);
                if ($logFormat) {
                    echoConsoleSafe("[".$logFormat['Description']."]\n", true);
                } 
                echoConsoleSafe(".... \n" .number_format((filesize($at)/1000/1000),2). " MB \n");             
                $lastpos = getProfileData($profile->profilename, "lastlogpos.".md5($at), 0);
                
                //if it's a gz file and it's been modified, we can't trust lastpos to bring us back to the right position, so we need to go through the file again                
                if (substr($file,-3,3)==".gz") {
                    $lastmodtime = getProfileData($profile->profilename, "lastmodtime.".md5($at), 0);
                    //echo "$lastmodtime!=$modtime";
                    if ($lastmodtime!=$modtime) {
                        $lastpos=0;
                    }             
                }
                
                $firstlogline = getProfileData($profile->profilename, "firstlogline.".md5($at), "");
                $startpos=$lastpos; // do this so we still know the original lastpos when we finish update
                //echo "the firstlogline is $firstlogline<br>";
                $lines=EstimateLines($at);
				echoConsoleSafe("<script type='text/javascript'>self.document.forms.progress.progbar.value='|';</script>");
				echoConsoleSafe("<script type='text/javascript'>pstatus('"._OPEN." $file')</script>");
				if($update_php_mode !== false) {
					analyzeThis($at);
				} else {
					$sql_logfiles[] = $at;
				}
                setProfileData($profile->profilename, "lastmodtime.".md5($at), $modtime);
                
                if ($movedone==true) {
                        $donefile = $logfilefullpath . "done/" . $file;
                        echo "<b>";
                        if (rename($at,$donefile)) {
                            echoConsoleSafe(_MOVED_FILE_TO." $donefile<P>\n", true);
                        } else {
                            echo "can't move $at to $donefile";
                        } 
                        echoConsoleSafe("</b>"); 
                                            
                }
				continue;
			}			
		}
	}
	closedir($handle);
	echoConsoleSafe("</form><script type='text/javascript'>pbar(100)</script>");
    if ($skippedfiles > 0) {
        echoConsoleSafe("<font color=silver>"._SKIPPED." $skippedfiles "._FILES_IN." $logfilefullpath</font><br>\n", true); 
    }
	$lines=0;
	lgflush();
	
	if($update_php_mode === false) {
		return $sql_logfiles;
	}
}

function mygets($logfile,$bytes,$zmode) {
    global $buf,$linemethod,$mygetstime;
    $starttime=getmicrotime();
    //$linemethod="";
	if ($zmode!="gz") {
        if ($linemethod=="stream") {
            // this method is supposed to be much faster than fgets in PHP 5
            return stream_get_line($logfile, 1000000, "\n"); 
        } else if ($linemethod=="fscanf") {
            // this method is faster, but seems to leak memory on big files, making it slower in the end
            $nline = fscanf($logfile, "%[ -~]\n");
            //echo $nline[0] . "<br>";
            //    lgflush();
            return $nline[0];    
        } else if ($linemethod=="bulk") {
            //let's experiment 
            
            if ($buf=="") {
                
                //Read 16meg chunks
                //$read = 16777216;
                $read = 1000000;
                //\n Marker   chr(10)
                while(!feof($logfile)) {
                    $buf = fread($logfile, $read);
                    $stop_at = strrpos ($buf , "\n" );
                    $lastline=substr($buf,$stop_at); 
                    $buf = substr($buf,0,($stop_at+1));
                    $llbytes=strlen($lastline);
                    if ($llbytes > 1) {
                        //we are here
                        $here=ftell($logfile);
                        //we should be here
                        $newhere=($here-$llbytes)+1;
                        fseek($logfile, $newhere);
                        echo ''._READ_PART.' '.$part.' '._CORRECTED." $newhere=$here-$llbytes; <br>";
                    } else {
                        echo ''._READ_PART.' '.$part.', '._NO_CORRECTION.'; <br>';  
                    }
                }
                $filepart=$logfile."workfile.tmp";
                $fppart = fopen($filepart, "w"); 
                fwrite($fppart, $buf); 
                fclose($fppart);  
            }
            //get the next line from the buffer
            $stop_at = strpos ($buf , "\n" );
            $newline=substr($buf,0,$stop_at);
            $buf = substr($buf,($stop_at+1));
            
            //echo "<br>$newline<br>";
            //exit();
            return $newline;                     
        } else if ($linemethod=="sql"){
            $nline['logline']="";
            $nline=$logfile->FetchRow();
            //echo $nline[0]."<br>";
            //sleep(1);
            return $nline['logline'];             
        } else {
            $stoptime=getmicrotime();
            $mygetstime=$mygetstime+($stoptime-$starttime);
            return fgets($logfile);
        }
	} else {
        $pl = gzgets($logfile,$bytes);
        //a zip file can contain error logs, so lets check that 
        if (strpos(" ".$pl,"[error]")!=FALSE) {
            //echoConsoleSafe("<script type='text/javascript'>pstatus('There is error log data in this zip file .. trying to skip')</script>");   
            $pl="skiperrorloglines";
        }
        return $pl;
	}
}

function myseek($logfile,$bytes) {
    global $zmode;
    //echo "zmode is $zmode";
    if ($zmode=="gz") {
        return gzseek($logfile,$bytes);
    } else {
        return fseek($logfile,$bytes);
    }
}

function mytell($logfile) {
    global $zmode;
    if ($zmode=="gz") {
        return gztell($logfile);
    } else {
        return ftell($logfile);
    }
}


// parse the interesting URL list - set up all search variables, etc.
function setupURLandParamData() {
	global $profile;
	global $importantURLParams;
	global $regexURLParamMatch;
	
	// Build a regex of all the important names.
	$importantURLParams = array();
	$regexURLParamMatch = "";
	
	// Iterate through all the parameter types.
	for ($param_loop = 0; $param_loop < $profile->getUrlParamCount(); $param_loop++) {
		$this_param =& $profile->getUrlParamByIndex($param_loop);
		if ($this_param["filename"]) {
			// We need to handle regexes, but we don't yet support this.
			if ($this_param["nameisregex"]) {
				$regexURLParamMatch .= "(".$this_param["filename"].")|";
			} else {
				$importantURLParams[] = $this_param["filename"];
			}
		}
		$this_param["paramarray"] = preg_split('/\s*,\s*/', trim($this_param["importantparams"]));
	}
	
	// Now, pull in any target urls where a parameter has been specified.
	foreach ($profile->targets as $thistarget) {
		// Does the target url have a ? (or a parameter).  If so, let's add it on to our array.
		if (!(($splitloc = strpos($thistarget, "?")) === false)) {
			
			// Create a temporary important parameter and stick it into the profile's list of parameters.
			// This shouldn't be permanently saved, just appended there during the scope of this import.
		  $this_param["filename"] = substr($thistarget, 0, $splitloc);
		  $this_param["importantparams"] = substr($thistarget, $splitloc+1);
			$this_param["paramarray"] = preg_split('/\s*&\s*/', trim($this_param["importantparams"]));
			$this_param["nameisregex"] = false;
			
			$importantURLParams[] = $this_param["filename"];
			$profile->importantURLParams[] = $this_param;
		}
	}
	
	if ($regexURLParamMatch > "") {
		$regexURLParamMatch = "/".substr($regexURLParamMatch, 0, -1)."/";
	}
}

/**
* Move some parameters from the parameter list to the url list, if 
* appropriate.
* @param string filename The base filename we're testing - this is passed by reference so it's editable
* @param string params The list of parameters - this is pased by reference
* @param array interestingParams The list of parameters we care about, in array form.
*/ 	
function moveParamsToUrl(&$filename, &$params, $interestingParams) {
	$paramlist = preg_split('/\s*[\&\?]\s*/', $params);
	$movedparams = array();
	$stayparams = array();
	
	for ($param_loop = 0; $param_loop < count($paramlist); $param_loop++) {
		$this_param =& $paramlist[$param_loop];
		if ($this_param) {
			// First, try it with the whole parameter=value string, in case the important parameter requires a value.
			$foundloc = array_search($this_param, $interestingParams);
			if ($foundloc === false) {
				$foundloc = $foundloc; // Just for debugging purposes.
			}
			// If it doesn't match with the =value there, take that out and look for a just a name match.
			if ($foundloc === false) {
				$param_data = explode("=", $this_param);
				$foundloc = array_search($param_data[0], $interestingParams);
			}
			if (!($foundloc === false)) {
				// We want to sort the parameters based on the order in $interestingParams, so stick it in like this.
				$movedparams[$foundloc] =& $this_param;
			} else {
				$stayparams[] =& $this_param;
			}
		}
	}
	
	if (count($movedparams) > 0) {
		// Need to sort the moved parameter list to match what's in the $interestingParams array.
		ksort($movedparams, SORT_NUMERIC);
		$movedparams = array_values($movedparams);
		
		$filename = $filename . "?" . implode("&", $movedparams);
		if (count($stayparams) > 0) {
			$params = "&" . implode("&", $stayparams);
		} else {
			$params = "";
		}
		return true;
	}
	return false;
}

/**
* Check to see if the passed in filename and parameters are important.  If so, then
* rewrite and return the new values *through the reference parameters*.
* @param string filename The filename part of the url
* @param string params The parameters for this url
*/ 	
function checkURLandParamsForImportant(&$filename, &$params) {
	global $profile;
	global $importantURLParams;
	
	// If we have important names, then let's try and do some parsing.
	if ($importantURLParams) {
		if (!(($found_index = array_search($filename, $importantURLParams)) === false)) {
			$this_paramurl = $profile->getUrlParamByIndex($found_index);
			return moveParamsToUrl($filename, $params, $this_paramurl["paramarray"]);
		}
	}
	return false;
}

$cache_limit=5000;

function FillCache($table) {
    global $db,$profile,$orist,$cache_limit,$debug;
    $prebyte=memory_get_usage();
    $start=getmicrotime();
    $CacheArray = getProfileData($profile->profilename,$profile->tablename."_$table"."_cachearray","");
    if ($CacheArray=="") {
        $CacheArray = array();
        if ($table=="titles") {
            // titles is not actually a table, so we're faking it
            $field="url";
            $query = "select MD5(title) as id, $field from ".$profile->tablename_urls." where title!='' limit $cache_limit";
        } else {
            $field = getField($table);
            $query = "select id, $field from ".$profile->tablename."_$table limit $cache_limit";    
        }    
        $q= $db->Execute($query);
        while ($data = $q->FetchRow()) {
          $CacheArray[md5($data[$field])]=$data['id'];
        }
    } else {
        $CacheArray=unserialize($CacheArray);
    }
    $took = getmicrotime() - $start;
    $usebyte=(memory_get_usage()-$prebyte);
	echoDebug("filled $table array with ".count($CacheArray)." items, used " . $usebyte ." bytes / ". ($usebyte/1024/1024). " MB (That took $took seconds)<br>");
    return $CacheArray;
}

function FillUserAgentCache() {
    global $db,$profile,$orist,$cache_limit,$debug;
    $prebyte=memory_get_usage();
    $str = array();
    $from = $orist - (86400 *3);
    $query  = "select id, useragent AS name from {$profile->tablename->useragents} limit {$cache_limit}";
    // $query  = "select id, name from ".TBL_USER_AGENTS." limit $cache_limit";
    //$query  = "select r.name,$profile->tablename.useragentid as id, count(distinct visitorid) as visitors from $profile->tablename, ".TBL_USER_AGENTS." as r where timestamp >=$from and r.id=$profile->tablename.useragentid group by $profile->tablename.useragentid order by visitors desc limit $cache_limit";
    //echo $query;
    $q= $db->Execute($query);
    while ($data = $q->FetchRow()) {
      $str[$data['name']]=$data['id'];
    }
    //print_r ($str);
    $usebyte=(memory_get_usage()-$prebyte);                        
    echoDebug("filled useragent array with ".count($str)." items, used " . $usebyte ." bytes / ". ($usebyte/1024/1024). " MB<br>");
    return $str;
}

function analyzeThis($tfile2) {
	global $profile;
	global $db;
	global $databasedriver;
	global $importantURLParams;
	global $lines, $st, $seektimestamp, $month, $httpget, $newlines, $logtime, $lastpos, $firstlogline,$ttlp,$geo,$debug, $skipfile,$logtimestamp;
	global $orist, $user_agents;
	global $equivalent_domains, $equivalent_domains_regex;
	global $skipip, $skipip_regex;
    global $startpos,$totbytes;
    global $urlparamfilter;
    global $linemethod,$mygetstime,$skipfiles;
    global $force, $first_ever_time, $last_inserted_time;
    global $zmode;
    global $ipnumber;
    global $running_from_command_line;
    global $timetracking;
    global $cache_limit;
    $timetracking = Array();
    $stopwatch = Array();	
    $Jan=1;
    $Feb=2;
    $Mar=3;
    $Apr=4;
    $May=5;
    $Jun=6;
    $Jul=7;
    $Aug=8;
    $Sep=9;
    $Oct=10;
    $Nov=11;
    $Dec=12;
    
    if ($debug) { $start_record_count = GetRecordCounts(); }
    // this determines if the 'select / insert' or 'insert on duplicate key update' method is used for inserting and retrieving id's
    $m = getProfileData($profile->profilename,$profile->profilename."_update_method","duplicate");
    if ($debug) {
        if ($m=="duplicate") { echoNotice("Update method is set to duplicate"); } else { echoNotice("Update method is select / insert");}
    }
    $sessioncounter=getProfileData($profile->profilename, "$profile->profilename.sessioncounter", 0);
    // if sessioncounter is 0, we need to get the higest number from the db and store it (this should only happen once)
    if ($sessioncounter==0) {
        $sq = $db->Execute("select max(sessionid) scount from $profile->tablename");
        $sqd = $sq->FetchRow();
        $sessioncounter=$sqd['scount'];
        if ($sessioncounter==0) { $sessioncounter=1; }
        setProfileData($profile->profilename, "$profile->profilename.sessioncounter", $sessioncounter);            
    }
	$stopwatch['initialize'] = getmicrotime();
    // uncomment for Shared Memory support
    //geoip_load_shared_mem("components/geoip/GeoLiteCity.dat");
    //$gi = geoip_open("components/geoip/GeoLiteCity.dat",GEOIP_SHARED_MEMORY);
    if ($geo) {
		include("components/geoip/open_geoip.php");

    }
    

    // see how much memory we can play with
    //convert to bytes and substract 10% to avoid going over limit
    $totmem=substr(ini_get('memory_limit'),0,-1);
    //echo "total memory is" .$totmem; 
    if (!$totmem) {
        $totmem=(8*1024*1024);    
    } else {
        if ($totmem > 14) {
            $fillcache=1;   
        }
        if ($totmem > 30) {
            // if total memory is more than 30, we can use a bigger cache
            //$cache_limit=20000;    
        }
        $totmem=($totmem*1024*1024);
    }
    $totmem=intval(($totmem*0.9));
    
    echoDebug("<br>"._TOTAL_MEMORY_IS." ".$totmem."<br>");

	if (!$st) {
		$st=$seektimestamp;
	}
	
    //this was moved from inside loop, it might cause bugs ...
    if (!@$orist) {
         //$orist = $logtimestamp;
         $orist = $st;
    }
    
	$timestamp=time();
	
    //file chopping:
    //if it's a fresh import, we may want to chop the file into smaller pieces
    /*
    if (!$firstlogline) {
        //let see how big the file is
        $size=filesize($tfile2);
        if ($size > 100777216) {
            //it's over 100 MB, let's chop, 
        }
    }
    */
    if ($profile->trackermode==1) {
        $trackertable=$profile->tablename_trackerlog;
        
        //if ($profile->profilename=="keywordspycom" || $profile->profilename=="keywordspycouk") {
        //    $logfile = $db->Execute("select * from $trackertable order by id");    
        //} else{
            $logfile = $db->Execute("select * from $trackertable");   
        //}
        $linemethod="sql";
        $zmode=""; 
	} else if (substr($tfile2,-3,3)==".gz") {
		$logfile = gzopen($tfile2, "r");
        if (!$totbytes) { $size=filesize($tfile2); } else { $size=$totbytes; } 
		$zmode="gz";
	} else {
		$logfile = fopen($tfile2, "r");
        $size=filesize($tfile2); 
		$zmode="";
	}
    //echo "zmode is $zmode<br>";
    if ($running_from_command_line==true) {
        echoConsoleSafe("[=======================25=======================50=======================75======================100]%\n ",true);    
    }
	$start=getmicrotime();
	if ($logfile!=FALSE) {
		echoConsoleSafe("<script type='text/javascript'>pstatus('"._ANALYZING."...')</script>");
		
		$i=0;
		$one=intval(($lines/100)*1);
		$logparsertype = getProfileData($profile->profilename,$profile->profilename.".logparsertype"  ,false);
		if ($profile->trackermode!=1) {
			$log_format = formatOfLogFile($tfile2);
            if($logparsertype != false && $logparsertype != "auto"){
				$log_format["ClassName"] = $logparsertype;
				if($log_format != $logparsertype){
					echoWarning("Log format and log parse type do not match!");
					echoConsoleSafe("<script type='text/javascript'>
						var answer = confirm('Log format and log parse type do not match! Stop the script now?');
						if (answer){
							if(document.all){
								document.execCommand('Stop');
							}else{
								window.stop();
							}
						}
					</script>");
				}	
			}			
			if (!$log_format) { 
				echoConsoleSafe(_FILE.": ".$tfile2." "._IS_OF_UNKNOWN_TYPE_SKIPPING."<br>");
				gzclose($logfile);
				return false;
			}
			
			// Create a parser.
			$log_parser = new $log_format["ClassName"];
			
			// Initialize it, pass it the log file we currently have optn.
			$log_parser->Initialize($logfile);
			
			//check if the log file is still the same file and if so move to lastpos
			$flogline=mygets($logfile,4096,$zmode); 
			//echo "<P>First logline:".md5($flogline)."<p>";
			//echo "<P>Old First logline: $firstlogline<p>";
			//compare it to the first line we know 
					
			if ($firstlogline==md5($flogline)) {
				myseek($logfile,$lastpos);
				//echo "moving to last known position $lastpos";
				echoConsoleSafe("<script type='text/javascript'>pstatus('"._MOVING_TO_START_POSITION."...')</script>");
				//echo "smartseek enabled";
			} else {
				myseek($logfile,0);
				echoConsoleSafe("<script type='text/javascript'>pstatus('"._ANALYZING_FILE."...')</script>");
				//echo "new first log line:". md5($flogline);
				$firstlogline=md5($flogline);
			}
		} else {
			$log_format["ClassName"]="ApacheCombinedCookieLogParser";
            $log_parser = new $log_format["ClassName"];
            $log_parser->Initialize($logfile);
        }
        //echoConsoleSafe($log_format["ClassName"]);
		lgflush();
		$timer=time();
		$preskip=0; // this is a speed optimization
		$weskippedsome=0;
		$known = array();
        $sessions = array();
        $last_request = array();  
		$kn = 0;
        $sesmem=0;
		$skip=0;
		$lastpbar = 0;
		$inserts=0;
		$insert="";
		
		//pre fill cache arrays        
        $startctime=time();
        
        if (@$fillcache==1) {
            //echoDebug("filling caches");
            echoConsoleSafe("<script type='text/javascript'>pstatus('"._PREPARING_CACHE."...                                                                                                                     ')</script>");
            //echo "<br>                                                                                                perparing cache ...";
            
            lgflush();
            $url_id = FillCache("urls");
		    $urlparams_id = FillCache("urlparams");
		    $referrers_id = FillCache("referrers");
		    $refparams_id = FillCache("refparams");
		    $keywords_id = FillCache("keyword");
		    $user_agents = FillCache("useragents");
            $titles_id = FillCache("titles");
            //echo "cacheing took". (time()-$startctime) . "<br>"; 
		}
        
        
        
		if ($databasedriver == "mysql") {
			//@$db->Execute("lock tables $profile->tablename write, ".TBL_GLOBAL_SETTINGS." write, ".TBL_USER_AGENTS." write") or echoConsoleSafe($db->ErrorMsg(), true);
			//@$db->Execute("lock tables $profile->tablename write, ".TBL_GLOBAL_SETTINGS." write, ".TBL_USER_AGENTS." write, ".$profile->tablename_urls." write, ".$profile->tablename_urlparams." write,".$profile->tablename_referrers." write, ".$profile->tablename_refparams." write, ".$profile->tablename_keywords." write") or echoConsoleSafe($db->ErrorMsg(), true);
             //$db->Execute("lock table $profile->tablename write") or echoConsoleSafe($db->ErrorMsg(), true); 
		} else {
			$db->Execute("Begin Transaction");
		}
		
		if ($databasedriver == "sqlite") {
			$maxinsertcount = 1;
		} else {
		    if ($debug) {
                $q=@$db->Execute("show variables where Variable_name='bulk_insert_buffer_size'");
                if ($result = $q->FetchRow()) {
                echo "<br>bulk_insert_buffer_size=".$result[1];
                }
                $q=@$db->Execute("show variables where Variable_name='max_allowed_packet'");
                if ($result = $q->FetchRow()) {
                echo "<br>max_allowed_packet=".$result[1];
                }   
                $q=@$db->Execute("show variables where Variable_name='key_buffer_size'");
                if ($result = $q->FetchRow()) {
                echo "<br>key_buffer_size=".$result[1];
                }
            }
            //$maxinsertcount = 50000;
            //$maxinsertcount = 12000; //should be about 1 mb, takes about 3.5 sec
            //$maxinsertcount = 8000; // takes about 2.5 sec
            //$maxinsertcount = 10000; // takes about 2.9 sec
            //$maxinsertcount = 5000; // takes about 1.42 sec
            $maxinsertcount = 1000; // for testing
            
		}
		
		// Check and see if we support prepared queries.
		//$insertstart = "INSERT into $profile->tablename (ipnumber,visitorid,timestamp,url,params,status,bytes,referrer,refparams,useragentid,keywords,crawl,country, authuser, sessionid) values ";
        $insertstart = "INSERT into $profile->tablename (visitorid,timestamp,url,params,status,bytes,referrer,refparams,useragentid,keywords,crawl,country,sessionid) values ";
		$insert = $insertstart . " (?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$preparedquery = $db->prepare($insert);
		if ($preparedquery == $insert) {
			$preparedquery = false;
		}
		
		$insert = "";
        lgflush();
		// analyze a line
        @$timetracking['initialize']+=(getmicrotime()-$stopwatch['initialize']);
        $timetracking['getnextline']=0; 
        
        echoDebug("Entering loop");
                
		while ($logline=mygets($logfile,4096,$zmode)) {
            $i++;
            $stopwatch['endnextline'] = getmicrotime(); 
            if (isset($stopwatch['getnextline']) && (($stopwatch['endnextline']-$stopwatch['getnextline'])>0.005)) {
                $timetracking['getnextline']+=($stopwatch['endnextline']-$stopwatch['getnextline']);
            }
            $stopwatch['parsenskiptime']=getmicrotime(); 
            //if ($debug) { echo "<pre>$logline</pre>"; }
            //parse a log line into elemets with log parser
            if (!$log_parser->ParseLine($logline)) {
        
                if (@$log_parse_errors==1) {
                $upfile="files/".$profile->profilename.".unparseable.log";
                $upfp = fopen ($upfile,"a");
                fwrite ($upfp, $log_parser->lasterrormessage. "\n");
                fclose ($upfp);
                //echoConsoleSafe($log_parser->lasterrormessage);
                echoConsoleSafe("<script type='text/javascript'>pstatus('"._UNPARSEABLE_LOG_LINES_WRITTEN_TO." ".$upfile."...')</script>");
                }
				continue;
			}
			
			countBandwidth($log_parser->bytes,$log_parser->logdate);
            
			// The last line might not be an invalid line, but it might be something we need to ignore.
			if ($log_parser->lastlineisdata===false) {				
                continue;
			}
            if ($debug) { echo "<pre>$logline</pre>"; }
            
            // decode the url
            // explode is fastest way - tested strpos, substr, parseurl    
            $urlparts=explode("?", $log_parser->reqfile);            
            $noparamsurl=$url=$urlparts[0];
            $path_parts = pathinfo($noparamsurl);
            $ext=".".@$path_parts['extension'];
            
            // skip files and extentions
            if ($url=="") {
                $url="/";   
            }
            foreach ($skipfile as $val) {
              if ($val) {
                  if (strtolower($ext)==strtolower ($val)) {
                        //echo "skipped extension $ext";
                        continue 2;
                  }
                  if ($url==$val) {
                        //echo "skipped exact file match";
                        continue 2;
                  }
                  if (strpos(" ".$url, $val)!=FALSE) {
                        //echo "skipped partial file match ($url / $val)";
                        continue 2;
                  }
              }
              
            }            
            
            //note: slow line, this one line takes a half second in test of 50 sec total
            $logtimestamp = $log_parser->logdate;
            
            // when force is true do this so we can figure out which part of the summary table to recreate later on
            if ($force==true && $i==1) {
                if (!isset($first_ever_time)) {
                    $first_ever_time=$logtimestamp;  
                } else if ($first_ever_time > $logtimestamp) {
                    $first_ever_time=$logtimestamp;   
                }
                echo _FIRST_TIMESTAMP_FOUND_IS." $first_ever_time ".date("Y-m-d H:i:s",$first_ever_time);
            }
            
            // If a timezone correction has been entered in the profile, apply it now.    //1= i s speed opti        
            if ($profile->timezonecorrection != 0) {
                  $logtimestamp += ($profile->timezonecorrection * 3600);
            }
            
            //skip if already done (occurs less often than file extensions, so is listed second not firts to skip on)
			if ($st > $logtimestamp) {                
                
				$pbar= intval(($i/$lines)*100); //= percent where we are now
                
				// print every 1 percent 1 bar
				if ($i==$one || ($pbar-$lastpbar)==1) {
                    if ($pbar > 100) {
                        $pbar=100;
                    }
                    if ($running_from_command_line==true) {
                        echoConsoleSafe("|",true);
                    } else {
					    echoConsoleSafe("<script type='text/javascript'>pbar($pbar)</script>");
					}
                    //echoConsoleSafe("<script type='text/javascript'>pstatus('Skipping lines (already done)')</script>");
					$weskippedsome=1;
					$lastpbar=$pbar;
					if ($pbar==25 || $pbar==50 || $pbar==75) {
						//write an intermediate progress marker in case the process fails
						if ($profile->trackermode!=1) {
                            echoConsoleSafe("<script type='text/javascript'>pstatus('"._PROGRESS_SAVED."...')</script>");
					        $lastpos=mytell($logfile);
					      
						    //echo "Ending, $firstlogline, $lastpos";
						    //write lastpos to disk
						    setProfileData($profile->profilename, "lastlogpos.".md5($tfile2), $lastpos);
						    setProfileData($profile->profilename, "firstlogline.".md5($tfile2), $firstlogline);
                        }
					}
                    $timerstop=time();
                    if ($timerstop > $timer) {
                        $avg=@intval(($i-$lasti)/($timerstop-$timer));
                        $timeleft=@intval((($lines-$i)/$avg)/60); // in minutes
                        if (memory_get_usage()==0) {
                        //if (0==0) { 
                            $usingmem= "";
                        } else {
                            $usingmem = _PHP_NOW_USING." ".number_format((memory_get_usage()/1024/1024),2)." MB"; 
                        }
                        echoConsoleSafe("<script type='text/javascript'>pstatus('"._SKIPPING_LINES.", "._ALREADY_DONE."... ($avg "._LINES." p/s $usingmem)')</script>");
                                                
                        $lasti=$i;
                        $timer=time();
                    }
				}
                
                // try to speed skip a whole chunk
                
                if (@$size > 12000000) {
                    if (!@$dontmove) {
                        $skipchunk=round(($size/20),0);
                        $curpos=mytell($logfile);
                        $moveto=$curpos+$skipchunk;
                        if (($moveto < $size) && (($st-$logtimestamp) > 172800)) {  
                            if (myseek($logfile,$moveto)=="-1") {
                                //echo "we could not move : size=$size, fseek($logfile,$moveto)";   
                            } else {
                                $logline=mygets($logfile,4096,$zmode); // do this to dump the first line (it may not start at the beginning)                      
                                $previ=$i;
                                // where are we now in terms of % through the file
                                $atperc = $moveto / $size;
                                $i = floor(($lines * $atperc));
                                if ($debug) { echo "we are now at line $i (".($atperc*100)."%) at position $moveto out of size $size<p>"; }
                                
                                $lastpbar= intval(($i/$lines)*100);
                                $cmb=number_format(($moveto/1024/1024),0);
                                echoConsoleSafe("<script type='text/javascript'>pstatus('Fast forward to $cmb mb ...')</script>");
                                lgflush(); 
                                $wemoved=1;
                                $lastmove=$curpos;
                            } 
                        }                             
                    }
                }
				lgflush();
				continue;
			} else if (@$wemoved==1) {
                myseek($logfile,$lastmove);
                $i=$previ; 
                if ($debug) { echo "we moved back to position $lastmove to line $i....<br>"; }
                $wemoved=0;
                $dontmove=1;
                echoConsoleSafe("<script type='text/javascript'>pstatus('Seeking ...')</script>");
                lgflush();
            }
            
            @$timetracking['parsenskiptime']+=getmicrotime()-$stopwatch['parsenskiptime'];               
            $stopwatch['purephptime']=getmicrotime();
			// Is this an IP address we need to skip?
			$ipnumber=$log_parser->clientip;
            //echo "<br>".$ipnumber."<br>";
            if (!is_numeric(substr($ipnumber,-1))) {
             $ipnumber  = gethostbyname($ipnumber);
             if (!is_numeric(substr($ipnumber,-1))) {
                $ipnumber  = "0.0.0.0";
             }
            }
			// skip ipnumbers
			if (in_array($ipnumber, $skipip)) {
				//$skip=1;
                continue;
			} else {
				// Check to see if we have a regex match.
				foreach ($skipip_regex as $thisregex) {
					if (preg_match("/".$thisregex."/i", $ipnumber)) {
						//$skip = 1;
						continue 2;
					}
				}
			}
           //end of main skipping part		
             
			//decode url was here - decode rest of url
			$urlhasimportantparams = false;
			if (isset($urlparts[1])) { 
				//$urlparams="?".$urlparts[1];
                
                //this is the skipping params part
                if ($profile->urlparamfilter!="") {
                    $urlparams="?";
                    //see if we need to strip anything in this string
                    parse_str($urlparts[1], $up_array);
                    while (list ($key, $val) = each ($up_array)) {
                        if ($profile->urlparamfiltermode=="Include") {
                            // this will include selected and delete all others
                            if (in_array($key, $urlparamfilter)) {
                                $urlparams.= "$key=$val&";
                            }
                        } else if (!in_array($key, $urlparamfilter)) {
                            $urlparams.= "$key=$val&";
                        }
                    }
                    $urlparams=substr($urlparams,0,-1);  
                } else {
                    $urlparams="?".$urlparts[1];
                }            
                
				// Check the url parameters and url parts to see if there are any "important" ones that need to be
				// spliced back together.  $url and $urlparams are passed *by reference* to they will automatically
				// be returned with new values, if they need to be.
				if ($importantURLParams) {
					$urlhasimportantparams = checkURLandParamsForImportant($url, $urlparams);
				}
			} else {
				$urlparams="";
			}

			//skip extension was here
			// If we rewrote the URL with parameters, then let's check to see if the *base* url was excluded (with no params)
			//I don't think we need this anymore
            /*
            if (($urlhasimportantparams) && in_array($noparamsurl, $skipfile)) {
				$i++;
				continue;
			}
            */
            
            $status = $log_parser->status;
			$bytes = $log_parser->bytes;
			$referrer = $log_parser->referrer;
            
			
			if ($referrer=="") { $referrer="-"; }
			
			// Rewrite the google syndication links.			
			if ((strpos($referrer, "googlesyndication")!=FALSE) || (strpos($referrer, "googleads")!=FALSE)) {
				$gsyn=@explode("&url=", $referrer);
				$gsyn=@explode("&", $gsyn[1]);
				$gsyn=@urldecode($gsyn[0]);
				if ($gsyn!="") {
                    $referrer="[G]".$gsyn;
                } else {
                    $referrer="[G]".$referrer;   
                }
			}
            
			$refparts=explode("?", $referrer);
			$ref=$refparts[0];
			if (isset($refparts[1])) {
                //if it's google, clean the url
                if (strpos($ref,"www.google.")!==false) {
                    $refparts[1] = CleanGoogleUrls($refparts[1]);    
                }                
                
                //this is the skipping params part, but only for local referrers
                if (strpos($ref, $profile->confdomain)!==FALSE || $profile->profilename=="keywordspycom") {
                    if ($profile->urlparamfilter!="") {
                        $refparams="?";
                        //see if we need to strip anything in this string
                        parse_str($refparts[1], $up_array);
                        while (list ($key, $val) = each ($up_array)) {
                            if ($profile->urlparamfiltermode=="Include") {
                                // this will include selected and delete all others
                                if (in_array($key, $urlparamfilter)) {
                                    $refparams.= "$key=$val&";
                                }
                            } else if (!in_array($key, $urlparamfilter)) {
                                $refparams.= "$key=$val&";
                            }
                        }
                        $refparams=substr($refparams,0,-1);
                    } else {
                        $refparams="?".$refparts[1];
                    }  
                } else {
                    $refparams="?".$refparts[1];
                }      
                
                
                /*
                //$urlparams="?".$urlparts[1];
                if ($include_refparams!="") {
                    $refparams="?";
                    //see if we need to strip anything in this string
                    parse_str($refparts[1], $up_array);
                    while (list ($key, $val) = each ($up_array)) {
                        if ($include_urlparams!="") {
                            if (in_array($key, $include_urlparams)) {
                                if (strlen($refparams) > 1) {
                                    $refparams.= "&";
                                }
                                $refparams.= "$key=$val";
                            }
                        } else if (!in_array($key, $skip_urlparams)) {
                            if (strlen($urlparams) > 1) {
                                $refparams.= "&";
                            }
                            $refparams.= "$key=$val";
                        }
                    }
                } else {
                   $refparams="?".$refparts[1];
                }
                */
			} else {
				$refparams="";
			}
			$refparams = urldecode($refparams);
	
			// Let's extract out the parts of the referrer and see if we need to rewrite the domain.
			//$ref_domain = strtolower(parse_url($ref, PHP_URL_HOST)); // the constant is causing error's, parse_url only supports one parameter
			$ref_domain = @parse_url($ref);
            $ref_domain= strtolower(@$ref_domain['host']);
            
			// Now, let's see if we need to modify the referrer domain to make it match our primary domain.
			$isEquivalent = false;
			if ($ref_domain > "") {
				if (in_array($ref_domain, $equivalent_domains)) {
					$isEquivalent = true;
				} else {
					foreach ($equivalent_domains_regex as $thisregex) {
						if (preg_match("/".$thisregex."/i", $ref_domain)) {
							$isEquivalent = true;
							break;
						}
					}
				}
			}
			
			if ($isEquivalent) {
				$foundloc = strpos(strtolower($ref), $ref_domain);
				// Pretty much HAS to be found - this string was parsed out of $ref
				if (!($foundloc === false)) {
					$ref = substr($ref, 0, $foundloc) . $profile->confdomain . substr($ref, $foundloc + strlen($ref_domain));
				}
				$ref_domain = $profile->confdomain;
			}
            $keywords="";
			// Do we need to move any parameters to the main page name for the referrer?  This is only applicable to pages
			// on our own site.
			if ($ref_domain == strtolower($profile->confdomain)) {
				// Check the referrer parameters and url parts to see if there are any "important" ones that need to be
				// spliced back together.  $url and $urlparams are passed *by reference* to they will automatically
				// be returned with new values, if they need to be.
				if ($importantURLParams) {
					$domain_loc = strpos($ref, $profile->confdomain);
					
					$basepart_ref = substr($ref, 0, $domain_loc + strlen($profile->confdomain));
					$pagepart_ref = substr($ref, $domain_loc + strlen($profile->confdomain));
					if (checkURLandParamsForImportant($pagepart_ref, $refparams)) {
						$ref = $basepart_ref . $pagepart_ref;
					}
				}
			} else {
				
				//decode keywords
				if ($refparams > "") {
					if (preg_match('/[?&]q=([^&]*)/', $referrer, $matched)) {
						$keywords = trim(strtolower(urldecode($matched[1])));
                        if(strpos($referrer, "www.google") !== FALSE && empty($keywords)){
							$keywords = "(Not Provided)";
						}                         
					}
			
					  
					# Yahoo uses p= instead of q=.
					if (strpos($referrer, "search.yahoo")!=FALSE) {
						if (preg_match('/[?&]p=([^&]*)/', $refparams, $matched)) {
							$keywords = trim(strtolower(urldecode($matched[1])));
                            // $keywords = str_replace(" ", "+", $keywords); 
						}
					}
					                
				}
                
				if (strpos($keywords, "ache:")!=FALSE) {
				 $keywords="";
				}

			}
			           
            // Set up the visitor id  (using original useragent)
            $log_parser->agent = explode("\" \"",$log_parser->agent); // this is prevent entries after the agent field to be included
            $log_parser->agent = $log_parser->agent[0];
            if (strpos(" ".$ipnumber, "65.55.")!=FALSE) {   // this this the ip range of the new livebot
                 $ipnumber="65.55.165.12";  //just set it to one arbitratry host
            }
                        
            if ($profile->visitoridentmethod == VIDM_IPADDRESS) {
                $visitorid = md5($ipnumber);
            } else if ($profile->visitoridentmethod == VIDM_IPPLUSAGENT) {
                $visitorid = md5($ipnumber . ':' . $log_parser->agent);
            } else if ($profile->visitoridentmethod == VIDM_COOKIE) {
                // if we have a cookie, use it, else use VIDM_IPPLUSAGENT
                $cookies = $log_parser->cookie.";";
                $thisLogaholic_VID=strpos($cookies, "Logaholic_VID");
                if ($thisLogaholic_VID!==FALSE) {
                    $thisLogaholic_VID=substr($cookies,$thisLogaholic_VID);
                    $thisLogaholic_VID=substr($thisLogaholic_VID,14,strpos($thisLogaholic_VID, ";")-14);
                    if (is_numeric($thisLogaholic_VID)==false) {
                        $thisLogaholic_VID = md5($ipnumber . ':' . $log_parser->agent);                                   
                    } else {
						$thisLogaholicVID = md5($thisLogaholicVID);
					}
                    $visitorid=$thisLogaholic_VID;
                    if ($debug) {
                        echo "we are using visitorid: $thisLogaholic_VID for $ipnumber<br>";
                        echo "a new logaholic vid would look like this:".md5($ipnumber . ':' . $log_parser->agent);
                        echo "<br>that is based on :".$ipnumber . ':' . $log_parser->agent;
                        echo "<br>this is the cookie string :" . $cookies;
                    }
                } else {
                    $visitorid = md5($ipnumber . ':' . $log_parser->agent);
                    
                }
            }
            //echoDebug($visitorid);
            $stoptime=getmicrotime();
            @$timetracking['purephptime_all']+=$stoptime-$stopwatch['purephptime'];
                             
            $stopwatch['id_lookup']=getmicrotime();
            $stopwatch['sessiontime']=getmicrotime();
            //check the session data
            $session_timeout=($profile->visittimeout * 60); // 20 minutes default
            //$starttime=getmicrotime();
            if (@array_key_exists($visitorid, @$sessions)) {
                    $time_elapsed=$logtimestamp - $sessions[$visitorid][1];
                    if ($time_elapsed > $session_timeout) {
                        //create a new sessionid
                        $sessionid=md5($logtimestamp.$visitorid);
                        //$last_request[$visitorid]=$logtimestamp;
                        $sessions[$visitorid][0]=$sessionid; 
                        $sessions[$visitorid][1]=$logtimestamp;
                        
						//$db->Execute("Insert into ".$profile->tablename_sessionids." (sessionid) values ('$sessionid')");
                        //$sessions[$visitorid][2]= $db->Insert_ID();
                        $sessions[$visitorid][2]= $sessioncounter++;
						//$inserted_sessionid_indatabase++;
                        //$db->Execute("delete from ".$profile->tablename_sessionids);
                        
                    } else {
                        //use the existing sessionid
                        //echo "using existing session.";
                        $sessionid=$sessions[$visitorid][0];
                        $sessions[$visitorid][1]=$logtimestamp;
                    }
                    //$stoptime=getmicrotime();
                    //@$sessionfindtime=$sessionfindtime+($stoptime-$starttime);
                    //@$foundsession++;
            } else {
                
                //create a new sessionid  
                $sessionid=md5($logtimestamp.$visitorid);
                $sessions[$visitorid][0]=$sessionid;
                $sessions[$visitorid][1]=$logtimestamp; 
                // 2 is for the db sessionid
                //$db->Execute("Insert into ".$profile->tablename_sessionids." (sessionid) values ('$sessionid')");
                //$sessions[$visitorid][2]= $db->Insert_ID();
                //$inserted_sessionid_indatabase++;
                //$db->Execute("delete from ".$profile->tablename_sessionids); 
				$sessions[$visitorid][2]= $sessioncounter++;
                $stopwatch['geotime']=getmicrotime();       
                //get the country code
                
                if ($geo && (@$sessions[$visitorid][3]=="")) {
                    $sessions[$visitorid][3]=geoip_country_code_by_addr_raw($gi, $ipnumber);
                    @$geoq++;
                }
				$stoptime=getmicrotime();
                @$timetracking['geotime']+=$stoptime-$stopwatch['geotime'];
                //set up the useragent and crawler status
                //echo $log_parser->agent."<br>";
                //$useragent = str_replace("+", " ", $log_parser->agent); 
                $stopwatch['useragenttime']=getmicrotime(); 
                               
                $useragent = $log_parser->agent;                 
                $crawl=0;
                //find out if it's a bot
				
				$human_agent = get_useragent($useragent);
				
				// $ua_array = browser_detection('full_assoc', '', $useragent);
				
				if($human_agent['agent_is_bot'] == 1) {
					$sessions[$visitorid][4] = 1;
					if(!isset($DetectBot)) {
						$DetectBot = 1;
					} else {
						$DetectBot++;
					}
				} else {
					$sessions[$visitorid][4] = 0;
				}
				
				if($human_agent['agent_is_mobile'] == 1) {
					$ismobile = 1;
				} else {
					$ismobile = 0;
					// continue;
					if(!isset($DetectBrowserOS)) {
						$DetectBrowserOS = 1;
					} else {
						$DetectBrowserOS++;
					}
				}
				// continue;
				
				$useragent = substr(strip_tags(trim($useragent)), 0, 255);
				
				// if ($sessions[$visitorid][4]==0) {
					// echo "<pre>".strip_tags($log_parser->agent)."</pre>";
					// echo "$useragent<hr>";
				// }
				
				/*
                $sessions[$visitorid][4] = DetectBot($useragent);
                @$DetectBot++;
                
                // return a pretty browsername and OS
                $ismobile=0;
                if ($sessions[$visitorid][4]==0) {
                    if (isMobile($useragent)==true) {
                        $ismobile = 1;   
                    } else {
                        $ismobile=0;
                        $ua = $useragent;
                        // $useragent = DetectBrowserOS($useragent);
                        $new_useragent = newDetectBrowserOS($ua);
                        @$DetectBrowserOS++;
                    }                    
                }
                //do this to prevent query failed (too many charters) (windows problem only ?)
                $useragent=substr(strip_tags(trim($useragent)), 0, 255);
				*/
				
                // Find the useragent id...
				$useragentid = @$user_agents[md5($human_agent['agent_string'])];
				
                if (!isset($useragentid)) {
					// agent_name, agent_version, agent_os, agent_os_version, agent_engine, agent_string, agent_is_bot, agent_is_mobile, agent_device
					
                    // $q = $db->Execute("INSERT INTO ".TBL_USER_AGENTS." (name,ismobile) VALUES (".$db->quote($useragent).", '$ismobile') ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)");
					$q = $db->Execute("INSERT INTO {$profile->tablename_useragents} (name, version, os, os_version, engine, useragent, hash, is_bot, is_mobile, device) VALUES (".$db->Quote(substr($human_agent['agent_name'],0,255)).", '{$human_agent['agent_version']}', '{$human_agent['agent_os']}', '{$human_agent['agent_os_version']}', '{$human_agent['agent_engine']}', '{$human_agent['agent_string']}', '".md5($human_agent['agent_string'])."', '{$human_agent['agent_is_bot']}', '{$human_agent['agent_is_mobile']}', '{$human_agent['agent_device']}') ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)");
                    $useragentid = $db->Insert_ID();
                    $user_agents[md5($human_agent['agent_string'])] = $useragentid;
                } else {
					@$useragentshit[$useragentid]++;
				}
				
                $stoptime=getmicrotime();
                @$timetracking['useragenttime']+=$stoptime-$stopwatch['useragenttime'];
                            
                $sessions[$visitorid][5] = $useragentid;                
                //$stoptime=getmicrotime();
                //$createsessionfindtime=$createsessionfindtime+($stoptime-$starttime);
                @$createsession++;
                $sesmem++;
                
            }
            $stoptime=getmicrotime();
            @$timetracking['sessiontime']+=$stoptime-$stopwatch['sessiontime'];
            
            $sessions_id = $sessions[$visitorid][2];
            $country = @$sessions[$visitorid][3];
            $crawl = $sessions[$visitorid][4];
            $useragentid = $sessions[$visitorid][5];
            if (!$useragentid) {
                echoDebug("No useragentID for: ".$useragent);
                // echoDebug(DetectBrowserOS($useragent));    
            }
            
            // clean the remembered session variables so we don't run out of memory or something
            if (memory_get_usage() > $totmem) {
            //if (0 > $totmem) {
                    $prebyte=memory_get_usage();
                    
                    //just unset the ones that have expired
                    $cses=0;
                    //$cleansessionstart=getmicrotime();
                    while (list ($key, $val) = each ($sessions)) {
                    //foreach($sessions as $ses) {
                        $time_elapsed=$logtimestamp - $val[1];
                        if ($time_elapsed < $session_timeout) {
                            $tempsessions[$key][0]=$val[0];
                            $tempsessions[$key][1]=$val[1];
                            $tempsessions[$key][2]=$val[2];
                            $tempsessions[$key][3]=@$val[3];
                            $tempsessions[$key][4]=$val[4];
                            $tempsessions[$key][5]=$val[5];
                            $cses++;
                        }
                    }
                    $rses=$sesmem-$cses;
                    $sesmem=$cses;
                    //$sessions="";
                    $sessions=$tempsessions;
                    $tempsessions="";
                
                    unset($urlparams_id);
                
                    unset($visitors_ids);

                    unset($refparams_id);
                    
                    unset($keywords_id);
                    
                    unset($authusers); 
                    
                    $freebyte=$prebyte-(memory_get_usage());                        
            }
            // clean the remembered session variables so we don't run out of memory or something
            if (memory_get_usage() == 0) {  // this is the case when we don't have the memory_get_usage function at all 
            //if (0 == 0) {
                if (count($sessions) > 2000) {                    
                    //just unset the ones that have expired
                    $cses=0;
                    //$cleansessionstart=getmicrotime();
                    while (list ($key, $val) = each ($sessions)) {
                    //foreach($sessions as $ses) {
                        $time_elapsed=$logtimestamp - $val[1];
                        if ($time_elapsed < $session_timeout) {
                            $tempsessions[$key][0]=$val[0];
                            $tempsessions[$key][1]=$val[1];
                            $tempsessions[$key][2]=$val[2];
                            $tempsessions[$key][3]=$val[3];
                            $cses++;
                        }
                    }
                    $rses=$sesmem-$cses;
                    $sesmem=$cses;
                    //$sessions="";
                    $sessions=$tempsessions;
                    $tempsessions="";
                    //echo "<br>dumped ols sessions<br>";
                }
                if (count($url_id) > 2000) {
                    //echo "<br>dumped urls<br>";
                    unset($url_id);
                }
                 
                if (count($urlparams_id) > 2000) {
                    //echo "<br>dumped urlparams<br>";
                    unset($urlparams_id);
                }
                                        
                if (count($visitors_ids) > 2000) {
                    //echo "<br>dumped visistorids<br>";
                    unset($visitors_ids);
                }
                                        
                if (count($refparams_id) > 2000) {
                    //echo "<br>dumped refparams<br>";
                    unset($refparams_id);
                }                        
                                        
                if (count($keywords_id) > 2000) {
                    //echo "<br>dumped keywords<br>";
                    unset($keywords_id);
                }
                if (count($authusers) > 500) {
                    //echo "<br>dumped keywords<br>";
                    unset($authusers);
                }
            }            
            
			// find or insert the url id...
            $url=substr($url,0,255);
			$urlid = @$url_id[md5($url)];
			if (!isset($urlid)) {
				$urlid = InsertOrGetID($url,"urls",$m);
                $url_id[md5($url)] = $urlid;
			} else {
                // record the hit on the cache
                @$urlhit[$urlid]++;    
            }
            
            // get the title from the cookie field if it's in there           
            if ($profile->trackermode==1 || $profile->visitoridentmethod == VIDM_COOKIE) {
                $title=getTitleFromCookies($log_parser->cookie);
                if ($title!="") {
                    $titleid = @$titles_id[md5($url)];
                    if (!isset($titleid) || (md5($title)!=$titleid)) {
                        // if we didn't find the title in the cache array, update the table now
                        //echoDebug("we are updating title $title for $url");
                        $db->Execute("update $profile->tablename_urls set title=".$db->quote($title)." where id='$urlid'");
                        $titles_id[md5($url)] = $urlid;
                    } else {
                         @$titlehit[$urlid]++;    
                    }
                }
            }
            
			// find or insert the param id...
            $urlparams=substr($urlparams,0,255);             
            $paramid = @$urlparams_id[md5($urlparams)];
  			if (!isset($paramid)) {
  				$paramid = InsertOrGetID($urlparams,"urlparams",$m);
  				$urlparams_id[md5($urlparams)] = $paramid;
  			} else {
                // record the hit on the cache
                @$urlparamhit[$paramid]++;    
            }			
            
			// find or insert the referrer id...
            $ref=substr($ref,0,255); 

			$referrerid = @$referrers_id[md5($ref)];
			if (!isset($referrerid)) {
				$referrerid = InsertOrGetID($ref,"referrers",$m);
				$referrers_id[md5($ref)] = $referrerid;
			} else {
                // record the hit on the cache
                @$refhit[$referrerid]++;    
            }
			
            // find or insert the refparam id.
            $refparams=substr($refparams,0,255);
            $refparamsid = @$refparams_id[md5($refparams)];
  			if (!isset($refparamsid)) {
                $refparamsid = InsertOrGetID($refparams,"refparams",$m); 
  				$refparams_id[md5($refparams)] = $refparamsid;
            } else {
                // record the hit on the cache
                @$refparamshit[$refparamsid]++;    
            }	
  			
			// find or insert the keyword id.
            $keywords=substr($keywords,0,255);
  			$keywordsid = @$keywords_id[md5($keywords)];
  			if (!isset($keywordsid)) {        	
  			    $keywordsid = InsertOrGetID($keywords,"keyword",$m);
                $keywords_id[md5($keywords)] = $keywordsid;
  			} else {
                // record the hit on the cache
                @$keywordshit[$keywordsid]++;    
            }
            
			$authuser = $log_parser->authuser;
            //echo $authuser;
			if ($authuser == "-") {
				$authuser = NULL;
			}
            
            // find or insert the visitorid, ipnumber, authusers.
            $starttime=getmicrotime();
            $visitors_id = @$visitors_ids[$visitorid];
            if ($authuser) {
                if ($authuser!=@$authusers[$visitorid]) {
                    //force an update
                    $visitors_id= NULL;
                }       
            }
            
            if (!isset($visitors_id)) {
                  $q = $db->Execute("Select id,authuser from ".$profile->tablename_visitorids." where visitorid = ".$db->Quote($visitorid));
                  if ($result = $q->FetchRow()) {
                      $visitors_id = $result[0];
                      //$found_visitorid_indatabase++;
                      if ($authuser && ($authuser!=$result[1])) {
                          //echo "upodate $authuser<br>" . "Update ".$profile->tablename_visitorids." set authuser='$authuser' where visitorid = '".$visitorid."'";
                          $db->Execute("Update ".$profile->tablename_visitorids." set authuser=".$db->Quote($authuser)." where visitorid = ".$db->Quote($visitorid));
                          $authusers[$visitorid]=$authuser;  
                      }
                      //echo "<br>we got visitorid in database:$visitorid";
                      $stoptime=getmicrotime();
                      @$visitorid_db_findtime=$visitorid_db_findtime+($stoptime-$starttime);
                  } else {
                      if (function_exists('encodeSPQR')) { $ipnumber = encodeSPQR($ipnumber); }
                      $db->Execute("Insert into ".$profile->tablename_visitorids." (visitorid,ipnumber,authuser,created) values (".$db->Quote($visitorid).",'$ipnumber',".$db->Quote($authuser).",'$logtimestamp')"); 
                      if ($authuser) {
                          $authusers[$visitorid]=$authuser;  
                      }
                      $visitors_id = $db->Insert_ID();
                        @$inserted_visitorid_indatabase++;
                        $stoptime=getmicrotime();
                        @$visitorid_insert_findtime=$visitorid_insert_findtime+($stoptime-$starttime);
                  }
                  $visitors_ids[$visitorid] = $visitors_id;
            } else {
                  @$found_visitorid_inarray++;
                  $stoptime=getmicrotime();
                  @$visitorid_array_findtime=$visitorid_array_findtime+($stoptime-$starttime);
            }
            $stoptime=getmicrotime();
            @$timetracking['id_lookuptime']+=($stoptime-$stopwatch['id_lookup']);
            		
            $stopwatch['pbartime']=getmicrotime();
            		
            //I don't remember why we do this, put it prevents the first log line to be put in the database:			
			//if ($lines==1) {
			//	 break;
			//} 
			// display the progress bar
			if ($lines) {
				if (!isset($lastpbar)) {$lastpbar=0;}
				$pbar= intval(($i/$lines)*100); //= percent where we are now
				
                // if it takes long, we want more feedback
                if ((time()- $timer) > 15) {
                    $avg=@intval(($i-$lasti)/(time()-$timer));
                    $timeleft=@number_format(((($lines-$i)/$avg)/60/60),2); // in hours
                    
                    echoConsoleSafe("<script type='text/javascript'>pstatus('Analyzing line ".date("d M H:i:s, Y",$logtimestamp)."... (line: ".$i." at $avg lines p/s, about $timeleft hours to go)')</script>");
                    $lasti=$i;
                    $timer=time();   
                }
                
                // print every 1 percent 1 bar
				if ($i==$one || ($pbar-$lastpbar)==1) {
					if ($pbar > 100) {
                         $pbar=100;
                         if (@$stopspittingatme!=1) {
						 echoConsoleSafe("<script type='text/javascript'>pbar($pbar)</script>\n");
						 echoConsoleSafe("<script type='text/javascript'>pstatus('...')</script>\n");
                         }
                         $stopspittingatme=1;
					} else {
                        if ($running_from_command_line==true) {
                            echoConsoleSafe("|",true);
                        } else {
						    echoConsoleSafe("<script type='text/javascript'>pbar($pbar)</script>\n");
                        }
					}
					$lastpbar=$pbar;
					
					if ($weskippedsome ==1) {
						//echo "<script>pstatus('Analyzing ...')</script>\n";
						// we are now analyzing so turn preskip on
						//$preskip=1;
						$weskippedsome=0;
					}
					
					if ($pbar==25 || $pbar==50 || $pbar==75) {
						//write an intermediate progress marker in case the process fails
						if ($profile->trackermode!=1) {
                            echoConsoleSafe("<script type='text/javascript'>pstatus('"._PROGRESS_SAVED."...')</script>\n");
						    $lastpos=mytell($logfile);
						    $weskippedsome=1; /// just to get the message back;
						    //echo "Ending, $firstlogline, $lastpos";
						    //write lastpos to disk
						    setProfileData($profile->profilename, "lastlogpos.".md5($tfile2), $lastpos);
						    setProfileData($profile->profilename, "firstlogline.".md5($tfile2), $firstlogline);
                        }
                        
						if ($db->hasTransactions) {
							$db->Execute("Commit");
							if ($databasedriver == "mysql") {
							 //$db->Execute("Start Transaction");
							} else {
							 $db->Execute("Begin Transaction");
							}
						}
                        
					}
                    
                    $timerstop=time();
                    if ($timerstop > $timer) {
                        $avg=@intval(($i-$lasti)/($timerstop-$timer));
                        $timeleft=@intval((($lines-$i)/$avg)/60); // in minutes
                        if (memory_get_usage()==0) {
                        //if (0==0) { 
                            $usingmem= "";
                        } else {
                            $usingmem = "PHP now using " .  number_format((memory_get_usage()/1024/1024),2). " MB"; 
                        }
                        echoConsoleSafe("<script type='text/javascript'>pstatus('"._ANALYZING." ".date("d M, Y",$logtimestamp)."... ($avg "._LINES." p/s, "._ABOUT." $timeleft "._MIN_TO_GO.") $usingmem')</script>");
                        //echo "PHP now using " . memory_get_usage() ." bytes / ". (memory_get_usage()/1024/1024). " MB, $avg lines p/s<br>";
                        //echo "<font color=silver>" .count($url_id) ." url in cache, ". count($urlparams_id) ." in urlparam cache, ". count($referrers_id) ." in referrers cache, ". count($refparams_id) ." in refparam cache, ". count($keywords_id) ." in keywords cache, ". count($user_agents) ." in useragent cache, ". count($visitors_ids) ." in visitor cache</font><br>";
                        $lasti=$i;
                        $timer=time();
					}
				}
				lgflush();
			}
            $stoptime=getmicrotime();
            @$timetracking['pbartime_all']+=($stoptime-$stopwatch['pbartime']);
                    
			if ($preparedquery) {
				/* //old:
                if (!@$db->Execute($preparedquery, array($ipnumber, $visitorid, $logtimestamp, substr($urlid,0,255), substr($paramid,0,255), $status, $bytes, substr($referrerid,0,255), substr($refparamsid,0,255), $useragentid, $keywordsid, $crawl, $country, $authuser, $sessionid))) {
					$insert = $insertstart . " \n('$ipnumber','$visitorid',$logtimestamp,".$db->Quote(substr($urlid,0,255)).",".$db->Quote(substr($paramid,0,255)).",'$status','$bytes',".$db->Quote(substr($referrerid,0,255)).",".$db->Quote(substr($refparamsid,0,255)).",".$useragentid.",".$db->Quote($keywordsid).",$crawl,".$db->Quote($country).", ".$db->Quote($authuser).", '".$sessionid."')";
					die("Error inserting record: ".$db->ErrorMsg()."<br>".str_replace("\n","<br>",$insert));
				}
                */
                if (!@$db->Execute($preparedquery, array($visitors_id, $logtimestamp, $urlid, $paramid, $status, $bytes, $referrerid, $refparamsid, $useragentid, $keywordsid, $crawl, $country, $sessions_id))) {
                    $insert = $insertstart . " \n('$visitors_id',$logtimestamp,".$db->Quote(substr($urlid,0,255)).",".$db->Quote(substr($paramid,0,255)).",'$status','$bytes',".$db->Quote(substr($referrerid,0,255)).",".$db->Quote(substr($refparamsid,0,255)).",".$useragentid.",".$db->Quote($keywordsid).",$crawl,".$db->Quote($country).", '".$sessions_id."')";
                    die(""._ERROR_INSERTING_RECORD.": ".$db->ErrorMsg()."<br>".str_replace("\n","<br>",$insert));
                }
                
				$newlines= $newlines + 1;
				$inserts = 0;
				
			} else {
				// save up a few inserts to make it go faster
                $starttime=getmicrotime();
				//if ($logtimestamp!="" || $logtimestamp!=0) {
					$insert .= "\n('$visitors_id',$logtimestamp,".$db->Quote(substr($urlid,0,255)).",".$db->Quote(substr($paramid,0,255)).",'$status','$bytes',".$db->Quote(substr($referrerid,0,255)).",".$db->Quote(substr($refparamsid,0,255)).",".$useragentid.",".$db->Quote($keywordsid).",$crawl,".$db->Quote($country).", '".$sessions_id."'),";
				
					$inserts++;
					@$timetracking['qcachetime']+=(getmicrotime()-$starttime);
				//} else {
				//	echo "<pre>"._LOGLINE.": $logline</pre>";
				//}
				
				if ($inserts >= $maxinsertcount) {
					$starttime=getmicrotime();
                    $prebyte= memory_get_usage();
                    $insert= $insertstart . substr($insert,0,-1);
					
                    //fwrite ($importfp, $insert);
                    if (!$db->Execute($insert)) {
						echo "<pre>"._LOGLINE.": $logline</pre>";
                        //die("Error inserting record: ".$db->ErrorMsg()."<br>".str_replace("\n","<br>",$insert));
                        echo(""._ERROR_INSERTING_RECORD.": ".$db->ErrorMsg()."<br>".str_replace("\n","<br>",$insert));
                        exit();
					}
					$insert="";
					$newlines= $newlines + $inserts;
					$inserts=0;
					$freebyte=$prebyte-(memory_get_usage());
                    $stoptime=getmicrotime();
                    @$timetracking['qtime']+=($stoptime-$starttime);
					//echo "Sent a query of " . $freebyte ." bytes / ". ($freebyte/1024/1024). " MB, that took ".($stoptime-$starttime)."<br>";
                    
				}
			}
            $stopwatch['getnextline']= getmicrotime();
		}
        $stopwatch['finalize'] = getmicrotime();
        if ($profile->trackermode!=1) {
            //$linemethod="";
            $lastpos=mytell($logfile);    
            //echo "Ending, $firstlogline, $lastpos";
            //write lastpos to disk
            setProfileData($profile->profilename, "lastlogpos.".md5($tfile2), $lastpos);
            setProfileData($profile->profilename, "firstlogline.".md5($tfile2), $firstlogline);
		    if ($zmode=="gz") {
			    gzclose($logfile);
		    } else {
			    
			    fclose($logfile);
		    }
        } else {
            // clean the table
            $db->Execute("delete from $trackertable");   
        }
		
		// insert any remaining lines
		$insert=substr($insert,0,-1);
		if ((!$preparedquery) && ($insert > "")) {
			$db->Execute($insertstart . $insert);
			$insert="";
			$newlines= $newlines + $inserts;
			$inserts=0;
		}
        //fclose($importfp);

		// free up some memory
		unset($sessions);
		unset($visitors_ids);
		
	} else {
		echo ""._ERROR_CANT_OPEN_OR_FIND_LOG_FILE.": $tfile2";
		//exit();	
	}
	if ($databasedriver == "mysql") {
		//@$db->Execute("unlock tables") or echoConsoleSafe($db->ErrorMsg(), true);
	}
	if ($db->hasTransactions) {
		$db->Execute("Commit");
	}
	if ($geo) {
		geoip_close($gi);
	}
  
  echoConsoleSafe("<script type='text/javascript'>pstatus('"._DONE." ($i "._ACTUAL_LINES_PROCESSED.")')</script>");
  echoConsoleSafe("<script type='text/javascript'>pbar(100); self.document.forms.progress.progbar.value='|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||';</script>");
  lgflush();
  $tlp=@$tlp+($i);
    if ($tlp==-1) {
      $tlp=0;
    }
    $e=getmicrotime();
    $took=($e-$start);
    //echo "<br>real took $took<br>";
    $speed=($tlp/$took);
    $speed=number_format($speed,0);
    $start=getmicrotime();
    if ($took > 120) {
        $took = $took / 60;
        $took=number_format($took,0);
        echoConsoleSafe("\n"._LOG_FILE_PARSING_TOOK." $took "._MINUTES_AVG_SPEED_WAS." $speed "._LINES." p/s ("._PROCESSED." $tlp "._LINES_IN_TOTAL.")<br><br>\n", true);
    } else {
        $took=number_format($took,0);
        echoConsoleSafe("\n"._LOG_FILE_PARSING_TOOK." $took "._SECONDS_AVG_SPEED_WAS." $speed "._LINES." p/s ("._PROCESSED." $tlp "._LINES_IN_TOTAL.")<br><br>\n", true);   
    }
    
    $timetracking['finalize']=(getmicrotime()-$stopwatch['finalize']);
    $timetracking['total']=(getmicrotime()-$stopwatch['initialize']);
    if ($debug || @$_REQUEST['showtime']) {
        showTimeTable($timetracking);
    }
    setProfileData($profile->profilename, "$profile->profilename.sessioncounter", $sessioncounter);
	 
    if ($tlp > 200) {
        //now store the actual number of lines in relation to total byes, so we can make accurate esimates next time
        $bytesinline = @(($totbytes-$startpos) / $tlp);
        if ($bytesinline < 5) {
            //echo "something is wrong: $bytesinline = @(($totbytes-$startpos) / $tlp);"  ;
        } else {
            setProfileData($profile->profilename, "bytesinline.".md5($tfile2), $bytesinline); 
            //echo  "$profile->profilename, bytesinline.".md5($tfile2).", $bytesinline";      
        }
        # also, store the cache array so we can use it next time
        StoreCacheID($profile->tablename_urls,$url_id,@$urlhit);
        StoreCacheID($profile->tablename_urlparams,$urlparams_id,@$urlparamhit);
        StoreCacheID($profile->tablename_referrers,$referrers_id,@$refhit);
        StoreCacheID($profile->tablename_refparams,$refparams_id,@$refparamshit);
        StoreCacheID($profile->tablename_keywords,$keywords_id,@$keywordshit);
        StoreCacheID($profile->tablename_useragents,$user_agents,@$useragentshit);
        StoreCacheID($profile->tablename."_titles",$titles_id,@$titlehit);
    }
    $ttlp=$ttlp+$tlp;
    $tlp=0;
    if ($force==true && isset($logtimestamp)) {
        if (!isset($last_inserted_time)) {
            $last_inserted_time=$logtimestamp;  
        } else if ($last_inserted_time < $logtimestamp) {
            $last_inserted_time=$logtimestamp;   
        }
        echo _LAST_INSERTED_TIMESTAMP_FOUND_IS." $last_inserted_time ".date("Y-m-d H:i:s",$last_inserted_time);
    }
    
    if ($debug) {
        # this will print some stats showing how many records were added to each table 
        $end_record_count = GetRecordCounts();    
        $rc="";
        foreach($end_record_count as $table => $count) {
            $rc .= "Added ".($count - $start_record_count[$table])." records to $table<br>";        
        }
        echoNotice($rc);
    }
	
	saveBandwidth();
	
    // var_dump($_SESSION['useragents']);
	unset($_SESSION['useragents']);
}

function showTimeTable($timetracking) {
    global $data,$labels,$from,$to;
    $showfields="Item,Duration,Share";
    $m=0;
    $data=array();
    $query="";
    foreach($timetracking as $key=>$value) {
        //echo "$key took $value or ".(($value/$timetracking['total'])*100)."% of total time<br>";
        $data[$m][0]=$key;
        $data[$m][1]=$value;
        $data[$m][2]=(($value/$timetracking['total'])*100);
        $m++;
    }
    $labels="speed report ".date("Y-m-d H:I:s",time());
    ArrayStatsTable($from,$to,$showfields,$labels,$query);    
}

function StoreCacheID($table,$cache,$hits) {
    global $profile, $cache_limit, $debug;    
    if ($debug) { echoNotice("store cache for $table"); }
    
    if (empty($hits)) {
        # nothing to do
        return;    
    }
    
    $start = getmicrotime();
          
    # flip the cache array so we can link by id
    $cache = array_flip($cache);
	$new_cache = array();
    
    # sort the hits descending and use it to make a new optimized cache array
    arsort($hits);
    $i=0;
    foreach ($hits as $id => $val) {
        $i++;
        if ($i == $cache_limit) {
            break;
        }
        $new_cache[$cache[$id]] = $id;
    }
    //echoNotice("$table cache used ".count($new_cache)." items");
    
    # make sure the top entries of the old array gets retained up to the cache_limit
    //$cache = array_flip($cache);
    $i=count($new_cache);
    foreach ($cache as $id => $url) {
        $new_cache[$url] = $id;
        $i++;
        if ($i > $cache_limit) { break; }            
    }
    
    # unset the old cache, just in case we run into memory problems
    unset($cache);
    
    # now store the optimized cache array so we can use it next time
    setProfileData($profile->profilename,$table."_cachearray",serialize($new_cache));
    
    $took = getmicrotime() - $start;
    echoDebug("Storing $table cache took $took seconds (".count($new_cache)." items)");
}

function GetRecordCounts() {
    global $profile,$db;
    $rc = array();
    $t = array();
    
    # tables to count
    $t[] =  $profile->tablename;
    $t[] =  $profile->tablename_urls;
    $t[] =  $profile->tablename_urlparams;
    $t[] =  $profile->tablename_referrers;
    $t[] =  $profile->tablename_refparams;
    $t[] =  $profile->tablename_keywords;
    $t[] =  $profile->tablename_visitorids;  
     
    foreach($t as $table) {
        $q = $db->Execute("select count(*) as records from $table");
        $data = $q->FetchRow();
        $rc[$table] = $data["records"];
    }
    return $rc;
}

$GoogleParams = str_replace(" ","",$profile->googleparams);
$GoogleParams = (!empty($GoogleParams) ? explode(",",$GoogleParams) : $GoogleParams);

function CleanGoogleUrls($gparams) {
    global $GoogleParams;
    
    $str="";
    if (!empty($GoogleParams)) {
        parse_str($gparams, $p_array);
        foreach ($p_array as $key => $val) {
            // this will include selected and delete all others
            if (in_array($key, $GoogleParams)) {
                $str.= "$key=$val&";
            }       
        }
        $str=substr($str,0,-1);
    } else {
        $str = $gparams;    
    }
    return $str;        
}

function getTitleFromCookies($cookies) {
    $cookies = str_replace("; ","&",$cookies);
    parse_str($cookies);
    if (isset($docTitle)) {
        //echoDebug($docTitle);
        return $docTitle;    
    } else {
        return "";    
    }
    
}
//----------------------------------------------------------------------------------------------
// START MAIN SCRIPT
//----------------------------------------------------------------------------------------------
if (!$running_from_command_line) {
?>
<script type='text/javascript'>
function pbar(p) {
	self.document.forms.progress.perc.value=p;
	self.document.forms.progress.progbar.value=self.document.forms.progress.progbar.value+'|';
}
function pstatus(p) {
	self.document.forms.progress.ptext.value=p;
}
</script>
<?php
}
//start output
ob_start(); 
echoConsoleSafe("<div class=\"indentbody\"><font size=3><strong>"._UPDATE_DATABASE."</strong></font></div><P>");
lgflush();

$skipips=$profile->skipips;

$skipip=explode(",", $skipips);
$skipip_regex = array();
for ($i=count($skipip)-1; $i >= 0; $i--) {
	// Is there a regex part of this?
	$skipip[$i] = trim(str_replace('*', '([0-9]{1,3})', $skipip[$i]));
	
	if (!($skipip[$i] > "")) {
		unset($skipip[$i]);
	} else if (!(strpos($skipip[$i], '(') === false)) {
		$skipip_regex[] = $skipip[$i];
		unset($skipip[$i]);
	}
}

//$skipfiles=$profile->skipfiles.",";
$skipfiles=trim(str_replace(" ", "", $profile->skipfiles));
//echo "<br>remove empty slots ";
$skipfiles=trim(str_replace(",,", ",", $skipfiles));
if (substr($skipfiles,-1)==",") {
    // ignore trailing comma
    $skipfiles=substr($skipfiles,0,-1);
}
$skipfile=explode(",",$skipfiles);
 

$equivalent_domains = explode(",", $profile->equivdomains);
$equivalent_domains_regex = array();
// Are any of our domains regexes?
for ($i=count($equivalent_domains)-1; $i >= 0; $i--) {
	// Is there a regex part of this?
	if (!(strpos($equivalent_domains[$i], '(') === false)) {
		$equivalent_domains_regex[] = $equivalent_domains[$i];
		unset($equivalent_domains[$i]);
	}
}

if ($profile->urlparamfilter!="") {
    $urlparamfilter=$profile->urlparamfilter;
    $urlparamfilter=explode(",", $urlparamfilter);
}

$s=0;
$httpget=1;
$user_agents = array();
$url_id = array();

setupURLandParamData();  // Set up the "Important URL Params" section

if (@$upgrade) {
    $profile->trackermode=0;
    $profile->splitfilter=0;
    $profile->splitfilternegative=0;
    $profile->logfilefullpath=$upgrade;   
}

//if we're in trackermode
if ($profile->trackermode==1) {
    
    $trackertable=$profile->tablename_trackerlog;
    $q = $db->Execute("select count(*) from $trackertable");
    $trackerlogdata=$q->FetchRow();
    $lines=$trackerlogdata[0];
    echoConsoleSafe("<div class=\"indentbody\">".$lines." "._DATABASE_LINES_TO_PROCESS."</div>\n", true); 
}

function Get_seektime(){
	global $db, $profile;
	$q = $db->Execute("select timestamp from $profile->tablename order by timestamp desc limit 1");
	$data=$q->FetchRow();
	return $data[0];
}

if ($profile->trackermode==2) {
	require_once 'includes/FTP_log_class.php';
	echo "<div id='FTP_progress' class='indentbody'>\n";
    // we have to FTP the file first
    $updir = new UploadDir();
        
    $profile->logfilefullpath = $updir->getUploadDir().$profile->profilename ;
    if(!is_dir($profile->logfilefullpath)){
    	mkdir($profile->logfilefullpath);
    }
        
    $profile->logfilefullpath .= '/';
    echo "Starting FTP download.<br>\n";
    $FTP_class = new FTP_log($profile->ftpserver, $profile->ftpuser, $profile->ftppasswd, $profile->ftpfullpath, $profile->splitfilter, $profile->splitfilternegative, $profile->logfilefullpath, Get_seektime());
    $FTP_class->Files_Download();
    echo "</div><br>\n";
    # if we are doing this we always have to me in multi file mode, so set that now, just in case
    $profile->splitlogs=1;
    # when we've ftp'd we know we don't have to do any more filtering on the files, since that has already been done. So, reset the filters
    $profile->splitfilter="";
    $profile->splitfilternegative="";
}

// see if the log file exists, if it's not a multi log dir
if ($profile->splitlogs!=1 && $profile->trackermode!=1) {
	if (substr($profile->logfilefullpath,0,7)=="http://")  {
        echo "we're gonna try http";   
    } else if (file_exists($profile->logfilefullpath) && $profile->splitlogs!=1) {
	    //echo "The file $logfilefullpath exists<P>";
	} else {
		echoConsoleSafe("<div class=\"indentbody\">"._ERROR_THE_LOG_FILE." <b>$profile->logfilefullpath</b> "._DOESNT_EXIST_TO_CORRECT_THIS."</div>", true);
		exit();
	}
}

$what = filetype($profile->logfilefullpath);
if ($what=="dir") {
	if (!$running_from_command_line) {
		echo "<div class=\"indentbody\">"._LOG_FILE_INFO_LOCATION." <font color=green>$profile->logfilefullpath</font> "._IS_A_DIRECTORY.".<br>";
	} else {
		echo ""._LOG_FILE_LOCATION.": $profile->logfilefullpath "._IS_A_DIRECTORY."\n";
	}
	if (substr($profile->logfilefullpath,-1,1)!="/") {
		$profile->logfilefullpath=$profile->logfilefullpath."/";
	}
	if ($profile->splitlogs!=1) {
		echo ""._THIS_SHOULD_BE_A_FILE_NAME;
		echo "<P><a href=profiles.php?editconf=$conf&edit=1>"._CHANGE.' '.$conf.' '._PROFILE."</a>.";
		exit();
	}
	if (!$running_from_command_line) {
		echo "</div>\n";
	}
} else if ($what=="file") {
	$lfown=fileowner($profile->logfilefullpath);
	$lfperm=substr(sprintf('%o', fileperms($profile->logfilefullpath)), -4);	 
	$lfmb=number_format(((filesize($profile->logfilefullpath)/1000)/1000),2);
	if (!$running_from_command_line) {
		echo "<div class=\"indentbody\">"._LOG_FILE_INFO_LOCATION." <font color=green>$profile->logfilefullpath</font> "._IS_A_FILE.".<br>\n";
		echo ""._SIZE.": $lfmb MB<br>\n";
	} else {
		echo ""._LOG_FILE_LOCATION.": $profile->logfilefullpath "._IS_A_FILE.".\n";
		echo ""._SIZE.": $lfmb MB\n";
	}

	if (@$_ENV['OS']!="Windows_NT") {
		if (extension_loaded('posix')) {
			$list=posix_getpwuid($lfown);
			$lfown=$list['name'];	
		}
		echoConsoleSafe(""._OWNER.": $lfown<br>\n", true);
		echoConsoleSafe(""._PERMISSIONS.": $lfperm<br>\n", true);
	}
	if (is_readable($profile->logfilefullpath)) {
		echoConsoleSafe(""._THE_FILE_IS_READABLE."<br>\n", true);
        $logFormat = formatOfLogFile($profile->logfilefullpath);             
        if ($logFormat) {
            echoConsoleSafe('<font color=green>'._VALID_LOG_FILE_FORMAT.' <b>'.$logFormat["Description"].'</b></font>', true);
        } else {
            echoConsoleSafe('<font color=red><b>'._FILE_NOT_A_KNOWN_LOG_FILE_TYPE.'.</b></font>', true);
        }
	} else {
		echoConsoleSafe(""._FILE_IS_NOT_READABLE."<br>\n", true);
		exit();
	}
    //if it's a file at this point, we can't do splitlogs so let's override that
    if ($profile->splitlogs==1) {
        echoConsoleSafe("<P><b>"._TIP_DESELECT_MULTI_LOG_FILES."<p>\n", true);
        $profile->splitlogs=0;   
    }
	echoConsoleSafe("</div>");
    
    
} else {
	if ($profile->trackermode!=1) {
        echo ""._ERROR_FILE_IS." $what";
    }
}
lgflush();

//get the last line inserted into the database
if ($force) {
	$q = $db->Execute("select timestamp from $profile->tablename order by timestamp desc limit 0");
} else {
	$q = $db->Execute("select timestamp from $profile->tablename order by timestamp desc limit 10");
}
$data=$q->FetchRow();

//Prepare the filepart to be analyzed
//Are we doing a straight file or a directory of zipped files ?

if ($profile->splitlogs==1) {
	 
	 if ($data[0]=="") {
		echoConsoleSafe("<div class=\"indentbody\">"._READING_DIRECTORY_FROM_START."<p>", true);
		ReadLogDir($profile->logfilefullpath);
		echoConsoleSafe("</div>");
	 } else {
		$now=time();
		echoConsoleSafe("<div class=\"indentbody\">"._SYSTEM_TIME_IS." ". date("d/M/Y:H:i:s",$now) . "<br>\n", true);
		while ($now < $data[0]) {
			if (($data[0]-$now) > 86400) {
				echo ""._ERROR_THERE_ARE_RECORDS_NEWER_THAN_SYSTEM_TIME." -".date("d/M/Y:H:i:s",$data[0])."<br>";
			}
			$data=$q->FetchRow();
		}
		//echo "</dir>";
		if ($data[0]=="") {
			//echo "just do today";
			$seektime=date("d/M/Y:H:i:s",$now);
			$st=$now;
			$orist=$st;
		} else {
			//echo "do from $seektime";
            $seektime=date("d/M/Y:H:i:s",$data[0]);
			$st=$data[0];
			$orist=$st;
		}
		echoConsoleSafe(_READING_FROM." $seektime<p>\n", true);
		ReadLogDir($profile->logfilefullpath);
		echoConsoleSafe("</div>");
	 }
} else {
	// it's a file
	if (substr($profile->logfilefullpath,-3,3)==".gz") {
  		$zmode="gz";
	}
	if ($data[0]=="") {
		//ahh! a new import, go directly to jail do not collect 200
		echoConsoleSafe( "<div class=\"indentbody\"><P>"._PREPARING_LOG_FILE_FROM_START."...</P></div>\n", true);
  	    @deleteProfileData($profile->profilename, "lastlogpos.$profile->profilename");
  	    @deleteProfileData($profile->profilename, "firstlogline.$profile->profilename");
		
	} else {
		//let's update this file
		$now=time();
        /*
        if ($profile->timezonecorrection != 0) {
            $now += ($profile->timezonecorrection * 3600);
        }
        */
		echoConsoleSafe("<div class=\"indentbody\">");
		$tip="";
		while ($now < $data[0]) {
			echo ""._ERROR_SEEKTIME_IS_IN_FUTURE." now : ".date("d/M/Y:H:i:s",$now).", seektime : ".date("d/M/Y:H:i:s",$data[0])."<br>";
			$data=$q->FetchRow();
			$tip=""._TIP_FAULTY_RECORDS." $profile->tablename";
		}
		echoConsoleSafe("$tip</div>");
		$seektime=date("d/M/Y:H:i:s",$data[0]);
		$st=$data[0];
		$orist=$st;
		$e=getmicrotime();
		echoConsoleSafe("<div class=\"indentbody\"><P>"._PREPARING_LOG_FILE_FROM." $seektime, "._PLEASE_BE_PATIENT."...</P></div>\n", true);
		$start=getmicrotime();
		lgflush();

	}
}

// this part will estimate the number of lines to do
if ($profile->splitlogs!=1 && $profile->trackermode!=1) { // with test tracker
	
	$lastpos = 0;
	$firstlogline = 0;
	
	$lastpos = getProfileData($profile->profilename, "lastlogpos.".md5($profile->logfilefullpath), 0);
    $firstlogline = getProfileData($profile->profilename, "firstlogline.".md5($profile->logfilefullpath), "");
	$startpos=$lastpos; // do this so we still know the original lastpos when we finish update
    
    //echo "esitmate lines now EsitmateLines($profile->logfilefullpath)";
	echoConsoleSafe("<div class=\"indentbody\"><p>");
    $lines = EstimateLines($profile->logfilefullpath);
	echoConsoleSafe("</p></div>");
	//echo "lines: $lines";
	lgflush();
}

//delete the old cache file(s)
//$db->hasTransactions=false;
if ($db->hasTransactions) {
//echo "database supports transactions"; 
  if ($databasedriver == "mysql") {
	 $db->Execute("Start Transaction");
	} else {
	 $db->Execute("Begin Transaction");
	}
}
deleteProfileData($profile->profilename, "cache\_%");
deleteProfileData($profile->profilename, $profile->profilename.".cache\_%");
deleteProfileData($profile->profilename, $profile->profilename."cache_trail"); 

if ($db->hasTransactions) { $db->Execute("Commit"); }

// if ($httpget==1) {
//	$tfile2=$profile->logfilefullpath;
	//echo "getting $logfilefullpath in Pure PHP mode! (Configure PHP to allow use of unix commands like 'cat' for much faster updates)<P>";
// }

echoConsoleSafe("<div class='indentbody'>");

if ($profile->splitlogs!=1) {
	echoConsoleSafe("<form name=progress>"._PROGRESS.":<br><input type=text name=perc size=1 value=\"0\" class=pbar>% &nbsp;");
	echoConsoleSafe("<input type=text name=progbar size=46 value=\"|\" class=progbar><P>");
	echoConsoleSafe(""._STATUS.": <input type=text name=ptext size=85 value=\"Opening Log file\" class=\"pbar progbar\"><P>");
	//echo "<font size=3 color=#2ED331><b>";
	//echo "|";
	lgflush();
	//now start analyzing
	analyzeThis($profile->logfilefullpath);
    echoConsoleSafe("</b></font>"); 
    echoConsoleSafe("</form>");
}

echoConsoleSafe("<script type='text/javascript'>pstatus('"._PREPARING_SUMMARIES."...')</script>");
echoConsoleSafe("<script type='text/javascript'>pbar(0); self.document.forms.progress.progbar.value='|';</script>\n");

$start=getmicrotime();
if (!@$newlines) {
    $newlines="no new";
    echoConsoleSafe("<script type='text/javascript'>pstatus('"._FINISHED."')</script>");
    echoConsoleSafe("<script type='text/javascript'>pbar(100); self.document.forms.progress.progbar.value='||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||';</script>");
    lgflush();
} else {
    lgflush();
    include("update_summaries.php");   
}

# now it's time to check if we need to send any email alerts
include "includes/emailalerts.php";
$e = new EmailAlerts();
echoConsoleSafe("<script type='text/javascript'>pstatus('"._SENDING_EMAIL_ALERTS."')</script>");
$sent = count($e->SendAlerts());
if ($sent > 0) {
    echoConsoleSafe("\n\n<br>".str_replace('%a',$sent,_SENT_X_ALERTS)."<br>",true);
}
echoConsoleSafe("<script type='text/javascript'>pstatus('"._FINISHED."')</script>");

if ($page==$_SERVER['PHP_SELF']) {
	 $page="index.php";
}
if (!$page) {
		$page="index.php?conf=$conf";
}
$e=getmicrotime();
$took=($e-$start);
$took=number_format($took,0);
if (@$logtimestamp) {
    $logtime=_LAST_RECORDED_REQUEST_ADDED_ON." " .date("Y-m-d, H:i:s", $logtimestamp);
} else {
    $logtime="";   
}

$e=time();
$tottook=($e-$scriptstart);
//$took=number_format($took,0);
if (!isset($ttlp)) {
    $ttlp="0";   
}
echoConsoleSafe("\n\n<br>"._DONE."! $logtime<br>\n"._SUMMARY_UPDATE_TOOK." $took "._SECONDS_TOTAL_SCRIPT_TIME_TOOK." $tottook "._SECONDS_ADDED." $newlines "._RECORDS_TO_DATABASE.".<br>\n"._PROCESSED_AN_ACTUAL_TOTAL_OF." $ttlp "._LOG_LINES.".<P>\n", true);

echoConsoleSafe("<form method=get action=$page><input type=hidden name=conf value=$conf><input type=submit value=\" Finish \"></form>");
//echoConsoleSafe("</td></tr></table>");
setProfileData($conf,"$conf.update_running","no");
ob_end_flush();
echoConsoleSafe("</div></body></html>");
?>