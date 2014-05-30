<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
//PHPCIPHER NO ENCRYPT
// PHPLOCKITOPT NOENCODE

function DetectBot($useragent) {
	global $ipnumber;
	//check for bots, crawlers, spiders and any other non-human useragent
	//returns 1 if it's a bot, 0 if not
	
	$useragent=strtolower($useragent);
	
	if (strpos($useragent, "bot")!==FALSE) {
		$crawl=1;
	} else if (strpos($useragent, "mediapartners-google")!==FALSE) {
		$crawl=1;
	} else if (strpos($useragent, "slurp")!==FALSE) {
		$crawl=1;
	} else if (strpos($useragent, "crawl")!==FALSE) {
		$crawl=1;
	} else if (strpos($useragent, "spider")!==FALSE) {
		$crawl=1;
	} else if (strpos($useragent, "ia_archiver")!==FALSE) {
		$crawl=1;
	} else if (strpos($useragent, "mnogo")!==FALSE) {
		$crawl=1;
	} else if (strpos($useragent, "libwww")!==FALSE) {
		$crawl=1;
	} else if (strpos($useragent, "askjeeves")!==FALSE) {
		$crawl=1;
	} else if (strpos($useragent, "teoma")!==FALSE) {
		$crawl=1;
	} else if (strpos($useragent, "findlinks")!==FALSE) {
		$crawl=1;
	} else if (strpos($useragent, "snoopy")!==FALSE) {
		$crawl=1;
	} else if (strpos($useragent, "lwp")!==FALSE) {
		$crawl=1;
	} else if (strpos($ipnumber, "65.55.")!==FALSE) {   // this this the ip range of the new livebot
		$useragent="(livebot-xx.search.live.com) ".$useragent;
		$ipnumber="65.55.165.12";
		$crawl=1;
	} else if (strpos($useragent, "nutch")!==FALSE) {
		$crawl=1;
	} else if (strpos($useragent, "yandex")!==FALSE) {   
		$crawl=1;
	} else if (strpos($useragent, "internetseer.com")!==FALSE) {   
		$crawl=1;
	} else if (strpos($useragent, "allrati")!==FALSE) {   
		$crawl=1;
	} else if (strpos($useragent, "java/")!==FALSE) {   
		$crawl=1;
	} else if (strpos($useragent, "poller")!==FALSE) {   
		$crawl=1;
	} else if (strpos($useragent, "checker")!==FALSE) {   
		$crawl=1;
	} else if (strpos($useragent, "scoutjet")!==FALSE) {   
		$crawl=1;
	} else {
		$crawl=0;
	}
	return $crawl;
}

