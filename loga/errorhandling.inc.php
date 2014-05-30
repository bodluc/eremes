<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
class clsErrorHandle
{
  var $error_log_filename;

  var $LOG_ERRORS_TO_FILE;
  var $LOG_WARNINGS_TO_FILE;
  var $LOG_NOTICES_TO_FILE;

  function clsErrorHandle()
  {
    if (file_exists("version_check.php")) {
        //we're in the base folder
        $cd="";
    } else {
        if (file_exists("../version_check.php")) {
            //we're in a sub folder
            $cd="../";
        } else {
            if (file_exists("../../version_check.php")) {
                //we're in a sub sub folder
                $cd="../../";
            } else {
                //we're lost
            }            
        }
    }      
    $filename= "";  
    $real_path = realpath($cd."version_check.php");
    $path = dirname($real_path);
    //echo "the path is".$path;
    if ($path=="") {
        //do this in case realpath fails
        if (file_exists($_SERVER['DOCUMENT_ROOT']."/files/index.html")==FALSE) {
            //we're lost 
        } else {
            $filename=$_SERVER['DOCUMENT_ROOT']."/files/Error_log.log";
        }

    } else {

        $filename=$path. '/files/Error_log.log';
       
    }
    //echo "the filesname is".$filename;
    if ($filename!="") {
       //chmod($filename, 0666);
        //Echo "Writing an error to $filename";
        $this->setFilename($filename);
    } else {
        //echo "file doesn't exist";   
    }
    $this->setFlags();
    set_error_handler( array( &$this, 'ERROR_HANDLER' ) );
    register_shutdown_function( array( &$this, 'clsErrorHandleDestruct' ) );
  } //END OF ErrorHandle()
  
  function clsErrorHandleDestruct()
  {
  }
  
  /**
  * The error handling routine set by set_error_handler()
  *
  * @param string $error_type The type of error being handled.
  * @param string $error_msg The error message being handled.
  * @param string $error_file The file in which the error occurred.
  * @param integer $error_line The line in which the error occurred.
  * @param string $error_context The context in which the error occurred.
  * @return Boolean
  * @access public
  * @see clsErrorHandle()
  */
  function ERROR_HANDLER($error_type, $error_msg, $error_file, $error_line, $error_context)
  { 
  	   
    // Are we supressing this error type?  If so, skip it.
    if ((error_reporting() & $error_type) == 0) { 
        return;
    } else {
        $error_time=@date("Y-m-d H:i:s - ", time()); 
        $error_msg=$error_time.$error_msg;    
    }
    
   $display_errors = ini_get('display_errors'); 
   switch( $error_type )
   {

     case E_ERROR:
     case E_USER_ERROR:      
			 if( $this->LOG_ERRORS_TO_FILE )
			 {
			   if( $this->error_log_filename == '' )
			   {
			     @error_log( $error_msg . ' (error type ' . $error_type . ' in ' . $error_file . ' on line ' . $error_line . ') ' . chr(10), 0);
			   } else  {
			     @error_log( $error_msg . ' (error type ' . $error_type . ' in ' . $error_file . ' on line ' . $error_line . ') ' . chr(10), 3, $this->error_log_filename);
			   }
			 }
	     ob_start();
	     echo $error_msg,' (error type ',$error_type,' in ',$error_file,' on line ',$error_line,')<br />';
	     $this->showBacktrace();
	     $err_msg = ob_get_clean();
    	     
	     if ($display_errors) {
    	     echo "<hr /><div class='err_msg'>{$err_msg}</div>";
	     } else {
	          $stderr_stream = fopen('php://stderr', 'w');
                 if (is_resource($stderr_stream)) {
                     fwrite($stderr_stream, $err_msg);
                     fclose($stderr_stream);
                 }
             }
	     
	     exit;
         break;
     case E_WARNING:     
     case E_USER_WARNING:
         if( $this->LOG_WARNINGS_TO_FILE )
         {
           if( $this->error_log_filename == '' )
           {
             @error_log( $error_msg . ' (error type ' . $error_type . ' in ' . $error_file . ' on line ' . $error_line . ')' . chr(10), 0);
           } else  {
             @error_log( $error_msg . ' (error type ' . $error_type . ' in ' . $error_file . ' on line ' . $error_line . ')' . chr(10), 3, $this->error_log_filename);
           }
         }
         
         ob_start();
         echo $error_msg,' (error type ',$error_type,' in ',$error_file,' on line ',$error_line,')<br />';
    	 $err_msg = ob_get_clean();
    	     
	     if ($display_errors) {
    	     echo $err_msg;     
	     } else {
                 $stderr_stream = fopen('php://stderr', 'w');
                 if (is_resource($stderr_stream)) {
                     fwrite($stderr_stream, $err_msg);
                     fclose($stderr_stream);
                 }
	     }
         break;
     case E_NOTICE:      
     case E_USER_NOTICE:
         if( $this->LOG_NOTICES_TO_FILE )
         {
           if( $this->error_log_filename == '' )
           {
             @error_log( $error_msg . ' (error type ' . $error_type . ' in ' . $error_file . ' on line ' . $error_line . ')' . chr(10), 0);
           } else  {
             @error_log( $error_msg . ' (error type ' . $error_type . ' in ' . $error_file . ' on line ' . $error_line . ')]' . chr(10), 3, $this->error_log_filename);
           }
         }
         ob_start();
         echo $error_msg,' (error type ',$error_type,' in ',$error_file,' on line ',$error_line,')<br />';
    	 $err_msg = ob_get_clean();
    	     
	     if ($display_errors) {
    	     echo $err_msg;     
	     } else {
	         $stderr_stream = fopen('php://stderr', 'w');
                 if (is_resource($stderr_stream)) {
                     fwrite($stderr_stream, $err_msg);
                     fclose($stderr_stream);
                 }
             }
         break;
     
   } 
     
   return true;
   
  } //END OF ERROR_HANDLER()
  
  function setFilename( $filename )
  {
      $this->error_log_filename = $filename; //Error Log Filename
  } 
  
  function setFlags( $error_flag = true, $warning_flag = true, $notice_flag = true)
  {
    $this->LOG_ERRORS_TO_FILE = $error_flag;           //Log errors to file?
    $this->LOG_WARNINGS_TO_FILE = $warning_flag;          //Log warnings to file?
    $this->LOG_NOTICES_TO_FILE = $notice_flag;           //Log notices to file?
  }//END OF setFlags()
  
  function restoreHandler()
  {
      restore_error_handler();
  }
  
  
  function returnHandler()
  {
    set_error_handler( array( &$this, 'ERROR_HANDLER' ) );
  }

  function showBacktrace()
  {
		$error_stack = @debug_backtrace();
		echo "Call Stack:<br>";
		foreach ($error_stack as $number => $error) {
			if (is_array($error) && isset($error["line"]) && ($number > 0)) {
				echo "file: ". $error["file"] . ", function: ".$error["function"].", line: ".$error["line"]."<br>";
			}
		}
  }
}
?>
