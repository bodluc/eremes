<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays a overview of your top cities
*/
$reports["_TOP_CITIES"] = Array(
	"ClassName" => "TopCities", 
	"Category" => "_INCOMING_TRAFFIC", 
	"icon" => "images/icons/32x32/topcities.png",
	"Options" => "daterangeField,country,columnSelector",
	"Filename" => "top_cities",
	"Distribution" => "Standard",
	"Order" => 10,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => false
);

class TopCities extends Report {

	function Settings() {
		if (isset($this->outputmode) && $this->outputmode=="xml") {
			$this->showfields = _CITY.','._VISITORS.',longitude,latitude';
		} else {
			$this->showfields = _CITY.','._VISITORS;	
		}
		$this->help = _TOP_CITIES_DESC;
	}
	
	function CreateData() {
		global $db, $gi, $cnames;
		
		if(empty($_REQUEST["country"])){
			$_REQUEST["country"] = "";
		}
		$this->addlabel=@$cnames[$_REQUEST["country"]]. " (".$_REQUEST["country"].")";
		$preq  = "select v.ipnumber from {$this->profile->tablename} as a, {$this->profile->tablename_visitorids} as v where timestamp >=". $db->quote($this->from) ." and timestamp <=". $db->quote($this->to) ." and crawl=0 and country=". $db->quote($_REQUEST["country"]) ." and a.visitorid=v.id group by a.visitorid";
		//echo $preq;
		
		$loop = $db->Execute($preq);
		$newdata = array();
		while ($loopdata=$loop->FetchRow()) {
			if (is_numeric(substr($loopdata["ipnumber"],-1))) {
                $area=geoip_record_by_addr($gi, $loopdata["ipnumber"]);
			    //$city=$area->city;
				if(empty($area->longitude) || empty($area->latitude)) { break; }
			    $longitude = $area->longitude;
			    $latitude = $area->latitude;
                $city = iconv("ISO-8859-1","UTF-8", $area->city);
			    if ($city=="") {
				    $city=_UNKNOWN;
			    } else {
					if($area->country_code == "US") {
						$city .= " ({$area->region})";
					}
				}
				
			    //echo "lala $city";
			    $newdata[$city] = @$newdata[$city] + 1;
			    $posdata[$city]['longitude'] = @$longitude;
			    $posdata[$city]['latitude'] = @$latitude;
            }
		}
		arsort($newdata);
		
		// now merge it in the stats table format
		$i=0;
		while (list ($key, $val) = each ($newdata)) {
			//echo "$key $val<br>";
			$data[$i][0] = $key;
			$data[$i][1] = $val;
			$data[$i][2] = $posdata[$key]['longitude'];
			$data[$i][3] = $posdata[$key]['latitude'];
			$i++;
		}
		
		$query="data array";
		
        if (isset($data)) {
		    return $data;
        } else {
            $this->showfields="Notice";
            $data[0][0]= _NO_DATA_FOR_THIS_DATE_RANGE;
            return $data;
        }
	}
	

	function DisplayCustomForm() {
		global $cnames;
		echo "<label for='country'>"._COUNTRY."</label><select class='report_option_field' id='country'>";
		foreach($cnames as $country_code => $countryname) {
			echo "<option"; if(!empty($this->country)) { if($country_code == $this->country) { echo " selected "; } } echo " value='{$country_code}'>{$countryname}</option>";
		}
		echo "</select>";
	}
}
?>
