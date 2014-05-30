<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This class will update the country info for each record in the main profile table.
*/

class UpdateCountries {
    
    var $showprogress = false;
    
    function __construct() {
        global $gi;
        set_time_limit(86400);
        #check to see if geoip is loaded
		if (!isset($gi)) {
			include_once("components/geoip/open_geoip.php");
		}
        if (!isset($gi)) {
                echo "GeoIP functions not loaded! We can't do anything without them.";    
        } 
    }
    function UpdateCountries() {
        $this->__construct();    
    }
    
    # call this function if you want to update part of the database, based on a date range
    function UpdateRange($from,$to) {
        global $profile, $db, $gi;
		echo $gi->databaseType;
		// exit();
        // include_once("components/geoip/geoipcity.inc");
        # get a list of IP numbers to check
		//$db->Execute("SET SESSION group_concat_max_len = 100000");
        $q = "select group_concat(id) visitorid, ipnumber from $profile->tablename_visitorids where created between $from and $to group by ipnumber";  
        $results = $db->Execute($q);
        $n = $results->NumRows();
        echo "<script>document.getElementById(\"patience\").innerHTML+=\" (Checking a total of $n IP addresses)\";</script>";
        $i=0; $updated=0; $last_progress=-1; $unknown=0;
        while ($data=$results->FetchRow()) {
            $data['visitorid'] = "'".str_replace(",","','",$data['visitorid'])."'";
            if (is_numeric(substr($data["ipnumber"],-1))) {
                # get the current country code from geoip and compare it to what we have in the database for this ip
                if (function_exists('encodeSPQR')) { $data["ipnumber"] = decodeSPQR($data["ipnumber"]); }
                $cc = geoip_country_code_by_addr_raw($gi, $data["ipnumber"]);
                $dbc = $this->GetCountry($data['visitorid'],$from,$to);
				//echo "$cc, $dbc<br>";
                if ($dbc===false) {
                    # visitorid was not found in the main table, it has probably been removed. We can either remove it from the visitorid table, or just skip it
                } else if ($cc!=$dbc) {
                    # the country is different, lets update the database
                    $uq = "update $profile->tablename set country = '$cc' where visitorid IN ({$data["visitorid"]}) and timestamp between $from and $to";
                    $db->Execute($uq);
                    //echo $uq;
                    $updated++;        
                }
            }
            $i++;
            if ($this->showprogress == true) {
                $progress = number_format((($i / $n)*100),0);
                if ($progress!=$last_progress) {
                    $this->Progress("$progress");
                    $last_progress = $progress;
                }
            }
        }
        $this->Progress("Done! Updated $updated entries out of a total of $n.");   
    }
    
    # call this function if you want to update the entire database
    function UpdateAll() {
        global $profile;
        $daterange = GetMaxDateRange($profile);
        $this->UpdateRange($daterange['from'],$daterange['to']);     
    }
    
    # call this function if you want to display a progress indicator
    function DisplayProgress() {
        global $profile;
        $this->showprogress = true;
        //ob_start();
        echo "<h3>Update Geographic Location information for '$profile->profilename'</h3><span id=\"patience\">Please be patient, this process can take a while.</span><br><br>";
        echo "Progress: <span id=\"UpdateCountriesStatus\">Updating Country Code information ...</span> <div style=\"width:640px;margin-top:5px;\" id=\"UpdateCountriesProgress\"></div>";
        ?> <script type="text/javascript">$("#UpdateCountriesProgress").progressbar({ value: 0 });</script> <?php
        flush();
        //lgflush();
    }
    
    private function Progress($string) {
        echo "<script type=\"text/javascript\">";
        if (is_numeric($string)) {
            echo '$( "#UpdateCountriesProgress" ).progressbar( "option", "value", '.$string.' );';
            $string .= " %"; 
        }
        echo "document.getElementById(\"UpdateCountriesStatus\").innerHTML=\"$string\";";
        echo "</script>\n";
        echo "<!-- this is filler to make it flush the buffer                                                                          -->\n";
        flush();
        //lgflush();   
    }
    
    private function GetCountry($vid,$from,$to) {
        global $db, $profile;
        $q  = "select country from $profile->tablename where visitorid IN ($vid) and timestamp between $from and $to limit 1";
        $r = $db->Execute($q);
        if ($data=$r->FetchRow()) {
            return $data['country'];    
        } else {
            return false;    
        }
    }    
   
}

?>

