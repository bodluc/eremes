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
	$log_parser_types[] = Array("order" => "10", "Description" => "Apache Common/Combined, including Proxy IP", "ClassName" => "DoubleIPApacheCommonLogParser", "AutoDiscoverable" => true);

	class DoubleIPApacheCommonLogParser extends GenericLogParser {
		
		var $scanEx;
		
		function Initialize($file) {
            $flogline=gzgets($file,5120);
            if (substr($flogline, 0, 1) =="#") {
                $this->lasterrormessage .= "# lines should not present in this format, aborting";
                return false;
            } else {
			    $this->scanEx = '%s %s %s [%[^]]] "%s %s %[^"]" %d %s "%[^"]" "%[^"]"';
			    return true;
            }
		}
		
		function ParseLine($line) {
			
            /*
            $log['ip'],  0
            $log['client'],       1
            $log['user'],          2
            $log['time'],           3
            $log['method'],          4
            $log['uri'],              5
            $log['prot'],              6
            $log['code'],               7
            $log['bytes'],               8
            $log['ref'],                  9
            $log['agent']                  10
             */
            $line=str_replace(", ", "#", $line);
			if ($matches= sscanf($line, $this->scanEx)) {
             
              $this->clientip = explode("#",$matches[0]);
              if (@$this->clientip[1]!="") {
                $this->clientip = $this->clientip[1];     
              } else {
                $this->clientip = $this->clientip[0];   
              }              
              $long = ip2long($this->clientip);

              if ($long == -1 || $long === FALSE) {
                   return false;
              }
              $this->authuser = $matches[2];
              // We're explicitly ignoring the time offset stored in the log here, for legacy reasons and because 
              // all the implications of time zone handling...
              $logtimeq=str_replace("/", " ", substr($matches[3],0,11)) . " " . substr($matches[3],12,8);
              $this->logdate = strtotime($logtimeq);
              $this->reqfile = $matches[5];
              $this->status = $matches[7];
              $this->bytes = $matches[8];
              if ($this->bytes == "-") {
                  $this->bytes = 0;
                }
              $this->referrer = $matches[9];
              $this->agent = $matches[10];
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
