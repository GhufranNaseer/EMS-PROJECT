<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends Initialize {

	protected function rule () {
		$login_fp = array (
			'login,
			forget_password' => array (
				'rule' => '-',
				'header' => 'loadHeader'
			),

			'login_validate,
			forget_password_validate,
			recover_account_validate' => array (
				'rule' => '-',
				'ajaxOnly' => true
			) ,
			'login_submit,
			forget_password_submit,
			recover_account_submit,
			recover_account' => array (
				'rule' => '-'
			)
		);

		$password = array (
			'my_password_validate' => array (
				'rule' => '@',
				'ajaxOnly' => true
			),
			'my_password_submit' => array (
				'rule' => '@'
			)

		);

		$common =  array (
			'index,logout' => array (
				'rule' => '@'
			),
			'file_upload' => array(
				'rule' => '@',
				'ajaxOnly' => true
			)
		);


		return array_merge($login_fp,$common,$password);
	}


	function login() {
		$this->load->view ('login');
	}

	function login_validate (){
		$username = $this->input->post ('username');
		$password = $this->input->post ('password');

		if (!is_string($username) || !is_string($password) || $username == "" || $password == "") {
			return $this->common->doError (func_num_args() , 'User email and Password required' );
		}
		$password  = md5($password);
		if (!$this->varifyUser ($username, $password)) {
			return $this->common->doError (func_num_args() , 'Invalid login details' );
		}
		else {
			return $this->common->doError (func_num_args() , 'done'  , true);
		}
	}

	function login_submit () {
		if ($this->login_validate () !== true)
			show_404 ();

		$rm = $this->input->post ('rememberme');
		$rm = (!is_null($rm))? true : false;

		$rm = false;

		$data = $this->setUplogin ($rm);

		$this->db
			->where('id', $this->userdata->id)
			->update('users', array(
				'lastLogin' => date('Y-m-d H:i:s')
			));

		redirect (base_url($this->homePage));
	}


	function my_password_validate() {
		$this->form_validation->set_rules('opassword', 'opassword*Old Password', array('required','trim',
			array('opassword', function ($str) {
				return ($this->userdata->{$this->passwordColumn} == md5($str));
			})
		));

		$this->form_validation->set_rules('password', 'password*New Password', 'trim|required|min_length[8]|password_check' );
		$this->form_validation->set_rules('cpassword', 'cpassword*Confirm Password', 'trim|required|matches[password]' );

		$this->form_validation->set_message('opassword' , '{field} is invalid');
		$this->form_validation->set_message('matches' , 'The {field} does not match with new password.');

		if ($this->form_validation->run() == false) {
			return $this->common->doError( func_num_args(),  $this->common->getFVError() );
		} else {
			return $this->common->doError(func_num_args() , "done" , true);
		}
	}

	function my_password_submit ($redirectTo="") {
		if ($this->my_password_validate () !== true)
			show_404 ();

		$password = md5($this->input->post('password'));

		$this->db->set($this->passwordColumn , $password)
			->where($this->userIDColumn, $this->userdata->{$this->userIDColumn})
			->update($this->tblName);

		$this->session->set_userdata ($this->passwordSession , $password);

		$this->session->set_flashdata ('message', 'Password successfully updated');
		redirect (base_url($redirectTo));
	}


	### Start Forget password

	function forget_password () {
		$this->load->view ('forget-password/fp-index');
	}

	function forget_password_validate () {
		$username = $this->input->post ('username');

		if (!is_string($username) ||  $username == "") {
			return $this->common->doError (func_num_args() , 'Email required');
		}
		$username = $this->db->escape($username);

		$compare = "";
		if (is_array($this->userNameColumn)) {
			for($i = 0 ; $i <  sizeof($this->userNameColumn) ; $i++) {
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

		$active = ($this->activeUserQuery == "")? "" : "AND {$this->activeUserQuery}";
		$compare = "$compare {$active}";

		$isValid = $this->db->where ($compare,null,false)->limit(1)->count_all_results ($this->tblName , false)
		== 0 ? false : true;

		if (!$isValid)
		{
			return $this->common->doError (func_num_args() , 'Email not register');
		}
		else
		{
			return $this->common->doError (func_num_args() , 'done'  , true);
		}
	}

	/*
	You need to change the this code ucwords($user->fname." ".$user->lname) and this code $this->email->to($user->email)
	If have have changed the database column names
	*/
	function forget_password_submit () {
		if ($this->forget_password_validate()!==true)
			show_404();

		$user = $this->db->get()->row();

		// S building data
		$this->load->model('tem_vals');

		$data = "";
		$userData = (array) $user;
		foreach ($userData as $val) {
			if (!is_null($val))
				$data .= $val;
		}

		$data .= $this->input->ip_address();
		$data = md5 ($data);

		$data = $this->tem_vals->setVal($data);
		$data = md5(config_item('encryption_key').$data);
		// E building data

		$loginlink = base_url ("recover-account/{$data}");

		$messageBody = $this->load->view ("forget-password/fp-email" ,
			array ('loginlink' => $loginlink  ,
				'username' =>  ucwords($user->user_first_name." ".$user->user_last_name)) , true);


		if ($user->user_email == "")
			$this->common->showMsg ('No Email' , "There is no email set up for your account please contact administrator for future assistance", base_url() );

		$this->email->initialize(array('mailtype'=>'html'));

		$this->email->from( 'no-reply@'.EMAIL_DOMAIN_NAME , PROJECT_NAME.' '. 'Forgotten Password');
		$this->email->to($user->user_email);

		$this->email->subject(PROJECT_NAME.' Forgotten Password');

		$this->email->message( $messageBody );

		$mailResult = $this->email->send();
		$this->email->clear();

		if (!$mailResult)
			$this->common->showMsg ("Unexpected Error", "We’re really sorry, we couldn't send you an email, we encountered an unexpected problem. We’ll try hard to resolve this issue.", "gb" );
		else
			$this->common->showMsg  ("Email Sent", "Please check your email where we have sent you the link to change the password", base_url ());
	}

	### recover
	private function get_recover_account ($data="")
	{
		if(!is_string($data) || $data == "")
			show_404();

		$this->load->model('tem_vals');

		$data = $this->db->escape($data);

		$encryption_key = config_item('encryption_key');
		$encryption_key = $this->db->escape($encryption_key);

		$data = $this->tem_vals->getValueWithMyWhere("MD5(CONCAT($encryption_key,`id`)) = $data");

		if ($data===false)
			show_404();

		$fields = $this->db->list_fields($this->tblName);
		$fields_select = "";
		for($i = 0 ; $i < sizeof($fields) ; $i++)
		{
			$fields_select .= "IFNULL(`{$fields[$i]}`,''),";
		}

		$fields_select .= $this->db->escape($this->input->ip_address());

		$r = $this->db
			->where("MD5( CONCAT({$fields_select}) ) = '{$data}' ",null,false)
			->get($this->tblName);

		if ($r->num_rows () == 0)
			show_404 ();
		else
			return $r->row_array ();
	}

	function recover_account ($data="")
	{
		$user = $this->get_recover_account ($data);
		$this->load->view('forget-password/fp-recover', array ('recover'=>$data));
	}

	function recover_account_validate ($data="") {
		$this->user = $this->get_recover_account ($data);

		$this->form_validation->set_rules('password', 'password*Password', 'trim|required|min_length[8]|password_check' );
		$this->form_validation->set_rules('confirmPassword', 'confirmPassword*Confirm Password', 'trim|required|matches[password]' );


		if ($this->form_validation->run() == false)
			return $this->common->doError( func_num_args()-1,  $this->common->getFVError() );
		else
			return $this->common->doError(func_num_args()-1 , "done" , true);
	}

	function recover_account_submit ($data="") {
		if ($this->recover_account_validate($data) !== true)
			show_404 ();

		$password = $this->input->post ('password');
		$password = md5($password);


		$r = $this->db->where($this->userIDColumn,$this->user[$this->userIDColumn])
			->set($this->passwordColumn,$password)
			->set ($this->lastActivityColumn , time())
			->update($this->tblName);

		$this->common->showMsg  ("Your Password Updated", "Your password has been updated now you can login to the system with your new password" , base_url ());
	}
	### End Forget password

	function index  () {
		redirect (base_url($this->homePage));
	}

	function file_upload() {
		if(isset($_FILES['upload_files'])){
			$result = null;
			$tmpFilePath = $_FILES['upload_files']['tmp_name'];
			//Make sure we have a file path
			if ($tmpFilePath != ""){
				// check folder
				if (!file_exists($this->input->post('base_url'))) {
					mkdir($this->input->post('base_url'));
				}

				//Setup our new file path

				$ext = pathinfo($_FILES['upload_files']['name']);
				$newFileName = uniqid(time().rand(1, 1000)) . '.' .strtolower($ext['extension']);
				$newFilePath = $this->input->post('base_url') . $newFileName;

				//Upload the file into the temp dir
				if(move_uploaded_file($tmpFilePath, $newFilePath)) {
					$result = array(
						'error' => false,
						'name' => $newFileName
					);
				} else {
					$result = array(
						'error' => true,
						'message' => 'error while upload'
					);
				}
			} else {
				$result = array(
					'error' => true,
					'message' => 'error while upload'
				);
			}

			echo json_encode($result);
			die();
		}
	}
}