function DetectBrowserOS($useragent) {
	//check for Broser name, version and OS
	$bname="";
	$useragent = str_replace("+"," ",$useragent);
	
	if (strpos($useragent, "MSIE")!==FALSE || strpos($useragent, "Internet Explorer")!==FALSE) {
		if (strpos($useragent, "MSIE 9.0")!==FALSE || strpos($useragent, "Internet Explorer 9.")!==FALSE) { 
			$bname="Internet Explorer 9.0 ";
		}else if (strpos($useragent, "MSIE 8.0")!==FALSE || strpos($useragent, "Internet Explorer 8.")!==FALSE) { 
			$bname="Internet Explorer 8.0 ";
		} else if (strpos($useragent, "MSIE 7.0")!==FALSE || strpos($useragent, "Internet Explorer 7.")!==FALSE) { 
			$bname="Internet Explorer 7.0 ";
		} else if (strpos($useragent, "MSIE 6.")!==FALSE || strpos($useragent, "Internet Explorer 6.")!==FALSE) { 
			$bname="Internet Explorer 6.0 ";
		} else if (strpos($useragent, "MSIE 5.0")!==FALSE || strpos($useragent, "Internet Explorer 5.")!==FALSE) { 
			$bname="Internet Explorer 5.0 ";
		}  else if (strpos($useragent, "MSIE 4.0")!==FALSE || strpos($useragent, "Internet Explorer 4.")!==FALSE) { 
			$bname="Internet Explorer 4.0 ";
		} else { 
			$bname="Internet Explorer (unknown version) ";
			//echoNotice("$bname detected based on: $useragent");
		}
	} else {
		if (strpos($useragent, "Chrome")!==FALSE) { 
			$bname="Chrome";
		} else if ((strpos($useragent, "Firefox/9.")!==FALSE) || (strpos($useragent, "Firefox 9.")!==FALSE)) {
			$bname="Firefox 9.x ";
		} else if ((strpos($useragent, "Firefox/8.")!==FALSE) || (strpos($useragent, "Firefox 8.")!==FALSE)) {
			$bname="Firefox 8.x ";
		} else if ((strpos($useragent, "Firefox/7.")!==FALSE) || (strpos($useragent, "Firefox 7.")!==FALSE)) {
			$bname="Firefox 7.x ";
		} else if ((strpos($useragent, "Firefox/6.")!==FALSE) || (strpos($useragent, "Firefox 6.")!==FALSE)) {
			$bname="Firefox 6.x ";
		} else if ((strpos($useragent, "Firefox/5.")!==FALSE) || (strpos($useragent, "Firefox 5.")!==FALSE)) {
			$bname="Firefox 5.x ";
		} else if ((strpos($useragent, "Firefox/4.")!==FALSE) || (strpos($useragent, "Firefox 4.")!==FALSE)) {
			$bname="Firefox 4.x ";
		} else if ((strpos($useragent, "Firefox/3.")!==FALSE) || (strpos($useragent, "Firefox 3.")!==FALSE)) {
			$bname="Firefox 3.x ";
		} else if ((strpos($useragent, "Firefox/2.")!==FALSE) || (strpos($useragent, "Firefox 2.")!==FALSE)) {
			$bname="Firefox 2.x ";
		} else if ((strpos($useragent, "Firefox/1.5")!==FALSE) || (strpos($useragent, "Firefox 1.5")!==FALSE)) {
			$bname="Firefox 1.5.x ";
		} else if ((strpos($useragent, "Firefox/1.")!==FALSE) || (strpos($useragent, "Firefox 1.")!==FALSE)) {
			$bname="Firefox 1.0.x ";
		} else if ((strpos($useragent, "Firefox/0.")!==FALSE) || (strpos($useragent, "Firefox 0.")!==FALSE)) {
			$bname="Firefox 0.x ";
		} else if (strpos($useragent, "Firefox")!==FALSE) { 
			$bname="Firefox (unknown version) ";
		} else if (strpos($useragent, "Opera")!==FALSE) { 
			$bname="Opera";
		} else if (strpos($useragent, "Konqueror")!==FALSE) { 
			$bname="Konqueror";
		} else if (strpos($useragent, "Lynx")!==FALSE) { 
			$bname="Lynx (text based browser) ";
		} else if (strpos($useragent, "Safari")!==FALSE) { 
			$bname="Safari";
		}
	}
	
	// If we don't recognize the browser, then let's just keep the user agent without
	// any parsing.  Otherwise, we'll look for an OS.
	if ($bname) {
		//check operating systems
		if (strpos($useragent, "Windows NT 6.1") !== FALSE || strpos($useragent, "Windows 7") !== FALSE) {
			$useragent="$bname on Windows 7 (NT 6.1)";
		} else if (strpos($useragent, "Windows NT 6.0") !== FALSE || strpos($useragent, "Windows Vista") !== FALSE) { 
			$useragent="$bname on Windows Vista (NT 6.0)";
		} else if (strpos($useragent, "Windows NT 5.1") !== FALSE || strpos($useragent, "Windows XP")!==FALSE) { 
			$useragent="$bname on Windows XP (NT 5.1)";
		} else if (strpos($useragent, "Windows NT 5.0")!==FALSE || strpos($useragent, "Windows 2000")!==FALSE) {
		  $useragent="$bname on Windows 2000 (NT 5.0)";
		} else if (strpos($useragent, "Windows 98")!==FALSE) {
		  $useragent="$bname on Windows 98";
		} else if (strpos($useragent, "Windows NT 5.2")!==FALSE || strpos($useragent, "Windows 2003 Server")!==FALSE) {
			$useragent="$bname on Windows 2003 Server (NT 5.2)";
		} else if (strpos($useragent, "Windows NT")!==FALSE) {
			$useragent="$bname on Windows NT";
		} else if (strpos($useragent, "Windows 95")!==FALSE) {
			$useragent="$bname on Windows 95";
		} else if (strpos($useragent, "Windows")!==FALSE) {
			$useragent="$bname on Windows (unknown version)";
		} else if (strpos($useragent, "Mac OS X")!==FALSE || strpos($useragent, "MacOSX")!==FALSE) {
			$useragent="$bname on Apple MacOSX";
		} else if (strpos($useragent, "Macintosh;")!==FALSE) {
			$useragent="$bname on Apple Macintosh system";
		} else if (strpos($useragent, "Linux")!==FALSE) {
			$useragent="$bname on Linux";
		} else if (strpos($useragent, "FreeBSD")!==FALSE) {
			$useragent="$bname on FreeBSD";
		} else {
			//echoDebug("Unknown OS detected in $useragent");
			$useragent="$bname on Unknown OS";
		}
	} else {
		//echoWarning("no browser detected in $useragent");
	}
	return $useragent;
}

function isMobile($useragent) {
	// go through a series of checks to see if this can be a Mobile User Agent
	// returns true/false
		
	$mobile_browser = '0';
	
	if (strpos(strtolower($useragent),'windows ce')>0) {
		$mobile_browser++;
	}
	else if (strpos(strtolower($useragent),'windows')>0) {
		$mobile_browser=0;
	} else if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($useragent))) {
		$mobile_browser++;
	} else {
		$mobile_ua = strtolower(substr($useragent,0,4));
		$mobile_agents = array(
			'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
			'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
			'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
			'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
			'newt','noki','palm','pana','pant','phil','play','port','prox',
			'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
			'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
			'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
			'wapr','webc','winw','winw','xda','xda-');
		 
		if(in_array($mobile_ua,$mobile_agents)) {
			$mobile_browser++;
		} else if ((strpos(strtolower($useragent),'operamini')>0)||(strpos(strtolower($useragent),'opera mini')>0)) {
			$mobile_browser++;
		} else if (strpos(strtolower($useragent),' ppc;')>0) {
			$mobile_browser++;
		}
	}
	if (strpos(strtolower($useragent),'iemobile')>0) {
		$mobile_browser++;
	}
	if($mobile_browser>0) {
		return true;
	} else {
		return false;
	}
}

?>