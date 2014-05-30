<?php /* Smarty version Smarty-3.1.8, created on 2013-01-21 01:32:39
         compiled from "/home/bodo/public_html/dev/modules/carriercompare/template/configuration.tpl" */ ?>
<?php /*%%SmartyHeaderCode:100341602250fc8ca759ec07-03331191%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '27bceeef806b352c8131d507ab6f1a7e0a7d3a22' => 
    array (
      0 => '/home/bodo/public_html/dev/modules/carriercompare/template/configuration.tpl',
      1 => 1347489896,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '100341602250fc8ca759ec07-03331191',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'display_error' => 0,
    'refresh_method' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_50fc8ca79966c7_50332617',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50fc8ca79966c7_50332617')) {function content_50fc8ca79966c7_50332617($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/bodo/public_html/dev/tools/smarty/plugins/modifier.escape.php';
?><?php if (isset($_smarty_tpl->tpl_vars['display_error']->value)){?>
	<?php if ($_smarty_tpl->tpl_vars['display_error']->value){?>
		<div class="error"><?php echo smartyTranslate(array('s'=>'An error occured during the form validation'),$_smarty_tpl);?>
</div>
	<?php }else{ ?>
		<div class="conf"><?php echo smartyTranslate(array('s'=>'Configuration updated'),$_smarty_tpl);?>
</div>
	<?php }?>
<?php }?>

<form method="post" action="<?php echo smarty_modifier_escape($_SERVER['REQUEST_URI'], 'htmlall', 'UTF-8');?>
">
	<fieldset>
		<div class="warn"><?php echo smartyTranslate(array('s'=>'This module is only available on standard order process because on One Page Checkout the carrier list is already available'),$_smarty_tpl);?>
.</div>
		<legend><?php echo smartyTranslate(array('s'=>'Global Configuration'),$_smarty_tpl);?>
</legend>
		
		<label for="refresh_method">Refresh carrier list method</label>
		<div class="margin-form">
			<select id="refresh_method" name="refresh_method">
				<option value="0" <?php if ($_smarty_tpl->tpl_vars['refresh_method']->value==0){?>selected<?php }?>><?php echo smartyTranslate(array('s'=>'Anytime'),$_smarty_tpl);?>
</option>
				<option value="1" <?php if ($_smarty_tpl->tpl_vars['refresh_method']->value==1){?>selected<?php }?>><?php echo smartyTranslate(array('s'=>'Required all information set'),$_smarty_tpl);?>
</option>
			</select>
			<p><?php echo smartyTranslate(array('s'=>'Set the way to refresh information for a customer'),$_smarty_tpl);?>
</p>
		</div>
		
		<div class="margin-form">
			<input name="setGlobalConfiguration" type="submit" class="button" value="<?php echo smartyTranslate(array('s'=>'Submit'),$_smarty_tpl);?>
">
		</div>
	</fieldset>
</form><?php }} ?>