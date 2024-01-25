<?php

class Validator 
{
	function validateCC($cc_number)
	{	
	
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
		   return $card_type;
	   } else {
		   return $false;
	   }
	}
	//////
	
	function isValidID ($str,$field)
	{
		$ci =& get_instance();
		$field = explode ("." , $field);
		if ($ci->db->where($field[1] , $str)->count_all_results($field[0]) == 0)
		{
			$ci->form_validation->set_message('isValidID', "{field} is invalid " );
			return false;
		}
		else
		{
			return true;
		}
	}
	
	function valdateZip ($val)
	{
		$expr = '/^([0-9\-]+)$/';
	
		if (preg_match($expr, $val) == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
		
	function validatedate ($str , $optional = true)
	{	
		$ci =& get_instance();
		
		if ($optional && $str == "")
		{
			return true;
		}
		
		$val = strtotime($str);
		
		
		if ($val === false || $val <= -1)
		{	
			$ci->form_validation->set_message('validatedate', '{field} Invalid');
			$ci->validatedate_val = false;
			return false;
		}
		else
		{
			if (isset ($ci->validatedate_futureonly) && $ci->validatedate_futureonly === true && $val <= time())
			{
				$ci->form_validation->set_message('validatedate', '{field} can have only future dates');
				return false;
			}
			
			
	
			if (isset ($ci->validatedate_pastonly) && $ci->validatedate_pastonly === true && $val >= time())
			{
				$ci->form_validation->set_message('validatedate', '{field} can have only past dates');
				return false;
			}
			
			$ci->validatedate_val = $val;
			return true;
		}
	}
	
	
	
	function beginsWith ($str,$field)
	{
		$ci =& get_instance();
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
			
			$ci->form_validation->set_message('beginsWith', "%s must begin with " .  implode(" or " , $field) );
		}
		else
		{
			if (strpos($str,$field) === 0)
			{
				return true;
			}
			
			$ci->form_validation->set_message('beginsWith', "%s must begin with {$field}");
		}
		
	
		return false;
	}
	
	
	function username_check($str)
	{
		$ci =& get_instance();
		$string = $str;
		$ci->form_validation->set_message('username_check', '%s Invalid');
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
	
	
	public  function inspect_postcode($postcode) {
	   $ci =& get_instance();
	   /*
	   ** This function takes in a string, cleans it up, and returns an array with the following information:
	   ** ["validate"] => TRUE/FALSE
	   ** ["prefix"] => first part of postcode
	   ** ["suffix"] => second part of postcode
	   */
	
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
	   
	   $ci->inspect_postcode =  $output;
	  
	   return $output['validate'];
	}
	

	
	function phone_check($str,$optional=false)
	{
		$ci =& get_instance();
		
		$string = (string) $str;
		if (preg_match('/[^0-9\s-+()]/i', $string))
		{
			$ci->form_validation->set_message('phone_check', '%s Invalid');
			return false;
		}
		else
		{
			/*if ($string !== "")
			{
				if (strlen($string) !== 13 || strpos($string , "+") !== 0)
				{
					$ci->form_validation->set_message('phone_check', '%s Invalid. Phone number length must be 13 and it must be in +12xxxxxxxxxx format');
					return false;
				}
			}*/
			
			return true;
		}
	}
}
