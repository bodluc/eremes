<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @version $Id: 1.10-b4.php 7763 2013-01-16 04:37:59Z matt $
 *
 * @category Piwik
 * @package Updates
 */

/**
 * @package Updates
 */
class Piwik_Updates_1_10_b4 extends Piwik_Updates
{
	static function isMajorUpdate()
	{
		return false;
	}
	
	static function update()
	{
		try
		{
			Piwik_PluginsManager::getInstance()->activatePlugin('MobileMessaging');
		}
		catch(Exception $e)
		{
			// pass
		}
	}
}