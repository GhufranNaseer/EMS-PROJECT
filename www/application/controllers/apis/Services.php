<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Services extends CI_Controller {
	const development = true;
	const key = '9D7R>q|c8l,fzyL';

	function __construct () {
		parent::__construct();
		$this->load->model ('tem_vals');

		$json = $this->input->post('json');
		if (is_string($json) && is_array($json = @json_decode($json , true))) {
			$pics = $this->input->post ('pics');
			$signature = $this->input->post ('signature');

			if (!is_null($pics) && !is_null($signature) && is_array($pics) && is_string($signature))
			{
				$this->pics = $pics;
				$this->signature = $signature;
			}

			$_POST = array_merge($json , $_POST);
			unset($_POST['json']);
		}

		$this->dokey ();
	}

	private function dokey () {
		$key = $this->input->post ('key');

		if(!is_string($key) || $key != self::key)
			show_404();
	}

	private function doJson ($ststus , $message = "" , $data = NULL) {
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
		header('Content-Type: application/json; Charset=UTF-8');

		$status = array ("status" => (($ststus == 1)? "SUCCESS" : "FAILURE") , 'message' => $message  , 'data' => $data );
		exit (
		json_encode($status, JSON_UNESCAPED_UNICODE)
		);
	}

	private function validate_session () {
		$session_id = $this->input->post ('session_id');

		$session_id = trim ($session_id);

		if (strlen($session_id)==0)
			$this->doJson(0 , "Invalid Parameters");

		$session_id = $this->db->escape($session_id);
		$this->userdata = $this->tem_vals->getValueWithMyWhere (mycolumn().'='.$session_id);

		if (!is_array($this->userdata))
			$this->doJson(0 , "Invalid Session or Session has expired");
	}


	function login () {
		$user_name = $this->input->post ('user_name');
		$password = $this->input->post ('password');

		if (!( is_string($user_name) && is_string($password)))
			$this->doJson(0 , "Invalid Parameters user_name and password are required");

		$user_name = trim ($user_name);
		$password = trim ($password);

		if (strlen($user_name)==0 || strlen($password)==0)
			$this->doJson(0 , "Invalid Parameters user_name and password are required");


		/*->group_start()
		->or_where ('user_email',$user_name)
		->or_where ('user_phone',$user_name)
		->group_end()*/
		$user = $this->db
			->where ('user_email',$user_name)
			->where ('is_active', 1)
			->where ('user_password', $password)
			->get ('users')
			->row_array ();

		if (is_null($user))
			$this->doJson(0 , "Invalid id and password");


		$session_id = $this->tem_vals->setVal ($user , '+3 days');

		$response = array (
			'user' => $user,
			'session_id' => myid ($session_id)
		);

		$this->doJson(1 , "Successfully generated the session." , $response);
	}


	function get_users () {
		$this->validate_session ();
		$userdata = $this->userdata;

		$users = $this->db
			->get('users')
			->result_array();


		$this->doJson(1, "users" , array (
			'users' => $users
		));
	}

}