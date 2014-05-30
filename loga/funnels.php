<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 

require_once "common.inc.php";
/* 
................................................
.......... Main program starts here ............
................................................
*/
// Pull in variables that may have been passed in with the URL.

$submit = @$_REQUEST["submit"];
$funnel = @$_REQUEST["funnel"];
$funnelid = @$_REQUEST["funnelid"];
$graphinclude = @$_REQUEST["graphinclude"];
$divlabel = @$_REQUEST["divlabel"];

if (isset($print)) {
 $noheader=1;
}
require "queries.php"; 
if (!$graphinclude) {
    require "top.php";
} else {
    $profile=new SiteProfile($conf);
    if ($funnelid) {
        setProfileData($profile->profilename, "$profile->profilename.last_funnel", $funnelid);
        //echo "set last funnl: $funnelid";
    }    
}
if ($funnel=="report") {
    $nicefrom = date("D, d M Y / H:i",$from);
    $niceto = date("D, d M Y / H:i",$to);
    ?>
    <div id="loading" style="position:absolute;left:100px;visibility:visible;">
    &nbsp;<P>
    <TABLE bgcolor=white cellspacing=0 cellpadding=3>
    <TR><TD class=toplinegreen><FONT size="+1"><?php echo _BUILDING_REPORT;?></FONT></TD></TR>
    <TR><TD bgcolor="#f0f0f0" class=dotline valign=middle><IMG src="images/Hourglass_icon.gif" width=32 height=32 alt="" border="0" align=left vspace=4 hspace=4>
    <?php echo _WAIT_WHILE_REPORT_IS_BEING_CREATED;?><BR>
    <?php echo _NOW_CALCULATING;?> <B><?php echo _FUNNEL_REPORT;?></b><?php echo _DATE_FROM;?><br><?php echo "$nicefrom "._DATE_TO." $niceto"; ?></B>
    </TD></TR>
    </TABLE>
    </div>
    <?php
    flush();
}

$funneltablename=$profile->tablename."_funnels";


function SelectBox() {
  global $conf,$to,$from,$limit,$labels,$roadto,$funnelid,$profile,$funnel;
  // Create Date Selector  
  echo "<div class='form1-wrap'><form method=get action=funnels.php id=\"form1\" name=\"form1\">";
	echo "<table border=0><tr><td><b>"._DATE_RANGE.": </b>";
	QuickDate($from,$to);
	echo "</td><td>";
  newDateSelector($from,$to);
	echo "<input type=hidden name=conf value=\"$conf\">";
	echo "<input type=hidden name=labels value=\"$labels\">";
	echo "<input type=hidden name=funnelid value=\"$funnelid\">";
	echo "<input type=hidden name=funnel value=\"report\">";
	echo "</td><td>";  
  echo "</td><td><input type=submit name=submitbut value=Report><input type=hidden name=but value=Report>";
  echo "</td></tr></table></form></div><div class=breaker></div>";
}

function GetFunnelid() {
  global $conf,$profile,$funneltablename,$funnelid, $db;
  $q = @$db->Execute("select id,label from $funneltablename where id=funnelid LIMIT 1");
  $data=@$q->FetchRow();
  return $data[0]; 
}

