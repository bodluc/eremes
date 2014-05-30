<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;
	
class panelsocial_rms extends Module
{
	public function __construct()
	{
		$this->name = 'panelsocial_rms';
		$this->tab = 'front_office_features';
		$this->version = '1.0';

		parent::__construct();

		$this->displayName = $this->l('Panel boczny Facebook');
		$this->description = $this->l('Panel boczny Facebook, Google, Tweeter');
	}
	
	public function install()
	{
		return (parent::install() && $this->registerHook('displayHeader') && $this->registerHook('displayLeftColumn'));
	}
	
	public function uninstall()
	{
		//Delete configuration			
		return (Configuration::deleteByName('blocksocial_facebook') AND Configuration::deleteByName('blocksocial_twitter') AND Configuration::deleteByName('blocksocial_rss') AND parent::uninstall());
	}
	
	public function getContent()
	{
		// If we try to update the settings
		$output = '';
		if (isset($_POST['submitModule']))
		{	
			Configuration::updateValue('facebookpage_url', (($_POST['facebookpage_url'] != '') ? $_POST['facebookpage_url']: ''));				
			$output = '<div class="conf confirm">'.$this->l('Configuration updated').'</div>';
		}
		return '
		<h2>'.$this->displayName.'</h2>
		'.$output.'
		<form action="'.Tools::htmlentitiesutf8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset class="">				
				<label for="facebook_url">'.$this->l('Facebook Page URL: ').'</label>
				<input type="text" id="facebookpage_url" size="150" name="facebookpage_url" value="'.Tools::safeOutput((Configuration::get('facebookpage_url') != "") ? Configuration::get('facebookpage_url') : "").'" />
				<div class="clear">&nbsp;</div>		
								
				<br /><center><input type="submit" name="submitModule" value="'.$this->l('Update settings').'" class="button" /></center>
			</fieldset>
		</form>';
	}
	
	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS(($this->_path).'panel.css', 'all');
                $this->context->controller->addJS($this->_path.'js/panelsocial.js');
	}
		
	public function hookDisplayLeftColumn()
	{
		global $smarty;

		$smarty->assign(array(
			'facebookpage_url' => Configuration::get('facebookpage_url'),
                    'dirImg' => _PS_BASE_URL_.'/modules/'.$this->name.'/img'
		));
		return $this->display(__FILE__, 'panelsocial_rms.tpl'); 
	}
}
?>
