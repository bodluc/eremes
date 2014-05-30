<?php 

class mod_pricewars_zakupy_onet extends Pricewars {
	protected function generateXML(){
		$link = new Link();
        $xml = new DOMDocument();
		
		//$Products = Product::getProducts(Language::getIdByIso("pl"), 0, NULL, 'name', 'ASC');
		$Products = $this->getProducts();
		$Categories = $this->_getCategoryTree();

		if($Products&&$Categories){
			$xml->loadXML('<?xml version="1.0" encoding="UTF-8" ?>
							<oferty aktualizacja="N" xmlns="http://www.zakupy.onet.pl/walidacja/oferty-partnerzy.xsd">

							</oferty>');

			$offers = $xml->getElementsByTagName('oferty');
			$offers = $offers->item(0);

			foreach ($Products AS $Product){

					$offer = $xml->createElement('oferta');
					$offers->appendChild($offer);

					$id = $xml->createElement('identyfikator',$Product['id_product']);
					$offer->appendChild($id);
					
					$name = $xml->createElement('nazwa',self::htmlentities($Product['name'])); 
					$offer->appendChild($name);

					$description_cdata = $xml->createCDATASection(strip_tags(html_entity_decode($Product['description_short'],ENT_COMPAT,"UTF-8"),"<br><li><p><ul><tr>"));
					$description = $xml->createElement('opis');
					$description->appendChild($description_cdata);
					$offer->appendChild($description);

					$category = $xml->createElement('sciezka_kategorii',$Categories[$Product['id_category_default']]['path']);
					$offer->appendChild($category);

					$category = $xml->createElement('id_kategorii_sklepu',$Product['id_category_default']);
					$offer->appendChild($category);

					$price = $xml->createElement('cena',$this-> getTaxedPrice($Product)); 
					$offer->appendChild($price);

					$url = $xml->createElement('url', htmlspecialchars($link->getProductLink($Product['id_product'], $Product['link_rewrite'])));
					$offer->appendChild($url);

					$producer = $xml->createElement('marka_producent',$Product['manufacturer_name']);
					$offer->appendChild($producer);

					$image = Image::getCover($Product['id_product']);					
					if ($image['id_image'])
						//$img_url = $this->getShopHost().'img/p/'.$image['id_product'].'-'.$image['id_image'].'-large.jpg';
						$img_url = $this->getImageLink($Product['link_rewrite'], $image['id_product'].'-'.$image['id_image'], $this->getXmlImageType());
					else
						$img_url = "";
					$image = $xml->createElement('zdjecie',$img_url);
					$offer->appendChild($image);
			}
		}		
		return $xml;
	}

}