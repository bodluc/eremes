<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/* 
................................................
.......... Main program starts here ............
................................................
*/
// Pull in variables that may have been passed in with the URL.

$print = @$_REQUEST["print"];
$nocache = @$_REQUEST["nocache"];
$email = @$_REQUEST["email"];
$submit = @$_REQUEST["but"];
$showfields = @$_REQUEST["showfields"];
$filter = @$_REQUEST["filter"];
$item = @$_REQUEST["item"];
$item2 = @$_REQUEST["item2"];
$drilldown = @$_REQUEST["drilldown"];
$labels = @$_REQUEST["labels"];
$status = @$_REQUEST["status"];
$agent = @$_REQUEST["agent"];
$testid = @$_REQUEST["testid"];
$edit = @$_REQUEST["edit"];
$roadto = urldecode(@$_REQUEST["roadto"]); 
$includereport = @$_REQUEST["includereport"];
$divlabel = @$_REQUEST["divlabel"]; 
$testname=@$_REQUEST['testname'];
$testurl=@$_REQUEST['testurl'];
$testdesc=@$_REQUEST['testdesc'];
$splitperc=@$_REQUEST['splitperc'];
$spt = @$_REQUEST["spt"];
$visitormethod = @$_REQUEST["visitormethod"];
$tn = @$_REQUEST["tn"];

require_once "common.inc.php";

if (isset($print)) {
 $noheader=1;
}


if ($spt=="savefile") {
  $filename=$conf."-splittest$testid".".php";
  ob_start();
  header("Window-target: _blank");
  header("Content-type: application/x-download");
  header("Content-Disposition: attachment; filename=$filename");
  header("Content-Transfer-Encoding: binary");
  $fname=realpath("testtemplate.php");
  $content = file_get_contents($fname);
  $content=str_replace("replace_db_name",$DatabaseName,$content);
  $content=str_replace("replace_mysql_servername",$mysqlserver,$content);
  $content=str_replace("replace_mysql_user",$mysqluname,$content);
  $content=str_replace("replace_mysql_password",$mysqlpw,$content);
  $content=str_replace("replace_profile_name",$conf,$content);
  $content=str_replace("replace_testid",$testid,$content);
  $content=str_replace("replace_visitormethod",$visitormethod,$content);
  $content=str_replace("replace_tablename",$tn,$content); 
  print $content;
  ob_end_flush();
  exit();
}

require "queries.php";
$nicefrom = date("D, d M Y / H:i",$from);
$niceto = date("D, d M Y / H:i",$to);

if (!$includereport) {
    require "top.php";
    
    ?>
    <div id="loading" style="position:absolute;left:100px;visibility:visible;">
    &nbsp;<P>
    <TABLE bgcolor=white cellspacing=0 cellpadding=3>
    <TR><TD class=toplinegreen><FONT size="+1"><?php echo""._BUILDING_REPORT;?></FONT></TD></TR>
    <TR><TD bgcolor="#f0f0f0" class=dotline valign=middle><IMG src="images/Hourglass_icon.gif" width=32 height=32 alt="" border="0" align=left vspace=4 hspace=4>
    <?php echo""._WAIT_WHILE_REPORT_IS_BEING_CREATED;?><BR>
    <?php echo""._NOW_CALCULATING;?> <?php echo "<b>"._TEST_RESULT."</b> "._DATE_FROM."<br> $nicefrom "._DATE_TO." $niceto"; ?></B>
    </TD></TR>
    </TABLE>
    </div>
    <?php
    echo "<script language=javascript>function syncpane(side) { if (side=='b') {  document.editor.paneb.scrollTop = document.editor.panea.scrollTop; } else { document.editor.panea.scrollTop = document.editor.paneb.scrollTop;  }}</script>";
    ?>
    <script language=javascript>
    function togglewrap() {
    if (document.editor.wr.checked==true) {
      document.editor.panea.wrap='soft';
      document.editor.paneb.wrap='soft';
    } else {
      document.editor.panea.wrap='off';
      document.editor.paneb.wrap='off';
    }
    
    }
    </script>
    <?php
    
} else {
    $profile=new SiteProfile($conf);
    if ($testid) {
        setProfileData($profile->profilename, "$profile->profilename.last_testid", $testid);
    }
    ?>  
    <?php
}

//split test functions
$sptablename=$profile->tablename."_splittests";
$sptablename_results=$profile->tablename."_splittests_results";


function Gettestid() {
  global $conf,$profile,$sptablename,$testid,$roadto, $db,$from,$to;  
  $q = @$db->Execute("select id,testname from $sptablename where status=1 limit 1"); 
  $data=@$q->FetchRow();
  return $data[0]; 
}

function SelectBox() {
  global $conf,$to,$from,$limit,$labels,$roadto,$testid,$profile;
  // Create Date Selector  
  echo "<div class='form1-wrap'><form method=post action=testcenter.php id=\"form1\" name=\"form1\">";
	echo "<table border=0><tr><td><b>". _DATE_RANGE .":</b>";
	QuickDate($from,$to);
	echo "</td><td>";
  newDateSelector($from,$to);
	echo "<input type=hidden name=conf value=\"$conf\">";
	echo "<input type=hidden name=labels value=\"$labels\">";
	echo "</td><td>";  
    makeTargetSelect();

  echo "</td><td><input type=submit name=submitbut value=Report><input type=hidden name=but value=Report><input type=hidden name=spt value=report><input type=hidden name=testid value=$testid>";
  echo "</td></tr></table></form></div><hr noshade size=1 width=100% style=\"float:left;\">";
}

function makeTargetSelect() {
  global $profile,$roadto;
  echo ""._TARGET.": <select name=roadto>";
  if ($profile->targetfiles) {
      $targets=explode(",",$profile->targetfiles);
      foreach ($targets as $thistarget) {
          if ($thistarget) {
              if (trim($thistarget)==$roadto) { $sel="SELECTED"; } else { $sel=""; }
              echo "<option $sel value=\"".trim($thistarget)."\">".trim($thistarget)."</option>\n";
          }
      }
    if (!$sel && !$roadto) {
          $roadto=$targets[0];
      }
      
      if (!$sel && !$roadto) {
  		echo "<option SELECTED value=\"\">"._NONE."</option>\n";
      } else {
  		echo "<option value=\"\">"._NONE."</option>\n";
      }
  }
  echo "</select>";    
}

function makeAjaxTargetSelect() {
  global $profile,$roadto,$divlabel,$testid, $conf, $from,$to;
  //echo "Target: <select name=roadto>";
  echo "&nbsp;&nbsp;&nbsp;Target: <select id=\"targetselector\" style=\"background:#f0f0f0;border:1px solid gray;\" onChange=\"showAjaxGet('testcenter.php?conf=$conf&to=$to&from=$from&includereport=1&divlabel=$divlabel&spt=report&testid=$testid&roadto='+encodeURI(this.options[this.selectedIndex].value), '$divlabel')\">"; 
  
  if ($profile->targetfiles) {
      $targets=explode(",",$profile->targetfiles);
      foreach ($targets as $thistarget) {
          echo "<option value=\"".urlencode($thistarget)."\"";
          if ($thistarget==$roadto) { echo "SELECTED "; }
          echo ">$thistarget</option>";
          $i++;
      }
  }
  echo "</select>";    
}


