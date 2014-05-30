<?php /* Smarty version Smarty-3.1.8, created on 2013-02-09 00:09:39
         compiled from "/home/bodo/public_html/dev/themes/default/category-count.tpl" */ ?>
<?php /*%%SmartyHeaderCode:587820979511585b3dc48d8-84330287%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bddd2dd62b6de646253174efd8e7d7643def2510' => 
    array (
      0 => '/home/bodo/public_html/dev/themes/default/category-count.tpl',
      1 => 1359415848,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '587820979511585b3dc48d8-84330287',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'category' => 0,
    'nb_products' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_511585b3e15a87_26256314',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_511585b3e15a87_26256314')) {function content_511585b3e15a87_26256314($_smarty_tpl) {?>
<?php if ($_smarty_tpl->tpl_vars['category']->value->id==1||$_smarty_tpl->tpl_vars['nb_products']->value==0){?>
<div class="resumecat category-product-count">
	<?php echo smartyTranslate(array('s'=>'There are no products.'),$_smarty_tpl);?>

    </div>
<?php }?><?php }} ?>