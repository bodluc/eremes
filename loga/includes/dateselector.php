<?php 
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 


function SyncTimezones($profile) {
	global $db;
	# the timezone set in the profile should be used if available
	# both PHP and MYsql should use that timezone if possible
	if ($profile->timezone!="") {
		$setzone = $profile->timezone;
		date_default_timezone_set($setzone);
	} else {
		$setzone = date_default_timezone_get();
	}
	
	@$db->Execute("set time_zone = '".$setzone."'");
    $result = @$db->Execute("SELECT @@session.time_zone;"); 
    if ($data = $result->FetchRow()) {
        if ($data[0]==$setzone) {
           // success
        } else {
           // failure, set it to the gmt zone instead
           $system_warning = "Your PHP timezone is set to '". date_default_timezone_get()."' and the Mysql timezone is set to GMT '+00:00'. This may cause unexpected results when shifts in Daylight Savings Time (DST) occur. For correct handling of DST, please load the time zone tables into your mysql server. See: <a href=\"http://dev.mysql.com/doc/refman/5.1/en/time-zone-support.html\">http://dev.mysql.com/doc/refman/5.1/en/time-zone-support.html</a>. For now, all timezones have been reset to +00:00.";
		   date_default_timezone_set('UCT');
		   $db->Execute("set time_zone = '+00:00'");
        }                    
    } else {
        $system_warning = "Unknown Error: Mysql returned no timezone at all. We failed setting the mysql timezone.";
    }
}


function newDateSelector($from,$to) {
  $minimumDate=date("m/d/Y", $from);
  $maximumDate=date("m/d/Y", $to);
  ?>
  <table style="border-collapse:collapse;vertical-align:top;"
        cellpadding="3" cellspacing="0" border=0>
          <tr>
            <td>
              <div style="text-align:center;">
                <?php echo _DATE_FROM;?> <input class="logaholic_qdate" type="text" name="minimumDate" style="width:75px;" value="<?php echo $minimumDate; ?>" alt="MM/DD/YYYY" onclick="date_selector.showDateSelector('minimumDate');">
                 <?php echo _DATE_TO;?>
                <input class="logaholic_qdate" type="text" name="maximumDate" style="width:75px;"
                value="<?php echo $maximumDate; ?>" alt="MM/DD/YYYY" onclick="date_selector.showDateSelector('maximumDate' );">
              </div>
              <div id="dateSelectorPane" onmouseover="indatediv()" onmouseout="outdatediv()"></div>
              <!-- this is some idiotic fix for the IE problem that select shine though any div, but it doesn't work right-->
              <iframe id="dateSelectorPaneIF" class="dateSelectorPaneIF"></iframe>
            </td>
          </tr>
      </table>
      <script language="javascript" type="text/javascript">
         date_selector.init(window.document.forms['form1']); 
      </script>  
  <?php
}

function DateSelector($from,$to) {
    echo "<table><tr><td>"._DATE_FROM.": ";
    // From Day
    echo "<select name=fday>";
    $i=1;
    $fd = date("d",$from);
    while ($i <=31) {
        if ($i == $fd) {
            $s="selected";
        } else {
            $s="";
        }
        echo "<option value=$i $s> $i";
        $i++;
    }
    echo "</select>";
    
    // From Month
    echo "<select name=fmonth>";
    $i=1;
    $fm = date("m",$from);
    while ($i <=12) {
        if ($i == $fm) {
            $s="selected";
        } else {
            $s="";
        }
        echo "<option value=$i $s> " . date("M",mktime(0,0,0,$i,1,date("Y")));
        $i++;
    }
    echo "</select>";

    // From Year
    echo "<select name=fyear>";
    $i=date("Y")-15;
    $n=$i+30;
    $fy = date("Y",$from);
    while ($i <= $n) {
        if ($i == $fy) {
            $s="selected";
        } else {
            $s="";
        }
        echo "<option value=$i $s> $i";
        $i++;
    }
    echo "</select>";
    echo "</td><td> "._DATE_TO.": ";
    
    // To Day
    echo "<select name=tday>";
    $i=1;
    $td = date("d",$to);
    while ($i <=31) {
        if ($i == $td) {
            $s="selected";
        } else {
            $s="";
        }
        echo "<option value=$i $s> $i";
        $i++;
    }
    echo "</select>";
    
    // To Month
    echo "<select name=tmonth>";
    $i=1;
    $tm = date("m",$to);
    while ($i <=12) {
        if ($i == $tm) {
            $s="selected";
        } else {
            $s="";
        }
        echo "<option value=$i $s> " . date("M",mktime(0,0,0,$i,1,date("Y")));
        $i++;
    }
    echo "</select>";

    // To Year
    echo "<select name=tyear>";
    $i=date("Y")-15;
    $n=$i+30;
    $ty = date("Y",$to);
    while ($i <= $n) {
        if ($i == $ty) {
            $s="selected";
        } else {
            $s="";
        }
        echo "<option value=$i $s> $i";
        $i++;
    }
    echo "</select>";
    echo "</td></tr></table>";
}

