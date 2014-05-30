<?php 

class mod_pricewars_skapiec extends Pricewars {
	protected function generateXML(){
		$link = new Link();
        $xml = new DOMDocument();
		
		//$Products = Product::getProductsForSkapiec(Language::getIdByIso("pl"), 0, NULL, 'name', 'ASC');
		$Products = $this->getProducts();
		$Categories = $this->_getCategoryTree();

		if($Products&&$Categories){
			$xml->loadXML('<?xml version="1.0" encoding="UTF-8"?>
							<xmldata>
							  <version>12</version>
							  <header/>		
							  <category/>
							  <data/> 
							</xmldata>');

			$header = $xml->getElementsByTagName('header');
			$header = $header->item(0);

			$name = $xml->createElement('name',Configuration::get('PS_SHOP_NAME'));
			$header->appendChild($name);
			
			/* 
			// Wersja 12 już tego nie używa, przynajmniej nie było tego w dokumentacji dostarczonej.
			$shopid = $xml->createElement('shopid',Configuration::get('PRICEWARS_SKAPIEC_SHOP_ID') ? Configuration::get('PRICEWARS_SKAPIEC_SHOP_ID') : "");
			$header->appendChild($shopid);
			*/

			$www = $xml->createElement('www',$this->getShopHost());
			$header->appendChild($www);

			$time = $xml->createElement('time',date('Y-m-d'));
			$header->appendChild($time);

			$category = $xml->getElementsByTagName('category');
			$category = $category->item(0);

			foreach($Categories as $catitem){
				$cat = $xml->createElement('catitem');
				$category->appendChild($cat);

				$catid = $xml->createElement('catid',$catitem['id_category']);
				$cat->appendChild($catid);

				$catname = $xml->createElement('catname',$Categories[$catitem['id_category']]['path']);   
				$cat->appendChild($catname);
			}

			$data = $xml->getElementsByTagName('data');
			$data = $data->item(0);
            
            foreach ($Products AS $Product){
				
				$item = $xml->createElement('item');
				$data->appendChild($item);

				$compid = $xml->createElement('compid',$Product['id_product']);
				$item->appendChild($compid);

				$vendor = $xml->createElement('vendor',$Product['manufacturer_name']);
				$item->appendChild($vendor);

				$name = $xml->createElement('name', self::htmlentities($Product['name'])); 
				$item->appendChild($name);

				$price = $xml->createElement('price',$this-> getTaxedPrice($Product)); 
				$item->appendChild($price);
				/*  
					<partnr>APED:2030-53PLVFY</partnr> 
				- <!--  tzw PartNumber czyli unikalny kod nadany przez producenta danemu towarowi, pole nie jest obowiazkowe; wartoĹÄ moĹźe byc umieszczona takze w znaczniku NAME
				  --> 
				*/
				$catid = $xml->createElement('catid',$Product['id_category_default']); 
				$item->appendChild($catid);
				
				
				// description
				$desc = $xml->createElement('desclong');
				$item->appendChild($desc);
				
				$cdata_desc = $xml->createCDATASection(self::htmlentities($Product['description_short']));
				$desc->appendChild($cdata_desc);
				/* 
				<desclong>pelny opis towaru w sklepie</desclong> 
				pole nie jest obowiazkowe 
				
				<availability>2</availability> 
				ilosc dni od zlozenia zamowienia do czasu wyslania towaru, (-1  towar na zamowienie), pole nie jest obowiazkowe 
				*/

				$ean = $xml->createElement('ean',$Product['ean13']); 
				$item->appendChild($ean);
				
				/*
				<ean>1234567890123</ean> 
				kod EAN/ISBN produktu (pole obowiazkowe dla ksiazek oraz plyt z muzyka) 

			    <url>http://www.sklep.pl/0123abc.php</url> 
				URL do widoku produktu, pole nie jest obowiazkowe, obecnie url generowany jest dynamicznie na podstawie <compid> 
				  */
				
				$url = $xml->createElement('url',htmlspecialchars($link->getProductLink($Product['id_product'], $Product['link_rewrite'])));
				$item->appendChild($url);

				
				$actionid = isset($Product['id_cennik_offline']) ? $xml->createElement('action',$Product['id_cennik_offline']) : $xml->createElement('action', NULL);
				$item->appendChild( $actionid );

			}
		}
		return $xml;
	}

}