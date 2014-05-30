<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
//PHPCIPHER NO ENCRYPT
// PHPLOCKITOPT NOENCODE
  // Tell Logaholic about this parser type.
  
  // Needs these parameters: Description (text description), ClassName (modify for each parser) and, AutoDiscoverable - true/false - must currently be true.
	$log_parser_types[] = Array("order" => "11", "Description" => "Apache Common/Combined, with leading hostname", "ClassName" => "ApacheCommonLogParserWithHostname", "AutoDiscoverable" => true);

	class ApacheCommonLogParserWithHostname extends GenericLogParser {
		
		var $scanEx;
		
		function Initialize($file) {
            $flogline=gzgets($file,5120);
            if (substr($flogline, 0, 1) =="#") {
                $this->lasterrormessage .= "# lines should not present in this format, aborting";
                return false;
            } else {
			    $this->scanEx = '%s %s %s %s [%[^]]] "%s %s %[^"]" %d %s "%[^"]" "%[^"]"';
			    return true;
            }
		}
		
		function ParseLine($line) {
            
			if ($matches= sscanf($line, $this->scanEx)) {
                
                $this->clientip=$matches[1];
                $long = ip2long($this->clientip);

              if ($long == -1 || $long === FALSE) {
                   return false;
              }
              $this->authuser = $matches[3];
              // We're explicitly ignoring the time offset stored in the log here, for legacy reasons and because 
              // all the implications of time zone handling...
              $logtimeq=str_replace("/", " ", substr($matches[4],0,11)) . " " . substr($matches[4],12,8);
              $this->logdate = strtotime($logtimeq);
              $this->reqfile = $matches[6];
              $this->status = $matches[8];
              $this->bytes = $matches[9];
              if ($this->bytes == "-") {
                  $this->bytes = 0;
                }
              $this->referrer = $matches[10];
              $this->agent = $matches[11];
              $this->cookie = "";
              $this->lastlineisdata = true;
              //echo "<pre>";
              //print_r($matches);
              //echo "<pre>";
              return true;
            } else {
              $this->lastlineisdata = false;
              $this->lasterrormessage = "Unparseable log line:<br><pre>".$line."\n</pre>";
              //$this->lasterrormessage = $line."\n Matches:" . print_r($matches);
              return false;
            }
		}
	}
	
?>
