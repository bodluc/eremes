<?php /* Smarty version Smarty-3.1.8, created on 2013-02-09 14:37:02
         compiled from "/home/bodo/public_html/dev/modules/feeder/feederHeader.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1982441240511650fe4a2620-75239131%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '326c67b595602120120ff9b2ed6a7b573ca8495b' => 
    array (
      0 => '/home/bodo/public_html/dev/modules/feeder/feederHeader.tpl',
      1 => 1347489884,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1982441240511650fe4a2620-75239131',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'meta_title' => 0,
    'feedUrl' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_511650fe4dbef7_37522434',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_511650fe4dbef7_37522434')) {function content_511650fe4dbef7_37522434($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/bodo/public_html/dev/tools/smarty/plugins/modifier.escape.php';
?>

<link rel="alternate" type="application/rss+xml" title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['meta_title']->value, 'html', 'UTF-8');?>
" href="<?php echo $_smarty_tpl->tpl_vars['feedUrl']->value;?>
" /><?php }} ?>