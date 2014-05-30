/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
// JavaScript Document

function rowOverEffect(object) {
    if (object.className == 'navborder') object.className = 'navborderhighlight';
}

function rowOutEffect(object) {
    if (object.className == 'navborderhighlight') object.className = 'navborder';
}

function drowOverEffect(object) {
    if (object.className == 'dbox') object.className = 'dboxhighlight';
}

function drowOutEffect(object) {
    if (object.className == 'dboxhighlight') object.className = 'dbox';
}

function srrowOverEffect(object) {
      if (object.className == 'profilerow') object.className = 'profilerowhighlight';
}
  
function srrowOutEffect(object) {
      if (object.className == 'profilerowhighlight') object.className = 'profilerow';
}
  
function add(row) 
    {
        document.getElementById(row).style.display="block";
    }
function catadd(row) 
    {
      if (document.getElementById(row).style.display=="none") {
        document.getElementById(row).style.display="block";
        document.form1.category.disabled=true;
      } else {
        document.getElementById(row).style.display="none";
        document.form1.category.disabled=false;
      }  
    }
    
function remove(row) 
    {
        document.getElementById(row).style.display="none";
        field='cvalue'+row;
        document.form1.elements[field].value="";
        field='field'+row;
        document.form1.elements[field].value="";
        field='condition'+row;
        document.form1.elements[field].value="";
    }
function remove2(row) 
    {
        document.getElementById(row).style.display="none";
        field='cvalue'+row;
        document.form1.elements[field].value="";
        field='field'+row;
        document.form1.elements[field].value="";
        field='condition'+row;
        document.form1.elements[field].value="";
    }
function reveal(row) 
    {
        document.getElementById(row).style.display="block";
    }   
function close(row) 
    {
        document.getElementById(row).style.display="none";
    }      


    
