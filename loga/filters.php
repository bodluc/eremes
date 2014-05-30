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

$command = @$_REQUEST["command"];
$edit = @$_REQUEST["edit"];
$del = @$_REQUEST["del"];


if (isset($print)) {
 $noheader=1;
}

if(empty($new_ui)) {
	include_once("top.php");
} else {
	echo $template->HTMLheadTag();
}

function Sidepanel() {
  global $conf,$profile, $db, $edit;
  $q = $db->Execute("select id,sourcename,category from ".TBL_TRAFFIC_SOURCES." where profileid='$profile->profileid' order by category, sourcename");
  if (!$q) {
  	// Probably no table.  When we create a test, we'll create the table.
	}
  
  ?>
  <?php
  
  //echo "</td></tr><tr><td rowspan=5 valign=top width=140>";
  echo "<div style=\"float:left;margin-right:10px;width:190px;\">";
  $i=0;
  $lastcat="";
  
  echo "<div id=\"accordion\"> ";
  //echo "<h3 class=\"accordion_header_first\"><a href=\"#\">"._AVAILABLE_FILTERS."</a></h3>";
  //echo "<div class=\"reportmenu\"><ul>";
  $icon = "images/icons/filters.gif";
  //echo " <li> <a href=\"#\" class=\"sidelinks\" style=\"background-image: url($icon);\">New Visitors (Built in)</a> </li>";
  //echo " <li> <a href=\"#\" class=\"sidelinks\" style=\"background-image: url($icon);\">Return Visitors (Built in)</a> </li>";    
  $icon = "images/icons/savedtest.gif";
  $ii=0;  
  while (($q) && ($data=@$q->FetchRow())) {
    if ($lastcat!=$data['category']) {
        if ($lastcat!="") { echo "</ul></div>"; }
        $lastcat=$data['category'];
        echo "<h3 class=\"accordion_header\"><a href=\"#\">$lastcat</a></h3>";
        echo "<div class=\"reportmenu\"><ul>";
        $active=$ii++;    
    }
    if ($edit==$data['id']) { 
        $openactive=$active;
        $s = "background: #CCFFCC no-repeat left;"; 
    } else {
        $s="";   
    } 
    if ($data['sourcename']=="New Visitors" || $data['sourcename']=="Return Visitors") {
        echo " <li> <a href=\"javascript:alert('{$data['sourcename']} is a built in Segementation filter and cannot be changed.')\" class=\"sidelinks\" style=\"$s background-image: url($icon);\">". $data['sourcename'] . "</a> </li>";
    } else {
        echo " <li> <a href=\"filters.php?conf=$conf&edit=". $data['id'] . "&command=".strtolower(_EDIT)."\" class=\"sidelinks\" style=\"$s background-image: url($icon);\">". $data['sourcename'] . "</a> </li>";
    }
    //echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td><img src=images/icons/savedtest.gif width=16 height=16 align=left></td><td><a href=filters.php?conf=$conf&edit=". $data['id'] . "&command=".strtolower(_EDIT).">". $data['sourcename'] . "</a><br>";
    $i++;
  }
  echo "</ul></div></div>";
  echo "&nbsp;<br><a class=\"extrabuttons ui-state-default ui-corner-all\" href=\"filters.php?conf=$conf\">"._CREATE_NEW_FILTER."</a><br>&nbsp;";
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
  return;
  
  /*
  echo "<table width=100% cellpadding=4 cellspacing=0 class=\"smallborder ui-corner-all\">";
  echo "<tr><td><img src=images/icons/filters.gif width=16 height=16 align=left></td><td><b>"._AVAILABLE_FILTERS.":</b></td></tr>";
  
  while (($q) && ($data=@$q->FetchRow())) {
	  if ($lastcat!=$data['category']) {
	    $lastcat=$data['category'];
      echo "<tr><td colspan=2><i>$lastcat:</i></td></tr>";    
    }
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td><img src=images/icons/savedtest.gif width=16 height=16 align=left></td><td><a href=\"filters.php?conf=$conf&edit=". $data['id'] . "&command=".strtolower(_EDIT)."\">". $data['sourcename'] . "</a><br>";
    echo "<a href=filters.php?conf=$conf&del=". $data['id'] . "&command=".strtolower(_DELETE)." class=graylink>".strtolower(_DELETE)."</a></td></tr>";
    $i++;
  }
  */
  
  if ($i==0) {
    echo "<tr><td>"._NONE."</td></tr>";
  }
  echo "<tr><td colspan=2><a class=graylink href=\"filters.php?conf=$conf\">"._CREATE_NEW_FILTER."</a></td></tr>";
  echo "</table></div><br>";
  //echo "</table></td><td valign=top>";
}

