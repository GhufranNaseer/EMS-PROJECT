<?php


class Common
{	
	public function __construct ()
	{
		$this->ci =& get_instance ();
	}
	
	// Strat Only for admin
	function userHasRight ($name)
	{
		return (stripos($this->ci->userdata->user_permission , $name) !== false ||$this->ci->userdata->user_permission=='*');
	}
	// End Only for admin
	
	 function preventCache ()
	{
		$this->ci->output->set_header("HTTP/1.0 200 OK");
		$this->ci->output->set_header("HTTP/1.1 200 OK");
		$this->ci->output->set_header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT');
		$this->ci->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
		$this->ci->output->set_header("Cache-Control: post-check=0, pre-check=0");
		$this->ci->output->set_header("Pragma: no-cache");
	}
	
	
	function disallowUnajaxRequests ()
	{
		if (!$this->input->is_ajax_request())
			show_404();
	}
	
	public  function showMsg_backup ($title = "" , $msg = "" , $urlgoback = "")
	{
		$this->ci->load->model ('tem_vals');
		
		$id = $this->ci->tem_vals->setValue (
		array ("title" => $title , 
				"msg" => $msg , 
				"url" =>  $urlgoback )
		, "+10 minutes");
		
		
		$id = md5 ($this->ci->config->item('encryption_key') . $id);
		
		$id  = urlencode($id);
		
		redirect (base_url ("alert.html?e=" . $id)  );
	}
	public  function showMsg ($title = "" , $msg = "" , $urlgoback = "")
	{
		$this->ci->load->library('session');
		$this->ci->session->set_flashdata ('alert_message', json_encode(array(
			"title" => $title ,
			"msg" => $msg ,
		)));

		redirect ($urlgoback);
	}
	
	//$this->common_funcs->getImageURL()
	function getImageURL ($imagename , $width , $height , $realfoldername = "user-uploaded-images" , $baseurl = false)
	{
		$imagedata = pathinfo($imagename);
		$imageurl = $baseurl===false ? base_url () : $baseurl;
		$imageurl .=  '';
		@$imageurl .= "images/{$realfoldername}_{$imagedata['filename']}_{$width}_{$height}.".strtolower($imagedata['extension']);
		return $imageurl;
	}
	
	
	// {$this->common_funcs->mysqlnow()}
	function mysqlnow($dateformate = 'Y-m-d H:i:s' , $datetime = 'now')
	{
		$dateformate = is_null($dateformate) ? 'Y-m-d H:i:s' : $dateformate;
		
		if ($datetime == 'now')
			return  " CONVERT('" . date($dateformate) ."', DATETIME) ";
		
		$datetime = @strtotime($datetime);
		$datetime = ($datetime===false)? time () : $datetime;
		return  " CONVERT('" . date($dateformate , $datetime) ."', DATETIME) ";
	}
	
	
	function time_elapsed_string($ptime , $currenttime , $textAfter = ' ago' , $zeroSecText = '0 seconds')
	{
		echo $etime = $currenttime - $ptime;
	
		if ($etime < 1)
		{
			return $zeroSecText;
		}
	
		$a = array( 365 * 24 * 60 * 60  =>  'year',
					 30 * 24 * 60 * 60  =>  'month',
						  24 * 60 * 60  =>  'day',
							   60 * 60  =>  'hour',
									60  =>  'minute',
									 1  =>  'second'
					);
		$a_plural = array( 'year'   => 'years',
						   'month'  => 'months',
						   'day'    => 'days',
						   'hour'   => 'hours',
						   'minute' => 'minutes',
						   'second' => 'seconds'
					);
	
		foreach ($a as $secs => $str)
		{
			$d = $etime / $secs;
			if ($d >= 1)
			{
				$r = round($d);
				return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . $textAfter;
			}
		}
	}

	function query_to_total_rows ($query)
	{
		$result = $this->ci->db->query ($query);
		$total = $result->row_array ();
		if (!isset ($total['total']))
		{
			return NULL;
		}
		
		return $total['total'];
	}
	
	public function get_auto_increment ( $tablename  )
	{
		$r = $this->ci->db->query (" SHOW TABLE STATUS LIKE '{$tablename}' ");
		$row = $r->row_array ();
		$unqueeId = $row['Auto_increment'];
		return $unqueeId;
	}
	
