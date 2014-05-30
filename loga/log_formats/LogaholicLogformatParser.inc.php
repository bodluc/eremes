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
	$log_parser_types[] = Array("order" => "1", "Description" => "Logaholic Log Format", "ClassName" => "LogaholicLogformatParser", "AutoDiscoverable" => true);

	class LogaholicLogformatParser extends GenericLogParser {
		
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
										 '"(.*?)"\\s+'. // Agent.  
                                         '"(.*?)"\\s*$'. // Cookies.  This ends things ($) 
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
				//echo $logtimeq;  //  08 Dec 2007 00:58:41
				//strtotime is fastest, test confirmed             
				$this->logdate = strtotime($logtimeq);
				$this->reqfile = $matches[4];
				$this->status = $matches[5];
				$this->bytes = $matches[6];
				if ($this->bytes == "-") {
					$this->bytes = 0;
				}
				$this->referrer = $matches[7];
				$this->agent = $matches[8];
				$this->cookie = $matches[9];
				$this->lastlineisdata = true;
				return true;
			} else {
				$this->lastlineisdata = false;
				//$this->lasterrormessage = "Unparseable log line:<br><pre>".$line."\n</pre>";
				$this->lasterrormessage = $line."\n";
				return false;
			}
		}
		
		function ImportQuery($file) {
			global $profile;
			
			return "LOAD DATA INFILE '{$file}' INTO TABLE `{$profile->tablename}log` FIELDS TERMINATED BY '|LWA|' ESCAPED BY '' LINES TERMINATED BY '\n' IGNORE 0 LINES 
			(`host`, logname, `user`, `timestamp`, timezone, request, `status`, bytes, referrer, useragent, useragent_hash, cookie, urlparams, refparams, keywords, visitorid, country, crawl, sessionid)";
		}
	}
	
?>