function Sidepanel() {
  global $conf,$profile,$funneltablename,$funnelid, $db, $from, $to,$funnel;
  $q = @$db->Execute("select id,label from $funneltablename where id=funnelid");
  if (!$q) {
  	// Probably no table.  When we create a test, we'll create the table.
  }
  echo "<div style=\"float:left;width:180px;margin-top:2px;border:0px solid red;\">"; 
  $i=0;
  $lastcat=_FUNNEL_ANALYSIS_REPORTS;
  $active=0;
  
  echo "<div id=\"accordion\"> <h3 class=\"accordion_header_first\"><a href=\"#\">"._FUNNEL_ANALYSIS_REPORTS."</a></h3>";
  echo "<div class=\"reportmenu\"><ul>";    
  $icon = "images/icons/savedtest.gif";
  echo " <li> <a href=\"funnels.php?conf=$conf&funnel=create\" class=\"sidelinks\" style=\"background-image: url($icon);\">". _CREATE_NEW_FUNNEL . "</a> </li>";    
  $ii=1;
  while (($q) && ($data=@$q->FetchRow())) { 
      
      if ($lastcat!=$data['label']) {
        $lastcat=$data['label']; 
        echo "</ul></div>";
        echo "<h3 class=\"accordion_header\"><a href=\"#\">".$data['label']."</a></h3>";
        echo "<div class=\"reportmenu\"><ul>";
        $active=$ii++;    
    }
    
    if ($funnelid==$data['id']) { 
        $openactive=$active;
        $s = "background: #CCFFCC no-repeat left;"; 
    } else {
        $s="";   
    }
    $s1="";
    $s2="";
    $s3="";
    if ($funnel=="report") { $s1=$s; }
    if ($funnel=="edit") { $s2=$s; }
    if ($funnel=="delete") { $s3=$s; }

    echo " <li> <a href=\"funnels.php?conf=$conf&funnelid=". $data['id'] . "&funnel=report\" class=\"sidelinks\" style=\"$s1 background-image: url(images/icons/funnels.gif);\">Show Report</a> </li>";    
    echo " <li> <a href=\"funnels.php?conf=$conf&funnelid=". $data['id'] . "&funnel=edit\" class=\"sidelinks\" style=\"$s2 background-image: url($icon);\">". _EDIT . "</a> </li>";    
    echo " <li> <a href=\"funnels.php?conf=$conf&funnelid=". $data['id'] . "&funnel=delete\" class=\"sidelinks\" style=\"$s3 background-image: url(images/icons/delete.gif);\">". _DELETE . "</a> </li>";    

    
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
    /*echo "</td><td valign=top width=100%>"; */ 
    // above line was commented with // first and that made it crash on some server, huh??
}


function FunnelGraphToggle() {
  global $conf,$profile,$funneltablename,$funnelid, $db, $from, $to,$divlabel;
  $q = @$db->Execute("select id,label from $funneltablename where id=funnelid");
  echo "<select id=\"funnelselector\" style=\"float:left;background:#f0f0f0;border:1px solid gray;\" onChange=\"showAjaxGet('funnels.php?conf=$conf&to=$to&from=$from&graphinclude=1&divlabel=$divlabel&funnelid='+this.options[this.selectedIndex].value, '$divlabel')\">";
   
  $i=0;
  while (($q) && ($data=@$q->FetchRow())) {
    echo "<option value=\"".$data['id']."\"";
    if ($data['id']==$funnelid) { echo " selected"; }
    echo "> $data[label] </option>";
    $i++;
  }
  if ($i==0) {
    //echo "<tr><td>none</td></tr>";
  }
  echo "</select>";
}

function Welcome() {
  global $conf;
  echo "<div style=\"float:left;\"><P><table cellpadding=5 border=0><tr><td valign=top style='line-height:20px;'>";
  echo "<h3 style=\"margin-top:0px;\">"._LOGAHOLIC_FUNNEL_ANALYSIS."</h3>";
  echo _WHAT_IS_FUNNEL."<P>";
  echo _CHOOSE_FUNNEL_OR."<a href=funnels.php?funnel=create&conf=$conf>"._CREATE_NEW_FUNNEL."</a>.</a>";
  echo "</td><td width=200 valign=top style='border-style : solid; border-width:0px; border-left-width : thin;'>";
  echo "<b>"._WHATS_SALES_FUNNEL."?</b><P>";
  echo _A_SALES_TUNNEL;
  echo "<P>"._EXAMPLE_SALES_FUNNEL.":<p><img src=images/funnelexample.gif>";
  echo "It means both versions of the page will be online side by side, some people will only see page A, others only see page B. After the test has run a while, you can come back and check the performance of each variation, in terms of converting users to one of your target pages.<P>Full Tutorial";
  echo "</td></tr></table>";
  echo "</div>";
  
}

function EditFunnel() {
  global $funnel,$conf,$funnelname,$url1,$ourls,$funnelid,$funneltablename,$db;
  if ($funnel=="create") {
    //this is new
  } else {
    // get info from database
    $ourls=array();
    $q = $db->Execute("select * from $funneltablename where funnelid='$funnelid' order by id");
    $i=1;
    while (($q) && ($data=@$q->FetchRow())) {
      if ($data['funnelid']==$data['id']) {
        //funnelname
        $funnelname=$data['label'];
      } else if ($data['funnelid']!=$data['id'] && $i==1) {
        //echo "// this is the first url";
        $url1=$data['url'];
        $label1=$data['label'];
        $value1=$data['value'];
        $i++;
      } else {
        //rest
        $ourls[$i]=$data['url'];
        $olabels[$i]=$data['label'];
        $ovalues[$i]=$data['value'];
        $i++;
      }
    }
  
  }
  echo "<form method=post id=\"editform\" name=\"editform\" action=funnels.php><div><table cellpadding=6 cellspacing=0 width=90% border=0><tr><td colspan=3 class=toplineblue bgcolor=#BBDDFF><font size=3><b>Define Funnel</b></font></td></tr>";
  echo "<tr><td valign=top colspan=2><b>"._FUNNEL_NAME.":</b> <input type=text size=40 name=funnelname maxval=50 value=\"$funnelname\"><br>&nbsp;</td>";
  echo "<td bgcolor=#e0e0e0 valign=top>"._GIVE_FUNNEL_NAME."</td></tr>";
  echo "<tr><td colspan=2 valign=top><b>"._FUNNEL_DEF.":</b><br><br>";
  
  ?>
  <table cellpadding=3 border=0>
  <tr><td></td><td><?php echo _LABELNAME;?></td><td><?php echo _PAGES;?></td><td><?php echo _VALUE;?></td></tr>
  <tr>
  <td><?php echo _STAGE;?>1</td>
  <td><input type=text size=15 name="field1" value="<?php echo @$label1; ?>"></td>
  <td><input type=text size=40 name="group1" id="group1" value="<?php echo @$url1; ?>" <?php echo "onkeyup=\"QBuilderHelpForms('funnelentry', event, this.value+'@'+this.id+'@funnelentry', 'forminput');\" onclick=\"QBuilderHelpForms('funnelentry', event, this.value+'@'+this.id+'@funnelentry', 'forminput');\"";?> autocomplete="off"></td>
  <td><input type=text size=5 name="value1" value="<?php echo @$value1; ?>"></td>
  <td width=150>
   <nobr><input type=button value=" + " onclick="add('2')">&nbsp;<input type=button value=" - " disabled></nobr>
  </td>
  </tr></table>
    <?php
    $i=2;
    while ($i < 25) {
      if (@$olabels[$i]!="") {
        $display="block";
      } else {
        $display="none";
      }
      echo "<div id=\"$i\" style=\"display:$display;\"><table cellpadding=3 border=0><tr><td>"._STAGE."$i</td>";
     
      ?>
      <td><input type=text size=15 name="<?php echo "field".$i;?>" value="<?php echo @$olabels[$i];?>"></td>
      <td><input type=text size=40 name="<?php echo "group".$i;?>" id="<?php echo "group".$i;?>" value="<?php echo @$ourls[$i];?>" <?php echo "onkeyup=\"QBuilderHelpForms('funnelentry', event, this.value+'@'+this.id+'@funnelentry', 'forminput');\" onclick=\"QBuilderHelpForms('funnelentry', event, this.value+'@'+this.id+'@funnelentry', 'forminput');\"";?> autocomplete="off"></td>
      <td><input type=text size=5 name="<?php echo "value".$i;?>" value="<?php echo @$ovalues[$i];?>"></td>
      <td width=150>
        <nobr><input type=button value=" + " onclick="add('<?php $i++; echo $i; ?>')">&nbsp;<input type=button value=" - " onclick="remove2('<?php echo ($i-1); ?>')" <?php if ($i==2) { echo "disabled"; }?>></nobr> 
      </td>
      </tr></table></div>
      <?php
    }
    ?> 
  <br>
  <?php
  if ($funnel=="create") {
      ?>
      <div id="funnelhelp" align=center>
      &nbsp;<P>
      <a href="javascript:close('funnelhelp');"><img src=images/funnelexample2.gif border=1 alt="Click to close example"></a>
      </div>
      <?php      
  }
  echo "<input type=submit value=Save><input type=hidden name=funnel value=\"save\"><input type=hidden name=funnelid value=\"$funnelid\"><input type=hidden name=edit value=\"$funnel\"><input type=hidden name=conf value=\"$conf\"></form>";
  //echo "</td></tr>";
  //echo "<tr><td width=200 valign=top>Start Page</td><td valign=top><input type=text size=40 maxval=50 name=url1  value=\"$url1\"></td>";
  echo "<td bgcolor=#e0e0e0 valign=top><b>"._STAGE." 1:</b><br>"._FIRST_FUNNEL_PAGE_DEF.":<br>http://www.google.*<br>";
  echo "<P>"._STAGE." 2, 3, 4 "._ETCETERA.":</b><br>"._ENTER_OTHER_FUNNEL_PAGES."<P>";
  echo _ENTER_MULTIPLE_FUNNEL_PAGES."<P>";
  echo _ENTER_OPTIONALLY_STAGE_VALUE;
  echo "<P>"._DONT_FORGET_LABEL."</td><tr>";
  
  echo "<tr class=dotline><td cospan=3>";
  //echo "<input type=submit value=Save><input type=hidden name=funnel value=\"save\"><input type=hidden name=funnelid value=\"$funnelid\"><input type=hidden name=edit value=\"$funnel\"><input type=hidden name=conf value=\"$conf\"></form>";
  echo "</td></tr></table></div>";
}

function SaveFunnel() {
  global $funnelname,$url1,$ourls,$conf,$profile,$funneltablename,$funnelid,$edit;
  global $db, $databasedriver;
  
  if ($_REQUEST["funnelname"]=="") {
    $_REQUEST["funnelname"]=_UNNAMED_FUNNEL_REPORT;
  } 
  if ($funnelid) {
      //update funnel
    $db->Execute("update $funneltablename set label='".$_REQUEST["funnelname"]."' where id='".$_REQUEST["funnelid"]."'");
    $db->Execute("delete from $funneltablename where funnelid='".$_REQUEST["funnelid"]."' and id!=funnelid") or die ($db->ErrorMsg());
    
    // loop the other urls
      /*
      if (@$_REQUEST["group1"]!="") {
          $i=1;
          while ($i < 25) {
            $group="";
            $value=@$_REQUEST["value$i"];
            $field=@$_REQUEST["field$i"];
            $group=@$_REQUEST["group$i"];
            if ($group) {
                $db->Execute("insert into $funneltablename (funnelid, label, url, value) values (\"".$_REQUEST["funnelid"]."\",\"".@$field."\",\"".$group."\",\"".$value."\")");
            }
            $i++;
          }
      }
      */
      if (@$_REQUEST["group1"]=="") {
        $_REQUEST["group1"]="any";
      }
      $i=1;
      while ($i < 25) {
        $group="";
        $value=@$_REQUEST["value$i"];
        $field=@$_REQUEST["field$i"];
        $group=@$_REQUEST["group$i"];
        if ($group || $field) {
            $db->Execute("insert into $funneltablename (funnelid, label, url, value) values (\"".$_REQUEST["funnelid"]."\",\"".@$field."\",\"".$group."\",\"".$value."\")");
        }
        $i++;
      }
 
      return "<div class=\"warning ui-state-highlight ui-corner-all\">"._UPDATED.": <b>".$_REQUEST["funnelname"]."</b></div>";  
  } else {
    //echo "funnelID:$funnelid<P>"; //insert new funnel   
    
    // If the tables don't already exists, then create them...
      $tablelist = $db->MetaTables();
      if (!in_array_insensitive($funneltablename, $tablelist)) {
	    $db->Execute("CREATE TABLE $funneltablename (".
	      "id " . ($databasedriver == "sqlite"? "INTEGER PRIMARY KEY ": "int(11) NOT NULL auto_increment ") . "," .
    	    "funnelid int(11) default NULL,
            label varchar(100) NOT NULL default '',
            value double(100,2) NOT NULL default '0.00',
    	    url blob default NULL ".
	      ($databasedriver == "sqlite" ? "" : ", PRIMARY KEY  (id)") . 
	    ") ENGINE=MyISAM CHARSET=utf8");
	    //$db->Execute("CREATE INDEX {$funneltablename}_url on $funneltablename(url)");
      }
		
	  //insert the funnel
	  $db->Execute("insert into $funneltablename (label) values ('".$_REQUEST["funnelname"]."')");
      $nfid=$db->Insert_ID();
      $db->Execute("update $funneltablename set funnelid='$nfid' where id='$nfid'");
      
      // loop the other urls
      if ($_REQUEST["group1"]!="") {
          $i=1;
          while ($i < 25) {
            $group="";
            $value=@$_REQUEST["value$i"];
            $field=@$_REQUEST["field$i"];
            $group=@$_REQUEST["group$i"];
            if ($group) {
                /*
                $urls=explode(",",$group); 
                $gi=0;
                $sqlstr="";  
                while (@$urls[$gi]!="") { 
                    if (strpos($urls[$gi], "*")!=FALSE) {
                        //we do a like style
                        $urls[$gi]=str_replace("*", "%", $urls[$gi]);
                        $sqlstr.="or url like '".$urls[$gi]."' ";
                    } else {
                        //regular
                        $sqlstr.="or url='".$urls[$gi]."' ";
                    }
                    $gi++;
                }
                */
                $db->Execute("insert into $funneltablename (funnelid, label, url, value) values (\"".$nfid."\",\"".@$field."\",\"".$group."\",\"".$value."\")");
            }
            $i++;
          }
      } 
      return "<div class=\"warning ui-state-error ui-corner-all\">"._SAVED."!</div>";
      //EditFunnel();   
  }
}

function makesql($urls,$prefix) {
    $urls=explode(",",$urls); 
    $gi=0;
    $sqlstr="";
    
    while (@$urls[$gi]!="") {
        $urls[$gi]=trim($urls[$gi]);
        if ($gi==0) {
            $cond="and (";
        } else {
            $cond="or";
        }
        if (strpos(" ".$urls[$gi], "*")!=FALSE) {
            //we do a like style
            $urls[$gi]=str_replace("*", "%", $urls[$gi]);
            if (strpos(" ".$urls[$gi],"ttp://")!=FALSE) {
              $sqlstr.="$cond r.referrer like '".$urls[$gi]."' ";
            } else {
              $sqlstr.="$cond concat(u.url,up.params) like '".$urls[$gi]."' ";
            }
        } else {
            if (strpos(" ".$urls[$gi],"http:")!=FALSE) {
              $sqlstr.="$cond r.referrer='".$urls[$gi]."' ";
            } else {
              if (strpos("?",$urls[$gi])!=FALSE) {
                $sqlstr.="$cond concat(u.url,up.params)='".$urls[$gi]."' ";
              } else {
                $sqlstr.="$cond u.url='".$urls[$gi]."' ";
              }
            }
            //regular
            
        }
        $gi++;
    }
    //echo $sqlstr;
    return $sqlstr.")";
    
}

function Report() {
  global $funneltablename,$profile,$db,$from,$to,$funnelid,$data,$addlabel,$help,$addgraph,$nograph,$graphinclude,$divlabel, $arr_FCColors, $FC_ColorCounter,$labels;
   
  //load funnel data
  //echo "entered report";
  flush();
  $q = $db->Execute("select * from $funneltablename where funnelid='$funnelid' order by id");
    
  $i=0;
  while (($q) && ($data=@$q->FetchRow())) {
    if ($data['funnelid']==$data['id']) {
      //funnelname
      $funnelname=$data['label'];
      //echo $funnelname;
    } else if ($data['funnelid']!=$data['id'] && $i==0) {
      //echo "// this is the first url: ". $data['url'];
      $url1=$data['url'];
      $label1=$data['label'];
      $value1=$data['value'];
      $i++;
    } else {
      //rest
     // echo "next url: ($i)".$data['url'];
      $ourls[$i]=$data['url'];
      $olabels[$i]=$data['label'];
      $ovalues[$i]=$data['value'];
      $i++;
    }
  }
    
  // 1. get all the ips that started at the first url and put that in a temp table
  if (@$url1=="" || @$url1=="any") {
    $q = $db->Execute("select count(distinct visitorid) from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0");
    $data=@$q->FetchRow();
    //echo "Rows found:". $data[0];
    $total_pop=$data[0];
      
  } else {
    
    $query= "create temporary table temp_fips (visitorid int(11))";
    //echo "<P>$query<P>";
    flush();
    $db->Execute($query) or die (_ERROR.":" . $db->ErrorMsg());
    $sqlstr=makesql($url1,"");
    $query  = "insert into temp_fips select visitorid from $profile->tablename as a,$profile->tablename_urls as u,$profile->tablename_urlparams as up,$profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and a.url=u.id and a.params=up.id and a.referrer=r.id $sqlstr and crawl=0 group by visitorid";
    //echo "<P>$query<P>";
    flush();  
    $db->Execute($query);
    
    $q = $db->Execute("select count(distinct visitorid) from temp_fips");
    $data=@$q->FetchRow();
    //echo "Rows inserted:". $data[0];
    $total_pop=$data[0];
  }
  
  // 2. from the above get all ips that made it to the other urls
  $i=1;
  while (@$ourls[$i]!="") {
     
    if ($url1=="" || $url1=="any") {
      $sqlstr=makesql($ourls[$i], "");
      $result = $db->Execute("select count(distinct visitorid) from $profile->tablename as a,$profile->tablename_urls as u,$profile->tablename_urlparams as up,$profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and crawl=0 and a.url=u.id and a.params=up.id and a.referrer=r.id $sqlstr")  or die ("Error:" . $db->ErrorMsg());
      //echo "select count(distinct visitorid) from $profile->tablename where timestamp >=$from and timestamp <=$to $sqlstr<P>";
    } else {
      $sqlstr=makesql($ourls[$i],"m.");
      $result = $db->Execute("select count(distinct t.visitorid) from $profile->tablename as a, temp_fips as t,$profile->tablename_urls as u,$profile->tablename_urlparams as up,$profile->tablename_referrers as r where a.timestamp >=$from and a.timestamp <=$to and a.visitorid=t.visitorid and a.url=u.id and a.params=up.id and a.referrer=r.id $sqlstr")  or die ("Error:" . $db->ErrorMsg());
      //echo "<P>select count(distinct t.visitorid) from $profile->tablename as m, temp_fips as t where m.timestamp >=$from and m.timestamp <=$to $sqlstr and m.visitorid=t.visitorid<P>";
    }
    $data=@$result->FetchRow();
    $url_pop[$i]=$data[0];
    //echo "<P>$i " . $data[0];
    $i++;
  }
  
  // create data array
  $r=0;
  $data="";
  
  //set the first row
  if (!@$url1) {
      if (!@$funnelname) {
        echo "<div class=\"warning ui-state-error ui-corner-all\">No Funnel Found</div>";
        return;   
      }
      echo "<div class=\"toplinegreen\" style=\"font-size:18px;display:block;padding:2px;font-family:arial;\">Funnel: $funnelname</div>";
      echo "<div style=\"background:silver;display:block;padding:2px;\">&nbsp;".FunnelGraphToggle()."</div>";
      echo ""._DEFINITION_INCOMPLETE." <a href=\"funnels.php?conf=$profile->profilename&amp;funnelid=$funnelid&amp;funnel=edit\">"._EDIT."</a>.";
      exit();
  }
  $data[$r][0]="100";
  $data[$r][1]="<a title=\"".$url1. "\">".$label1."</a>";
  $data[$r][2]=$total_pop;
  $data[$r][3]="0";
  $data[$r][4]="0";
  $data[$r][5]=($value1*$total_pop);
  
  //set the rest
  $r=1;
  $i=1;
  while (@$ourls[$i]!="") {
    
    $data[$r][0]=@round((($url_pop[$i]/$total_pop)*100),2); 
    $data[$r][1]="<a title=\"".$ourls[$i]. "\">".$olabels[$i]."</a>";
    $data[$r][2]=$url_pop[$i];
    if ($i <= 1) {
      @$data[$r][3]=(($url_pop[$i]/$total_pop)*100)-100;
      @$data[$r][4]=(($url_pop[$i]/$total_pop)*100);
    } else {
      @$data[$r][3]=(($url_pop[$i]/$url_pop[($i-1)])*100)-100;
      @$data[$r][4]=(($url_pop[$i]/$url_pop[($i-1)])*100);
    }
    $data[$r][5]=($url_pop[$i]*$ovalues[$i]);
    $data[$r][6]=$ourls[$i];
    $i++;
    $r++;
  }
    $labels=_FUNNEL_ANALYSIS;
  
 	$showfields = _CONVERSION_PERC.","._STAGE.","._VISITORS.","._BOUNCE_RATE.","._RETENTION_PERC.","._VALUE;
    $nograph=1;
 
 	reset($data);
 	$filter="";
 	$drilldown="";
 	$query="";
    
    $help=_HELP_TEXT_FUNNELS;
    $addlabel=" <font size=+1>: $funnelname</font>";
    
    
    include_once("charts/includes/FusionCharts.php"); 
    $strXML  = "";
    $strXML .= "<chart bgcolor='FFFFFF' isSliced='1' slicingDistance='10' decimalPrecision='2' chartLeftMargin='0' chartRightMargin='0' chartTopMargin='4' numberSuffix='%'  >\n";
    //$strXML .= '<set name=\'Tested\' value=\'84\' color=\'333333\' alpha=\'85\' />\n";
    //$strXML .= "<set name='Tested' value='84' color='333333' alpha='85' />\n";
    //$strXML .= "<set name='Interviewed' value='126' color='99CC00' alpha='85' />\n";
    //$strXML .= "<set name='Candidates Applied' value='180' color='333333' alpha='85' />\n";
    //$strXML .= "</chart>";

    krsort($data);
    foreach ($data as $set) {
      $set[1]=strip_tags($set[1]);
      $strXML .= "<set name='".strip_tags($set[1])."' value='$set[0]' color='".getFCColor()."' alpha='85' hoverText='".@$set[6]."'/>\n";
    }
    $strXML .= "</chart>";    

    
    ksort($data);
    if (!$graphinclude) {
        $addgraph = renderChartHTML("charts/FCF_Funnel.swf", "", urlencode($strXML), "FunnelGraph", 250, 200, false, false, "opaque");
        echo "<div id=\"funneltable\" style=\"float:left;min-width:600px;line-height:20px;\">";
        ArrayStatsTable($from,$to,$showfields,$labels,$query,$drilldown,$filter);    
        echo "</div>";
    } else {
        echo "<div class=\"toplinegreen\" style=\"font-size:18px;display:block;padding:2px;font-family:arial;cursor:move\">Funnel: $funnelname</div>";
        echo "<div style=\"background:silver;display:block;padding:2px;\">&nbsp;".FunnelGraphToggle()."</div>";
        echo renderChartHTML("charts/FCF_Funnel.swf", "", urlencode($strXML), "FunnelGraph", 300, 250, false, false, "opaque"); 
    }
    
    //echo "<pre>$addgraph</pre>";
}


function DeleteFunnel() {
  global $funneltablename,$conf,$funnelid;
  global $db;
  $db->Execute("delete from $funneltablename where funnelid='$funnelid'") or die ($db->ErrorMsg());
  return "<div class=\"warning ui-state-error ui-corner-all\">"._DELETED_FUNNEL."!</div>";
}

?>

<?php
flush();
// PROGRAM STARTS HERE ------------------------------------------------------------------------
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
		$labels = _VISITORS_PER_DAY;
	}

