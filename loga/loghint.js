/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
var xmlHttp
var prevLogfileValue = "";

function showHint(str)
{
    if (getTrackermode()=='2') {
        //we are doing ftp checks
        if (str.length==0) {
            str = document.getElementById("ftpfullpath").value;
            if (str.length==0) {
                if (document.getElementById("ftpserver").value=="" && document.getElementById("ftpuser").value=="" && document.getElementById("ftppasswd").value=="") {              
                    return;
                }
            }
        }
        showFTPHint(str);
        return;
    }
    
    if (str.length==0) {
        str = document.getElementById("logfilefullpath").value;
        if (str.length==0) { return; }
    }

	// make sure we replace windows style slashes with unix style slashes
    str = unixslash(str,'logfilefullpath');
    
    //if (prevLogfileValue != str) {
		xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null)
		{
			alert ("Browser does not support HTTP Request")
			return
		} 
		
		var url="loghint.php"
		url=url+"?q="+escape(str)+"&splitfilter="+document.getElementById("splitfilter").value+"&splitfilternegative="+document.getElementById("splitfilternegative").value;
		url=url+"&sid="+Math.random()
		xmlHttp.onreadystatechange=stateChanged
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
		
		prevLogfileValue = str;
	//}
} 

function stateChanged() 
{ 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{ 
		document.getElementById("txtHint").innerHTML=xmlHttp.responseText 
	} 
} 

function GetXmlHttpObject()
{ 
	var objXMLHttp=null
	if (window.XMLHttpRequest)
	{
		objXMLHttp=new XMLHttpRequest()
	}
	else if (window.ActiveXObject)
	{
		objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
	}
	return objXMLHttp
}

function showFTPHint(str) {
    $.ajax({
       type: "POST",
       url: "loghint.php",
       data: "ftpserver="+document.getElementById("ftpserver").value+"&ftpuser="+document.getElementById("ftpuser").value+"&ftppasswd="+document.getElementById("ftppasswd").value+"&ftpfullpath="+str+"&splitfilter="+document.getElementById("splitfilter").value+"&splitfilternegative="+document.getElementById("splitfilternegative").value,
       success: function(msg){
         if (msg!="") {
            document.getElementById("ftpHint").innerHTML=msg;
         }
       }
     });
         
}

function getTrackermode() {
    var radioButtons = document.getElementsByName("trackermode");
    for (var x = 0; x < radioButtons.length; x ++) {
        if (radioButtons[x].checked) {
            return radioButtons[x].value;
        }
    }
}
