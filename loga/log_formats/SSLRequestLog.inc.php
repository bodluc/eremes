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
	$log_parser_types[] = Array("order" => "11", "Description" => "Apache HTTPS Request log format", "ClassName" => "SSLRequestLog", "AutoDiscoverable" => true);

	class SSLRequestLog extends GenericLogParser {
		
		var $regEx;
		
		function Initialize($file) {
			// Create the regex once.
			$this->regEx = '%^'.'\\[([^]]+)\\]\\s+'. // log entry date  
                                        '(\\S+)\\s+'. // IP Address
                                        '(\\S+)\\s+'. // SSL Protocol
                                        '(\\S+)\\s+'. // SSL Cypher
                                         // URL and query.  Lots of optional stuff here.  A blank URL will be just -, so that shows up first with an or
                                         // If we have a non -, though, we'll have a GET, POST or HEAD, but we don't want to capture that (?:).
                                         // Then, we have the url - which is terminated by a space or a ? (or an implied ", which is specified later).
                                         // Optional parameter (?), then an optional protocol specifier (HTTP).
                                         // This *always* ends with a quote and a space, though - just a quote could be an embedded quote, but the quote, then space
                                         // indicates the end of the url field
                                         '"(?:-|(?:GET|POST|HEAD) ([^ ?]*?\\??[^ ]+?)?\s?(?:HTTP/[0-9.]+)?)"\\s+'. 
                                         '([-0-9]+)\\s*$'. // Bytes.
                                         '%m';  // the "m" modifier means to look at the entire text, not worrying about CR/LF's.  We always pass only a single line.
            return true;
        }
        
		
		function ParseLine($line) {
			// Note: This regex will skip anything that doesn't have a file name specified (which we really don't care about...)
			if (preg_match($this->regEx, $line, $matches)) {
              $logtimeq=str_replace("/", " ", substr($matches[1],0,11)) . " " . substr($matches[1],12,8);
              $this->logdate = strtotime($logtimeq);                
			  $this->clientip = $matches[2];
			  $this->authuser = "";
			  $this->reqfile = $matches[5];
			  $this->status = "";
			  $this->bytes = $matches[6];
			  if ($this->bytes == "-") {
		  		$this->bytes = 0;
				}
			  $this->referrer = "";
			  $this->agent = "";
  			  $this->lastlineisdata = true;
			  return true;
			} else {
  			$this->lastlineisdata = false;
  			//$this->lasterrormessage = "Unparseable log line:<br><pre>".$line."\n</pre>";
            $this->lasterrormessage = $line."\n";
  			return false;
			}
		}
	}
	
?>
