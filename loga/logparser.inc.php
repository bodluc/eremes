<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
$log_parser_types = array();
  
// This is the base class used by all log parser classes - this defines the base interface
class GenericLogParser {
	
	// These are things we are parsing out of the log file.
  var $clientip;
  var $authuser;
  var $logdate;
  var $reqfile;
  var $status;
  var $bytes;
  var $referrer;
  var $agent;
  
  var $lastlineisdata;
  var $lasterrormessage;
  
  function IsValidLogFile($filename) {
      global $debug;
  	$logfile = gzopen($filename, "r") or die("Can't open logfile: " . $filename);  // gzopen will open a regular file *or* a gzipped one.
  	
		if (!$this->Initialize($logfile)) {
			return false;
		};
  	
  	// Are the first 10 (now 5) lines parseable?
  	$result = true;
  	$linestoparse = 20;
  	$hasvalidlines = false;
    $errors=0;
    $allowed_errors=$linestoparse/3;

		//while (($flogline=gzgets($logfile,1024)) && ($result) && ($linestoparse-- > 0)) {
        while (($flogline=gzgets($logfile,4096)) && ($linestoparse-- > 0)) {  
            if (substr($flogline,0,3)=="::1") {
                // we've seen this before in log files and we should just skip it because it could have valid lines later on in the file     
                $linestoparse++;
                continue;
            }
            if (!$this->ParseLine($flogline)) {
				$result = false;
				$falseline=20-$linestoparse;
				if(!empty($debug)) { echo "<pre>$flogline</pre>"; }
                $errors++;
			} else {
				$hasvalidlines = true;
			}
            //echo "parsed line  $linestoparse ($hasvalidlines,$result)<br>";
            
		}
		gzclose($logfile);
		
		if (!empty($debug) && $errors > $allowed_errors) {
			if(!empty($debug)) { echo "<P>too many errors in log  ($errors)<p>"; }
        }
        
        if ($hasvalidlines===true && ($errors < $allowed_errors)) {
            if(!empty($debug)) { echo "set result to true"; }
            $result=true;   
        } 
        
        return (($result) && ($hasvalidlines));  	
	}
	
	function Initialize($file) {
		return false;
	}
	
	function ParseLine($line) {
	  $this->lastlineisdata = false;
	  return $this->lastlineisdata;
	}
	
	/* function ImportQuery() {
		return "";
	} */
	
	function CleanUp() {
		return "";
	}
}

// Iterate all the files in the "log_formats" directory and load any filters found 
// there.  A format always has ".inc.php" extension, so only load those files.

// Each format needs to register itself to the $log_parser_types array so we know
// it's name, class name, and if it supports auto discovery.

$format_path = dirname(realpath("index.php")) . "/log_formats/";
$dirhandle = opendir($format_path);
$ignoreparser = array("ApacheCommonNoReferrer.inc.php","ApacheCommonWithCookie.inc.php", "ApacheVCombined.inc.php");
while ($file = readdir($dirhandle)) {
	if (!in_array($file,$ignoreparser)) {
		if (strtolower(substr($file, -8)) == ".inc.php") {
			require $format_path . $file;
		}
	}
}
closedir($dirhandle);


if (count($log_parser_types) == 0) {
	die("No logfile parsers - check your log_formats directory and make sure parsers exist there.");
}

// Returns the array of format data for the passed in filename.  return NULL if no match.
function formatOfLogFile($filename) {
  global $log_parser_types,$debug;
  asort($log_parser_types);
  
	if(!empty($debug)) {
		echo "<pre>";
			print_r($log_parser_types);
		echo "</pre>";
	}
	
  foreach ($log_parser_types as $format_type) {
  	$this_parser = new $format_type["ClassName"];
  	if (@$debug) {
    echo "<br><b>".$format_type["ClassName"] . "</b><br>";
    }
    if ($this_parser->IsValidLogFile($filename)) {
  	    //echo $format_type["ClassName"] . "is valid<br>"; 
        return $format_type;
	}  else {
        if (@$debug) {
           echo $this_parser->lasterrormessage;  
        }
    }
  }
  return NULL;
}

?>