function SelectBox() {
  global $conf,$profile,$command;
  // Create Date Selector  
  echo "<div>";
	echo "<br><font size=3><b><a href=filters.php?conf=$conf class=nodec4>"._LOGAHOLIC_FILTERS."</a></b>";
	if (!$command) {
	 echo ": "._CREATE_NEW_FILTER;
	} else if ($command==strtolower(_EDIT)) {
   echo ": "._EDIT_FILTER;
  } else if ($command==strtolower(_DELETE)) {
    echo ": "._DELETE_FILTER;
  }
  echo "</font>";
  echo "<a style='float: right; margin-left: 7px; cursor: pointer; text-decoration: underline; margin-bottom: 10px;' class='filters_help'>"._HELP."</a>";
  echo "</div><div class=\"breaker\"></div>";
  echo "<script type='text/javascript'>
$(document).ready(function() {
	$('.filters_help').click(function() {
		if($('.filter_explanation').is(':hidden') == true) {
			$('.filter_explanation').show();
		} else {
			$('.filter_explanation').hide();
		}
	});
});
	</script>";
  echo "<div class='filter_explanation' style='margin: 10px; display: none; float; right;'>";
  echo _FILTER_INFO_AND_EXPLAIN;
  echo _LIST_OF_AVAILABLE_FIELDS;
  echo "</div>";
}

function CategorySelect($item) {
 global $conf, $profile, $db, $edit;
 $output="";
  $q = @$db->Execute("select category from ".TBL_TRAFFIC_SOURCES." where profileid='$profile->profileid' and category!='"._MARKETING_CAMPAIGNS."' and category!='"._GEOGRAPHIC_SEGMENTS."' and category!='"._SEGMENTS."' group by category");
  while (($q) && ($data=@$q->FetchRow())) {
      if ($item==$data['category']) {
        $sel=" selected";
      } else {
        $sel="";
      }
      $output.="<option value=\"".$data['category']."\" $sel>".$data['category'] . "</option>\n";
  }
  ?>
  <option value=<?php echo "\""._MARKETING_CAMPAIGNS."\""; if ($item==_MARKETING_CAMPAIGNS) { echo " selected"; } ?>><?php echo _MARKETING_CAMPAIGNS;?>
  <option value=<?php echo "\""._GEOGRAPHIC_SEGMENTS."\""; if ($item==_GEOGRAPHIC_SEGMENTS) { echo " selected"; } ?>><?php echo _GEOGRAPHIC_SEGMENTS;?>
  <option value=<?php echo "\""._SEGMENTS."\""; if ($item==_SEGMENTS) { echo " selected"; } ?>><?php echo _SEGMENTS;?>
  <?php
  echo $output;
}

