<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @version $Id: 1.9.3-b8.php 7535 2012-11-24 08:01:10Z capedfuzz $
 *
 * @category Piwik
 * @package Updates
 */

/**
 * @package Updates
 */
class Piwik_Updates_1_9_3_b8 extends Piwik_Updates
{
	static function getSql($schema = 'Myisam')
	{
		return array(
			// ignore existing column name error (1060)
			'ALTER TABLE '.Piwik_Common::prefixTable('site')
				. " ADD COLUMN excluded_user_agents TEXT NOT NULL AFTER excluded_parameters" => 1060,
		);
	}
	
	static function update()
	{
		// add excluded_user_agents column to site table
		Piwik_Updater::updateDatabase(__FILE__, self::getSql());
	}
}
