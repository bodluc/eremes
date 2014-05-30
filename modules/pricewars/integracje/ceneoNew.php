<?php 

class ceneoNew extends Pricewars {
	static public function newCeneo(){
		
		$link = new Link();
        $xml  = new DOMDocument();

		$Products = $this->getProducts();
		$Categories = $this->_getCategoryTree();				

		// Generate cool Manufacturers array :)
		
		$Manufacturers_temp = Manufacturer::getManufacturers();
		$Manufacturers = array();
		foreach ($Manufacturers_temp as $m)
			$Manufacturers[$m['id_manufacturer']] = $m['name'];

		// OMg... heeeeeavy, time to free up some space... not that Domdocument is havy as it is.... Smarty would be better...
		
		unset($Manufacturers_temp); /// it wont do a shit anyway... just looks nice. Give me Garbarge collectors! give php5.3 everywhere <3 !!
		
		//if($Products && $Categories && $Manufacturers){
		if($Products && $Categories){
			
			$xml->loadXML('<?xml version="1.0" encoding="utf-8"?>
							<offers xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="1">
								<group name="other">
								</group>
							</offers>
							');

			$group = $xml->getElementsByTagName('group');
			$group = $group->item(0);

			foreach ($Products AS $Product){
				if(!isset($this->mappings[$Product['id_category_default']]) OR !isset($this->CeneoCategories[$this->mappings[$Product['id_category_default']]])) continue;
					
			    $availability = $Product['quantity'] > 0 ? 1 : 0;

				$o = $xml->createElement('o');
				
				$id = $xml->createAttribute ('id');
				$id->value = $Product['id_product'];
				$o->appendChild($id);
				
				$url = $xml->createAttribute ('url');
				$url->value = htmlspecialchars($link->getProductLink($Product['id_product'], $Product['link_rewrite']));
				$o->appendChild($url);
				
				$price = $xml->createAttribute ('price');
				$price->value = $this-> getTaxedPrice($Product);
				$o->appendChild($price);

				$avail = $xml->createAttribute ('avail');
				$avail->value = 1;
				$o->appendChild($avail);
				
				$set = $xml->createAttribute ('set');
				$set->value = '0';
				$o->appendChild($set);
				
				$weight = $xml->createAttribute ('weight');
				$Product['weight'] = floatval(str_replace(',', '.', $Product['weight']));
				$weight->value = ($Product['weight'] ? $Product['weight']:'0.0');
				$o->appendChild($weight);
				
				$stock = $xml->createAttribute ('stock');
				$stock->value = 'small';
				$o->appendChild($stock);
				

				$name = $xml->createElement('name');
				$o->appendChild($name);
				
				$cdata_name = $xml->createCDATASection(self::htmlentities($Product['name']));
				$name->appendChild($cdata_name);
				
				
				// description
				$desc = $xml->createElement('desc');
				$o->appendChild($desc);
				
				
				$Product['description_short'] = trim($Product['description_short']);
				$cdata_desc = $xml->createCDATASection(self::htmlentities(empty($Product['description_short']) ? $Product['description'] : $Product['description_short']));
				$desc->appendChild($cdata_desc);

				// category
				$cat = $xml->createElement('cat');
				$o->appendChild($cat);
				
				$cdata_cat = $xml->createCDATASection($this->CeneoCategories[$this->mappings[$Product['id_category_default']]]);
				$cat->appendChild($cdata_cat);
				
				
				/// Start atrybutów
				$attrs = $xml->createElement('attrs');
				$o->appendChild($attrs);
				
				// Atrybuty New
				$a = $xml->createElement('a');
				$attrs->appendChild($a);
				
				$cdata_a = $xml->createCDATASection( isset($Manufacturers[$Product['id_manufacturer']]) ? $Manufacturers[$Product['id_manufacturer']] : NULL);
				$a->appendChild($cdata_a);
				
				$name = $xml->createAttribute ('name');
				$a->appendChild($name);
				$name->value = 'Producent';
				
				// Atrybuty New
				$a = $xml->createElement('a');
				$attrs->appendChild($a);
				
				$cdata_a = $xml->createCDATASection($Product["reference"]);
				$a->appendChild($cdata_a);
				
				$name = $xml->createAttribute ('name');
				$a->appendChild($name);
				$name->value = 'Kod_producenta';
				
				// Atrybuty New :: EAN
				$a = $xml->createElement('a');
				$attrs->appendChild($a);
				
				$cdata_a = $xml->createCDATASection($Product["ean13"]);
				$a->appendChild($cdata_a);
				
				$name = $xml->createAttribute ('name');
				$a->appendChild($name);
				$name->value = 'EAN';
				
				
				// Atrybuty New  :: Dla hurtowni ACTION S.A. W sklepach zintegrowanych z prgramem Cennik-offline.
				if(isset($Product["id_cennik_offline"])) {
					$a = $xml->createElement('a');
					$attrs->appendChild($a);

					$cdata_a = $xml->createCDATASection($Product["id_cennik_offline"]);
					$a->appendChild($cdata_a);
					
					$name = $xml->createAttribute ('name');
					$a->appendChild($name);
					$name->value = 'Kod hurtowni';
				}
				
									
				// images
				$image = Image::getCover($Product['id_product']);					
				if ($image['id_image']) {
					//$img_url = ''.$this->getShopHost().'img/p/'.$image['id_product'].'-'.$image['id_image'].'-large.jpg';
					$img_url = $this->getImageLink($Product['link_rewrite'], $image['id_product'].'-'.$image['id_image'],$this->getXmlImageType());
					
					$imgs = $xml->createElement('imgs');
					$o->appendChild($imgs);
					
					$main = $xml->createElement('main');
					$imgs->appendChild($main);
					
					$url = $xml->createAttribute ('url');
					$main->appendChild($url);
					
					$url->value = $img_url;
				}
				// eof::images
				
				
				
				$group->appendChild($o);
				//echo $xml->saveXML(); die();

			}
		}
		return $xml;
	}

}