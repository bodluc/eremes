<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
//PHPCIPHER NO ENCRYPT
// PHPLOCKITOPT NOENCODE
  
  // Param 1 = long description, 2 = class name (must match the class in this file!), 3 = if this can be used to auto-discover the format type.
	$log_parser_types[] = Array("order" => "6", "Description" => "Microsoft ISAS", "ClassName" => "ISASLogParser", "AutoDiscoverable" => true);

	class ISASLogParser extends GenericLogParser {
		
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
        var $fldIdxCookie;
			
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
			$flogline=str_replace(": ",":\t", $flogline);
            $fields=explode("\t",$flogline);
            //print_r($fields);
			
			// Set up the variables for the field indexes
            // #Fields: c-ip    cs-username    c-agent    date    time    s-computername    cs-referred    r-host    r-ip    r-port    time-taken    cs-bytes    sc-bytes    cs-protocol    s-operation    cs-uri    s-object-source    sc-status    rule    FilterInfo    cs-Network    sc-Network    error-info    action    AuthenticationServer
            // #192.168.107.50    anonymous    bls     2009-03-21    00:01:31    HCSE01ISA1    -    hcse01isa1    192.168.10.202    8080    1    145             5082        http           GET      http://hcse01isa1/wpad.dat    -    200    -    Req ID: 10129c7e; Compression: client=No, server=No, compress rate=0% decompress rate=0%    Internal    -    0x0    Allowed    -
            
            
            
			$this->fldIdxIP = array_search("c-ip",$fields)-1;
            $this->lasterrormessage .= "<P>this fldIdxIP is ". (array_search("c-ip",$fields)-1);    
			$this->fldIdxAuthUser = array_search("cs-username",$fields)-1;
			$this->fldIdxDate = array_search("date",$fields)-1;
			$this->fldIdxTime = array_search("time",$fields)-1;
			$this->fldIdxURI = array_search("cs-uri",$fields)-1;
			$this->fldIdxQuery = array_search("cs-uri-query",$fields)-1;
			$this->fldIdxStatus = array_search("sc-status",$fields)-1;
			$this->fldIdxBytes = array_search("sc-bytes",$fields)-1;
			$this->fldIdxReferer = array_search("cs-referred",$fields)-1;
			$this->fldIdxAgent = array_search("c-agent",$fields)-1;
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
			
			$iisline = explode("\t", $line);
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
	}
  
?>
