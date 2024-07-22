<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mou_report extends MY_Controller {
	protected function rule() {
        $this->activateRightsSystem();
        $crd = array(
            'crd_list,
			cancel,
			get_available_time,
			schedule,
			mou_re_schedule_validate,
			mou_re_schedule_submit,
			approved' => array(
                'rule' => '@'
            ),
            'crd_list_datatable' => array(
                'rule' => '@',
            )
        );
        $this->load->model('usermdl');
        $this->myparent = base_url('mou_sign.html');
        return array_merge($crd);
    }

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('mou_report/exhibition_list');
	}


	function crd_list_datatable() {

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
                    B.request_to_id,
   				  ', false)

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

		

        print ($this->datatables->generate());
	}

	function schedule() {
		$this->load->view('includes/after_login/head');
		$this->load->view('mou_report/mou-reschedule');
	}

	function get_available_time() {
		$mou_sign = $this->db
		->where('id', $this->input->post('mou_id'))
		->get('es_exhibition_mou_sign')
		->row();
    	$day = $this->input->post('day');
    	$date = $this->input->post('date');
		$open_time = strtotime("09:00");
		$close_time = strtotime("19:00");
		$html = '';
		$condition = "((user_type_from = 'exhibitor' AND request_from_id = ".$mou_sign->request_from_id.") OR (user_type_to = 'exhibitor' AND request_to_id = ".$mou_sign->request_to_id."))";
		for( $i=$open_time; $i<$close_time; $i+=1800) {

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
					$condition_other = "((user_type_from = 'officer' AND request_from_id = ".$book_to_data->id.") OR (user_type_to = 'officer' AND request_to_id = ".$book_to_data->id."))";
				} else {
					$book_to_data = $this->db
						->where(mycolumn(), $this->input->post('id'))
						->get('es_customers')
						->row();
					$condition_other = "((user_type_from = 'exhibitor' AND request_from_id = ".$book_to_data->id.") OR (user_type_to = 'exhibitor' AND request_to_id = ".$book_to_data->id."))";
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
				<div class="small-box '.$is_booked.'" data-time="'. date('H:i', $i) .'">
					<div class="inner">
						<h3 style="font-size: 20px; margin: 0">'.date("H:i",$i).'</h3>
					</div>'.$is_booked_status.'
				</div>
			</div>';
		}

		echo $html;
		die();
	}

	function mou_re_schedule_validate() {
        $this->form_validation->set_rules('mou_sign_location', 'mou_sign_location*MOU location', 'trim|required');
		$this->form_validation->set_rules('booking_day', 'booking_day*Day', 'trim|required');
		$this->form_validation->set_rules('booking_date', 'booking_date*Date', 'trim|required');
		$this->form_validation->set_rules('booking_time', 'booking_time*Time', 'trim|required');

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}


	function mou_re_schedule_submit() {
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
			'commercial_value' => $meeting->commercial_value.' '.$meeting->currency,
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



		// send email
		$email_data = null;
		$title = $this->event->exhibition_title;
		$ReceiverEmail = '';
		$phone_number = '';
		$text_msg = '';

		$get_email_template = $this->db
			->where("title" , "MOU_SIGNING_APPOINTMENT_RE_SCHEDULE")
			->where("exhibition_id" , $meeting->exhibition_id)
			->get('email_template')
			->row();

		// {NAME},{EMAIL},{PHONE},{COMPANY},{SENDER_NAME},{SENDER_EMAIL},{SENDER_PHONE},{SENDER_COMPANY},{SENDER_WEBSITE},{APPOINTMENT_TIME},{APPOINTMENT_AGENDA}
		$Subject = $get_email_template->subject;
		$message = $get_email_template->message;

		if ($this->input->post('user_type') == 'officer') {
			$email_data = $this->db
				->where('id', $this->input->post('booking_to'))
				->get('es_officer')
				->row();

			$ReceiverEmail = $email_data->officer_email;

			$phone_number = $email_data->officer_phone;
			$text_msg = $this->userdata->name . ' has re-schedule a MoU Signing for ' . date('M d', strtotime($this->input->post('booking_date'))) . ', ' . date('H:i', strtotime($this->input->post('booking_time')));


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
			$text_msg = $this->userdata->name . ' has re-schedule a MoU signing for ' . date('M d', strtotime($this->input->post('booking_date'))) . ', ' . date('H:i', strtotime($this->input->post('booking_time')));

			$Subject = str_replace('{NAME}', $email_data->name, $Subject);
			$Subject = str_replace('{EMAIL}', $email_data->email, $Subject);
			$Subject = str_replace('{PHONE}', $email_data->phone, $Subject);
			$Subject = str_replace('{COMPANY}', $email_data->company, $Subject);

			$message = str_replace('{NAME}', $email_data->name, $message);
			$message = str_replace('{EMAIL}', $email_data->email, $message);
			$message = str_replace('{PHONE}', $email_data->phone, $message);
			$message = str_replace('{COMPANY}', $email_data->company, $message);
		}

		$Subject = str_replace('{EVENT_NAME}', $title, $Subject);
		$Subject = str_replace('{SENDER_NAME}', $this->userdata->name, $Subject);
		$Subject = str_replace('{SENDER_EMAIL}', $this->userdata->email, $Subject);
		$Subject = str_replace('{SENDER_PHONE}', $this->userdata->phone, $Subject);
		$Subject = str_replace('{SENDER_COMPANY}', $this->userdata->company, $Subject);
		$Subject = str_replace('{SENDER_WEBSITE}', $this->userdata->url, $Subject);
		$Subject = str_replace('{APPOINTMENT_TIME}', date('M d', strtotime($this->input->post('booking_date'))) . ', ' . date('H:i', strtotime($this->input->post('booking_time'))), $Subject);
		$Subject = str_replace('{APPOINTMENT_AGENDA}', $meeting->agenda_of_meeting, $Subject);

		$message = str_replace('{EVENT_NAME}', $title, $message);
		$message = str_replace('{SENDER_NAME}', $this->userdata->name, $message);
		$message = str_replace('{SENDER_EMAIL}', $this->userdata->email, $message);
		$message = str_replace('{SENDER_PHONE}', $this->userdata->phone, $message);
		$message = str_replace('{SENDER_COMPANY}', $this->userdata->company, $message);
		$message = str_replace('{SENDER_WEBSITE}', $this->userdata->url, $message);
		$message = str_replace('{APPOINTMENT_TIME}', date('M d', strtotime($this->input->post('booking_date'))) . ', ' . date('H:i', strtotime($this->input->post('booking_time'))), $message);
		$message = str_replace('{APPOINTMENT_AGENDA}', $meeting->agenda_of_meeting, $message);

		if($ReceiverEmail !=""){
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



	function cancel() {

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
			->where("title" , "MOU_SIGNING_CANCELED")
			->where("exhibition_id" , $data->exhibition_id)
			->get('email_template')
			->row();

		// {NAME},{EMAIL},{PHONE},{COMPANY},{SENDER_NAME},{SENDER_EMAIL},{SENDER_PHONE},{SENDER_COMPANY},{SENDER_WEBSITE},{APPOINTMENT_TIME},{APPOINTMENT_AGENDA}
		$Subject = $get_email_template->subject;
		$message = $get_email_template->message;

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

			$text_msg = 'Your MoU signing with ' . $email_customer_data->name . ' of '.$email_customer_data->company.' for ' . date('M d', strtotime($data->mou_sign_date)) . ' at ' . date('H:i', strtotime($data->mou_sign_time)) . ' has been declined.';


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
			$text_msg = 'Your MoU singing with ' . $email_data->name . ' of '.$email_data->company.' for ' . date('M d', strtotime($data->mou_sign_date)) . ' at ' . date('H:i', strtotime($data->mou_sign_time)) . ' has been declined.';


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

		if($ReceiverEmail !=""){
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

    function approved() {
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
		->where("title" , "MOU_SIGNING_ACCEPTED")
		->where("exhibition_id" , $data->exhibition_id)
		->get('email_template')
		->row();

		// {NAME},{EMAIL},{PHONE},{COMPANY},{SENDER_NAME},{SENDER_EMAIL},{SENDER_PHONE},{SENDER_COMPANY},{SENDER_WEBSITE},{SCHEDULE_TIME},{DESCRIPTION},{COMMERCIAL_VALUE}
		$Subject = $get_email_template->subject;
		$message = $get_email_template->message;

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
			$text_msg = 'Your request for MOU Signing' . $email_customer_data->name . ' of '.$email_customer_data->company.' for ' . date('M d', strtotime($data->mou_sign_date)) . ' at ' . date('H:i', strtotime($data->mou_sign_time)) . ' has been accepted. Please login for further details.';

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
			$text_msg = 'Your request for MOU Signing ' . $email_data->name . ' of '.$email_data->company.' for ' . date('M d', strtotime($data->mou_sign_date)) . ' at ' . date('H:i', strtotime($data->mou_sign_time)) . ' has been accepted. Please login for further details.';

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

		if($ReceiverEmail !=""){
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

}