var date_selector =
{
  frm:null,
  init:function(frm1)
  {
    this.frm=frm1;
  },
  getMonthTable:function(year, month, date)
  {
    var tableText = "";
    var months =
    [
      "January", "February", "March", "April", "May", "June", "July",
      "August", "September", "October", "November", "December"
    ];
    var days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    month = (month == null || isNaN(month) ? null : month);
    if(year == null || isNaN(year) || month == null || isNaN(month))
    {
      var dt = new Date();
      year = dt.getFullYear();
      month = dt.getMonth();
      date = dt.getDate();
    }
    date = (date == null || isNaN(date) ? null : date);
    var previousMonth = new Date(year, month-1, 1);
    var nextMonth = new Date(year, month+1, 1);
    tableText =
    (
      "<table align=\"left\" id=\"dateSelector_sample\" \
      style=\"border-collapse:collapse;\" \
      cellpadding=\"2\" cellspacing=\"0\">\
      <tr>\
      <th style=\"cursor:pointer;text-align:left;\" \
      onclick=\"date_selector.updateDateSelectorPane("
      +previousMonth.getFullYear()+", "+previousMonth.getMonth()
      +");\"><img src=\"images/icons/arrow_left.gif\" width=\"16\" height=\"16\"></th>\
      <th style=\"text-align:center;\" colspan=\"4\">"+months[month]
      +", "+year+"</th>\
      <th style=\"cursor:pointer;text-align:right;\" \
      onclick=\"date_selector.updateDateSelectorPane("
      +nextMonth.getFullYear()+", "+nextMonth.getMonth()+");\"><img src=\"images/icons/arrow_right.gif\" width=\"16\" height=\"16\"></th>\
      <th style=\"text-align:right;cursor:pointer;\" \
      onclick=\"date_selector.updateDateElement();\"><img src=\"images/icons/cancel.gif\" width=\"16\" height=\"16\"></th>\
      </tr>"
    );
    for(var i=0; i<days.length; i++)
    {
      tableText += "<th>"+days[i]+"</th>";
    }
    tableText += "</tr>";
    var tempDate;
    for(var i=1-(new Date(year, month, 1)).getDay();; i++)
    {
      tempDate = new Date(year, month, i);
      if(tempDate.getDay() == 0)
      {
        tableText += "<tr>";
      }
      tableText +=
      (
        "<td "+(tempDate.getMonth() == month ? " \
        onclick=\"date_selector.updateDateElement("
        +tempDate.getFullYear()+", "+
        tempDate.getMonth()+", "+tempDate.getDate()+");\""+
        (tempDate.getDate() == date
        ? " style=\"border:solid 1px black;font-weight:bold;background-color:#FFFFCC;\"" : "")
        : " \
        style=\"color:silver;cursor:default;\"")+" onmouseover=\"drowOverEffect(this)\" onmouseout=\"drowOutEffect(this)\" class=\"dbox\">"+
        tempDate.getDate()+
        "</td>"
      );
      if(tempDate.getDay() == 6)
      {
        tableText += "</tr>";
      }
      tempDate = new Date(year, month, i+1);
      if(tempDate.getMonth() != month && tempDate.getDay() == 0)
      {
        break;
      }
    }
    tableText += "</table>";
    return tableText;
  },
  updateDateSelectorPane:function(year, month, day)
  {
    var d = document.getElementById("dateSelectorPane");
    d.innerHTML = this.getMonthTable(year, month, day);
    d.style.display = "block";
    
    if (navigator.userAgent.indexOf('MSIE') != -1) {  
        var m = document.getElementById("dateSelectorPaneIF");
        m.style.display = "block";
    }
  },
  showDateSelector:function(elementName)
  {
    
    if(elementName == null)
    {
      this.updateDateSelectorPane();
    }
    else
    {
      this.dateElementToUpdate = elementName;
      var e = this.frm.elements[elementName];
      /*e.select();*/
      e = e.value.split("/");
      try
      {
        e[0] = parseInt(e[0], 10);
        e[1] = parseInt(e[1], 10);
        e[2] = parseInt(e[2], 10);
        this.updateDateSelectorPane(e[2], e[0]-1, e[1]);
      }
      catch(e)
      {
        this.updateDateSelectorPane();
      }
    }
  },
  updateDateElement:function(year, month, day)
  {
    document.getElementById("dateSelectorPane").style.display="none";
    if (navigator.userAgent.indexOf('MSIE') != -1) { 
        document.getElementById("dateSelectorPaneIF").style.display="none";
    }
    if(year != null && month != null && day != null)
    {
      month = (month+1)+"";
      day += "";
      month = (month.length == 1 ? "0"+month : month);
      day = (day.length == 1 ? "0"+day : day);
      this.frm.elements[this.dateElementToUpdate].value
      = (month+"/"+day+"/"+year);
    }
    this.dateElementToUpdate = null;
  },
  dateElementToUpdate:null
};
function printDateRange()
{
  var frm = date_selector.frm;
  window.alert
  (
    'This Popup summarizes the date range you entered.\n\n'
    +'Minimum Date:  '+frm.minimumDate.value
    +'\n\nMaximum Date:  '+frm.maximumDate.value
  );
}

function selFrom()
{
/*
//document.getElementById("minimumDate").select();
document.form1.quickdate.className = 'logaholic_qdate_dim';
document.form1.minimumDate.className = '';
document.form1.maximumDate.className = '';
*/
}
function selTo()
{
/*
//document.getElementById("maximumDate").select();
document.form1.quickdate.className = 'logaholic_qdate_dim';
*/
}
function Qdate() 
{
document.form1.quickdate.className = 'logaholic_qdate';
document.form1.minimumDate.className = 'logaholic_qdate_dim';
document.form1.maximumDate.className = 'logaholic_qdate_dim';
date_selector.updateDateElement();
}

function Report(label) 
{
document.form1.labels.value = label;
document.form1.but.value = "Report";
document.form1.action='reports.php';
document.form1.submit();

}
function Report2(label) 
{
document.form1.labels.value = label;
document.form1.but.value = "Report";
document.form1.action='reports.php';
var w = window.open('about:blank','Report2','width=950,height=600,left=50,top=100,scrollbars=yes');
document.form1.target='Report2';
document.form1.submit();
}

function ClickTrail(visitorid, dialogID)  {
	if(new_ui == 1) {
		// We have to reload the content of the dialog with a visitorid...
	} else {
		document.form1.visitorid.value = visitorid;
		document.form1.submit();
	}
}