function TestReportToggle() {
  global $conf,$profile,$sptablename,$testid,$roadto, $db,$from,$to,$divlabel;  
  $q = @$db->Execute("select id,testname from $sptablename where status=1"); 
  echo "Test: <select id=\"testselector\" style=\"background:#f0f0f0;border:1px solid gray;\" onChange=\"showAjaxGet('testcenter.php?conf=$conf&to=$to&from=$from&includereport=1&divlabel=$divlabel&roadto=".urlencode($roadto)."&spt=report&testid='+this.options[this.selectedIndex].value, '$divlabel')\">";
   
  $i=0;
  while (($q) && ($data=@$q->FetchRow())) {
    echo "<option value=\"{$data['id']}\"";
    if ($data['id']==$testid) { echo " SELECTED "; }
    echo ">{$data['testname']}</option>";
    $i++;
  }
  echo "</select>";
}



function Sidepanel() {
 global $conf,$profile,$sptablename,$testid,$roadto, $db,$from,$to,$spt;
  $q = @$db->Execute("select id,testname from $sptablename where status=1");
  if (!$q) {
      // Probably no table.  When we create a test, we'll create the table.
  }
  echo "</td></tr><tr><td rowspan=5 valign=top width=180>";
  echo "<div style=\"float:left;width:180px;margin-top:2px;border:0px solid red;\">"; 
  $i=0;
  $lastcat=_TEST_CENTER;
  $active=0;
  
  echo "<div id=\"accordion\"> <h3 class=\"accordion_header_first\"><a href=\"#\">"._TEST_CENTER."</a></h3>";
  echo "<div class=\"reportmenu\"><ul>";    
  $icon = "images/icons/savedtest.gif";
  echo " <li> <a href=\"testcenter.php?conf=$conf&spt=create\" class=\"sidelinks\" style=\"background-image: url($icon);\">"._CREATE_PHP_SPLIT_TEST."</a> </li>";    
  echo " <li> <a href=\"testcenter.php?conf=$conf&testid=$testid&spt=showclosed\" class=\"sidelinks\" style=\"background-image: url($icon);\">"._SHOW_CLOSED_TESTS."</a> </li>";
  $ii=1;
  while (($q) && ($data=@$q->FetchRow())) { 
      
      if ($lastcat!=$data['testname']) {
        $lastcat=$data['testname']; 
        echo "</ul></div>";
        echo "<h3 class=\"accordion_header\"><a href=\"#\">".$data['testname']."</a></h3>";
        echo "<div class=\"reportmenu\"><ul>";
        $active=$ii++;    
    }
    
    if ($testid==$data['id']) { 
        $openactive=$active;
        $s = "background: #CCFFCC no-repeat left;"; 
    } else {
        $s="";   
    }
    $s1="";
    $s2="";
    $s3="";
    if ($spt=="report") { $s1=$s; }
    if ($spt=="edit") { $s2=$s; }
    if ($spt=="delete") { $s3=$s; }

    echo " <li> <a href=\"testcenter.php?conf=$conf&testid=". $data['id'] . "&roadto=$roadto&spt=report&from=$from&to=$to\" class=\"sidelinks\" style=\"$s1 background-image: url(images/icons/splittest.gif);\">Show Report</a> </li>";    
    echo " <li> <a href=\"testpreview.php?conf=$conf&testid=". $data['id'] . "&page=A\" class=\"sidelinks\" style=\"$s2 background-image: url(images/icons/newpage.gif);\" target=\"_blank\">". _PREVIEW . "</a> </li>";    
    echo " <li> <a href=\"testcenter.php?conf=$conf&testid=". $data['id'] . "&spt=edit\" class=\"sidelinks\" style=\"$s3 background-image: url(images/icons/savedtest.gif);\">". _EDIT . "</a> </li>";    
    echo " <li> <a href=\"testcenter.php?conf=$conf&testid=". $data['id'] . "&spt=close\" class=\"sidelinks\" style=\"$s3 background-image: url(images/icons/compress.png);\">". _CLOSE . "</a> </li>";    

    
    $i++;
  }
  
  echo "</ul></div></div>";
  echo "</div>";
  
  echo "<script type=\"text/javascript\">";
    if (@$openactive) { 
        ?>
        function openaccordion() {
         /*alert(<?php echo ($openactive); ?>);*/
         $("#accordion").accordion('activate', <?php echo ($openactive); ?>);         
        }
        <?php
    } else {
        
        echo "function openaccordion() { /* alert('no openactive'); $(\"#accordion\").accordion('activate', 0);*/ }";
    }
    
    echo "</script>";
    echo "</td><td valign=top width=100%>";
}

function oldSidepanel() {
  global $conf,$profile,$sptablename,$testid,$roadto, $db,$from,$to;
  $q = @$db->Execute("select id,testname from $sptablename where status=1");
  if (!$q) {
  	// Probably no table.  When we create a test, we'll create the table.
	}
  
  ?>
  <script language="javascript">
  function srrowOverEffect(object) {
  	if (object.className == 'profilerow') object.className = 'profilerowhighlight';
  }
  
  function srrowOutEffect(object) {
  	if (object.className == 'profilerowhighlight') object.className = 'profilerow';
  }
  </script>
  <?php
  
  echo "</td></tr><tr><td rowspan=5 valign=top width=140>";
  echo "<table width=150 cellpadding=4 class=smallborder>";
  echo "<tr><td><img src=images/icons/splittest.gif width=16 height=16 align=left><b> "._ACTIVE_TESTS."</b></td></tr>";
  $i=0;
	while (($q) && ($data=@$q->FetchRow())) {
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td valign=center><nobr><img src=images/icons/savedtest.gif width=16 height=16 align=left> <a href=testcenter.php?conf=$conf&testid=". $data['id'] . "&roadto=$roadto&spt=report&from=$from&to=$to><nobr>". $data['testname'] . "</a></nobr>
    <br><a target=_blank href=testpreview.php?conf=$conf&testid=". $data['id'] . "&page=A class=graylink>"._PREVIEW."</a>  
    <a href=testcenter.php?conf=$conf&testid=". $data['id'] . "&spt=edit class=graylink>"._EDIT."</a>  
    <a href=testcenter.php?conf=$conf&testid=". $data['id'] . "&spt=close class=graylink>"._CLOSE."</a></td></tr>";
    $i++;
  }
  if ($i==0) {
    echo "<tr><td>"._NONE."</td></tr>";
  }
  echo "<tr><td><a href=\"testcenter.php?conf=$conf&spt=create\" class=graylink>"._CREATE_PHP_SPLIT_TEST."</a></td></tr>";
  echo "<tr><td><a href=\"testcenter.php?conf=$conf&testid=$testid&spt=showclosed\" class=graylink>"._SHOW_CLOSED_TESTS."</a></td></tr>";
  echo "</table></td><td valign=top width=100%>";
}

