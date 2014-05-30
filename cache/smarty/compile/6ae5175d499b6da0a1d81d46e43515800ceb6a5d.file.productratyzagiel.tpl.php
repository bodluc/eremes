<?php /* Smarty version Smarty-3.1.8, created on 2012-11-15 13:05:01
         compiled from "/home/bodo/public_html/dev/modules/ratyzagiel/productratyzagiel.tpl" */ ?>
<?php /*%%SmartyHeaderCode:64632238550a4da6d1a13f8-05738404%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6ae5175d499b6da0a1d81d46e43515800ceb6a5d' => 
    array (
      0 => '/home/bodo/public_html/dev/modules/ratyzagiel/productratyzagiel.tpl',
      1 => 1351216328,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '64632238550a4da6d1a13f8-05738404',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'base_dir_ssl' => 0,
    'price_for_sym' => 0,
    'shop_id' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_50a4da6d1bd787_42877807',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a4da6d1bd787_42877807')) {function content_50a4da6d1bd787_42877807($_smarty_tpl) {?><div class="zagiel" style="margin-top:20px">
<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['base_dir_ssl']->value;?>
modules/ratyzagiel/js/zagiel.js"></script> 
<p style="text-align: right; padding-top: 20px;">
<a  href="javascript:PoliczRate(<?php echo $_smarty_tpl->tpl_vars['price_for_sym']->value;?>
,1,<?php echo $_smarty_tpl->tpl_vars['shop_id']->value;?>
);" style="cursor:pointer">
    <img src="<?php echo $_smarty_tpl->tpl_vars['base_dir_ssl']->value;?>
modules/ratyzagiel/images/symulator.gif" alt="Oblicz ratę!"/>
</a> 
</p>
<p style="text-align: right; padding-top: 5px;">

<a rel="nofollow" class="raty_link" href="https://www.zagiel.com.pl/kalkulator/jak_kupic.html" >
<img src="<?php echo $_smarty_tpl->tpl_vars['base_dir_ssl']->value;?>
modules/ratyzagiel/images/jakkupic.gif" alt="Jak kupić na raty"  style="cursor: pointer;" border="0">
</a>
</p>
</div>

<?php }} ?>