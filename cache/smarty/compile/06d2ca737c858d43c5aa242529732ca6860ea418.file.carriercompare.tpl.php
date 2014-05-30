<?php /* Smarty version Smarty-3.1.8, created on 2013-01-21 01:33:40
         compiled from "/home/bodo/public_html/dev/modules/carriercompare/template/carriercompare.tpl" */ ?>
<?php /*%%SmartyHeaderCode:66603863950fc8ce4cdf354-15774891%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '06d2ca737c858d43c5aa242529732ca6860ea418' => 
    array (
      0 => '/home/bodo/public_html/dev/modules/carriercompare/template/carriercompare.tpl',
      1 => 1347489896,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '66603863950fc8ce4cdf354-15774891',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'opc' => 0,
    'use_taxes' => 0,
    'priceDisplay' => 0,
    'currencySign' => 0,
    'currencyRate' => 0,
    'currencyFormat' => 0,
    'currencyBlank' => 0,
    'id_carrier' => 0,
    'id_state' => 0,
    'refresh_method' => 0,
    'countries' => 0,
    'country' => 0,
    'id_country' => 0,
    'zipcode' => 0,
    'new_base_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_50fc8ce4e274f2_60214093',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50fc8ce4e274f2_60214093')) {function content_50fc8ce4e274f2_60214093($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/bodo/public_html/dev/tools/smarty/plugins/modifier.escape.php';
?>

<?php if (!$_smarty_tpl->tpl_vars['opc']->value){?>
<script type="text/javascript">
	// <![CDATA[
	var taxEnabled = "<?php echo $_smarty_tpl->tpl_vars['use_taxes']->value;?>
";
	var displayPrice = "<?php echo $_smarty_tpl->tpl_vars['priceDisplay']->value;?>
";
	var currencySign = '<?php echo html_entity_decode($_smarty_tpl->tpl_vars['currencySign']->value,2,"UTF-8");?>
';
	var currencyRate = '<?php echo floatval($_smarty_tpl->tpl_vars['currencyRate']->value);?>
';
	var currencyFormat = '<?php echo intval($_smarty_tpl->tpl_vars['currencyFormat']->value);?>
';
	var currencyBlank = '<?php echo intval($_smarty_tpl->tpl_vars['currencyBlank']->value);?>
';
	var id_carrier = '<?php echo intval($_smarty_tpl->tpl_vars['id_carrier']->value);?>
';
	var id_state = '<?php echo intval($_smarty_tpl->tpl_vars['id_state']->value);?>
';
	var SE_RedirectTS = "<?php echo smartyTranslate(array('s'=>'Refreshing page and updating cart…','mod'=>'carriercompare'),$_smarty_tpl);?>
";
	var SE_RefreshStateTS = "<?php echo smartyTranslate(array('s'=>'Checking available states…','mod'=>'carriercompare'),$_smarty_tpl);?>
";
	var SE_RetrievingInfoTS = "<?php echo smartyTranslate(array('s'=>'Retrieving information…','mod'=>'carriercompare'),$_smarty_tpl);?>
";
	var SE_RefreshMethod = <?php echo $_smarty_tpl->tpl_vars['refresh_method']->value;?>
;

	var txtFree = "<?php echo smartyTranslate(array('s'=>'Free!','mod'=>'carriercompare'),$_smarty_tpl);?>
";
	
	PS_SE_HandleEvent();
	//]]>
</script>

<form class="std" id="compare_shipping_form" method="post" action="#" >
	<fieldset id="compare_shipping">
		<h3><?php echo smartyTranslate(array('s'=>'Estimate your shipping & taxes','mod'=>'carriercompare'),$_smarty_tpl);?>
</h3>
		
		<p id="carriercompare_errors" style="display: none;">
			<ul id="carriercompare_errors_list">
				
			</ul>
		</p>
		
		<p>
			<label for="id_country"><?php echo smartyTranslate(array('s'=>'Country','mod'=>'carriercompare'),$_smarty_tpl);?>
</label>
			<select name="id_country" id="id_country">
				<?php  $_smarty_tpl->tpl_vars['country'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['country']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['countries']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['country']->key => $_smarty_tpl->tpl_vars['country']->value){
$_smarty_tpl->tpl_vars['country']->_loop = true;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['country']->value['id_country'];?>
" <?php if ($_smarty_tpl->tpl_vars['id_country']->value==$_smarty_tpl->tpl_vars['country']->value['id_country']){?>selected="selected"<?php }?>><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['country']->value['name'], 'htmlall', 'UTF-8');?>
</option>
				<?php } ?>
			</select>
		</p>
		
		<p id="states" style="display: none;">
			<label for="id_state"><?php echo smartyTranslate(array('s'=>'State','mod'=>'carriercompare'),$_smarty_tpl);?>
</label>
			<select name="id_state" id="id_state">
				
			</select>
		</p>
		
		<p>
			<label for="zipcode"><?php echo smartyTranslate(array('s'=>'Zipcode','mod'=>'carriercompare'),$_smarty_tpl);?>
</label>
			<input type="text" name="zipcode" id="zipcode" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['zipcode']->value, 'htmlall', 'UTF-8');?>
"/> (<?php echo smartyTranslate(array('s'=>'Needed for certain carriers','mod'=>'carriercompare'),$_smarty_tpl);?>
)
		</p>
		
		<div id="SE_AjaxDisplay">
			<img src="<?php echo $_smarty_tpl->tpl_vars['new_base_dir']->value;?>
loader.gif" /><br />
			<p></p>
		</div>
		
		<div id="availableCarriers" style="display: none;">
			<table cellspacing="0" cellpadding="0" id="availableCarriers_table" class="std">
				<thead>
					<tr>
						<th class="carrier_action first_item"></th>
						<th class="carrier_name item"><?php echo smartyTranslate(array('s'=>'Carrier','mod'=>'carriercompare'),$_smarty_tpl);?>
</th>
						<th class="carrier_infos item"><?php echo smartyTranslate(array('s'=>'Information','mod'=>'carriercompare'),$_smarty_tpl);?>
</th>
						<th class="carrier_price last_item"><?php echo smartyTranslate(array('s'=>'Price','mod'=>'carriercompare'),$_smarty_tpl);?>
</th>
					</tr>
				</thead>
				<tbody id="carriers_list">
					
				</tbody>
			</table>
		</div>
		<p class="warning center" id="noCarrier" style="display: none;"><?php echo smartyTranslate(array('s'=>'No carrier is available for this selection','mod'=>'carriercompare'),$_smarty_tpl);?>
</p>
		
		<p class="SE_SubmitRefreshCard">
			<input class="exclusive_large" id="carriercompare_submit" type="submit" name="carriercompare_submit" value="<?php echo smartyTranslate(array('s'=>'Update Cart','mod'=>'carriercompare'),$_smarty_tpl);?>
"/>
			<input id="update_carriers_list" type="button" class="exclusive_large" value="<?php echo smartyTranslate(array('s'=>'Update carrier list'),$_smarty_tpl);?>
" />
		</p>
	</fieldset>
</form>
<?php }?>
<?php }} ?>