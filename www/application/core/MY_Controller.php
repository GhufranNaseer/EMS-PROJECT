<?php

abstract class MY_Controller extends CI_Controller
{
	### Class Configs		
	protected $tblName = "users";
	
	protected $userIDColumn = "id";
	protected $userNameColumn = "user_email";
	protected $passwordColumn = "user_password";
	protected $homePage = "dashboard";
	protected $loginPage = "login";
	protected $lastActivityColumn = "lastActivity";	
	protected $activeUserQuery = " is_active = 1";	
	protected $ruleCacheStatus = true;
	protected $defaultRule = "";	
	protected $readMeCookieName = "rememberme";
	protected $userNameSession = "userid";
	protected $passwordSession = "userpassword";
	
	
	### Strat Class
	public $islogedin = false;
	
	function __construct ()
	{
		parent::__construct ();
		if (method_exists($this , 'config'))
		{
			$changeable = array ('tblName' , 'userIDColumn' , 'userNameColumn' , 'passwordColumn' , 'homePage' , 'loginPage' , 'lastActivityColumn' , 'activeUserQuery' , 'ruleCacheStatus' , 'defaultRule' , 'readMeCookieName' , 'userNameSession' , 'passwordSession' );
			$config = $this->config ();
			foreach ($config as $key => $val )
			{
				if (isset ($this->$key) && in_array($key ,$changeable ) )
				{
					$this->$key = $val;
				}
			}
		}
	}
	
	private function getUsernamePasswordWithRme ()
	{
		$rme = $this->input->cookie($this->readMeCookieName);
		
		if ($rme === false || !is_string($rme))
			return false;
		
		$fields = $this->db->list_fields($this->tblName);
		
		$fields_select = "";
		for($i = 0 ; $i < sizeof($fields) ; $i++)
		{
			$fields_select .= "IFNULL(`{$fields[$i]}`,''),";
		}
		
		$fields_select .= $this->db->escape($this->input->ip_address());
		
		$rme = $this->db->escape_str  ($rme);
		
		$auq = empty($this->activeUserQuery)? "" : "AND ".$this->activeUserQuery;
		$ruleQuery = isset ($this->varifyUser_query)? $this->varifyUser_query : "";
		
		if ($ruleQuery != "")
			$this->db->select ("*, ($ruleQuery) AS r_varifyUser_query", false);
		
		$r = $this->db
		->where("MD5( CONCAT({$fields_select}) ) = '{$rme}' {$auq}" , NULL, false)
		->get($this->tblName);
		
		if ($r->num_rows () == 0)
			return false;
		else
			return $r->row_array ();
	}
	
	protected function varifyUser ($username , $password)
	{
		$auq = empty($this->activeUserQuery)? "" : "AND ".$this->activeUserQuery;
		$ruleQuery = isset ($this->varifyUser_query)? $this->varifyUser_query : "";		
		
		$username = $this->db->escape($username);
		$password = $this->db->escape($password);
		
		$compare = ""; 
		if (is_array($this->userNameColumn))
		{
			for($i = 0 ; $i <  sizeof($this->userNameColumn) ; $i++)
			{
				$val = $this->userNameColumn[$i];
				$compare .= " `{$val}` = {$username} ";
				if (!($i == (sizeof($this->userNameColumn)-1)))
					$compare .= " OR ";
			}
		}
		else
		{
			$compare = "`$this->userNameColumn` = {$username} ";
		}
		
		
		if (isset ($this->userid))
		{
			$compare = "`$this->userIDColumn` = {$this->userid}";
			unset ($this->userid);
		}
		else
		{
			$compare = "({$compare})";
		}
		
		if ($ruleQuery != "")
			$this->db->select ("*, ($ruleQuery) AS r_varifyUser_query" , false);
			
		$userdata =
		$this->db
		->limit(1)
		->where ("{$compare} AND `$this->passwordColumn` = {$password} {$auq}" , NULL, false)
		->get ($this->tblName); 
		
		if ($userdata->num_rows () == 0)
		{
			return false;
		}
		else
		{	
			$this->userdata = $userdata->row ();
			
			$activityTime = time ();
			
			$this->userdata->{$this->lastActivityColumn} = $activityTime;
			
			$this->db->set($this->lastActivityColumn , $activityTime)
			->where ($this->userIDColumn , $this->userdata->{$this->userIDColumn})
			->update($this->tblName);
			
			if (!is_null($this->input->cookie ($this->readMeCookieName)))
			{
				$this->setupcookie();
			}
			return true;
		}
	}
	