function QuickDate($from,$to) {
 global $quickdate;
 
    echo "<select name=quickdate class=\"logaholic_qdate\" onmousedown=\"Qdate();\">";
    if ($quickdate) {
         echo "<option value=\"\"> $quickdate";
    } else {
        echo "<option value=\"\"> "._QUICKDATE."</option>";
    }
    echo "<option value=\""._TODAY."\">"._TODAY."</option>";
    echo "<option value=\""._YESTERDAY."\">"._YESTERDAY."</option>";
    echo "<option value=\""._LAST_24_HOURS."\">"._LAST_24_HOURS."</option>";
    echo "<option value=\""._LAST_7_DAYS."\">"._LAST_7_DAYS."</option>";
    echo "<option value=\""._LAST_14_DAYS."\">"._LAST_14_DAYS."</option>";
    echo "<option value=\""._LAST_30_DAYS."\">"._LAST_30_DAYS."</option>";
    echo "<option value=\""._THIS_MONTH."\">"._THIS_MONTH."</option>";
    echo "<option value=\""._LAST_MONTH."\">"._LAST_MONTH."</option>";
    echo "<option value=\""._LAST_3_MONTHS."\">"._LAST_3_MONTHS."</option>";
    echo "<option value=\""._ALL_TIME."\">"._ALL_TIME."</option>";
    
    echo "</select>";
}

/**
* Timezone settings. We only want to do stuff if it's PHP 5.1+
* We only want to set the timezone if it is actually different 
* from the system timezone, because otherwise mysql will loose 
* DST ability unless the timezone tables are loaded (see mysql manual)
* (Since we can't rely on those tables being loaded, we set it using GMT +-
* but that is probably the reason it looses DST.)
* So, we really only want to mess with this if we really have to.
*/
//this piece of code should be moved really, to common
/**
* Now set up the date variables
*/
$dateFormat = array(
	"format1" => "d",
	"seperator1" => " ",
	"format2" => "M",
	"seperator2" => " ",
	"format3" => "Y",
	"seperator3" => " ",
	"format4" => ""				
);
$dateFormat = serialize($dateFormat);

if (function_exists("date_default_timezone_get")) {    
    if (!isset($profile) && (!empty($conf) || isset($editconf)) && $conf!="newcnf") {  
		$profile = new SiteProfile($conf);
        SyncTimezones($profile);
    } 
}

$from = @$_SESSION['from'];
if(!empty($_REQUEST['from'])) {
	$from = @$_REQUEST["from"];
	$_SESSION["from"]= $from;
}

$to = @$_SESSION['to'];
if(!empty($_REQUEST["to"])) {
	$to = @$_REQUEST["to"];
	$_SESSION["to"]= $to;
}

if (isset($_REQUEST["quickdate"])) { $quickdate = $_REQUEST["quickdate"]; }

if (!isset($quickdate)) {$quickdate="";}

