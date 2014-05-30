<?php /* Smarty version 2.6.26, created on 2013-02-05 11:37:33
         compiled from /home/bodo/public_html/dev/piwik/plugins/UserCountry/templates/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'postEvent', '/home/bodo/public_html/dev/piwik/plugins/UserCountry/templates/index.tpl', 1, false),array('function', 'sparkline', '/home/bodo/public_html/dev/piwik/plugins/UserCountry/templates/index.tpl', 12, false),array('modifier', 'translate', '/home/bodo/public_html/dev/piwik/plugins/UserCountry/templates/index.tpl', 5, false),)), $this); ?>
<?php echo smarty_function_postEvent(array('name' => 'template_headerUserCountry'), $this);?>


<div id="leftcolumn">

<h2><?php echo ((is_array($_tmp='UserCountry_Country')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
<?php echo $this->_tpl_vars['dataTableCountry']; ?>


<h2><?php echo ((is_array($_tmp='UserCountry_Continent')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
<?php echo $this->_tpl_vars['dataTableContinent']; ?>


<div class="sparkline">
	<?php echo smarty_function_sparkline(array('src' => $this->_tpl_vars['urlSparklineCountries']), $this);?>

	<?php echo ((is_array($_tmp='UserCountry_DistinctCountries')) ? $this->_run_mod_handler('translate', true, $_tmp, "<strong>".($this->_tpl_vars['numberDistinctCountries'])."</strong>") : smarty_modifier_translate($_tmp, "<strong>".($this->_tpl_vars['numberDistinctCountries'])."</strong>")); ?>

</div>	

</div>

<div id="rightcolumn">

<h2><?php echo ((is_array($_tmp='UserCountry_Region')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
<?php echo $this->_tpl_vars['dataTableRegion']; ?>


<h2><?php echo ((is_array($_tmp='UserCountry_City')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
<?php echo $this->_tpl_vars['dataTableCity']; ?>


</div>

<?php echo smarty_function_postEvent(array('name' => 'template_footerUserCountry'), $this);?>