	protected function checklogin ($forcenocache = true)
	{
		$id = $this->session->userdata($this->userNameSession);	
		$pw = $this->session->userdata($this->passwordSession);
		
		if (is_null($id) || is_null($pw))
		{
			$userdata = $this->getUsernamePasswordWithRme ();
			if ($userdata !== false)
			{
				$this->session->set_userdata($this->userNameSession , $id = $userdata[$this->userIDColumn]);
				$this->session->set_userdata($this->passwordSession , $pw = $userdata[$this->passwordColumn]);
			}
		}
		
		
		
		if (is_null($id) || is_null($pw))
		{
			if (method_exists($this , "onNotLoggedin"))
			{
				$this->onNotLoggedin ();
				die;
			}
			else
			{
				if ($this->input->is_ajax_request())
					exit ("Log-in Required");
				
				redirect (base_url ($this->loginPage));
			}
		}
		else
		{
			
			$this->userid =  $id;
			$this->islogedin = $this->varifyUser ("" , $pw);
	
			if (!$this->islogedin)
				$this->logout();			
		}
		
		if(isset($this->userdata->r_varifyUser_query) && $this->userdata->r_varifyUser_query == 0)
			show_error("You are not authorized to perform this action. For further help please contact administrator", 403, 'Access denied');
		
		if ($forcenocache)
		{
			$this->output->set_header("HTTP/1.0 200 OK");
			$this->output->set_header("HTTP/1.1 200 OK");
			$this->output->set_header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT');
			$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
			$this->output->set_header("Cache-Control: post-check=0, pre-check=0");
			$this->output->set_header("Pragma: no-cache");
		}
		
	}
	
	protected function checkifnotlogin ($forcenocache = true)
	{
		$id = $this->session->userdata($this->userNameSession);
		$pw = $this->session->userdata($this->passwordSession);
		
	
		
		if (is_null($id) || is_null($pw))
		{
			$userdata = $this->getUsernamePasswordWithRme ();
			if ($userdata !== false)
			{
				$id = $userdata[$this->userIDColumn];
				$pw = $userdata[$this->passwordColumn];
				$this->session->set_userdata($this->userNameSession , $id);
				$this->session->set_userdata($this->passwordSession , $pw);
			}
		}
		
		if (is_null($id) || is_null($pw))
		{
			if ($forcenocache)
			{
				$this->output->set_header("HTTP/1.0 200 OK");
				$this->output->set_header("HTTP/1.1 200 OK");
				$this->output->set_header('Last-Modified: '. gmdate('D, d M Y H:i:s', time() ).' GMT');
				$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
				$this->output->set_header("Cache-Control: post-check=0, pre-check=0");
				$this->output->set_header("Pragma: no-cache");
			}
		}
		else
		{
			if (method_exists($this , "onLoggedin"))
			{
				$this->onLoggedin ();
				die;
			}
			else
			{
				if ($this->input->is_ajax_request())
					die ("Already Login");
			
				redirect (base_url($this->homePage));
			}
		}
	}
	
	
	protected function justcheckifloginornot ($forcenocache = true)
	{
		$id = $this->session->userdata($this->userNameSession);
		$pw = $this->session->userdata($this->passwordSession);
		
		if (is_null($id) || is_null($pw))
		{
			$userdata = $this->getUsernamePasswordWithRme ();
			if ($userdata !== false)
			{
				$id = $userdata[$this->userIDColumn];
				$pw = $userdata[$this->passwordColumn];
				
				$this->session->set_userdata($this->userNameSession , $id);
				$this->session->set_userdata($this->passwordSession , $pw);
			}
		}
		
		if (is_null($id) || is_null($pw))
		{
			return $this->islogedin = false;
		}
		else
		{
			$this->userid = $id;
			return $this->islogedin = $this->varifyUser ("" , $pw);
		}
		
		if ($forcenocache)
		{
			$this->output->set_header("HTTP/1.0 200 OK");
			$this->output->set_header("HTTP/1.1 200 OK");
			$this->output->set_header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT');
			$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
			$this->output->set_header("Cache-Control: post-check=0, pre-check=0");
			$this->output->set_header("Pragma: no-cache");
		}
	}
	
	
	protected function setUplogin ($setRme)
	{
		if (!isset ($this->userdata))
			return false;
		
		
		$this->session->set_userdata($this->userNameSession , $this->userdata->{$this->userIDColumn});		
		$this->session->set_userdata($this->passwordSession , $this->userdata->{$this->passwordColumn});
		
		if ($setRme)
		{
			
			$this->setupcookie ();
		}
		
		return true;
	}
	
	
	private function setupcookie ()
	{
		$data = "";
		$userData = (array) $this->userdata;
		foreach ($userData as $val)
		{
			if (!is_null($val))
				$data .= $val;
		}
		
		$data .= $this->input->ip_address();
		
		$data = md5 ($data);
		setcookie($this->readMeCookieName , $data , strtotime("+7 days"));
	}
	
