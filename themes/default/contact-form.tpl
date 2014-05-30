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
*  @version  Release: $Revision: 6594 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture name=path}{l s='Contact'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<div class="contact clearfix">
  <div class="left">
     <P><b>///AudioRMS.pl - Sklep Internetowy</b></p>
     <P>Fax : +48 (59) 727 34 66</p>
     <p>Kom.: +48 501 759 039</p>
     <p><b>WWW: </b>www.AudioRMS.pl</p>
     <p><b>E-mail: </b><a href="sklep@audiorms.pl">sklep@audiorms.pl</a></p>
  </div>
  <div class="right">
     <p><b>Dane firmy:</b></p>
     <p>AdsCity Media Łukasz Bodnar</p>
     <p>Wołcza Mała 4</p>
     <p>77-200 Miastko</p>
     <p><b>Raiffeisen Bank:</b></p>
     <p>PL05 1750 0012 0000 0000 2073 1338</p>
   </div>

   </div>
   <br />
<h4><b>Interaktywna Mapa siedziby firmy.</b></h4>
<iframe width="532" height="250" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.pl/maps?f=q&amp;source=s_q&amp;hl=pl&amp;geocode=&amp;q=Miastko&amp;aq=&amp;sll=51.953751,19.134379&amp;sspn=10.772927,19.753418&amp;ie=UTF8&amp;hq=&amp;hnear=Miastko,+bytowski,+Pomorskie&amp;ll=54.0026,16.98265&amp;spn=5.271618,9.876709&amp;t=m&amp;z=7&amp;output=embed"></iframe>
 <br />
 <br />


{if isset($confirmation)}
	<h4 color="red">{l s='Your message has been successfully sent to our team.'}</h4>
	<ul class="footer_links">
		<li><a href="{$base_dir}"><img class="icon" alt="" src="{$img_dir}icon/home.gif"/></a><a href="{$base_dir}">{l s='Home'}</a></li>
	</ul>
{elseif isset($alreadySent)}
	<h4>{l s='Your message has already been sent.'}</h4>
	<ul class="footer_links">
		<li><a href="{$base_dir}"><img class="icon" alt="" src="{$img_dir}icon/home.gif"/></a><a href="{$base_dir}">{l s='Home'}</a></li>
	</ul>
{else}
    <h4>{l s='Customer Service'} - {if isset($customerThread) && $customerThread}{l s='Your reply'}{else}{l s='Contact us'}{/if}</h4>
	<p class="bold">{l s='For questions about an order or for more information about our products'}.</p>
	{include file="$tpl_dir./errors.tpl"}
	<form action="{$request_uri|escape:'htmlall':'UTF-8'}" method="post" class="std" enctype="multipart/form-data">
		<fieldset>
			<h3>{l s='Send a message'}</h3>
			<p class="select" style="display:none">
				<label for="id_contact">{l s='Subject Heading'}</label>
			{if isset($customerThread.id_contact)}
				{foreach from=$contacts item=contact}
					{if $contact.id_contact == $customerThread.id_contact}
						<input type="text" id="contact_name" name="contact_name" value="{$contact.name|escape:'htmlall':'UTF-8'}" readonly="readonly" />
						<input type="hidden" name="id_contact" value="{$contact.id_contact}" />
					{/if}
				{/foreach}
			</p>
			{else}
				<select id="id_contact" name="id_contact" onchange="showElemFromSelect('id_contact', 'desc_contact')">
				{foreach from=$contacts item=contact}
					<option value="{$contact.id_contact|intval}" {if isset($smarty.post.id_contact) && $smarty.post.id_contact == $contact.id_contact}selected="selected"{/if}>{$contact.name|escape:'htmlall':'UTF-8'}</option>
				{/foreach}
				</select>
			</p>

				{foreach from=$contacts item=contact}
					<p id="desc_contact{$contact.id_contact|intval}" class="desc_contact" style="display:none;">
						{$contact.description|escape:'htmlall':'UTF-8'}
					</p>
				{/foreach}
			{/if}
			<p class="text">
				<label for="email">{l s='E-mail address'}</label>
				{if isset($customerThread.email)}
					<input type="text" id="email" name="from" value="{$customerThread.email|escape:'htmlall':'UTF-8'}" readonly="readonly" />
				{else}
					<input type="text" id="email" name="from" value="{$email|escape:'htmlall':'UTF-8'}" />
				{/if}
			</p>


		<p class="textarea">
			<label for="message">{l s='Message'}</label>
			 <textarea id="message" name="message" rows="15" cols="10">{if isset($message)}{$message|escape:'htmlall':'UTF-8'|stripslashes}{/if}</textarea>
		</p>
                {if $fileupload == 1}
            <p class="text">
            <label for="fileUpload">{l s='Attach File'}</label>
                <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
                <input type="file" name="fileUpload" id="fileUpload" />
            </p>
        {/if}
		<p class="submit">
			<input type="submit" name="submitMessage" id="submitMessage" value="{l s='Send'}" class="button_large" onclick="$(this).hide();" />
		</p>
	</fieldset>
</form>
{/if}