function EditFilter() {
  global $conf,$profile, $edit,$db;
  $fieldselect = LoadFieldSelect();
  $q = $db->Execute("select * from ".TBL_TRAFFIC_SOURCES." where id='$edit' and profileid='$profile->profileid'");
  $data=$q->FetchRow();
  //echo $data['sourcecondition'];
  if (strpos($data['sourcecondition']," AND ")!=FALSE) {
    $rows=explode(" AND ", $data['sourcecondition']);
    $andor="AND";
  } else if (strpos($data['sourcecondition']," OR ")!=FALSE) {
    $rows=explode(" OR ", $data['sourcecondition']);
    $andor="OR";
  } else {
    $rows[0]= $data['sourcecondition'];
    $andor="AND";
  }
  $i=0;
  while (@$rows[$i]!="") {
    if (strpos($rows[$i]," NOT LIKE ")!=FALSE) {
      $rsplit=explode(" NOT LIKE ", $rows[$i]);
      $condition[$i]="nocontain";
      $field[$i]=$rsplit[0];
      if (substr($rsplit[1],0,2)!="'%") {
        $condition[$i]="nostart";
        $cval[$i]=substr($rsplit[1],1,-2);
      } else {
        $cval[$i]=substr($rsplit[1],2,-2);
      }
    } else if (strpos($rows[$i]," LIKE ")!=FALSE) {
      $rsplit=explode(" LIKE ", $rows[$i]);
      $field[$i]=$rsplit[0];
      if (substr($rsplit[1],0,2)=="'%") {
        $condition[$i]="end";
        $cval[$i]=substr($rsplit[1],2,-1);
      }
      if (substr($rsplit[1],-2)=="%'") {
        if (@$condition[$i]=="") {
          $condition[$i]="start";
          $cval[$i]=substr($rsplit[1],1,-2);
        } else {
          $condition[$i]="contains";
          $cval[$i]=substr($rsplit[1],2,-2);
        }
      }
    } else if (strpos($rows[$i],"!=")!=FALSE) {
      $rsplit=explode("!=", $rows[$i]);
      $condition[$i]="isnot";
      $field[$i]=$rsplit[0];
      $cval[$i]=substr($rsplit[1],1,-1);
    } else if (strpos($rows[$i],"=")!=FALSE) {
      $rsplit=strpos($rows[$i],"=");
      $condition[$i]="is";
      $field[$i]=substr($rows[$i],0,$rsplit);
      $cval[$i]=substr($rows[$i],($rsplit+2),-1);
    }    
    $i++;
  } 
  ?>
  <div style="float:left;">
  <form name="form1" method=post action="filters.php">
  <table cellpadding=5 width=100%>
  <tr><td><?php echo _FILTER_NAME;?>: </td><td><input type=text name=name class="bigform" size=25 value="<?php echo $data['sourcename']; ?>"> 
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo "<a href=filters.php?conf=$conf&del=". $edit . "&command=".strtolower(_DELETE)." class=\"extrabuttons ui-state-default ui-corner-all\">".strtolower(_DELETE)."</a>"; ?>
  </td></tr>
  <tr><td><?php echo _FILTER_CATEGORY;?>: </td>
  <td><select name=category>
    <?php CategorySelect($data['category']); ?>
    </select> <a class=graylink href="javascript:catadd('newcat');"> <?php echo _ADD_NEW_CATEGORY;?></a>
  </td></tr>
  <tr id="newcat" style="display:none;"><td align=right><?php echo _NEW_CATEGORY;?>: </td><td><input type=text name=newcategory></td></tr>
  <tr><td colspan=2>
  <?php echo _FILTER_STATISTICS_TO_INCLUDE_VISITORS;?>:<p>
  <input type=radio name=andor value="OR" <?php if ($andor=="OR") { echo "checked"; }?>> <?php echo _ANY_OF_FOLLOWING_CRITERIA;?><br>
  <input type=radio name=andor value="AND" <?php if ($andor=="AND") { echo "checked"; }?>> <?php echo _ALL_OF_FOLLOWING_CRITERIA;?><P>
    
    <table cellpadding=4 class=dotline2>
    <tr><td width=78><?php echo _FIELD;?></td><td width=110><?php echo _CONDITION;?></td><td width=200><?php echo _VALUE;?></td><td></td></tr></table>
    <?php
    $i=1;
    while ($i < 25) {
      if (@$cval[$i-1]!="") {
        echo "<div id=\"$i\"><table cellpadding=4 border=0><tr>";
        ?>
        <td><select name="<?php echo "field".$i; ?>" id="<?php echo "field".$i; ?>"><?php $fieldselect = SelectField($fieldselect,$field[$i-1]); echo $fieldselect;?></select></td>
        <td><select name="<?php echo "condition".$i; ?>">
              <option></option>
              <option value="is" <?php if ($condition[$i-1]=="is") { echo "selected"; }?>><?php echo _IS_SELECT;?></option>
              <option value="start" <?php if ($condition[$i-1]=="start") { echo "selected"; }?>><?php echo _STARTS_WITH;?></option>
              <option value="end" <?php if ($condition[$i-1]=="end") { echo "selected"; }?>><?php echo _ENDS_WITH;?></option>
              <option value="contains" <?php if ($condition[$i-1]=="contains") { echo "selected"; }?>><?php echo _CONTAINS;?></option>
              <option value="isnot" <?php if ($condition[$i-1]=="isnot") { echo "selected"; }?>><?php echo _IS_NOT;?></option>
              <option value="nostart" <?php if ($condition[$i-1]=="nostart") { echo "selected"; }?>><?php echo _DOES_NOT_START_WITH;?></option>
              <option value="nocontain" <?php if ($condition[$i-1]=="nocontain") { echo "selected"; }?>><?php echo _DOES_NOT_CONTAIN;?></option>
            </select>
        </td>
        <td><input type=text id="<?php echo "cvalue".$i; ?>" name="<?php echo "cvalue".$i; ?>" value="<?php echo $cval[$i-1];?>" <?php echo "onkeyup=\"QBuilderHelpForms(document.form1.elements['field$i'].value, event, this.value+'@'+this.id+'@'+document.form1.elements['field$i'].value, 'forminput');\" onclick=\"QBuilderHelpForms(document.form1.elements['field$i'].value, event, this.value+'@'+this.id+'@'+document.form1.elements['field$i'].value, 'forminput');\"";?> autocomplete="off"></td>
        <td>
          <input type=button value=" + " onclick="add('<?php $i++; echo $i; ?>')">&nbsp;<input type=button value=" - " onclick="remove('<?php echo ($i-1); ?>')" <?php if ($i==2) { echo "disabled"; }?>> 
        </td>
        </tr></table></div>
        <?php
      } else {
        echo "<div id=\"$i\" style='display:none;'><table cellpadding=4 border=0><tr>";
        ?>
        <td><select id="<?php echo "field".$i; ?>" name="<?php echo "field".$i; ?>"><?php echo $fieldselect;?></select></td>
        <td><select name="<?php echo "condition".$i; ?>">
              <option></option>
              <option value="is"><?php echo _IS_SELECT;?></option>
              <option value="start"><?php echo _STARTS_WITH;?></option>
              <option value="end"><?php echo _ENDS_WITH;?></option>
              <option value="contains"><?php echo _CONTAINS;?></option>
              <option value="isnot"><?php echo _IS_NOT;?></option>
              <option value="nostart"><?php echo _DOES_NOT_START_WITH;?></option>
              <option value="nocontain"><?php echo _DOES_NOT_CONTAIN;?></option>
            </select>
        </td>
        <td><input type=text name="<?php echo "cvalue".$i; ?>" id="<?php echo "cvalue".$i; ?>" value="" <?php echo "onkeyup=\"QBuilderHelpForms(document.form1.elements['field$i'].value, event, this.value+'@'+this.id+'@'+document.form1.elements['field$i'].value, 'forminput');\" onclick=\"QBuilderHelpForms(document.form1.elements['field$i'].value, event, this.value+'@'+this.id+'@'+document.form1.elements['field$i'].value, 'forminput');\"";?> autocomplete="off"></td>
        <td>
          <input type=button value=" + " onclick="add('<?php $i++; echo $i; ?>')">&nbsp;<input type=button value=" - " onclick="remove('<?php echo ($i-1); ?>')" <?php if ($i==2) { echo "disabled"; }?>> 
        </td>
        </tr></table></div>
        <?php
      }
    }
    ?> 
    
    <input type=hidden name="conf" value="<?php echo $conf;?>">
    <input type=hidden name="edit" value="<?php echo $edit;?>">
    <input type=hidden name="command" value="<?php echo strtolower(_SAVE);?>">
    <input type=submit value="<?php echo _SAVE;?>">
    </form>
    
  </td></tr>
  </table>
  </div>
  
  
  <?php

}

