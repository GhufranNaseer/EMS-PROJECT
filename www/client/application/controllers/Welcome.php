<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends Initialize {

	function login($event_id, $event_name) {
		if (is_null($event_id))
			show_404();

		$event = $this->db
			->where('id', $event_id)
			->get('es_exhibitions')
			->row();

		if (!isset($event)) {
			show_404();
		}

		$this->load->view ('login', array(
			'event' => $event
		));
	}

	function login_validate (){
		$event_id = $this->input->get ('id');
		$username = $this->input->post ('username');
		$password = $this->input->post ('password');

		if (!is_string($username) || !is_string($password) || $username == "" || $password == "") {
			return $this->common->doError (func_num_args() , 'Login ID and Password required' );
		}

		$password  = md5($password);
		if (!$this->verifyUser($event_id, $username, $password)) {
			return $this->common->doError (func_num_args() , 'Invalid login details' );
		}
		else {
			return $this->common->doError (func_num_args() , 'done'  , true);
		}
	}

	function login_submit () {
		if ($this->login_validate () !== true)
			show_404 ();

		$data = $this->check_login();

		$this->db
			->where('id', $this->userdata->id)
			->update('es_customers', array(
				'lastLogin' => date('Y-m-d H:i:s')
			));


		redirect(base_url($this->homePage));
	}

	function index  () {
		redirect (base_url($this->homePage));
	}

	function event_updates() {
		$notification_id = $this->input->post('notification_id');
		if (is_null($notification_id)) {
			echo json_encode(array(
				'error' => 1,
				'message' => 'required parameters is missing'
			));
			exit;
		}

		$notification = $this->db
			->where(mycolumn(), $notification_id)
			->get('es_exhibition_notification')
			->row();

		if ($notification) {
			echo json_encode(array(
				'error' => 0,
				'data' => $notification
			));
		} else {
			echo json_encode(array(
				'error' => 1,
				'message' => 'Event notification not found!'
			));
			exit;
		}
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


				// validation
				/*if (!is_null($this->input->post('dimension'))) {
					$image_info = getimagesize($_FILES["upload_files"]["tmp_name"]);
					$image_width = $image_info[0];
					$image_height = $image_info[1];
					$dimension = explode('x', $this->input->post('dimension'));

					if ($image_width != $dimension[0] || $image_height != $dimension[1]) {
						echo json_encode(array(
							'error' => true,
							'message' => 'Image dimension must be ' . $this->input->post('dimension')
						));
						die();
					}
				}*/

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



	/* START FORGOT PASSWORD */
	function forget_password($event_id, $event_name) {
		if (is_null($event_id))
			show_404();

		$event = $this->db
			->where('id', $event_id)
			->get('es_exhibitions')
			->row();

		if (!isset($event)) {
			show_404();
		}

		$this->load->view ('forget-password/fp-index', array(
			'event' => $event
		));
	}

	function forget_password_validate() {
		$event_id = $this->input->get ('id');
		$username = $this->input->post ('username');

		if (!is_string($username) || $username == "") {
			return $this->common->doError (func_num_args() , 'Login ID is required' );
		}

		$event = $this->db
			->where(mycolumn(), $event_id)
			->where('is_deleted', 0)
			->where('is_completed', 0)
			->get('es_exhibitions');

		if ($event->num_rows() == 0) {
			return $this->common->doError (func_num_args() , 'Event not found!' );
		}

		$event_data = $event->row();

		$check = $this->db
			->where('exhibition_id', $event_data->id)
			->where('login_id', $username)
			->where('is_canceled', 0)
			->where('is_approved', 1)
			->get('es_exhibition_booking');

		if ($check->num_rows() == 0) {
			return $this->common->doError (func_num_args() , 'Booking not found!' );
		}
		$booking = $check->row();

		$customer = $this->db
			->where('id', $booking->customer_id)
			->get('es_customers')
			->row();

		if (!isset($customer)) {
			return $this->common->doError (func_num_args() , 'Customer not found!' );
		}

		$this->forget_event = $event_data;
		$this->forget_booking = $booking;
		$this->forget_customer = $customer;

		return $this->common->doError (func_num_args() , 'done'  , true);
	}

	function forget_password_submit() {
		// S Rate Limiting check (60 seconds session cooldown)
		$last_request = $this->session->userdata('last_client_fp_request_time');
		$current_time = time();
		if ($last_request && ($current_time - $last_request) < 60) {
			$remaining = 60 - ($current_time - $last_request);
			$this->session->set_flashdata('message', 'Too Many Requests! Please wait ' . $remaining . ' seconds before requesting password reset again.');
			if (isset($this->forget_event)) {
				redirect(base_url ('login/' . $this->forget_event->id . '-' . str_replace(' ', '-', $this->forget_event->exhibition_title)));
			} else {
				redirect(base_url());
			}
			return;
		}
		// E Rate Limiting check

		if ($this->forget_password_validate() !== true)
			show_404 ();

		$temp_data = array(
			'forget_booking' => $this->forget_booking,
			'forget_customer' => $this->forget_customer,
			'forget_event' => $this->forget_event,
		);

		$this->dbvars->setVar('fp_' . myid($this->forget_booking->id), $temp_data);

		$login_link = base_url('recover-account/' . myid($this->forget_booking->id));

		$get_email_template = $this->db
			->where("title" , "RESET_PASSWORD_LINK")
			->where("exhibition_id" , $this->forget_event->id)
			->get('email_template')
			->row();

		$subject = 'Email not configure!';
		$message = 'Email not configure!';
		if (isset($get_email_template)) {
			$subject = $get_email_template->subject;
			$message = $get_email_template->message;
		}
		// {PASSWORD_RESET_LINK},{CUSTOMER_COMPANY},{CUSTOMER_NAME},{CUSTOMER_EMAIL},{EVENT_NAME}
		$subject = str_replace('{CUSTOMER_COMPANY}', $this->forget_customer->company, $subject);
		$subject = str_replace('{CUSTOMER_NAME}', $this->forget_customer->name, $subject);
		$subject = str_replace('{CUSTOMER_EMAIL}', $this->forget_customer->email, $subject);
		$subject = str_replace('{EVENT_NAME}', $this->forget_event->exhibition_title, $subject);

		$message = str_replace('{PASSWORD_RESET_LINK}', $login_link, $message);
		$message = str_replace('{CUSTOMER_COMPANY}', $this->forget_customer->company, $message);
		$message = str_replace('{CUSTOMER_NAME}', $this->forget_customer->name, $message);
		$message = str_replace('{CUSTOMER_EMAIL}', $this->forget_customer->email, $message);
		$message = str_replace('{EVENT_NAME}', $this->forget_event->exhibition_title, $message);

		// Direct Instant SMTP Mailer & Log (Queue Bypass)
		$main_app_path = FCPATH . 'application/';
		if (file_exists($main_app_path . 'models/Email_configuration_model.php') && file_exists($main_app_path . 'helpers/phpmailer_helper.php')) {
			require_once $main_app_path . 'models/Email_configuration_model.php';
			require_once $main_app_path . 'helpers/phpmailer_helper.php';

			$email_model = new Email_configuration_model();
			$settings = $email_model->get_mailer_settings();

			$sendResult = sendMail(
				($this->forget_customer->name ? $this->forget_customer->name : $this->forget_customer->company),
				$this->forget_customer->email,
				$subject,
				$message,
				$this->forget_event->exhibition_title,
				$settings['mail_from_email'],
				'',
				array(
					'settings' => $settings,
					'echo' => false,
					'debug' => (isset($settings['smtp_debug']) && $settings['smtp_debug'] === 'yes')
				)
			);

			$email_model->log_email(array(
				'email_type' => 'RESET_PASSWORD_LINK',
				'recipient_email' => $this->forget_customer->email,
				'subject' => $subject,
				'driver' => $settings['mail_driver'],
				'status' => (isset($sendResult['success']) && $sendResult['success']) ? 'sent' : 'failed',
				'attempts' => 1,
				'error_message' => (isset($sendResult['success']) && $sendResult['success']) ? null : (isset($sendResult['error']) ? $sendResult['error'] : 'Unknown error'),
				'debug_message' => (isset($settings['smtp_debug']) && $settings['smtp_debug'] === 'yes' && isset($sendResult['debug'])) ? $sendResult['debug'] : null,
			));
		} else {
			$this->db->insert('es_emails_cron', array(
				'type' => 'RESET_PASSWORD_LINK',
				'data' => null,
				'from_name' => $this->forget_event->exhibition_title,
				'email' => $this->forget_customer->email,
				'subject' => $subject,
				'message' => $message,
				'created_on' => date('Y-m-d H:i:s'),
			));
		}

		// Set rate limit timestamp
		$this->session->set_userdata('last_client_fp_request_time', time());

		$this->session->set_flashdata('message', 'Please check your email ('.$this->forget_customer->email.') where we have sent you the link to change the password');
		redirect(base_url ('login/' . $this->forget_event->id . '-' . str_replace(' ', '-', $this->forget_event->exhibition_title)));
	}

	function recover_account($booking_id) {
		$data = $this->dbvars->getVar('fp_' . $booking_id);

		if (is_null($data)) {
			//$this->session->set_flashdata('error', 'Link has been expired, Please reset your password again!');
			//redirect(base_url ('login/' . $this->forget_event->id . '-' . str_replace(' ', '-', $this->forget_event->exhibition_title)));
			show_404();
			die();
		}

		$this->load->view('forget-password/fp-recover', array (
			'event'=>$data['forget_event'],
			'recover'=>$data
		));
	}

	function recover_account_validate($booking_id) {
		$data = $this->dbvars->getVar('fp_' . $booking_id);

		if (is_null($data)) {
			return $this->common->doError( func_num_args()-1,  'Something went wrong! please reset your password again!');
		}

		$this->form_validation->set_rules('password', 'password*Password', 'trim|required|min_length[8]|password_check' );
		$this->form_validation->set_rules('confirmPassword', 'confirmPassword*Confirm Password', 'trim|required|matches[password]' );


		if ($this->form_validation->run() == false)
			return $this->common->doError( func_num_args()-1,  $this->common->getFVError() );
		else
			return $this->common->doError(func_num_args()-1 , "done" , true);
	}

	function recover_account_submit($booking_id) {
		$data = $this->dbvars->getVar('fp_' . $booking_id);

		if (is_null($data)) {
			//$this->session->set_flashdata('error', 'Link has been expired, Please reset your password again!');
			//redirect(base_url ('login/' . $data['forget_event']->id . '-' . str_replace(' ', '-', $data['forget_event']->exhibition_title)));
			show_404();
			die();
		}

		$password = $this->input->post ('password');
		$password = md5($password);

		$this->db
			->where('id', $data['forget_booking']->id)
			->update('es_exhibition_booking', array(
				'login_password' => $password
			));

		$this->dbvars->unsetVar('fp_' . $booking_id);

		$this->session->set_flashdata('message', 'Your password has been updated now you can login to the system with your new password');
		redirect(base_url ('login/' . $data['forget_event']->id . '-' . str_replace(' ', '-', $data['forget_event']->exhibition_title)));
	}
	/* END FORGOT PASSWORD */
}
