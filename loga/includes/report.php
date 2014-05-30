<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This is the master report class. All other reports should be childen of this parent class
* it is assumed that this is included somewhere where common.inc.php is also included 
*/
class Report {
    var $from;               		// This contains the from timestamp
    var $to;                 		// This contains the to timestamp
    var $labels;             		// This contains the report name
    var $showfields;         		// This is a comma delimited list defining the fields/columns in a report
	var $paginationPanel;	  		// This is a toggle to show the pagination panel under a report or not.
	var $sortTable;			   		// If this is set to false, no javascript datatable plugin will be activated
    var $help;                 		// This contains the help text for a report
    var $bchart;               		// An array containing the columns from showfields that should be displayed as a horizontal 'bar chart' in the table
	var $displayHeader;        		// controls if we want to display a report header (default true)
	var $displayReportLabel;   		// controls if we want to print the report name in the header (default false)
	var $addlabel;					// this can be used to add custom text to the report options list in the header
	var $customHeaderContent;  		// replaces the list of report options in the header with a custom string (default empty)
	var $displayReportButtons; 		// controls if we want to print report buttons in the header for help, print, export, email etc. (default true)
    var $displayTotalRow;      		// controls if we want to display a row with totals at the bottom of a table (default true)
	var $displayMaxChars;      		// controls how many characters of urls, keywords, etc. are displayed in report tables, legends etc. (default 100)
	var $graphcolors;          		// contains the colors of graphs and pies.
	var $template;			   		// Globaling the template
	var $disableShowfieldsHandling; // Disables the special code for certain showfields (default empty array();)	
	var $allowDateFormat;			// Allows the use of dateFormat from the profile. ( default true ) 
	var $displaymode;				// defines if you want to see the data as a table, pie, chart etc
	var $DefaultDisplay;
	var $period;
	var $roadto;
	var $sparktype;
	
    function __construct() {
        global $from, $to, $profile, $labels, $template, $db, $reports, $get_constant, $get_label;
		
        $this->from = $from;
        $this->to = $to;
        $this->profile = $profile;
        $this->bchart = array();
		$this->addlabel = "";
		$this->displayHeader = true;
		$this->displayReportLabel = false;
		$this->displayReportButtons = true;
		$this->displayTotalRow = true;
		$this->displayMaxChars = 100;
		$this->template = $template;
		$this->disableShowfieldsHandling = array();		
		$this->allowDateFormat = true;
		$this->DefaultDisplay = "table";
		$this->period = 'auto';
		$this->sparktype = "";
		
		if($this->profile->animate == 1) {
			$this->animate_graph = false;
		} else {
			$this->animate_graph = true;
		}
		
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		if(strpos($useragent, "MSIE") > 0) {
			$ie_agent = substr($useragent, strpos($useragent, "MSIE"), 10);
			$ie_agent = explode(" ", $ie_agent);
			$ie_version = explode(".", $ie_agent[1]);
			$ie_version = $ie_version[0];
		}
		
		if(!empty($ie_version) && $ie_version <= 8) {
			$this->graphcolors = array("rgb(0,120,174)","rgb(255,0,0)","rgb(0,111,0)",
			"rgb(0,221,34)","rgb(200,66,57)","rgb(249,113,247)",
			"rgb(60,31,110)","rgb(46,136,10)","rgb(221,24,64)",
			"rgb(252,218,0)","rgb(25,65,165)","rgb(175,216,248)",
			"rgb(246,189,15)","rgb(139,186,0)","rgb(166,110,221)",
			"rgb(249,132,161)","rgb(204,204,0)","rgb(153,153,153)",
			"rgb(0,153,225)","rgb(255,102,204)","rgb(102,153,102)",
			"rgb(124,124,180)","rgb(255,153,51)","rgb(152,0,255)",
			"rgb(153,0,204)","rgb(204,204,0)","rgb(102,153,0)",
			"#FF0000","#00F000","#0000F0","#FF00FF","#00FFFF",
			"#FFFF00","#1970B4","#19B470","#7019B4","#70B419",
			"#B41970","#B47019","#FB9619","#FB1996","#96FB19",
			"#9619FB","#1996FB","#19FB96","#CC0000","#00C000",
			"#0000C0","#CC00CC","#00CCCC","#CCCC00","#414D91",
			"#41914D","#4D4191","#4D9141","#91414D","#914D41",
			"#E78741","#E74187","#87E741","#8741E7","#4187E7",
			"#41E787","#990000","#009000","#000090","#990099",
			"#009999","#999900","#692A6E","#696E2A","#2A696E",
			"#2A6E69","#6E692A","#6E2A69","#D37869","#D36978",
			"#78D369","#7869D3","#6978D3","#69D378","#660000",
			"#006000","#000060","#660066","#006666","#666600",
			"#91074B","#914B07","#07914B","#074B91","#4B9107",
			"#4B0791","#BF6991","#BF9169","#69BF91","#6991BF",
			"#9169BF","#91BF69");
		} else {
			$this->graphcolors = array("rgba(0,120,174,1)","rgba(255,0,0,1)","rgba(0,111,0,1)",
			"rgba(0,221,34,1)","rgba(200,66,57,1)","rgba(249,113,247,1)",
			"rgba(60,31,110,1)","rgba(46,136,10,1)","rgba(221,24,64,1)",
			"rgba(252,218,0,1)","rgba(25,65,165,1)","rgba(175,216,248,1)",
			"rgba(246,189,15,1)","rgba(139,186,0,1)","rgba(166,110,221,1)",
			"rgba(249,132,161,1)","rgba(204,204,0,1)","rgba(153,153,153,1)",
			"rgba(0,153,225,1)","rgba(255,102,204,1)","rgba(102,153,102,1)",
			"rgba(124,124,180,1)","rgba(255,153,51,1)","rgba(152,0,255,1)",
			"rgba(153,0,204,1)","rgba(204,204,0,1)","rgba(102,153,0,1)",
			"#FF0000","#00F000","#0000F0","#FF00FF","#00FFFF",
			"#FFFF00","#1970B4","#19B470","#7019B4","#70B419",
			"#B41970","#B47019","#FB9619","#FB1996","#96FB19",
			"#9619FB","#1996FB","#19FB96","#CC0000","#00C000",
			"#0000C0","#CC00CC","#00CCCC","#CCCC00","#414D91",
			"#41914D","#4D4191","#4D9141","#91414D","#914D41",
			"#E78741","#E74187","#87E741","#8741E7","#4187E7",
			"#41E787","#990000","#009000","#000090","#990099",
			"#009999","#999900","#692A6E","#696E2A","#2A696E",
			"#2A6E69","#6E692A","#6E2A69","#D37869","#D36978",
			"#78D369","#7869D3","#6978D3","#69D378","#660000",
			"#006000","#000060","#660066","#006666","#666600",
			"#91074B","#914B07","#07914B","#074B91","#4B9107",
			"#4B0791","#BF6991","#BF9169","#69BF91","#6991BF",
			"#9169BF","#91BF69");
		}
		
		foreach($_REQUEST as $key => $value) {
			if(isset($value)) {
				$this->$key = addslashes($value);
				$this->options[$key] = addslashes($value);
			}
		}
		
		if(empty($this->source)) {
			$this->source = "";
		}
		
		if($this->period == 'auto') {
			$this->period = $this->getPeriod($this->from, $this->to);
		}	
		
		if(!isset($this->trafficsource)) {
			$this->trafficsource = null;
			$this->applytrafficsource = false;
		} else {
			$this->applytrafficsource = true;
		}
		
		if(empty($this->limit) || is_numeric($this->limit) === false) {
			$this->limit = 10;
		}
		
		if($this->limit <= 20) {
			$this->paginationPanel = false;
		} else {
			$this->paginationPanel = true;
		}
		
		if(empty($this->current_plotting_graph)) {
			$this->current_plotting_graph = 1;
		}
		
		$this->sortTable=true;
		
        if(defined($labels)) {
            $labels = constant($labels);
        }
        $this->label = $labels;
		
		if(!empty($this->label) && empty($this->roadto)) {
			if (defined($this->label)) {
				$opts = $reports[$this->label]['Options'];
			} else {
				$opts = $reports[$get_constant[$this->label]]['Options'];
			}
			if(strpos($opts, 'roadto') !== false) {
                $targetfiles = explode(",", str_replace(" ", "", $this->profile->targetfiles));
				if(!empty($targetfiles[0])) {
					$this->roadto = $targetfiles[0];
				} else {
					$this->roadto = '';
				}
            }
        }
		
		if(method_exists($this, "Settings") == true) {
			$this->Settings();
		}
		
		if(empty($this->displaymode)) {
			$this->displaymode = $this->DefaultDisplay;
		}
		
		if(empty($this->label)) {
			$this->label = constant($get_label[get_class($this)]);
		}
    }
    # do this for PHP 4 compatibility
    function Report() { __construct(); }
    
    # this function displays any custom form fields needed to run this report
    function DisplayCustomForm() {
        return false;
    }
    
    # this function returns either a database query or false
    function DefineQuery() {
        return false;
    }
    
    # this function creates a data array that we will use to display results
    function CreateData() {
        global $db;
        $q = $this->DefineQuery();
        if ($q!==false) {
            if ($this->applytrafficsource == true) { $q = subsetDataToSourceID($q,$this->trafficsource);  }
            $db->SetFetchMode(ADODB_FETCH_NUM);
            $result = $db->Execute($q);
            $data = $result->GetArray();
            $db->SetFetchMode(ADODB_FETCH_BOTH);
            return $data;
        } else {
            # if the report is not based on a single database query, this is where the data will be created
            return false;
        }
    }
    
    # this function will print the report, by default it just prints a table, but this can be overruled in child classes
    function DisplayReport() {
        $data = $this->CreateData();
		
		if(isset($this->DefaultDisplay) && !isset($this->displaymode)) { $this->displaymode = $this->DefaultDisplay; }
		
		if(isset($this->displaymode) && !empty($data)) {
			if($this->displaymode == "pie") {
				$this->displayReportButtons = false;
				
				$fields = explode(",",$this->showfields);
				$fields = $fields[0].",".$fields[1];
				$this->showfields = $fields;
				
				$this->reportHeader();
				
				$this->PieChart($data);
			} elseif($this->displaymode == "linechart") {
				$this->displayReportButtons = false;
		
				$this->reportHeader();
				
				$this->Graph($data);
			} elseif($this->displaymode == "barchart") {
				$this->displayReportButtons = false;
		
				$this->reportHeader();
				
				$this->Graph($data, 'bar');
			} elseif($this->displaymode == "areachart") {
				$this->displayReportButtons = false;
		
				$this->reportHeader();
				
				$this->Graph($data, 'area');
			} elseif($this->displaymode == "bubble") {
				$this->displayReportButtons = false;
		
				$this->reportHeader();
				
				$this->BubbleChart($data);
			} else {
				$this->Table($data);
			}
		} else {
			$this->Table($data);
		}
    }
    
    # this report displays a table containing data
    public function Table($tabledata) {
        $this->showfields = str_replace(", ",",",$this->showfields);
		$this->showfields = str_replace(" ,",",",$this->showfields);
        $this->ArrayStatsTable($tabledata,$this->from,$this->to,$this->showfields,$this->label);
    }
    