	public function echoTitle ($title = NULL , $domain = NULL)
	{
		if (is_null($domain) && defined("PROJECT_NAME"))
		{
			$domain = PROJECT_NAME;
		}
		elseif (!defined("PROJECT_NAME") || !is_string($title))
		{
			$domain = "mywebsite.com";
		}
		

		if (!is_null($title))
		{
			$ptitle = $title;
		}
		
		
   	    $domainname = "{$domain}";
		
		if (isset ($ptitle)){
		  $ptitle =  htmlentities($ptitle);
		} else{
		  $ptitle = $this->ci->uri->uri_string();
		  $ptitle = basename($ptitle);
		  $ptitle = str_replace(".html", "", $ptitle);
		  $ptitleparts = explode("-", $ptitle);
		  
		  if(is_numeric( $ptitleparts[sizeof($ptitleparts) - 1 ])){
			  $ptitleparts[sizeof($ptitleparts) - 1 ] = "";
			  $ptitle = implode(" ", $ptitleparts);
		  }
		  $ptitle = str_replace("-", " ", $ptitle);
		  $ptitle = trim($ptitle);
		  $ptitle = htmlentities(ucwords($ptitle));
		}
		
		if ($ptitle == "" || strtolower($ptitle) == "index"){
		 
		  echo "Welcome to {$domainname}";
		} else{
		  if (isset ($ptitlesuffix)){
			  $ptitle .= "{$ptitlesuffix}";
		  }
		  echo "{$ptitle} - {$domainname}";
		} 
	}
	
	
	### Start Commen Functions

	public function goBack ($elsewhere = '')
	{
		redirect ($this->getGoBackLink($elsewhere));
	}
	
	public function getGoBackLink ($elsewhere = '')
	{
		if ( !is_null($this->ci->input->server('HTTP_REFERER')) )
		{
			return ($this->ci->input->server('HTTP_REFERER'));
		}
		else
		{
			return (site_url ($elsewhere));
		}
	}
	
	public function getFVError ()
	{
		$parts = explode("\n"  , validation_errors());	
		
		$error = "";
		if ( isset ($parts[0]) )
		{
			$error = htmlentities( strip_tags( $parts[0]) ) ;
		}
		return $error;
	}
	
	public function getFVJsonErrors ()
	{
		$parts = explode("\n"  , trim(validation_errors()));	
		
		$error = array ();
		$error['errors'] = array ();
		
		foreach ($parts as $val)
		{
			
			$val_part = explode('*' , $val);
			
			$item = array ();
			$item['field'] = strip_tags($val_part[0]);
			$item['msg'] = strip_tags ($val_part[1]);
			
			$error['errors'][] = $item;
		}
		
		return '('.json_encode($error).')';
	}
	

	public function doError ($funcarg  , $conditionerror , $returnBol = false)
	{
		if ($funcarg === 0)
		{
			return $returnBol;
		}
		else
		{
			exit ($conditionerror); 
		}
	}
	