function CreateFilterForm() {
  global $conf;
  $fieldselect = LoadFieldSelect();
  ?>
  <div style="float:left;">
  <form name="form1" method=post action="filters.php">
  <table cellpadding=5 width=100%>
  <tr><td><?php echo _FILTER_NAME;?>: </td><td><input type=text name=name class="bigform" value='<?php echo @$_REQUEST["name"]; ?>'></td></tr>
  <tr><td><?php echo _FILTER_CATEGORY;?>: </td>
  <td><select name=category>
    <?php CategorySelect($data['category']); ?>
    </select> <a class=graylink href="javascript:catadd('newcat');"> <?php echo _ADD_NEW_CATEGORY;?></a>
  </td></tr>
  <tr id="newcat" style="display:none;"><td align=right><?php echo _NEW_CATEGORY;?>: </td><td><input type=text name=newcategory></td></tr>
  <tr><td colspan=2>
  <?php echo _FILTER_STATISTICS_TO_INCLUDE_VISITORS;?>:<p>
  <input type=radio name=andor value="OR"><?php echo _ANY_OF_FOLLOWING_CRITERIA;?><br>
  <input type=radio name=andor value="AND" checked><?php echo _ALL_OF_FOLLOWING_CRITERIA;?><P>
  
    <table cellpadding=4 class=dotline2>
    <tr><td width=78><?php echo _FIELD;?></td><td width=110><?php echo _CONDITION;?></td><td width=200><?php echo _VALUE;?></td><td></td></tr></table> 
    <?php
    if (@$_REQUEST["field1"]!="") {
      $fieldselect = SelectField($fieldselect,@$_REQUEST["field1"]);
      ?>
      <div><table cellpadding=4 border=0><tr><td><select id="field1" name="field1"><?php echo $fieldselect;?></select></td>
      <td><select name="condition1">
            <?php echo "<option value=\"".@$_REQUEST["condition1"]."\" SELECTED>".@$_REQUEST["condition1"];?>
            <option value="is"><?php echo _IS_SELECT;?></option>
            <option value="start"><?php echo _STARTS_WITH;?></option>
            <option value="end"><?php echo _ENDS_WITH;?></option>
            <option value="contains"><?php echo _CONTAINS;?></option>
            <option value="isnot"><?php echo _IS_NOT;?></option>
            <option value="nostart"><?php echo _DOES_NOT_START_WITH;?></option>
            <option value="nocontain"><?php echo _DOES_NOT_CONTAIN;?></option>
          </select>
      </td>
      <td><input type=text name="cvalue1" id="cvalue1" value="<?php echo @$_REQUEST["cvalue1"] ?>" <?php echo "onkeyup=\"QBuilderHelpForms(document.form1.elements['field1'].value, event, this.value+'@'+this.id+'@'+document.form1.elements['field1'].value, 'forminput');\" onclick=\"QBuilderHelpForms(document.form1.elements['field1'].value, event, this.value+'@'+this.id+'@'+document.form1.elements['field1'].value, 'forminput');\"";?> autocomplete="off"></td>
      <td>
        <input type=button value=" + " onclick="add('2')">&nbsp;<input type=button value=" - " disabled> 
      </td>
      </tr></table></div>
      <?php
      $i=2;
    } else {
      $i=1;
    }
    $fieldselect = LoadFieldSelect();
    while ($i < 25) {
      if ($i==1) {
        echo "<div><table cellpadding=4 border=0><tr>";
      } else {
        echo "<div id=\"$i\" style='display:none;'><table cellpadding=4 border=0><tr>";
      }
      
      ?>
      <td><select name="<?php echo "field".$i; ?>" id="<?php echo "field".$i; ?>"><?php echo $fieldselect;?></select></td>
      <td><select name="<?php echo "condition".$i; ?>">
            <option></option>
            <option value="is"><?php echo _IS_SELECT;?></option>
            <option value="start"><?php echo _STARTS_WITH;?></option>
            <option value="end"><?php echo _ENDS_WITH;?></option>
            <option value="contains"><?php echo _CONTAINS;?></option>
            <option value="isnot"><?php echo _IS_NOT;?></option>
            <option value="nostart"><?php echo _DOES_NOT_START_WITH;?></option>
            <option value="nocontain"><?php echo _DOES_NOT_CONTAIN;?></option>
          </select>
      </td>
      <td><input type=text name="<?php echo "cvalue".$i; ?>" id="<?php echo "cvalue".$i; ?>" value="" <?php echo "onkeyup=\"QBuilderHelpForms(document.form1.elements['field$i'].value, event, this.value+'@'+this.id+'@'+document.form1.elements['field$i'].value, 'forminput');\" onclick=\"QBuilderHelpForms(document.form1.elements['field$i'].value, event, this.value+'@'+this.id+'@'+document.form1.elements['field$i'].value, 'forminput');\"";?> autocomplete="off"></td>
      <td>
        <input type=button value=" + " onclick="add('<?php $i++; echo $i; ?>')">&nbsp;<input type=button value=" - " onclick="remove('<?php echo ($i-1); ?>')" <?php if ($i==2) { echo "disabled"; }?>> 
      </td>
      </tr></table></div>
      <?php
    }
    ?> 

    <input type=hidden name=conf value="<?php echo $conf;?>">
    <input type=hidden name=command value="<?php echo strtolower(_SAVE);?>">
    <input type=submit value=<?php echo _SAVE;?>>
    </form>
  </td></tr>
  </table>
  </div>
  
  <?php

}

