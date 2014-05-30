<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
//PHPCIPHER NO ENCRYPT
// PHPLOCKITOPT NOENCODE
//required variables
include("files/global.php");
$conf=$_REQUEST['conf'];
$testid=$_REQUEST['testid'];;
$visitormethod=$_REQUEST['vmethod'];
$jsmode=$_REQUEST['jsmode'];

$cookiename="lg".$conf.$testid;

//get data
if (!empty($_REQUEST['tn'])) {
    $sptablename=$_REQUEST['tn']."_splittests";
    $sptablename_results=$_REQUEST['tn']."_splittests_results";
} else {
    $sptablename=$conf."_splittests";
    $sptablename_results=$conf."_splittests_results";
}

$conn = mysql_connect($mysqlserver,$mysqluname,$mysqlpw) or die ($db->ErrorMsg());
$sel = mysql_select_db($DatabaseName, $conn);
//mysql_query("SET NAMES utf8");
//$q = $db->Execute("select * from $sptablename where id=$testid");
$q = mysql_query("select * from $sptablename where id=$testid");
//$data=$q->FetchRow();
$data=mysql_fetch_array($q);

//split traffic
if (!isset($$cookiename)) {
		// New User: determine which content to present and store the choice in a cookie
		if (!$data['splitperc']) {
      $data['splitperc']=50;
    }
  	$stest=rand(0,100);
    if ($stest < (100- $data['splitperc'])) {
		    SetCookie($cookiename,"A",time() + 8640000,"/",$_SERVER['HTTP_HOST'],0);
				$splittester="A";
		} else {
				SetCookie($cookiename,"B",time() + 8640000,"/",$_SERVER['HTTP_HOST'],0);
				$splittester="B";
		}
}
//echo $$cookiename . " ($cookiename)<P>";

// $page is an overrule parameter
if (isset($page)) {
    if (strtoupper($page)=="A") {
      echo stripslashes($data['pagea']);
      exit();
    } else if (strtoupper($page)=="B") {
      echo stripslashes($data['pageb']);
      exit();
    }
}

// Get some variables to track
$ipnumber = $_SERVER["REMOTE_ADDR"];
$useragent = $_SERVER['HTTP_USER_AGENT'];
$timestamp= time();

// This won't work because we don't have a profile, but I'm not sure what else to do withoout totally fixing the *rest* of this
// file.  Delaying for now, just inserting code...
//reworked ....
if ($visitormethod == 1) {
	$visitorid = md5($ipnumber);
} else if ($visitormethod == 2) {
	$visitorid = md5($ipnumber . ':' . $useragent);
}


//spit content & update stats
if (@$$cookiename=="A" || $splittester=="A") {
  //$db->Execute("insert into $sptablename_results set testid='$testid', visitorid='$visitorid',timestamp='$timestamp',page='A'") or die ($db->ErrorMsg());
  mysql_query("insert into $sptablename_results set testid='$testid', visitorid='$visitorid',timestamp='$timestamp',page='A'");
  if (@$jsmode) {
    $clean=$data['pagea'];
    
  } else {
    echo stripslashes($data['pagea']);
  }
} else {
  //$db->Execute("insert into $sptablename_results set testid='$testid', visitorid='$visitorid',timestamp='$timestamp',page='B'") or die ($db->ErrorMsg());
  mysql_query("insert into $sptablename_results set testid='$testid', visitorid='$visitorid',timestamp='$timestamp',page='B'") or die ($db->ErrorMsg());
  if (@$jsmode) {
    $clean=$data['pageb'];
     
  } else {
    echo stripslashes($data['pageb']);
  }
}
if ($clean) {
    
    $clean=str_replace("\n","",$clean);
    $clean=str_replace("\r","",$clean); 
    $clean= str_replace(chr(10), " ", $clean); //remove carriage returns
    $clean = str_replace(chr(13), " ", $clean); //remove carriage returns
    //$clean = htmlspecialchars_decode($clean);
    
    /*
    ?>
    var newcontent = document.createElement('p');   
    newcontent.id = 'syndicated-content';   
    <?php
    //echo "newcontent.appendChild(document.createTextNode('Here is some syndicated content'));"; 
    //echo "newcontent.appendChild(document.createTextNode('".addslashes($clean)."'));"; 
    echo "newcontent.appendChild(document.createTextNode('$clean'));";         
    ?>                                                                       
    var scr = document.getElementById('abtester');   
    scr.parentNode.insertBefore(newcontent, scr);  
      
    <?php 
    */ 
    //echo "document.write(\"".$data['pageb']."\");";
    //echo "document.getElementById(\"abtester\").innerHTML=\"$clean\";";
    
    if ($jsmode==1) {
       echo "document.getElementById(\"abtester\").innerHTML=\"".str_replace("\"","\\\"",$clean)."\";"; 
    } else if ($jsmode==2) {
       ?>    
        function createNewDoc()
        { 
        var newDoc=document.open("text/html","replace");
        var txt="<?php echo str_replace("\"","\\\"",$clean); ?>";
        newDoc.write(txt);    
        newDoc.close();
        }
        
        if (document.all) {
              document.write('<body onload="createNewDoc()">'); 
        } else {
              document.write('<body>'); 
              createNewDoc();  
        }
       
        <?php        
    }

}
?>
