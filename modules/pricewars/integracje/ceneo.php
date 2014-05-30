<?php 

class mod_pricewars_ceneo extends Pricewars {
	protected function generateXML(){
		$link = new Link();
        $xml = new DOMDocument();

		//$Products = Product::getProducts(Language::getIdByIso("pl"), 0, NULL, 'name', 'ASC');
		$Products = $this->getProducts();
		$Categories = $this->_getCategoryTree();				
		
		if($Products&&$Categories){
			
			$xml->loadXML('<?xml version="1.0" encoding="UTF-8"?>
						   <!DOCTYPE pasaz:Envelope SYSTEM "loadOffers.dtd">
							<pasaz:Envelope xmlns:pasaz="http://schemas.xmlsoap.org/soap/envelope/">
							 <pasaz:Body>
							  <loadOffers xmlns="urn:ExportB2B">
							   <offers/>
							  </loadOffers>
							 </pasaz:Body>	
							</pasaz:Envelope>');

			$offers = $xml->getElementsByTagName('offers');
			$offers = $offers->item(0);

			foreach ($Products AS $Product){
				
				$offer = $xml->createElement('offer');
				$offers->appendChild($offer);

				$id = $xml->createElement('id',$Product['id_product']);
				$offer->appendChild($id);
				
				//to avoid entity warning
				//$productName = str_replace("&", "&amp;", $Product['name']);
				//again. one below is better ;)
				$name = $xml->createElement('name', self::htmlentities($Product['name'])); 
				$offer->appendChild($name);

				$price = $xml->createElement('price',$this-> getTaxedPrice($Product)); 
				$offer->appendChild($price);

				$url = $xml->createElement('url',htmlspecialchars($link->getProductLink($Product['id_product'], $Product['link_rewrite'])));
				$offer->appendChild($url);

				$categoryId = $xml->createElement('categoryId',$Categories[$Product['id_category_default']]['path']);
				$offer->appendChild($categoryId);

				$description_cdata = $xml->createCDATASection(strip_tags(html_entity_decode($Product['description_short'],ENT_COMPAT,"UTF-8"),"<br><li><p><ul><tr>"));
				$description = $xml->createElement('description');
				$description->appendChild($description_cdata);
				$offer->appendChild($description);

				$image = Image::getCover($Product['id_product']);					
				if ($image['id_image'])
					//$img_url = $this->getShopHost().'img/p/'.$image['id_product'].'-'.$image['id_image'].'-large.jpg';
					$img_url = $this->getImageLink($Product['link_rewrite'], $image['id_product'].'-'.$image['id_image'],$this->getXmlImageType());
				else
					$img_url = "";
					
				$image = $xml->createElement('image',$img_url);
				$offer->appendChild($image);
				
				$attributes = $xml->createElement('attributes');
				$offer->appendChild($attributes);

				$attribute = $xml->createElement('attribute');
				$attributes->appendChild($attribute);

				$name = $xml->createElement('name','Producent');
				$attribute->appendChild($name);

				$value = $xml->createElement('value',$Product['manufacturer_name']);
				$attribute->appendChild($value);
				
				/// ean13

				$attribute = $xml->createElement('attribute');
				$attributes->appendChild($attribute);

				$name = $xml->createElement('name', 'EAN');
				$attribute->appendChild($name);

				$value = $xml->createElement('value', $Product["ean13"]);
				$attribute->appendChild($value);
				/// eof ean13;
				
				/// ean13
				$attribute = $xml->createElement('attribute');
				$attributes->appendChild($attribute);

				$name = $xml->createElement('name', 'Kod producenta');
				$attribute->appendChild($name);

				$value = $xml->createElement('value', $Product["reference"]);
				$attribute->appendChild($value);
				/// eof ean13;
			

				/// hurtownie ACTION S.A. - Dla sklepów zintegrowanych z programem Cennik-Offline
				if(isset($Product["id_cennik_offline"])) {
					$attribute = $xml->createElement('attribute');
					$attributes->appendChild($attribute);

					$name = $xml->createElement('name','Kod hurtowni');
					$attribute->appendChild($name);

					$value = $xml->createElement('value',$Product["id_cennik_offline"]);
					$attribute->appendChild($value);
				}
				/// eof hurtownie;
				
				$availability = $xml->createElement('availability', $Product['quantity']);
				$offer->appendChild($availability);

			}
		}
		return $xml;
	}

}