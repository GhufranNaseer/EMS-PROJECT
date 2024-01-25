<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 CREATE TABLE `user_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `log_query` text,
  `ip_address` varchar(100),
  `createdon` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;
 * */
class Db_log extends CI_Model
{
	public function logQueries ()
	{
		$data = array ();

		foreach ($this->db->queries as $key => $query) 
		{
		   if (stripos( $query , 'UPDATE `users` SET `lastActivity` = ') === false &&
			   stripos( $query , 'INSERT INTO `user_log` (`createdon`, `log_query`, `user_id`) VALUES') === false)
		   if (stripos( $query , 'INSERT') === 0 || stripos( $query , 'UPDATE') === 0 || stripos( $query , 'DELETE') === 0)
		   {
				$data[] = array (
					'user_id' => (isset($this->userdata)) ? $this->userdata->id : NULL,
					'log_query' => $query ,
					'ip_address' => $this->input->ip_address(),
					'createdon' => date('Y-m-d H:i:s')
		   		);
		   }
        }
		
		if (sizeof($data) > 0)
		$this->db
		->insert_batch('user_log', $data);
	}
}