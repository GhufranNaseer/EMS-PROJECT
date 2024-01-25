<?php

abstract class MY_Controller extends CI_Controller {

	protected $homePage = "dashboard";
	protected $loginPage = "login";

	protected $allow_route = array(
		'login',
		'login-validate',
		'login-submit',
		'log-out',
		'event_updates',
		'forget-password',
		'forget-password-validate',
		'forget-password-submit',
		'recover-account',
		'recover-account-validate',
		'recover-account-submit',
	);

	function __construct () {
		parent::__construct ();
		if (method_exists($this , 'config')) {
			$config = $this->config ();
			foreach ($config as $key => $val ) {
				if (isset($this->$key)) {
					$this->$key = $val;
				}
			}
		}

		$route = $this->uri->segment(1);

		if (!in_array($route, $this->allow_route)) {
			$this->check_login();
		}
	}


	protected function verifyUser($event_id, $user, $password) {
		$event = $this->db
			->where(mycolumn(), $event_id)
			->where('is_deleted', 0)
			->where('is_completed', 0)
			->get('es_exhibitions');

		if ($event->num_rows() == 0) {
			return false;
		}

		$event_data = $event->row();

		$check = $this->db
			->where('exhibition_id', $event_data->id)
			->where('login_id', $user)
			->where('login_password', $password)
			->where('is_canceled', 0)
			->where('is_approved', 1)
			->get('es_exhibition_booking');

		if ($check->num_rows() == 0) {
			return false;
		}

		$booking = $check->row();

		$this->session->set_userdata('client_event_id', $event_data->id);
		$this->session->set_userdata('client_booking_id', $booking->id);
		$this->session->set_userdata('client_login_id', $booking->login_id);
		$this->session->set_userdata('client_login_password', $booking->login_password);

		return true;
	}

	protected function check_login() {
		$client_event_id = $this->session->userdata('client_event_id');
		$client_booking_id = $this->session->userdata('client_booking_id');
		$client_login_id = $this->session->userdata('client_login_id');
		$client_login_password = $this->session->userdata('client_login_password');

		if (is_null($client_event_id)) {
			show_404();
		}

		$this->event = $this->db
			->where('id', $client_event_id)
			->get('es_exhibitions')
			->row();

		if (is_null($client_booking_id) || is_null($client_login_id) || is_null($client_login_password)) {
			redirect(base_url($this->loginPage . '/' . $client_event_id . '-' . str_replace(' ', '-', $this->event->exhibition_title)));
			die();
		}

		$this->booking = $this->db
			->where('id', $client_booking_id)
			->get('es_exhibition_booking')
			->row();

		$this->userdata = $this->db
			->where('id', $this->booking->customer_id)
			->get('es_customers')
			->row();

		return $this->userdata;
	}

	
	public function logout() {
		$event_id = $this->session->userdata('client_event_id');
		$this->session->sess_destroy();

		$this->session->set_userdata('client_event_id', $event_id);
		$event = $this->db
			->where('id', $event_id)
			->get('es_exhibitions')
			->row();
		redirect(base_url($this->loginPage . '/' . $event_id . '-' . str_replace(' ', '-', $event->exhibition_title)));
	}

	


	function __destruct() {
		$this->load->model('db_log');
		$this->db_log->logQueries();
	}
}

abstract class Initialize extends MY_Controller {
	
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