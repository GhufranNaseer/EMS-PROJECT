<?php
defined('BASEPATH') OR exit('No direct script access allowed');


function myid ($id)
{
	return md5(config_item('encryption_key').$id);
}

function mycolumn ($columnname='id')
{
	$key = config_item('encryption_key');
	$key = get_instance()->db->escape($key);
		
	return "MD5(CONCAT($key,{$columnname}))";
}

function set_mycolumn_where ($value , $columnname='id' )
{
	$key = get_instance()->db->escape(config_item('encryption_key'));
	$value = get_instance()->db->escape($value);
	
	get_instance()->db
	->where ("MD5(CONCAT($key,{$columnname})) = {$value}",null,false);
}