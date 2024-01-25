<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Created By http://www.thephpcode.com/blog/view/a-smart-codeigniter-model.html

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed'); 




class MY_Model extends CI_Model 
{ 
    public $table; 
    public function __construct() 
    { 
        parent::__construct(); 
		
		if (method_exists($this , 'getTable'))
		$this->table = $this->getTable ();
		else
        $this->table = get_Class($this); 
		
       // $this->load->database(); 
    } 
	
	function count()
	{
		$tablename = $this->table; 
		return $this->db->count_all_results ($tablename);
	}
	
	function getField ($field , $id , $id_column = 'id')
	{
		$tablename = $this->table;
		
		$data = $this->db
		->where ($id_column , $id)
		->select ($field)
		->limit (1)
		->get ($tablename)
		->row_array ();
		
		return  $data[$field];
	}
	
	function getRow ($id , $id_column = 'id')
	{
		$tablename = $this->table;
		
		$data = $this->db
		->where ($id_column , $id)
		->limit (1)
		->get ($tablename)
		->row_array ();
		
		return  $data;
	}
	
	public function save($data,$tablename="") 
	{ 
		if($tablename=="") 
		{ 
			$tablename = $this->table; 
		} 
		$op = 'update'; 
		$keyExists = FALSE; 
		$fields = $this->db->field_data($tablename); 
	
		foreach ($fields as $field) 
		{ 
			if($field->primary_key==1) 
			{ 
				$keyExists = TRUE;
				if(isset($data[$field->name])) 
				{ 
					$this->db->where($field->name, $data[$field->name]); 
				} 
				else 
				{ 
					$op = 'insert'; 
				} 
			} 
		} 
		if($keyExists && $op=='update') 
		{ 
			$this->db->set($data); 
			$this->db->update($tablename); 
			if($this->db->affected_rows()==1) 
			{ 
				return $this->db->affected_rows(); 
			} 
		} 
		$this->db->insert($tablename,$data); 
		return $this->db->affected_rows(); 
	} 
	
	function search($conditions=NULL,$tablename="",$limit=500,$offset=0) 
	{     
		if($tablename=="") 
		{ 
			$tablename = $this->table; 
		} 
		if($conditions != NULL) 
			$this->db->where($conditions); 
	
		$query = $this->db->get($tablename,$limit,$offset=0); 
		return $query->result(); 
	} 
	
	function insert($data,$tablename="") 
	{ 
		if($tablename=="") 
			$tablename = $this->table; 
		$this->db->insert($tablename,$data); 
		return $this->db->affected_rows(); 
	} 
	
	function update($data,$conditions,$tablename="") 
	{ 
		if($tablename=="") 
			$tablename = $this->table; $this->db->where($conditions); 
		$this->db->update($tablename,$data); 
		return $this->db->affected_rows(); 
	} 
	
	function delete($conditions,$tablename="") 
	{ 
		if($tablename=="") 
			$tablename = $this->table; 
		$this->db->where($conditions); 
		$this->db->delete($tablename); 
		return $this->db->affected_rows(); 
	}
}