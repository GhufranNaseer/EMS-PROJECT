<?php

defined('BASEPATH') OR exit('No direct script access allowed');
// Create table like following for this class to work with
/*
CREATE TABLE IF NOT EXISTS `temp_value` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `val` text NOT NULL,
 `createdon` datetime NOT NULL,
 PRIMARY KEY (`id`)
)
*/
class Tem_vals extends CI_Model
{
		const tbl_dbvar = "temp_value";
		
		private $remaindata = "+2 hours";
		
		function __construct ()
		{
			$createdon = date('Y-m-d H:i:s');
			$this->db
			->where('createdon <' , $createdon )
			->delete (self::tbl_dbvar);
		}
	
		
		
		function getValue ($id)
		{
			if (!is_numeric($id))
			{
				return false;
			}
			
			$r = $this->db
			->where ('id',$id)
			->get (self::tbl_dbvar);
			
			if ($r->num_rows () === 0)
			{
				return false;
			}
			
			$row = $r->row_array (); 
			
			$row['val'] = base64_decode($row['val']);
			$row['val'] = unserialize($row['val']);
			
			return $row['val'];
		}
		
		
		function getValueWithMyWhere ($where)
		{
			$r = $this->db->where($where,null,false)->get(self::tbl_dbvar);;
			
			if ($r->num_rows () === 0)
			{
				return false;
			}
			
			$row = $r->row_array (); 
			
			$row['val'] = base64_decode($row['val']);
			$row['val'] = unserialize($row['val']);
			
			return $row['val'];
		}
		
		function setValue ($val , $expire = 'default')
		{
			return $this->setVal($val, $expire);
		}
		
		function setVal ($val , $expire = 'default')
		{
			
			$val = base64_encode(serialize($val));
			
			if (($expire = @strtotime($expire)) === false)
			{
				$time = strtotime ($this->remaindata);
			}
			else
			{
				$time = $expire;
			}
			
			
			$time = date('Y-m-d H:i:s' , $time);
				
				
			$data = array (
			'val' => $val,
			'createdon' => $time 
			);	
			
			$this->db
			->set ($data)
			->insert(self::tbl_dbvar);
			
			
			return $this->db->insert_id ();
		}
		
		
		function unsetVar ($id)
		{
			$this->db
			->where('id' , $id )
			->delete (self::tbl_dbvar);
		}
		
		function setValueWithSession ($name , $data)
		{
			$id = $this->setVal($data);
			$this->session->set_userdata ( $name, $id);
			return true;
		}
		
		function getValueWithSession ($name , $default = false)
		{
			$id = $this->session->userdata($name);
			if (is_null($id))
				return $default;
			
			$data = $this->getValue($id);
			if ($data === false)
				return $default;
				
			return $data;
		}
}