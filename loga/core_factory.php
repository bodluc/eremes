<?php 
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
 * This is simply a way to dynamically set core application functions.
 * 
 * Until the application is mostly OO (with various design patterns), this is
 * the simplist, most maintainable way to add custom functionality without going
 * crazy.
 * 
 * Anyone implementing their own customized application functions should work
 * off of function set in Core/Std.php (it serves as a canonical reference for
 * future development)
 */
if ( !function_exists('core_factory') ) {
    
    /**
     * Load proper function set depending on PHP and ENV environment the current
     * process has.
     * 
     * @return void
     */
    function core_factory()
    {
        if (!defined('LOGAHOLIC_DIR')) {
            define( 'LOGAHOLIC_DIR', realpath(dirname(__FILE__)) );
        }
        
        if ( version_compare(PHP_VERSION, '5.0.0', '>=') ) {
            $sys = (isset($_ENV['LOGAHOLIC_SYSTEM'])) ? $_ENV['LOGAHOLIC_SYSTEM'] : 'Std';
            if ($sys == 'Std') {
                include_once 'Core.php';
            } else {
                $file = LOGAHOLIC_DIR . "/components/System/$sys.php";
                if (file_exists($file) ) {
                    include_once $file;
                } else {
                    # oops, they didn't follow the convention
                    include_once 'Core.php';    
                }
            }
        } else {
            include_once 'Core/Legacy.php';
        }
    }
    
    core_factory();
}
?>