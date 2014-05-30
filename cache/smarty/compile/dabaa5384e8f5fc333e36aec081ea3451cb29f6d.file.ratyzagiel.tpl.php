<?php /* Smarty version Smarty-3.1.8, created on 2012-11-15 15:00:56
         compiled from "/home/bodo/public_html/dev/modules/ratyzagiel/ratyzagiel.tpl" */ ?>
<?php /*%%SmartyHeaderCode:82332608350a4f5982fac10-60467385%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dabaa5384e8f5fc333e36aec081ea3451cb29f6d' => 
    array (
      0 => '/home/bodo/public_html/dev/modules/ratyzagiel/ratyzagiel.tpl',
      1 => 1311432430,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '82332608350a4f5982fac10-60467385',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'total_order' => 0,
    'module_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_50a4f59834ed69_94897831',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a4f59834ed69_94897831')) {function content_50a4f59834ed69_94897831($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['total_order']->value>=100){?>
<p class="payment_module">
		<a href="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
ratyzagiel-payment.php" title="<?php echo smartyTranslate(array('s'=>'Kup na raty za pomocą systemu ratalnego Żagiel S.A.','mod'=>'ratyzagiel'),$_smarty_tpl);?>
">
		<img src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
images/logo.gif" width="86px" height="59px" alt="<?php echo smartyTranslate(array('s'=>'Kup na raty za pomocą systemu ratalnego Żagiel S.A.','mod'=>'ratyzagiel'),$_smarty_tpl);?>
" />
		<?php echo smartyTranslate(array('s'=>'Kup na raty za pomocą systemu ratalnego Żagiel S.A.','mod'=>'ratyzagiel'),$_smarty_tpl);?>
</a>
</p>
<?php }?><?php }} ?>