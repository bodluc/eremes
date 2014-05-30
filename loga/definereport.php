<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include_once("common.inc.php");

echo "<script type='text/javascript'> var conf_name = \"{$conf}\"; var from_date; var to_date; </script>";
// include_once("templates/template.php");
// include_once("templates/template_v3.php");
// $interface = new Template_v3();
echo $template->HTMLheadTag();

$labels = $_REQUEST['labels'];
?>
<script language="javascript" type="text/javascript">
// Define our global variables.
    var conf_name="<?php echo @$conf; ?>";
    var from_date=<?php echo @$from; ?>;
    var to_date=<?php echo @$to; ?>;
</script>
<?php
if(defined($labels)) {
	$clabel = $labels;
	$labels = constant($labels);
} else {
	$clabel = $get_constant[$labels];
}

if (isset($clabel) && isset($reports[$clabel])) {
	# there is a class file for this report, let's use it
	$r = new $reports[$clabel]["ClassName"]();
	$r->DefineReport();
}
?>