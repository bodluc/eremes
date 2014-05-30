<?php 

class mod_pricewars_szoker extends Pricewars {
	protected function generateXML(){
		$link = new Link();
        $xml = new DOMDocument();
		
		//$Products = Product::getProducts(Language::getIdByIso("pl"), 0, NULL, 'name', 'ASC');
		$Products = $this->getProducts();
		$Categories = $this->_getCategoryTree('&gt;');

		if($Products&&$Categories){
			$xml->loadXML('<?xml version="1.0" encoding="UTF-8"?>
								<offers />
						  ');

			$offers = $xml->getElementsByTagName('offers');
			$offers = $offers->item(0);

			foreach ($Products AS $Product){

				$offer = $xml->createElement('offer');
				$offers->appendChild($offer);

				$id = $xml->createElement('id',(int) $Product['id_product']);
				$offer->appendChild($id);
				
				$name_cdata = $xml->createCDATASection(self::htmlentities($Product['name']));
				$name = $xml->createElement('name'); 
				$name->appendChild($name_cdata);
				$offer->appendChild($name);

				$description_cdata = $xml->createCDATASection(strip_tags(!empty($Product['description_short']) ? $Product['description_short'] : $Product['description'] ));
				$description = $xml->createElement('description');
				$description->appendChild($description_cdata);
				$offer->appendChild($description);

				$category = $xml->createElement('category',$Categories[$Product['id_category_default']]['path']);
				$offer->appendChild($category);

				$producer = $xml->createElement('producer',$Product['manufacturer_name']);
				$offer->appendChild($producer);

				$url = $xml->createElement('url', htmlspecialchars($link->getProductLink($Product['id_product'], $Product['link_rewrite'])));
				$offer->appendChild($url);
				
				
				$image = Image::getCover($Product['id_product']);					
				if ($image['id_image'])
					//$img_url = ''.$this->getShopHost().'img/p/'.$image['id_product'].'-'.$image['id_image'].'-large.jpg';
					$img_url = $this->getImageLink($Product['link_rewrite'], $image['id_product'].'-'.$image['id_image'],$this->getXmlImageType());
				else
					$img_url = "";
				$image = $xml->createElement('image',$img_url);
				$offer->appendChild($image);
				
				
				$price = $xml->createElement('price',Product::getPriceStatic($Product['id_product']));
				$offer->appendChild($price);
			}
		}
		return $xml;
	}

}