function ShowClosed() {
  global $conf,$profile,$sptablename;
  global $db;
  
  SelectBox();
  Sidepanel();
  
  $q = $db->Execute("select id,testname from $sptablename where status=2");
  echo "<table cellpadding=4>";
  echo "<tr><td><b>"._CLOSED_TESTS."</b></td></tr>";
  $i=0;
	while ($data=@$q->FetchRow()) {
    echo "<tr><td valign=center><img src=images/icons/savedtest.gif width=16 height=16 align=left> <a href=testcenter.php?conf=$conf&testid=". $data['id'] . "&spt=report><nobr>". $data['testname'] . "</nobr></a><br><a href=testpreview.php?conf=$conf&testid=". $data['id'] . "&page=A class=graylink>"._PREVIEW."</a> | <a href=testcenter.php?conf=$conf&testid=". $data['id'] . "&spt=edit class=graylink>"._EDIT."</a> | <a href=testcenter.php?conf=$conf&testid=". $data['id'] . "&spt=activate class=graylink>activate</a></td></tr>";
    $i++;
  }
  if ($i==0) {
    echo "<tr><td>"._NONE."</td></tr>";
  }
  echo "</table>";
}


function Welcome() {
  global $conf,$databasedriver;
  SelectBox();
  if (!IS_PHPDOCK) {
    Sidepanel();
  }
  echo "<P><table width=100% cellpadding=5 border=0><tr><td valign=top style='line-height:20px;'>";
   echo "<h3 style=\"margin-top:0px;\">"._WELCOME_TEST_CENTER."</h3>";
  echo _WHAT_IS_TEST_CENTER."<P>";
  echo _CREATE_2_TYPES_TESTS . "<ul>";
  if (!IS_PHPDOCK) {
      echo "<li> <a href=testcenter.php?conf=$conf&spt=create>"._CREATE_PHP_SPLIT_TEST."</a>"._EASY;                                                                                        
      echo "<li> <a href=splittest.php?conf=$conf>"._URL_BASED_TEST."</a>"._FLEXIBLE; 
  } else {
      echo "<li> <a href=splittest.php?conf=$conf>"._URL_BASED_TEST."</a>"._FLEXIBLE; 
      echo "<li> <font color=gray><u>"._CREATE_PHP_SPLIT_TEST."</u><br>"._NO_DESKTOP_VERSION."</font>";    
  }
  echo "</ul>";
  if ($databasedriver=='sqlite' && !IS_PHPDOCK) {
    echo "<P><B><font color=red>"._PLEASE_NOTE_TEST_ONLY_WITH_MYSQL."</b></font>";
  }
  echo "</td><td width=200 style='border-style : solid; border-width:0px; border-left-width : thin;'>";
  echo _WHAT_IS_SPLIT_TESTING;
  echo "</td></tr></table>";
}

function CreateTest() {
  global $conf;
  echo "&nbsp;<br><div class=\"indentbody\"><table cellpadding=6 cellspacing=0 width=800 border=0><tr><td colspan=3 class=toplineblue bgcolor=#BBDDFF><font size=3><b>"._CREATE_TEST."</b></font></td></tr>";
  echo "<tr><td colspan=3><ul><li>"._STEP1_TEST;
  echo "<li>"._STEP2_TEST;
  echo "<li>"._STEP3_TEST."</ul><P>";  
  echo _LETS_START_TEST."<hr noshade size=1>";
  echo "<form method=get action=testcenter.php></td></tr>";
  echo "<tr><td>"._TEST_NAME."</td><td><input type=text size=20 name=testname maxval=50></td>";
  echo "<td bgcolor=#e0e0e0>"._TEST_NAME.":</td></tr>";
  echo "<tr><td width=200>"._IMPORT_TEST.":</td><td><input type=text size=40 maxval=50 name=testurl></td>";
  echo "<td bgcolor=#e0e0e0>"._ENTER_TEST_URL.":</td></tr>";
  echo "<tr><td>"._TEST_DESCRIPT."</td><td><textarea name=testdesc cols=30 rows=5 maxval=255></textarea></td>";
  echo "<td bgcolor=#e0e0e0>"._DESCRIBE_YR_TEST."</td></tr>";
  echo "<tr><td>"._TRAFFIC_SPLIT."%</td><td><input type=text size=40 maxval=50 name=splitperc value=\"50\"></td>";
  echo "<td bgcolor=#e0e0e0>"._ENTER_PERCENT."</td></tr>";

  echo "</table>";
  echo "<input type=submit value=Create><input type=hidden name=spt value=\"define\"><input type=hidden name=testid value=\"0\"><input type=hidden name=conf value=\"$conf\"></form></div>";
}

function EditTest() {
  global $conf, $testid,$sptablename;
  global $db;
  
  $q = $db->Execute("select * from $sptablename where id=$testid");
  $data=$q->FetchRow();
  $testname=$data['testname'];
  $testurl=$data['testurl'];
  $testdesc=$data['testdesc'];
  $splitperc=$data['splitperc'];
  $panea=stripslashes($data['pagea']);
  $paneb=stripslashes($data['pageb']);
  $panea=str_replace("&","[#amp#]",$panea); 
  $paneb=str_replace("&","[#amp#]",$paneb);
  $panea=str_replace("</textarea>","</overruletextarea>",$panea);
  $paneb=str_replace("</textarea>","</overruletextarea>",$paneb); 
  
  echo "<form name=editor method=post action=testcenter.php><div class=\"indentbody\"><table cellpadding=6 cellspacing=0 width=600 border=0><tr><td colspan=3 class=toplineblue bgcolor=#BBDDFF><font size=3><b>"._EDIT_SPLIT_TEST."</b></font></td></tr>";

  echo "<tr><td>"._TEST_NAME."</td><td><input type=text size=20 name=testname maxval=50 value=\"$testname\"></td>";
  echo "<td bgcolor=#e0e0e0>"._TEST_NAME."</td></tr>";
  echo "<tr><td width=200>"._IMPORT_TEST."</td><td><input type=text size=40 maxval=50 name=testurl  value=\"$testurl\" disabled></td>";
  echo "<td bgcolor=#e0e0e0>"._ENTER_TEST_URL."</td></tr>";
  echo "<tr><td>Test Description</td><td><textarea name=testdesc cols=30 rows=5 maxval=255>$testdesc</textarea></td>";
  echo "<td bgcolor=#e0e0e0>"._DESCRIBE_YR_TEST."</td></tr>";
  echo "<tr><td>Traffic Split %</td><td><input type=text size=40 maxval=50 name=splitperc value=\"$splitperc\"></td>";
  echo "<td bgcolor=#e0e0e0>"._ENTER_PERCENT."</td></tr>";
  echo "<tr><td colspan=3><hr noshade size=1>";
  ?>
  <input type=checkbox name="wr" onclick="togglewrap()"> <?php echo _WORD_WRAP;?><P>
  <table with=100%><tr><td><b><?php echo _PAGE_A_ORIGINAL;?>:</b><br>
  <textarea name="panea" onscroll="syncpane('b')" cols=50 rows=25 wrap=off style='color:darkgray;'><?php echo htmlspecialchars($panea); ?></textarea>
  </td><td><b><?php echo _PAGE_B_VARIATION;?>:</b><br>
  <textarea name="paneb" onscroll="syncpane('a')" cols=50 rows=25 wrap=off style='color:darkgreen;'><?php echo $paneb; ?></textarea>
  </td></tr>
  </table>
  <?php  
  echo "<input type=submit value=Save><input type=hidden name=spt value=\"savetest\"><input type=hidden name=testid value=\"$testid\"><input type=hidden name=edit value=\"1\"><input type=hidden name=conf value=\"$conf\"></form></td></tr></table></div>";
}

