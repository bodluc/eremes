{*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 14973 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- Module HomeSlider -->
{if isset($p)}
<script type="text/javascript">
{if isset($homeslider_slides) && $homeslider_slides|@count > 1}
	{if $homeslider.loop == 1}
		var homeslider_loop = true;
	{else}
		var homeslider_loop = false;
	{/if}
{else}
	var homeslider_loop = false;
{/if}
var homeslider_speed = {$homeslider.speed};
var homeslider_pause = {$homeslider.pause};
</script>
{/if}
{if isset($homeslider_slides)}
<ul id="homeslider">
{foreach from=$p item=slide}
	{if $slide.active}
		<li class="homeslider_li">
        <div class="homeslider">
          <h4>{$slide.name}</h4>
        </div>
        <a href="{$slide.link}" title="{$slide.description_short}"><img src="{$slide.path}" alt="{$slide.description_short}" title="{$slide.name}" height="{$homeslider.height}"  /></a>
        
        </li>
	{/if}
{/foreach}
</ul>
{/if}
<!-- /Module HomeSlider -->