if ($quickdate==_TODAY) {
	 $from   = mktime(0,0,0,date("m"),date("d"),date("Y"));
	 $to     = mktime(23,59,59,date("m"),date("d"),date("Y"));
}
if ($quickdate==_YESTERDAY) {
	 $from   = mktime(0,0,0,date("m"),date("d")-1,date("Y"));
	 $to     = mktime(23,59,59,date("m"),date("d")-1,date("Y"));
}
if ($quickdate==_LAST_24_HOURS) {
    $from   = time() - 86400; 
    $to     = time();
}
if ($quickdate==_LAST_7_DAYS) {
	$to     = mktime(23,59,59,date("m"),date("d"),date("Y"));  
    $from   = $to-(7*86400)+1;
	 
}
if ($quickdate==_LAST_14_DAYS) {
	 $to     = mktime(23,59,59,date("m"),date("d"),date("Y"));
     $from   = $to-(14*86400)+1;
}
if ($quickdate==_LAST_30_DAYS) {
	 $to     = mktime(23,59,59,date("m"),date("d"),date("Y"));
     $from   = $to-(30*86400)+1;
}
if ($quickdate==_THIS_MONTH) {
	 $from   = mktime(0,0,0,date("m"),01,date("Y"));
	 $to     = mktime(23,59,59,date("m"),date("d"),date("Y"));
}
if ($quickdate==_LAST_MONTH) {
	 $from   = mktime(0,0,0,date("m")-1,01,date("Y"));
	 $to     = mktime(23,59,59,date("m")-1,30,date("Y"));
}
if ($quickdate==_LAST_3_MONTHS) {
	 $from   = mktime(0,0,0,date("m")-2,01,date("Y"));
	 $to     = mktime(23,59,59,date("m"),date("d"),date("Y"));
}
if ($quickdate==_ALL_TIME) {
    $from = getStartDate($conf);
	$to = mktime(23,59,59,date("m"),date("d"),date("Y"));
}
if (!$quickdate || $quickdate==_QUICKDATE) {
	if (isset($_REQUEST["fmonth"])) {
		$from   = mktime(0,0,0,$_REQUEST["fmonth"],$_REQUEST["fday"],$_REQUEST["fyear"]);
		$to     = mktime(23,59,59,$_REQUEST["tmonth"],$_REQUEST["tday"],$_REQUEST["tyear"]);
	} else if (isset($_REQUEST["minimumDate"])) {
		$minD = urldecode($_REQUEST["minimumDate"]);
		$maxD = urldecode($_REQUEST["maximumDate"]);
		$cformat = GetCustomDateFormat("PHP",true);
		$format = array();
		foreach($cformat as $k => $v){
			if(strpos($k,"seperator") !== FALSE){
				$minD = str_replace($v,",",$minD);
				$maxD = str_replace($v,",",$maxD);				
			}else{
				$format[] = $v;
			}
		}
        $fromparts = explode(",", $minD);
        $toparts = explode(",", $maxD);
		$fparts = array();
		$tparts = array();
		$i = 0;
		if(count($fromparts) == count($format)){
			foreach($format as $f => $p){		
				if($p == "M"){
					for($ij=1;$ij<=12;$ij++){
						if(strtolower(date("M", mktime(0, 0, 0, $ij, 1, 0))) == strtolower($fromparts[$i])){
							$fparts["month"] = $ij;
						}
						if(strtolower(date("M", mktime(0, 0, 0, $ij, 1, 0))) == strtolower($toparts[$i])){
							$tparts["month"] = $ij;
						}
					}
				}
				if($p == "m"){
					$fparts["month"] = $fromparts[$i];
					$tparts["month"] = $toparts[$i];
				}
				if($p == "d"){
					$fparts["day"] = $fromparts[$i];
					$tparts["day"] = $toparts[$i];
				}
				if($p == "Y"){
					$fparts["year"] = $fromparts[$i];
					$tparts["year"] = $toparts[$i];
				}
				$i ++;
			}
			$from = mktime(0,0,0,$fparts["month"],$fparts["day"],$fparts["year"]);
			$to = mktime(23,59,59,$tparts["month"],$tparts["day"],$tparts["year"]);
		}else{
			$from = strtotime($_REQUEST['minimumDate']." 00:00:00");
			$to = strtotime($_REQUEST['maximumDate']." 23:59:59");
		}
        $tparts="";
		if(empty($reporting)) {
			if(!empty($from)) { $_SESSION["from"]=$from; }
			if(!empty($to)) { $_SESSION["to"]=$to; }
		}		
	} else {
		//$from   = mktime(0,0,0,date("m"),(date("d")-2),date("Y"));
		//$to     = mktime(23,59,59,date("m"),date("d"),date("Y"));
	}
}
if (!isset($from) && !isset($fmonth)) {
	//default values
	$from   = mktime(0,0,0,date("m"),01,date("Y"));
	$to     = mktime(23,59,59,date("m"),date("d"),date("Y"));
}

if ($to < $from) {
    // haha, we can't do this, reset from to be the same day as to
    $from = mktime(0,0,0,date("m",$to),date("d",$to),date("Y",$to));
}