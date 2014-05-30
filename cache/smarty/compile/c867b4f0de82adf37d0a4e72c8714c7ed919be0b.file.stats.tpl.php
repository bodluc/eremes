<?php /* Smarty version Smarty-3.1.8, created on 2012-12-13 00:28:41
         compiled from "/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/stats/stats.tpl" */ ?>
<?php /*%%SmartyHeaderCode:48237008450c913294e0ee3-36078297%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c867b4f0de82adf37d0a4e72c8714c7ed919be0b' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/stats/stats.tpl',
      1 => 1347489974,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '48237008450c913294e0ee3-36078297',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'module_name' => 0,
    'module_instance' => 0,
    'hook' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_50c9132952cec8_60801751',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50c9132952cec8_60801751')) {function content_50c9132952cec8_60801751($_smarty_tpl) {?>

<div>
	<?php if ($_smarty_tpl->tpl_vars['module_name']->value){?>
		<?php if ($_smarty_tpl->tpl_vars['module_instance']->value&&$_smarty_tpl->tpl_vars['module_instance']->value->active){?>
			<?php echo $_smarty_tpl->tpl_vars['hook']->value;?>

		<?php }else{ ?>
			<?php echo smartyTranslate(array('s'=>'Module not found'),$_smarty_tpl);?>

		<?php }?>
	<?php }else{ ?>
		<h3 class="space"><?php echo smartyTranslate(array('s'=>'Please select a module in the left column.'),$_smarty_tpl);?>
</h3>
	<?php }?>
</div>
</div>
</div>


<?php }} ?>