    # this report displays a pie chart, for example
    public function PieChart($data = array(), $legendOrientation = "east", $legendDisplay = "block",  $actionmenu_type = '') {
		# Return an error when there's no data for this daterange.
		if(!empty($data) && $data == _NO_DATA_FOR_THIS_DATE_RANGE) {
			$this->reportHeader();
			echoNotice(_NO_DATA_FOR_THIS_DATE_RANGE, "margin: 10px;");
			return;
		}
		
		# Return an error when there's no data.
		if(empty($data)) {
			echoNotice(_NO_DATA_TO_DISPLAY." "._CHECK_SETTINGS_FOR_REPORT,"margin:10px;");
			return; 
		}
		
		# Return an error when the highest value in a data set is 0.
		if(multimax($data, 1) == 0){
			echoNotice(_NO_DATA_TO_DISPLAY." "._CHECK_SETTINGS_FOR_REPORT,"margin:10px;");
			return;
		}
		
		# Create an unique id for the graphcontainer
		$md5_time = md5(time());
		$container_id = $md5_time.rand(1,1000);
		
		$datapoints = array();
		$legends = array();
		$matches = array();
		
		$c = 0;
		$dataset = "";
		foreach($data as $key => $val) {
			$datapoints[] = $val[1];
			
			$uparts = explode("##",$val[0]);
			if(isset($uparts[2])) { # it's an IP number
				if(!empty($uparts[2])) {
					$title = $uparts[2];
				} else {
					$title = $uparts[0];
				}
			} elseif(!empty($uparts[1])) { # it's a page with a title
				if (strlen($uparts[1]) > $this->displayMaxChars) {
					$title = substr($uparts[1],0,$this->displayMaxChars) . "...";
				} else {
					$title = $uparts[1];
				}
			} else { # it's something different
				$title = $uparts[0];
			}
			$legends[] = "'{$title}'";
			
			preg_match_all("/<a(.*)>(.*)<\/a>/", $title, $regex_result);
			$matches[] = $regex_result[2];
			
			$c++;
		}
		
		foreach($matches as $match_key => $match) {
			if(!empty($matches[$match_key])) {
				$matches[$match_key] = "'".$match[0]."'";
			} else {
				$matches[$match_key] = $legends[$match_key];
			}
		}
		
		# Fetch the plots that need to be hidden
		$hiddenLegends = $this->GetHiddenLegends($legends);
		
		# Pick the largest of limit and the legend count
		if(count($legends) >= $this->limit) {
			$legendcount = count($legends);
		} else {
			$legendcount = $this->limit;
		}
		
		# If we hide this row of data; Set the data for each data point in this row to zero
		foreach($datapoints as $k => $val) {
			if(!empty($hiddenLegends[$this->current_plotting_graph - 1]) && in_array($k, $hiddenLegends[$this->current_plotting_graph - 1])) {
				$datapoints[$k] = 0;
			}
		}
		
		?>
        <script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			var data = [
				<?php for($c = 0; $c < count($datapoints); $c++) { if($c > 0) { echo " ,"; } ?>[<?php echo $legends[$c]; ?>, <?php echo $datapoints[$c]; ?>]<?php } ?>
			];
			
			var graphlabels = new Array(
				<?php for($c = 0; $c < count($datapoints); $c++) { if($c > 0) { echo " ,"; } echo $legends[$c]; } ?>
			);
			
			var htmlless_graphlabels = new Array(
				<?php for($c = 0; $c < count($datapoints); $c++) { if($c > 0) { echo " ,"; } echo $matches[$c]; } ?>
			);
			
			$("#<?php echo $container_id; ?>").bind('jqplotDataMouseOver', function (ev, seriesIndex, pointIndex, data) {
				if(data != null) {
					tooltipcontents = "<strong>" + data[0] + "</strong><br/> " + data[1] + " (" + Math.round((data[1] / $(this).attr('rel')) * 100) + "%)";
					
					$(this).parent().find(".graph_tooltip").css("top", (ev.pageY + 25));
					
					if(ev.pageX - ($(this).parent().find(".graph_tooltip").outerWidth() / 2) < $(this).parent().find(".graph_tooltip").outerWidth() / 2) {
						var tooltip_horizon = ev.pageX;
					} else if(ev.pageX - ($(this).parent().find(".graph_tooltip").outerWidth() / 2) > $("body").outerWidth() - $(this).parent().find(".graph_tooltip").outerWidth()) {
						var tooltip_horizon = ev.pageX - $(this).parent().find(".graph_tooltip").outerWidth();
					} else {
						var tooltip_horizon = ev.pageX - ($(this).parent().find(".graph_tooltip").outerWidth() / 2);
					}
					
					$(this).parent().find(".graph_tooltip").css("left", tooltip_horizon);
					
					$(this).parent().find(".graph_tooltip").html(tooltipcontents);
					
					$(this).parent().find(".graph_tooltip").show();
					$(this).parent().find(".graph_tooltip").css("display","block");
				} else {
					$(this).parent().find(".graph_tooltip").hide();
					$(this).parent().find(".graph_tooltip").css("display","none");
				}
			});
			
			$("#<?php echo $container_id; ?>").bind('jqplotDataUnhighlight', function (ev) {
				$(this).parent().find(".graph_tooltip").hide();
			});
			
			<?php
			# Populate the graphcolors array, so always have enough colors.
			$this->graphcolors = $this->PopulateColors($this->graphcolors, ceil(count($legends) / count($this->graphcolors)));
			
			$gc = array();
			foreach($this->graphcolors as $k => $graphcolor) {
				# If this data row is 'hidden'; give it color white, so it's physically invisible.
				if(isset($showcolumns[$k]) && $showcolumns[$k] == 0) {
					$gc[] = "'rgb(255, 255, 255)'";
				} else { # If it's not hidden, just give it the color it should have.
					$gc[] = "'{$graphcolor}'";
				}
			}
			
			$graphcolors_js = implode(",", $gc);
			?>
			
			plots['<?php echo $container_id; ?>'] = $.jqplot ('<?php echo $container_id; ?>', [data],{
				seriesColors: [<?php echo $graphcolors_js; ?>],
				seriesDefaults: {
					// Make this a pie chart.
					renderer: jQuery.jqplot.PieRenderer,
					rendererOptions: {
						// Put data labels on the pie slices.
						// By default, labels show the percentage of the slice.
						diameter: $("#<?php echo $container_id; ?>").outerHeight(),
						showDataLabels: true,
						startAngle: -125,
						shadowOffset: 0,
						shadowDepth: 5,
						shadowAlpha: 0.07
					}
				},
				highlighter: {
					show:false
				},
				cursor: {
					show:false
				},
				legend: { show: false },
				grid: {
					shadow: false,
					borderWidth: 0
				},
				height: 250,
				gridPadding: {top:0, bottom:0, left:0, right:0}
			});
			
			$.jqplot.eventListenerHooks.push(['jqplotMouseMove', showGraphTooltip]);
			$.jqplot.eventListenerHooks.push(['jqplotMouseLeave', hideGraphTooltip]);
			
			$("#<?php echo $container_id; ?>").closest(".graph_area").append("<div class='graph_tooltip'></div>");
		});
        </script>
		
		<?php if($legendOrientation == "east") { $pie_styling = "width: 45%; float: left; "; } else if($legendOrientation == "west") { $pie_styling = "width: 40%; float: right;"; } ?>
		<?php 
			if ($this->limit > 13) {
				$graphstyle = "margin: 10px;";
				$legendcontainerstyle = "margin: 0 auto; padding-left: 45px;";
				$legendstyle = "width: 45%; float: left;";
			} else {
				$graphstyle = "width: 60%; float: left; margin: 10px;";
				$legendcontainerstyle = "width: 33%; float: left; margin: 15px 0px 0px 0px;";
				$legendstyle = "width: 100%; display: block; clear: both;";
			}
		?>
		<div class='graph_area' style=''>
			<div id='<?php echo $container_id; ?>' rel='<?php echo array_sum($datapoints); ?>' style='<?php echo $pie_styling; ?>'></div>
			<?php echo $this->CreateLegend($legends, $legendcontainerstyle, $legendstyle, 28); ?>
			<div class='graph_tooltip'></div>
		</div>
		<?php
        return;
    }
	
	private function CreateLegend($legends, $containerStyle = "", $legendStyle = "", $maxchars = 27) {
		# Even though Tupac and Bob Marley are legends, they were not made here...
		
		# Get a list of legend items to hide
		$hiddenLegends = $this->GetHiddenLegends($legends);
		
		$legend = "<div class='glegend' style='{$containerStyle}'>";
		
		for($c = 0; $c < count($legends); $c++) {
			# If the legend items starts or ends with a quote, remove it.
			if(substr($legends[$c], 0, 1) == "'") {
				$legends[$c] = substr($legends[$c], 1);
			}
			if(substr($legends[$c], -1) == "'") {
				$legends[$c] = substr($legends[$c], 0, -1);
			}
			
			$full_text = strip_tags($legends[$c]);
			$short_text = substr($full_text, 0, $maxchars);
			$short_text_inc_html = str_replace($full_text, $short_text, $legends[$c]);
			
			# Set whether to show or hide a legend item
			if(!empty($hiddenLegends[$this->current_plotting_graph - 1]) && in_array($c, $hiddenLegends[$this->current_plotting_graph - 1])) {
				$legendclass = 'switchable inactive_plot';
			} else {
				$legendclass = 'switchable';
			}
			
			$legend .= "<div style='color: {$this->graphcolors[$c]}; {$legendStyle}' title='{$full_text}'>";
				if(!empty($this->actionmenu_type)) {
					$actionmenu_link = " style='cursor: pointer;' onclick='popupMenu(event, \"{$legends[$c]}\", \"{$this->actionmenu_type}\")' ";
				} else {
					$actionmenu_link = "";
				}
				
				$legend .= "<div class='legend_bullet {$legendclass}'>";
					$legend .= "<div style='background-color: {$this->graphcolors[$c]};'></div>";
				$legend .= "</div>";
				
				$legend .= "<label {$actionmenu_link}>".$short_text_inc_html;
				if (strlen($short_text) > $maxchars) {
					$legend .= "...";
				}
				$legend .= "</label>";
			$legend .= "</div>";
		}
		
		$legend .= "<div class='clear'></div></div>";
		
		return $legend;
	}
	
	public function ReorderColumns($data,$neworder) {
		if (empty($neworder)) {
			return $data;
		}		
		$cols = array_flip(explode(',',$this->showfields));
		$order = explode(',',$neworder);
		$ncols = count($order);
		$newdata = array();
		$i=0;
		foreach ($data as $row) {
			$c = 0;
			while ($c < $ncols) {
				$thiscol = $cols[$order[$c]];
				$newdata[$i][$c] = $row[$thiscol];
				$c++;
			}
			$i++;
		}
		return $newdata;
	}
	
	public function CompareMeter($what,$val_a,$val_b,$text_a="",$text_b="") {
				
		if ($val_a == 0 && $val_b == 0) {
			$a=50;
			$b=50;
		} else {		
			$tot = ($val_a*100) + ($val_b*100);
			$a = round((($val_a*100) / $tot) * 100);
			$b = round((($val_b*100) / $tot) * 100);		
		}	
		if (empty($text_a)) {
			$text_a=$val_a;
			$text_b=$val_b;			
		}
		
		echo "<div style='margin:0 auto;width:500px;font-style:italic;text-align:center;padding:3px 0 3px 0;'>$what</div>";
		echo "<div style='margin:0 auto;width:500px;border:1px solid grey;border-radius:4px;clear:both;margin-bottom:3px;'>";
		
		echo "<div style='position:absolute;width:250px;height:20px;border-right:1px solid white;'></div>";
		
		echo "<div style='font-size:11px;width:$a%;height:20px;line-height:20px;background: #de241b url(images/bg_glass_red2.png) 50% 50% repeat-x;color:white;float:left;'></div>";		
		echo "<div style='font-size:11px;width:$b%;height:20px;line-height:20px;background: #49c106 url(images/bg_glass_green.png) 50% 50% repeat-x;float:right;text-align:right;'></div>";
		echo "<div style='float:left;margin-top: -20px;width:100%; line-height:20px;'>";
		echo "<p style='float:left; margin:0 0 0 5px; padding:0; color:#FFFFFF;'>".$text_a."</p>";
		echo "<p style='float:right; margin:0 5px 0 0; padding:0; color:#000000;'>".$text_b."</p>";
		echo "</div>";
		echo "<div class=clear></div>";
		echo "</div>";
		
	}
	
	function DisplayWinner($data1,$data2,$itemname=_PAGE){		
		$confidence = 0;
		$cfactor = 0;
		$md5_time = md5(time());
		$container_id = $md5_time.rand(1,1000);
			
		echo "<div id='decide_winner'>";
		echo "<div id='$container_id' class='confidence_graph'></div>";
		
		if ($data1["page_total"]==0 || $data2["page_total"]==0) {
			echo "<b>"._NOT_ENOUGH_DATA_TO_DETERMINE."!</b><br/><br/>";
		} else {
			$epop = ($data1["target_indirect_total"] + $data2["target_indirect_total"]) / ($data1["page_total"] + $data2["page_total"]);
			$eerr = sqrt(($epop * (1 - $epop) * ($data1["page_total"] + $data2["page_total"]))/($data1["page_total"] * $data2["page_total"]));
			$cfactor = @(abs((@($data1["target_indirect_total"]/$data1["page_total"]) - @($data2["target_indirect_total"]/$data2["page_total"])))/$eerr);
			
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
			} 

			if (($data1["target_indirect_total"]/$data1["page_total"]) > ($data2["target_indirect_total"]/$data2["page_total"])) {
					 $winner="<span class=testa>$itemname A</span>";
					 $loser="<span class=testb>$itemname B</span>";
			} else {
					 $winner="<span class=testb>$itemname B</span>";
					 $loser="<span class=testa>$itemname A</span>";
			}
			if ($confidence >= 90) {
				echo "<font size=+1>"._SPLIT_TEST_WINNER." <b>$winner</b>!</font><br/><br/>";
				echo _TEST_RESULTS_ARE_SIGNIFICANT." ($confidence% "._CONFIDENCE."). "._YOU_CAN_BE_ABOUT." $confidence% "._CONFIDENT_THAT." $winner "._WILL_PERFORM_BETTER_THAN." $loser";
				echo "<br>";
			} else if ($confidence > 50) {
				echo "<font size=+1>"._SPLIT_TEST_WINNER." <b>$winner</b></font>";
				echo "<P><b>$winner performed better but ... </b></p>";
				echo _TEST_RESULTS_ARE_NOT_SIGNIFICANT." ($confidence% "._CONFIDENCE."). "._WHEN_THE_LEVEL_DROPS." $winner didn't "._JUST_GOT_LUCKY.".";
				echo "<br>";
			} else {
				echo "<font size=+1>"._SPLIT_TEST_WINNER." "._INCONCLUSIVE."</font>";
				echo "<P><b>$winner performed better but ... </b></p>";
				echo _TEST_RESULTS_ARE_NOT_SIGNIFICANT." ($confidence% "._CONFIDENCE."). "._WHEN_THE_LEVEL_DROPS." $winner did not just perform better by chance."; //"._JUST_GOT_LUCKY.".";
				echo "<br>";
			}
		}
		echo "</div>";
		?>
		<script type="text/javascript" charset="utf-8">
		$(document).ready(function(){
			s1 = [<?php echo $confidence;?>];
	 
			plots['<?php echo $container_id; ?>']  = $.jqplot('<?php echo $container_id;?>',[s1],{
			    seriesDefaults: {
				   renderer: $.jqplot.MeterGaugeRenderer,
				   rendererOptions: {
					   label: 'Confidence',
					   padding:0,
					   min: 0,
					   max: 100,
					   intervals:[25, 50, 75, 90, 100],
					   intervalColors:['#FCF2E5','#FCDCB3','#FCB85F','#FC950F','#0078AE']
				   }
			    },
			    highlighter: {
					show:false
				},
				cursor: {
					show:false
				},
				
			});
		});
		</script>
		<?php
			
	}
	public function FunnelChart($data = array()){
		$md5_time = md5(time()).rand(1,1000);		
		$funnel_labels = array();
		$funnel_data = array();
		
		foreach ($data as $set) {
			$funnel_data[] = $set[2];
			$funnel_labels[] = $set[1];
		}
		
		$jqplot_funnel = "<div class='graph_area' style='width: 250px;'>
			<div class='graph_tooltip'></div>			
			<div id='{$md5_time}' class='graphcontainer' style='height: 250px; margin: 0; width: auto;'>
						
			</div>
			
			<ul class='graphlegend' style='display:none;'>";
			foreach($funnel_labels as $funnel_label) {
				$jqplot_funnel .= "<li><div class='legend_bullet'><div style='background-color: #F00;'></div></div><label>{$funnel_label}</label></li>";
			}
			$jqplot_funnel .= "</ul>
		</div>";
		
		$funnel_labels = json_encode($funnel_labels);
		$funnel_data = json_encode($funnel_data);		
		$plot_options = array(
			"seriesColors" => $this->graphcolors,
			"seriesDefaults" => array(
				"rendererOptions" => array(
					"sectionMargin" => 0,
					"widthRatio" => 0.3,
					"dataLabels" => 'value',
					"highlightMouseOver" => true,
					"shadow" => false,
					"padding" => array(
						"top" => 0,
						"right" => 0,
						"bottom" => 0,
						"left" => 0
					)
				)
			),
			"cursor" => array(
				"show" => false
			),
			"highlighter" => array(
				"show" => false
			),
			"grid" => array(
				"shadow" => false,
				"gridLineColor" => 'rgba(239,239,239,1)',
				"borderWidth" => 0,
				"borderColor" => 'rgba(239,239,239,1)'
			)
		);		
		$plot_options = json_encode($plot_options);		
		$jqplot_funnel .= "<script type='text/javascript'>createFunnel('{$md5_time}', ".$funnel_labels.", ".$funnel_data.", ".$plot_options.");</script>";
		echo $jqplot_funnel;
	}
	
	public function BubbleChart($data = array()) {
		
		# create a unique container id for our graph
		$md5_time = md5(time());
		$container_id = $md5_time.rand(1, 1000);
		
		# reorder our data array to what the bubble chart wants (x, y, bubblesize, label)
		$data = $this->ReorderColumns($data, $this->bubblefields);
		
		# we need to make a field list with last field at the beginning of the array to get our tool tip right
		$fields = explode(",", $this->bubblefields);
		$last = array_pop($fields);
		array_unshift($fields, $last);
		
		# Set up a legends array.
		$datastring = "";
		foreach($data as $row) {
			$legends[] = substr($row[3], 0, strpos($row[3], '##'));
		}
		
		# Fetch an array with plots/bubbles to hide
		$hiddenLegends = $this->GetHiddenLegends($legends);
		
		# Change the data array to a string for javascript.
		$datastring = "";
		foreach($data as $k => $row) {
			if (!empty($datastring)) {
				$datastring .= ", ";
			}
			
			$row[3] = '"'.substr($row[3], 0, strpos($row[3], '##')).'"';
			
			# If we hide this row of data; Set the data for each data point in this row to zero
			if(!empty($hiddenLegends[$this->current_plotting_graph - 1]) && in_array($k, $hiddenLegends[$this->current_plotting_graph - 1])) {
				$row[0] = 0;
				$row[1] = 0;
				$row[2] = 0;
			}
			
			$datastring .= "[".implode(', ',$row)."]";
		}
		
		# Populate the color array so we always have enough colors
		$this->graphcolors = $this->PopulateColors($this->graphcolors, ceil(count($legends) / count($this->graphcolors)));
		
		# create a string of colors for use in javascript 
		$gc = array();
		foreach($this->graphcolors as $k => $graphcolor) {
			# If this data row is 'hidden'; give it color white, so it's physically invisible.
			if(isset($showcolumns[$k]) && $showcolumns[$k] == 0) {
				$gc[] = "'rgb(255, 255, 255)'";
			} else { # If it's not hidden, just give it the color it should have.
				$gc[] = "'{$graphcolor}'";
			}
		}
		$graphcolors_js = implode(",", $gc);
		
		# print the javascript and the html container
		?>
		
        <script type="text/javascript" charset="utf-8">
		$(document).ready(function(){			
			
			<?php
			echo "var arr = [".$datastring."];";
			?>
			plots['<?php echo $container_id; ?>'] = $.jqplot('<?php echo $container_id; ?>',[arr],{
				animate: <?php echo $this->animate_graph ?>,
				animateReplot: <?php echo $this->animate_graph ?>,
				seriesColors: [<?php echo $graphcolors_js; ?>],
				seriesDefaults:{
					renderer: $.jqplot.BubbleRenderer,
					rendererOptions: {
						highlightMouseOver: false,
						bubbleAlpha: 0.95,
						autoscaleMultiplier: 1.2,
						showLabels: false
					},
					shadow: false,
				},
				cursor:{
					show: true,
					zoom:true,
					showTooltip:false
				},
				highlighter: {
					show:true,
					showTooltip: true,
					showMarker: false,
					tooltipAxes: 'both',
					yvalues: 2,
					tooltipLocation: 'e',
					useAxesFormatters: true,
					formatString: '<table class="jqplot-highlighter" style="color: #333;"><tr><td><?php echo $fields[0]; ?>:</td><td>%s</td></tr><tr><td><?php echo $fields[1]; ?>:</td><td>%s</td></tr><tr><td><?php echo $fields[2]; ?>:</td><td>%s</td><tr><td><?php echo $fields[3]; ?>:</td><td>%s</td></tr></tr></table>'
				},
				grid: {
					shadow: false,
					gridLineColor: 'rgba(239,239,239,1)',
					borderWidth: 1,
					borderColor: 'rgba(239,239,239,1)'
				},
				axes: {
					xaxis: {
						tickRenderer: $.jqplot.CanvasAxisTickRenderer,
						tickOptions: {
							angle: -45,
							fontSize: '9px'
						},
						showLabel: true,
						label:'Visitors',
						labelOptions: {
							fontSize: '10px'
						}
					},
					yaxis: {
						labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
						tickRenderer: $.jqplot.CanvasAxisTickRenderer,
						tickOptions: {
							fontSize: '9px'
							
						},						
						showLabel: true,
						label:'Pages per User',
						labelOptions: {
							fontSize: '10px',
							rotate: 90
						}
					}
				}
			});

		});		
		</script>
		<?php
		if ($this->limit > 13) {
			$graphstyle = "margin:10px;";
			$legendcontainerstyle = "margin:0 auto;padding-left:45px;";
			$legendstyle = "width:45%;float:left;";
		} else {
			$graphstyle = "width:60%; float:left;margin:10px;";
			$legendcontainerstyle = "width:33%;float:left;margin:15px 0px 0px 0px;";
			$legendstyle = "width:100%;display:block;clear:both;";
		}
		
		echo "<div style='overflow:hidden;'>";
		echo "<div class='graph_area' style='$graphstyle'>";
		echo "<div id='{$container_id}'></div>";
		echo "</div>";			
		echo $this->CreateLegend($legends, $legendcontainerstyle, $legendstyle, 28);
		echo "</div>";
	}
	
	
	
	public function Graph($data = array(), $graph_type = 'line', $actionmenu_type = '', $chartWidth = 0, $chartHeight = 300, $custom_graphoptions = array(), $legendOrientation = "south", $legendDisplay = "block") {
		global $reports;
		
		if(empty($data)) { return false; }
		
		# Create a unique id for our graph
		$md5_time = md5(time()).rand(1,1000);
		
		$graphdata = array();
		
		$fields = explode(",", $this->showfields);
		
		# Fix fields array, should any be missing
		$x = 0;
		$newfields = array();
		foreach($fields as $c => $f) {
			$newfields[$x] = $f;
			$x++;
		}
		
		$fields = $newfields;
		
		$newdata = array();
		foreach($data as $key => $value) {
			$x = 0;
			foreach($value as $k => $v) {
				$newdata[$key][$x] = $v;
				$x++;
			}
		}
		
		$data = $newdata;
		
		$barcount = 0;
		$linecount = 0;
		$maxNumber = 0;
		$i = 1;
		foreach($data as $key => $value) {
			for($c = 0; $c < count($value); $c++) {
				# Find out which graph renderer we should use
				if(!is_array($graph_type)) {
					if($graph_type == 'line') {
						$dataset_type = 'line';
						$linecount++;
					} elseif($graph_type == 'bar') {
						$dataset_type = 'bar';
						$barcount++;
					} elseif($graph_type == 'area') {
						$dataset_type = 'area';
					}
				} else {
					if(!empty($graph_type[$c])) {
						if($graph_type[$c] == 'line') {
							$dataset_type = 'line';
							$linecount++;
						} elseif($graph_type[$c] == 'bar') {
							$dataset_type = 'bar';
							$barcount++;
						} elseif($graph_type[$c] == 'area') {
							$dataset_type = 'area';
						}
					} else {
						$dataset_type = '';
					}
				}
				
				# Set the first entry in each series to include the legend's label, and which plot type it should be
				$graphdata[$c][0] =  "'".$fields[$c]."|{$dataset_type}"."'";
				
				if(empty($value[$c])) {
					# If the value does not exists, make it empty.
					$graphdata[$c][$i] = "' '";
				} else {
					if(strpos($graphdata[$c][0], _DATE) != false && $this->allowDateFormat == true) {
						$tempstamp = strtotime($value[$c]);
						$value[$c] = date(implode($this->profile->dateFormat),$tempstamp);
					}
					
					if(is_numeric($value[$c]) && $value[$c] > $maxNumber) {
						$maxNumber = $value[$c];
					}
					
					$graphdata[$c][$i] = "'".$value[$c]."'";
				}
			}
			$i++;
		}
		
		$tmp_data = array();
		for($c = 1; $c < count($graphdata); $c++) {
			$total_val = 0;
			for($i = 1; $i < count($graphdata[$c]); $i++) {
				$v = trim(str_replace("'", "", $graphdata[$c][$i]));
				if(!empty($v)) {
					$total_val += $v;
				}
			}
			
			$graphdata[$c][0] = "'".substr($graphdata[$c][0], 1);
			$tmp_data[$total_val][] = $graphdata[$c];
		}
		
		krsort($tmp_data);
		
		# Create the Legends array
		$legends = array();
		foreach($tmp_data as $tmp) {
			foreach($tmp as $temp) {
				$legends[] = substr($temp[0], 0, strpos($temp[0], '|'));
			}
		}
		
		# Get the hidden legend items
		$hiddenLegends = $this->GetHiddenLegends($legends);
		
		$t = array();
		$c = 1;
		foreach($tmp_data as $tmp) {
			foreach($tmp as $temp) {
				if(!empty($hiddenLegends[$this->current_plotting_graph - 1])) {
					foreach($hiddenLegends[$this->current_plotting_graph - 1] as $x) {
						if(($c - 1) == $x) {
							# Set the data for this data set to zero, if we hide this set
							$t[$c] = array_fill(0, count($temp), "'0'");
							$t[$c][0] = $temp[0];
						} else {
							$t[$c] = $temp;
						}
					}
				} else {
					$t[$c] = $temp;
				}
				
				$c++;
			}
		}
		
		if(!empty($graphdata[0])) {
			$darr = $graphdata[0];
		} else {
			$darr = NULL;
		}
		$graphdata = $t;
		$graphdata[0] = $darr;
		
		ksort($graphdata);
		
		if($maxNumber < 10) {
			$yaxis_format = "%.2f";
		} else {
			$yaxis_format = "%.0f";
		}
		
		# Populate the color array so we always have enough colors.
		$this->graphcolors = $this->PopulateColors($this->graphcolors, ceil(count($legends) / count($this->graphcolors)));
		$gcolors = $this->graphcolors;
		
		foreach($gcolors as $k => $gcolor) {
			if(!empty($hiddenLegends[$this->current_plotting_graph - 1])) {
				foreach($hiddenLegends[$this->current_plotting_graph - 1] as $x) {
					# If the plot is 'hidden', color it white.
					$gcolors[$x] = 'rgb(255, 255, 255)';
				}
			}
		}
		
		$color_js = $gcolors;
		
		if($barcount == 0 && $linecount > 0) {
			$graphoptions = array( // lineChart
				"animate" => $this->animate_graph,
				"animateReplot" => $this->animate_graph,
				"seriesColors" => $color_js,
				"seriesDefaults" => array(
					"shadow" => false,
					"lineWidth" => 6,
					"markerOptions" => array(
						"show" => true,
						"size" => 8,
						"style" => 'circle',
						"color" => "#FFFFFF",
						"lineWidth" => 1,
						"shadow" => false
					),
					"rendererOptions" => array(
						"smooth" => true,
						"animation" => array(
							"speed" => 4000
						)
					)
				),
				"series" => array(
				),
				"highlighter" => array(
					"show" => false
				),
				"cursor" => array(
					"show" => false,
					"style" => 'default',
					"zoom" => false,
					"showTooltip" => false,
					"showVerticalLine" => true
				),
				"grid" => array(
					"shadow" => false,
					"gridLineColor" => 'rgba(239,239,239,1)',
					"borderWidth" => 1,
					"borderColor" => 'rgba(239,239,239,1)'
				),
				"axes" => array(
					"xaxis" => array(
						"tickInterval" => 1,
						"tickOptions" => array(
							"angle" => -45,
							"fontSize" => '9px'
						),
						"ticks" => array()
					),
					"yaxis" => array(
						"tickOptions" => array(
							"fontSize" => '9px',
							"formatString" => $yaxis_format
						),
						"ticks" => array(),
						"showLabel" => true,
						"label" => "",
						"labelOptions" => array(
							"fontSize" => "10px",
							"rotate" => 90
						),
						"rendererOptions" => array(
							"forceTickAt0" => true
						)
					)
				)
			);
		} else if($linecount == 0 && $barcount > 0) {
			$graphoptions = array( // barChart
				"animate" => $this->animate_graph,
				"animateReplot" => $this->animate_graph,
				"seriesColors" => $color_js,
				"seriesDefaults" => array(					
					"rendererOptions" => array(
						"barWidth" => null,
						"barPadding" => 3,
						"barMargin" => 3,
						"animation" => array(
							"speed" => 4000
						)
					),
					"shadow" => false,
					"markerRenderer" => "$.jqplot.MarkerRenderer",
					"markerOptions" => array(
						"show" => true,
						"size" => 8,
						"style" => 'circle',
						"color" => "#FFFFFF",
						"lineWidth" => 1,
						"shadow" => false
					)
				),
				"series" => array(
				),
				"highlighter" => array(
					"show" => false
				),
				"cursor" => array(
					"show" => false,
					"style" => 'default',
					"zoom" => false,
					"showTooltip" => false,
					"showVerticalLine" => true
				),
				"grid" => array(
					"shadow" => false,
					"gridLineColor" => 'rgba(239,239,239,1)',
					"borderWidth" => 1,
					"borderColor" => 'rgba(239,239,239,1)'
				),
				"axes" => array(
					"xaxis" => array(
						"renderer" => "$.jqplot.CategoryAxisRenderer",
						"tickRenderer" => "$.jqplot.CanvasAxisTickRenderer",
						"tickOptions" => array(
							"angle" => -45,
							"fontSize" => '9px'
						),
						"ticks" => array()
					),
					"yaxis" => array(
						"tickRenderer" => "$.jqplot.CanvasAxisTickRenderer",
						"tickOptions" => array(
							"fontSize" => '9px',
							"formatString" => $yaxis_format
						),
						"ticks" => array(),
						"showLabel" => true,
						"label" => "",
						"labelOptions" => array(
							"fontSize" => "10px",
							"rotate" => 90
						)
					)
				)
			);
		} else if($barcount > 0 && $linecount > 0) {
			$graphoptions = array( // barLineChart
				"animate" => $this->animate_graph,
				"animateReplot" => $this->animate_graph,
				"seriesColors" => $color_js,
				"seriesDefaults" => array(
					"renderer" => "$.jqplot.BarRenderer",
					"rendererOptions" => array(
						"barWidth" => null,
						"barPadding" => 3,
						"barMargin" => 3,
						"animation" => array(
							"speed" => 4000
						)
					),
					"shadow" => false,
					"markerRenderer" => "$.jqplot.MarkerRenderer",
					"markerOptions" => array(
						"show" => true,
						"size" => 8,
						"style" => 'circle',
						"color" => "#FFFFFF",
						"lineWidth" => 1,
						"shadow" => false
					),
				),
				"series" => array(),
				"cursor" => array(
					"show" => false,
					"style" => 'default',
					"zoom" => false,
					"showTooltip" => false,
					"showVerticalLine" => true
				),
				"highlighter" => array(
					"show" => false
				),
				"grid" => array(
					"shadow" => false,
					"gridLineColor" => 'rgba(239,239,239,1)',
					"borderWidth" => 1,
					"borderColor" => 'rgba(239,239,239,1)'
				),
				"axes" => array(
					"xaxis" => array(
						"renderer" => "$.jqplot.CategoryAxisRenderer",
						"tickRenderer" => "$.jqplot.CanvasAxisTickRenderer",
						"tickOptions" => array(
							"angle" => -45,
							"fontSize" => '9px'
						),
						"ticks" => array()
					),
					"yaxis" => array(
						"tickRenderer" => "$.jqplot.CanvasAxisTickRenderer",
						"tickOptions" => array(
							"fontSize" => '9px',
							"formatString" => $yaxis_format
						),
						"ticks" => array(),
						"showLabel" => true,
						"label" => "",
						"labelOptions" => array(
							"fontSize" => "10px",
							"rotate" => 90
						)
					)
				)
			);
		} else {
			$graphoptions = array( // areaChart
				"animate" => $this->animate_graph,
				"animateReplot" => $this->animate_graph,
				"seriesColors" => $color_js,
				"seriesDefaults" => array(
					"shadow" => false,
					"lineWidth" => 6,
					"markerOptions" => array(
						"show" => true,
						"size" => 8,
						"style" => 'circle',
						"color" => "#FFFFFF",
						"lineWidth" => 1,
						"shadow" => false
					),
					"fill" => true,
					"trendline" => array(
						"show" => false
					),
					"rendererOptions" => array(
						"smooth" => true,
						"animation" => array(
							"speed" => 4000
						)
					)
				),
				"series" => array(
				),
				"stackSeries" => true,
				"highlighter" => array(
					"show" => false
				),
				"cursor" => array(
					"show" => false,
					"style" => 'default',
					"zoom" => false,
					"showTooltip" => false,
					"showVerticalLine" => true
				),
				"grid" => array(
					"shadow" => false,
					"gridLineColor" => 'rgba(239,239,239,1)',
					"borderWidth" => 1,
					"borderColor" => 'rgba(239,239,239,1)'
				),
				"axes" => array(
					"xaxis" => array(
						"tickOptions" => array(
							"angle" => -45,
							"fontSize" => '9px'
						),
						"ticks" => array()
					),
					"yaxis" => array(
						"tickOptions" => array(
							"fontSize" => '9px',
							"formatString" => $yaxis_format
						),
						"ticks" => array(),
						"showLabel" => true,
						"label" => "",
						"labelOptions" => array(
							"fontSize" => "10px",
							"rotate" => 90
						)
					)
				)
			);
		}
		
		$new_graphoptions = $graphoptions;
		
		# This does some multi-dimensional voodoo.
		foreach($graphoptions as $key => $val) {
			if(count($val) > 1) {
				foreach($val as $key2 => $val2) {
					if(count($val2) > 1) {
						foreach($val2 as $key3 => $val3) {
							if(count($val3) > 1) {
								foreach($val3 as $key4 => $val4) {
									if(isset($custom_graphoptions[$key][$key2][$key3][$key4])) {
										$new_graphoptions[$key][$key2][$key3][$key4] = $custom_graphoptions[$key][$key2][$key3][$key4];
									}
								}
							} else {
								if(isset($custom_graphoptions[$key][$key2][$key3])) {
									$new_graphoptions[$key][$key2][$key3] = $custom_graphoptions[$key][$key2][$key3];
								}
							}
						}
					} else {
						if(isset($custom_graphoptions[$key][$key2])) {
							$new_graphoptions[$key][$key2] = $custom_graphoptions[$key][$key2];
						}
					}
				}
			} else {
				if(isset($custom_graphoptions[$key])) {
					$new_graphoptions[$key] = $custom_graphoptions[$key];
				}
			}
		}
		
		$graphdata_js = "";
		$graphdata_js_array = array();
		$c = 0;
		
		foreach($graphdata as $value) {
			$graphdata_js_array[$c] = "[";
			if(!empty($value)) {
				$graphdata_js_array[$c] .= implode(",", $value);
			}
			$graphdata_js_array[$c] .= "]";
			$c++;
		}
		
		$graphdata_js = implode(",", $graphdata_js_array);
		?>
        <script type="text/javascript" charset="utf-8">
			var graphdata_<?php echo $md5_time; ?> = [<?php echo $graphdata_js; ?>];
			$(document).ready(function() {
			<?php
			$new_graphoptions = json_encode($new_graphoptions);
			$plotgraph_js = "plotGraph('$md5_time',graphdata_{$md5_time}";
				if(!empty($actionmenu_type)) {
					$plotgraph_js .= ", '{$actionmenu_type}'";
				} else {
					$plotgraph_js .= ", ''";
				}
				$plotgraph_js .= ", {$new_graphoptions}, '{$legendOrientation}_legend', '{$legendDisplay}');";
			echo $plotgraph_js; ?>
			});
        </script>
		<?php
		if($legendOrientation == "east") {
			$chartWidth = "60%; float: left";
		} else if($legendOrientation == "west") {
			$chartWidth = "60%; float: right";
		}
		
		if ($this->limit > 13) {
			$legendcontainerstyle = "margin:0 auto; padding-left: 45px;";
			$legendstyle = "width: 45%; float: left;";
		} else {
			if(count($legends) < 10) {
				$legendcontainerstyle = "margin: 10px 0px 0px 0px;";
				$legendstyle = "float: left;";
			} else {
				$legendcontainerstyle = "width: 33%; float: left; margin: 15px 0px 0px 0px;";
				$legendstyle = "width: 100%; display: block; clear: both;";
			}
		}
		
		$this->actionmenu_type = $actionmenu_type;
		
		$legendoptions = array();
		?>
		<div class='graph_area' style='margin-top: 20px;'>
			<div id='<?php echo $md5_time; ?>' class='graphcontainer' style='<?php if(empty($chartWidth)) { echo "width: auto; "; } else { echo "width: {$chartWidth}; "; } echo "height: {$chartHeight}px;"; ?>'></div>
			<?php echo $this->CreateLegend($legends, $legendcontainerstyle, $legendstyle, 40, $legendoptions); ?>
		</div>
		<?php
        return;
	}
	
	
	
	public function ReportHeader() {
		global $reports, $clabel;
		
		$nicefrom = date("d M Y",$this->from);
		$niceto = date("d M Y",$this->to);
		
		if(isset($this->trafficsource)) {
			if ($this->trafficsource > 0) {
				$source = getTrafficSourceByID($this->trafficsource);
				$segmentname = $source['sourcename'];
			} else {
				$segmentname="";   
			}
		}
		if (isset($this->searchmode)) {
			if ($this->searchmode == "like") {
				$nice_searchmode = "contain";
			} else {
				$nice_searchmode = "do not contain";
			}
		}
		echo "<div class='report-header'>";
			if ($this->displayReportLabel == true) {
				echo "<h2>{$this->label}</h2>";
			}
			
			// Check date format handling
			if($this->allowDateFormat == true){
				$tempstamp = strtotime($nicefrom); 
				$nicefrom = date(implode($this->profile->dateFormat),$tempstamp);
				
				$tempstamp = strtotime($niceto); 
				$niceto = date(implode($this->profile->dateFormat),$tempstamp);
			}
			
			echo "<div class='report-header-content'>";
				echo "<div class='dialog_info'>";
				if (empty($this->customHeaderContent)) {
					if(!empty($clabel)) {
						if(strpos($reports[$clabel]["Options"], "daterangeField") !== false) {
							echo "<span>{$nicefrom} - {$niceto}</span>";
						}
					} else {
						if(strpos($reports[$this->labels]["Options"], "daterangeField") !== false) {
							echo "<span>{$nicefrom} - {$niceto}</span>";
						}
					}
					if(!empty($this->addlabel)){ echo "<span>".stripslashes($this->addlabel)."</span> "; }
					if(!empty($segmentname)){ echo "<span>"._SEGMENT.": {$segmentname}</span> "; }
					if(!empty($this->search)){ echo "<span>"._RESULTS_THAT." {$nice_searchmode} '".stripslashes($this->search)."'</span> "; }
					if(!empty($this->roadto)){ echo "<span>"._USING_TARGET_PAGE." '".stripslashes($this->roadto)."'</span> "; }
				} else {
					echo "{$this->customHeaderContent}";
				}
				echo "</div>";
				if ($this->displayReportButtons == true) {
					echo "<div class='report-header-buttons'>";
						echo $this->ReportHeaderButtons();
					echo "</div>";
					echo "<div class='clear'></div>";
					echo "<div class='help_content'>";
						echo "{$this->help}";
						echo "<div style='padding: 12px;'><a class='help_btn text'>"._CLOSE_HELP_TEXT."</a></div>";
					echo "</div>";
				}
			echo "</div>";
		echo "</div>";
	}
	
	function ReportHeaderButtons() {
		global $cachename, $validUserRequired;
		

		//echo "<a target='_blank' href='reports.php?conf={$this->profile->profilename}&to={$this->to}&from={$this->from}&labels={$this->labels}&limit={$this->limit}&outputmode=print' title=\""._PRINTABLE_REPORT."\"><img src='images/icons/printer.png'></a>";
		echo "<a target='_blank' href='reports.php?{$_SERVER['QUERY_STRING']}&outputmode=print' title=\""._PRINTABLE_REPORT."\"><img src='images/icons/printer.png'></a>";
		
		// echo "<a target='_blank' href='reports.php?{$_SERVER['QUERY_STRING']}&outputmode=xml' title=\""._PRINTABLE_REPORT."\">XML</a>";
		
		echo "<a class='mail_btn' title=\""._EMAIL_REPORT."\"><img src='images/icons/email_go.gif'></a>";

		echo "<a href='reports.php?{$_SERVER['QUERY_STRING']}&outputmode=csv' target='_blank' title=\""._SAVE_REPORT_CSV."\"><img src='images/icons/csvexport.gif'></a>";
		
		echo "<a class='help_btn' title='Open Help'><img src='images/icons/help.png'></a>";
		echo "<div class='clear'></div>";
		
		if (!$validUserRequired && (!isset($_SERVER["PHP_AUTH_USER"]))) {
			//echo "<div id=\"mailer{$cachename}\" style=\"display : none; line-height : 18px; position : absolute; top:100px;left:250px;background-color:#f0f0f0;border: 2px solid red;z-index:10;padding:15px;\"><img src=\"images/icons/email_go.gif\" alt=\"\" align=left> "._ONLY_WHEN_PASSWORD_PROTECTED."</div>";
			echo "<div id=\"mailer{$cachename}\" class='mailer' style='padding:10px;'><img src=\"images/icons/email_go.gif\" alt=\"\" align=left> "._ONLY_WHEN_PASSWORD_PROTECTED."</div>";
		} else {
			?>
			<div id="mailer<?php echo $cachename;?>" class="mailer">
				<form method=post action="reports.php?<?php echo $_SERVER['QUERY_STRING']; ?>&outputmode=email" target=_blank>
					<table cellpadding=5 cellspacing=5 width=450>
						<tr><td colspan=2 class=smallborder>
							<a class='mail_btn'><img src=images/icons/cancel.gif border=0 align=right></a><img src=images/icons/email_go.gif width=16 height=16 align=left> &nbsp;&nbsp;<b><?php echo _SEND_THIS_REPORT_AS_EMAIL;?>:</b>
							<?php //  href="javascript:mailbox('mailer<?php echo $cachename;  ')" ?>
						</td></tr>
						<tr><td title="<?php echo _EMAIL_FROM;?>"><?php echo _YOUR_EMAIL;?>:</td><td title="<?php echo _EMAIL_FROM;?>"> <input type=text name=fromemail size=40></td></tr>
						<tr><td title="<?php echo _EMAIL_TO;?>"><?php echo _THEIR_EMAIL;?>:</td><td title="<?php echo _EMAIL_TO;?>"> <input type=text name=email size=40></td></tr>
						<tr><td><?php echo _SUBJECT;?>:</td><td> <input type=text name=subject value="<?php  echo $this->profile->confdomain . " " .$this->label; ?>" size=40></td></tr>
						<tr><td><?php echo _MESSAGE;?>:</td><td> <textarea name=message cols=40 rows=7></textarea></td></tr>
						<tr><td></td><td> 
						<input type=submit value="Send" onclick="javascript:mailbox('mailer<?php echo $cachename;?>')"><p>
						<?php echo _YOU_WILL_RECEIVE_COPY_OF_EMAIL;?></p>
						</td></tr>
					</table>
				</form>        
			</div>
			<?php
		}

	}
	
	public function ArrayStatsTable($data,$from,$to,$showfields,$labels) {
		//OLD GLOBALS (keep in case of emergancy :P ): global $mini,$nototal,$nograph,$status,$agent,$cnames,$profile,$formemail,$helpdiv,$gi,$search,$searchmode,$addgraph, $opengraph, $cachename;
		global $mini,$nograph,$cachename,$cnames,$gi,$reports;
		
		$header = explode(",",$showfields);
		$table_id=md5('ReportTable'.$labels.$cachename.time());
		
		// define an array of columns that should be turned into a bar chart
		if (!isset($this->bchart)) { $this->bchart= array(); }
		array_push($this->bchart,_PAGEVIEWS,_REQUESTS,_HITS,_CRAWLED_PAGES,_UNIQUE_IPS ,_UNIQUE_IDS,_VISITORS,_TOTAL_REQUESTS,_TOTAL_PAGES,_VIEWED_PAGES,_VISITS,_BOTS,_EXITS,_SIZE_IN_MB,_RECORDS,_SALES ,_UNITS,_REVENUE,_AVERAGE_REVENUE_P_SALE,_RESPONSES,"Friends");
		
		$total_entrys = sizeof($data);
		$displayLength = 25;
		$displayLengthArray = array(10,25,50,100);
		if(intval($this->limit) <= 100){
			if($total_entrys < 10){ unset($displayLengthArray[0]); }
			if($total_entrys < 25){ unset($displayLengthArray[1]); }
			if($total_entrys < 50){ unset($displayLengthArray[3]); }
			if($total_entrys < 100){ unset($displayLengthArray[4]); }
			$displayLength = $total_entrys;
		}
		array_push($displayLengthArray,intval($this->limit));
		array_push($displayLengthArray,$total_entrys);
		sort($displayLengthArray);
		$displayLengthArray = array_unique($displayLengthArray);
		if($this->paginationPanel == true){	$sDom = "tipl"; }else{ $sDom = "t"; }
		
		?>
		<?php
		if(isset($reports[$this->labels]["Options"])){
			$opts = explode(",",$reports[$this->labels]["Options"]);
			if(in_array("columnSelector", $opts)){
				$fields = array();
				foreach($header as $k => $v){
					$durp = "showColumn".$k;
					$fields[$durp] = $k;
				}
				if(!empty($fields)) {
					$hideCols = array_diff_key($fields, $this->options);
					if(count($fields) != count($hideCols)){
						$hides = implode(",",$hideCols);
					}
				}
			}
		}
		?>
		<script language="javascript" type="text/javascript">
		// Define our global variables.
			var conf_name="<?php echo $this->profile->profilename; ?>";
			var from_date=<?php echo $from; ?>;
			var to_date=<?php echo $to; ?>;

			<?php if ($total_entrys > 1 && $this->sortTable==true) { ?>
			$(document).ready(function() {
				$('#<?php echo $table_id; ?>').dataTable( {	
				"aaSorting": [],	
				"sDom": '<?php echo $sDom; ?>',
				"bDestroy": true,
				"iDisplayLength": <?php echo $displayLength; ?>,
				"aoColumnDefs": [
                        { "bVisible": false, "aTargets": [ <?php if(isset($hides)){ echo $hides; }?> ] }
                    ],
				"aLengthMenu":[<?php echo implode(",",$displayLengthArray); ?>]
				});
			} );
			<?php } ?>
			
		</script>
		<?php
		if ($this->displayHeader == true) {
			$this->ReportHeader();
		}
		
		// if we are showing the world map do it now
		if ($labels == _TOP_COUNTRIES_CITIES) {
			$xmlstr = makeMapXMLstr($this->profile->profilename,$from,$to);
			echo "<div id=\"mapArea\" style=\"width:100%;text-align:center;\">";
				// if($mini == 3) {
					////include  "components/map/map_v3.php";
					echo "<iframe src='components/map/map_v3.php?conf={$this->profile->profilename}&from=$from&to=$to' style='border: 0; padding: 0; margin: 0; overflow: hidden;' width='100%' height='400'></iframe>";
				// } else {
					// include  "components/map/map.php";
				// }
			echo "</div>";
		}
		
		?>
		<table cellspacing=0 cellpadding=2 border=0 width="100%" id="<?php echo $table_id; ?>" class="datatable">
		<thead>
		<tr class="tabletotalcolor">
		<?php
		//Print the table headers
		if (isset($this->addgraph) && !empty($this->addgraph)) {
				// this must be the first one in the list or it wont work
				echo "<th>"._GRAPH."</th>\n";
				//$addgraph="";            
		}
		$i=0;
		foreach($header as $thisheader) {
			echo "<th>$thisheader</th>\n";
			$i++;
		}
		echo "</tr>\n";
		echo "</thead><tbody>";
		if (isset($this->addgraph) && !empty($this->addgraph)) {
			// this must be the first one in the list or it wont work
			echo "<tr class=small><td width='250' rowspan='".(count($data) + 1)."' bgcolor=\"white\" valign=\"top\">{$this->addgraph}</td>\n";          
		}  
		
		//Print the values
		$foravg =1;
		$rn=0;
		$totals = array_fill(0, $i, 0);
		$maxval = array_fill(0, $i, 0);
		
		while (isset($data[$rn])) {
			$ii=0;
			while ($ii < $i) {
				$totals[$ii] = $totals[$ii] + $data[$rn][$ii];
				if ($maxval[$ii] < $data[$rn][$ii]) {
					 $maxval[$ii] = $data[$rn][$ii];
				}
				$ii++;
			}
			$rn++;
		}
		
		$r = 0;
		$rn = 0;
		if(!isset($this->search)){
			$this->search = "";
		}
		if (!empty($data)) {
		foreach ($data as $thisdatarow) {
			//echo "we have data";
			$ii=0;
			$rn++;
			//alternate bgcolor
			$r++;
			if ($r==2) {
				$gogray="gray_row";
				$r=0;
			} else {
				$gogray="";
			}
			// print
			$graphcolor="";
			$rid=md5($labels); // this is used to make each menu id unique for the action menu, so it doesn't mess up when there are multple tables on one page
			//echo "<tr id=$ii bgcolor=$gogray class=small>";
			if (@$addgraph=="") {
				echo "<tr class='small $gogray'>";
				//echo "<tr>";
			} else {
				$addgraph="";
			}
			while ($ii < $i) {
				$thisheader = strip_tags($header[$ii]);
				
				// Check date format handling
				if($thisheader == _DATE && $this->allowDateFormat == true){
					$tempstamp = strtotime($thisdatarow[$ii]); 
					$thisdatarow[$ii] = date(implode($this->profile->dateFormat),$tempstamp);
				}				
				// check no handling
				if (in_array($thisheader, $this->disableShowfieldsHandling)) {
					echo "<td>". $thisdatarow[$ii] ."</td>";
				} else if ($thisheader==_PAGE || $thisheader==_LANDING_PAGE) {
					$uparts = explode("##",$thisdatarow[$ii]);
					$thisdatarow[$ii] = $uparts[0];
					$title = @$uparts[1];
					$purl = $thisdatarow[$ii];
					if (strlen($purl) > $this->displayMaxChars) { $purl=substr($purl,0,$this->displayMaxChars) . "..."; }
					if ($purl=="/") {
					  $purl="/ (Home Page)";
					}
					echo "<td class=\"pagecell\">";                
					if ($title!="") { 
						echo "<span class=\"pagetitle\" title=\"$title\">";
						if (strlen($title) > $this->displayMaxChars) {  echo substr($title,0,$this->displayMaxChars) . "..."; } else { echo $title; } 
						echo "</span><br />"; 
					}
					echo "<a class=\"small\" title=\""._CLICK_TO_OPEN_MENU_FOR." $thisdatarow[$ii]\" onclick=\"popupMenu(event, '".urlencode($thisdatarow[$ii])."', 'page', '&from={$this->from}&to={$this->to}');\" uri=\"$thisdatarow[$ii]\" href=\"\">". urldecode($purl) ."</a>";
					echo "</td>";
					
				} else if ($thisheader==_PATH) {
					$path_parts=explode(" <img src=images/icons/arrow_right.gif> ",$thisdatarow[$ii]);
					$npp=array();                
					foreach ($path_parts as $page) {
						$npp[]= "<span class='pathpart'>$page</span>";
					} 
					$thisdatarow[$ii] = implode(" <img src=images/icons/arrow_right.gif> ",$npp);
					echo "<td>". $thisdatarow[$ii] ."</td>";
					
				} else if ($thisheader==_INTERNAL_KEYWORD) {
					$pretty = urldecode($thisdatarow[$ii]);
					echo "<td title=\"".$pretty."\">";					
					if (strlen($pretty) > $this->displayMaxChars) {  echo substr($pretty,0,$this->displayMaxChars) . "..."; } else { echo $pretty; }
					echo "</td>";
					
				} else if ($thisheader==_KEYWORDS) {
					$pretty = urldecode($thisdatarow[$ii]);
					echo "<td title=\"".$pretty."\">";					
					echo "<a class=small title=\""._CLICK_TO_OPEN_MENU_FOR." $pretty\" onclick=\"popupMenu(event, '".urlencode($thisdatarow[$ii])."', 'keyword');\" href=\"\">";
					if (strlen($pretty) > $this->displayMaxChars) {  echo substr($pretty,0,$this->displayMaxChars) . "..."; } else { echo $pretty; }
					echo "</a></td>";
					
				} else if ($thisheader==_REFERRER) {
					if (strpos($thisdatarow[$ii], "[G]")!==FALSE) {
					 $gsyn=substr($thisdatarow[$ii],3);
					 $thisdatarow[$ii]=$gsyn;
					 $gsyn="<img src=images/google.png border=0 alt='"._VIA_GOOGLE_ADS.": $gsyn'>&nbsp;";
					 $gsyncode="[G]";
					} else {
						$gsyn="";
						$gsyncode="";
					}
					$pretty = $thisdatarow[$ii];
					if (strlen($pretty) > $this->displayMaxChars) {  $pretty = substr($pretty,0,$this->displayMaxChars) . "..."; }
					echo "<td title=\"".$thisdatarow[$ii]."\">";
					echo "<a class=small title=\""._CLICK_TO_OPEN_MENU_FOR." $thisdatarow[$ii]\" onclick=\"popupMenu(event, '".urlencode($gsyncode.$thisdatarow[$ii])."', 'referrer');\" href=\"\">$gsyn". $pretty ."</a>";
					echo "</td>";
					
				} else if ($thisheader==_USER_AGENT) { // CLICKTHROUGH HERE
					echo "<td><a class='small open_in_new_dialog quickopen' name='"._DETAILED_CRAWLER_REPORT."' type='_DETAILED_CRAWLER_REPORT' rel='CrawlerReport' href=\"reports.php?agent=".md5($thisdatarow[$ii])."&agent_string={$thisdatarow[$ii]}&from=$from&to=$to&conf={$this->profile->profilename}&submit=Report&statstable_only=1&labels=_DETAILED_CRAWLER_REPORT\">". $thisdatarow[$ii] ."</a></td>";
				} else if ($thisheader==_IP_NUMBER) {
					$ipparts = explode("##",$thisdatarow[$ii]);
					$thisdatarow[$ii] = $ipparts[0];
					$visitorid = @$ipparts[1];
					$visitorlabel = @$ipparts[2];
					echo "<td width=\"50%\"><a class=small onclick=\"popupMenu(event, '".$thisdatarow[$ii].";".$visitorid."', 'ipnumber');\" href=\"\">";
					echo $thisdatarow[$ii];
					echo "</a>";
					if(!empty($visitorlabel)){ echo " - <br>$visitorlabel\n"; }
					if ($nograph!=1) {
						if(empty($visitorlabel)){
							echo " - <br><span id=\"ip$rn\" style=\"font-size:10px;\"><font color=silver>"._RESOLVING."....</font></span>\n";
							echo "<script language=\"javascript\" type=\"text/javascript\"> StoreResolveIP('".$thisdatarow[$ii]."', 'ip$rn','".$visitorid."','".$this->profile->profilename."'); 
							</script>\n";
						}
					}
					echo "</td>";
					$lastknownip=$thisdatarow[$ii];
					
				} else if ($thisheader==_PARAMETERS) {
					$purl = $thisdatarow[($ii-1)] . " " . $thisdatarow[$ii];
					if (strlen($purl) > 10) { $purl=substr($thisdatarow[$ii],0,7) . "..."; }
					
					echo "<td title=\"".$thisdatarow[$ii]."\">";
					echo "<a class=small title=\""._CLICK_TO_OPEN_MENU."\" onclick=\"popupMenu(event, '".urlencode($thisdatarow[$ii-1]).urlencode($thisdatarow[$ii])."', 'page');\" href=\"\">". $purl ."</a>";
					echo "</td>";
					
				} else if ($thisheader==_SEARCH_RESULT_PAGE) {
					echo "<td title=\"".$thisdatarow[$ii+1]."\">";
					echo $thisdatarow[$ii];// "$page";
					echo "</td>";
					
				} else if ($thisheader==_STATUS) {
						echo "<td><a class='small open_in_new_dialog quickopen' name='".$thisdatarow[$ii]." "._ERROR_REPORT."' type='_ERROR_REPORT' rel='ErrorReport' href=\"reports.php?labels=_ERROR_REPORT&amp;status=".urlencode($thisdatarow[$ii])."&amp;from=$from&amp;to=$to&amp;conf={$this->profile->profilename}&amp;submit=Report&amp;status=".$thisdatarow[$ii]."&amp;search={$this->search}&amp;searchmode={$this->searchmode}\">". $thisdatarow[$ii] ."</a></td>";
				} else if ($thisheader==_COUNTRY) {
					if ($thisdatarow[$ii]) {
						$cparts=explode(", ", $thisdatarow[$ii]);
						
						$ccode = strtolower(((count($cparts) > 1) && ($cparts[1] > "")) ? $cparts[1] : $cparts[0]);
						$image= "<img hspace=3 width=14  height=11 src=\"images/flags/$ccode.png\" border=0 alt=\"$ccode\">";
						
						$countryname = $cnames[$cparts[0]];
						if (isset($gi) && @$lastknownip!="") {
							$area=geoip_record_by_addr($gi, $lastknownip);
							if ($area) {
								$countryname=$area->country_name .", " . $area->city;
							}
							$lastknownip="";                 
						}
					} else {
						$image = "";
						$countryname = "";
					}
					echo "<td>". $image ." <a class='small open_in_new_dialog quickopen' name='"._TOP_CITIES."' type='_TOP_CITIES' rel='TopCities' href=\"reports.php?conf={$this->profile->profilename}&amp;from=$from&amp;to=$to&amp;submit=Report&amp;labels=_TOP_CITIES&amp;statstable_only=1&amp;country=". $thisdatarow[$ii] ."\">". $countryname ."</a></td>";
					
				} else if (in_array($thisheader,$this->bchart) && ($nograph!=1)) {            
					@$val=$thisdatarow[$ii];
					if ($totals[$ii]>0){
						$perc = ($val/$totals[$ii])*100;
						$oriperc=$perc;
						
						$max = ($maxval[$ii]/$totals[$ii])*100;
						$point = 100/$max;
						$width = $point*$perc;
						$width=intval($width);
						$perc=intval($perc);
					} else {
						$perc=0;
						$oriperc=0;
						$width=0;
					}
					
					if ($graphcolor=="#EBECFB") {
						$graphcolor="#FBEBED";
					} else {
						$graphcolor="#EBECFB";
					}
					if ($labels==_DISABLE_FUNNEL_ANALYSIS) {
						//inflate low numbers
						if ($width < 20) {
							$imageleft ="";
							$imageright="";  
						} else {
							$imageleft ="<img src=images/funnelleft.gif>";
							$imageright="<img src=images/funnelright.gif>";   
						}
						
						echo "<td align=center><table cellpadding=0 cellspacing=0 border=0 class=small style=\"margin:0px;line-height:20px;\"><tr><td align=left valign=top>$imageleft</td><td width=\"$width\" class=graphborder_funnel align=center title=\"". number_format($oriperc,1) ." %\">&nbsp;".number_format($val)."&nbsp;";
						
						echo "</td><td align=right valign=top>$imageright</td></tr></table>";
					} else {
						$ori_val = $val;
						if(is_numeric($val)) {
							$val = number_format($val);
						} else {
							$val = $val;
						}
						echo "<td style='white-space:nowrap;'><div sort=\"{$ori_val}\" class='graphborder' style='width:{$width}px;min-width:".(strlen($val)*7)."px;background-color:{$graphcolor};' title='".number_format($oriperc,1)." %'><span class='graphborder' style='background-color:{$graphcolor};border-right:0;'>".$val."</span></div>";
					}
				} else {
					if (($thisheader==_PAGEVIEWS || $thisheader==_REQUESTS || $thisheader==_HITS || $thisheader==_TOTAL_HITS || $thisheader==_CRAWLED_PAGES  || $thisheader==_UNIQUE_IPS || $thisheader==_UNIQUE_IDS  || $thisheader==_BOT_REQUESTS || $thisheader==_VISITORS || $thisheader==_TOTAL_REQUESTS || $thisheader==_TOTAL_PAGES || $thisheader==_VIEWED_PAGES || $thisheader==_USERS ||$thisheader==_RECORDS)) {
					   $thisdatarow[$ii]=number_format($thisdatarow[$ii],0);
					} else if (is_int($thisdatarow[$ii])==TRUE) {
						 $thisdatarow[$ii]=number_format($thisdatarow[$ii],0);
					} else if (is_numeric($thisdatarow[$ii]) == TRUE) {
						 $thisdatarow[$ii] = $thisdatarow[$ii] * 1; // turn it into a real number, not a string
						 if (is_int($thisdatarow[$ii])== TRUE) {
							$thisdatarow[$ii]=number_format($thisdatarow[$ii],0);    
						 } else {
							$thisdatarow[$ii]=number_format($thisdatarow[$ii],2);
						 }					 
					}
					if ($thisheader==_MEGABYTES) {
						// $thisdatarow[$ii] = number_format($thisdatarow[$ii],2);
					}
					if ($thisheader==_CONVERSION || $thisheader==_BOUNCE_RATE || $thisheader==_RETENTION_PERC || $thisheader==_VISIT_SHARE) {
						 $thisdatarow[$ii]=number_format($thisdatarow[$ii],2) . "%";
					}
					if ($thisdatarow[$ii] < 0) {
						echo "<td><font color=red>". $thisdatarow[$ii] ."</font></td>";
					} else {
						echo "<td>". $thisdatarow[$ii] ."</td>";   
					}
				}
				$ii++;
			}
			echo "</tr>\n";
		}
		} else {
			echo "<tr><td colspan=8 class=small>"._NO_DATA_IN_DATE_RANGE;
			if ($mini==1) {
				echo "<br>(".date("m/d/Y",$from)." - ".date("m/d/Y",$to).")";       
			}
			echo "</td></tr>";   
			
		}

		echo "</tbody>";
		$ii=0;
		//Print the Totals
		if ($this->displayTotalRow == true && $labels!=_FUNNEL_ANALYSIS) {
			echo "<tfoot><tr class=\"tabletotalcolor\">";
			while ($ii < $i) {
				if (trim($header[$ii])==_CONVERSION_RATE) {
						$totals[$ii]=0;
				} 
				if ($totals[$ii] < 10) { $ftype="2"; } else { $ftype="0"; }
				
				echo "<td><b>";
				if ($totals[$ii] != 0) {
					// this list says which columns not to produce a total for, we should probably change this to a list for which it should produce a total!
					if ($header[$ii]==_VISITS_PER_USER || $header[$ii]==_PAGES_PER_USER) {
						$a = ColumnArray($data,$ii);
						echo number_format((array_sum($a)/count($a)),2);
						//echo " median: ".median($a);    
					} else if ($header[$ii] != _DATE && $header[$ii] != _BROWSER_VERSION && $header[$ii] != _BROWSER." version" && $header[$ii] != _OS_VERSION && $header[$ii] != _PAGES_PER_USER && $header[$ii] != _HOUR && $header[$ii] != _IP_NUMBER && urldecode($header[$ii]) != _CRAWLED_PERC && $header[$ii] != _PAGES_PER_IP && $header[$ii] != _STATUS && $header[$ii] != _CONVERSION_PERC && $header[$ii] != _CONVERSION_PERC && $header[$ii] != _BOUNCE_RATE && $header[$ii] != _RETENTION_PERC && $header[$ii] != _SEARCHES_PER_USER && $header[$ii] != _AVERAGE_DURATION_IN_MINUTES && $header[$ii] != _TIME_SPENT && $header[$ii] != _VISIT_SHARE && strip_tags($header[$ii]) != _CONVERSION && $header[$ii] != _INTERNAL_KEYWORD && $header[$ii] != _KEYWORDS) {
						echo number_format($totals[$ii], $ftype);
					}
				}
				echo "</b></td>";
				$ii++;
			}
		 echo "</tr></tfoot>\n";
		}
		echo "</table>";
		//reset nograph
		$nograph=0;
	}
	
	function UpdateStats(){
		return false;
	}	
	
	function DisplayCSV() {
        $data = $this->CreateData();
        $this->CSVStatsTable($data,$this->from,$this->to,$this->showfields,$this->label);
    }
	
	function CSVStatsTable($data,$from,$to,$showfields,$labels) {
		global $db;
		
		$filename = $this->conf."-".str_replace(" ","-",$labels).".csv";
		
		ob_start();
		header("Window-target: _blank");
		header("Content-type: application/x-download");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Content-Transfer-Encoding: binary");
		
		$nicefrom = date("D, d M Y / H:i", $this->from);
		$niceto = date("D, d M Y / H:i", $this->to);
		$header = explode(",", $this->showfields);
	  
		echo "\"".str_replace("%20", " ", $labels); 
		echo " ".strip_tags(printSegmentName()); 
		echo " "._DATE_FROM." {$nicefrom} {$niceto}\"\r\n";
		//Print the table headers
		$i=0;
		foreach($header as $thisheader) {
			echo "\"{$thisheader}\",";
			$i++;
		}
		echo "\r\n";
		
		//Print the data 
		$r = 0;
		foreach ($data as $thisdatarow) {
			//echo "we have data";
			$ii=0;
			
			while ($ii < $i) {
				if (($labels == _MOST_ACTIVE_USERS || $labels == _RECENT_VISITORS) && $header[$ii] == _IP_NUMBER) {
					$thisdatarow[$ii] = gethostbyaddr($thisdatarow[$ii])." - ".$thisdatarow[$ii];
				}
				if ($header[$ii] == _PAGE || $header[$ii] == _LANDING_PAGE) {
					$uparts = explode("##",$thisdatarow[$ii]);
					$thisdatarow[$ii] = $uparts[0];        
				}
				echo "\"". $thisdatarow[$ii] ."\",";
				$ii++;
			}
			echo "\r\n";
		}
		ob_end_flush();
	}
	
	function DisplayXML() {
        $data = $this->CreateData();
        $this->XMLStatsTable($data,$this->from,$this->to,$this->showfields,$this->label);
    }
	
	function XMLStatsTable($data,$from,$to,$showfields,$labels) {
		global $db;
		$nicefrom = date("D, d M Y / H:i",$this->from);
		$niceto = date("D, d M Y / H:i",$this->to);
		$header = explode(",",$this->showfields);
		
		iconv_set_encoding('output_encoding', 'UTF-8');
		/*echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"; */
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
		//echo "\"".str_replace("%20"," ",$labels); echo " "._DATE_FROM." $nicefrom $niceto\"\r\n";
		//Print the table headers
		
		$i=0;
		foreach($header as $thisheader) {
			//echo "\"$thisheader\",";
			$i++;
		}
		
		$r = 0;
		
		echo "\n<dataset id=\"".$labels."\" >\n";
		if (strtolower($labels)==strtolower(_TOP_CONTINENTS) || strtolower($labels)==strtolower(_TOP_COUNTRIES_CITIES) || strtolower($labels)==strtolower(/*"Top Cities Map")*/_TOP_CITIES)) {
			foreach ($data as $thisdatarow) {
				//echo "we have data";
				$ii=0;
				echo "\t<entry ";
				while ($ii < $i) {
					//echo strtolower(str_replace(" ","_",$header[$ii]))."='".$thisdatarow[$ii]."' ".strtolower(str_replace(" ","_",$header[$ii]))."Prefix"."='' ".strtolower(str_replace(" ","_",$header[$ii]))."Suffix"."='' ".strtolower(str_replace(" ","_",$header[$ii]))."Color='009900' ";
					
					if(strtolower($header[$ii]) != strtolower(_COUNTRIES)
						&& strtolower($header[$ii]) != strtolower(_CITY)
						&& strtolower($header[$ii]) != strtolower(_CONTINENT)
						&& strtolower($header[$ii]) != strtolower('longitude')
						&& strtolower($header[$ii]) != strtolower('latitude')
					) {
						echo "value".$ii."Name=\"".$header[$ii]."\" ";
						echo "value".$ii."Value='".$thisdatarow[$ii]."' ";
						echo "value".$ii."Prefix='' ";
						echo "value".$ii."Suffix='' ";
						echo "value".$ii."Color='D63C06' ";
					}
					if(strtolower($header[$ii]) == strtolower(_COUNTRIES)) {
						echo "country='".$thisdatarow[$ii]."' ";
						echo "countryPrefix='' ";
						echo "countrySuffix='' ";
					}
					if(strtolower($header[$ii]) == strtolower(_CITY)) {
						//echo "city='<![CDATA[".$thisdatarow[$ii]."]]>' ";
						echo "city=\"".$thisdatarow[$ii]."\" ";
						echo "cityPrefix='' ";
						echo "citySuffix='' ";
					}
					if(strtolower($header[$ii]) == strtolower(_CONTINENT)) {
						echo "continent=\"".$thisdatarow[$ii]."\" ";
						echo "continentPrefix='' ";
						echo "continentSuffix='' ";
					}
					if(strtolower($header[$ii]) == strtolower('longitude')) {
						echo "longitude='".$thisdatarow[$ii]."' ";
					}
					if(strtolower($header[$ii]) == strtolower('latitude')) {
						echo "latitude='".$thisdatarow[$ii]."' ";
					}
					$ii++;
					if($ii > 3) { break; }
				}
				echo "/>\n";
			}
		} else {
			foreach ($data as $thisdatarow) {
				$ii=0;
				echo "\t<entry ";
				while ($ii < $i) {
					if (($labels==_MOST_ACTIVE_USERS || $labels==_RECENT_VISITORS) && $header[$ii]==_IP_NUMBER) {
						$thisdatarow[$ii]=gethostbyaddr($thisdatarow[$ii])." - ".$thisdatarow[$ii];
					}
					if ($header[$ii]==_PAGE || $header[$ii]==_LANDING_PAGE) {
						$uparts = explode("##",$thisdatarow[$ii]);
						$thisdatarow[$ii] = $uparts[0];        
					}
					echo strtolower(str_replace(" ","_",$header[$ii]))."='".safeXML($thisdatarow[$ii])."' ";
					$ii++;
				}
				echo "/>\n";
			}
		}
		echo "</dataset>";
	}
	
	function DisplaySimpleTable() {
        $data = $this->CreateData();
        $this->SimpleStatsTable($data,$this->from,$this->to,$this->showfields,$this->label);
    }
	
	function SimpleStatsTable($data,$from,$to,$showfields,$labels) {
		
		//global $conf,$mini,$nototal,$addlabel,$nograph,$status,$agent,$help,$print,$cnames,$profile,$limit,$data;
		//global $db, $trafficsource;
		global $db;

		$nicefrom = date("D, d M Y / H:i",$this->from);
		$niceto = date("D, d M Y / H:i",$this->to);
		$header = explode(",",$this->showfields);

		echo "<div class=\"report_title\">".str_replace("%20"," ",$labels); 
		echo "<span class=\"segment_name\">".strip_tags(printSegmentName())."</span></div>\n"; 
		echo "<div class=\"date_line\">$nicefrom - $niceto</div>\r\n";
		echo "<div class=\"report_table\"><table><tr>\n";
		//Print the table headers
		$i=0;
		foreach($header as $thisheader) {
			echo "<th>$thisheader</th>";
			$i++;
		}
		echo "</tr>\r\n";
				
		//Print the data
		$r = 0;
		$rn = 0;
		$totals = array();
		while (isset($data[$rn])) {
			$ii=0;
			while ($ii < $i) {
				if(isset($totals[$ii])){
				$totals[$ii] = $totals[$ii] + $data[$rn][$ii];
				}else{
					$totals[$ii] = $data[$rn][$ii];
				}
				$ii++;
			}
			$rn++;
		}
		foreach ($data as $thisdatarow) {
			echo "<tr>";
			$ii=0;		
			while ($ii < $i) {
				if (($labels==_MOST_ACTIVE_USERS || $labels==_RECENT_VISITORS) && $header[$ii]==_IP_NUMBER) {
					$rowparts = explode("##",$thisdatarow[$ii]);
					//$thisdatarow[$ii]=gethostbyaddr($thisdatarow[$ii])." - ".$thisdatarow[$ii];
					$thisdatarow[$ii]=$rowparts[0];
					if (!empty($rowparts[3])) {
						$thisdatarow[$ii].=" - ".$rowparts[3];
					}
				}
				if (is_numeric($thisdatarow[$ii])) { 
					if (strpos($thisdatarow[$ii],".")!==false) {
						$thisdatarow[$ii] = number_format($thisdatarow[$ii],2);
					} else {
						$thisdatarow[$ii] = number_format($thisdatarow[$ii]);
					}
				}
				if ($header[$ii]==_VISIT_SHARE) {
					$thisdatarow[$ii] = number_format($thisdatarow[$ii],2) . "%";        
				}
				if ($header[$ii]==_PAGE || $header[$ii]==_LANDING_PAGE) {
					$uparts = explode("##",$thisdatarow[$ii]);
					$thisdatarow[$ii] = $uparts[0];        
				}
				echo "<td>". $thisdatarow[$ii] ."</td>";
				$ii++;
			}
			echo "</tr>\r\n";
		}
		$ii=0;
		//Print the Totals
		if ($this->displayTotalRow == true && $labels!=_FUNNEL_ANALYSIS) {
			echo "<tfoot><tr class=\"tabletotalcolor\">";
			while ($ii < $i) {
				if (trim($header[$ii])==_CONVERSION_RATE) {
						$totals[$ii]=0;
				} 
				if ($totals[$ii] < 10) { $ftype="2"; } else { $ftype="0"; }
				
				echo "<td><b>";
				if ($totals[$ii] != 0) {
					// this list says which columns not to produce a total for, we should probably change this to a list for which it should produce a total!
					if ($header[$ii]==_VISITS_PER_USER || $header[$ii]==_PAGES_PER_USER) {
						$a = ColumnArray($data,$ii);
						echo number_format((array_sum($a)/count($a)),2);
						//echo " median: ".median($a);    
					} else if ($header[$ii]!=_DATE && $header[$ii]!=_BROWSER." version" && $header[$ii]!=_PAGES_PER_USER && $header[$ii]!=_HOUR && $header[$ii]!=_IP_NUMBER  && urldecode($header[$ii])!=_CRAWLED_PERC && $header[$ii]!=_PAGES_PER_IP && $header[$ii]!=_STATUS && $header[$ii]!=_CONVERSION_PERC && $header[$ii]!=_CONVERSION_PERC && $header[$ii]!=_BOUNCE_RATE && $header[$ii]!=_RETENTION_PERC && $header[$ii]!=_SEARCHES_PER_USER && $header[$ii]!=_AVERAGE_DURATION_IN_MINUTES && $header[$ii]!=_TIME_SPENT && $header[$ii]!=_VISIT_SHARE && strip_tags($header[$ii])!=_CONVERSION && $header[$ii]!=_INTERNAL_KEYWORD && $header[$ii]!=_KEYWORDS) {
						echo number_format($totals[$ii], $ftype);
					}
				}
				echo "</b></td>";
				$ii++;
			}
			echo "</tr></tfoot>\n";
		}	
		echo "</table></div>\r\n";
	}
	
	function MakeSearchString($search,$field,$searchmode) {
		//this function turns the search field input into valid sql
		
		if (strpos($search," and ")!=FALSE) {
			$searchitems=explode(" and ", $search);
			$andor="AND";
		} else if (strpos($search," or ")!=FALSE) {
			$searchitems=explode(" or ", $search);
			$andor="OR";
		} else {
			$searchitems[0]= $search;
			$andor="AND";
		}
		$i=0;
		$searchst="and ($field $searchmode '%$searchitems[$i]%' ";
		$i++;
		while (@$searchitems[$i]!="") {
			$searchst.="$andor $field $searchmode '%$searchitems[$i]%' ";
			$i++;
		}
		$searchst.=")";
		return $searchst;    
	}

	function SearchMatchingIDs($search,$field,$searchmode,$tables) {
		global $db;
		//this function makes a subquery that returns matching id's
		
		if (strpos($search," and ")!=FALSE) {
			$searchitems=explode(" and ", $search);
			$andor="AND";
		} else if (strpos($search," or ")!=FALSE) {
			$searchitems=explode(" or ", $search);
			$andor="OR";
		} else {
			$searchitems[0]= $search;
			$andor="AND";
		} 
		$i=0;
		$searchst="($field $searchmode '%$searchitems[$i]%' ";
		$i++;
		while (@$searchitems[$i]!="") {
			$searchst.="$andor $field $searchmode '%$searchitems[$i]%' ";
			$i++;
		}
		$searchst.=")";
	   
		$searchst = "and $field IN (select id from $tables where $searchst)";
		return $searchst;    
	}
	
	// this creates an empty zerofilled array
	function newReportArray($rows,$collums) {
		$data = array();
		$r=0;
		while ($r < $rows) {
			$c=0;
			while ($c < $collums) {
				$data[$r][$c] = 0;
				$c++;
			}
			$r++;
		}
		return $data;
	}
	
	// this function takes a series based array and converts/pivots the array to columns
	function seriesToColumns($input_data, $row_key=0, $row_label="Date", $series_key=1) {
		$data = array();
		if(empty($input_data)){
			return false;
		}
		# create a column name array, the first element should be the row label
		$col_names = array();
		$col_names[]=$row_label;		
				
		# now get the column names
		foreach ($input_data as $row => $serie_data) {
			if (!in_array($serie_data[$series_key],$col_names)) {
				$col_names[] = $serie_data[$series_key];
			}
		}		
		$col_nums = array_flip($col_names);
		$cn = count($col_nums);
		
		# convert the input data
		foreach ($input_data as $row => $serie_data) {
			# use the chosen field from the serie data as the row key for the new array
			$rk = $serie_data[$row_key];
			
			# use the chosen field from the serie data as column key
			$ck = $col_nums[$serie_data[$series_key]];
			
			# loop each series array and transform to column
			foreach ($serie_data as $col_num => $val) {
				# if column is the series key, skip it
				if ($col_num == $series_key) { 
					continue; 
				}
				# add a column to the array.
				if ($col_num == $row_key) {
					$data[$rk][$row_key] = $val;
				} else {
					$data[$rk][$ck] = $val;
				}
			}			
			# fill empty data
			for ($i=0;$i<$cn;$i++) {
				if (!isset($data[$rk][$i])) { $data[$rk][$i]=0; }
			}
		}
		$result['fields'] = $col_names;
		$result['data'] = $data;
		return $result;		
	}
	
	// this function returns the number of intervals between from and now, based on the period
	function dateNumber($from, $now, $period) {
		switch ($period) {
			case _DAYS: return round(($now - $from) / 86400);
			case _WEEKS: return round(($now - $from) / (7*86400));
			case _MONTHS: return round(($now - $from) / (31*86400));
        }
	}
	
	// this function returns the date period to use 
	function getPeriod($from, $to) {
		if((($to - $from) / 86400) >= 217) {
			$period = _MONTHS;
		} elseif((($to - $from) / 86400) >= 32) {
			$period = _WEEKS;
		} else {
			$period = _DAYS;
		}
		return $period;
	}
	
	// this function retuns the number of seconds in an interval period
	function getSeconds($period) {
		switch ($period) {
			case _DAYS: return 86400;
			case _WEEKS: return (7*86400);
			case _MONTHS: return (31*86400);
        }
		return false;
	}
	
	// this function retuns the default date anotation of an interval period
	function getFormatDate($period,$timestamp) {
		switch ($period) {
			case _DAYS: return  date("D, m/d/Y",$timestamp);
			case _WEEKS: return date('o-\\WW',$timestamp);
			case _MONTHS: return date("M Y",$timestamp);
        }
		return false;
	}
	
	// This function must be use in DefineReport()
	function externDbConnectForm($savename){
		// Checking global settings if data is already stored.
		$connection = unserialize(getProfileData($this->profile->profilename,$this->profile->profilename.".externalConnection.$savename",""));
		// if  not stored setup the array but empty.
		if(empty($connection)){
			$connection["server"] = "";
			$connection["username"] = "";
			$connection["password"] = "";
			$connection["database"] = "";
			$connection["tableprefix"] = "";
		}
		// if saved store data.
		if($this->action == "save"){
			echoNotice("Saved your connection data.","margin:15px;");
			$connection["server"] = $_REQUEST["server"];
			$connection["username"] = $_REQUEST["username"];
			$connection["password"] = $_REQUEST["password"];
			$connection["database"] = $_REQUEST["database"];
			$connection["tableprefix"] = $_REQUEST["tableprefix"];
			$save = serialize($connection);
			setProfileData($this->profile->profilename,$this->profile->profilename.".externalConnection.$savename",$save);
			setProfileData($this->profile->profilename,$this->profile->profilename.".externalConnection.prefix.$savename",$connection["tableprefix"]);
		}
		$connectionForm = "<form method=POST>";
		$connectionForm .= "<table>";
		$connectionForm .= "<tr>";
		$connectionForm .= "<td><label for='server'>Server:</label></td>";
		$connectionForm .= "<td><input type='text' id='server' name='server' value='".$connection["server"]."' /></td>";
		$connectionForm .= "</tr><tr>";
		$connectionForm .= "<td><label for='server'>Username:</label></td>";
		$connectionForm .= "<td><input type='text' id='username' name='username' value='".$connection["username"]."' /></td>";
		$connectionForm .= "</tr><tr>";
		$connectionForm .= "<td><label for='server'>Password:</label></td>";
		$connectionForm .= "<td><input type='password' id='password' name='password' value='".$connection["password"]."' /></td>";
		$connectionForm .= "</tr><tr>";
		$connectionForm .= "<td><label for='server'>Database:</label></td>";
		$connectionForm .= "<td><input type='text' id='database' name='database' value='".$connection["database"]."' /></td>";
		$connectionForm .= "</tr><tr>";
		$connectionForm .= "<td><label for='tableprefix' title='Leave empty if not set'>Table Prefix:</label></td>";
		$connectionForm .= "<td><input type='text' id='tableprefix' name='tableprefix' value='".$connection["tableprefix"]."' /></td>";
		$connectionForm .= "</tr><tr>";
		$connectionForm .= "<td></td><td><input type='submit' value='Set Connection' /></td>";
		$connectionForm .= "</tr>";
		$connectionForm .= "</table>";
		$connectionForm .= "<input type='hidden' name='action' value='save' />";
		$connectionForm .= "<form>";
		echo "<div style='height:100%; float:left; width:50%;'>";
		echo "<h2>Setup connection data.</h2>";
			echo $connectionForm;
		echo "</div>";
		echo "<div style='height:100%; border-left:1px solid silver; float:left; width:40%; padding:0 10px;'>";
			echo "<h2>Testing your connection.</h2>";
			
			if($con = $this->checkExternalConnection($savename)){			
				echo "<p>Connected to server.</p>";
				echo "<p>Connected to database.</p>";
				echo "<p>You are now ready to use this connection.</p>";
				echo "<p>Please close this frame.</p>";
				mysql_close($con);
			}else{
				echo "<p>Could not connect to server or database.</p>";
				echo "<p>Please check your settings.</p>";
			}
		echo "</div>";
	}	
	function checkExternalConnection($savename){
		/// From here we are testing the connection with the given data.
		$connection = unserialize(getProfileData($this->profile->profilename,$this->profile->profilename.".externalConnection.$savename",""));
		if(empty($connection)){
			$connection["server"] = "";
			$connection["username"] = "";
			$connection["password"] = "";
			$connection["database"] = "";
		}
		if(empty($connection["server"])){
			return false;
		}
		// Check connection with server
		$con = @mysql_connect($connection["server"],$connection["username"],$connection["password"]);
		if ($con == false) {
			return false;
		}
		// Check connection with database
		$db = mysql_select_db($connection["database"], $con);
		if ($db == false) {
			return false;
		}
		return $con;
	}
	
	# This function populated the graph color array.
	# Times is the amount of times the graph color array should multiply itself.
	function PopulateColors($colors = array(), $times = 2) {
		if(empty($colors)) {
			$colors = $this->graphcolors;
		}
		
		$newcolors = array();
		for($c = 1; $c <= $times; $c++) {
			foreach($colors as $color) {
				$newcolors[] = $color;
			}
		}
		
		return $newcolors;
	}
	
	function GetHiddenLegends($legends = array()) {
		# Check which legend items need to be hidden.
		# Returns and array containing the number of the corresponding legend items.
		
		$hiddenLegends = @$this->hiddenlegends;
		if(!empty($hiddenLegends)) {
			$hiddenLegends = json_decode($hiddenLegends);
		} else {
			$hiddenLegends = array(); 
		}
		
		return $hiddenLegends;
	}
	
	# a sparkline is a mini graph
	function DisplaySparkline() {
        $data = $this->CreateData();
        $this->Sparkline($data,$this->sparktype);
    }
	
	# this function will take a default data array and convert it to something that
	# we can use to generate sparkline graphs
	function ConvertToSparklineData($data) {
		$d = array();
		$dd = array();
		foreach ($data as $key => $val) {
			$d[] = "$key: '{$val[0]}'";
			$dd[] = $val[1];
		}
		$newdata['names'] = implode(", ",$d);
		$newdata['values'] = implode(", ",$dd);
		return $newdata;
	}
	
	function Sparkline($data, $type="", $label="", $postfix="") {
		global $get_constant;
				
		if (empty($label)) {
			$label = $this->label;
		}
		$clabel = $get_constant[$this->label];
		$sourcedata = $data;
		
		if ($postfix=="") {
			$showfield = explode(",",$this->showfields);
			$showfield = $showfield[1];
			$showvalue = $data;
			$showvalue = array_pop($showvalue);
			$showvalue = $showvalue[1];
			$trend = number_format(getTrend($data,1),0);
			if ($trend > 0) {
				$trend = "<span class=\'uptrend\'>+$trend</span>";
			} else {
				$trend = "<span class=\'downtrend\'>$trend</span>";
			}
			$postfix = "$showvalue $showfield ($trend)";
		}
		
		$data = $this->ConvertToSparklineData($data);
		
		$div_id = md5($label.time());
		
		switch($type) {

			case "bar":
			
				?>
					if ($("#<?php echo $clabel; ?>").length == 0){
						$('<div id ="<?php echo $clabel; ?>"></div>').appendTo(".inpage_container");
					}
					$('<div class="spark_container"><span><?php echo $label; ?>: <span id="<?php echo $div_id; ?>"><?php echo $data["values"]; ?></span></span></div>').appendTo("#<?php echo $clabel; ?>");
					$(function() {
						$("#<?php echo $div_id;?>").sparkline('html', {
							type: 'bar',
							tooltipFormat: '<span style="color: {{color}}">&#9679;</span> {{offset:names}}  ({{value}})',
							tooltipValueLookups: { names: { <?php echo $data['names'];?> } }							
						});
					});
					
				<?php
				break;
				
			case "pie":
			
				?>
					if ($("#<?php echo $clabel; ?>").length == 0){
						$('<div id ="<?php echo $clabel; ?>"></div>').appendTo(".inpage_container");
					}
					$('<div class="spark_container"><span><?php echo $label; ?>: <span id="<?php echo $div_id; ?>"><?php echo $data["values"]; ?></span> <?php echo $postfix; ?></span></div>').appendTo("#<?php echo $clabel; ?>");
					$(function() {
						$("#<?php echo $div_id;?>").sparkline('html', {
							type: 'pie',
							tooltipFormat: '<span style="color: {{color}}">&#9679;</span> {{offset:names}} ({{percent.1}}%)',
							tooltipValueLookups: { names: { <?php echo $data['names'];?> } }														
						});
					});					
				<?php
				break;
				
			default:
				?>
					if ($("#<?php echo $clabel; ?>").length == 0){
						$('<div id ="<?php echo $clabel; ?>"></div>').appendTo(".inpage_container");
					}
					$('<div class="spark_container"><span><?php echo $label; ?>: <span id="<?php echo $div_id; ?>"><?php echo $data["values"]; ?></span> <?php echo $postfix; ?></span></div>').appendTo("#<?php echo $clabel; ?>");
					$(function() {
						$("#<?php echo $div_id;?>").sparkline('html', {
							tooltipFormat: '<span style="color: {{color}}">&#9679;</span> {{offset:names}}  ({{y}})',
							tooltipValueLookups: { names: { <?php echo $data['names'];?> } }							
						});
					});
					

				<?php
				break;		
		}
		return $sourcedata;
	}
}
if (file_exists("includes/adwords_core.php")) {
	include_once("includes/adwords_core.php");
}
include_once("includes/twitter_core.php");

