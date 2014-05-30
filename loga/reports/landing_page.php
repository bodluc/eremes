<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays a overview of your top keywords
*/
$reports["_LANDING_PAGE"] = Array(
	"ClassName" => "LandingPage", 
	"Category" => "_POPULAR_CONTENT", 
	"icon" => "images/icons/32x32/topkeywords.png",
	"Options" => "daterangeField,displaymode,trafficsource,limit",
	"Filename" => "landing_page",
	"Distribution" => "Standard",
	"Order" => 5,
	"ReportVersion" => 1.0,
	"MinimumVersion" => 3.0,
	"EmailAlerts" => true
);

class LandingPage extends Report {

	function Settings() {
		$this->DefaultDisplay = "table";
		$this->DisplayModes = "table,pie";
		$this->help = "";
		$this->actionmenu_type = 'page';
		
		if(empty($this->sourcetype)) {
			$this->sourcetype = "keywords";
		}
		
		if(empty($this->view)) {
			$this->view ="";
		}
		if($this->view == "All") {
			$this->showfields = _LANDING_PAGE.','._PARAMETERS.','._HITS.','._VISITORS;
		} else {
			$this->showfields = _LANDING_PAGE.','._HITS.','._VISITORS;
		}
		
		$this->addlabel = "{$this->sourcetype}: ".@$this->source;
	}

	function DefineQuery() {
		global $db,$nc;
		
		$refparams = "";
		
		if( $this->sourcetype == "keywords") {
			$table = $this->profile->tablename_keywords;
			$field = "keywords";
		} else if ( $this->sourcetype == 'referrer'){
			$table = $this->profile->tablename_referrers;
			$field = "referrer";
			
			if(strpos($this->source,'?') !== FALSE) {
				$this->source = explode("?",$this->source);
				$refparams = "?".$this->source[1];
				$this->source = $this->source[0];
				$table = "{$this->profile->tablename_refparams} as rp,".$this->profile->tablename_referrers;
				
				$field = "refparams = rp.id  AND a.referrer";
			}
		}
		
		if($this->view == "All") {
			$select = "u.url,up.params";
			$from_table =", {$this->profile->tablename_urlparams} as up";
			$join = "and a.params=up.id ";
		} else {
			$select = "u.url";
			$from_table ="";
			$join = "";
		}
		
		$query  = "select {$nc}
			{$select},
			count(*) as hits,
			count(DISTINCT visitorid) as visitors
			
			from {$this->profile->tablename} as a,
			$table as k,
			{$this->profile->tablename_urls} as u
			$from_table
			
			where timestamp >= {$this->from} and timestamp <= {$this->to}
			and k.{$this->sourcetype}='{$this->source}'
			and a.$field=k.id 
			and a.url=u.id 
			{$join}
			and crawl=0 
			
			group by {$select} ORDER BY hits DESC LIMIT {$this->limit}";
		
		return $query;
	}
    function DisplayCustomForm() {
		echo "View:";
		echo "<select class='report_option_field' name='view'>
			<option value='page' "; if (@$this->view == "page") { echo "selected=\"selected\""; } echo ">"._LANDING_PAGE."</option>
			<option value='All' "; if (@$this->view == "All") { echo "selected=\"selected\""; } echo ">"._LANDING_PAGE." + "._PARAMETERS."</option>
		</select>";
		
		echo "<label for='sourcetype'>"._SEARCH."</label>";
		echo "<select class='report_option_field' id='sourcetype'>";
			echo "<option value=\"keywords\" "; if (@$this->sourcetype == "keyword") { echo "selected=\"selected\""; } echo ">"._KEYWORD.": </option>";
			echo "<option value=\"referrer\" ";     if (@$this->sourcetype == "referrer") { echo "selected=\"selected\""; } echo ">"._REFERRER.": </option>";
		echo "</select>";
		
		echo "<div>";
			echo "<input class='report_option_field' type=\"text\" name=\"source\" id=\"source\" value=\"".@urldecode($this->source)."\" onkeyup=\"popupActionMenu(event, this.value+'@'+this.id+'@'+$('#sourcetype').val(), 'forminput');\" onclick=\"popupMenu(event, this.value+'@'+this.id+'@'+$('#sourcetype').val(), 'forminput');\" autocomplete=\"off\">";
		echo "</div>";
	}
}
?>
