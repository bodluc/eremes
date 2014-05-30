<?php /* Smarty version Smarty-3.1.8, created on 2013-02-09 14:37:02
         compiled from "/home/bodo/public_html/dev/modules/blockpermanentlinks/blockpermanentlinks-header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:644669525511650fe5fd7b8-11667565%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dff4561412cd5b66b9f36d9740e74cd58d02422e' => 
    array (
      0 => '/home/bodo/public_html/dev/modules/blockpermanentlinks/blockpermanentlinks-header.tpl',
      1 => 1355575342,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '644669525511650fe5fd7b8-11667565',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_511650fe62f669_21178184',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_511650fe62f669_21178184')) {function content_511650fe62f669_21178184($_smarty_tpl) {?>

<!-- Block permanent links module HEADER -->
<ul id="header_links">
	<li id="header_kom"><b>Infolinia</b> <span class="kom">518 787 600</span> </li>
    <li id="header_link_contact"><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('contact',true);?>
" title="<?php echo smartyTranslate(array('s'=>'contact','mod'=>'blockpermanentlinks'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Status Zamówienia','mod'=>'blockpermanentlinks'),$_smarty_tpl);?>
</a></li>
    <li id="header_link_contact"><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('contact',true);?>
" title="<?php echo smartyTranslate(array('s'=>'contact','mod'=>'blockpermanentlinks'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'contact','mod'=>'blockpermanentlinks'),$_smarty_tpl);?>
</a></li>
	<li id="header_link_sitemap"><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('sitemap');?>
" title="<?php echo smartyTranslate(array('s'=>'sitemap','mod'=>'blockpermanentlinks'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'sitemap','mod'=>'blockpermanentlinks'),$_smarty_tpl);?>
</a></li>
    
</ul>
<!-- /Block permanent links module HEADER -->
<?php }} ?>