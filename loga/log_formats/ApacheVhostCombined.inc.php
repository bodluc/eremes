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
	$log_parser_types[] = Array("order" => "12", "Description" => "Apache Vhosts Combined", "ClassName" => "ApacheVhostCombined", "AutoDiscoverable" => true);

	class ApacheVhostCombined extends GenericLogParser {
		
		var $scanEx;
		
		function Initialize($file) {
            $flogline=gzgets($file,5120);
            if (substr($flogline, 0, 1) =="#") {
                $this->lasterrormessage .= "# lines should not present in this format, aborting";
                return false;
            } else {               
                
                // LogFormat "%V %h %l %u %t \"%r\" %s %b \"%{Referer}i\" \"%{User-Agent}i\"" vcombined      
			    $this->scanEx = '%s %s %s %s [%[^]]] "%s %s %[^"]" %d %s "%[^"]" "%[^"]"';
			    return true;
            }
		}
		
		function ParseLine($line) {
            global $profile;
            global $equivdomains, $equivalent_domains_regex;
			
            /*
            $log['vhost']   0
            $log['ip'],     1
            $log['client'],       2
            $log['user'],          3
            $log['time'],           4
            $log['method'],          5
            $log['uri'],              6
            $log['prot'],              7
            $log['code'],               8
            $log['bytes'],               9
            $log['ref'],                  10
            $log['agent']                  11
             */
			 
			if ($matches= sscanf($line, $this->scanEx)) {
              
              // analyze the line
              // check the ip address first because it's a good way to see if the format is valid, the skipping part below can cause it to say valid when it's not 
              $this->clientip = $matches[1];                 
              $long = ip2long($this->clientip);
              if ($long == -1 || $long === FALSE) {
                  return false;
              }
			  
              //skip the lines that don't match the host name we're interested in 
			  if (isset($profile)) {
					
				  $isEquivalent = true;  
				  if ($profile->confdomain == $matches[0]) {                  
				  } else {
					if (!@in_array($matches[0], $equivdomains)) {
					  $isEquivalent = false;    
					}
				  } 
				  if (isset($equivalent_domains_regex)) {
					  foreach ($equivalent_domains_regex as $thisregex) {
							if (preg_match("/".$thisregex."/i", $matches[0])) {
								$isEquivalent = true;
								break;
							}
					  }
				  }

				  if ($isEquivalent == false) {
						$this->lastlineisdata = false;
						return true;    
				  }
				  
				  
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
              return true;
            } else {
              $this->lastlineisdata = false;
              $this->lasterrormessage = "Unparseable log line:<br><pre>".$line."\n</pre>";
              return false;
            }
		}
		
		function ImportQuery($file, $skip_no = 0) {
			global $profile;
			
			/* "%v %h %l %u %t \"%r\" %>s %b" */
			
			return "LOAD DATA INFILE '{$file}' INTO TABLE `{$profile->tablename}log` FIELDS TERMINATED BY ' ' OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '' LINES TERMINATED BY '\n' IGNORE {$skip_no} LINES 
			(@vhost, host,logname,user,@logdate,timezone,@request,status,bytes,@referrer,useragent,cookie,@urlparams,@refparams,@keywords) SET 
			logdate = STR_TO_DATE(@logdate, '\[%d/%b/%Y:%H:%i:%s'), 

			request = SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(@request,'?', 1),' ',2),' ',-1), 
			urlparams = IF(STRCMP(0,LOCATE('?',@request)),concat('?',SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(@request,'?', -1),' ',2),' ',1)),''),

			referrer = SUBSTRING_INDEX(@referrer,'?', 1),
			refparams = IF(STRCMP(referrer,SUBSTRING_INDEX(@referrer,'?', -1)),CONCAT('?',SUBSTRING_INDEX(@referrer,'?', -1)),''),

			keywords = IF(STRCMP(0,LOCATE('&q=',@referrer)),urldecode(SUBSTRING_INDEX(SUBSTRING_INDEX(@referrer,'&q=', -1),'&',1)),'')";
		}
        
	}
?>
