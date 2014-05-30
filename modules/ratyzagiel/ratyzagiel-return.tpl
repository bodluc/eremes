{if $status == 'false'}
<p class="warning">
		{l s='Zrezygnowałes z otrzymania kredytu ratalnego.' mod='ratyzagiel'}
		<br>
		{l s='W razie wątpliwości prosimy o kontakt z ' mod='ratyzagiel'} 
		<a href="{$base_dir}contact-form.php">{l s='Działem Obsługi Klienta' mod='ratyzagiel'}</a>.
	</p>	
{else}
	<p>{l s='Dziękujemy za zakupy w ' mod='ratyzagiel'} <span class="bold">{$shop_name}</span>.
		<br /><br />
		<b>{l s='Oczekuj na kontakt telefoniczny z konsultantem Żagiel S.A.' mod='ratyzagiel'}</b>
		<br><br>
		{l s='Podczas rozmowy telefonicznej sporządzi razem z Toba umowę ratalna.' mod='ratyzagiel'}
		<br><br>
		<b>{l s='Przygotuj: dowód osobisty, oraz drugi dokument tożsamosci.' mod='ratyzagiel'}</b>
		<br /><br />
		{l s='Kiedy tylko otrzymamy informację o wpłynięciu płatności będziesz mógł śledzić stan swojego zamówienia w sekcji ' mod='ratyzagiel'}
		<span class="bold">{l s=' MOJE KONTO' mod='ratyzagiel'}</span>.
		<br /><br />{l s='W razie jakichkolwiek pytań prosimy o kontakt z ' mod='ratyzagiel'} <a href="{$base_dir}contact-form.php">{l s='Działem Obsługi Klienta' mod='ratyzagiel'}</a>.
	</p>
{/if}