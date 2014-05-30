<?php /* Smarty version Smarty-3.1.8, created on 2012-11-15 13:05:41
         compiled from "/home/bodo/public_html/dev/modules/ratyzagiel/blockratyzagiel.tpl" */ ?>
<?php /*%%SmartyHeaderCode:91865391750a4da95eb9e53-12407813%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ce3721ad3fd20bb763b0088c42497885a0e047a1' => 
    array (
      0 => '/home/bodo/public_html/dev/modules/ratyzagiel/blockratyzagiel.tpl',
      1 => 1351216776,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '91865391750a4da95eb9e53-12407813',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'base_dir_ssl' => 0,
    'raty_block_title' => 0,
    'raty_logo' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_50a4da95ecec59_69568903',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a4da95ecec59_69568903')) {function content_50a4da95ecec59_69568903($_smarty_tpl) {?><script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['base_dir_ssl']->value;?>
modules/ratyzagiel/js/zagiel.js"></script> 
 <div class="block">
	<h4><?php echo $_smarty_tpl->tpl_vars['raty_block_title']->value;?>
</h4>
	<div class="block_content">
        <p style="text-align:center">
        <a class="raty_link" title="Zobacz jak kupić na raty" href="https://www.zagiel.com.pl/kalkulator/jak_kupic.html" style="cursor: pointer;">
            <img src="<?php echo $_smarty_tpl->tpl_vars['base_dir_ssl']->value;?>
modules/ratyzagiel/logos/<?php echo $_smarty_tpl->tpl_vars['raty_logo']->value;?>
.png" width="139" alt="Zobacz jak kupić na raty" />
        </a>
        </p>
    </div>
</div>
    <?php }} ?>