function DefineTest() {
  global $conf,$testname,$testurl,$testdesc,$splitperc,$testid;
  $orisource=file_get_contents($testurl);
  //echo $orisource . "hier" . strlen($orisource).$testurl;
  SaveTest(); // save basic info from step 1
  
  echo "<br><div class=\"indentbody\"><table cellpadding=6 cellspacing=0 width=500 border=0><tr><td colspan=2 class=toplineblue bgcolor=#BBDDFF><font size=3><b>"._CREATE_TEST."</b></font></td></tr>";
  echo "<tr><td colspan=2>"._TEST_HTML_COPY."<hr noshade size=1>";
  
  echo "<form name=editor method=post action=testcenter.php>";
  //$orisource=utf8_encode(str_replace("&","[#]",$orisource));
  //$orisource=str_replace("?","&euro;",$orisource);
  $orisource=str_replace("&","[#amp#]",$orisource);
  $orisource=str_replace("</textarea>","</overruletextarea>",$orisource);  
  
  ?>
  <input type=checkbox name="wr" onclick="togglewrap()"><?php echo _WORD_WRAP?><P>
  <table with=100%><tr><td><b><?php echo _ORIGINAL_VERSION?>:</b><br>
  <textarea name="panea" onscroll="syncpane('b')" cols=50 rows=25 wrap=off style='color:darkgray;'><?php echo $orisource; ?></textarea>
  </td><td><b><?php echo _CREATE_VARIATION?>:</b><br>
  <textarea name="paneb" onscroll="syncpane('a')" cols=50 rows=25 wrap=off style='color:darkgreen;'><?php echo $orisource; ?></textarea>
  </td></tr>
  <tr><td colspan=2 align=right>
  <?php
  echo "<input type=submit value=Create><input type=hidden name=testid value=\"$testid\"><input type=hidden name=spt value=\"publish\"><input type=hidden name=conf value=\"$conf\"></form>";
  ?>
  </td>
  </table>
  </form>
  </td></tr></table>
  <?php
}

function PublishTest() {
    global $conf, $panea, $paneb, $testid,$edit,$nosave,$profile,$silent;
    $filename=$conf."-splittest$testid".".php";
    echo "<br><div class=\"indentbody\"><table cellpadding=6 cellspacing=0 width=500 border=0><tr><td colspan=2 class=toplineblue bgcolor=#BBDDFF><font size=3><b>Publish a PHP Split Test</b></font></td></tr>";
    echo "<tr><td colspan=2>";

    if ($nosave!=1) {
        $silent=1;
        SaveTest();
    }
    echo _PUBLISH_TEST_NEXT."<p>";

    echo "<form>";
    $w_path = $_SERVER['PHP_SELF'];
    $wpath = dirname($w_path);
               
    // if (_LOGAHOLIC_EDITION=="2") {
        
        if (stripos($panea,"<BODY")!=FALSE) {
            echo "<font color=red>A BODY tag was detected in your split test content, we recommend to use the 'Testing an Entire Page' code below.</font><br>";
        }
        
        echo "<br><hr noshade size=1><br>"._TESTING_AN_ENTIRE_PAGE.":<br><br>";
        $abcodespace="<html><head>\n<script language=\"javascript\" type=\"text/javascript\" src=\"http://$_SERVER[HTTP_HOST]$wpath/abtester.php?conf=$profile->profilename&testid=$testid&vmethod=$profile->visitoridentmethod&tn=$profile->tablename&jsmode=2\"></script>\n</head><body></body></html>"; 
        ?><textarea wrap=on cols=70 rows=6 name="abcodespace2" onClick="javascript:this.form.abcodespace2.focus();this.form.abcodespace2.select();"><?php echo $abcodespace; ?></textarea><br><font size=1>Click above to Select All, then press Ctrl-C to copy.</font><br><br><?php
        
        echo "<br><hr noshade size=1><br><b>Testing part of a page:</b><p>If you are testing only a small bit of code (not a whole page), you can use the javascript below to insert this test into any page on your website. Just replace the original piece of source code with this:";  
       $abcodespace="<div id=\"abtester\"></div><script language=\"javascript\" type=\"text/javascript\" src=\"http://$_SERVER[HTTP_HOST]$wpath/abtester.php?conf=$profile->profilename&testid=$testid&vmethod=$profile->visitoridentmethod&tn=$profile->tablename&jsmode=1\"></script>";
       ?><br><br><textarea wrap=on cols=70 rows=4 name="abcodespace" onClick="javascript:this.form.abcodespace.focus();this.form.abcodespace.select();"><?php echo $abcodespace; ?></textarea><br><font size=1><?php echo _CLICK_ABOVE_CTRL_C_COPY?></font><?php
    /*
    } else {
        echo _TEST_FILE_CALLED." <b>$filename</b><P>"._HERE_WHAT_YOU_NEED_TO_DO.":";
        echo "<ol><li> <a href=testcenter.php?conf=$conf&testid=$testid&visitormethod=".$profile->visitoridentmethod."&tn=$profile->tablename&spt=savefile>"._CLICK_SAVE_TEST."</a><p>";
        //define('', ' You can rename the file to anything you want, but the extension must be .php or it probably won\'t work. ');
        //define('', ' Make a backup of the original file if you overwrite it ');
        //define('_THATS_IT', 'That\'s it. When you are done, click Finish below ');
        echo "<li>"._UPLOAD_TEST_FILE.".<p>";
        echo "<ul><li>"._RENAME_TEST_FILE."<p>";
        echo "<li>"._BACKUP_TEST_FILE."</ul><p>";
        echo "<li>"._TESTING_SMALL_BIT_1." $filename "._TESTING_SMALL_BIT_2.":";
        $abcodespace="<script language=\"javascript\" src=\"/$filename?jsmode=1\"></script>";
           ?><br><br><textarea wrap=on cols=70 rows=4 name="abcodespace" onClick="javascript:this.form.abcodespace.focus();this.form.abcodespace.select();"><?php echo $abcodespace; ?></textarea><br><font size=1><?php echo _CLICK_ABOVE_CTRL_C_COPY?></font><P><?php
        echo "<li>"._THATS_IT."</ol><p>";

        echo "<font size=1>"._TIP_VARIATION.".</font>"; 
    }
	*/
    ?>
    </form>
    <form method=post action=testcenter.php><input type=hidden name=conf value=<?php echo $conf;?>><input type=submit value=Finish></form>
    </td></tr></table></div>       
    <?php
}

