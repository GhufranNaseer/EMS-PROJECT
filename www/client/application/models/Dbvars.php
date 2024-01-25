<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Create table like following for this class to work with
/*
CREATE TABLE IF NOT EXISTS `variables` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(200) NOT NULL,
 `val` text NOT NULL,
 `createdon` datetime NOT NULL,
 PRIMARY KEY (`id`)
)
*/

class Dbvars extends CI_Model
{
	const tbl_dbvar = "variables";
	
	
	function getVar ($name , $default = NULL ,$doDefultOnEmptyString = false)
	{
		$r = $this->db
		->where('name',$name)
		->get(self::tbl_dbvar);
		
		if ($r->num_rows () === 0)
		{
			return $default;
		}
		
		$row = $r->row_array (); 
		$val = @unserialize (  base64_decode($row['val']) );
		
		if ($doDefultOnEmptyString && is_string($val) && ( $val = trim($val)) === '')
			return $default;
			
		return $val;
	}
	
	
	function getVarWithMyWhere ($where)
	{	
		$r = $this->db
		->where($where)
		->get(self::tbl_dbvar);
		
		
		if ($r->num_rows () === 0)
		{
			return false;
		}
		
		$row = $r->row_array ();
		$row['val'] = @unserialize (  base64_decode($row['val']) );	
		return $row;
	}
	
	function setVar ($name , $val)
	{
		$val = base64_encode(serialize($val));
		
		$r = $this->db
		->where('name',$name)
		->get(self::tbl_dbvar);
		
		$data = array (
		'name' => $name , 
		'val' => $val,
		'createdon' => date('Y-m-d H:i:s')
		);
		
		if ($r->num_rows () == 0)
		{
			$this->db
			->set($data)
			->insert(self::tbl_dbvar);
			return $this->db->insert_id ();
		}
		else
		{
			$this->db
			->where('name',$name)
			->set($data)
			->update(self::tbl_dbvar);
			
			$row = $r->row_array();
			return $row['id'];
		}
	}
	
	
	
	function unsetVar ($name)
	{
		$this->db
		 ->where('name',$name)
		 ->delete(self::tbl_dbvar);
		  
		return $this->db->affected_rows ();
	}
}