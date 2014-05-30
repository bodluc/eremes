<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays the feedburner
*/
$reports["_FEEDBURNER"] = Array(
	"ClassName" => "FeedBurner", 
	"Category" => "_SOCIAL_MEDIA", 
	"icon" => "images/icons/32x32/feedburnerstats.png",
	"Options" => "daterangeField,columnSelector",
	"Filename" => "feedburner",
	"Distribution" => "Standard",
	"Order" => 2,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class FeedBurner extends Report {

	function Settings() {
		$this->showfields = _PAGE.','._VISITORS.','._PAGEVIEWS;
		$this->help = _TOP_FEEDS_DESC;
	}
	
	function CreateData() {
        if (!function_exists('simplexml_load_file')) {
            $this->showfields = _ERROR;
            $data[0][0] = _REQUIRE_SIMPLEXML;   
        } else if (!extension_loaded('openssl')) {
            $this->showfields = _ERROR;
            $data[0][0] = _REQUIRE_OPENSSL;     
        } else {
            $help=_FEEDBURNER_DESC;
            $this->showfields = _DATE.","._CIRCULATION.","._HITS.","._REACH;
            $dates = date("Y-m-d",$this->from).",".date("Y-m-d",$this->to);
			if(extension_loaded("openssl")) {
				try {
					$xml = @simplexml_load_file("https://feedburner.google.com/api/awareness/1.0/GetFeedData?uri={$this->profile->feedburneruri}&dates={$dates}");
				} catch(Exception $err) {
					$this->showfields = _ERROR;
					$data[0][0] = "Your protocol seems to be invalid.";
				}
			} else {
				return false;
			}
            $i=0;
            $data = array();
            while ($i < count($xml->feed->entry)) {
               $data[$i][0] = $xml->feed->entry[$i]['date'];
               $data[$i][1] = $xml->feed->entry[$i]['circulation'];
               $data[$i][2] = $xml->feed->entry[$i]['hits'];
               $data[$i][3] = $xml->feed->entry[$i]['reach'];
               $i++;       
            }
        }
		
		return $data;
	}
}
?>