function SaveTestFile() {
  global $conf;
  header("Content-Type: octet/stream");
  header("Content-Disposition: attachment; filename=\"".$conf."\"");
  $content = file_get_contents("testtemplate.php");
  print $content;
}

function SaveTest() {
  global $testname,$testurl,$testdesc,$splitperc,$panea,$paneb,$conf,$profile,$sptablename,$sptablename_results,$testid,$edit,$silent;
  global $db, $databasedriver,$profile;
  
  
  if (!$testid) {
    //echo "testID:$testid<P>"; //insert new test   
    
    // If the tables don't already exists, then create them...
	  $tablelist = $db->MetaTables();
	  if (!in_array_insensitive($sptablename, $tablelist)) {
	    $db->Execute("CREATE TABLE $sptablename (".
	      "id " . ($databasedriver == "sqlite"? "INTEGER PRIMARY KEY ": "int(11) NOT NULL auto_increment ") . "," .
    		"testname varchar(100) NOT NULL default '',
    		testdesc text,
    		testurl varchar(255) default NULL,
    		pagea blob,
    		pageb blob,
    		splitperc int(3) default '50',
    		created int(11) NOT NULL default '0',
	      status int(1) default '1' ".
	      ($databasedriver == "sqlite" ? "" : ", PRIMARY KEY  (id)") . 
	    ") ENGINE=MyISAM CHARSET=utf8");
            if ($databasedriver == "mysql") { 
                $db->Execute("ALTER TABLE {$sptablename} ADD INDEX {$sptablename}_testname (testname)"); 
            } else { 
                $db->Execute("CREATE INDEX {$sptablename}_testname on $sptablename(testname)"); 
            }
		}
		
	  if (!in_array_insensitive($sptablename_results, $tablelist)) {
	    $db->Execute("CREATE TABLE $sptablename_results (".
	      "id " . ($databasedriver == "sqlite"? "INTEGER PRIMARY KEY ": "int(11) NOT NULL auto_increment ") . "," .
	      " testid int(10) NOT NULL default '0',
	      visitorid varchar(32) NOT NULL default '0',
	      timestamp int(11) NOT NULL default '0',
	      page varchar(2) NOT NULL default '0' ".
	      ($databasedriver == "sqlite" ? "" : ", PRIMARY KEY  (id)") . 
	    ") ENGINE=MyISAM CHARSET=utf8");
            if ($databasedriver == "mysql") { 
                $db->Execute("ALTER TABLE {$sptablename_results} ADD INDEX {$sptablename_results}_visitorid (visitorid)");
                $db->Execute("ALTER TABLE {$sptablename_results} ADD INDEX {$sptablename_results}_timestamp (timestamp)");
                $db->Execute("ALTER TABLE {$sptablename_results} ADD INDEX {$sptablename_results}_testid (testid)");
                $db->Execute("ALTER TABLE {$sptablename_results} ADD INDEX {$sptablename_results}_page (page)");
            } else { 
                $db->Execute("CREATE INDEX {$sptablename_results}_visitorid on $sptablename_results(visitorid)");
                $db->Execute("CREATE INDEX {$sptablename_results}_timestamp on $sptablename_results(timestamp)");
                $db->Execute("CREATE INDEX {$sptablename_results}_testid on $sptablename_results(testid)");
                $db->Execute("CREATE INDEX {$sptablename_results}_page on $sptablename_results(page)"); 
            }
	    
		}
		
    $cd=time();
    $db->Execute("insert into $sptablename (testname, testurl, testdesc, splitperc, created) values ('".$_REQUEST["testname"]."','".$_REQUEST["testurl"]."','".$_REQUEST["testdesc"]."','".$_REQUEST["splitperc"]."','$cd')");
    $testid=$db->Insert_ID();
    //echo "inserted new test";
    return $testid;  
  } else {
 
    //update test
    //$_REQUEST["panea"]=$db->escape(htmlspecialchars($_REQUEST["panea"], ENT_QUOTES, 'utf-8'));
    //$_REQUEST["paneb"]=$db->escape(htmlspecialchars($_REQUEST["paneb"], ENT_QUOTES, 'utf-8'));
    
    $_REQUEST["panea"]=$db->escape(str_replace("[#amp#]","&",$_REQUEST["panea"]));
    $_REQUEST["paneb"]=$db->escape(str_replace("[#amp#]","&",$_REQUEST["paneb"]));
    $_REQUEST["panea"]=$db->escape(str_replace("</overruletextarea>","</textarea>",$_REQUEST["panea"]));
    $_REQUEST["paneb"]=$db->escape(str_replace("</overruletextarea>","</textarea>",$_REQUEST["paneb"]));
     
    if ($edit==1) {
      $db->Execute("update $sptablename set testname='".$_REQUEST["testname"]."',testdesc='".$_REQUEST["testdesc"]."',splitperc='".$_REQUEST["splitperc"]."',pagea='".$_REQUEST["panea"]."', pageb='".$_REQUEST["paneb"]."' where id='$testid'") or die ($db->ErrorMsg());
    } else {
      $db->Execute("update $sptablename set pagea='".$_REQUEST["panea"]."', pageb='".$_REQUEST["paneb"]."' where id='$testid'") or die ($db->ErrorMsg());
    }
    //SelectBox();
    Sidepanel();
    echo "<b>"._TEST_SAVED."</b>.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=testpreview.php?conf=$conf&testid=$testid&page=B target=_blank class=graylink>"._PREVIEW_PAGE."</a><P>";
    if ($silent!=1) {
        echo _DONT_REPUBLISH_TEST_FILE."<a href=testcenter.php?conf=$conf&testid=$testid&visitormethod=".$profile->visitoridentmethod."&spt=savefile>"._CLICK_HERE ."</a> "._SAVE_FILE_AGAIN.".<p>";
    }
    
  }
}