function DeleteFilter() {
  global $conf,$edit,$profile,$del;
  global $db;
  $db->Execute("delete from ".TBL_TRAFFIC_SOURCES." where id='$del' and profileid='$profile->profileid'") or die ($db->ErrorMsg());
  $db->Execute("drop table if exists ".$profile->tablename."_SEGMENT_".$del);
  SelectBox();
  Sidepanel();
  echo "<P><div class=\"fwarning ui-state-error ui-corner-all\"><b>"._DELETED_FILTER."!</b></div>";
}

function EvaluateFilter($sqlstring) {
    // Lets do a series of tests to see if we should throw any warnings at the user,
    // because builing a query could potatially be dangerous for abuse or high server load times
    $warning="";
    
    //warn if a user has a wildcard at start of string
    if (strpos($sqlstring,"'%")!==FALSE) {
        //echo "lalala";
        $warning .= "<b><i>Warning:</b></i> You are using a <i>Contains/Ends With/Does Not Contain</i> condition.<br> Always try to avoid using this because it is slow when a condition matches a lot of records.<br> Always try using <i>Starts with</i> or <i>Is</i> or <i>Does Not Start With</i> instead.<br>";   
    }     
    // warn if a user uses the same field twice in an AND condtion
    if (getOperator($sqlstring)=="AND") {
        $wordcount = Array();
        foreach(arrayConditions($sqlstring) as $condition) {
            //we're only interested in the first part
            $condition = trim($condition);
            $str = substr($condition, 0, strpos($condition," "));
            //echo "the condition is $condition, we turned that into $str<br>";
            @$wordcount[$str]++;        
        }
        while (list($key, $val) = each($wordcount)) {
            if ($val > 1) {
                $warning .= "<b><i>Warning:</b></i> You have used the field '<b>$key</b>' $val times. This filter wil  not match any records.<br> You can only use this field ONCE in a filter that is set to match <b>ALL</b> (<i>and</i>) conditions.<br> Please give this field only one value, or switch to a filter that matches <b>ANY</b> (<i>or</i>) condition.<br>";   
            }
        }
    } else {
    // warn if a user uses NOT LIKE or != in an OR condition
        if (count(arrayConditions($sqlstring)) > 1 && hasNegativeCondition($sqlstring)==true) {
            $warning .= "<b><i>Warning:</b></i> You have defined multiple (negative) conditions with the ANY (<i>or</i>) setting.<br> This can result in very slow reporting if the filter matches a lot of records.";
        }
    }
    return $warning;
    
        
       
}

