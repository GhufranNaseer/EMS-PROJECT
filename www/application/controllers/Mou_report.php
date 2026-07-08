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
				->where('is_deleted', 0)
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
					->where('is_deleted', 0)
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

		// Check for conflict before approving (Issue 6)
		$condition = "((user_type_from = '{$data->user_type_from}' AND request_from_id = {$data->request_from_id}) OR (user_type_to = '{$data->user_type_to}' AND request_to_id = {$data->request_to_id}))";
		$conflict_count = $this->db->where('exhibition_id', $data->exhibition_id)
			->where('is_approved', 1)
			->where('is_deleted', 0)
			->where('id !=', $data->id)
			->where('mou_sign_date', $data->mou_sign_date)
			->where('mou_sign_time', $data->mou_sign_time)
			->where($condition)
			->count_all_results('es_exhibition_mou_sign');

		if ($conflict_count > 0) {
			$this->session->set_flashdata('error', 'Cannot approve: Either Sender or Receiver is already booked for another approved MoU at this date and time.');
			redirect(base_url('mou_sign.html'));
			return;
		}

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

		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		}

		$exhibition_id = $this->input->post('exhibition_id');
		$from_id = $this->input->post('request_from_id');
		$to_id = $this->input->post('request_to_id');
		$day = $this->input->post('exhibition_day');
		$date = $this->input->post('booking_date');
		$time = $this->input->post('booking_time');
		$location = $this->input->post('mou_sign_location');
		$commercial_value = $this->input->post('commercial_value');

		// Validate Commercial Value currency format
		if (!preg_match('/^(\d+(?:\.\d+)?)\s*(USD|PKR)$/i', $commercial_value, $matches)) {
			return $this->common->doError(func_num_args(), "Commercial Value must be a numeric amount followed by USD or PKR.");
		}
		$_POST['commercial_value'] = $matches[1] . ' ' . strtoupper($matches[2]);

		// 1. Sender and Receiver check
		if ($from_id == $to_id) {
			return $this->common->doError(func_num_args(), "Request From and Request To customers must be different.");
		}

		// 2. Validate exhibition existence
		$exhibition = $this->db->where('id', $exhibition_id)->where('is_deleted', 0)->get('es_exhibitions')->row();
		if (!$exhibition) {
			return $this->common->doError(func_num_args(), "Selected Exhibition does not exist.");
		}

		// 3. Verify event dates & days
		$event_dates = $this->db->where('exhibition_id', $exhibition_id)->order_by('date', 'ASC')->get('es_exhibition_date')->result();
		$valid_days_map = array();
		$valid_dates_map = array();
		$day_counter = 1;
		foreach ($event_dates as $ed) {
			$day_name = 'Day ' . $day_counter;
			$valid_days_map[$day_name] = $ed->date;
			
			$open = !empty($ed->open_time) ? $ed->open_time : '09:00:00';
			$close = !empty($ed->closing_time) ? $ed->closing_time : '18:00:00';
			$valid_dates_map[$ed->date] = array('day' => $day_name, 'open' => $open, 'close' => $close);
			$day_counter++;
		}

		if (!isset($valid_days_map[$day])) {
			return $this->common->doError(func_num_args(), "Exhibition Day '{$day}' is invalid for this event. Maximum allowed day is Day " . count($valid_days_map) . ".");
		}

		if (!isset($valid_dates_map[$date])) {
			return $this->common->doError(func_num_args(), "Date '{$date}' is not configured for this exhibition.");
		}

		if ($valid_days_map[$day] !== $date) {
			return $this->common->doError(func_num_args(), "Mismatch: Exhibition Day '{$day}' is configured for date '{$valid_days_map[$day]}', but you selected date '{$date}'.");
		}

		// 4. Validate time range (operating hours)
		$formatted_time = date('H:i:s', strtotime($time));
		$open_time = $valid_dates_map[$date]['open'];
		$close_time = $valid_dates_map[$date]['close'];
		if ($formatted_time < $open_time || $formatted_time > $close_time) {
			return $this->common->doError(func_num_args(), "Sign time '{$time}' is outside event operating hours (" . date('H:i', strtotime($open_time)) . " to " . date('H:i', strtotime($close_time)) . ").");
		}

		// 5. Validate location (map to database casing if it matches case-insensitively)
		$event_locations = $this->db->select('location')
			->where('exhibition_id', $exhibition_id)
			->where('type', 'mou_location')
			->get('location_for_meeting')
			->result();
		
		$locations_map = array();
		foreach ($event_locations as $el) {
			$locations_map[strtolower(trim($el->location))] = $el->location;
		}

		$loc_key = strtolower(trim($location));
		if (isset($locations_map[$loc_key])) {
			$_POST['mou_sign_location'] = $locations_map[$loc_key];
		}

		// 6. Validate exhibitor bookings (event participation)
		$from_booked = $this->db->where('exhibition_id', $exhibition_id)
			->where('customer_id', $from_id)
			->where('is_canceled', 0)
			->count_all_results('es_exhibition_booking');
		if ($from_booked === 0) {
			return $this->common->doError(func_num_args(), "The 'Request From' customer is not a registered exhibitor for this exhibition.");
		}

		$to_booked = $this->db->where('exhibition_id', $exhibition_id)
			->where('customer_id', $to_id)
			->where('is_canceled', 0)
			->count_all_results('es_exhibition_booking');
		if ($to_booked === 0) {
			return $this->common->doError(func_num_args(), "The 'Request To' customer is not a registered exhibitor for this exhibition.");
		}

		// 7. Check for approved conflicts
		$condition = "((user_type_from = 'exhibitor' AND request_from_id = {$from_id}) OR (user_type_to = 'exhibitor' AND request_to_id = {$to_id}))";
		$conflict_count = $this->db->where('exhibition_id', $exhibition_id)
			->where('is_approved', 1)
			->where('is_deleted', 0)
			->where('mou_sign_date', $date)
			->where('mou_sign_time', $formatted_time)
			->where($condition)
			->count_all_results('es_exhibition_mou_sign');

		if ($conflict_count > 0) {
			return $this->common->doError(func_num_args(), "Scheduling conflict: Either Sender or Receiver is already booked for an approved MoU at this date and time.");
		}

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
		$exhibition_id = $this->input->get('exhibition_id');
		if (empty($exhibition_id)) {
			show_error('Exhibition ID is required to download the template.');
		}

		$exhibition = $this->db->select('id, exhibition_title')
			->where('id', $exhibition_id)
			->where('is_deleted', 0)
			->get('es_exhibitions')
			->row();

		if (!$exhibition) {
			show_error('Selected Exhibition does not exist.');
		}

		// 1. Fetch active event exhibitors
		$exhibitors = $this->db->select('C.company, C.name, C.email')
			->from('es_exhibition_booking as B')
			->join('es_customers as C', 'B.customer_id = C.id')
			->where('B.exhibition_id', $exhibition_id)
			->where('B.is_canceled', 0)
			->where('C.is_deleted', 0)
			->where('C.is_active', 1)
			->group_by('C.id')
			->get()
			->result();

		$exhibitors_data = array(
			array('Company Name', 'Contact Person', 'Email Address')
		);
		$sample_sender_email = 'sender@example.com';
		$sample_receiver_email = 'receiver@example.com';
		if (count($exhibitors) > 0) {
			$sample_sender_email = $exhibitors[0]->email;
			if (isset($exhibitors[1])) {
				$sample_receiver_email = $exhibitors[1]->email;
			} else {
				$sample_receiver_email = $exhibitors[0]->email;
			}
		}
		foreach ($exhibitors as $e) {
			$exhibitors_data[] = array(
				$e->company,
				$e->name,
				$e->email
			);
		}

		// 2. Fetch event dates
		$dates = $this->db->select('date, open_time, closing_time')
			->where('exhibition_id', $exhibition_id)
			->order_by('date', 'ASC')
			->get('es_exhibition_date')
			->result();

		$dates_data = array(
			array('Exhibition Day', 'Sign Date', 'Operating Hours')
		);
		$day_counter = 1;
		$sample_day = 'Day 1';
		$sample_date = date('Y-m-d');
		foreach ($dates as $d) {
			$day_name = 'Day ' . $day_counter;
			if ($day_counter === 1) {
				$sample_date = $d->date;
			}
			$open = !empty($d->open_time) ? date('H:i', strtotime($d->open_time)) : '09:00';
			$close = !empty($d->closing_time) ? date('H:i', strtotime($d->closing_time)) : '18:00';
			$dates_data[] = array(
				$day_name,
				$d->date,
				$open . ' - ' . $close
			);
			$day_counter++;
		}

		// 3. Fetch event locations
		$locations = $this->db->select('location')
			->where('exhibition_id', $exhibition_id)
			->where('type', 'mou_location')
			->get('location_for_meeting')
			->result();

		$locations_data = array(
			array('Location Name')
		);
		$sample_location = 'Meeting Room A';
		if (count($locations) > 0) {
			$sample_location = $locations[0]->location;
		}
		foreach ($locations as $l) {
			$locations_data[] = array(
				$l->location
			);
		}

		// 4. Generate Main Import Sheet
		$main_sheet_data = array(
			array('EVENT ID', (int)$exhibition_id),
			array('EVENT NAME', $exhibition->exhibition_title),
			array('INSTRUCTIONS: Please do not modify rows 1-3. Fill your MoU details from Row 5 onwards. Reference tabs at the bottom contain valid exhibitors, locations, and dates.'),
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
				$sample_day,
				$sample_date,
				'12:00',
				$sample_sender_email,
				$sample_receiver_email,
				$sample_location,
				'50000 USD',
				"Sample MoU signing details.\nLine breaks within this cell are fully supported."
			)
		);

		$lib_path = APPPATH . 'libraries' . DIRECTORY_SEPARATOR . 'SimpleXLSXGen.php';
		$lib_path = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $lib_path);
		require_once($lib_path);

		$xlsx = \Shuchkin\SimpleXLSXGen::fromArray($main_sheet_data, 'MoU Sign Import');
		$xlsx->addSheet($exhibitors_data, 'Valid Exhibitors');
		$xlsx->addSheet($locations_data, 'Valid Locations');
		$xlsx->addSheet($dates_data, 'Exhibition Dates & Days');

		$safe_title = preg_replace('/[^A-Za-z0-9_\-]/', '_', $exhibition->exhibition_title);
		$filename = 'mou_bulk_import_' . $safe_title . '.xlsx';
		$xlsx->downloadAs($filename);
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

			if (count($rows) <= 4) {
				echo json_encode(array('status' => 'error', 'message' => 'Excel file is empty or contains no records. It must contain metadata in rows 1-3, headers in row 4, and data starting at row 5.'));
				return;
			}

			// Validate Event ID metadata
			$excel_event_id = isset($rows[0][1]) ? (int)trim($rows[0][1]) : 0;
			if ($excel_event_id !== (int)$exhibition_id) {
				echo json_encode(array(
					'status' => 'error',
					'message' => 'Event mismatch! The uploaded template belongs to Event ID ' . $excel_event_id . ', but you selected "' . $exhibition->exhibition_title . '" (ID: ' . $exhibition_id . '). Please upload the correct template for the selected event.'
				));
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
			}, $rows[3]);

			$header_errors = array();
			for ($i = 0; $i < 8; $i++) {
				if (!isset($file_headers[$i]) || $file_headers[$i] !== $expected_headers[$i]) {
					$header_errors[] = "Column " . ($i + 1) . " should be '" . $expected_headers[$i] . "' (found: '" . ($file_headers[$i] ?? 'none') . "')";
				}
			}

			if (!empty($header_errors)) {
				echo json_encode(array(
					'status' => 'error', 
					'message' => 'Excel headers mismatch in Row 4. Please use the downloaded template.', 
					'details' => $header_errors
				));
				return;
			}

			// Fetch active exhibitors registered for this specific event
			$event_exhibitors = $this->db->select('C.id, C.name, C.company, C.email')
				->from('es_exhibition_booking as B')
				->join('es_customers as C', 'B.customer_id = C.id')
				->where('B.exhibition_id', $exhibition_id)
				->where('B.is_canceled', 0)
				->where('C.is_deleted', 0)
				->where('C.is_active', 1)
				->get()
				->result();

			$exhibitor_map = array();
			foreach ($event_exhibitors as $ee) {
				$exhibitor_map[strtolower(trim($ee->email))] = array(
					'id' => $ee->id,
					'name' => $ee->name,
					'company' => $ee->company
				);
			}

			// Fetch event dates and build maps for days and dates
			$event_dates_db = $this->db->select('date, open_time, closing_time')
				->where('exhibition_id', $exhibition_id)
				->order_by('date', 'ASC')
				->get('es_exhibition_date')
				->result();

			$valid_days_map = array();
			$valid_dates_map = array();
			$day_counter = 1;
			foreach ($event_dates_db as $ed) {
				$day_name = 'Day ' . $day_counter;
				$open = !empty($ed->open_time) ? $ed->open_time : '09:00:00';
				$close = !empty($ed->closing_time) ? $ed->closing_time : '18:00:00';
				
				$valid_days_map[$day_name] = $ed->date;
				$valid_dates_map[$ed->date] = array('day' => $day_name, 'open' => $open, 'close' => $close);
				$day_counter++;
			}

			// Fetch registered locations for this specific event
			$event_locations_db = $this->db->select('location')
				->where('exhibition_id', $exhibition_id)
				->where('type', 'mou_location')
				->get('location_for_meeting')
				->result();

			$valid_locations = array();
			$locations_map = array();
			foreach ($event_locations_db as $el) {
				$loc_key = strtolower(trim($el->location));
				$valid_locations[] = $loc_key;
				$locations_map[$loc_key] = $el->location;
			}

			$validated_rows = array();
			$has_errors = false;
			$sheet_bookings = array();

			for ($idx = 4; $idx < count($rows); $idx++) {
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
				$mou_sign_location_original = isset($locations_map[strtolower($mou_sign_location)]) ? $locations_map[strtolower($mou_sign_location)] : $mou_sign_location;
				$commercial_value = trim($row[6] ?? '');
				$description = trim($row[7] ?? '');

				if ($exhibition_day === '') $row_errors[] = 'Exhibition Day is required.';
				if ($booking_date === '') $row_errors[] = 'Sign Date is required.';
				if ($booking_time === '') $row_errors[] = 'Sign Time is required.';
				if ($request_from_email === '') $row_errors[] = 'Request From Email is required.';
				if ($request_to_email === '') $row_errors[] = 'Request To Email is required.';
				if ($mou_sign_location === '') $row_errors[] = 'Location is required.';
				if ($commercial_value === '') {
					$row_errors[] = 'Commercial Value is required.';
				} else {
					if (preg_match('/^(\d+(?:\.\d+)?)\s*(USD|PKR)$/i', $commercial_value, $matches)) {
						$commercial_value = $matches[1] . ' ' . strtoupper($matches[2]);
					} else {
						$row_errors[] = 'Commercial Value must be a numeric amount followed by USD or PKR (e.g., 5000 USD or 5000 PKR).';
					}
				}
				if ($description === '') $row_errors[] = 'Description is required.';

				// Validation: Day Index Check
				if ($exhibition_day !== '') {
					if (!isset($valid_days_map[$exhibition_day])) {
						$row_errors[] = 'Exhibition Day "' . $exhibition_day . '" is invalid for this event. Max allowed day is Day ' . count($valid_days_map) . '.';
					}
				}

				// Validation: Date Range and format check
				if ($booking_date !== '') {
					$dt = \DateTime::createFromFormat('Y-m-d', $booking_date);
					if (!$dt || $dt->format('Y-m-d') !== $booking_date) {
						$row_errors[] = 'Sign Date must be a valid date in YYYY-MM-DD format.';
					} else {
						if (!isset($valid_dates_map[$booking_date])) {
							$row_errors[] = 'Sign Date "' . $booking_date . '" is not configured for this exhibition.';
						} else if ($exhibition_day !== '' && isset($valid_days_map[$exhibition_day])) {
							// Cross-validation: Day index must match configured date
							if ($valid_days_map[$exhibition_day] !== $booking_date) {
								$row_errors[] = 'Mismatch: "' . $exhibition_day . '" is configured for date ' . $valid_days_map[$exhibition_day] . ', but Excel lists date ' . $booking_date . '.';
							}
						}
					}
				}

				// Validation: Location check
				if ($mou_sign_location !== '') {
					if (!in_array(strtolower($mou_sign_location), $valid_locations)) {
						$row_errors[] = 'Location "' . $mou_sign_location . '" is not registered for this exhibition. See the "Valid Locations" reference sheet.';
					}
				}

				// Validation: Operating hours and format check
				if ($booking_time !== '') {
					if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/', $booking_time)) {
						$row_errors[] = 'Sign Time must be in HH:MM format.';
					} else if ($booking_date !== '' && isset($valid_dates_map[$booking_date])) {
						$formatted_time = date('H:i:s', strtotime($booking_time));
						$open_time = $valid_dates_map[$booking_date]['open'];
						$close_time = $valid_dates_map[$booking_date]['close'];
						if ($formatted_time < $open_time || $formatted_time > $close_time) {
							$row_errors[] = 'Sign Time "' . $booking_time . '" is outside event operating hours (' . date('H:i', strtotime($open_time)) . ' to ' . date('H:i', strtotime($close_time)) . ').';
						}
					}
				}

				// Validation: Event-bound Exhibitor check
				$customer_from_id = null;
				$customer_from_name = '';
				if ($request_from_email !== '') {
					$from_email_key = strtolower($request_from_email);
					if (isset($exhibitor_map[$from_email_key])) {
						$customer_from_id = $exhibitor_map[$from_email_key]['id'];
						$customer_from_name = $exhibitor_map[$from_email_key]['company'] . ' (' . $exhibitor_map[$from_email_key]['name'] . ')';
					} else {
						$row_errors[] = "Request From Email ('{$request_from_email}') is not registered as an active Exhibitor for this specific exhibition.";
					}
				}

				$customer_to_id = null;
				$customer_to_name = '';
				if ($request_to_email !== '') {
					$to_email_key = strtolower($request_to_email);
					if (isset($exhibitor_map[$to_email_key])) {
						$customer_to_id = $exhibitor_map[$to_email_key]['id'];
						$customer_to_name = $exhibitor_map[$to_email_key]['company'] . ' (' . $exhibitor_map[$to_email_key]['name'] . ')';
					} else {
						$row_errors[] = "Request To Email ('{$request_to_email}') is not registered as an active Exhibitor for this specific exhibition.";
					}
				}

				if ($request_from_email !== '' && $request_to_email !== '' && strtolower($request_from_email) === strtolower($request_to_email)) {
					$row_errors[] = 'Sender and Receiver emails must be different.';
				}

				if ($customer_from_id && $customer_to_id && $booking_date !== '' && $booking_time !== '') {
					$formatted_time = date('H:i:s', strtotime($booking_time));
					
					// Conflict within spreadsheet
					$timeslot_key = $booking_date . '_' . $formatted_time;
					if (isset($sheet_bookings[$timeslot_key])) {
						foreach ($sheet_bookings[$timeslot_key] as $booked_exhibitor_id) {
							if ($booked_exhibitor_id === $customer_from_id || $booked_exhibitor_id === $customer_to_id) {
								$row_errors[] = 'Spreadsheet conflict: Either Sender or Receiver has another MoU entry at this same date and time in the uploaded file.';
								break;
							}
						}
					}

					// Database conflict (only check active, approved MoUs)
					if (empty($row_errors)) {
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

					// Track bookings for sheet checks
					if (empty($row_errors)) {
						if (!isset($sheet_bookings[$timeslot_key])) {
							$sheet_bookings[$timeslot_key] = array();
						}
						$sheet_bookings[$timeslot_key][] = $customer_from_id;
						$sheet_bookings[$timeslot_key][] = $customer_to_id;
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
					'mou_sign_location' => $mou_sign_location_original,
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

		// Re-fetch all event-specific validation targets for full server-side verification (tamper prevention)
		$event_exhibitors = $this->db->select('C.id, C.name, C.company, C.email')
			->from('es_exhibition_booking as B')
			->join('es_customers as C', 'B.customer_id = C.id')
			->where('B.exhibition_id', $exhibition_id)
			->where('B.is_canceled', 0)
			->where('C.is_deleted', 0)
			->where('C.is_active', 1)
			->get()
			->result();

		$exhibitor_map = array();
		foreach ($event_exhibitors as $ee) {
			$exhibitor_map[strtolower(trim($ee->email))] = $ee->id;
		}

		$event_dates_db = $this->db->select('date, open_time, closing_time')
			->where('exhibition_id', $exhibition_id)
			->order_by('date', 'ASC')
			->get('es_exhibition_date')
			->result();

		$valid_days_map = array();
		$valid_dates_map = array();
		$day_counter = 1;
		foreach ($event_dates_db as $ed) {
			$day_name = 'Day ' . $day_counter;
			$open = !empty($ed->open_time) ? $ed->open_time : '09:00:00';
			$close = !empty($ed->closing_time) ? $ed->closing_time : '18:00:00';
			
			$valid_days_map[$day_name] = $ed->date;
			$valid_dates_map[$ed->date] = array('day' => $day_name, 'open' => $open, 'close' => $close);
			$day_counter++;
		}

		$event_locations_db = $this->db->select('location')
			->where('exhibition_id', $exhibition_id)
			->where('type', 'mou_location')
			->get('location_for_meeting')
			->result();

		$valid_locations = array();
		$locations_map = array();
		foreach ($event_locations_db as $el) {
			$loc_key = strtolower(trim($el->location));
			$valid_locations[] = $loc_key;
			$locations_map[$loc_key] = $el->location;
		}

		$sheet_bookings = array();
		$validated_inserts = array();

		foreach ($import_rows as $row) {
			if (($row['status'] ?? '') !== 'valid') {
				continue;
			}

			$day = trim($row['exhibition_day'] ?? '');
			$date = trim($row['booking_date'] ?? '');
			$time = trim($row['booking_time'] ?? '');
			$from_email = trim($row['request_from_email'] ?? '');
			$to_email = trim($row['request_to_email'] ?? '');
			$location = trim($row['mou_sign_location'] ?? '');
			$location_original = isset($locations_map[strtolower($location)]) ? $locations_map[strtolower($location)] : $location;
			$value = trim($row['commercial_value'] ?? '');
			$desc = trim($row['description'] ?? '');

			// 1. Check basic non-empty values
			if (empty($day) || empty($date) || empty($time) || empty($from_email) || empty($to_email) || empty($location)) {
				echo json_encode(array('status' => 'error', 'message' => 'Validation failed: Missing required fields in some records.'));
				return;
			}

			// 2. Check different email address requirement
			if (strtolower($from_email) === strtolower($to_email)) {
				echo json_encode(array('status' => 'error', 'message' => 'Validation failed: Sender and Receiver emails must be different.'));
				return;
			}

			// 3. Check exhibition day index validity
			if (!isset($valid_days_map[$day])) {
				echo json_encode(array('status' => 'error', 'message' => "Validation failed: Exhibition Day '{$day}' is invalid for this event."));
				return;
			}

			// 4. Check exhibition date configuration validity
			if (!isset($valid_dates_map[$date])) {
				echo json_encode(array('status' => 'error', 'message' => "Validation failed: Date '{$date}' is not configured for this exhibition."));
				return;
			}

			// 5. Cross-validate that Day maps to that Date
			if ($valid_days_map[$day] !== $date) {
				echo json_encode(array('status' => 'error', 'message' => "Validation failed: Mismatch between Exhibition Day '{$day}' and Date '{$date}'."));
				return;
			}

			// 6. Check meeting location validity
			if (!in_array(strtolower($location), $valid_locations)) {
				echo json_encode(array('status' => 'error', 'message' => "Validation failed: Location '{$location}' is not registered for this exhibition."));
				return;
			}

			// 7. Check operational event hours
			$formatted_time = date('H:i:s', strtotime($time));
			$open_time = $valid_dates_map[$date]['open'];
			$close_time = $valid_dates_map[$date]['close'];
			if ($formatted_time < $open_time || $formatted_time > $close_time) {
				echo json_encode(array('status' => 'error', 'message' => "Validation failed: Sign time '{$time}' is outside event operating hours."));
				return;
			}

			// 8. Verify event registration mappings for emails
			$from_email_key = strtolower($from_email);
			$to_email_key = strtolower($to_email);

			if (!isset($exhibitor_map[$from_email_key])) {
				echo json_encode(array('status' => 'error', 'message' => "Validation failed: Sender '{$from_email}' is not a registered exhibitor for this exhibition."));
				return;
			}

			if (!isset($exhibitor_map[$to_email_key])) {
				echo json_encode(array('status' => 'error', 'message' => "Validation failed: Receiver '{$to_email}' is not a registered exhibitor for this exhibition."));
				return;
			}

			$from_id = $exhibitor_map[$from_email_key];
			$to_id = $exhibitor_map[$to_email_key];
			$timeslot_key = $date . '_' . $formatted_time;

			// 9. Re-check sheet conflicts
			if (isset($sheet_bookings[$timeslot_key])) {
				foreach ($sheet_bookings[$timeslot_key] as $booked_id) {
					if ($booked_id === $from_id || $booked_id === $to_id) {
						echo json_encode(array('status' => 'error', 'message' => 'Validation failed: Conflicting booking times within the sheet.'));
						return;
					}
				}
			}

			// 10. Re-check database conflicts
			$condition = "((user_type_from = 'exhibitor' AND request_from_id = {$from_id}) OR (user_type_to = 'exhibitor' AND request_to_id = {$to_id}))";
			$conflict_count = $this->db->where('exhibition_id', $exhibition_id)
				->where('is_approved', 1)
				->where('is_deleted', 0)
				->where('mou_sign_date', $date)
				->where('mou_sign_time', $formatted_time)
				->where($condition)
				->count_all_results('es_exhibition_mou_sign');

			if ($conflict_count > 0) {
				echo json_encode(array('status' => 'error', 'message' => 'Validation failed: Scheduling conflict exists for one of the exhibitors at the selected timeslots.'));
				return;
			}

			// 11. Validate Commercial Value format
			if (!preg_match('/^(\d+(?:\.\d+)?)\s*(USD|PKR)$/i', $value, $matches)) {
				echo json_encode(array('status' => 'error', 'message' => "Validation failed: Commercial Value '{$value}' is invalid. It must be a numeric amount followed by USD or PKR."));
				return;
			}
			$value_original = $matches[1] . ' ' . strtoupper($matches[2]);

			$sheet_bookings[$timeslot_key][] = $from_id;
			$sheet_bookings[$timeslot_key][] = $to_id;

			$validated_inserts[] = array(
				'exhibition_id' => $exhibition_id,
				'request_from_id' => $from_id,
				'request_to_id' => $to_id,
				'mou_sign_location' => $location_original,
				'exhibition_day' => $day,
				'mou_sign_date' => $date,
				'mou_sign_time' => $formatted_time,
				'user_type_from' => 'exhibitor',
				'user_type_to' => 'exhibitor',
				'description' => $desc,
				'commercial_value' => $value_original,
				'is_approved' => $auto_approve,
				'approved_by' => $auto_approve ? $this->userdata->id : null,
				'approved_on' => $auto_approve ? date('Y-m-d H:i:s') : null,
				'is_canceled' => 0,
				'is_deleted' => 0,
				'created_on' => date('Y-m-d H:i:s'),
			);
		}

		$this->db->trans_start();
		foreach ($validated_inserts as $data) {
			$this->db->insert('es_exhibition_mou_sign', $data);
		}
		$insert_count = count($validated_inserts);
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

		$dates = $this->db->select('date')
			->where('exhibition_id', $exhibition_id)
			->order_by('date', 'ASC')
			->get('es_exhibition_date')
			->result();

		$exhibitors = $this->db->select('C.id, C.name, C.company')
			->from('es_exhibition_booking as B')
			->join('es_customers as C', 'B.customer_id = C.id')
			->where('B.exhibition_id', $exhibition_id)
			->where('B.is_canceled', 0)
			->where('C.is_deleted', 0)
			->where('C.is_active', 1)
			->group_by('C.id')
			->get()
			->result();

		echo json_encode(array(
			'locations' => $locations,
			'dates' => array_map(function($d) { return $d->date; }, $dates),
			'exhibitors' => $exhibitors
		));
	}
}