function DeleteTest() {
  global $sptablename,$conf,$testid;
  global $db;
  $db->Execute("delete from $sptablename where id='$testid'") or die ($db->ErrorMsg());
  SelectBox();
  Sidepanel();
  echo _DEL_TEST;
}
function CloseTest() {
  global $sptablename,$conf,$testid;
  global $db;
  $db->Execute("update $sptablename set status=2 where id='$testid'");
  SelectBox();
  Sidepanel();
  echo "<b>"._ACTIVE_TEST_CLOSED."</b><P>"._REMOVE_AND_REPLACE_TEST."<P>"._REVIEW_CLOSED_TEST."<a href=testcenter.php?conf=$conf&testid=$testid&spt=showclosed>"._CLICK_HERE."</a>.<P>"._REMOVE_TEST_COMPLETE."<a href=testcenter.php?conf=$conf&testid=$testid&spt=delete>"._CLICK_HERE."</a>.<br><font color=red>"._WARNING_DELETE_TEST_FILE."</font>";
  
}
function ActivateTest() {
  global $sptablename,$conf,$testid,$nosave;
  global $db;
  $db->Execute("update $sptablename set status=1 where id='$testid'");
  SelectBox();
  Sidepanel();
  echo _TEST_REACTIVETED;
  $nosave=1;
  PublishTest();
}

function SplitTestTimeOnPage($page) {
    global $db, $profile, $from, $to, $testid, $sptablename,$sptablename_results;
    /**
    * @desc This function will calculate grouped averages for time spent on a particular page, 
    * not counting people who bounced (had no further requests)
    * @returns $output that contains a html table
    */    
    $db->Execute("drop temporary table if exists goodtimes"); $db->Execute("drop table if exists pagetime");
    
    // first find the visitorid and timestamp of the request we're interested in.   
    $query="CREATE TEMPORARY TABLE goodtimes ";
    $query.="SELECT vi.id as visitorid,min(timestamp) as timestamp FROM $sptablename_results as sp, $profile->tablename_visitorids as vi ";
    $query.="WHERE testid='$testid' AND (timestamp >=$from and timestamp <=$to) ";
    $query.="AND page='$page' AND sp.visitorid=vi.visitorid ";
    $query.="GROUP BY visitorid";   
    
    // get the visitorid and timestamp of the request that came after the one we're interested in
    // and create a tempoary table to hold the difference between the two timestamps
    $query2 ="CREATE TABLE pagetime ";  
    $query2.="SELECT a.visitorid, (min(a.timestamp)-b.timestamp) as length from $profile->tablename as a, goodtimes as b ";
    $query2.="WHERE b.visitorid=a.visitorid and a.timestamp > b.timestamp and a.timestamp <= $to ";
    $query2.="AND crawl='0' ";
    $query2.="GROUP BY a.visitorid";  
    
    // get rid of the ones that have no time
    //$query3="DELETE from pagetime WHERE length=0";
    
    $db->Execute($query);
    $db->Execute($query2);
    //$db->Execute($query3);
    
    // get a meaningful breakdown of the time spent
    $range = $db->Execute("select count(*) as visitors from pagetime");
    $range_data = $range->FetchRow();
    $total_visitors=$range_data['visitors'];
    $query = "select \"0 to 10 seconds            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"1\" as ord from pagetime where length >=0 and length <=10 union ";
    $query.= "select \"10 to 60 seconds            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"2\" as ord from pagetime where length >=10 and length <=60 union ";
    $query.= "select \"1 to 5 minutes            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"3\" as ord from pagetime where length >=60 and length <=300 union ";
    $query.= "select \"5 to 15 minutes            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"4\" as ord from pagetime where length >=300 and length <=900 union ";
    $query.= "select \"15 to 30 minutes            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"5\" as ord from pagetime where length >=900 and length <=1800 union ";
    $query.= "select \"30 to 1 hour            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"6\" as ord from pagetime where length >=1800 and length <=3600 union ";
    $query.= "select \"more than 1 hour            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"7\" as ord from pagetime where length >=3600";
    $query.= " order by ord";
    
    // get the results form the database
    $db->SetFetchMode(ADODB_FETCH_NUM);
    $result = $db->Execute($query);
    $data = $result->GetArray();
    $db->SetFetchMode(ADODB_FETCH_BOTH);
    
    // clean up temp tables (so we can do this more than once in a single script)
    $db->Execute("drop temporary table goodtimes");
    $db->Execute("drop table pagetime");
    
    //prepare the output
    $output="<table width='100%' bgcolor='#F8F8F8'><tr><td><i>Time Spent</i></td><td><i>Visitors</i></td><td><i>Average Duration</i></td></tr>";
    foreach ($data as $thisdatarow) { 
        $output.= "<tr><td>$thisdatarow[0]</td><td title='$thisdatarow[2] Visitors'>".number_format($thisdatarow[1],2)."%</td><td>".number_format($thisdatarow[3],2)." minutes</td></tr>";   
    }
    $output.="</table>";    
    return $output;       
}



function SplitTestBounceRateOfPageVariation($page) {
    global $db, $profile, $from, $to, $testid, $sptablename,$sptablename_results;
    
    // get the visitorid and timestamp of the request we're interested in 
    $db->Execute("CREATE TEMPORARY TABLE goodtimes select vi.id as visitorid,timestamp from $sptablename_results as sp, $profile->tablename_visitorids as vi where testid='$testid' and timestamp >=$from and timestamp <=$to and page='$page' and sp.visitorid=vi.visitorid");
    
    // first get total number of visitors to page
    $query = "select count(distinct visitorid) from goodtimes";
    $result = $db->Execute($query); 
    $total_visitors = $result->FetchRow();
    $total_visitors = $total_visitors[0];    
    
    // count how many visitors have hits beyond the timestamp we just found
    //$query = "select count(distinct b.visitorid) from $profile->tablename as a, goodtimes as b where b.visitorid=a.visitorid and a.timestamp > b.timestamp and a.timestamp <= $to";
    $query = "select count(distinct b.visitorid) from $profile->tablename as a, goodtimes as b where b.visitorid=a.visitorid and a.timestamp > b.timestamp and a.timestamp <= $to";
    $result = $db->Execute($query); 
    $stay_visitors = $result->FetchRow();
    $stay_visitors = $stay_visitors[0];
    
    $bounce_visitors = $total_visitors - $stay_visitors;
    $bounce_rate = @($bounce_visitors / $total_visitors * 100);
    /*
    echo "<br>total: $total_visitors";
    echo "<br>stayed: $stay_visitors";
    echo "<br>bounced: $bounce_visitors";
    echo "<br>rate: $bounce_rate";
    */
    $db->Execute("drop temporary table goodtimes");  
    return number_format($bounce_rate,0) . "%";       
}

