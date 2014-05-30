<?php /* Smarty version Smarty-3.1.8, created on 2013-02-09 00:09:01
         compiled from "/home/bodo/public_html/dev/admin-dev/themes/default/template/helpers/list/list_action_edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7656754395115858d896ec0-43042501%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ba817de72a1ccf4892e8dbe70d08023bbe6affd3' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/helpers/list/list_action_edit.tpl',
      1 => 1347489972,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7656754395115858d896ec0-43042501',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'href' => 0,
    'action' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5115858d8a3c56_95690048',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5115858d8a3c56_95690048')) {function content_5115858d8a3c56_95690048($_smarty_tpl) {?>
<a href="<?php echo $_smarty_tpl->tpl_vars['href']->value;?>
" class="edit" title="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
">
	<img src="../img/admin/edit.gif" alt="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
" />
</a><?php }} ?>