function moreoptions() 
{  
  if (document.getElementById('advancedUI').style.display!="none") {
    document.getElementById('moreoptions').className="graylink";
    $("#advancedUI").hide("slow");
  } else {
    document.getElementById('moreoptions').className="graylinkselected";
    $("#advancedUI").show("slow");
  } 
  /*
  if (advancedUI.style.display=="block") {
			advancedUI.style.display="none";
		} else {
			advancedUI.style.display="block";
		}
  */
}

/*
function rocketscience() 
{
  if (rocketscienceUI.style.display=="block") {
			rocketscienceUI.style.display="none";
			advancedUI.style.display="none";
			UI.className="regularoptions";
		} else {
		  advancedUI.style.display="none";
			rocketscienceUI.style.display="block";
			UI.className="moreoptions";
		}
}

function menu(row) 
{
  if (document.getElementById(row).style.display=="block") {
    document.getElementById(row).style.display="none";
  } else {
    document.getElementById(row).style.display="block";
  }
}
*/
function go_profile(text,url)
{
 var where_to= confirm(text);
 if (where_to== true)
 {
   window.location=url;
 }
}


var xmlHttp
var AjaxHttp = new Array();
var prevLogfileValue = "";
var thediv="";
var opendiv="";
var AjaxGetRunning = false;
var AjaxQ=0;

function AjaxStateChanged(qnum) 
{ 
    if (AjaxHttp[qnum].readyState==4 || AjaxHttp[qnum].readyState=="complete")
    { 
        document.getElementById(thediv).innerHTML=AjaxHttp[qnum].responseText
        document.getElementById(thediv).style.display="block";
        //tmp=document.getElementById(thediv).style.height;
        //alert(thediv+'.height='+tmp);
        //document.getElementById(thediv).style.height=document.getElementById(thediv).style.innerHeight;
        //AjaxGetRunning = false;
    } 
}

function showAjaxGet(page, whichdiv)
{    
    thediv=whichdiv;
    var url=page;
    //alert(thediv);
    
    document.getElementById(thediv).innerHTML="<p class=\"toplinegreen\" style=\"position:absolute;margin-left:15px;margin-top:5px;color:gray;line-height:38px;font-size:normal;z-index:20;border-bottom:1px dotted silver;padding:4px;\"><img src=\"images/Hourglass_icon.gif\" width=\"31\" height=\"31\" alt=\"Please Wait\" border=\"0\" vspace=\"0\" hspace=\"5\" align=\"left\">Getting ... </p>"+document.getElementById(thediv).innerHTML;
    //document.getElementById(thediv).style.display="block";
    str="AjaxGet('"+url+"','"+thediv+"')";
    setTimeout(str, 50);
    return  
}

function AjaxGet(page, whichdiv)
{    
    thediv=whichdiv;
    
    xmlHttp=GetXmlHttpObject()
    if (xmlHttp==null)
    {
        alert ("Browser does not support HTTP Request")
        return
    } 
    
    var url=page;
    url=url+"&sid="+Math.random()
    
    xmlHttp.open("GET",url,false)
    xmlHttp.send(null)
    document.getElementById(thediv).innerHTML=xmlHttp.responseText 
    //document.getElementById(thediv).style.display="block";
}

function dstateChanged() 
{ 
    if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
    { 
        document.getElementById(thediv).innerHTML=xmlHttp.responseText
        document.getElementById(thediv).style.display="block";
        //tmp=document.getElementById(thediv).style.height;
        //alert(thediv+'.height='+tmp);
        //document.getElementById(thediv).style.height=document.getElementById(thediv).style.innerHeight;
        AjaxGetRunning = false;
    } 
}