function ReportTest() {
  global $testid,$conf,$sptablename,$sptablename_results,$from,$to,$profile,$roadto,$includereport,$niceto,$nicefrom;
  global $db;
  $target=$roadto;
  //get some info
  $q = $db->Execute("select * from $sptablename where id=$testid");
  $data=$q->FetchRow();
  $testname=$data['testname'];
  $testurl=$data['testurl'];
  $testdesc=$data['testdesc'];
  $splitperc=$data['splitperc'];
  $created=date("Y-m-d",$data['created']);
  
  if (!$includereport) {
    SelectBox();
    Sidepanel();
  }
  
  //1. Select uniques from page a
  $q = $db->Execute("select count(distinct visitorid) as users from $sptablename_results where timestamp >=$from and timestamp <=$to and testid=$testid and page='A'");
  $pagea=$q->FetchRow() or die ($db->ErrorMsg());
  //echo "select count(distinct visitorid) as users from $sptablename_results where timestamp >=$from and timestamp <=$to and testid=$testid and page='A'<P>";
  
  //2. Select uniques from page b
  $q = $db->Execute("select count(distinct visitorid) as users from $sptablename_results where timestamp >=$from and timestamp <=$to and testid=$testid and page='B'");
  $pageb=$q->FetchRow() or die ($db->ErrorMsg());
  
  if ($pagea[0]==0 && $pageb[0]==0) {
    $q = $db->Execute("select max(timestamp) from $sptablename_results where testid=$testid");
    $lasttime=$q->FetchRow() or die ($db->ErrorMsg());
  
    if ($includereport) {
        echo "<span style=\"padding-top:2px;padding-bottom:2px;display:block;\">";
        TestReportToggle();
        makeAjaxTargetSelect();
        echo "</span>";
    }
    echo "<b>"._NO_SPLIT_TEST_COLLECTED." '$testname' "._DURING_DATE_RANGE.".</b><br>";
    if ($lasttime[0]!=0) {
        echo ""._LAST_ACTIVITY_ADDED_ON.date("Y-m-d, H:i:s",$lasttime[0])."<br>";   
    }
    echo "<br>"._MAKE_SURE_TEST_CODE_IS_PUBLISHED.".<br><br>";
    echo ""._DESCRIPTION.": $testdesc<br>"._TRAFFIC_SPLIT_PERCENT." $splitperc % &nbsp;&nbsp;&nbsp; "._TEST_CREATED_ON." $created<br>"._REPORT." "._DATE_RANGE.": "._REPORT." $nicefrom "._TO." $niceto<br>"._SELECTED." "._TARGET.": $target"; 
    return;   
  }
  
  //1. Select Converted uniques from page a
  $q = $db->Execute("select count(distinct sp.visitorid) as users from $sptablename_results as sp, $profile->tablename as t, $profile->tablename_urls as u,$profile->tablename_visitorids as vi where t.timestamp >=$from and t.timestamp <=$to and sp.timestamp < t.timestamp and t.visitorid=vi.id and vi.visitorid=sp.visitorid and sp.page='A' and sp.testid=$testid and t.url=u.id and u.url='$target' and (t.status=200 or t.status=302) and t.crawl=0");
  $converta=$q->FetchRow() or die ($db->ErrorMsg());
  //echo "select count(distinct sp.visitorid) as users from $sptablename_results as sp, $profile->tablename as t where t.timestamp >=$from and t.timestamp <=$to and sp.timestamp < t.timestamp and t.visitorid=sp.visitorid and sp.page='A' and t.url='$target' and (t.status=200 or t.status=302) and t.crawl=0<P>";
  
  //1. Select Converted uniques from page b
  $q = $db->Execute("select count(distinct sp.visitorid) as users from $sptablename_results as sp, $profile->tablename as t, $profile->tablename_urls as u,$profile->tablename_visitorids as vi where t.timestamp >=$from and t.timestamp <=$to and sp.timestamp < t.timestamp and t.visitorid=vi.id and vi.visitorid=sp.visitorid and sp.page='B' and sp.testid=$testid  and t.url=u.id and u.url='$target' and (t.status=200 or t.status=302) and t.crawl=0");
  $convertb=$q->FetchRow() or die ($db->ErrorMsg());
  
  
  // calculate the rest
  $cra=@number_format((($converta[0]/$pagea[0])*100),2);
  $crb=@number_format((($convertb[0]/$pageb[0])*100),2);
  
  /*
  echo "Unique Visitors that saw page A:".$pagea[0] . "<br>";
  echo "Unique Converted Users from page A:".$converta[0] . "<p>";
  
  echo "Unique Visitors that saw page B:".$pageb[0] . "<br>";
  echo "Unique Converted Users from page B:".$convertb[0] . "<p>";
  */
  
  
  if (!$testid) {
    echo _NO_SPLIT_SELECT;
  } else if (!$roadto) {
    echo _SELECT_TARGET;
  } else {
  
  
    echo "<table cellpadding=6 cellspacing=0 width=100%><tr><td valign=top class=toplinegreen bgcolor=d5ffd5 colspan=4><font size=+1>"._TEST_RESULT.": $testname</font></td></tr>";
    
    echo "<tr><td bgcolor=#f0f0f0 colspan=4 style=\"line-height:17px;\">";
    if ($includereport) {
        echo "<span style=\"padding-top:2px;padding-bottom:2px;display:block;\">";
        TestReportToggle();
        makeAjaxTargetSelect();
        echo "</span>";   
    }
    echo "Description: $testdesc<br>"._TRAFFIC_SPLIT_PERCENT." $splitperc % &nbsp;&nbsp;&nbsp; Test Created on $created<br>"._REPORT." "._DATE_RANGE.": "._DATE_FROM." $nicefrom "._DATE_TO." $niceto<br>"._SELECTED." "._TARGET.": $target";
    echo "</td></tr>";
    echo "<tr><td colspan=2><b>"._PAGE." A</b></td><td colspan=2><b>"._PAGE." B</b></td></tr>";
    
    echo "<tr><td>"._PAGE_A_UNIQUE.":</td><td>$pagea[0]</td>";
    echo "<td>"._PAGE_B_UNIQUE."</td><td>$pageb[0]</td></tr>";
    
    echo "<tr bgcolor=#F8F8F8><td>"._PAGE_A_TARGET.":</td><td>$converta[0]</td>";
    echo "<td>"._PAGE_B_TARGET.":</td><td>$convertb[0]</td></tr>";
    
    echo "<tr><td>"._PAGE_A_CONVERSION."</td><td>$cra %</td>";
    echo "<td>"._PAGE_B_CONVERSION."</td><td>$crb %</td></tr>";
    
    echo "<tr><td colspan=4>";
    
    //statistically significant ?

    if ($pagea[0]==0 || $pageb[0]==0) {
    	 echo "<P class=\"smallborder\" style=\"display:block;\"><b>"._NOT_ENOUGH_DATA."</b></p>";
    } else {
    	$epop = ($converta[0] + $convertb[0]) / ($pagea[0] + $pageb[0]);
    	$eerr = sqrt(($epop * (1 - $epop) * ($pagea[0] + $pageb[0]))/($pagea[0] * $pageb[0]));
    	$cfactor = @(abs((@($converta[0]/$pagea[0]) - @($convertb[0]/$pageb[0])))/$eerr);
    	echo ""._FACTOR.": $cfactor<br>";
    
        $confidence = 0; 
        
    	if ($cfactor > 0.01) {
    	 $confidence=1;
    	}
    	if ($cfactor > 0.06) {
    	 $confidence=5;
    	}
    	if ($cfactor > 0.14) {
    	 $confidence=10;
    	}
    	if ($cfactor > 0.25) {
    	 $confidence=20;
    	}
    	if ($cfactor > 0.52) {
    	 $confidence=39;
    	}
    	if ($cfactor > 0.68) {
    	 $confidence=50;
    	}
    	if ($cfactor > 1.00) {
    	 $confidence=68;
    	}
    	if ($cfactor > 1.64) {
    	 $confidence=90;
    	}
    	if ($cfactor > 1.96) {
    	 $confidence=95;
    	}
    	if ($cfactor > 2.58) {
    	 $confidence=99;
    	}
    	if ($cfactor > 2.81) {
    	 $confidence=99.5;
    	} else {
    		//$confidence = 0;
    	}
    
    	if (($converta[0]/$pagea[0]) > ($convertb[0]/$pageb[0])) {
    			 $winner="Page A";
    			 $loser="Page B";
    	} else {
    			 $winner="Page B";
    			 $loser="Page A";
    	}	
    	
    	if ($confidence >= 90) {
    		 echo "<P><table width=100% align=center cellpadding=4 border=0 cellspacing=0 class=smallborder>";
    		 echo "<tr><td valign=top>&nbsp;<br><font size=+1>"._SPLIT_TEST_WINNER."<font color=red><b>$winner</b></font> !</font><br>";
      	 echo _SIGNIFCANT_TEST_RESULT." ( > $confidence% confidence )"._THIS_MEANS_YOU_CAN."$confidence%"._CONFIDENT_THAT."$winner"._PERFORM_BETTER."$loser";
    		 echo "<br>&nbsp;</td></tr></table>";
    		 
      } else {
    	   echo "<P><table width=100% align=center cellpadding=4 border=0 cellspacing=0 class=smallborder>";
    		 echo "<tr><td valign=top align=center style='line-height:18px;'>&nbsp;<br><font size=+1>"._SPLIT_TEST_WINNER."<b>"._INCONCLUSIVE."</b></font><br>";
      	 echo _NO_SIGNIFCANT_TEST_RESULT."( < $confidence% confidence )"._THIS_MEANS_YOU_CAN_ONLY."$confidence%"._CONFIDENT_THAT."$winner"._PERFORM_BETTER."$loser"._CONFIDENCE_BELOW_90."$winner"._JUST_LUCKY;
    		 echo "<br>&nbsp;</td></tr></table>";
      }
      
    }
    echo "</td></tr>";
    
    echo "<tr><td colspan=4>Other information about this Split Test that might be useful:</td></tr>";
    
    echo "<tr><td>"._BOUNCE_RATE." "._PAGE." A</td><td>".SplitTestBounceRateOfPageVariation("A")."</td>";
    echo "<td>"._BOUNCE_RATE." "._PAGE." B</td><td>".SplitTestBounceRateOfPageVariation("B")."</td></tr>";
    
    echo "<tr><td colspan=2>Esimated Time Spent on Page A<br>".SplitTestTimeOnPage("A")."</td>";
    echo "<td colspan=2>Esimated Time Spent on Page B<br>".SplitTestTimeOnPage("B")."</td></tr>";
    
    "</table>";
    
  
  }
  
}

