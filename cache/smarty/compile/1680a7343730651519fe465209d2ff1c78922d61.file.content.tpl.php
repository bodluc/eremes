<?php /* Smarty version Smarty-3.1.8, created on 2013-01-07 22:35:24
         compiled from "/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/cms_content/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:175108251950eb3f9c675439-70513143%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1680a7343730651519fe465209d2ff1c78922d61' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/cms_content/content.tpl',
      1 => 1347489972,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '175108251950eb3f9c675439-70513143',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'cms_breadcrumb' => 0,
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_50eb3f9ca07259_59984016',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50eb3f9ca07259_59984016')) {function content_50eb3f9ca07259_59984016($_smarty_tpl) {?>
<?php if (isset($_smarty_tpl->tpl_vars['cms_breadcrumb']->value)){?>
	<div class="cat_bar">
		<span style="color: #3C8534;"><?php echo smartyTranslate(array('s'=>'Current category'),$_smarty_tpl);?>
 :</span>&nbsp;&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['cms_breadcrumb']->value;?>

	</div>
<?php }?>

<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

<?php }} ?>