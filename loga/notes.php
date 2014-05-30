<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 

//functions for the notes programs
function SelectBox() {
  global $conf,$to,$from,$limit,$profile,$donote;
  // Create Date Selector  
 echo "<div style=\"position:relative;width:100%;z-index:10;float:left;\">";
  echo "<form method=get action=notes.php id=\"form1\" name=\"form1\"><table border=0><tr><td width=70><b>"._DATE_RANGE.": </b></td><td>";
  QuickDate($from,$to);
  echo "</td><td>";
  newDateSelector($from,$to);
  echo "<input type=hidden name=conf value=\"$conf\">";
  echo "<input type=hidden name=donote value=\"$donote\">";
    
  echo "</td><td><input type=submit name=submitbut value=Report class=small><input type=hidden name=but value=Report> "; 
 
  echo "</td></tr></table>";
  /*
  echo "<div id=\"advancedUI\" style=\"display:none;position:relative;\">";
  
  echo "<table border=0>";
  echo "<tr><td width=70><b>Filters:</b></td><td>";
  //echo printTrafficSourceSelect();
  echo "Not available on Today report";
  echo "</td></tr><tr><td colspan=2> </td></tr><tr>";
  echo "<td title=\"Max Number of results to show\">";
  if (!$limit) {
       $limit=100;
  }
  echo "<b>Limit:</b> </td><td><input type=text size=3 name=limit value=\"$limit\"  class=small> (Maximum Number of results to show)";
  
  echo "</td></tr></table><br>";
  echo "</div>";
  */
  ?>  
  </td></tr></table>
  <?php
  echo "</div><br><hr noshade size=1 width=100% style=\"float:left;\">";
}

function CreateNote() {
	global $conf,$timestamp,$note,$noteid;
	if (!$timestamp) {
		$timestamp = time();
	}
	$date = date("Y-m-d",$timestamp);
	?>
	<div class='indentbody' style="float: left;">
	<table ><tr><td valign=top colspan=2>
  <h3><img src="images/icons/note_add.gif" width=16 height=16 align=left alt="Notes"> &nbsp;<?php echo _NOTES_FOR;?> <?php echo $conf;?></h3>
  </td></tr><tr><td valign=top>
	
	<table width="400" cellpadding=3 cellspacing=0 class=small border=0><tr bgcolor=#FFFFCC class=small><td class=toplineyellow colspan=2 valign=top>
	<form method=get action=notes.php>
		<input type=hidden name=conf value="<?php echo $conf;?>">
		<input type=hidden name=noteid value="<?php echo $noteid;?>">
		<input type=hidden name=timestamp value="<?php echo $timestamp;?>">
		<strong><?php echo _CREATE_A_NOTE;?>:</strong></td></tr>
	<tr><TD><?php echo _DATE_YMD;?>: </TD><td><input type="text" name="date" value="<?php echo $date;?>"></td></tr>
		<tr><TD><?php echo _NOTE_255_MAX;?>:</TD><td>
		<textarea cols=30 rows=2 name="note" maxlength="255"><?php echo $note;?></textarea></TD></tr>
		<tr><TD colspan=2>
		<input type="submit" name="donote" value="Save"><P>
		<font color=gray><?php echo _NOTES_EXPLAIN;?></font><P>
		
		
		</TD></tr></table>
	</form>
	</td>	<td valign=top><?php CheckNotes("10000");?></td>
	</tr></table>
	</div>

	<?php
}

function EditNote() {
	global $conf,$timestamp,$note,$noteid,$db;
	
	$q = $db->Execute("select * from ".TBL_NOTES." where id='$noteid'");
	if ($data = $q->FetchRow()) {
		$noteid=$data["id"];
		$timestamp=$data["timestamp"];
		$note=$data["note"];
		CreateNote();
	}
	
}

function DelNote() {
	global $conf,$noteid,$mysqlprefix,$db;
	
	$db->Execute("delete from ".TBL_NOTES." where id='$noteid'");
	echo "<div class=\"indentbody\"><h3>"._NOTES_FOR." $conf</h3><strong>"._NOTE_DELETED."</strong><P><a class=nodec3 href=\"notes.php?conf=$conf&amp;donote=create\">"._MORE_NOTES."</a></div>";
}

function SaveNote() {
	global $conf,$timestamp,$date,$note,$noteid;
	global $db;
	
	$datepart=explode("-",$date);
	if ($date!=date("Y-m-d",$timestamp)) {
		$timestamp=mktime(12,0,0,$datepart[1], $datepart[2],$datepart[0]);
	}
	
	$record = array();
	$record["profile"] = $conf;
	$record["timestamp"] = $timestamp;
	$record["note"] = $note;
	
	if ($noteid) {
		$db->AutoExecute(TBL_NOTES, $record, "UPDATE", "id = '$noteid'");
		echo "<div class=\"indentbody\"><h3>"._NOTES_FOR." $conf</h3><strong>"._NOTE_UPDATED."</strong>:<p>$note<P><a class=nodec3 href=\"notes.php?conf=$conf&amp;donote=create\">"._MORE_NOTES."</a></div>";
	} else {
		$db->AutoExecute(TBL_NOTES, $record, "INSERT", "id = '$noteid'");
		echo "<div class=\"indentbody\"><h3>"._NOTES_FOR." $conf</h3><strong>"._NOTE_ADDED."</strong>:<p>$note<P><a class=nodec3 href=\"notes.php?conf=$conf&amp;donote=create\">"._MORE_NOTES."</a></div>";
	}
}

//---- start main Notes program -----//
$noteid = @$_REQUEST["noteid"];
$note = @$_REQUEST["note"];
$timestamp = @$_REQUEST["timestamp"];
$date = @$_REQUEST["date"];
$donote = @$_REQUEST["donote"];

if ($donote=='check') {
	CheckNotes();
} else {
	include_once "common.inc.php";

	if(!isset($profile) && isset($conf)) {
		$profile = new SiteProfile($conf);
	}

	if(!isset($headAddition)) { $headAddition = ''; }

	$template->HTMLheadTag($headAddition); // The default content of the <head> tag, including an optional addition.

	$template->BodyStart();

	$template->LoginForm(); // Display a Login Form, if needed.


	if ($donote=='create') {
        SelectBox(); 
		CreateNote();
	} else if ($donote=='edit') {
		EditNote();
	} else if ($donote=='del') {
		DelNote();
	} else if ($donote=='Save') {
		SaveNote();
	}
}
?>
</body>
</html>
