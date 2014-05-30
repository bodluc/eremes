<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
 * Standard Logaholic application functions - Modern set
 * 
 * This depends on PHP >= 5
 * 
 * Override these function by setting $_ENV['LOGAHOLIC_SYSTEM'] = $system, where
 * $system is a base filename that defines all the same functions. Place
 * customized function set in components/System/$system.php.  This file, or @author davidneimeyer
 * customized version will be loaded via core_factory(), which is in
 * logaholic/core_factory.php
 * 
 * @category  Logaholic
 * @package   Core
 * @author    David Neimeyer <david.neimeyer@cpanel.net>
 * @copyright Copyright (c) 2011, cPanel, Inc., All rights Reserved. (http://cpanel.net) 
 * @license   http://www.cpanel.net/legal-agreements/cpanel-whm-eula.html cPanel EULA
 * @version   0.1.0
 * @link      http://cpanel.net
 * @since     2.7.8.1
 */
$core_class = 'Core/Std.php';
require_once $core_class;

//////////////////////////////////////////////////////////////
// Session related functions

/**
 * Start a PHP session
 * 
 * @return boolean
 */
function Logaholic_sessionStart() {
    $func = substr(__FUNCTION__, 10);
    return @Logaholic_Core_Std::runFunc($func); 
}


/**
 * Logout of the application
 * 
 * @return void
 */

function Logaholic_applicationLogout() {
    return Logaholic_Core_Std::runFunc('applicationLogout');
}

//////////////////////////////////////////////////////////////
// Language and locale functons

/**
 * Legacy function for determining and setting application's language
 *
 * Previous function lang() would determine language for app and set a cookie
 * 
 * That behavior is now emulated by Logaholic_setLang() and a auxillary
 * function, Logaholic_getCurrentLang(), can simply look for the current lang
 * (ie, for ajax based requests)
 * 
 * @return string Language to use
 */
function lang()
{
    return Logaholic_setLang();
}


/**
 * Determine and set application's language
 * 
 * 'english' will be returned if a valid language cannot be determined
 * 
 * @return string Language to use
 */
function Logaholic_setLang()
{
    $func = substr(__FUNCTION__, 10);
    return Logaholic_Core_Std::runFunc($func);
}

function Logaholic_hasLangChanged()
{
    $func = substr(__FUNCTION__, 10);
    return Logaholic_Core_Std::runFunc($func);	
}

/**
 * Determine the current set language
 * 
 * 'english' will be returned if a valid language cannot be determined
 * 
 * @return string Language currently set, otherwise 'english'
 */
function Logaholic_getCurrentLang()
{
    $func = substr(__FUNCTION__, 10);
    return Logaholic_Core_Std::runFunc($func);
}

/**
 * Fetch a list of available languages
 * 
 * @return array An array of strings which itemizes availible languages 
 */
function Logaholic_getAvailableLangs()
{
    $func = substr(__FUNCTION__, 10);
    return Logaholic_Core_Std::runFunc($func);
}

/**
 * Return the proper URL parameter key expected when requesting a language
 * change
 * 
 * @return string URL parameter key that will be passed or parsed during a
 * user-requested language change
 */
function Logaholic_getLanguageRequestKey()
{
    $func = substr(__FUNCTION__, 10);
    return Logaholic_Core_Std::runFunc($func);
}

//////////////////////////////////////////////////////////////
// File system functions

/**
 * Legacy function for determining Logaholic's application directory
 * 
 * @return string The absolute path of Logaholic's application directory
 */
function logaholic_dir()
{
    //return Logaholic_Core_Std::runFunc(__FUNCTION__);
	return Logaholic_logaholicDir();
}

/**
 * Fetch the Logaholic application directory
 * 
 * @return string The absolute path of Logaholic's application directory
 */
function Logaholic_logaholicDir()
{
    $func = substr(__FUNCTION__, 10);
    return Logaholic_Core_Std::runFunc($func);
}
?>