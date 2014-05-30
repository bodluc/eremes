<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
class Form
{
   var $values = array();  //Holds submitted form field values
   var $errors = array();  //Holds submitted form error messages
   var $num_errors;   //The number of errors in submitted form

   /* Class constructor */
   function Form(){
      /**
       * Get form value and error arrays, used when there
       * is an error with a user-submitted form.
       */
      if(isset($_SESSION['value_array']) || isset($_SESSION['error_array'])){
         if (isset($_SESSION['value_array'])) { $this->values = $_SESSION['value_array']; } else { $this->values = array(); }
         if (isset($_SESSION['error_array'])) { $this->errors = $_SESSION['error_array']; } else { $this->errors = array(); }
         $this->num_errors = count($this->errors);

         unset($_SESSION['value_array']);
         unset($_SESSION['error_array']);
      }
      else{
         $this->num_errors = 0;
      }
   }

   /**
    * setValue - Records the value typed into the given
    * form field by the user.
    */
   function setValue($field, $value){
      $this->values[$field] = $value;
      $_SESSION['value_array'] = $this->values;
   }

   /**
    * setError - Records new form error given the form
    * field name and the error message attached to it.
    */
   function setError($field, $errmsg){
      $this->errors[$field] = $errmsg;
      $this->num_errors = count($this->errors);
      $_SESSION['error_array'] = $this->errors;
   }
   
   /**
    * value - Returns the value attached to the given
    * field, if none exists, the empty string is returned.
    */
   function value($field){
      if (($this->values) && (array_key_exists($field,$this->values))){
         return htmlspecialchars(stripslashes($this->values[$field]));
      }else{
         return "";
      }
   }

   /**
    * error - Returns the error message attached to the
    * given field, if none exists, the empty string is returned.
    */
   function error($field){
      if(array_key_exists($field,$this->errors)){
         return "<font size=\"2\" color=\"#ff0000\">".$this->errors[$field]."</font>";
      }else{
         return "";
      }
   }

   /* getErrorArray - Returns the array of error messages */
   function getErrorArray(){
      return $this->errors;
   }
   
   function clearErrors(){
     $this->values = array();
     $this->errors = array();
     $this->num_errors = 0;

     unset($_SESSION['value_array']);
     unset($_SESSION['error_array']);
   }
};
 
?>
