<?php 

class mod_pricewars_sklepy24 extends Pricewars {
	protected function generateXML(){
		$link = new Link();
        $xml = new DOMDocument();

		//$Products = Product::getProducts(Language::getIdByIso("pl"), 0, NULL, 'name', 'ASC');
		$Products = $this->getProducts();
		$Categories = $this->_getCategoryTree();

		if($Products&&$Categories){
			$xml->loadXML('<?xml version="1.0" encoding="UTF-8"?>
							<products
								xmlns="http://www.sklepy24.pl"
								xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
								xsi:schemaLocation="http://www.sklepy24.pl http://www.sklepy24.pl/formats/products.xsd"
								date="' . date('Y-m-d') . '">
							</products>');

			$offers = $xml->getElementsByTagName('products');
			$offers = $offers->item(0);

			foreach ($Products AS $Product){

					$offer = $xml->createElement('product');
					$offers->appendChild($offer);

					$id = $xml->createAttribute('id');
					$offer->appendChild($id);

					$id_text = $xml->createTextNode($Product['id_product']);
					$id->appendChild($id_text);
					
					
					$name_cdata = $xml->createCDATASection(strip_tags(html_entity_decode($Product['name'],ENT_COMPAT,"UTF-8"),"<br><li><p><ul><tr>"));
					$name = $xml->createElement('name');
					$name-> appendChild($name_cdata);
					$offer-> appendChild($name);
					

					$url = $xml->createElement('url',htmlspecialchars($link->getProductLink($Product['id_product'], $Product['link_rewrite'])));
					$offer->appendChild($url);

					if ($Product['manufacturer_name']==NULL)
						$brand = $xml->createElement('brand'," ");
					else
						$brand = $xml->createElement('brand',$Product['manufacturer_name']);
					
					$offer->appendChild($brand);


					$categories = $xml->createElement('categories');
					$offer->appendChild($categories);

					$category = $xml->createElement('category',$Categories[$Product['id_category_default']]['path']);
					$categories->appendChild($category);

					$image = Image::getCover($Product['id_product']);
					if ($image['id_image'])
						$img_url = $this->getImageLink($Product['link_rewrite'], $image['id_product'].'-'.$image['id_image'],$this->getXmlImageType());
					else
						$img_url = "";
					$photo = $xml->createElement('photo',$img_url);
					$offer->appendChild($photo);

					$description_cdata = $xml->createCDATASection(strip_tags(html_entity_decode($Product['description_short'],ENT_COMPAT,"UTF-8"),"<br><li><p><ul><tr>"));
					$description = $xml->createElement('description');
					$description->appendChild($description_cdata);
					$offer->appendChild($description);

					$price = $xml->createElement('price', $this->getTaxedPrice($Product));
					$offer->appendChild($price);
			}
		}
		return $xml;
	}

}