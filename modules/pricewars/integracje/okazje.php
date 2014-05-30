<?php 

class mod_pricewars_okazje extends Pricewars {
	protected function generateXML(){
		$link = new Link();
        $xml = new DOMDocument();
		
		//$Products = Product::getProducts(Language::getIdByIso("pl"), 0, NULL, 'name', 'ASC');
		$Products = $this->getProducts();
		$Categories = $this->_getCategoryTree();

		if($Products&&$Categories){
			$xml->loadXML('<?xml version="1.0" encoding="UTF-8" ?>
							<okazje>
								<offers/>
							</okazje>');

			$offers = $xml->getElementsByTagName('offers');
			$offers = $offers->item(0);

			foreach ($Products AS $Product){

				$offer = $xml->createElement('offer');
				$offers->appendChild($offer);

				$id = $xml->createElement('id',$Product['id_product']);
				$offer->appendChild($id);

				$name = $xml->createElement('name',self::htmlentities($Product['name'])); 
				$offer->appendChild($name);

				$description_cdata = $xml->createCDATASection(strip_tags(html_entity_decode($Product['description_short'],ENT_COMPAT,"UTF-8"),"<br><li><p><ul><tr>"));
				$description = $xml->createElement('description');
				$description->appendChild($description_cdata);
				$offer->appendChild($description);

				$category = $xml->createElement('category',$Categories[$Product['id_category_default']]['path']);
				$offer->appendChild($category);

				//$price = $xml->createElement('price',$this-> getTaxedPrice($Product)); 
				//$offer->appendChild($price);
				
				if (Product::isDiscounted($Product['id_product']))
                {
                    $price = $xml->createElement('price',Product::getPriceStatic($Product['id_product']));
                    $offer->appendChild($price);
                   
                    $price = $xml->createElement('old_price',Product::getPriceStatic($Product['id_product'], true, NULL, 2, NULL, false, false));
                    $offer->appendChild($price);
                   /// oh watever... niech juz ttaj bedzie ten product::getPriceStatic :)
                }
                else {
                    $price = $xml->createElement('price',Product::getPriceStatic($Product['id_product']));
                    $offer->appendChild($price);
                }
				
				

				$url = $xml->createElement('url', htmlspecialchars($link->getProductLink($Product['id_product'], $Product['link_rewrite'])));
				$offer->appendChild($url);

				$producer = $xml->createElement('producer',$Product['manufacturer_name']);
				$offer->appendChild($producer);

				$image = Image::getCover($Product['id_product']);					
				if ($image['id_image'])
					//$img_url = ''.$this->getShopHost().'img/p/'.$image['id_product'].'-'.$image['id_image'].'-large.jpg';
					$img_url = $this->getImageLink($Product['link_rewrite'], $image['id_product'].'-'.$image['id_image'],$this->getXmlImageType());
				else
					$img_url = "";
				$image = $xml->createElement('image',$img_url);
				$offer->appendChild($image);
			}
		}
		return $xml;
	}

}