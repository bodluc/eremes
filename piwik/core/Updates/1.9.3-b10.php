<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @version $Id: 1.9.3-b10.php 7612 2012-12-14 08:56:21Z capedfuzz $
 *
 * @category Piwik
 * @package Updates
 */

/**
 * @package Updates
 */
class Piwik_Updates_1_9_3_b10 extends Piwik_Updates
{
	static function isMajorUpdate()
	{
		return false;
	}
	
	static function update()
	{
		try
		{
			Piwik_PluginsManager::getInstance()->activatePlugin('Annotations');
		}
		catch(Exception $e)
		{
			// pass
		}
	}
}
