<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
//PHPCIPHER NO ENCRYPT
// PHPLOCKITOPT NOENCODE
  // Tell Logaholic about this parser type. lalala
  
  // Needs these parameters: Description (text description), ClassName (modify for each parser) and, AutoDiscoverable - true/false - must currently be true.
	$log_parser_types[] = Array("order" => "7", "Description" => "Apache Common / Combined with special handling", "ClassName" => "SpecialApacheCommonLogParser", "AutoDiscoverable" => true);

	class SpecialApacheCommonLogParser extends GenericLogParser {
		
        var $regEx;
        
		function Initialize($file) {

			// Create the regex once.
			$this->regEx = '%^(\\S+)\\s+'. // IP Address
										 '\\S+\\s+'.    // IdentD user - totally unreliable and unused in all cases - ignore.
										 '(\\S+)\\s+'. // authenticated user
										 '\\[([^]]+)\\]\\s+'. // log entry date
										 // URL and query.  Lots of optional stuff here.  A blank URL will be just -, so that shows up first with an or
										 // If we have a non -, though, we'll have a GET, POST or HEAD, but we don't want to capture that (?:).
										 // Then, we have the url - which is terminated by a space or a ? (or an implied ", which is specified later).
										 // Optional parameter (?), then an optional protocol specifier (HTTP).
										 // This *always* ends with a quote and a space, though - just a quote could be an embedded quote, but the quote, then space
										 // indicates the end of the url field
										 '"(?:-|(?:GET|POST|HEAD) ([^ ?]*?\\??[^ ]+?)?\s?(?:HTTP/[0-9.]+)?)"\\s+'. 
										 '([0-9]+)\\s+'. // Status
										 '([-0-9]+)\\s+'. // Bytes.
										 '"(.*?)"\\s+'. // referrer.  This always ends with quote space.  Could have quotes in the referrer, though, so can't just look for that.
										 '"(.*?)"\\s*'. // Agent. 
                                         '$'.     //This ends things ($) 
										 '%m';  // the "m" modifier means to look at the entire text, not worrying about CR/LF's.  We always pass only a single line.
			
            return true;
		}
		
		function ParseLine($line) {             
            
			// Note: This regex will skip anything that doesn't have a file name specified (which we really don't care about...)
			if (preg_match($this->regEx, $line, $matches)) {
			  $this->clientip = $matches[1];
			  $this->authuser = $matches[2];
			  // We're explicitly ignoring the time offset stored in the log here, for legacy reasons and because 
			  // all the implications of time zone handling...
			  $logtimeq=str_replace("/", " ", substr($matches[3],0,11)) . " " . substr($matches[3],12,8);            
			  $this->logdate = strtotime($logtimeq);
			  if ($this->skipLine($this->reqfile)==true) {
                $this->lastlineisdata = false;
                return true;     
              }
              $this->reqfile = $this->specialHandling($matches[4]);
			  $this->status = $matches[5];
			  $this->bytes = $matches[6];
			  if ($this->bytes == "-") {
		  		$this->bytes = 0;
			  }
			  $this->referrer = $matches[7];
			  $this->agent = $matches[8];
			  $this->cookie = "";
  			  $this->lastlineisdata = true;
			  return true;
			} else {
  			    $this->lastlineisdata = false;
  			    $this->lasterrormessage = "Unparseable log line:<br><pre>".$line."\n</pre>";
                //$this->lasterrormessage = $line."\n";
  			    return false;
			}
		}
        
        /**
         *   specialHandling allows you to rewrite a field for selected profiles 
         *   In this example, specialHandling is applied to $this->reqfile in ParseLine 
         *   function above. If the profilename listed below is active this field will 
         *   be stripped of the domain name when the log file is parsed
         */
        
        function specialHandling($string) {
            global $conf;
            
            // add the profilenames for which you want to have special handling here and edit accordingly
            switch($conf) {
                case 'profilename':
                    $string = str_replace("/www.xyz.com",$string);
                    break;
                case 'otherprofilename':
                    $string = str_replace("/www.lalala.com","",$string);
                    break;
                case 'etc.':
                    $string = str_replace("/www.etc.com","",$string);
                    break;            
            }
            return $string;   
            
        }
        
        /**
         *   skipLine allows you to skip lines based on a field for selected profiles 
         *   In this example, skipLine is applied to $this->reqfile in ParseLine 
         *   function above. 
         */        
        function skipLine($string) {
            global $conf;
            
            // add the profilenames for which you want to have special handling here and edit accordingly
            switch($conf) {
                case 'profilename':
                    // if it matches $find, this function will return true and this line will be skipped
                    $find="skipthis";
                    if (strpos($string,$skipthis)!==FALSE) {
                        return true;    
                    }            
            }
            return false;          
        }
	}
	
?>
