
if(typeof baseUri==="undefined"&&typeof baseDir!=="undefined")
baseUri=baseDir;var ajaxCart={nb_total_products:0,overrideButtonsInThePage:function(){$('.ajax_add_to_cart_button').unbind('click').click(function(){var idProduct=$(this).attr('rel').replace('ajax_id_product_','');if($(this).attr('disabled')!='disabled')
ajaxCart.add(idProduct,null,false,this);return false;});$('body#product p#add_to_cart input').unbind('click').click(function(){ajaxCart.add($('#product_page_product_id').val(),$('#idCombination').val(),true,null,$('#quantity_wanted').val(),null);return false;});$('#cart_block_list .ajax_cart_block_remove_link').unbind('click').click(function(){var customizationId=0;var productId=0;var productAttributeId=0;if($($(this).parent().parent()).attr('name')=='customization')
var customizableProductDiv=$($(this).parent().parent()).find("div[id^=deleteCustomizableProduct_]");else
var customizableProductDiv=$($(this).parent()).find("div[id^=deleteCustomizableProduct_]");if(customizableProductDiv&&$(customizableProductDiv).length)
{$(customizableProductDiv).each(function(){var ids=$(this).attr('id').split('_');if(typeof(ids[1])!='undefined')
{customizationId=parseInt(ids[1]);productId=parseInt(ids[2]);if(typeof(ids[3])!='undefined')
productAttributeId=parseInt(ids[3]);return false;}});}
if(!customizationId)
{var firstCut=$(this).parent().parent().attr('id').replace('cart_block_product_','');firstCut=firstCut.replace('deleteCustomizableProduct_','');ids=firstCut.split('_');productId=parseInt(ids[0]);if(typeof(ids[1])!='undefined')
productAttributeId=parseInt(ids[1]);}
var idAddressDelivery=$(this).parent().parent().attr('id').match(/.*_\d+_\d+_(\d+)/)[1];ajaxCart.remove(productId,productAttributeId,customizationId,idAddressDelivery);return false;});},expand:function(){if($('#cart_block #cart_block_list').hasClass('collapsed'))
{$('#header #cart_block #cart_block_summary').slideUp(200,function(){$(this).addClass('collapsed').removeClass('expanded');$('#header #cart_block #cart_block_list').slideDown({duration:450,complete:function(){$(this).addClass('expanded').removeClass('collapsed');}});});$('#cart_block h4 span#block_cart_expand').fadeOut('slow',function(){$('#cart_block h4 span#block_cart_collapse').fadeIn('fast');});$.ajax({type:'GET',url:baseDir+'modules/blockcart/blockcart-set-collapse.php',async:true,data:'ajax_blockcart_display=expand'+'&rand='+new Date().getTime()});}},refresh:function(){$.ajax({type:'GET',url:baseUri,async:true,cache:false,dataType:"json",data:'controller=cart&ajax=true&token='+static_token,success:function(jsonData)
{ajaxCart.updateCart(jsonData);}});},collapse:function(){if($('#cart_block #cart_block_list').hasClass('expanded'))
{$('#header #cart_block #cart_block_list').slideUp('slow',function(){$(this).addClass('collapsed').removeClass('expanded');$('#header #cart_block #cart_block_summary').slideDown(450,function(){$(this).addClass('expanded').removeClass('collapsed');});});$('#cart_block h4 span#block_cart_collapse').fadeOut('slow',function(){$('#cart_block h4 span#block_cart_expand').fadeIn('fast');});$.ajax({type:'GET',url:baseDir+'modules/blockcart/blockcart-set-collapse.php',async:true,data:'ajax_blockcart_display=collapse'+'&rand='+new Date().getTime()});}},updateCartInformation:function(jsonData,addedFromProductPage)
{ajaxCart.updateCart(jsonData);if(addedFromProductPage)
$('body#product p#add_to_cart input').removeAttr('disabled').addClass('exclusive').removeClass('exclusive_disabled');else
$('.ajax_add_to_cart_button').removeAttr('disabled');},add:function(idProduct,idCombination,addedFromProductPage,callerElement,quantity,whishlist){if(addedFromProductPage&&!checkCustomizations())
{alert(fieldRequired);return;}
emptyCustomizations();if(addedFromProductPage)
{$('body#product p#add_to_cart input').attr('disabled',true).removeClass('exclusive').addClass('exclusive_disabled');$('.filled').removeClass('filled');}
else
$(callerElement).attr('disabled',true);if($('#cart_block #cart_block_list').hasClass('collapsed'))
this.expand();$.ajax({type:'POST',url:baseUri,async:true,cache:false,dataType:"json",data:'controller=cart&add=1&ajax=true&qty='+((quantity&&quantity!=null)?quantity:'1')+'&id_product='+idProduct+'&token='+static_token+((parseInt(idCombination)&&idCombination!=null)?'&ipa='+parseInt(idCombination):''),success:function(jsonData,textStatus,jqXHR)
{if(whishlist&&!jsonData.errors)
WishlistAddProductCart(whishlist[0],idProduct,idCombination,whishlist[1]);var $element=$(callerElement).parent().parent().find('a.product_image img,a.product_img_link img');if(!$element.length)
$element=$('#bigpic');var $picture=$element.clone();var pictureOffsetOriginal=$element.offset();if($picture.size())
$picture.css({'position':'absolute','top':pictureOffsetOriginal.top,'left':pictureOffsetOriginal.left});var pictureOffset=$picture.offset();if($('#cart_block').offset().top&&$('#cart_block').offset().left)
var cartBlockOffset=$('#cart_block').offset();else
var cartBlockOffset=$('#shopping_cart').offset();if(cartBlockOffset!=undefined&&$picture.size())
{$picture.appendTo('body');$picture.css({'position':'absolute','top':$picture.css('top'),'left':$picture.css('left'),'z-index':4242}).animate({'width':$element.attr('width')*0.66,'height':$element.attr('height')*0.66,'opacity':0.2,'top':cartBlockOffset.top+30,'left':cartBlockOffset.left+15},1000).fadeOut(100,function(){ajaxCart.updateCartInformation(jsonData,addedFromProductPage);});}
else
ajaxCart.updateCartInformation(jsonData,addedFromProductPage);},error:function(XMLHttpRequest,textStatus,errorThrown)
{alert("Impossible to add the product to the cart.\n\ntextStatus: '"+textStatus+"'\nerrorThrown: '"+errorThrown+"'\nresponseText:\n"+XMLHttpRequest.responseText);if(addedFromProductPage)
$('body#product p#add_to_cart input').removeAttr('disabled').addClass('exclusive').removeClass('exclusive_disabled');else
$(callerElement).removeAttr('disabled');}});},remove:function(idProduct,idCombination,customizationId,idAddressDelivery){$.ajax({type:'POST',url:baseUri,async:true,cache:false,dataType:"json",data:'controller=cart&delete=1&id_product='+idProduct+'&ipa='+((idCombination!=null&&parseInt(idCombination))?idCombination:'')+((customizationId&&customizationId!=null)?'&id_customization='+customizationId:'')+'&id_address_delivery='+idAddressDelivery+'&token='+static_token+'&ajax=true',success:function(jsonData){ajaxCart.updateCart(jsonData);if($('body').attr('id')=='order'||$('body').attr('id')=='order-opc')
deleteProductFromSummary(idProduct+'_'+idCombination+'_'+customizationId+'_'+idAddressDelivery);},error:function(){alert('ERROR: unable to delete the product');}});},hideOldProducts:function(jsonData){if($('#cart_block #cart_block_list dl.products').length>0)
{var removedProductId=null;var removedProductData=null;var removedProductDomId=null;$('#cart_block_list dl.products dt').each(function(){var domIdProduct=$(this).attr('id');var firstCut=domIdProduct.replace('cart_block_product_','');var ids=firstCut.split('_');var stayInTheCart=false;for(aProduct in jsonData.products)
{if(jsonData.products[aProduct]['id']==ids[0]&&(!ids[1]||jsonData.products[aProduct]['idCombination']==ids[1]))
{stayInTheCart=true;ajaxCart.hideOldProductCustomizations(jsonData.products[aProduct],domIdProduct);}}
if(!stayInTheCart)
{removedProductId=$(this).attr('id');}});if(removedProductId!=null)
{var firstCut=removedProductId.replace('cart_block_product_','');var ids=firstCut.split('_');$('#'+removedProductId).addClass('strike').fadeTo('slow',0,function(){$(this).slideUp('slow',function(){$(this).remove();if($('#cart_block dl.products dt').length==0)
{$("#header #cart_block").stop(true,true).slideUp(200);$('p#cart_block_no_products:hidden').slideDown(450);$('div#cart_block dl.products').remove();}});});$('dd#cart_block_combination_of_'+ids[0]+(ids[1]?'_'+ids[1]:'')+(ids[2]?'_'+ids[2]:'')).fadeTo('fast',0,function(){$(this).slideUp('fast',function(){$(this).remove();});});}}},hideOldProductCustomizations:function(product,domIdProduct)
{var customizationList=$('#cart_block #cart_block_list ul#customization_'+product['id']+'_'+product['idCombination']);if(customizationList.length>0)
{$(customizationList).find("li").each(function(){$(this).find("div").each(function(){var customizationDiv=$(this).attr('id');var tmp=customizationDiv.replace('deleteCustomizableProduct_','');var ids=tmp.split('_');if((parseInt(product.idCombination)==parseInt(ids[2]))&&!ajaxCart.doesCustomizationStillExist(product,ids[0]))
$('#'+customizationDiv).parent().addClass('strike').fadeTo('slow',0,function(){$(this).slideUp('slow');$(this).remove();});});});}
var removeLinks=$('#cart_block_product_'+domIdProduct).find('a.ajax_cart_block_remove_link');if(!product.hasCustomizedDatas&&!removeLinks.length)
$('#'+domIdProduct+' span.remove_link').html('<a class="ajax_cart_block_remove_link" rel="nofollow" href="'+baseUri+'?controller=cart&amp;delete&amp;id_product='+product['id']+'&amp;ipa='+product['idCombination']+'&amp;token='+static_token+'"> </a>');if(parseFloat(product.price_float)<=0)
$('#'+domIdProduct+' span.remove_link').html('');},doesCustomizationStillExist:function(product,customizationId)
{var exists=false;$(product.customizedDatas).each(function(){if(this.customizationId==customizationId)
{exists=true;return false;}});return(exists);},refreshVouchers:function(jsonData){if(typeof(jsonData.discounts)=='undefined'||jsonData.discounts.length==0)
$('#vouchers').hide();else
{$('#vouchers tbody').html('');for(i=0;i<jsonData.discounts.length;i++)
{if(parseFloat(jsonData.discounts[i].price_float)>0)
{var delete_link='';if(jsonData.discounts[i].code.length)
delete_link='<a class="delete_voucher" href="'+jsonData.discounts[i].link+'" title="'+delete_txt+'"><img src="'+img_dir+'icon/delete.gif" alt="'+delete_txt+'" class="icon" /></a>';$('#vouchers tbody').append($('<tr class="bloc_cart_voucher" id="bloc_cart_voucher_'+jsonData.discounts[i].id+'">'
+' <td class="quantity">1x</td>'
+' <td class="name" title="'+jsonData.discounts[i].description+'">'+jsonData.discounts[i].name+'</td>'
+' <td class="price">-'+jsonData.discounts[i].price+'</td>'
+' <td class="delete">'+delete_link+'</td>'
+'</tr>'));}}
$('#vouchers').show();}},updateProductQuantity:function(product,quantity){$('dt#cart_block_product_'+product.id+'_'+(product.idCombination?product.idCombination:'0')+'_'+(product.idAddressDelivery?product.idAddressDelivery:'0')+' .quantity').fadeTo('fast',0,function(){$(this).text(quantity);$(this).fadeTo('fast',1,function(){$(this).fadeTo('fast',0,function(){$(this).fadeTo('fast',1,function(){$(this).fadeTo('fast',0,function(){$(this).fadeTo('fast',1);});});});});});},displayNewProducts:function(jsonData){$(jsonData.products).each(function(){if(this.id!=undefined)
{if($('div#cart_block dl.products').length==0)
{$('p#cart_block_no_products').before('<dl class="products"></dl>');$('p#cart_block_no_products').hide();}
var domIdProduct=this.id+'_'+(this.idCombination?this.idCombination:'0')+'_'+(this.idAddressDelivery?this.idAddressDelivery:'0');var domIdProductAttribute=this.id+'_'+(this.idCombination?this.idCombination:'0');if($('#cart_block dt#cart_block_product_'+domIdProduct).length==0)
{var productId=parseInt(this.id);var productAttributeId=(this.hasAttributes?parseInt(this.attributes):0);var content='<dt class="hidden" id="cart_block_product_'+domIdProduct+'">';content+='<span class="quantity-formated"><span class="quantity">'+this.quantity+'</span>x</span>';var name=(this.name.length>12?this.name.substring(0,10)+'...':this.name);content+='<a href="'+this.link+'" title="'+this.name+'">'+name+'</a>';if(parseFloat(this.price_float)>0)
content+='<span class="remove_link"><a rel="nofollow" class="ajax_cart_block_remove_link" href="'+baseUri+'?controller=cart&amp;delete&amp;id_product='+productId+'&amp;token='+static_token+(this.hasAttributes?'&amp;ipa='+parseInt(this.idCombination):'')+'"> </a></span>';else
content+='<span class="remove_link"></span>';content+='<span class="price">'+(parseFloat(this.price_float)>0?this.priceByLine:freeProductTranslation)+'</span>';content+='</dt>';if(this.hasAttributes)
content+='<dd id="cart_block_combination_of_'+domIdProduct+'" class="hidden"><a href="'+this.link+'" title="'+this.name+'">'+this.attributes+'</a>';if(this.hasCustomizedDatas)
content+=ajaxCart.displayNewCustomizedDatas(this);if(this.hasAttributes)content+='</dd>';$('#cart_block dl.products').append(content);}
else
{var jsonProduct=this;if($('dt#cart_block_product_'+domIdProduct+' .quantity').text()!=jsonProduct.quantity||$('dt#cart_block_product_'+domIdProduct+' .price').text()!=jsonProduct.priceByLine)
{if(parseFloat(this.price_float)>0)
$('dt#cart_block_product_'+domIdProduct+' .price').text(jsonProduct.priceByLine);else
$('dt#cart_block_product_'+domIdProduct+' .price').html(freeProductTranslation);ajaxCart.updateProductQuantity(jsonProduct,jsonProduct.quantity);if(jsonProduct.hasCustomizedDatas)
{customizationFormatedDatas=ajaxCart.displayNewCustomizedDatas(jsonProduct);if(!$('#cart_block ul#customization_'+domIdProductAttribute).length)
{if(jsonProduct.hasAttributes)
$('#cart_block dd#cart_block_combination_of_'+domIdProduct).append(customizationFormatedDatas);else
$('#cart_block dl.products').append(customizationFormatedDatas);}
else
{$('#cart_block ul#customization_'+domIdProductAttribute).html('');$('#cart_block ul#customization_'+domIdProductAttribute).append(customizationFormatedDatas);}}}}
$('#cart_block dl.products .hidden').slideDown(450).removeClass('hidden');var removeLinks=$('#cart_block_product_'+domIdProduct).find('a.ajax_cart_block_remove_link');if(this.hasCustomizedDatas&&removeLinks.length)
$(removeLinks).each(function(){$(this).remove();});}});},displayNewCustomizedDatas:function(product)
{var content='';var productId=parseInt(product.id);var productAttributeId=typeof(product.idCombination)=='undefined'?0:parseInt(product.idCombination);var hasAlreadyCustomizations=$('#cart_block ul#customization_'+productId+'_'+productAttributeId).length;if(!hasAlreadyCustomizations)
{if(!product.hasAttributes)
content+='<dd id="cart_block_combination_of_'+productId+'" class="hidden">';if($('#customization_'+productId+'_'+productAttributeId).val()==undefined)
content+='<ul class="cart_block_customizations" id="customization_'+productId+'_'+productAttributeId+'">';}
$(product.customizedDatas).each(function(){var done=0;customizationId=parseInt(this.customizationId);productAttributeId=typeof(product.idCombination)=='undefined'?0:parseInt(product.idCombination);content+='<li name="customization"><div class="deleteCustomizableProduct" id="deleteCustomizableProduct_'+customizationId+'_'+productId+'_'+(productAttributeId?productAttributeId:'0')+'"><a  rel="nofollow" class="ajax_cart_block_remove_link" href="'+baseUri+'?controller=cart&amp;delete&amp;id_product='+productId+'&amp;ipa='+productAttributeId+'&amp;id_customization='+customizationId+'&amp;token='+static_token+'"> </a></div><span class="quantity-formated"><span class="quantity">'+parseInt(this.quantity)+'</span>x</span>';$(this.datas).each(function(){if(this['type']==CUSTOMIZE_TEXTFIELD)
{$(this.datas).each(function(){if(this['index']==0)
{content+=' '+this.truncatedValue.replace(/<br \/>/g,' ');done=1;return false;}})}});if(!done)
content+=customizationIdMessage+customizationId;if(!hasAlreadyCustomizations)content+='</li>';if(customizationId)
{$('#uploadable_files li div.customizationUploadBrowse img').remove();$('#text_fields li input').attr('value','');}});if(!hasAlreadyCustomizations)
{content+='</ul>';if(!product.hasAttributes)content+='</dd>';}
return(content);},updateCart:function(jsonData){if(jsonData.hasError)
{var errors='';for(error in jsonData.errors)
if(error!='indexOf')
errors+=jsonData.errors[error]+"\n";alert(errors);}
else
{ajaxCart.updateCartEverywhere(jsonData);ajaxCart.hideOldProducts(jsonData);ajaxCart.displayNewProducts(jsonData);ajaxCart.refreshVouchers(jsonData);$('#cart_block dl.products dt').removeClass('first_item').removeClass('last_item').removeClass('item');$('#cart_block dl.products dt:first').addClass('first_item');$('#cart_block dl.products dt:not(:first,:last)').addClass('item');$('#cart_block dl.products dt:last').addClass('last_item');ajaxCart.overrideButtonsInThePage();}},updateCartEverywhere:function(jsonData){$('.ajax_cart_total').text(jsonData.productTotal);if(parseFloat(jsonData.shippingCostFloat)>0)
$('.ajax_cart_shipping_cost').text(jsonData.shippingCost);else
$('.ajax_cart_shipping_cost').html(freeShippingTranslation);$('.ajax_cart_tax_cost').text(jsonData.taxCost);$('.cart_block_wrapping_cost').text(jsonData.wrappingCost);$('.ajax_block_cart_total').text(jsonData.total);this.nb_total_products=jsonData.nbTotalProducts;if(parseInt(jsonData.nbTotalProducts)>0)
{$('.ajax_cart_no_product').hide();$('.ajax_cart_quantity').text(jsonData.nbTotalProducts);$('.ajax_cart_quantity').fadeIn('slow');$('.ajax_cart_total').fadeIn('slow');if(parseInt(jsonData.nbTotalProducts)>1)
{$('.ajax_cart_product_txt').each(function(){$(this).hide();});$('.ajax_cart_product_txt_s').each(function(){$(this).show();});}
else
{$('.ajax_cart_product_txt').each(function(){$(this).show();});$('.ajax_cart_product_txt_s').each(function(){$(this).hide();});}}
else
{$('.ajax_cart_quantity, .ajax_cart_product_txt_s, .ajax_cart_product_txt, .ajax_cart_total').each(function(){$(this).hide();});$('.ajax_cart_no_product').show('slow');}}};function HoverWatcher(selector){this.hovering=false;var self=this;this.isHoveringOver=function(){return self.hovering;}
$(selector).hover(function(){self.hovering=true;},function(){self.hovering=false;})}
$(document).ready(function(){$('#block_cart_collapse').click(function(){ajaxCart.collapse();});$('#block_cart_expand').click(function(){ajaxCart.expand();});ajaxCart.overrideButtonsInThePage();ajaxCart.refresh();var cart_block=new HoverWatcher('#cart_block');var shopping_cart=new HoverWatcher('#shopping_cart');$("#shopping_cart a:first").hover(function(){$(this).css('border-radius','3px 3px 0px 0px');if(ajaxCart.nb_total_products>0)
$("#header #cart_block").stop(true,true).slideDown(450);},function(){$('#shopping_cart a').css('border-radius','3px');setTimeout(function(){if(!shopping_cart.isHoveringOver()&&!cart_block.isHoveringOver())
$("#header #cart_block").stop(true,true).slideUp(450);},200);});$("#cart_block").hover(function(){$('#shopping_cart a').css('border-radius','3px 3px 0px 0px');},function(){$('#shopping_cart a').css('border-radius','3px');setTimeout(function(){if(!shopping_cart.isHoveringOver())
$("#header #cart_block").stop(true,true).slideUp(450);},200);});$('.delete_voucher').live('click',function(){$.ajax({url:$(this).attr('href')});$(this).parent().parent().remove();if($('body').attr('id')=='order'||$('body').attr('id')=='order-opc')
{if(typeof(updateAddressSelection)!='undefined')
updateAddressSelection();else
location.reload();}
return false;});});;function openBranch(jQueryElement,noAnimation){jQueryElement.addClass('OPEN').removeClass('CLOSE');if(noAnimation)
jQueryElement.parent().find('ul:first').show();else
jQueryElement.parent().find('ul:first').slideDown();}
function closeBranch(jQueryElement,noAnimation){jQueryElement.addClass('CLOSE').removeClass('OPEN');if(noAnimation)
jQueryElement.parent().find('ul:first').hide();else
jQueryElement.parent().find('ul:first').slideUp();}
function toggleBranch(jQueryElement,noAnimation){if(jQueryElement.hasClass('OPEN'))
closeBranch(jQueryElement,noAnimation);else
openBranch(jQueryElement,noAnimation);}
$(document).ready(function(){if(!$('ul.tree.dhtml').hasClass('dynamized'))
{$('ul.tree.dhtml ul').prev().before("<span class='grower OPEN'> </span>");$('ul.tree.dhtml ul li:last-child, ul.tree.dhtml li:last-child').addClass('last');$('ul.tree.dhtml span.grower.OPEN').addClass('CLOSE').removeClass('OPEN').parent().find('ul:first').hide();$('ul.tree.dhtml').show();$('ul.tree.dhtml .selected').parents().each(function(){if($(this).is('ul'))
toggleBranch($(this).prev().prev(),true);});toggleBranch($('ul.tree.dhtml .selected').prev(),true);$('ul.tree.dhtml span.grower').click(function(){toggleBranch($(this));});$('ul.tree.dhtml').addClass('dynamized');$('ul.tree.dhtml').removeClass('dhtml');}});;(function($){$.fn.hoverIntent=function(f,g){var cfg={sensitivity:7,interval:0,timeout:0};cfg=$.extend(cfg,g?{over:f,out:g}:f);var cX,cY,pX,pY;var track=function(ev){cX=ev.pageX;cY=ev.pageY;};var compare=function(ev,ob){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t);if((Math.abs(pX-cX)+Math.abs(pY-cY))<cfg.sensitivity){$(ob).unbind("mousemove",track);ob.hoverIntent_s=1;return cfg.over.apply(ob,[ev]);}else{pX=cX;pY=cY;ob.hoverIntent_t=setTimeout(function(){compare(ev,ob);},cfg.interval);}};var delay=function(ev,ob){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t);ob.hoverIntent_s=0;return cfg.out.apply(ob,[ev]);};var handleHover=function(e){var p=(e.type=="mouseover"?e.fromElement:e.toElement)||e.relatedTarget;while(p&&p!=this){try{p=p.parentNode;}catch(e){p=this;}}
if(p==this){return false;}
var ev=jQuery.extend({},e);var ob=this;if(ob.hoverIntent_t){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t);}
if(e.type=="mouseover"){pX=ev.pageX;pY=ev.pageY;$(ob).bind("mousemove",track);if(ob.hoverIntent_s!=1){ob.hoverIntent_t=setTimeout(function(){compare(ev,ob);},cfg.interval);}}else{$(ob).unbind("mousemove",track);if(ob.hoverIntent_s==1){ob.hoverIntent_t=setTimeout(function(){delay(ev,ob);},cfg.timeout);}}};return this.mouseover(handleHover).mouseout(handleHover);};})(jQuery);;;(function($){$.fn.superfish=function(op){var sf=$.fn.superfish,c=sf.c,$arrow=$(['<span class="',c.arrowClass,'"> &#187;</span>'].join('')),over=function(){var $$=$(this),menu=getMenu($$);clearTimeout(menu.sfTimer);$$.showSuperfishUl().siblings().hideSuperfishUl();},out=function(){var $$=$(this),menu=getMenu($$),o=sf.op;clearTimeout(menu.sfTimer);menu.sfTimer=setTimeout(function(){o.retainPath=($.inArray($$[0],o.$path)>-1);$$.hideSuperfishUl();if(o.$path.length&&$$.parents(['li.',o.hoverClass].join('')).length<1){over.call(o.$path);}},o.delay);},getMenu=function($menu){var menu=$menu.parents(['ul.',c.menuClass,':first'].join(''))[0];sf.op=sf.o[menu.serial];return menu;},addArrow=function($a){$a.addClass(c.anchorClass).append($arrow.clone());};return this.each(function(){var s=this.serial=sf.o.length;var o=$.extend({},sf.defaults,op);o.$path=$('li.'+o.pathClass,this).slice(0,o.pathLevels).each(function(){$(this).addClass([o.hoverClass,c.bcClass].join(' ')).filter('li:has(ul)').removeClass(o.pathClass);});sf.o[s]=sf.op=o;$('li:has(ul)',this)[($.fn.hoverIntent&&!o.disableHI)?'hoverIntent':'hover'](over,out).each(function(){if(o.autoArrows)addArrow($('>a:first-child',this));}).not('.'+c.bcClass).hideSuperfishUl();var $a=$('a',this);$a.each(function(i){var $li=$a.eq(i).parents('li');$a.eq(i).focus(function(){over.call($li);}).blur(function(){out.call($li);});});o.onInit.call(this);}).each(function(){menuClasses=[c.menuClass];if(sf.op.dropShadows&&!($.browser.msie&&$.browser.version<7))menuClasses.push(c.shadowClass);$(this).addClass(menuClasses.join(' '));});};var sf=$.fn.superfish;sf.o=[];sf.op={};sf.IE7fix=function(){var o=sf.op;if($.browser.msie&&$.browser.version>6&&o.dropShadows&&o.animation.opacity!=undefined)
this.toggleClass(sf.c.shadowClass+'-off');};sf.c={bcClass:'sf-breadcrumb',menuClass:'sf-js-enabled',anchorClass:'sf-with-ul',arrowClass:'sf-sub-indicator',shadowClass:'sf-shadow'};sf.defaults={hoverClass:'sfHover',pathClass:'overideThisToUse',pathLevels:1,delay:100,animation:{opacity:'show'},speed:'fast',autoArrows:true,dropShadows:true,disableHI:false,onInit:function(){},onBeforeShow:function(){},onShow:function(){},onHide:function(){}};$.fn.extend({hideSuperfishUl:function(){var o=sf.op,not=(o.retainPath===true)?o.$path:'';o.retainPath=false;var $ul=$(['li.',o.hoverClass].join(''),this).add(this).not(not).removeClass(o.hoverClass).find('>ul').hide().css('visibility','hidden');o.onHide.call($ul);return this;},showSuperfishUl:function(){var o=sf.op,sh=sf.c.shadowClass+'-off',$ul=this.addClass(o.hoverClass).find('>ul:hidden').css('visibility','visible');sf.IE7fix.call($ul);o.onBeforeShow.call($ul);$ul.animate(o.animation,o.speed,function(){sf.IE7fix.call($ul);o.onShow.call($ul);});return this;}});})(jQuery);jQuery(function(){jQuery('ul.sf-menu').superfish();});;