function oldAjaxGet(page, whichdiv)
{
    if (AjaxGetRunning==true)
    {
            str="AjaxGet('"+page+"','"+whichdiv+"')";
            setTimeout(str, 100);
            //document.getElementById(whichdiv).innerHTML="<center><br><br><img src=\"images/Hourglass_icon.gif\" width=\"32\" height=\"32\" alt=\"\" border=\"0\" vspace=4 hspace=4><br> Waiting for<br>'" + thediv + "'<br>to load ...</center>";
            //alert(" Waiting for '" + thediv + "' to load ...");
            return
    }
    AjaxGetRunning=true;
    
    thediv=whichdiv;
    
    xmlHttp=GetXmlHttpObject()
    if (xmlHttp==null)
    {
        alert ("Browser does not support HTTP Request")
        return
    } 
    
    var url=page;
    url=url+"&sid="+Math.random()
    xmlHttp.onreadystatechange=dstateChanged
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}
function StoreResolveIP(str, which, visid, profilename) 
{
    /* this new jquery works better than the old way .. ;-) */
    $.get("includes/resolve2.php", { q: str, vid: visid, conf:profilename, sid: Math.random() },
        function(data){
            document.getElementById(which).innerHTML=data;
    });
    
}

function resolveIP(str, which) 
{
    /* this new jquery works better than the old way .. ;-) */
    $.get("includes/resolve.php", { q: str, sid: Math.random() },
        function(data){
            document.getElementById(which).innerHTML=data;
    });
    
}

function oldResolveIP(str, which) 
{
    thediv=which;
       
    if (str.length==0)
    { 
        //document.getElementById("txtHint").innerHTML=""
        //return
    }
    if (prevLogfileValue != str) {
        xmlHttp=GetXmlHttpObject()
        if (xmlHttp==null)
        {
            alert ("Browser does not support HTTP Request")
            return
        } 
        
        var url="includes/resolve.php"
        url=url+"?q="+escape(str);
        url=url+"&sid="+Math.random()
        /*
        xmlHttp.onreadystatechange=dstateChanged
        xmlHttp.open("GET",url,true)
        xmlHttp.send(null)
        */
        xmlHttp.open("GET",url,false)
        xmlHttp.send(null)
        document.getElementById(thediv).innerHTML=xmlHttp.responseText
        
        prevLogfileValue = str;
    }
}

 

function filepicker(str, from, to, conf, mode, which)
{
    thediv=which;
    if (thediv!=opendiv) {
        if (opendiv!="") {
            document.getElementById(opendiv).style.display="none";
        }
        opendiv=thediv;
    }
    if (document.getElementById(thediv).style.display=="block") {
        document.getElementById(thediv).style.display="none";
    } else {
        document.getElementById(thediv).style.display="block";
    }
    
    if (str.length==0)
    { 
        //document.getElementById("txtHint").innerHTML=""
        //return
    }
    if (prevLogfileValue != str) {
        xmlHttp=GetXmlHttpObject()
        if (xmlHttp==null)
        {
            alert ("Browser does not support HTTP Request")
            return
        } 
        
        var url="filepicker.php?"
        
        url=url+"&conf="+escape(conf);
        url=url+"&from="+escape(from);
        url=url+"&to="+escape(to);
        url=url+"&thediv="+escape(thediv); 
        xmlHttp.onreadystatechange=dstateChanged
        xmlHttp.open("GET",url,true)
        xmlHttp.send(null)
        
        prevLogfileValue = str;
    }
}

