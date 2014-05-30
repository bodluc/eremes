<?php /* Smarty version Smarty-3.1.8, created on 2013-02-09 00:08:04
         compiled from "/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/products/informations.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6367003145115855473e722-10722406%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '35a70c360b8978f854beef64fba2ea461dd8b6ef' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/products/informations.tpl',
      1 => 1350413597,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6367003145115855473e722-10722406',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'check_product_association_ajax' => 0,
    'combinationImagesJs' => 0,
    'link' => 0,
    'id_lang' => 0,
    'display_common_field' => 0,
    'bullet_common_field' => 0,
    'product_type' => 0,
    'languages' => 0,
    'language' => 0,
    'class_input_ajax' => 0,
    'product' => 0,
    'display_multishop_checkboxes' => 0,
    'PS_PRODUCT_SHORT_DESC_LIMIT' => 0,
    'images' => 0,
    'key' => 0,
    'image' => 0,
    'imagesTypes' => 0,
    'type' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_51158554b89920_33854723',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51158554b89920_33854723')) {function content_51158554b89920_33854723($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/bodo/public_html/dev/tools/smarty/plugins/modifier.escape.php';
?>
<?php if ($_smarty_tpl->tpl_vars['check_product_association_ajax']->value){?>
<?php $_smarty_tpl->tpl_vars['class_input_ajax'] = new Smarty_variable('check_product_name ', null, 0);?>
<?php }else{ ?>
<?php $_smarty_tpl->tpl_vars['class_input_ajax'] = new Smarty_variable('', null, 0);?>
<?php }?>
<input type="hidden" name="submitted_tabs[]" value="Informations" />
<div id="step1">
	<h4 class="tab">1. <?php echo smartyTranslate(array('s'=>'Info.'),$_smarty_tpl);?>
</h4>
	<h4><?php echo smartyTranslate(array('s'=>'Product global information'),$_smarty_tpl);?>
</h4>
	<script type="text/javascript">
		<?php echo $_smarty_tpl->tpl_vars['combinationImagesJs']->value;?>

		<?php if ($_smarty_tpl->tpl_vars['check_product_association_ajax']->value){?>
				var search_term = '';
				$('document').ready( function() {
					$(".check_product_name")
						.autocomplete(
							'<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminProducts',true);?>
', {
								minChars: 3,
								max: 10,
								width: $(".check_product_name").width(),
								selectFirst: false,
								scroll: false,
								dataType: "json",
								formatItem: function(data, i, max, value, term) {
									search_term = term;
									// adding the little
									if ($('.ac_results').find('.separation').length == 0)
										$('.ac_results').css('background-color', '#EFEFEF')
											.prepend('<div style="color:#585A69; padding:2px 5px"><?php echo smartyTranslate(array('s'=>'Use a product from the list'),$_smarty_tpl);?>
<div class="separation"></div></div>');
									return value;
								},
								parse: function(data) {
									var mytab = new Array();
									for (var i = 0; i < data.length; i++)
										mytab[mytab.length] = { data: data[i], value: data[i].name };
									return mytab;
								},
								extraParams: {
									ajax: 1,
									action: 'checkProductName',
									id_lang: <?php echo $_smarty_tpl->tpl_vars['id_lang']->value;?>

								}
							}
						)
						.result(function(event, data, formatted) {
							// keep the searched term in the input
							$('#name_<?php echo $_smarty_tpl->tpl_vars['id_lang']->value;?>
').val(search_term);
							jConfirm('<?php echo smartyTranslate(array('s'=>'Do you want to use this product?'),$_smarty_tpl);?>
&nbsp;<strong>'+data.name+'</strong>', '<?php echo smartyTranslate(array('s'=>'Confirmation'),$_smarty_tpl);?>
', function(confirm){
								if (confirm == true)
									document.location.href = '<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminProducts',true);?>
&updateproduct&id_product='+data.id_product;
								else
									return false;
							});
						});
				});
		<?php }?>
	</script>

	<?php if (isset($_smarty_tpl->tpl_vars['display_common_field']->value)&&$_smarty_tpl->tpl_vars['display_common_field']->value){?>
		<div class="warn" style="display: block"><?php echo smartyTranslate(array('s'=>'Warning, if you change the value of fields with an orange bullet %s, the value will be changed for all other shops for this product','sprintf'=>$_smarty_tpl->tpl_vars['bullet_common_field']->value),$_smarty_tpl);?>
</div>
	<?php }?>

	<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/check_fields.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('product_tab'=>"Informations"), 0);?>


	<div class="separation"></div>
	<div id="warn_virtual_combinations" class="warn" style="display:none"><?php echo smartyTranslate(array('s'=>'You cannot use combinations with a virtual product.'),$_smarty_tpl);?>
</div>
	<div>
		<label class="text"><?php echo $_smarty_tpl->tpl_vars['bullet_common_field']->value;?>
 <?php echo smartyTranslate(array('s'=>'Type:'),$_smarty_tpl);?>
</label>
		<input type="radio" name="type_product" id="simple_product" value="<?php echo Product::PTYPE_SIMPLE;?>
" <?php if ($_smarty_tpl->tpl_vars['product_type']->value==Product::PTYPE_SIMPLE){?>checked="checked"<?php }?> />
		<label class="radioCheck" for="simple_product"><?php echo smartyTranslate(array('s'=>'Product'),$_smarty_tpl);?>
</label>
		<input type="radio" name="type_product" id="pack_product" value="<?php echo Product::PTYPE_PACK;?>
" <?php if ($_smarty_tpl->tpl_vars['product_type']->value==Product::PTYPE_PACK){?>checked="checked"<?php }?> />
		<label class="radioCheck" for="pack_product"><?php echo smartyTranslate(array('s'=>'Pack'),$_smarty_tpl);?>
</label>
		<input type="radio" name="type_product" id="virtual_product" value="<?php echo Product::PTYPE_VIRTUAL;?>
" <?php if ($_smarty_tpl->tpl_vars['product_type']->value==Product::PTYPE_VIRTUAL){?>checked="checked"<?php }?> />
		<label class="radioCheck" for="virtual_product"><?php echo smartyTranslate(array('s'=>'Virtual Product (services, booking and downloadable products)'),$_smarty_tpl);?>
</label>
	</div>

	<div class="separation"></div>
	<br />
	<table cellpadding="5" style="width: 50%; float: left; margin-right: 20px; border-right: 1px solid #CCCCCC;">
	
		<tr>
			<td class="col-left">
				<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"name",'type'=>"default",'multilang'=>"true"), 0);?>

				<label><?php echo smartyTranslate(array('s'=>'Name:'),$_smarty_tpl);?>
</label>
			</td>
			<td style="padding-bottom:5px;" class="translatable">
			<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value){
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
				<div class="lang_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
" style="<?php if (!$_smarty_tpl->tpl_vars['language']->value['is_default']){?>display: none;<?php }?> float: left;">
						<input class="<?php echo $_smarty_tpl->tpl_vars['class_input_ajax']->value;?>
<?php if (!$_smarty_tpl->tpl_vars['product']->value->id){?>copy2friendlyUrl<?php }?> updateCurrentText" size="43" type="text" <?php if (!$_smarty_tpl->tpl_vars['product']->value->id){?>disabled="disabled"<?php }?>
						id="name_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
" name="name_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
"
						value="<?php echo (($tmp = @smarty_modifier_htmlentitiesUTF8($_smarty_tpl->tpl_vars['product']->value->name[$_smarty_tpl->tpl_vars['language']->value['id_lang']]))===null||$tmp==='' ? '' : $tmp);?>
"/><sup> *</sup>
					<span class="hint" name="help_box"><?php echo smartyTranslate(array('s'=>'Invalid characters:'),$_smarty_tpl);?>
 <>;=#{}<span class="hint-pointer">&nbsp;</span>
					</span>
				</div>
			<?php } ?>
			</td>
		</tr>
		<tr>
			<td class="col-left"><label><?php echo $_smarty_tpl->tpl_vars['bullet_common_field']->value;?>
 <?php echo smartyTranslate(array('s'=>'Reference:'),$_smarty_tpl);?>
</label></td>
			<td style="padding-bottom:5px;">
				<input size="55" type="text" name="reference" value="<?php echo smarty_modifier_htmlentitiesUTF8($_smarty_tpl->tpl_vars['product']->value->reference);?>
" style="width: 130px; margin-right: 44px;" />
				<span class="hint" name="help_box"><?php echo smartyTranslate(array('s'=>'Special characters allowed:'),$_smarty_tpl);?>
.-_#\<span class="hint-pointer">&nbsp;</span></span>
			</td>
		</tr>
		<tr>
			<td class="col-left"><label><?php echo $_smarty_tpl->tpl_vars['bullet_common_field']->value;?>
 <?php echo smartyTranslate(array('s'=>'EAN13 or JAN:'),$_smarty_tpl);?>
</label></td>
			<td style="padding-bottom:5px;">
				<input size="55" maxlength="13" type="text" name="ean13" value="<?php echo smarty_modifier_htmlentitiesUTF8($_smarty_tpl->tpl_vars['product']->value->ean13);?>
" style="width: 130px; margin-right: 5px;" /> <span class="small"><?php echo smartyTranslate(array('s'=>'(Europe, Japan)'),$_smarty_tpl);?>
</span>
			</td>
		</tr>
		<tr>
			<td class="col-left"><label><?php echo $_smarty_tpl->tpl_vars['bullet_common_field']->value;?>
 <?php echo smartyTranslate(array('s'=>'UPC:'),$_smarty_tpl);?>
</label></td>
			<td style="padding-bottom:5px;">
				<input size="55" maxlength="12" type="text" name="upc" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value->upc, 'html', 'UTF-8');?>
" style="width: 130px; margin-right: 5px;" /> <span class="small"><?php echo smartyTranslate(array('s'=>'(US, Canada)'),$_smarty_tpl);?>
</span>
			</td>
		</tr>
	</table>
	
	<table cellpadding="5" style="width: 40%; float: left; margin-left: 10px;">
	<tr>
		<td class="col-left">
			<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"active",'type'=>"radio",'onclick'=>''), 0);?>

			<label class="text"><?php echo smartyTranslate(array('s'=>'Status:'),$_smarty_tpl);?>
</label>
		</td>
		<td style="padding-bottom:5px;">
			<ul class="listForm">
				<li>
					<input onclick="toggleDraftWarning(false);showOptions(true);" type="radio" name="active" id="active_on" value="1" <?php if ($_smarty_tpl->tpl_vars['product']->value->active||!$_smarty_tpl->tpl_vars['product']->value->isAssociatedToShop()){?>checked="checked" <?php }?> />
					<label for="active_on" class="radioCheck"><?php echo smartyTranslate(array('s'=>'Enabled'),$_smarty_tpl);?>
</label>
				</li>
				<li>
					<input onclick="toggleDraftWarning(true);showOptions(false);"  type="radio" name="active" id="active_off" value="0" <?php if (!$_smarty_tpl->tpl_vars['product']->value->active&&$_smarty_tpl->tpl_vars['product']->value->isAssociatedToShop()){?>checked="checked"<?php }?> />
					<label for="active_off" class="radioCheck"><?php echo smartyTranslate(array('s'=>'Disabled'),$_smarty_tpl);?>
</label>
				</li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="col-left">
			<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"visibility",'type'=>"default"), 0);?>

			<label><?php echo smartyTranslate(array('s'=>'Visibility:'),$_smarty_tpl);?>
</label>
		</td>
		<td style="padding-bottom:5px;">
			<select name="visibility" id="visibility">
				<option value="both" <?php if ($_smarty_tpl->tpl_vars['product']->value->visibility=='both'){?>selected="selected"<?php }?> ><?php echo smartyTranslate(array('s'=>'Everywhere'),$_smarty_tpl);?>
</option>
				<option value="catalog" <?php if ($_smarty_tpl->tpl_vars['product']->value->visibility=='catalog'){?>selected="selected"<?php }?> ><?php echo smartyTranslate(array('s'=>'Catalog only'),$_smarty_tpl);?>
</option>
				<option value="search" <?php if ($_smarty_tpl->tpl_vars['product']->value->visibility=='search'){?>selected="selected"<?php }?> ><?php echo smartyTranslate(array('s'=>'Search only'),$_smarty_tpl);?>
</option>
				<option value="none" <?php if ($_smarty_tpl->tpl_vars['product']->value->visibility=='none'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Nowhere'),$_smarty_tpl);?>
</option>
			</select>
		</td>
	</tr>
	<tr id="product_options" <?php if (!$_smarty_tpl->tpl_vars['product']->value->active){?>style="display:none"<?php }?> >
		<td class="col-left">
			<?php if (isset($_smarty_tpl->tpl_vars['display_multishop_checkboxes']->value)&&$_smarty_tpl->tpl_vars['display_multishop_checkboxes']->value){?>
				<div class="multishop_product_checkbox">
					<ul class="listForm">
						<li><?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('only_checkbox'=>"true",'field'=>"available_for_order",'type'=>"default"), 0);?>
</li>
						<li><?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('only_checkbox'=>"true",'field'=>"show_price",'type'=>"show_price"), 0);?>
</li>
						<li><?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('only_checkbox'=>"true",'field'=>"online_only",'type'=>"default"), 0);?>
</li>
					</ul>
				</div>
			<?php }?>

			<label><?php echo smartyTranslate(array('s'=>'Options:'),$_smarty_tpl);?>
</label>
		</td>
		<td style="padding-bottom:5px;">
			<ul class="listForm">
				<li>
					<input  type="checkbox" name="available_for_order" id="available_for_order" value="1" <?php if ($_smarty_tpl->tpl_vars['product']->value->available_for_order){?>checked="checked"<?php }?>  />
					<label for="available_for_order" class="t"><?php echo smartyTranslate(array('s'=>'available for order'),$_smarty_tpl);?>
</label>
				</li>
			<li>
				<input type="checkbox" name="show_price" id="show_price" value="1" <?php if ($_smarty_tpl->tpl_vars['product']->value->show_price){?>checked="checked"<?php }?> <?php if ($_smarty_tpl->tpl_vars['product']->value->available_for_order){?>disabled="disabled"<?php }?>/>
				<label for="show_price" class="t"><?php echo smartyTranslate(array('s'=>'show price'),$_smarty_tpl);?>
</label>
			</li>
			<li>
				<input type="checkbox" name="online_only" id="online_only" value="1" <?php if ($_smarty_tpl->tpl_vars['product']->value->online_only){?>checked="checked"<?php }?> />
				<label for="online_only" class="t"><?php echo smartyTranslate(array('s'=>'online only (not sold in store)'),$_smarty_tpl);?>
</label>
			</li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="col-left">
			<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"condition",'type'=>"default"), 0);?>

			<label><?php echo smartyTranslate(array('s'=>'Condition:'),$_smarty_tpl);?>
</label>
		</td>
		<td style="padding-bottom:5px;">
			<select name="condition" id="condition">
				<option value="new" <?php if ($_smarty_tpl->tpl_vars['product']->value->condition=='new'){?>selected="selected"<?php }?> ><?php echo smartyTranslate(array('s'=>'New'),$_smarty_tpl);?>
</option>
				<option value="used" <?php if ($_smarty_tpl->tpl_vars['product']->value->condition=='used'){?>selected="selected"<?php }?> ><?php echo smartyTranslate(array('s'=>'Used'),$_smarty_tpl);?>
</option>
				<option value="refurbished" <?php if ($_smarty_tpl->tpl_vars['product']->value->condition=='refurbished'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Refurbished'),$_smarty_tpl);?>
</option>
			</select>
		</td>
	</tr>
</table>

<table cellpadding="5" cellspacing="0" border="0" style="width: 100%;"><tr><td><div class="separation"></div></td></tr></table>
		<table cellspacing="0" cellpadding="5" border="0">
			<tr>
				<td class="col-left">
					<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"description_short",'type'=>"tinymce",'multilang'=>"true"), 0);?>

					<label><?php echo smartyTranslate(array('s'=>'Short description:'),$_smarty_tpl);?>
<br /></label>
					<p class="product_description">(<?php echo smartyTranslate(array('s'=>'appears in the product lists and on the top of the product page'),$_smarty_tpl);?>
)</p>
				</td>
				<td style="padding-bottom:5px;">
						<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/textarea_lang.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('languages'=>$_smarty_tpl->tpl_vars['languages']->value,'input_name'=>'description_short','input_value'=>$_smarty_tpl->tpl_vars['product']->value->description_short,'max'=>$_smarty_tpl->tpl_vars['PS_PRODUCT_SHORT_DESC_LIMIT']->value), 0);?>

					<p class="clear"></p>
				</td>
			</tr>
			<tr>
				<td class="col-left">
					<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('field'=>"description",'type'=>"tinymce",'multilang'=>"true"), 0);?>

					<label><?php echo smartyTranslate(array('s'=>'Description:'),$_smarty_tpl);?>
<br /></label>
					<p class="product_description">(<?php echo smartyTranslate(array('s'=>'appears in the body of the product page'),$_smarty_tpl);?>
)</p>
				</td>
				<td style="padding-bottom:5px;">
						<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/textarea_lang.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('languages'=>$_smarty_tpl->tpl_vars['languages']->value,'input_name'=>'description','input_value'=>$_smarty_tpl->tpl_vars['product']->value->description), 0);?>

					<p class="clear"></p>
				</td>
			</tr>
		<?php if ($_smarty_tpl->tpl_vars['images']->value){?>
			<tr>
				<td class="col-left"><label></label></td>
				<td style="padding-bottom:5px;">
					<div style="display:block;width:620px;" class="hint clear">
						<?php echo smartyTranslate(array('s'=>'Do you want an image associated with the product in your description?'),$_smarty_tpl);?>

						<span class="addImageDescription" style="cursor:pointer"><?php echo smartyTranslate(array('s'=>'Click here'),$_smarty_tpl);?>
</span>.
					</div>
					<p class="clear"></p>
				</td>
			</tr>
			</table>
				<table id="createImageDescription" style="display:none;width:100%">
					<tr>
						<td colspan="2" height="10"></td>
					</tr>
					<tr>
						<td class="col-left"><label><?php echo smartyTranslate(array('s'=>'Select your image:'),$_smarty_tpl);?>
</label></td>
						<td style="padding-bottom:5px;">
							<ul class="smallImage">
							<?php  $_smarty_tpl->tpl_vars['image'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['image']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['images']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['image']->key => $_smarty_tpl->tpl_vars['image']->value){
$_smarty_tpl->tpl_vars['image']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['image']->key;
?>
									<li>
										<input type="radio" name="smallImage" id="smallImage_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['image']->value['id_image'];?>
" <?php if ($_smarty_tpl->tpl_vars['key']->value==0){?>checked="checked"<?php }?> >
										<label for="smallImage_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" class="t">
											<img src="<?php echo $_smarty_tpl->tpl_vars['image']->value['src'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['image']->value['legend'];?>
" />
										</label>
									</li>
							<?php } ?>
							</ul>
							<p class="clear"></p>
						</td>
					</tr>
					<tr>
						<td class="col-left"><label><?php echo smartyTranslate(array('s'=>'Position:'),$_smarty_tpl);?>
</label></td>
						<td style="padding-bottom:5px;">
							<ul class="listForm">
								<li><input type="radio" name="leftRight" id="leftRight_1" value="left" checked>
									<label for="leftRight_1" class="t"><?php echo smartyTranslate(array('s'=>'left'),$_smarty_tpl);?>
</label>
								</li>
								<li>
									<input type="radio" name="leftRight" id="leftRight_2" value="right">
									<label for="leftRight_2" class="t"><?php echo smartyTranslate(array('s'=>'right'),$_smarty_tpl);?>
</label>
								</li>
							</ul>
						</td>
					</tr>
					<tr>
						<td class="col-left"><label><?php echo smartyTranslate(array('s'=>'Select the type of picture:'),$_smarty_tpl);?>
</label></td>
						<td style="padding-bottom:5px;">
							<ul class="listForm">
							<?php  $_smarty_tpl->tpl_vars['type'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['type']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['imagesTypes']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['type']->key => $_smarty_tpl->tpl_vars['type']->value){
$_smarty_tpl->tpl_vars['type']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['type']->key;
?>
								<li><input type="radio" name="imageTypes" id="imageTypes_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['type']->value['name'];?>
" <?php if ($_smarty_tpl->tpl_vars['key']->value==0){?>checked="checked"<?php }?>>
									<label for="imageTypes_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" class="t"><?php echo $_smarty_tpl->tpl_vars['type']->value['name'];?>
 <span>(<?php echo $_smarty_tpl->tpl_vars['type']->value['width'];?>
px <?php echo smartyTranslate(array('s'=>'by'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['type']->value['height'];?>
px)</span></label>
								</li>
							<?php } ?>
							</ul>
							<p class="clear"></p>
						</td>
					</tr>
					<tr>
						<td class="col-left"><label><?php echo smartyTranslate(array('s'=>'Image tag to insert:'),$_smarty_tpl);?>
</label></td>
						<td style="padding-bottom:5px;">
							<input type="text" id="resultImage" name="resultImage" />
							<p class="preference_description"><?php echo smartyTranslate(array('s'=>'The tag to copy/paste into the description.'),$_smarty_tpl);?>
</p>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div class="separation"></div>
						</td>
					</tr>
				</table>
		<?php }?>
		<table>
		<tr>
			<td class="col-left"><label><?php echo smartyTranslate(array('s'=>'Tags:'),$_smarty_tpl);?>
</label></td>
			<td style="padding-bottom:5px;" class="translatable">
				<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value){
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
					<div class="lang_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
" style="<?php if (!$_smarty_tpl->tpl_vars['language']->value['is_default']){?>display: none;<?php }?>float: left;">
						<input size="55" type="text" id="tags_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
" name="tags_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
"
							value="<?php echo smarty_modifier_htmlentitiesUTF8($_smarty_tpl->tpl_vars['product']->value->getTags($_smarty_tpl->tpl_vars['language']->value['id_lang'],true));?>
" />
						<span class="hint" name="help_box"><?php echo smartyTranslate(array('s'=>'Forbidden characters:'),$_smarty_tpl);?>
 !&lt;;&gt;;?=+#&quot;&deg;{}_$%<span class="hint-pointer">&nbsp;</span></span>
					</div>
				<?php } ?>
				<p class="preference_description clear"><?php echo smartyTranslate(array('s'=>'Tags separated by commas (e.g. dvd, dvd player, hifi)'),$_smarty_tpl);?>
</p>
			</td>
		</tr>
		</table>
	</table>
	<br />
</div>
<?php }} ?>