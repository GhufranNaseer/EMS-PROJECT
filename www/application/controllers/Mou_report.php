<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mou_report extends MY_Controller
{
	protected function rule()
	{
		$this->activateRightsSystem();
		$crd = array(
			'crd_list,
			crd_add,
			crd_add_submit,
			cancel,
			get_available_time,
			schedule,
			mou_re_schedule_validate,
			mou_re_schedule_submit,
			download_template,
			bulk_import_validate,
			bulk_import_confirm,
			get_mou_locations_json,
			approved' => array(
				'rule' => '@'
			),
			'crd_list_datatable,
			crd_add_validate' => array(
				'rule' => '@',
			)
		);
		$this->load->model('usermdl');
		$this->myparent = base_url('mou_sign.html');
		return array_merge($crd);
	}

	function crd_list()
	{
		$this->load->view('includes/after_login/head');
		$this->load->view('mou_report/exhibition_list');
	}


	function crd_list_datatable()
	{

		$this->load->library('datatables');
		$this->datatables
			->select('B.id,
					B.exhibition_id,
                    C.company as request_from_comapny,
                    D.company as request_to_comapny,
					E.exhibition_title as exhibition_title,
                    B.exhibition_day,
                    B.mou_sign_date,
                    B.mou_sign_time,
					B.description,
					B.commercial_value,
                    IF(B.is_canceled = 1, "<span class=\'label label-danger\'>Decline</span>",
                    	IF(B.is_approved = 1, "<span class=\'label label-success\'>Approved</span>", "<span class=\'label label-warning\'>Pending</span>")) as status,
					B.is_canceled,
                    B.is_approved,
                    B.user_type_to,
                    B.request_to_id', false)

			->unset_column('B.exhibition_id')
			->unset_column('B.is_canceled')
			->unset_column('B.is_approved')
			->unset_column('B.user_type_to')
			->unset_column('B.request_to_id')

			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '';
				$html .= '<a href="' . base_url() . 'mou_sign_re_schedule.html?id=' . urlencode(myid($id)) . '" class="btn btn-xs btn-info">Re-Schedule</a> ';
				if ($row['is_approved'] == 1 || $row['is_canceled'] == 1) {
					return $html;
				}
				$html .= '<a href="' . base_url() . 'mou_sign-cancel.html?id=' . urlencode(myid($id)) . '" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you would like to cancel the MoU request?\')">Decline</a> ';
				$html .= '<a href="' . base_url() . 'mou_sign-approved.html?id=' . urlencode(myid($id)) . '" class="btn btn-xs btn-success" onclick="return confirm(\'Are you sure you would like to accept the MoU request\')">Accept</a> ';
				return "<div class='text-right'>{$html}</div>";
			}, NULL)

			->where('B.is_deleted', 0)
			->join('es_customers as C', 'B.request_from_id = C.id')
			->join('es_customers as D', 'B.request_to_id = D.id')
			->join('es_exhibitions as E', 'B.exhibition_id = E.id')
			->from('es_exhibition_mou_sign as B');

		if ($this->input->get('filter_status') && $this->input->get('filter_status') != '') {
			if ($this->input->get('filter_status') == 'approved') {
				$this->datatables->where('B.is_approved', 1);
			} else if ($this->input->get('filter_status') == 'pending') {
				$this->datatables->where('B.is_approved', 0);
			} else if ($this->input->get('filter_status') == 'my_requests') {
				$this->datatables->where('B.is_approved', 0);
			}
		}
		if ($this->input->get('filter_day') && $this->input->get('filter_day') != '') {
			$this->datatables->where('B.exhibition_day', $this->input->get('filter_day'));
		}



		print($this->datatables->generate());
	}

	function schedule()
	{
		$this->load->view('includes/after_login/head');
		$this->load->view('mou_report/mou-reschedule');
	}

	function get_available_time()
	{
		$mou_sign = $this->db
			->where('id', $this->input->post('mou_id'))
			->get('es_exhibition_mou_sign')
			->row();
		$day = $this->input->post('day');
		$date = $this->input->post('date');
		$open_time = strtotime("09:00");
		$close_time = strtotime("19:00");
		$html = '';
		$condition = "((user_type_from = 'exhibitor' AND request_from_id = " . $mou_sign->request_from_id . ") OR (user_type_to = 'exhibitor' AND request_to_id = " . $mou_sign->request_to_id . "))";
		for ($i = $open_time; $i < $close_time; $i += 1800) {

			$check = $this->db
				->where('exhibition_id', $mou_sign->exhibition_id)
				->where('is_approved', 1)
				->where('mou_sign_date', $date)
				->where('mou_sign_time', date('H:i:s', $i))
				->where($condition)
				->count_all_results('es_exhibition_mou_sign');

			$other_user = 0;
			if ($this->input->post('type') && $this->input->post('id')) {
				if ($this->input->post('type') == 'officer') {
					$book_to_data = $this->db
						->where(mycolumn(), $this->input->post('id'))
						->get('es_officer')
						->row();
					$condition_other = "((user_type_from = 'officer' AND request_from_id = " . $book_to_data->id . ") OR (user_type_to = 'officer' AND request_to_id = " . $book_to_data->id . "))";
				} else {
					$book_to_data = $this->db
						->where(mycolumn(), $this->input->post('id'))
						->get('es_customers')
						->row();
					$condition_other = "((user_type_from = 'exhibitor' AND request_from_id = " . $book_to_data->id . ") OR (user_type_to = 'exhibitor' AND request_to_id = " . $book_to_data->id . "))";
				}
				$other_user = $this->db
					->where('exhibition_id', $mou_sign->exhibition_id)
					->where('is_approved', 1)
					->where('mou_sign_date', $date)
					->where('mou_sign_time', date('H:i:s', $i))
					->where($condition_other)
					->count_all_results('es_exhibition_mou_sign');
			}

			$is_booked = ($check > 0 || $other_user > 0) ? 'booked' : '';
			$is_booked_status = ($check > 0 || $other_user > 0) ? '<div class="small-box-footer">BOOKED</div>' : '';
			$html .= '<div class="col-lg-3 col-xs-6">
				<div class="small-box ' . $is_booked . '" data-time="' . date('H:i', $i) . '">
					<div class="inner">
						<h3 style="font-size: 20px; margin: 0">' . date("H:i", $i) . '</h3>
					</div>' . $is_booked_status . '
				</div>
			</div>';
		}

		echo $html;
		die();
	}

	function mou_re_schedule_validate()
	{
		$this->form_validation->set_rules('mou_sign_location', 'mou_sign_location*MOU location', 'trim|required');
		$this->form_validation->set_rules('booking_day', 'booking_day*Day', 'trim|required');
		$this->form_validation->set_rules('booking_date', 'booking_date*Date', 'trim|required');
		$this->form_validation->set_rules('booking_time', 'booking_time*Time', 'trim|required');

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}


	function mou_re_schedule_submit()
	{
		if ($this->mou_re_schedule_validate() !== true)
			show_404();

		$meeting = $this->db
			->where(mycolumn(), $this->input->get('id'))
			->get('es_exhibition_mou_sign')
			->row();

		$data = array(
			'exhibition_id' => $meeting->exhibition_id,
			'request_from_id' => $meeting->request_from_id,
			'mou_sign_location' => $this->input->post('mou_sign_location'),
			'request_to_id' => $meeting->request_to_id,
			'description' => $meeting->description,
			'commercial_value' => $meeting->commercial_value,
			'exhibition_day' => $this->input->post('booking_day'),
			'mou_sign_date' => $this->input->post('booking_date'),
			'mou_sign_time' => $this->input->post('booking_time') . ':00',
			'user_type_from' => 'exhibitor',
			'user_type_to' => $meeting->user_type_from,
			'created_on' => date('Y-m-d H:i:s'),
		);

		$this->db
			->insert('es_exhibition_mou_sign', $data);

		$this->db
			->where('id', $meeting->id)
			->update('es_exhibition_mou_sign', array(
				'is_deleted' => 1,
				'deleted_on' => date('Y-m-d H:i:s'),
			));



		$exhibition_data = $this->db
			->where('id', $meeting->exhibition_id)
			->get('es_exhibitions')
			->row();

		// send email
		$email_data = null;
		$title = $exhibition_data ? $exhibition_data->exhibition_title : '';
		$ReceiverEmail = '';
		$phone_number = '';
		$text_msg = '';

		$get_email_template = $this->db
			->where("title", "MOU_SIGNING_RE_SCHEDULE")
			->where("exhibition_id", $meeting->exhibition_id)
			->get('email_template')
			->row();

		$Subject = 'Email not configure!';
		$message = 'Email not configure!';
		if (isset($get_email_template)) {
			$Subject = $get_email_template->subject;
			$message = $get_email_template->message;
		}

		$user_fullname = '';
		if (isset($this->userdata)) {
			$first_name = isset($this->userdata->user_first_name) ? $this->userdata->user_first_name : '';
			$last_name = isset($this->userdata->user_last_name) ? $this->userdata->user_last_name : '';
			$user_fullname = trim($first_name . ' ' . $last_name);
		}

		if ($this->input->post('user_type') == 'officer') {
			$email_data = $this->db
				->where('id', $this->input->post('booking_to'))
				->get('es_officer')
				->row();

			$ReceiverEmail = $email_data->officer_email;

			$phone_number = $email_data->officer_phone;
			$text_msg = $user_fullname . ' has re-schedule a MoU Signing for ' . date('M d', strtotime($this->input->post('booking_date'))) . ', ' . date('H:i', strtotime($this->input->post('booking_time')));


			$Subject = str_replace('{NAME}', $email_data->contact_person, $Subject);
			$Subject = str_replace('{EMAIL}', $email_data->officer_email, $Subject);
			$Subject = str_replace('{PHONE}', $email_data->officer_phone, $Subject);
			$Subject = str_replace('{COMPANY}', $email_data->officer_company, $Subject);

			$message = str_replace('{NAME}', $email_data->contact_person, $message);
			$message = str_replace('{EMAIL}', $email_data->officer_email, $message);
			$message = str_replace('{PHONE}', $email_data->officer_phone, $message);
			$message = str_replace('{COMPANY}', $email_data->officer_company, $message);
		} else {
			$email_data = $this->db
				->where('id', $this->input->post('booking_to'))
				->get('es_customers')
				->row();

			$ReceiverEmail = $email_data->email;

			$phone_number = $email_data->phone;
			$text_msg = $user_fullname . ' has re-schedule a MoU signing for ' . date('M d', strtotime($this->input->post('booking_date'))) . ', ' . date('H:i', strtotime($this->input->post('booking_time')));

			$Subject = str_replace('{NAME}', $email_data->name, $Subject);
			$Subject = str_replace('{EMAIL}', $email_data->email, $Subject);
			$Subject = str_replace('{PHONE}', $email_data->phone, $Subject);
			$Subject = str_replace('{COMPANY}', $email_data->company, $Subject);

			$message = str_replace('{NAME}', $email_data->name, $message);
			$message = str_replace('{EMAIL}', $email_data->email, $message);
			$message = str_replace('{PHONE}', $email_data->phone, $message);
			$message = str_replace('{COMPANY}', $email_data->company, $message);
		}

		$user_email = (isset($this->userdata) && isset($this->userdata->user_email)) ? $this->userdata->user_email : '';
		$user_phone = (isset($this->userdata) && isset($this->userdata->user_phone)) ? $this->userdata->user_phone : '';
		$user_company = '';
		$user_website = '';

		$Subject = str_replace('{EVENT_NAME}', $title, $Subject);
		$Subject = str_replace('{SENDER_NAME}', $user_fullname, $Subject);
		$Subject = str_replace('{SENDER_EMAIL}', $user_email, $Subject);
		$Subject = str_replace('{SENDER_PHONE}', $user_phone, $Subject);
		$Subject = str_replace('{SENDER_COMPANY}', $user_company, $Subject);
		$Subject = str_replace('{SENDER_WEBSITE}', $user_website, $Subject);
		$Subject = str_replace('{APPOINTMENT_TIME}', date('M d', strtotime($this->input->post('booking_date'))) . ', ' . date('H:i', strtotime($this->input->post('booking_time'))), $Subject);
		$Subject = str_replace('{APPOINTMENT_AGENDA}', $meeting->description, $Subject);

		$message = str_replace('{EVENT_NAME}', $title, $message);
		$message = str_replace('{SENDER_NAME}', $user_fullname, $message);
		$message = str_replace('{SENDER_EMAIL}', $user_email, $message);
		$message = str_replace('{SENDER_PHONE}', $user_phone, $message);
		$message = str_replace('{SENDER_COMPANY}', $user_company, $message);
		$message = str_replace('{SENDER_WEBSITE}', $user_website, $message);
		$message = str_replace('{APPOINTMENT_TIME}', date('M d', strtotime($this->input->post('booking_date'))) . ', ' . date('H:i', strtotime($this->input->post('booking_time'))), $message);
		$message = str_replace('{APPOINTMENT_AGENDA}', $meeting->description, $message);

		if ($ReceiverEmail != "") {
			$this->db->insert('es_emails_cron', array(
				'type' => 'MOU_SIGNING_RE_SCHEDULE',
				'data' => null,
				'from_name' => $title,
				'email' => $ReceiverEmail,
				'subject' => $Subject,
				'message' => $message,
				'created_on' => date('Y-m-d H:i:s'),
			));
		}

		if ($phone_number != '') {
			$this->funcs->send_sms($phone_number, $text_msg);
		}


		$this->session->set_flashdata('message', 'MoU signing re-schedule request submitted successfully');
		redirect(base_url('mou_sign.html'));
	}



	function cancel()
	{

		$data = $this->db
			->where(mycolumn(), $this->input->get('id'))
			->get('es_exhibition_mou_sign')
			->row();

		$exhibition_data = $this->db
			->where('id', $data->exhibition_id)
			->get('es_exhibitions')
			->row();



		$this->db
			->where(mycolumn(), $this->input->get('id'))
			->update('es_exhibition_mou_sign', array(
				'is_approved' => 0,
				'approved_on' => null,
				'approved_by' => null,
				'is_canceled' => 1,
				'canceled_by' => $this->userdata->id,
				'canceled_on' => date('Y-m-d H:i:s')
			));

		// send email
		$email_data = null;
		$title = $exhibition_data->exhibition_title;
		$ReceiverEmail = '';
		$phone_number = '';
		$text_msg = '';

		$get_email_template = $this->db
			->where("title", "MOU_SIGNING_CANCELED")
			->where("exhibition_id", $data->exhibition_id)
			->get('email_template')
			->row();

		$Subject = 'Email not configure!';
		$message = 'Email not configure!';
		if (isset($get_email_template)) {
			$Subject = $get_email_template->subject;
			$message = $get_email_template->message;
		}

		if ($data->user_type_from == 'officer') {
			$email_data = $this->db
				->where('id', $data->request_from_id)
				->get('es_officer')
				->row();

			$ReceiverEmail = $email_data->officer_email;

			$phone_number = $email_data->officer_phone;

			$email_customer_data = $this->db
				->where('id', $data->request_from_id)
				->get('es_customers')
				->row();

			$text_msg = 'Your MoU signing with ' . $email_customer_data->name . ' of ' . $email_customer_data->company . ' for ' . date('M d', strtotime($data->mou_sign_date)) . ' at ' . date('H:i', strtotime($data->mou_sign_time)) . ' has been declined.';


			$Subject = str_replace('{NAME}', $email_data->contact_person, $Subject);
			$Subject = str_replace('{EMAIL}', $email_data->officer_email, $Subject);
			$Subject = str_replace('{PHONE}', $email_data->officer_phone, $Subject);
			$Subject = str_replace('{COMPANY}', $email_data->officer_company, $Subject);

			$message = str_replace('{NAME}', $email_data->contact_person, $message);
			$message = str_replace('{EMAIL}', $email_data->officer_email, $message);
			$message = str_replace('{PHONE}', $email_data->officer_phone, $message);
			$message = str_replace('{COMPANY}', $email_data->officer_company, $message);
		} else {
			$email_data = $this->db
				->where('id', $data->request_from_id)
				->get('es_customers')
				->row();

			$ReceiverEmail = $email_data->email;

			$phone_number = $email_data->phone;
			$text_msg = 'Your MoU singing with ' . $email_data->name . ' of ' . $email_data->company . ' for ' . date('M d', strtotime($data->mou_sign_date)) . ' at ' . date('H:i', strtotime($data->mou_sign_time)) . ' has been declined.';


			$Subject = str_replace('{NAME}', $email_data->name, $Subject);
			$Subject = str_replace('{EMAIL}', $email_data->email, $Subject);
			$Subject = str_replace('{PHONE}', $email_data->phone, $Subject);
			$Subject = str_replace('{COMPANY}', $email_data->company, $Subject);

			$message = str_replace('{NAME}', $email_data->name, $message);
			$message = str_replace('{EMAIL}', $email_data->email, $message);
			$message = str_replace('{PHONE}', $email_data->phone, $message);
			$message = str_replace('{COMPANY}', $email_data->company, $message);
		}
		$customer_data = $this->db
			->where('id', $data->request_from_id)
			->get('es_customers')
			->row();

		$Subject = str_replace('{EVENT_NAME}', $title, $Subject);
		$Subject = str_replace('{SENDER_NAME}', $customer_data->name, $Subject);
		$Subject = str_replace('{SENDER_EMAIL}', $customer_data->email, $Subject);
		$Subject = str_replace('{SENDER_PHONE}', $customer_data->phone, $Subject);
		$Subject = str_replace('{SENDER_COMPANY}', $customer_data->company, $Subject);
		$Subject = str_replace('{SENDER_WEBSITE}', $customer_data->url, $Subject);
		$Subject = str_replace('{SCHEDULE_TIME}', date('M d', strtotime($data->mou_sign_date)) . ' at ' . date('H:i', strtotime($data->mou_sign_time)), $Subject);
		$Subject = str_replace('{DESCRIPTION}', $data->description, $Subject);
		$Subject = str_replace('{COMMERCIAL_VALUE}', $data->commercial_value, $Subject);

		$message = str_replace('{EVENT_NAME}', $title, $message);
		$message = str_replace('{SENDER_NAME}', $customer_data->name, $message);
		$message = str_replace('{SENDER_EMAIL}', $customer_data->email, $message);
		$message = str_replace('{SENDER_PHONE}', $customer_data->phone, $message);
		$message = str_replace('{SENDER_COMPANY}', $customer_data->company, $message);
		$message = str_replace('{SENDER_WEBSITE}', $customer_data->url, $message);
		$message = str_replace('{SCHEDULE_TIME}', date('M d', strtotime($data->mou_sign_date)) . ' at ' . date('H:i', strtotime($data->mou_sign_time)), $message);
		$message = str_replace('{DESCRIPTION}', $data->description, $message);
		$Subject = str_replace('{COMMERCIAL_VALUE}', $data->commercial_value, $Subject);

		if ($ReceiverEmail != "") {
			$this->db->insert('es_emails_cron', array(
				'type' => 'MOU_SIGNING_CANCELED',
				'data' => null,
				'from_name' => $title,
				'email' => $ReceiverEmail,
				'subject' => $Subject,
				'message' => $message,
				'created_on' => date('Y-m-d H:i:s'),
			));
		}
		if ($phone_number != '') {
			$this->funcs->send_sms($phone_number, $text_msg);
		}

		$this->session->set_flashdata('message', 'MoU has been cancel successfully');
		redirect(base_url('mou_sign.html'));
	}

	function approved()
	{
		$data = $this->db
			->where(mycolumn(), $this->input->get('id'))
			->get('es_exhibition_mou_sign')
			->row();

		$exhibition_data = $this->db
			->where('id', $data->exhibition_id)
			->get('es_exhibitions')
			->row();

		$this->db
			->where(mycolumn(), $this->input->get('id'))
			->update('es_exhibition_mou_sign', array(
				'is_approved' => 1,
				'approved_on' => date('Y-m-d H:i:s'),
				'approved_by' => $this->userdata->id,
				'is_canceled' => 0,
				'canceled_by' => null,
				'canceled_on' => null
			));


		// send email
		$email_data = null;
		$title = $exhibition_data->exhibition_title;
		$ReceiverEmail = '';
		$phone_number = '';
		$text_msg = '';

		$get_email_template = $this->db
			->where("title", "MOU_SIGNING_ACCEPTED")
			->where("exhibition_id", $data->exhibition_id)
			->get('email_template')
			->row();

		$Subject = 'Email not configure!';
		$message = 'Email not configure!';
		if (isset($get_email_template)) {
			$Subject = $get_email_template->subject;
			$message = $get_email_template->message;
		}

		if ($data->user_type_from == 'officer') {
			$email_data = $this->db
				->where('id', $data->request_from_id)
				->get('es_officer')
				->row();

			$email_customer_data = $this->db
				->where('id', $data->request_from_id)
				->get('es_customers')
				->row();

			$ReceiverEmail = $email_data->officer_email;

			$phone_number = $email_data->officer_phone;
			$text_msg = 'Your request for MOU Signing' . $email_customer_data->name . ' of ' . $email_customer_data->company . ' for ' . date('M d', strtotime($data->mou_sign_date)) . ' at ' . date('H:i', strtotime($data->mou_sign_time)) . ' has been accepted. Please login for further details.';

			$Subject = str_replace('{NAME}', $email_data->contact_person, $Subject);
			$Subject = str_replace('{EMAIL}', $email_data->officer_email, $Subject);
			$Subject = str_replace('{PHONE}', $email_data->officer_phone, $Subject);
			$Subject = str_replace('{COMPANY}', $email_data->officer_company, $Subject);

			$message = str_replace('{NAME}', $email_data->contact_person, $message);
			$message = str_replace('{EMAIL}', $email_data->officer_email, $message);
			$message = str_replace('{PHONE}', $email_data->officer_phone, $message);
			$message = str_replace('{COMPANY}', $email_data->officer_company, $message);
		} else {
			$email_data = $this->db
				->where('id', $data->request_from_id)
				->get('es_customers')
				->row();

			$ReceiverEmail = $email_data->email;

			$phone_number = $email_data->phone;
			$text_msg = 'Your request for MOU Signing ' . $email_data->name . ' of ' . $email_data->company . ' for ' . date('M d', strtotime($data->mou_sign_date)) . ' at ' . date('H:i', strtotime($data->mou_sign_time)) . ' has been accepted. Please login for further details.';

			$Subject = str_replace('{NAME}', $email_data->name, $Subject);
			$Subject = str_replace('{EMAIL}', $email_data->email, $Subject);
			$Subject = str_replace('{PHONE}', $email_data->phone, $Subject);
			$Subject = str_replace('{COMPANY}', $email_data->company, $Subject);

			$message = str_replace('{NAME}', $email_data->name, $message);
			$message = str_replace('{EMAIL}', $email_data->email, $message);
			$message = str_replace('{PHONE}', $email_data->phone, $message);
			$message = str_replace('{COMPANY}', $email_data->company, $message);
		}

		$customer_data = $this->db
			->where('id', $data->request_from_id)
			->get('es_customers')
			->row();

		$Subject = str_replace('{EVENT_NAME}', $title, $Subject);
		$Subject = str_replace('{SENDER_NAME}', $customer_data->name, $Subject);
		$Subject = str_replace('{SENDER_EMAIL}', $customer_data->email, $Subject);
		$Subject = str_replace('{SENDER_PHONE}', $customer_data->phone, $Subject);
		$Subject = str_replace('{SENDER_COMPANY}', $customer_data->company, $Subject);
		$Subject = str_replace('{SENDER_WEBSITE}', $customer_data->url, $Subject);
		$Subject = str_replace('{SCHEDULE_TIME}', date('M d', strtotime($data->mou_sign_date)) . ' at ' . date('H:i', strtotime($data->mou_sign_time)), $Subject);
		$Subject = str_replace('{DESCRIPTION}', $data->description, $Subject);
		$Subject = str_replace('{COMMERCIAL_VALUE}', $data->commercial_value, $Subject);

		$message = str_replace('{EVENT_NAME}', $title, $message);
		$message = str_replace('{SENDER_NAME}', $customer_data->name, $message);
		$message = str_replace('{SENDER_EMAIL}', $customer_data->email, $message);
		$message = str_replace('{SENDER_PHONE}', $customer_data->phone, $message);
		$message = str_replace('{SENDER_COMPANY}', $customer_data->company, $message);
		$message = str_replace('{SENDER_WEBSITE}', $customer_data->url, $message);
		$message = str_replace('{SCHEDULE_TIME}', date('M d', strtotime($data->mou_sign_date)) . ' at ' . date('H:i', strtotime($data->mou_sign_time)), $message);
		$message = str_replace('{DESCRIPTION}', $data->description, $message);
		$Subject = str_replace('{COMMERCIAL_VALUE}', $data->commercial_value, $Subject);

		if ($ReceiverEmail != "") {
			$this->db->insert('es_emails_cron', array(
				'type' => 'MOU_SIGNING_ACCEPTED',
				'data' => null,
				'from_name' => $title,
				'email' => $ReceiverEmail,
				'subject' => $Subject,
				'message' => $message,
				'created_on' => date('Y-m-d H:i:s'),
			));
		}
		if ($phone_number != '') {
			$this->funcs->send_sms($phone_number, $text_msg);
		}

		$this->session->set_flashdata('message', 'Mou has been approved successfully');
		redirect(base_url('mou_sign.html'));
	}

	function crd_add()
	{
		$data['exhibitions'] = $this->db
			->select('id, exhibition_title')
			->where('is_deleted', 0)
			->get('es_exhibitions')
			->result();

		$data['customers'] = $this->db
			->select('id, name, company')
			->where('is_deleted', 0)
			->where('is_active', 1)
			->get('es_customers')
			->result();

		$this->load->view('includes/after_login/head');
		$this->load->view('mou_report/add', $data);
	}

	function crd_add_validate()
	{
		$this->form_validation->set_rules('exhibition_id', 'exhibition_id*Exhibition', 'trim|required|integer');
		$this->form_validation->set_rules('request_from_id', 'request_from_id*Request From Customer', 'trim|required|integer');
		$this->form_validation->set_rules('request_to_id', 'request_to_id*Request To Customer', 'trim|required|integer');
		$this->form_validation->set_rules('mou_sign_location', 'mou_sign_location*Signing Location', 'trim|required');
		$this->form_validation->set_rules('exhibition_day', 'exhibition_day*Exhibition Day', 'trim|required');
		$this->form_validation->set_rules('booking_date', 'booking_date*Date', 'trim|required');
		$this->form_validation->set_rules('booking_time', 'booking_time*Time', 'trim|required');
		$this->form_validation->set_rules('commercial_value', 'commercial_value*Commercial Value', 'trim|required');
		$this->form_validation->set_rules('description', 'description*Description', 'trim|required');

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function crd_add_submit()
	{
		if ($this->crd_add_validate() !== true)
			show_404();

		$data = array(
			'exhibition_id' => $this->input->post('exhibition_id'),
			'request_from_id' => $this->input->post('request_from_id'),
			'request_to_id' => $this->input->post('request_to_id'),
			'mou_sign_location' => $this->input->post('mou_sign_location'),
			'exhibition_day' => $this->input->post('exhibition_day'),
			'mou_sign_date' => $this->input->post('booking_date'),
			'mou_sign_time' => $this->input->post('booking_time') . ':00',
			'user_type_from' => 'exhibitor',
			'user_type_to' => 'exhibitor',
			'description' => $this->input->post('description'),
			'commercial_value' => $this->input->post('commercial_value'),
			'is_approved' => 0,
			'is_canceled' => 0,
			'is_deleted' => 0,
			'created_on' => date('Y-m-d H:i:s'),
		);

		$this->db->insert('es_exhibition_mou_sign', $data);

		$this->session->set_flashdata('message', 'MoU Signing entry has been added successfully');
		redirect($this->myparent);
	}

	function download_template()
	{
		$lib_path = APPPATH . 'libraries' . DIRECTORY_SEPARATOR . 'SimpleXLSXGen.php';
		$lib_path = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $lib_path);
		require_once($lib_path);

		$headers = array(
			array(
				'Exhibition Day', 
				'Sign Date', 
				'Sign Time', 
				'Request From Email', 
				'Request To Email', 
				'Location', 
				'Commercial Value', 
				'Description'
			),
			array(
				'Day 1',
				'2026-07-01',
				'12:00',
				'sender@example.com',
				'receiver@example.com',
				'Meeting Room A',
				'50000 USD',
				"Sample MoU signing details.\nLine breaks within this cell are fully supported."
			)
		);

		$xlsx = \Shuchkin\SimpleXLSXGen::fromArray($headers);
		$xlsx->downloadAs('mou_bulk_import_template.xlsx');
		exit;
	}


	function bulk_import_validate()
	{
		$exhibition_id = $this->input->post('exhibition_id');
		if (empty($exhibition_id)) {
			echo json_encode(array('status' => 'error', 'message' => 'Please select an Exhibition first.'));
			return;
		}

		$exhibition = $this->db->select('id, exhibition_title')
			->where('id', $exhibition_id)
			->where('is_deleted', 0)
			->get('es_exhibitions')
			->row();

		if (!$exhibition) {
			echo json_encode(array('status' => 'error', 'message' => 'Selected Exhibition does not exist.'));
			return;
		}

		if (empty($_FILES['import_file']['name'])) {
			echo json_encode(array('status' => 'error', 'message' => 'Please upload an Excel file.'));
			return;
		}

		$config['upload_path'] = FCPATH . 'uploads' . DIRECTORY_SEPARATOR;
		$config['upload_path'] = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $config['upload_path']);
		
		if (!is_dir($config['upload_path'])) {
			mkdir($config['upload_path'], 0777, true);
		}

		$config['allowed_types'] = 'xlsx';
		$config['max_size'] = 5120;
		$config['encrypt_name'] = TRUE;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('import_file')) {
			echo json_encode(array('status' => 'error', 'message' => $this->upload->display_errors('', '')));
			return;
		}

		$upload_data = $this->upload->data();
		$file_path = $upload_data['full_path'];

		$lib_path = APPPATH . 'libraries' . DIRECTORY_SEPARATOR . 'SimpleXLSX.php';
		$lib_path = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $lib_path);
		require_once($lib_path);

		if ($xlsx = \Shuchkin\SimpleXLSX::parse($file_path)) {
			$rows = $xlsx->rows();
			
			if (is_file($file_path)) {
				unlink($file_path);
			}

			if (count($rows) <= 1) {
				echo json_encode(array('status' => 'error', 'message' => 'Excel file is empty or contains no records.'));
				return;
			}

			$expected_headers = array(
				'exhibition day',
				'sign date',
				'sign time',
				'request from email',
				'request to email',
				'location',
				'commercial value',
				'description'
			);

			$file_headers = array_map(function($header) {
				return trim(strtolower($header));
			}, $rows[0]);

			$header_errors = array();
			for ($i = 0; $i < 8; $i++) {
				if (!isset($file_headers[$i]) || $file_headers[$i] !== $expected_headers[$i]) {
					$header_errors[] = "Column " . ($i + 1) . " should be '" . $expected_headers[$i] . "' (found: '" . ($file_headers[$i] ?? 'none') . "')";
				}
			}

			if (!empty($header_errors)) {
				echo json_encode(array(
					'status' => 'error', 
					'message' => 'Excel headers mismatch. Please use the downloaded template.', 
					'details' => $header_errors
				));
				return;
			}

			$validated_rows = array();
			$has_errors = false;

			for ($idx = 1; $idx < count($rows); $idx++) {
				$row = $rows[$idx];
				
				$is_empty_row = true;
				foreach ($row as $cell) {
					if (trim($cell) !== '') {
						$is_empty_row = false;
						break;
					}
				}
				if ($is_empty_row) {
					continue;
				}

				$row_errors = array();

				$exhibition_day = trim($row[0] ?? '');
				$booking_date = trim($row[1] ?? '');
				if ($booking_date !== '') {
					$date_parts = explode(' ', $booking_date);
					$booking_date = trim($date_parts[0]);
				}
				
				$booking_time = trim($row[2] ?? '');
				if ($booking_time !== '') {
					$time_parts = explode(' ', $booking_time);
					$booking_time_val = end($time_parts);
					$time_subparts = explode(':', $booking_time_val);
					if (count($time_subparts) >= 2) {
						$booking_time = $time_subparts[0] . ':' . $time_subparts[1];
					} else {
						$booking_time = $booking_time_val;
					}
				}
				
				$request_from_email = trim($row[3] ?? '');
				$request_to_email = trim($row[4] ?? '');
				$mou_sign_location = trim($row[5] ?? '');
				$commercial_value = trim($row[6] ?? '');
				$description = trim($row[7] ?? '');


				if ($exhibition_day === '') $row_errors[] = 'Exhibition Day is required.';
				if ($booking_date === '') $row_errors[] = 'Sign Date is required.';
				if ($booking_time === '') $row_errors[] = 'Sign Time is required.';
				if ($request_from_email === '') $row_errors[] = 'Request From Email is required.';
				if ($request_to_email === '') $row_errors[] = 'Request To Email is required.';
				if ($mou_sign_location === '') $row_errors[] = 'Location is required.';
				if ($commercial_value === '') $row_errors[] = 'Commercial Value is required.';
				if ($description === '') $row_errors[] = 'Description is required.';

				$valid_days = array('Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5');
				if ($exhibition_day !== '' && !in_array($exhibition_day, $valid_days)) {
					$row_errors[] = 'Exhibition Day must be Day 1, Day 2, Day 3, Day 4, or Day 5.';
				}

				if ($booking_date !== '') {
					$dt = \DateTime::createFromFormat('Y-m-d', $booking_date);
					if (!$dt || $dt->format('Y-m-d') !== $booking_date) {
						$row_errors[] = 'Sign Date must be a valid date in YYYY-MM-DD format.';
					}
				}

				if ($booking_time !== '') {
					if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/', $booking_time)) {
						$row_errors[] = 'Sign Time must be in HH:MM format.';
					}
				}

				$customer_from_id = null;
				$customer_from_name = '';
				if ($request_from_email !== '') {
					$cust_from = $this->db->select('id, name, company')
						->where('email', $request_from_email)
						->where('is_deleted', 0)
						->where('is_active', 1)
						->get('es_customers')
						->row();
					if ($cust_from) {
						$customer_from_id = $cust_from->id;
						$customer_from_name = $cust_from->company . ' (' . $cust_from->name . ')';
					} else {
						$row_errors[] = "Request From Email ('{$request_from_email}') is not registered as an active Exhibitor.";
					}
				}

				$customer_to_id = null;
				$customer_to_name = '';
				if ($request_to_email !== '') {
					$cust_to = $this->db->select('id, name, company')
						->where('email', $request_to_email)
						->where('is_deleted', 0)
						->where('is_active', 1)
						->get('es_customers')
						->row();
					if ($cust_to) {
						$customer_to_id = $cust_to->id;
						$customer_to_name = $cust_to->company . ' (' . $cust_to->name . ')';
					} else {
						$row_errors[] = "Request To Email ('{$request_to_email}') is not registered as an active Exhibitor.";
					}
				}

				if ($request_from_email !== '' && $request_to_email !== '' && $request_from_email === $request_to_email) {
					$row_errors[] = 'Sender and Receiver emails must be different.';
				}

				if ($customer_from_id && $customer_to_id && $booking_date !== '' && $booking_time !== '') {
					$formatted_time = date('H:i:s', strtotime($booking_time));
					$condition = "((user_type_from = 'exhibitor' AND request_from_id = {$customer_from_id}) OR (user_type_to = 'exhibitor' AND request_to_id = {$customer_to_id}))";
					$conflict_count = $this->db->where('exhibition_id', $exhibition_id)
						->where('is_approved', 1)
						->where('is_deleted', 0)
						->where('mou_sign_date', $booking_date)
						->where('mou_sign_time', $formatted_time)
						->where($condition)
						->count_all_results('es_exhibition_mou_sign');
					if ($conflict_count > 0) {
						$row_errors[] = 'Scheduling conflict: Either Sender or Receiver is already booked for an approved MoU at this date and time.';
					}
				}

				if (!empty($row_errors)) {
					$has_errors = true;
				}

				$validated_rows[] = array(
					'excel_row_num' => $idx + 1,
					'exhibition_day' => $exhibition_day,
					'booking_date' => $booking_date,
					'booking_time' => $booking_time,
					'request_from_email' => $request_from_email,
					'request_from_id' => $customer_from_id,
					'request_from_name' => $customer_from_name,
					'request_to_email' => $request_to_email,
					'request_to_id' => $customer_to_id,
					'request_to_name' => $customer_to_name,
					'mou_sign_location' => $mou_sign_location,
					'commercial_value' => $commercial_value,
					'description' => $description,
					'status' => empty($row_errors) ? 'valid' : 'invalid',
					'errors' => $row_errors
				);
			}

			echo json_encode(array(
				'status' => 'success',
				'has_errors' => $has_errors,
				'rows' => $validated_rows
			));
		} else {
			if (is_file($file_path)) {
				unlink($file_path);
			}
			echo json_encode(array('status' => 'error', 'message' => 'Failed to parse Excel file: ' . \Shuchkin\SimpleXLSX::parseError()));
		}
	}

	function bulk_import_confirm()
	{
		$exhibition_id = $this->input->post('exhibition_id');
		$auto_approve = $this->input->post('auto_approve') ? 1 : 0;
		$rows_json = $this->input->post('rows');

		if (empty($exhibition_id)) {
			echo json_encode(array('status' => 'error', 'message' => 'Exhibition ID is missing.'));
			return;
		}

		if (empty($rows_json)) {
			echo json_encode(array('status' => 'error', 'message' => 'No import data provided.'));
			return;
		}

		$import_rows = json_decode($rows_json, true);
		if (!is_array($import_rows)) {
			echo json_encode(array('status' => 'error', 'message' => 'Invalid import data format.'));
			return;
		}

		$exhibition = $this->db->where('id', $exhibition_id)->where('is_deleted', 0)->get('es_exhibitions')->row();
		if (!$exhibition) {
			echo json_encode(array('status' => 'error', 'message' => 'Exhibition not found.'));
			return;
		}

		$this->db->trans_start();

		$insert_count = 0;
		foreach ($import_rows as $row) {
			if (($row['status'] ?? '') !== 'valid') {
				continue;
			}

			$data = array(
				'exhibition_id' => $exhibition_id,
				'request_from_id' => $row['request_from_id'],
				'request_to_id' => $row['request_to_id'],
				'mou_sign_location' => $row['mou_sign_location'],
				'exhibition_day' => $row['exhibition_day'],
				'mou_sign_date' => $row['booking_date'],
				'mou_sign_time' => date('H:i:s', strtotime($row['booking_time'])),
				'user_type_from' => 'exhibitor',
				'user_type_to' => 'exhibitor',
				'description' => $row['description'],
				'commercial_value' => $row['commercial_value'],
				'is_approved' => $auto_approve,
				'approved_by' => $auto_approve ? $this->userdata->id : null,
				'approved_on' => $auto_approve ? date('Y-m-d H:i:s') : null,
				'is_canceled' => 0,
				'is_deleted' => 0,
				'created_on' => date('Y-m-d H:i:s'),
			);

			$this->db->insert('es_exhibition_mou_sign', $data);
			$insert_count++;
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(array('status' => 'error', 'message' => 'Database transaction failed. Data was not saved.'));
		} else {
			$this->session->set_flashdata('message', "{$insert_count} MoU Signing records successfully imported.");
			echo json_encode(array('status' => 'success', 'inserted' => $insert_count));
		}
	}

	function get_mou_locations_json()
	{
		$exhibition_id = $this->input->post('exhibition_id');
		$locations = $this->db->select('id, location')
			->where('exhibition_id', $exhibition_id)
			->where('type', 'mou_location')
			->get('location_for_meeting')
			->result();
		echo json_encode($locations);
	}
}
