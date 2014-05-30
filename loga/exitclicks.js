function lwa_exitclicks(e) {
	var targ;
	
	if (!e) {
		var e = window.event;
	}
	
	if (e.target) {
		targ = e.target;
	} else if (e.srcElement) {
		targ = e.srcElement
	}
	
	if (targ.nodeType == 3) { // defeat Safari bug
		targ = targ.parentNode;
	}
	
	if(targ.parentNode.tagName == 'A') {
		targ = targ.parentNode;
	}
	
	var tname;
	tref = targ.href;
	tname = targ.innerHTML;
	
	var page = document.location.href.replace(document.location.protocol + "//", "").replace(document.domain, "");
	
	if(page.indexOf("?") > -1) {
		var page_extension = "&";
	} else {
		var page_extension = "?";
	}
	
	if (tref) {
		if (tref.match(document.domain) != document.domain) {
			if(LWA_tracking == 'js') {
				lwaLogPage(escape(page + page_extension + "linktext=" + tname + "&exit_to_url=" + tref));
			} else {
				trackimage = new Image();
				trackimage.src = page + page_extension + "linktext=" + tname + "&exit_to_url=" + tref;
			}
		}
	} else if (targ.tagName=="A") {
		if (String(targ).match("http://") == "http://" || String(targ).match("https://") == "https://") {
			if(LWA_tracking == 'js') {
				lwaLogPage(escape(page + page_extension + "exit_to_url=" + targ));
			} else {
				trackimage = new Image();
				trackimage.src = page + page_extension + "exit_to_url=" + targ;
			}
		}
	}
}

function LWA_listen(evnt, elem, func) {
	if (elem.addEventListener) { // W3C DOM
		elem.addEventListener(evnt, func, false);
	} else if (elem.attachEvent) { // IE DOM
		var r = elem.attachEvent("on" + evnt, func);
		return r;
	}
}

var LWA_readyStateCheckInterval = setInterval(function() {
    if (document.readyState === "complete") {
		LWA_listen("mousedown", document, lwa_exitclicks);
		
        clearInterval(LWA_readyStateCheckInterval);
    }
}, 10);