if (@$_REQUEST["graphinclude"]) {
    if ($funnelid==0) {
        $funnelid=GetFunnelid();
        //echo $funnelid;    
    }
    Report();
    exit();    
}
// Build Screen

echo "<div style=\"border:0px solid black;width:100%;\">";

if ($funnel=="save") {
  $message = SaveFunnel();
  if ($funnelid) { $funnel="edit"; }
} else if ($funnel=="delete") {
  $message = DeleteFunnel();
}
SelectBox();
Sidepanel();
echo "<div style=\"margin-left:190px;\">";

if (@$message) {
  echo $message;  
}
if ($funnel=="") {
  Welcome();
} else if ($funnel=="create" || $funnel=="edit") {
  EditFunnel();
} else if ($funnel=="report") {
  if (!$funnelid) {
    Welcome();
  } else { 
    Report();
  }
}
echo "</div><div style=\"clear:both;\"></div></div>";


$rend=getmicrotime();
$rtook=number_format(($rend-$rstart),2);
echoDebug("<P>&nbsp;<p>&nbsp;<p><table width=500 cellpadding=3 border=0><tr><td rowspan=2>&nbsp;&nbsp;</td><td><font face=\"ms sans serif,arial\" size=1 color=silver>MySQL query:</font></td></tr><tr><td class=dotline2 bgcolor=#F8F8F8><font face=\"ms sans serif,arial\" size=1 color=gray>$query<P>Page took $rtook sec to build</font></td></tr></table></dir>");
?>
<P>
&nbsp;
<P>
&nbsp;
<div align=center>
<font size=1>
<a class=nodec href="credits.php<?php echo "?conf=$conf"; ?>">&copy; 2005-<?php echo date('Y');?> Logaholic BV</a></font>
</div>
<P>
&nbsp;
</body>
</html>
<?php
?>
