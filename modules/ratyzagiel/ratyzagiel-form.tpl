<h1>{l s='Podsumowanie zamówienia' mod='ratyzagiel'}</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<h3>{l s='Zapłata na raty za pomocą systemu ratalnego Żagiel S.A.' mod='ratyzagiel'}</h3>
<br />Sumaryczna kwota zamówienia wynosi <span class="bold">{convertPrice price=$cart->getOrderTotal()}</span><br />
<form name="frZagiel" id="formZagiel" action="https://www.eraty.pl/symulator/krok1.php" method="post" onsubmit="return validate_Zagiel()">
	<input type="hidden" name="nrZamowieniaSklep" readonly="readonly" value="{$order_id}"  />
	<!-- Produkty -->
	{$products_inputs}
	
	<input type="hidden" name="typProduktu" value="0">
	<input type="hidden" name="wariantSklepu" value="1">
	<input type="hidden" name="sposobDostarczeniaTowaru" value="{$sposobDostarczeniaTowaru}">
	<input type="hidden" readonly="readonly" name="wartoscTowarow" value="{$cart->getOrderTotal()}" />
	<input type="hidden" readonly="readonly" name="imie" value="{$name}" />
	<input type="hidden" readonly="readonly" name="nazwisko" value="{$surname}" />
	<input type="hidden" readonly="readonly" name="email" value="{$email}" />
	<input type="hidden" readonly="readonly" name="telKontakt" value="{$phone}" />
	<input type="hidden" readonly="readonly" name="ulica" value="{$street}" />
	<input type="hidden" readonly="readonly" name="miasto" value="{$city}" />
	<input type="hidden" readonly="readonly" name="kodPocz" value="{$postal_code}" />
	<input type="hidden" name="char" value="UTF">{*
	Zestaw kodowania znaków przesyłanych przez sklep.
	Dostępne są tylko wartości: ISO, UTF, WIN (tylko je należy wysyłać).
	Odpowiadają one wartościom: ISO-8859-2, UTF-8, WINDOWS-1250
	*}
	<input type="hidden" readonly="readonly" name="numerSklepu" value="{$shop_id}" />
	<input type="hidden" readonly="readonly" name="wniosekZapisany" value="{$ok_return}" />
	<input type="hidden" readonly="readonly" name="wniosekAnulowany" value="{$error_return}" />	
	<input type="hidden" readonly="readonly" name="liczbaSztukTowarow" value="{$total_products}" />
	<br />
	
	Zapoznałem się <a onclick="nowe_okno();" id="jakkupic" style="cursor: pointer;"><b><u>z procedurą udzielenia kredytu ratalnego eRaty Żagiel</u></b></a> 
	<input type="checkbox" id="zagielzgoda" />
	</p>
    <br />
	<b>Proszę nie zapomnieć żeby rozpocząć proces zapłaty za zamówienie należy kliknąć na przycisk &quot;Kupuje na raty z Żagiel S.A.&quot;</b><br /><br />
	<p class="cart_navigation">
	<a href="{$base_dir_ssl}order.php?step=3" class="button_large">Inne formy płatności</a>
	<input type="submit" name="submit" value="{l s='Kupuje na raty z Żagiel S.A.' mod='ratyzagiel'}" class="exclusive_large" onclick="return validate_Zagiel()" />
</p>
</form>
