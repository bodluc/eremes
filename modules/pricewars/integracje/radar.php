<?php 

class mod_pricewars_radar extends Pricewars {
	protected function generateXML(){
		$link = new Link();
        $xml = new DOMDocument();
		
		//$Products = Product::getProducts(Language::getIdByIso("pl"), 0, NULL, 'name', 'ASC');
		$Products = $this->getProducts();
		$Categories = $this->_getCategoryTree();

		if($Products&&$Categories){

			$xml->loadXML('<?xml version="1.0" encoding="UTF-8"?>
						   <radar wersja="1.0">
							 <oferta/>
							</radar>');

			$oferta = $xml->getElementsByTagName('oferta');
			$oferta = $oferta->item(0);


			foreach ($Products AS $Product){


				
				$produkt = $xml->createElement('produkt');
				$oferta->appendChild($produkt);

				$grupa1 = $xml->createElement('grupa1');
				$produkt->appendChild($grupa1);

				$nazwa = $xml->createElement('nazwa',self::htmlentities($Product['name'])); 
				$grupa1->appendChild($nazwa);

				$producent = $xml->createElement('producent',$Product['manufacturer_name']);
				$grupa1->appendChild($producent);

				$description_cdata = $xml->createCDATASection(strip_tags(html_entity_decode($Product['description_short'],ENT_COMPAT,"UTF-8")));
				$opis = $xml->createElement('opis');
				$opis->appendChild($description_cdata);
				$grupa1->appendChild($opis);

				$id = $xml->createElement('id',$Product['id_product']);
				$grupa1->appendChild($id);
				
				$url = $xml->createElement('url',htmlspecialchars($link->getProductLink($Product['id_product'], $Product['link_rewrite'])));
				$grupa1->appendChild($url);

				$image = Image::getCover($Product['id_product']);					
				if ($image['id_image'])
					//$img_url = ''.$this->getShopHost().'img/p/'.$image['id_product'].'-'.$image['id_image'].'-large.jpg';
					$img_url = $this->getImageLink($Product['link_rewrite'], $image['id_product'].'-'.$image['id_image'],$this->getXmlImageType());
				else
					$img_url = "";
				$foto = $xml->createElement('foto',$img_url);
				$grupa1->appendChild($foto);

				$kategoria = $xml->createElement('kategoria',$Categories[$Product['id_category_default']]['path']);
				$grupa1->appendChild($kategoria);

				$cena = $xml->createElement('cena',$this-> getTaxedPrice($Product)); 
				$grupa1->appendChild($cena);
			}
		}
		return $xml;
	}

}