?>

<?php
flush();
// PROGRAM STARTS HERE ------------------------------------------------------------------------
//require "queries.php";
$rstart=getmicrotime();
//Default values
	if ($submit) {
		if ($filter==false) {
			$filter=0;
		} else {
			$filter=0;
			$checked="checked";
		}	
		if (!$from) {
			$from   = mktime(0,0,0,$fmonth,$fday,$fyear);
			$to     = mktime(23,59,59,$tmonth,$tday,$tyear);
		}
	} else {
		$filter = false;
		$checked="";
		if (!$from) {
			$from   = mktime(0,0,0,date("m"),01,date("Y"));
			$to     = mktime(23,59,59,date("m"),date("d"),date("Y"));
		}
		$labels = "Visitors Per Day";
	}

// Get the Report type (query, drilldownlinks)
	GetQuery($labels,$showfields,$from,$to,$item,$item2);

if (@$_REQUEST["includereport"]) {
    if ($testid==0) {
        $testid=GetTestid();  
    }
    ReportTest();
    exit();    
}
// Build Screen
echo "<table cellpadding=8 border=0 width=100%><tr><td colspan=2>";
//echo "spt:".$spt;
// Print Statistics table
if ($spt=="" || !$spt) {
    //echo "hallo";
    Welcome();
} else if ($spt=="create") {
  CreateTest();
} else if ($spt=="define") {
  DefineTest();
} else if ($spt=="publish") {
  PublishTest();
} else if ($spt=="report") {
  ReportTest();
} else if ($spt=="close") {
  CloseTest();
} else if ($spt=="delete") {
  DeleteTest();
} else if ($spt=="preview") {
  PreviewTest();
} else if ($spt=="edit") {
  EditTest();
} else if ($spt=="savetest") {
  SaveTest();
} else if ($spt=="showclosed") {
  ShowClosed();
} else if ($spt=="activate") {
  ActivateTest();
}
//echo $spt;
//Welcome();

$rend=getmicrotime();
$rtook=number_format(($rend-$rstart),2);
echoDebug("<P>&nbsp;<p>&nbsp;<p><table width=500 cellpadding=3 border=0><tr><td rowspan=2>&nbsp;&nbsp;</td><td><font face=\"ms sans serif,arial\" size=1 color=silver>MySQL query:</font></td></tr><tr><td class=dotline2 bgcolor=#F8F8F8><font face=\"ms sans serif,arial\" size=1 color=gray>$query<P>"._PAGE_TOOK."$rtook"._SEC_BUILD."</font></td></tr></table></div>");

?>
</td></tr></table>
<P>
&nbsp;
<P>
&nbsp;
<div align=center>
<font size=1>
<a class=nodec href="credits.php<?php echo "?conf=$conf"; ?>">&copy; 2005-<?php echo date('Y');?> Logaholic BV</a></font>
</div>
<P>
<script>
  loading.style.visibility="hidden";
</script>
&nbsp;
</body>
</html>
<?php
?>
