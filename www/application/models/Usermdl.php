<?php


class Usermdl extends MY_Model {
	private $current_user_group = NULL;
	function getTable () {
		return 'users';
	}

	
	function getAllRights ($chbox_name_only = false)
	{
		$dirpath = APPPATH . 'controllers';
		$dir = opendir ($dirpath);
		
		$returnArray = array ();
		$di = new RecursiveDirectoryIterator($dirpath);
		foreach (new RecursiveIteratorIterator($di) as $dir_file) {
		//while ($file = readdir ($dir)) {
			$file = pathinfo($dir_file)['filename'] . '.' . pathinfo($dir_file)['extension'];

			if ($file=='.'|| $file=='..' ||
				$file == 'Services.php' ||
				$file == 'Welcome.php' ||
				$file == 'Portal.php' ||
				$file == 'My_funcs.php'||
				$file == 'Cron_email.php')
				continue;

			$fileInfo = pathinfo($file);
			if (!(isset ($fileInfo['extension']) && $fileInfo['extension'] == 'php'))
				continue;
				
			
			$filename = $fileInfo['filename'];
			
			
		
			$filename = str_replace('_',' ' , $filename);
			$filename = ucwords($filename);
			
			if ($chbox_name_only)
			$item =  str_replace('_','' , $file);
			else
			$item = array (
			'cbname' => $filename,
			'filename' => str_replace('_','' , $file)
			);
			
			$returnArray[] = $item ;
		}
		
		return $returnArray;
	}
	
	function getReportRight ($report , $right_str = null) {
		if (!is_string($right_str))
		$right_str = $this->userdata->reports_rights;
		
		
		return (stripos($right_str , ';'.$report.';') !== false || $right_str == '*');
	}

	
	function getUserImage ($userid = NULL , $width = 100, $height = 100) {
		$userimage = null;
		if (is_numeric($userid))
		{
			$r = $this->db
			->select ('user_image')
			->where ('id' , $userid)
			->get ('users');
			
			if ($r->num_rows () == 0)
				$userimage = $r->row()->user_image;
			else
				$userimage = false;
		}
		else
		{			
			$userimage = $this->userdata->user_image;
		}
		

		if ($userimage===false)
			return false;
		
		
		$userimage = is_string($userimage)? $userimage : 'default.png';
		return $this->common->getImageURL ($userimage , $width , $height ,  "uploads/profile");
	}

}