<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
 * Logaholic_Core_Std
 * 
 * @category  Logaholic
 * @package   Core
 * @author    David Neimeyer <david.neimeyer@cpanel.net>, Michael Erkelens <michael@logaholic.com>
 * @copyright Copyright (c) 2011, Logaholic B.V., All rights Reserved. (http://www.logaholic.com) 
 * @version   0.1.0
 * @since     2.7.8.1
 */

class Logaholic_Core_Std {

    /**
     * The singleton object
     * @var Logaholic_Components_System_cPanel_Core
     */
    private static $_instance;
    
    /**
     * The current session id
     * @var string
     */
    protected $sessionId;
    
    /**
     * Available language for the Logaholic application
     * @var array
     */
    protected $availableLangs;
    
    /**
     * Logaholic's application directory
     * 
     * This is be base directory in which all application files (ie, index.php,
     * update.php, etc) are installed 
     * 
     * Will include a trailing slash, ex: /usr/local/logaholic/
     * 
     * @var string
     */
    protected $baseDir;
    
    /**
     * The URL parameter key used for user-requested langauge change in app
     * @var string
     */
    protected $languageRequestKey = 'lang';
    
    /**
     * Cookie key that maps to the current language set in the application
     * @var string
     */
    protected $langCookieKey = 'lg_lang';
    
    /**
     * Default langauge to use in application
     * @var string
     */
    protected $defaultLanguage = 'english';
    
    /**
     * Constructor that enforces singleton pattern
     * 
     * @return Logaholic_Core_Std
     */
    private function __construct()
    {
        $this->logaholicDir();
        return $this;
    }
    
    /**
     * Begin a PHP session, if not previously started
     * 
     * @return boolean True on valid session start or if session was already
     * started, otherwise false
     */
    public function sessionStart()
    {
        if ( ! $this->sessionId ) {
            $return = session_start();
            if ( $return ) {
                $this->sessionId = session_id();
            }
            return $return;
        }
        return true;
    }
    
    /**
     * Log out of Logaholic
     * 
     * @return void
     */
    public function applicationLogout()
    {
        include_once "{$this->baseDir}user_login/process.php";
        $process = new Process;
        $process->procLogout();
        //echo "logged out";
        //exit();
        header("location: ../index.php");
    }
    
    /**
     * Retrieve a list of valid languages
     * 
     * Languages will be listed as they are spelled in English, and all
     * lowercase
     * 
     * @return array Ordinal array of valid languages
     */
    public function getAvailableLangs()
    {
        if (! count($this->availableLangs) ) {
            if ($lhandle = @opendir("{$this->baseDir}languages/")) {       
                $lngs = 0;
                while ($lfile = readdir($lhandle)) {
                    if ($lfile[0] != '.' && (strpos($lfile,".php")!==FALSE)) {
                        $available_langs[$lngs]=str_replace(".php","",$lfile);
                        $lngs++;                
                    }
                }
                sort($available_langs);
            }
            $this->availableLangs = $available_langs;
        }
        return $this->availableLangs;
    }
    
    /**
     * Retrieve the user-requested language string, via a GET/POST request
     * 
     * This is likely to be populated with a user changes their language with
     * the applications settings area or during the install process
     * 
     * @return string Language that user has specified, otherwise null
     */
    protected function requestedLang()
    {
        $lang = null;
        $available_langs = $this->getAvailableLangs();
        $lang_key = $this->getLanguageRequestKey();
        if ( isset($_REQUEST[$lang_key])
            && ( in_array($_REQUEST[$lang_key], $available_langs) == true )
        ) {
            
            $lang = $_REQUEST[$lang_key];
        }
        return $lang; 
    }
    
    /**
     * Return the URL parameter key that is used to denote a user-requested
     * change of language.
     * 
     * @return string A URL parameter key
     */
    public function getLanguageRequestKey()
    {
        return $this->languageRequestKey;
    }
    
    /**
     * Logically determine what language the process is expected to render
     * 
     * @return string Language string that has been requested or is currently
     * set in a cookie, otherwise __CLASS__::defaultLanguage
     */
    public function getCurrentLang()
    {
        $lang = $this->requestedLang(); 
        if ( ! $lang ) {
            $lang_cookie = $this->getLangCookie();
            if ( $lang_cookie ) {
                $lang = $lang_cookie;
            } else {        
                $lang = $this->defaultLanguage;
            }
        }
        return $lang;
    }
    
    /**
     * Fetch the current language cookie, if any
     * 
     * @return string Current language as set in cookie key 'lg_lang', otherwise
     * null
     */
    protected function getLangCookie()
    {
        if ( isset($_COOKIE[$this->langCookieKey])
            && !empty($_COOKIE[$this->langCookieKey])
        ) {
            return $_COOKIE[$this->langCookieKey];
        }
        return null;
    }
    
    /**
     * Set the application's language cookie
     * 
     * @param string $lang English spelling of langauge to use, all lowercase
     * 
     * @return void
     */
    protected function setLangCookie($lang)
    {
        //if ( strpos($_SERVER["PHP_SELF"], "install.php") != true
        //    && $this->getLangCookie() != $lang ) {
		if ($this->getLangCookie() != $lang) {
            setCookie($this->langCookieKey, $lang, (time()+(365*86400)), "/");
        }   
    }
    
    /**
     * Setup proper language for the application
     * 
     * Determine the current language (via REQUEST or COOKIE) and set a cookie,
     * as necessary, and finally load the proper language files.  If the $lang
     * argument is passed, it will forcefully set the language and disregard all
     * detection logic (though, it will still be validated against available
     * language filesets)
     * 
     * If the language cannot be determined or an invalid language is requested,
     * a default will be used ( aka English, aka 'english').
     * 
     * @param string $lang English spelling of language to set
     * 
     * @return string $lang English spelling of language that was set
     */
    public function setLang($lang = null)
    {
        global $available_langs, $conf, $debug;
		
        $available_langs = $this->getAvailableLangs();
        
        $lang = ($lang)? $lang : $this->getCurrentLang();
        
        // one last check to be sure
        if ( !file_exists("{$this->baseDir}languages/".$lang.".php") ) {
            $lang = $this->defaultLanguage;
        }        
        $this->setLangCookie($lang);
        
        include_once "{$this->baseDir}languages/$lang.php";

        return $lang;
    }
	
	/**     
     * Determine if the language is being changed
     * 
     */
    public function hasLangChanged()
    {		
		$a = $this->getLangCookie();
		$b = $this->requestedLang();
		
		if (isset($a) && isset($b)) {
			if ($a != $b) {
				return true;
			}
		}
		return false;
    }

    
    /**
     * Retrieve the full path to the Logaholic root/install/base folder
     * 
     * @return string Base application directory, otherwise false
     */
    public function logaholicDir()
    {
        if (!$this->baseDir) {
            if ( defined('LOGAHOLIC_DIR')
                && file_exists(LOGAHOLIC_DIR ."/version_check.php")
            ) {
                $cd = LOGAHOLIC_DIR .'/';
            } elseif (@file_exists("version_check.php")) {
                //we're in the base folder
                $cd="";
            } else {
                if (@file_exists("../version_check.php")) {
                    //we're in a sub folder
                    $cd="../";
                } else {
                    if (@file_exists("../../version_check.php")) {
                        //we're in a sub sub folder
                        $cd="../../";
                    } else {
                        return false;
                    }            
                }
            }      
            $real_path = realpath($cd."version_check.php");
            $this->baseDir = str_replace("\\", "/", dirname($real_path)) . "/";
        }
        return $this->baseDir; 
    }
    
    /**
     * Dispatch method
     * 
     * Fetches singleton instance of this class and invokes the requested
     * methods with arguments.
     * 
     * @param string $func The function to envoke
     * @param array  $args An array of arguments needed by $func, if any
     * 
     * @return mixed The output of $func
     */
    static function runFunc($func, $args = array())
    {
        $obj = self::getInstance();
        return call_user_func_array(array($obj,$func), $args);
    }
    
    /**
     * Return an instance of this class, creating one if necessary
     * 
     * @return Logaholic_Core_Std
     */
    static function getInstance()
    {
        if ( !isset(self::$_instance) ) {
            $className = __CLASS__;
            self::$_instance = new $className;
        }
        return self::$_instance;
    }
}
?>