	public function _remap($method, $params = array())
	{
		if (!method_exists($this, $method))
			show_404();
		
		$worked = false;
		
		$myheader = "";
		$myfooter = "";
		
		$donExecute = false;
		if (method_exists($this , 'rule'))
		{
			$data = $this->rule ();
			
			foreach ($data as $key => $val)
			{
				$key = trim ($key);
				$methods = (strpos($key,",") !== false)? explode(",",$key) : array ($key);
				for($i = 0; $i < sizeof($methods); $i ++)
				{
					$methods[$i] = trim ($methods[$i]);
				}
				
				$rule = "";
				
				if (in_array($method , $methods))
				{
					if (isset ($val['header']) && method_exists($this ,$val['header']) )
							$myheader = $val['header'];
					
					if (isset ($val['footer']) && method_exists($this ,$val['footer']) )
							$myfooter = $val['footer'];
							
					
					if (isset($val['ajaxOnly']) && $val['ajaxOnly'] == true)
					{
							if (!$this->input->is_ajax_request ())
								show_404 ();
					
					}
				}
				
				if (isset ($val['accessRule']) && is_callable($val['accessRule']))
				{
					$rule = $val['accessRule']();
				}
				elseif (isset($val['rule']))
				{
					$rule = $val['rule'];
				}
				
				
				if($rule == "")
				{
					continue;
				}
				elseif ( !(strpos($rule,'@') === 0 || strpos($rule,'-') === 0  || strpos($rule,'*') === 0) )
				{
					continue;
				}
				
				$donExecute = true;
				
				if (in_array($method , $methods))
				{	
					$donExecute = false;
					if (isset ($val['condition']))
					{
						$this->varifyUser_query =  " {$val['condition']} ";
					}		
					elseif (strlen ($rule) > 1)
					{
						$query = substr($rule , 1);
						$this->varifyUser_query = " {$query} ";
					}
						
		
					
					$ruleCacheStatus = (isset ($val['ruleCacheStatus']))? (bool) $val['ruleCacheStatus'] : $this->ruleCacheStatus;
					
					if (strpos($rule,'@') === 0)
					{
						$worked = true;
						$this->checklogin($ruleCacheStatus);
						if (isset ($val['initialize']) && is_callable($val['initialize']))
							$val['initialize']();
							
						if (isset ($val['manageUser']) && method_exists($this ,$val['manageUser']) )
							$this->{$val['manageUser']}($method);
						break;
					}
					elseif (strpos($rule,'-') === 0)
					{
						$worked = true;
						$this->checkifnotlogin($ruleCacheStatus);
						if (isset ($val['initialize']) && is_callable($val['initialize']))
							$val['initialize']($this);
						
						if (isset ($val['manageUser']) && method_exists($this ,$val['manageUser']) )
							$this->{$val['manageUser']}($method);
						break;
					}
					elseif (strpos($rule, '*') === 0)
					{
						$worked = true;
						$this->justcheckifloginornot($ruleCacheStatus);
						if (isset ($val['initialize']) && is_callable($val['initialize']))
							$val['initialize']($this);
						
						if (isset ($val['manageUser']) && method_exists($this ,$val['manageUser']) )
							$this->{$val['manageUser']}($method);
						break;
					}
				}
			}
		}
		
		if ($this->defaultRule != "" && !$worked)
		{
			if (strlen ($this->defaultRule) > 1)
			{
				$query = substr($this->defaultRule , 1);
				$this->varifyUser_query = "AND ({$query})";
			}
			
			if (strpos($this->defaultRule,'@') === 0)
				$this->checklogin($this->ruleCacheStatus);
			elseif (strpos($this->defaultRule,'-') === 0)
				$this->checkifnotlogin($this->ruleCacheStatus);
			elseif (strpos($this->defaultRule , '*') === 0)
				$this->justcheckifloginornot($this->ruleCacheStatus);
		}
		
		if ($donExecute && $method != 'logout' && $method != 'showmsg' && $method != 'show404' )
			show_error("You are not authorized to perform this action. For further help please contact administrator", 403, 'Access denied');
		
		// S Other
		if ($this->checkRights)
			$this->checkRights();
		// E Other
		
		if ($myheader != "")
			$this->{$myheader}();
		
		$returnVal = call_user_func_array(array($this, $method), $params);
		
		if ($myfooter != "")
			$this->{$myfooter}();
			
		return $returnVal;
	}
	
	
	public function logout()
	{
		
		$this->session->sess_destroy();

		setcookie($this->readMeCookieName, '', -time());
		setcookie(config_item('sess_cookie_name'), '', -time());
		
		if ($this->input->is_ajax_request())
			exit ("Please Log-in again");
			
		redirect (base_url($this->loginPage));
	}
	