function Welcome() {
  global $conf;
  SelectBox();
  echo "<div class='clear'></div>";
  Sidepanel();
  echo "<div><table cellpadding=5><tr><td colspan='2' valign=top style='line-height:20px;'>";
  echo _DEFINE_A_GROUP_OF_VISITORS;
  CreateFilterForm();
  echo "</td>";?>
  
  <?php
  //echo "It means both versions of the page will be online side by side, some people will only see page A, others only see page B. After the test has run a while, you can come back and check the performance of each variation, in terms of converting users to one of your target pages.<P>Full Tutorial";
  echo "</tr></table></div>";
  
}

/// Start Page

if ($command==strtolower(_SAVE)) {
  $i=1;
  $filtername=@$_REQUEST["name"];
  $category=@$_REQUEST["category"];
  $newcategory=@$_REQUEST["newcategory"];
  if ($newcategory!="") {
    $category=$newcategory;
  }
  $andor=@$_REQUEST["andor"];
  while ($i < 25) {
    //$tmp="_REQUEST[\"cvalue".$i."\"]";
    $cvalue="";
    $cvalue=$_REQUEST["cvalue$i"];
    if ($cvalue!="") {
      $field=@$_REQUEST["field$i"];
      $condition=@$_REQUEST["condition$i"];
      //make sql string
      if ($condition=="contains") {
        $op = "$field LIKE '%$cvalue%'";
      } else if ($condition=="nocontain") {
        $op = "$field NOT LIKE '%$cvalue%'";
        $warn_nocontain=1;
      } else if ($condition=="nostart") {
        $op = "$field NOT LIKE '$cvalue%'";
        $warn_nocontain=1;
      } else if ($condition=="start") {
        $op = "$field LIKE '$cvalue%'";
      } else if ($condition=="end") {
        $op = "$field LIKE '%$cvalue'";
      } else if ($condition=="is") {
        $op = "$field ='$cvalue'";
      } else if ($condition=="isnot") {
        $op = "$field !='$cvalue'";
      }
      if ($i==1) {
        $sqlstring="$op";
      } else {
        $sqlstring.=" $andor $op";
      }
    }
    $i++;
  }

  if ($edit=="") { // for new filters
    if (isset($sqlstring)) {
        if ($filtername=="") {
            $filtername=_UNNAMED_FILTER;
        } 
		// echo "insert into ".TBL_TRAFFIC_SOURCES." (profileid,sourcename,sourcecondition,category) values ('$profile->profileid', '$filtername',\"$sqlstring\",'$category')";
        $db->Execute("insert into ".TBL_TRAFFIC_SOURCES." (profileid,sourcename,sourcecondition,category) values ('$profile->profileid', '$filtername',\"$sqlstring\",'$category')");
        $edit = $db->Insert_ID();
        $message= "<div class=\"fwarning ui-state-highlight ui-corner-all\"><b>"._NEW_FILTER_SAVED.":</b> $filtername ($category)</div>";
    } else {
        $message= "<div class=\"fwarning ui-state-error ui-corner-all\">"._PROBLEM_NOTHING_TO_SAVE."</div>";
		SelectBox();
		Sidepanel();
		echo $message;
		CreateFilterForm();
		echo "</body></html>";
		exit;
    }
  } else { // for saving existing filters
    $db->Execute("update ".TBL_TRAFFIC_SOURCES." set profileid='$profile->profileid', sourcename='$filtername',sourcecondition=\"$sqlstring\", category='$category' where id='$edit' and profileid='$profile->profileid'");
    $message= "<div class=\"fwarning ui-state-highlight ui-corner-all\"><b>"._FILTER_UPDATED.":</b> $filtername ($category)</div>";
    // when a filter has been edited, we need to reset the old table;
    $db->Execute("drop table if exists ".$profile->tablename."_SEGMENT_".$edit);
    setProfileData($profile->profilename,$profile->profilename."SEGMENT_".$edit."_Range","0");
  }
  SelectBox();
  Sidepanel();
  //echo "<P><table width=100% cellpadding=5><tr><td valign=top>"; 
  //echo "$message</td></tr></table>";
  echo $message;
  if ($warning = EvaluateFilter(@$sqlstring)) {
    //echo "<div class=\"warning\"><table width=\"100%\"><tr><td>$warning</td></tr></table></div>";
    echo "<div class=\"fwarning ui-state-error ui-corner-all\">$warning</div>";    
  }
  EditFilter(); 
    
} else if ($command==strtolower(_EDIT)) {
    SelectBox();
    Sidepanel();
    EditFilter();
} else if ($command==strtolower(_DELETE)) {
    DeleteFilter();
} else {
    Welcome();
}

if (@$warn_nocontain==1) {
    /*
    ?>
    <div class="indentbody"><?php echo _PLEASE_NOTE_KNOWN_PROBLEM;?></div>
    <?php
    */
}

?>
</body>
</html>