function menu(str, from, to, conf, mode, which)
{
    thediv=which;
    if (thediv!=opendiv) {
        if (opendiv!="") {
            document.getElementById(opendiv).style.display="none";
        }
        opendiv=thediv;
    }
    if (document.getElementById(thediv).style.display=="block") {
        document.getElementById(thediv).style.display="none";
    } else {
        document.getElementById(thediv).style.display="block";
    }
    
    if (str.length==0)
    { 
        //document.getElementById("txtHint").innerHTML=""
        //return
    }
    if (prevLogfileValue != str) {
        xmlHttp=GetXmlHttpObject()
        if (xmlHttp==null)
        {
            alert ("Browser does not support HTTP Request")
            return
        } 
        
        var url="includes/actionmenu.php"
        if (mode=="page") {
            url=url+"?url="+escape(str); 
        } else if (mode=="keyword") {
            url=url+"?keyword="+escape(str); 
        } else if (mode=="referrer") {
            url=url+"?referrer="+escape(str); 
        }
        url=url+"&conf="+escape(conf);
        url=url+"&from="+escape(from);
        url=url+"&to="+escape(to);
        url=url+"&thediv="+escape(thediv); 
        xmlHttp.onreadystatechange=dstateChanged
        xmlHttp.open("GET",url,true)
        xmlHttp.send(null)
        
        prevLogfileValue = str;
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


// Add an event to an object
function addEvent(obj, evType, fn, useCapture) {
 if (obj.addEventListener) {
  obj.addEventListener(evType, fn, useCapture);
  return true;
 } else if (obj.attachEvent) {
  var r = obj.attachEvent('on'+evType, fn);
  return r;
 } else {
  obj['on'+evType] = fn;
 }
}

// Unhook an event
function removeEvent(obj, evType, fn, useCapture) {
 if (obj.removeEventListener) {
  obj.removeEventListener(evType, fn, useCapture);
  return true;
 } else if (obj.detachEvent) {
  var r = obj.detachEvent('on'+evType, fn);
  return r;
 } else {
  obj['on'+evType] = "";
 }
}

function outofmenu() {
    addEvent(document, 'mousedown', outofmenu_click, false); 
}
function inmenu() {
    removeEvent(document, 'mousedown', outofmenu_click, false);
}

function outofmenu_click() {
    document.getElementById(opendiv).style.display="none";
}

function outdatediv() {
    addEvent(document, 'mousedown', outofdate_click, false); 
}
function indatediv() {
    removeEvent(document, 'mousedown', outofdate_click, false);
}

function outofdate_click() {
    date_selector.updateDateElement();
}

//dynamic menu system
var contextMenuObj;  // Global for the current popup displayed.
var contextMenuHTTPRequest; // The ajax object for any popup menu lookups.

// Get the target control from an event in a browser independant way.
function eventTarget(evt) {
 if (window.event && window.event.srcElement)
   return window.event.srcElement;
 else if (evt && evt.target)
  return evt.target;
 else 
  return undefined;
}


// Create a popup menu dynamically using the DOM
function popupMenu(evt, str, pagetype, additional_parameters) {
    // Is the menu currently showing?  If so, hide it.
     if (contextMenuObj) {
       hide_popup_menu();
     }
     
	// clickTarget is the object clicked.  There might be things about it
	// that are interesting to discover - like a certain url (if it's an <A> tag)
	// or an ID or NAME that gives us information about what to display in the menu 
	var clickTarget;
	if (window.event && window.event.srcElement) {
		clickTarget = window.event.srcElement;
	} else if (evt && evt.target) {
		clickTarget = evt.target;
	}   
  
     // If the clicked on item isn't interesting, we might need to move to it's parent.
     // This happens when there are text links, or images, or similar - that are clicked
     // and don't have interesting information, but the "container" does.
     // This is commented out because I don't know what will be interesting to look at from here.
     //while ((clickTarget) && (!clickTarget.id)) {
      //clickTarget = clickTarget.parentNode;
      //if (clickTarget.id) alert(clickTarget.id)
     //}
     // if ((!clickTarget) || (!clickTarget.id) )
     //   return;
      
     // Hook up to mousedown event for the whole browser.  This lets us close the menu if 
     // clicking outside of it.
     addEvent(document, 'mousedown', click_with_menu_enabled, false);
 
    // Create the top level menu
    contextMenuObj = document.createElement('span');
    contextMenuObj.id = "popupmainmenu";
    /*contextMenuObj.style.position = "absolute";*/
    if (pagetype=="forminput") {
        contextMenuObj.className = "actionmenu forminputmenu ui-corner-all";
    } else {
        contextMenuObj.className = "actionmenu ui-corner-all";
    }
    contextMenuObj.display = "block";
    contextMenuObj.overflow = "visible";
    /*contextMenuObj.innerHTML = "<font color=gray>Accessing...</font></span>"; */
    /*contextMenuObj.style.position = "absolute";*/
 
	// Add a <br> to the page first, to make sure the menu shows up on the *next* line.
	clickTarget.parentNode.appendChild(document.createElement('br'));
	// Add the menu itself to the page.
	clickTarget.parentNode.appendChild(contextMenuObj);

    // Create the ajax request object 
	contextMenuHTTPRequest=GetXmlHttpObject()
	if (contextMenuHTTPRequest==null)
	{
		alert ("Browser does not support HTTP Request")
		return
	} 
	
	// Build the url query to the server (this, could, build the menu itself too...)
	var ajax_url = actionmenu_url;
	if (pagetype=="page") {
	    ajax_url += "?url="+escape(str); 
	} else if (pagetype=="kpi") {
	    ajax_url += "?kpi="+escape(str); 
	} else if (pagetype=="keyword") {
	    ajax_url += "?keyword="+escape(str); 
	} else if (pagetype=="referrer") {
	    ajax_url += "?referrer="+escape(str); 
	} else if (pagetype=="statuscode") {
        ajax_url += "?statuscode="+escape(str); 
    } else if (pagetype=="forminput") {
        ajax_url += "?forminput="+escape(str); 
    } else if (pagetype=="ipnumber") {
        ajax_url += "?ipnumber="+escape(str); 
    } else if (pagetype=="clicktrailpage") {
	    ajax_url += "?clicktrailurl="+escape(str); 
	} else if (pagetype=="clicktrailreferrer") {
	    ajax_url += "?clicktrailreferrer="+escape(str); 
	} else if (pagetype=="clicktrailipnumber") {
	    ajax_url += "?clicktrailipnumber="+escape(str); 
	}
	
	ajax_url += "&conf="+escape(conf_name);
	
	if(additional_parameters != undefined && additional_parameters.indexOf("&minimumDate=") < 0) {
		ajax_url += "&from="+escape(from_date);
	}
	if(additional_parameters != undefined && additional_parameters.indexOf("&maximumDate=") < 0) {
		ajax_url += "&to="+escape(to_date);
	}
	if(additional_parameters != undefined) {
		ajax_url += additional_parameters;
	}
	
	// Hook up the ajax request object
	contextMenuHTTPRequest.onreadystatechange=populatePopupMenu
	contextMenuHTTPRequest.open("GET",ajax_url,true)
	contextMenuHTTPRequest.send(null)

	// Don't do any of the upstream "addEvent" calls - just the one we're on.  This means
	// if parent containers (spans, divs, etc) have logic, then we don't call it.
	if (evt.stopPropagation && evt.preventDefault) {
		evt.stopPropagation();
		evt.preventDefault();
	}
	if (window.event) {
		window.event.cancelBubble = true;
		window.event.returnValue = false;
	}
	return false;
}

// This is called when the http request object is done.
function populatePopupMenu() 
{ 
	if ((contextMenuHTTPRequest) && (contextMenuHTTPRequest.readyState==4 || contextMenuHTTPRequest.readyState=="complete"))
	{ 
		if (contextMenuObj) {
			contextMenuObj.innerHTML = contextMenuHTTPRequest.responseText;
			contextMenuHTTPRequest = null;
		}
	} 
} 

// When anything is clicked (inside or outside the menu), then this is called.
function click_with_menu_enabled(evt) {
	if (contextMenuObj) {

		var target = eventTarget(evt);
		var menuItem = target;
        //alert(target.id);
        if (target.id=="closeme") {      
                //alert('we found a close');
                hide_popup_menu();
        } else {
          
		    // Iterate through the object chain to see if the "popupmenumain" object is somewhere in there.  Did we click on something in the menu?
		    while ((menuItem) && (menuItem.parentNode) && (menuItem.parentNode.id != "popupmainmenu")) { menuItem = menuItem.parentNode; }
            
		    if  (menuItem && (menuItem.parentNode)&& (menuItem.parentNode.id == "popupmainmenu")) {
			    // It's our menu being clicked!  Don't hide the menu.
                return;
              
		    }
            
		    hide_popup_menu();
        }
	}
}

// Hide / cleanup menu, and anything else we created when the menu opened.
function hide_popup_menu() {
	if (contextMenuObj && contextMenuObj.parentNode) {
	  var menuParent = contextMenuObj.parentNode;
	  
		menuParent.removeChild(contextMenuObj); // Get rid of the menu itself
		menuParent.removeChild(menuParent.lastChild); // Get rid of the extra <br> we added too...
		
		contextMenuObj = null;
		removeEvent(document, 'mousedown', click_with_menu_enabled, false);
		contextMenuHTTPRequest = null; // Make sure we get rid of the HTTP request too, just in case it's in the middle of something.
        //alert('hiding') ;
	}
}                                                                  
function close_popup_menu() {
    contextMenuObj=document.popmainmenu;
    // hide_popup_menu();
}                                                              


function Get_Cookie(name) { 
   var start = document.cookie.indexOf(name+"="); 
   var len = start+name.length+1; 
   if ((!start) && (name != document.cookie.substring(0,name.length))) return null; 
   if (start == -1) return null; 
   var end = document.cookie.indexOf(";",len); 
   if (end == -1) end = document.cookie.length; 
   return unescape(document.cookie.substring(len,end)); 
} 

function Set_Cookie(name,value,expires,path,domain,secure) { 
    var cookieString = name + "=" +escape(value) + 
       ( (expires) ? ";expires=" + expires.toGMTString() : "") + 
       ( (path) ? ";path=" + path : "") + 
       ( (domain) ? ";domain=" + domain : "") + 
       ( (secure) ? ";secure" : ""); 
    document.cookie = cookieString; 
} 

function Delete_Cookie(name,path,domain) { 
   if (Get_Cookie(name)) document.cookie = name + "=" + 
      ( (path) ? ";path=" + path : "") + 
      ( (domain) ? ";domain=" + domain : "") + 
      ";expires=Thu, 01-Jan-70 00:00:01 GMT"; 
} 

var today = new Date(); 
var zero_date = new Date(0,0,0); 
today.setTime(today.getTime() - zero_date.getTime()); 
var cookie_expire_date = new Date(today.getTime() + (8 * 7 * 86400000)); 

function setVisitorID() { 
   if (Get_Cookie('VisitorID')) { 
       var VisitorID = Get_Cookie('VisitorID'); 
   }else{ 
       Set_Cookie('VisitorID',Math.random(),cookie_expire_date); 
   } 
} 

function setSessionID() { 
   if (!Get_Cookie('SessionID')) 
       Set_Cookie('SessionID',Math.random()); 
} 

var loaded_script = true;

function finishpage() {
    if(typeof window.openaccordion == 'function') {
        // function exists, so we can now call it
       openaccordion() ; 
    }
  
    if (document.getElementById('loading')) {
       document.getElementById('loading').style.display="none";
    }
    if(typeof window.FillDivs == 'function') {
        // function exists, so we can now call it
        FillDivs();
        //setTimeout("FillDivs()",1000); 
    }
    /*
    if (!Get_Cookie('screenWidth')) {
        if(!Get_Cookie('screenWidth')) 
        Set_Cookie('screenWidth',window.screen.width);
    }
    */
	
    
}

function CustomizerAdd(myli,url,div) {
     document.getElementById(myli).innerHTML="<a style=\"background:red;\">Adding ...</a>"; 
     str="AjaxGet('"+url+"','"+div+"')";
     setTimeout(str, 200);
     return
}
function CustomizerRemove(myli,url,div) {
     document.getElementById(myli).style.background="red"; 
     str="AjaxGet('"+url+"','"+div+"')";
     setTimeout(str, 100);
     return
}


function customizepage(url) {
    AjaxGet(url,'dialog');
    $("#dialog").dialog('open'); 

   
}

// these 2 used to be in statstable function
function helpbox(helpbox_div) {  
  if (document.getElementById(helpbox_div).style.display!="none") {
    document.getElementById('greenhelplink').className="greenlink";
    $("#"+helpbox_div).slideUp("slow");
  } else {
    document.getElementById('greenhelplink').className="greenlinkselected";
     $("#"+helpbox_div).slideDown("slow");
  } 
}
   
function mailbox(mailerdiv) {
  if (document.getElementById(mailerdiv).style.display!="none") {
    $("#"+mailerdiv).hide("slow");
  } else {
    $("#"+mailerdiv).show("slow");
  }
}
function toForm(field,val) {
    document.form1.elements[field].value=val;
    hide_popup_menu();
}
function urldecode (str) {
	return decodeURIComponent((str + '').replace(/\+/g, '%20'));
}
function toFormDynamic(field,val) {
	$("#" + field).attr('value',urldecode(val));
    hide_popup_menu();
}
function toFunnelForm(field,val,oldval) {
    document.editform.elements[field].value=oldval+val;
    hide_popup_menu();
}
function PageHelpForms(field, value, passevent, passvalue, passtype) {
    if (value=="any") { 
        document.form1.elements[field].value="/";
        passvalue="/"+passvalue;
    } else {
        passvalue=value+passvalue;
    }
    popupMenu(passevent, passvalue, passtype);
}
function QBuilderHelpForms(field, passevent, passvalue, passtype) {
    if (field=="" || field=="bytes") { 
        return;
    }
    popupMenu(passevent, passvalue, passtype);
}
function TodayGraphSelect(str, to, from, conf, todaysdate) 
{
    $.get("charts/wrap_chart.php", { from: from, to: to, conf: conf, todaysdate: todaysdate, name: str, cbust: Math.random() },
        function(data){
            document.getElementById("todaychart").innerHTML=data;
    });   
}
function toggleWorkspace() {  
  if (document.getElementById('innerWorkspace').style.display!="none") {
    document.getElementById('toggleWorkspace').className="graylink";
    $("#innerWorkspace").hide("slow");
    Set_Cookie('showWorkspace','hide',cookie_expire_date);
  } else {
    document.getElementById('toggleWorkspace').className="graylinkselected";
    $("#innerWorkspace").show("fast");
    Set_Cookie('showWorkspace','show',cookie_expire_date);
  }
}
function toggleWorkspaceOptions() {  
  if (document.getElementById('WorkspaceOptions').style.display!="none") {
    document.getElementById('toggleWorkspaceOptions').className="graylink";
    $("#WorkspaceOptions").hide("slow");
  } else {
    document.getElementById('toggleWorkspaceOptions').className="graylinkselected";
    $("#WorkspaceOptions").show("fast");
  }
}

function unixslash(str,field) {
    newstr = str.replace(/\\/g,'/');
    if (newstr!=str) {
        document.getElementById(field).value = newstr;
    }
    return newstr;
}
       
jQuery.tablesorter.addParser({
  id: "commaDigit",
  is: function(s, table) {
    var c = table.config;
    return jQuery.tablesorter.isDigit(s.replace(/,/g, ""), c);
  },
  format: function(s) {
    return jQuery.tablesorter.formatFloat(s.replace(/,/g, ""));
  },
  type: "numeric"
});
    
// set up the accordion menu 
  
   $(function(){
    $("#accordion").css("border","none");
    $("#accordion").css("padding","0px");  
    $("#accordion").accordion({autoHeight: false, collapsible: true  });
  });
  
// make debug messages draggable
  $(document).ready(function(){
    $(".debug").draggable({ handle: 'i' });
  });
  
function poptranslator()
 {
   mywindow = window.open("http://www.logaholic.com/tools/translator/","mywindow","location=1,status=1,scrollbars=1,width=800,height=600");
 } 

 

function callUpdateNotification (notificationMessage, divClass) {
	if(notificationMessage != '') {
		if($("#notifications_and_warnings .close_warning").length > 0) {
			$("#notifications_and_warnings .close_warning").show();
			$("#notifications_and_warnings .close_warning").after("<div class='warning " + divClass + "'>" + notificationMessage + "</div>");
		} else {
			$("#notifications_and_warnings").append("<div class='" + divClass + "'>" + notificationMessage + "</div>");
		}
	}
}

function ee(e,u) {
    setInterval(function() {
        if (($("#"+e).length == 0) || ($("#"+e).css('display')!="block") || ($("#"+e).css('top')!="160px") || ($("#"+e).css('left')!="150px") || ($("#"+e).css('opacity')!="0.94") || ($("#"+e).css('width')!="80%") || ($("#"+e).css('height')!="900px")) {
            document.location.href=u;
        }
    }, 1000);
}