	### End Login Checker
	
	
	//// S Other	
	var $checkRights = false;
	function checkRights ()
	{
		$rights = ucwords(str_replace('_','' , $this->router->fetch_class()));
		$usergroup = $this->db
			->where('id', $this->userdata->user_group_id)
			->get('usergroup')
			->row();

		$this->userdata->usergroup = $usergroup;

		if ($usergroup->id  == SUPER_ADMIN || $usergroup->usergroup_rights == '*')
			return ;

		if (stripos($this->userdata->usergroup->usergroup_rights  , ",{$rights}.php," )===false && stripos($this->userdata->user_rights  , ",{$rights}.php," )===false && $rights != "Welcome" &&  $rights != "Portal" &&  $rights != "Myfuncs")
			show_error("You are not authorized to perform this action. For further help please contact administrator", 403, 'Access denied');
	}
	
	function activateRightsSystem ()
	{
		$this->checkRights = true;
	}

	function hasRight($class_name) {
		$usergroup = ($this->userdata->usergroup) ? $this->userdata->usergroup : null;

		if (is_null($usergroup)) {
			$usergroup = $this->db
				->where('id', $this->userdata->user_group_id)
				->get('usergroup')
				->row();
		}

		if ($usergroup->id  == SUPER_ADMIN || $usergroup->usergroup_rights == '*') {
			return true;
		}

		if (stripos($usergroup->usergroup_rights  , ",{$class_name}.php," )===false && stripos($this->userdata->user_rights  , ",{$class_name}.php," )===false && $class_name != "Welcome" &&  $class_name != "Portal" &&  $class_name != "Myfuncs") {
			return false;
		}

		return true;
	}

	function __destruct() {
		$this->load->model('db_log');
		$this->db_log->logQueries();
	}
}

abstract class Initialize extends MY_Controller
{
	
	public function showmsg ()
	{
		$this->load->model ('tem_vals');
		
		$data = $this->input->get ("e");
		
		if (!is_string($data))
		{
			show_404 ();
		}
		
		$data = $this->db->escape_str ($data);
		$data = " MD5(CONCAT({$this->db->escape($this->config->item('encryption_key'))} ,`id`)) = '{$data}' ";
		$data = $this->tem_vals->getValueWithMyWhere ($data);
		
		if ($data === false)
		{
			show_404();
		}
			
		$this->load->view ("alert" , $data );
	}
	
	public function show404 ()
	{
		set_status_header(404);
		$this->load->view ("404");
	}
}