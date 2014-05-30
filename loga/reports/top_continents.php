<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays a overview of your top continents
*/
$reports["_TOP_CONTINENTS"] = Array(
	"ClassName" => "TopContinents", 
	"Category" => "_INCOMING_TRAFFIC", 
	"icon" => "images/icons/32x32/topcontinents.png",
	"Options" => "daterangeField,columnSelector",
	"Distribution" => "Standard",
	"Filename" => "top_continents",
	"Order" => 10,
	"hidden" => true,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class TopContinents extends Report {

	function Settings() {
        $this->showfields = _CONTINENT.','._VISITORS.','._PAGEVIEWS.','._PAGES_PER_USER;
		$this->help = "";
	}
	
	function CreateData() {
		global $db, $gi, $cnames;
		
		$continents = array(
            "North America" => "AI,AG,AW,BS,BB,BZ,BM,VG,CA,KY,CR,CU,DM,DO,SV,GL,GD,GP,GT,HT,HN,JM,MQ,MX,MS,AN,NI,PA,PR,BL,KN,LC,MF,PM,VC,TT,TC,US,VI",
            "South America" => "AR,BO,BR,CL,CO,EC,FK,GF,GY,PY,PE,SR,UY,VE",
            "Europe" => "AX,AL,AD,AT,BY,BE,BA,BG,HR,CZ,DK,EE,FO,FI,FR,DE,GI,GR,GG,VA,HU,IS,IE,IM,IT,JE,LV,LI,LT,LU,MK,MT,MD,MC,ME,NL,NO,PL,PT,RO,RU,SM,RS,SK,SI,ES,SJ,SE,CH,UA,GB",
            "Africa" => "DZ,AO,BJ,BF,BI,CM,CV,CF,TD,KM,CD,CG,CI,DJ,EG,GQ,ER,ET,GA,GM,GH,GN,GW,KE,LS,LR,LY,MR,MU,YT,MG,MW,ML,MA,MZ,NA,NE,NG,RE,SH,RW,ST,SN,SC,SL,SO,ZA,SD,SZ,TZ,TG,TN,UG,EH,ZM,ZW",
            "Asia" => "AF,AM,AZ,BH,BD,BT,IO,BN,KH,CN,CX,CC,CY,GE,HK,IN,ID,IR,IQ,IL,JP,JO,KZ,KP,KR,KW,KG,LA,LB,MO,MY,MV,MN,MM,NP,OM,PK,PS,PH,QA,SA,SG,LK,SY,TW,TJ,TH,TL,TR,TM,AE,UZ,VN,YE",
            "Oceania" => "AS,AU,CK,FJ,PF,GU,KI,MH,FM,NR,NC,NZ,NU,NF,MP,PW,PG,PN,WS,SB,TK,TO,TV,UM,VU,WF",
            "Antarctica" => "AQ,BV,TF,HM,GS"
        );
        
		$prequery  = "select country, count(distinct visitorid) as ips,count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from {$this->profile->tablename} where timestamp >=". $db->quote($this->from) ." and timestamp <=". $db->quote($this->to) ." and crawl=0 and country!='' group by country order by ips desc limit ". addslashes($this->limit);
		if (@$_SESSION["trafficsource"]) { $prequery = subsetDataToSourceID($prequery); }
        
        $q = $db->Execute($prequery);
		while($result = $q->FetchRow()) {
			foreach($continents as $continent=>$lands) {
				if(strpos($lands,$result['country']) !== false) {
					$c[$continent]['name'] = $continent;
					$c[$continent]['ips'] = (@$c[$continent]['ips'] + $result['ips']);
					$c[$continent]['hits'] = (@$c[$continent]['hits'] + $result['hits']);
					$c[$continent]['ppu'] = (@$c[$continent]['ppu'] + $result['ppu']);					
				}
			}
		}
        
        # check for continents that have no stats, and add them with (or the flash map will get messed up)
        foreach($continents as $continent=>$lands) {
            if (!isset($c[$continent]['name'])) {
                $c[$continent]['name'] = $continent;
                $c[$continent]['ips'] = 0;
                $c[$continent]['hits'] = 0;
                $c[$continent]['ppu'] = 0;                
            }    
        }
        
		$i = 0;
		$data = array();
		foreach($c as $continent_name=>$value_array) {
			$ii = 0;
			foreach($value_array as $key=>$value) {
				$data[$i][$ii] = $value;
				$ii++;
			}
			$i++;
		}
		
		return $data;
	}
}
?>
