/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
var lgtoday = new Date(); 
var lgzero_date = new Date(0,0,0); 
lgtoday.setTime(lgtoday.getTime() - lgzero_date.getTime()); 
var cookie_expire_date = new Date(lgtoday.getTime() + (8 * 7 * 86400000));
if (typeof lwa_id == 'undefined') {
	var lwa_id = "";
} else {
	var lwa_profile = lwa_id;
}
if (typeof lwa_id == 'undefined') {
	var lwa_trackermode = 1;
}
function lgGet_Cookie(name) { 
   var start = document.cookie.indexOf(name+"="); 
   var len = start+name.length+1; 
   if ((!start) && (name != document.cookie.substring(0,name.length))) return null; 
   if (start == -1) return null; 
   var end = document.cookie.indexOf(";",len); 
   if (end == -1) end = document.cookie.length; 
   return unescape(document.cookie.substring(len,end)); 
}
function lgSet_Cookie(name,value,expires,path,domain,secure) { 
    var cookieString = name + "=" +escape(value) + ";path=/" +
       ( (expires) ? ";expires=" + expires.toGMTString() : "") + 
       ( (domain) ? ";domain=" + domain : "") + 
       ( (secure) ? ";secure" : ""); 
    document.cookie = cookieString; 
}
function lggetVisitorID() { 
   if (!lgGet_Cookie('NewLogaholic_VID')) {
       var lgvid = Math.floor(Math.random() * (navigator.userAgent.length * 1000000000));
       lgSet_Cookie('NewLogaholic_VID',lgvid,cookie_expire_date);        
   }
   return lgGet_Cookie('NewLogaholic_VID'); 
}
function lggetSessionID() { 
   if (!lgGet_Cookie('NewLogaholic_SESSION')) {
       var lgses = Math.floor(Math.random() * (navigator.userAgent.length * 1000000000));
       lgSet_Cookie('NewLogaholic_SESSION',lgses);
       return lgGet_Cookie('NewLogaholic_SESSION') + "&newses=1";        
   } else {
       return lgGet_Cookie('NewLogaholic_SESSION');
   } 
}
function trackPage() {
    var logaholic = "";
    logaholic = "referrer=" + escape(window.document.referrer) + "&visitorid=" + lggetVisitorID() + "&sessionid=" + lggetSessionID() + "&trackermode=" + lwa_trackermode;
    if(window.screen) {  logaholic +="&w=" + window.screen.width + "&h=" + window.screen.height + "&cd=" + window.screen.colorDepth;  }
    if(document.title) {  logaholic +="&docTitle=" + escape(document.title);  }
    var logatr = new Image();
    logatr.src = lwa_server + 'includes/trackPage.php?conf=' + lwa_profile + '&lwa_id=' + lwa_id + '&' + logaholic;
}
function lwaLogPage(page) {
	var logaholic = "";
    logaholic = "referrer=" + escape(window.document.location) + "&visitorid=" + lggetVisitorID() + "&sessionid=" + lggetSessionID() + "&page=" + page;
    var logatrEvent = new Image();
    logatrEvent.src = lwa_server + 'includes/trackPage.php?conf=' + lwa_profile + '&lwa_id=' + lwa_id + '&' + logaholic;	
}