$ignore_report_files = array("test_center.php");

# THIS IS FOR TESTING THE REPORT STORE ... KILLS ALL THE REPORTS FROM THE REPORTS FOLDER! BEWARE!
if(isset($_GET["kill_reports"])){
	$delete_reports = getReportArray();
	foreach($delete_reports as $removal){	
		if ($removal["Distribution"]!="Standard") {
			if (file_exists("reports/".$removal["Filename"].".php")) {
				unlink("reports/".$removal["Filename"].".php");
			}
		}
	}
	unset($delete_reports);
}

# This is as good a place as any to include all the report child classes from the reports dir
$report_dir = opendir(logaholic_dir() . "reports/");
while ($file = readdir($report_dir)) {
    if (strpos($file,".php")!==false) {
		if(in_array($file,$ignore_report_files) == false){
			include_once "reports/" . $file;
		}
    }
}
closedir($report_dir);

if (isset($profile)) {
	$downloaded_reports = getProfileData($profile->profilename, "{$profile->profilename}.downloaded_reports", "");
	$is_download = array();
	if(!empty($downloaded_reports)) {
		$downloaded_reports = unserialize($downloaded_reports);
		
		foreach($downloaded_reports as $downloaded_report) {
			if (!file_exists("reports/".$downloaded_report.".php")) {
				$dld_report_data = unserialize(getProfileData($profile->profilename, "{$profile->profilename}.report.{$downloaded_report}"));
				if(!empty($dld_report_data['expires_after'])) {
					if($dld_report_data['installDate'] + $dld_report_data['expires_after'] > time()) {
						$not_expired = true;
					} else {
						$not_expired = false;
					}
				} else {
					$not_expired = true;
				}
				if($not_expired == true) {
					$dl_report = base64_decode($dld_report_data['code']);
					$dl_report = "?>".$dl_report;
					//var_dump($dld_report_data);
					// echo "<pre>".$dl_report."</pre>";
					eval($dl_report);
					$is_download[$dld_report_data['classname']] = $downloaded_report;
				}
			}
		}
	}
}

// sort out the reports
$reports = sort_reports($reports);

#Now we need to make a label/constant matcher, so can always get the constant name
$get_constant = array();
foreach ($reports as $key => $value) {
    if (defined($key)) {
        $get_constant[constant($key)] = $key;
		$get_label[$value['ClassName']] = $key;
    }
}

?>
