<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
//PHPCIPHER NO ENCRYPT
// PHPLOCKITOPT NOENCODE
  
  // Param 1 = long description, 2 = class name (must match the class in this file!), 3 = if this can be used to auto-discover the format type.
	$log_parser_types[] = Array("order" => "5", "Description" => "Microsoft IIS", "ClassName" => "IISLogParser", "AutoDiscoverable" => true);

	class IISLogParser extends GenericLogParser {
		
		var $fldIdxDate;
		var $fldIdxAuthUser;
		var $fldIdxTime;
		var $fldIdxIP;
		var $fldIdxURI;
		var $fldIdxQuery;
		var $fldIdxStatus;
		var $fldIdxBytes;
		var $fldIdxReferer;
		var $fldIdxAgent;
			
		function Initialize($file) {
			
			$this->lasterrormessage = "";
			
			// probably W3C
            $flogline=gzgets($file,5120);
			while (substr($flogline,0,1)=="#") {
                if (substr($flogline, 0, 7) !="#Fields") {   
                    $flogline=gzgets($file,5120);
                    //echo "<P>".$flogline."<P>";
                } else {
                    break;   
                }
            }
            			
			if (substr($flogline, 0, 7) !="#Fields") {
				$this->lasterrormessage .= "Log file doesn't have #Fields line in correct spot.<br>";
				return false;
			}
			
		  //map the fields names to index points
			//$flogline=substr($flogline, 7);
			//$flogline=str_replace(": ",":\t", $flogline);
            $fields=explode(" ",$flogline);
            //print_r($fields);
			
			// Set up the variables for the field indexes
			$this->fldIdxIP = array_search("c-ip",$fields)-1;
			$this->fldIdxAuthUser = array_search("cs-username",$fields)-1;
			$this->fldIdxDate = array_search("date",$fields)-1;
			$this->fldIdxTime = array_search("time",$fields)-1;
			$this->fldIdxURI = array_search("cs-uri-stem",$fields)-1;
            if ($this->fldIdxURI < 0) { $this->fldIdxURI = array_search("cs-uri",$fields)-1; }
            if ($this->fldIdxURI < 0) { $this->fldIdxURI = array_search("cs-url",$fields)-1; }
			$this->fldIdxQuery = array_search("cs-uri-query",$fields)-1;
			$this->fldIdxStatus = array_search("sc-status",$fields)-1;
            if ($this->fldIdxStatus < 0) { $this->fldIdxStatus = array_search("cs-status",$fields)-1; } 
			$this->fldIdxBytes = array_search("sc-bytes",$fields)-1;
			$this->fldIdxReferer = array_search("cs(Referer)",$fields)-1;
			$this->fldIdxAgent = array_search("cs(User-Agent)",$fields)-1;
            $this->fldIdxCookie = array_search("cs(Cookie)",$fields)-1;
			
			// These fields are absolutely required.
			if ($this->fldIdxDate < 0) { $this->lasterrormessage .= "No Date field in log<br>"; return false; }
			if ($this->fldIdxTime < 0) { $this->lasterrormessage .= "No Time field in log<br>"; return false; }
			if ($this->fldIdxIP < 0) { $this->lasterrormessage .= "No IP Address field in log<br>"; return false; }
			if ($this->fldIdxURI < 0) { $this->lasterrormessage .= "No URL/URI field in log<br>"; return false; }
			
			// These fields aren't required, just useful.
			if ($this->fldIdxQuery < 0) { $this->lasterrormessage .= "Warning: No url parameters in log.<br>"; }
			if ($this->fldIdxStatus < 0) { $this->lasterrormessage .= "Warning: No status field in log.<br>"; }
			if ($this->fldIdxBytes < 0) { $this->lasterrormessage .= "Warning: No bytes field in log.<br>"; }
			if ($this->fldIdxReferer < 0) { $this->lasterrormessage .= "Warning: No referrer field in log.<br>"; }
			if ($this->fldIdxAgent < 0) { $this->lasterrormessage .= "Warning: No agent field in log.<br>"; }
            if ($this->fldIdxCookie < 0) { $this->lasterrormessage .= "Warning: No cookie field in log.<br>"; }
			
			return true;
		}
		
		function ParseLine($line) {
			$line = trim($line);
			
			// Is it a comment?
			if (substr($line,0,1)=="#") {
				$this->lastlineisdata = false; 
				return true; // It's a valid line, it's just one we don't care about.
			}
			
			// #Fields: date time c-ip cs-username s-ip s-port cs-method cs-uri-stem cs-uri-query sc-status sc-bytes cs-bytes time-taken cs-host cs(User-Agent) cs(Cookie) cs(Referer)
			// #Fields: time c-ip cs-method cs-uri-stem cs-uri-query sc-status cs-host cs(User-Agent) cs(Cookie) cs(Referer) 			
			$iisline = explode(" ", $line);
            
			$logtime=$iisline[$this->fldIdxDate] . " " .$iisline[$this->fldIdxTime];
			
			$this->logdate = strtotime($logtime);
			
			$this->clientip = $iisline[$this->fldIdxIP];
			$this->authuser = ($this->fldIdxAuthUser < 0) ? "" : $iisline[$this->fldIdxAuthUser];
			$this->reqfile = $iisline[$this->fldIdxURI];
			$params = ($this->fldIdxQuery < 0) ? "" : $iisline[$this->fldIdxQuery];
			if ($params != "-") {
				$this->reqfile .= "?". $params;
			}
			$this->status = ($this->fldIdxStatus < 0) ? "" : $iisline[$this->fldIdxStatus];
			$this->bytes = ($this->fldIdxBytes < 0) ? "0" : $iisline[$this->fldIdxBytes];
			if (!$this->bytes) {
				// do this to prevent mysql insert failures
			  $this->bytes = "0";
			}
			
			$this->referrer = ($this->fldIdxReferer < 0) ? "" : $iisline[$this->fldIdxReferer];
			$this->agent = ($this->fldIdxAgent < 0) ? "" : $iisline[$this->fldIdxAgent];
            $this->cookie = ($this->fldIdxCookie < 0) ? "" : $iisline[$this->fldIdxCookie];
			
  		    $this->lastlineisdata = true;
			
			return $this->lastlineisdata;
		}
		
		function ImportQuery($file, $skip_no = 0) {
			global $profile;
			
			$iis_fields = array();
			
			$fp = fopen($file, 'r');
			while($line = fgets($fp)) {
				if(strpos($line, "#Fields") !== false) {
					$iis_fields = substr($line, 9);
					$iis_fields = explode(" ", $iis_fields);
				}
			}
			
			$fields = array(
				"date" => "@logdate",
				"time" => "@logtime",
				"cs-method" => "@requestmethod",
				"cs-uri-stem" => "@request",
				// "cs-url-stem" => "@request",
				// "cs-uri" => "@request",
				// "cs-url" => "@request",
				"cs-uri-query" => "@request_query",
				// "cs-url-query" => "@request_query",
				"cs-username" => "user",
				"c-ip" => "host",
				"cs-version" => "@protocol",
				"cs(User-Agent)" => "useragent",
				"cs(Referer)" => "@referrer",
				"cs(Cookie)" => "cookie",
				"sc-status" => "status",
				"sc-bytes" => "bytes"
			);
			
			$request_sql = "";
			
			/* if(in_array("cs-url-stem", $iis_fields) && in_array("cs-url-query", $iis_fields)) {
				$request_sql = "request = @request,
				urlparams = IF(@request_query != '-', CONCAT('?', @request_query), ''),";
			} elseif(in_array("cs-uri-stem", $iis_fields) && in_array("cs-uri-query", $iis_fields)) {
				$request_sql = "request = @request,
				urlparams = IF(@request_query != '-', CONCAT('?', @request_query), ''),";
			} elseif(in_array("cs-url", $iis_fields)) {
				$request_sql = "request = SUBSTRING_INDEX(@request,'?', 1),
				urlparams = IF(STRCMP(request,SUBSTRING_INDEX(@request,'?', -1)),CONCAT('?',SUBSTRING_INDEX(@request,'?', -1)),''),";
			} elseif(in_array("cs-uri", $iis_fields)) {
				$request_sql = "request = SUBSTRING_INDEX(@request,'?', 1),
				urlparams = IF(STRCMP(request,SUBSTRING_INDEX(@request,'?', -1)),CONCAT('?',SUBSTRING_INDEX(@request,'?', -1)),''),";
			} else {
				$request_sql = "request = '',
				urlparams = '',";
			} */
			
			if(in_array("cs-uri-stem", $iis_fields) && in_array("cs-uri-query", $iis_fields)) {
				$request_sql = "request = @request,
				urlparams = IF(@request_query != '-', CONCAT('?', @request_query), ''),";
			} else {
				$request_sql = "request = '',
				urlparams = '',";
			}
			
			$field_sql = '';
			foreach($iis_fields as $iis_field) {
				$field_sql .= $fields[trim($iis_field)].", ";
			}
			
			/* date time cs-method cs-uri-stem cs-uri-query cs-username c-ip cs-version cs(User-Agent) cs(Referer) sc-status sc-bytes */
			
			return "LOAD DATA INFILE '{$file}' INTO TABLE `{$profile->tablename}log` FIELDS TERMINATED BY ' ' OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '' LINES TERMINATED BY '\n' IGNORE {$skip_no} LINES 
			({$field_sql} @urlparams, @refparams, @keywords) SET
			
			logdate = STR_TO_DATE(CONCAT(@logdate,' ',@logtime), '%Y-%m-%d %H:%i:%s'), 

			{$request_sql}

			referrer = SUBSTRING_INDEX(@referrer,'?', 1),
			refparams = IF(STRCMP(referrer,SUBSTRING_INDEX(@referrer,'?', -1)),CONCAT('?',SUBSTRING_INDEX(@referrer,'?', -1)),''),

			keywords = IF(STRCMP(0,LOCATE('&q=',@referrer)),urldecode(SUBSTRING_INDEX(SUBSTRING_INDEX(@referrer,'&q=', -1),'&',1)),'')";
		}
		
		function CleanUp() {
			global $db, $profile;
			
			$db->Execute("DELETE FROM `{$profile->tablename}log` WHERE `logdate` IS NULL");
		}
	}
  
?>
