<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
Class editVisitor{
	function EditIpnumber($ip){
		$data = $this->getData("ipnumber",$ip);
		if(!empty($data)){
			$this->CreateForm($data);
		}
	}
	function EditVisitorId($id){
		$data = $this->getData("id",$id);
		if(!empty($data)){
			$this->CreateForm($data);
		}
	}
	function getData($where,$value){
		global $db, $profile, $gi;
		$data = array();
		
		$query = "select * from  {$profile->tablename_visitorids} where $where ='$value'";
		$result = $db->Execute($query);
		$i = 0;
		while($row = $result->FetchRow()){
			$info = $this->getInfo($row["id"]);
			$data[$i]["id"] = $row["id"];
			$data[$i]["visitor"] = $row["visitorid"];
			$data[$i]["ip"] = $row["ipnumber"];
			$data[$i]["label"] = $row["customlabel"];
			$data[$i]["created"] = $row["created"];
			$data[$i]["crawl"] = $info['crawl'];
			$data[$i]["agent"] = $info['useragent'];
			$data[$i]["country"] = $info['country'];
			$area = geoip_record_by_addr($gi, $row["ipnumber"]);
			$loc_string = "";
			if(!empty($area->city)) { $city= iconv("ISO-8859-1","UTF-8", $area->city); $loc_string .= $area->city . ", "; }
			if(!empty($area->region)) { $loc_string .= $area->region .", "; }
			if(!empty($area->country_name)) { $loc_string .= $area->country_name; }
			$data[$i]["location"] = $loc_string;
			$i ++;
		}
		return $data;
	}	
	
	function getInfo($id) {
		global $db, $profile;
		$query = "select * from {$profile->tablename} as a, {$profile->tablename_useragents} as u where a.visitorid='$id' and a.useragentid=u.id order by timestamp desc limit 1";	
		$result = $db->Execute($query);
		if ($row = $result->FetchRow()) {
			return $row;
		}
		return array();
		
	}
	
	function CreateForm($data){		
		foreach($data as $k => $v){				
			echo "<div class='visitor-info-field'>";				
				echo "<form action='' method='POST'>";
					echo "<input type='hidden' name='visid' value='{$v["id"]}' />";
					echo "<b>Visitor info:</b> (ID: {$v["visitor"]}) ";
					
					echo "<table>";
						echo "<tr><td>Useragent:</td><td>{$v["agent"]}</td></tr>";
						echo "<tr><td>"._IP_NUMBER.":</td><td>{$v["ip"]}</td></tr>";
						echo "<tr><td>"._LOCATION.":</td><td>{$v["location"]} ";
						if(!empty($v["country"])){	
							echo "<img src='images/flags/".strtolower($v["country"]).".png' width='16px' />";
						}
						echo "</td></tr>";
						echo "<tr><td>"._IDENTIFY.":</td>";
						echo "<td><select name='setting'>";
						if($v["crawl"] == 1){
							echo "<option value='bot'>"._BOTS."</option><option value='human'>"._HUMAN."</option>";
						}else{
							echo "<option value='human'>"._HUMAN."</option><option value='bot'>"._BOTS."</option>";				
						}
						echo "</select></td></tr>";
						echo "<tr><td>"._LABELNAME.":</td><td><input type='text' name='label' value='{$v["label"]}' /><input type='submit' value='"._SAVE."' style='margin-left:10px;'/></td></tr>";
					echo "</table>";
				echo "</form>";
			echo "</div>";
		}
	}
	function updateLabel($id, $label){
		global $db, $profile;
		$query = "UPDATE {$profile->tablename_visitorids} SET customlabel=".$db->Quote($label)." where id = '$id'";
		$db->Execute($query);
	}
	function updateSetting($crawl,$id){
		global $db, $profile;
		if (!empty($id)) {
			$query = "UPDATE {$profile->tablename} SET crawl=$crawl where visitorid = '$id'";
		}
		$db->Execute($query);
	}
}
?>