	public function get_from_options_to_array ($optiontags)
	{
		$list_counter = $optiontags;
		$list_counter = preg_replace ("<option value=\"([0-9]+)\">" , "" , $list_counter );
		$list_counter = str_replace("<>" , "" , $list_counter);
		$list_counter = str_replace("</option>" , "%sep%" , $list_counter);
		$list_counter = explode("%sep%" , $list_counter);	
		array_splice( $list_counter , sizeof ($list_counter) - 1 , 1);
		
		return $list_counter;
	}
	
	
	public function doRequest ($url , $data , $method = "POST" , $moreHeaders = NULL)
	{
		// use key 'http' even if you send the request to https://...
		$moreHeadersData = "";
		if (is_array($moreHeaders) && sizeof ($moreHeaders) > 0)
		{
			$moreHeadersData = implode("\r\n" , $moreHeaders);
		}
		
		
		if ($moreHeadersData == "")
		{
			$moreHeadersData = "Content-type: application/x-www-form-urlencoded\r\n";
		}
		
		$options = array(
			'http' => array(
				'header'  => $moreHeadersData,
				'method'  => strtoupper($method),
				'content' => http_build_query($data),
			),
		);
		$context  = stream_context_create($options);
		return $result = file_get_contents($url, false, $context);
	}
	
	
	public function uploadMultiFiles ($fieldname,$foldername,$filetypes , $limit = null,$maxsize=2048) 
	{
		/*ini_set( "post_max_size" , "25M");
		ini_set( "upload_max_filesize" , "10M");
		ini_set( "max_input_time" , "-1");
		ini_set( "memory_limit" , "2000M");
		ini_set( "max_execution_time" , "2000");*/
		
		if (!isset ($_FILES[$fieldname]))
			return array();
		
		
		if (!is_array($_FILES[$fieldname]))
			$this->showMsg ("Invalid Request" , "Something wrong has gone. There’s something wrong in your request. Please try again later." , "gb");

		if (!file_exists($foldername))
			mkdir($foldername);
		
		
		$uploadedfiles_array = array();
		
		$i = 0;
		$filenamemid = uniqid(time().'-');
		$totalsize = 0;
		
		for ($j =0 ; $j  < sizeof ($_FILES[$fieldname]['error'])  ; $j ++)
		{
			if ($_FILES[$fieldname]['error'][$j]  == UPLOAD_ERR_OK &&  is_uploaded_file ($_FILES[$fieldname]['tmp_name'][$j])  )
			{
				$i ++;
				
				if (is_numeric($limit) && $i > $limit)
					break;
					
				$ext = pathinfo($_FILES[$fieldname]['name'][$j] , PATHINFO_EXTENSION);
				$ext = strtolower($ext);
				if (!in_array($ext,$filetypes))
				{
					foreach ($uploadedfiles_array as $innfiles)
					{
						@unlink($foldername.$innfiles);
					}
					$this->showMsg ("File Uploading Error" , "Invalid File please upload only " . implode(' or ' , $filetypes) , "gb");
				}
			
				$filename = "{$filenamemid}-{$i}.{$ext}";
				$fileSizeKB = $_FILES[$fieldname]["size"][$j] / 1024;
				
				$totalsize += $fileSizeKB;
				
				if ($totalsize > $maxsize)
				{
					
					foreach ($uploadedfiles_array as $innfiles)
					{
						@unlink($foldername.$innfiles);
					}
					
					$this->showMsg ("File size excide from limit " , "The file(s) you are uploading having size more than ".(ceil($maxsize/1024))." MB you can’t upload More than ".(ceil($maxsize/1024))." MB data in per request." , "gb"  );
				}
				
				if(move_uploaded_file($_FILES[$fieldname]['tmp_name'][$j] , $foldername.$filename))
				{
					$uploadedfiles_array[] = $filename;
				}
			}
		}
		
		return $uploadedfiles_array;
	}
	
	function uploadSingleFile ($fieldname,$foldername,$filetypes,$maxsize=10048)
	{
		ini_set( "post_max_size" , "25M");
		ini_set( "upload_max_filesize" , "10M");
		ini_set( "max_input_time" , "-1");
		ini_set( "memory_limit" , "2000M");
		ini_set( "max_execution_time" , "2000");
		
			
		if (!file_exists($foldername))
		{
			mkdir($foldername);
		}
		
		if (!isset ($_FILES[$fieldname]))
		{
			return false;
		}
		
	
		
		if ($_FILES[$fieldname]['error']  == UPLOAD_ERR_OK &&  is_uploaded_file ($_FILES[$fieldname]['tmp_name'])  )
		{
			$filenamemid = uniqid(time());
			
			$ext = pathinfo($_FILES[$fieldname]['name'] , PATHINFO_EXTENSION);
			$ext = strtolower($ext);
			
			
			if (is_array($filetypes) && !in_array($ext,$filetypes))
			{
				$this->showMsg ("File Uploading Error" , "Invalid File please upload only jpg or png" , "gb");
			}
			
			$filename = "{$filenamemid}.{$ext}";
			$fileSizeKB = $_FILES[$fieldname]["size"] / 1024;
			if (is_numeric($maxsize) && $fileSizeKB > $maxsize)
			{
				$this->showMsg ("File Uploading Error" , "File size can't be more than 2 MB" , "gb");
			}
			
			if(move_uploaded_file($_FILES[$fieldname]['tmp_name'] , $foldername.$filename))
			{
				return  $filename;
			}
		}
		else
		{
			return false;
		}
	}
	
	function getUrl ( $title , $id, $prefix = 'page-' , $sufix = '.html')
	{
		$title = url_title($title);
		return  $prefix.$title.'-'.$id.$sufix;
	}

	function urlToBase64($path) {
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = file_get_contents($path);
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
		return $base64;
	}
	### End Commen Functions
}