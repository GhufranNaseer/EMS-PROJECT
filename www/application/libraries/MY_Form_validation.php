<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation
{

	public $validator_data = array ();
	
	
	public function validate_mobileno ($str)
	{
		//0300-5908540

		if(preg_match("/^[0-9]{4}-[0-9]{7}$/", $str)) 
		{
			return true;
		}
		
		$this->set_message ('validate_mobileno' , '{field} should contain a formated number like this 0300-1111111');
		return false;	
	}

	
	/*
	Returns TRUE for following values:
	2:03:32
	02:03:32
	23:59:59
	15:23 AM
	15:23 am
	09:41 pm
	9:41 PM
	etc...
	*/
	
	public function verify_time_format($value) 
	{
	
	
	if (is_string($value))
		$value .= ":00";
		
	  $this->set_message ('verify_time_format' , '{field} contains Invalid value');

	  $pattern1 = '/^(0?\d|1\d|2[0-3]) : [0-5]\d:[0-5]\d$/';
	  $pattern2 = '/^(0?\d|1[0-2]) : [0-5]\d\s(am|pm)$/i';
	  
	  $pattern3 = '/^(0?\d|1\d|2[0-3]):[0-5]\d:[0-5]\d$/';
	  $pattern4 = '/^(0?\d|1[0-2]):[0-5]\d\s(am|pm)$/i';
	  return preg_match($pattern1, $value) || preg_match($pattern2, $value) ||
	 		 preg_match($pattern3, $value) || preg_match($pattern4, $value);
	}
  
  
	public function not_in_list ($str , $params)
	{
		$this->set_message ('not_in_list' , '{field} can\'t contain ('.str_replace(',' , ' or ' , $params) .')');

		$params = explode( ',' , $params);
		
		foreach ($params as $val)
		{
			if ( strpos($str , $val) !== false)
				return false;
		}
		
		return true;
	}
	
	public function password_check($str)
	{
	   $this->set_message ('password_check' , '{field} should contain at least one small letter, one Upper case letter and one number');
	   
	   if (preg_match('#[0-9]#', $str) && preg_match('#[A-Z]#', $str) && preg_match('#[a-z]#', $str)) {
		 return TRUE;
	   }
	   return FALSE;
	}

	public function set_edit_mood ($str , $param)
	{
		$param = explode( "." , $param );
		
		$this->set_message ('set_edit_mood' , '{field} has invalid value for set_edit_mood validator');
		
		if(!(sizeof($param) == 2 && is_string($param[0]) && is_numeric($param[1]) ))
			return false;
		
		get_instance ()->db
		->where ("{$param[0]} <>" , $param[1]);
		
		return true;
	}
	
	public function validateCC($cc_number)
	{	
		$this->set_message ('validateCC' , '{field} has invalid value');
	   /* Validate; return value is card type if valid. */
	
	   $false = false;
	
	   $card_type = "";
	
	   $card_regexes = array(
	
		  "/^4\d{12}(\d\d\d){0,1}$/" => "visa",
	
		  "/^5[12345]\d{14}$/"       => "mastercard",
	
		  "/^3[47]\d{13}$/"          => "amex",
	
		  "/^6011\d{12}$/"           => "discover",
	
		  "/^30[012345]\d{11}$/"     => "diners",
	
		  "/^3[68]\d{12}$/"          => "diners",
	
	   );
	
	 
	
	   foreach ($card_regexes as $regex => $type) {
	
		   if (preg_match($regex, $cc_number)) {
	
			   $card_type = $type;
	
			   break;
	
		   }
	
	   }
	
	   if (!$card_type) {
		   return $false;
	   }
	
	 
	
	   /*  mod 10 checksum algorithm  */
	
	   $revcode = strrev($cc_number);
	
	   $checksum = 0; 
	
	 
	
	   for ($i = 0; $i < strlen($revcode); $i++) {
	
		   $current_num = intval($revcode[$i]);  
		   if($i & 1) {  /* Odd  position */
			  $current_num *= 2;
		   }
	
			$checksum += $current_num % 10; 
			if ($current_num >  9) {
			   $checksum += 1;
		   }
	   }
	   
	   if ($checksum % 10 == 0) {
		   $this->validator_data['card_type'] = $card_type;
		   return true;
	   } else {
		   return $false;
	   }
	}
	
	public function isValidID ($str,$field)
	{
		if (empty($str))
			return true;
		
		$ci =& get_instance();
		$field = explode ("." , $field);
		
		if ($ci->db->where($field[1] , $str)->count_all_results($field[0]) == 0)
		{
			$this->set_message('isValidID', "{field} is invalid " );
			return false;
		}
		else
		{
			return true;
		}
	}
	
	public function valdateZip ($val)
	{
		$expr = '/^([0-9\-]+)$/';
		
		$this->set_message('valdateZip', "{field} is invalid " );
		
		if (preg_match($expr, $val) == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	// validatedate[optional(d=1),futureonly(d=0),pastonly(d=0),maintainParsedDate(d=1)]
	// 0 = false ---- 1 = true 
	public function validatedate ($str , $param = "")
	{
		$param = explode("," , $param);
		
		$optional = (isset ($param[0]))? (int) $param[0] : 1;
		$futureonly =  (isset ($param[1]))? (int) $param[1] : 0;
		$pastonly = (isset ($param[2]))? (int) $param[2] : 0;
		$maintainParsedDate = (isset ($param[3]))? (int) $param[3] : 1;
		
		if ($maintainParsedDate)
		$this->validator_data['validatedate'] = array ();
			
		if ($optional == 1 && $str == "")
			return true;
		
		$val = @strtotime($str);
		
		
		if ($val === false || $val <= -1)
		{	
			$this->set_message('validatedate', '{field} Invalid');
			
			if ($maintainParsedDate)
			$this->validator_data['validatedate'][] = false;
			
			return false;
		}
		else
		{
			if ($futureonly == 1 && $val <= time())
			{
				$this->set_message('validatedate', '{field} can have only future dates');
				
				if ($maintainParsedDate)
				$this->validator_data['validatedate'][] = false;
				
				return false;
			}
			
			
	
			if ($pastonly == true && $val >= time())
			{
				$this->set_message('validatedate', '{field} can have only past dates');
				
				if ($maintainParsedDate)
				$this->validator_data['validatedate'][] = false;
				
				return false;
			}
			
			if ($maintainParsedDate)
			$this->validator_data['validatedate'][] = $val;
			
			return true;
		}
	}
	
	// beginsWith[+92,+01,+93]
	public function beginsWith ($str,$field)
	{
		if (strpos($field , ",") !== false)
		{
			$field = explode(",",$field);
			foreach ($field as $val)
			{
				$val = trim($val);
				if (strpos($str,$val) === 0)
				{
					return true;
				}
			}
			
			$this->set_message('beginsWith', "%s must begin with " .  implode(" or " , $field) );
		}
		else
		{
			if (strpos($str,$field) === 0)
			{
				return true;
			}
			
			$this->set_message('beginsWith', "%s must begin with {$field}");
		}
		
		return false;
	}
	
	public function username_check($str)
	{
		$string = $str;
		$this->set_message('username_check', '{field} Invalid');
		$expr = '/^([A-Za-z\'\` ]+)$/';
		
		if (preg_match($expr, $string) == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	public function inspect_postcode($postcode) 
	{
	   /*
	   ** This function takes in a string, cleans it up, and returns an array with the following information:
	   ** ["validate"] => TRUE/FALSE
	   ** ["prefix"] => first part of postcode
	   ** ["suffix"] => second part of postcode
	   */
	   
	   
	   $this->set_message('inspect_postcode', '{field} Invalid');
	   	
	   $postcode = str_replace(' ', '', $postcode); // remove any spaces;
	   $postcode = strtoupper($postcode); // force to uppercase;
	   $valid_postcode_exp = "/^(([A-PR-UW-Z]{1}[A-IK-Y]?)([0-9]?[A-HJKS-UW]?[ABEHMNPRVWXY]?|[0-9]?[0-9]?))\s?([0-9]{1}[ABD-HJLNP-UW-Z]{2})$/i";
	
	   // set default output results (assuming invalid postcode):
	   $output['validate'] = FALSE;
	   $output['prefix'] = '';
	   $output['suffix'] = '';
	
	   if (preg_match($valid_postcode_exp, strtoupper($postcode))) {
			 $output['validate'] = TRUE;
			 $suffix = substr($postcode, -3);
			 $prefix = str_replace($suffix, '', $postcode);
			 $output['prefix'] = $prefix;
			 $output['suffix'] = $suffix;
	   }
	   
	   $this->validator_data['inspect_postcode'] = $output;
	   
	   return $output['validate'];
	}
	
	
	public function phone_check($str)
	{
		$string = (string) $str;
		
		$this->set_message('phone_check', '%s Invalid');
		
		if (preg_match('/[^0-9\s-+()]/i', $string))
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	 * Is Unique Overwrite
	 *
	 * Check if the input value doesn't already exist
	 * in the specified database field.
	 *
	 * @param	string	$str
	 * @param	string	$field
	 * @return	bool
	 */
	public function is_unique($str, $field)
	{
		sscanf($field, '%[^.].%[^.]', $table, $field);
		return isset($this->CI->db)
			? ($this->CI->db->limit(1)->where('is_deleted', 0)->get_where($table, array($field => $str))->num_rows() === 0)
			: FALSE;
	}
}