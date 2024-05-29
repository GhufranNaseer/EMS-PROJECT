<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Meeting_report extends MY_Controller
{

    function crd_list()
    {
        $this->load->view('includes/after_login/head');
        $this->load->view('meeting_report/exhibition_list');
    }

    function crd_list_datatable()
    {
    	/*
    	CREATE VIEW `my_appointments_datatable` AS
		SELECT
		  `a`.`id`               AS `id`,
		  `a`.`exhibition_id`    AS `exhibition_id`,
		  `a`.`appointment_from` AS `appointment_from`,
		  `a`.`user_type_from`   AS `user_type_from`,
		  `a`.`appointment_to`   AS `appointment_to`,
		  `a`.`user_type_to`     AS `user_type_to`,
		  `a`.`exhibition_day`   AS `exhibition_day`,
		  `a`.`appointment_date` AS `appointment_date`,
		  `a`.`appointment_time` AS `appointment_time`,
		  `a`.`is_approved`      AS `is_approved`,
		  `a`.`approved_on`      AS `approved_on`,
		  `a`.`is_conducted`      AS `is_conducted`,
		  `a`.`appointment_feedback`      AS `appointment_feedback`,
		  `a`.`is_canceled`      AS `is_canceled`,
		  `a`.`canceled_on`      AS `canceled_on`,
		  `a`.`created_on`       AS `created_on`,
		  `a`.`is_deleted`       AS `is_deleted`,
		  `a`.`deleted_on`       AS `deleted_on`,
		  IF((`a`.`user_type_from` = 'exhibitor'),
			(SELECT `es_customers`.`company` FROM `es_customers` WHERE (`es_customers`.`id` = `a`.`appointment_from`)),
			(SELECT CONCAT(`es_officer`.`officer_designation`,' (',
				IF(es_officer.officer_type="foreign_delegates", 'Foreign Delegate',
				IF(es_officer.officer_type="local_delegates", 'Local Delegate',
				IF(es_officer.officer_type="chief_of_servicing", 'Gov. Services Chief', "-"))),
				')')
			FROM `es_officer` WHERE (`es_officer`.`id` = `a`.`appointment_from`))) AS `appointment_from_company_name`,
		  IF((`a`.`user_type_to` = 'exhibitor'),
			(SELECT `es_customers`.`company` FROM `es_customers` WHERE (`es_customers`.`id` = `a`.`appointment_to`)),
			(SELECT CONCAT(`es_officer`.`officer_designation`,' (',
				IF(es_officer.officer_type="foreign_delegates", 'Foreign Delegate',
				IF(es_officer.officer_type="local_delegates", 'Local Delegate',
				IF(es_officer.officer_type="chief_of_servicing", 'Gov. Services Chief', "-"))),
				')')
			FROM `es_officer` WHERE (`es_officer`.`id` = `a`.`appointment_to`))) AS `appointment_to_company_name`
		FROM `es_exhibition_appointments` `a`
    	 */

    	$condition = "((user_type_from = 'exhibitor' AND appointment_from = ".$this->userdata->id.") OR (user_type_to = 'exhibitor' AND appointment_to = ".$this->userdata->id."))";

		if ($this->input->get('filter_status') && $this->input->get('filter_status') == 'my_requests') {
			$condition = "(user_type_from = 'exhibitor' AND appointment_from = ".$this->userdata->id.")";
		}

    	$this->load->library('datatables');
        $this->datatables
            ->select('id,
            		exhibition_id,
                    appointment_from_company_name,
                    appointment_to_company_name,
                    exhibition_day,
                    appointment_date,
                    appointment_time,
                    IF(is_canceled = 1, "<span class=\'label label-danger\'>Regretted</span>",
                    	IF(is_approved = 1, "<span class=\'label label-success\'>Accepted</span>", "<span class=\'label label-warning\'>Pending</span>")) as status,
					is_conducted,
					is_canceled,
                    is_approved,
                    user_type_to,
                    appointment_to,
   				  ', false)

			->unset_column('exhibition_id')
			->unset_column('is_conducted')
			->unset_column('is_canceled')
			->unset_column('is_approved')
			->unset_column('user_type_to')
			->unset_column('appointment_to')

			->add_column('col_conducted', function ($row) {
				$id = $row['id'];
				$is_conducted = $row['is_conducted'];
				
				if ($row['is_approved'] == 1) {
					if ($row['is_conducted'] == 1) {
						return '<div class="text-center"><input type="checkbox" checked disabled/></div>';
					} else {
						return '<div class="text-center"><input type="checkbox" onchange="confirm_conducted(\''.myid($id).'\')" /></div>';
					}
				} else {
					return '<div class="text-center">-</div>';
				}
			}, NULL)
			
            ->add_column('col_action', function ($row) {
                $id = $row['id'];
                $html = '';
                if ($row['is_canceled'] == 0) {
					if ($row['user_type_to'] == 'exhibitor' && $row['appointment_to'] == $this->userdata->id) {
						if ($row['is_approved'] == 0) {
							$html .= '<a href="' . base_url() . 'meeting-approved.html?id=' . urlencode(myid($id)) . '" class="btn btn-xs btn-success" onclick="return confirm(\'Are you sure you would like to accept the meeting request\')">Accept</a> ';
						}
						$html .= '<a href="' . base_url() . 'meeting-cancel.html?id=' . urlencode(myid($id)) . '" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you would like to deny the meeting request?\')">Regret/Deny</a> ';
						$html .= '<a href="' . base_url() . 'meeting_re_schedule.html?id=' . urlencode(myid($id)) . '" class="btn btn-xs btn-info">Re-Schedule</a> ';
					} else {
						$html = '<a href="' . base_url() . 'meeting-delete.html?id=' . urlencode(myid($id)) . '" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you would like to delete the meeting request?\')">Delete</a> ';
					}
				}

                return "<div class='text-right'>{$html}</div>";
            }, NULL)

            ->where('exhibition_id', $this->event->id)
            ->where('is_deleted', 0)
            ->where($condition)
            ->from('my_appointments_datatable');

        if ($this->input->get('filter_status') && $this->input->get('filter_status') != '') {
        	if ($this->input->get('filter_status') == 'approved') {
				$this->datatables->where('is_approved', 1);
			} else if ($this->input->get('filter_status') == 'pending') {
				$this->datatables->where('is_approved', 0);
			} else if ($this->input->get('filter_status') == 'my_requests') {
				$this->datatables->where('is_approved', 0);
			}
		}
		if ($this->input->get('filter_day') && $this->input->get('filter_day') != '') {
			$this->datatables->where('exhibition_day', $this->input->get('filter_day'));
		}

        print ($this->datatables->generate());
    }


    function cancel() {

    	$data = $this->db
			->where(mycolumn(), $this->input->get('id'))
			->get('es_exhibition_appointments')
			->row();

        $this->db
            ->where(mycolumn(), $this->input->get('id'))
            ->update('es_exhibition_appointments', array(
                'is_approved' => 0,
                'approved_on' => null,
				'is_conducted' => 0,
				'appointment_feedback' => null,
                'is_canceled' => 1,
                'canceled_on' => date('Y-m-d H:i:s')
            ));

		// send email
		$email_data = null;
		$title = $this->event->exhibition_title;
		$ReceiverEmail = '';
		$phone_number = '';
		$text_msg = '';

		$get_email_template = $this->db
			->where("title" , "APPOINTMENT_CANCELED")
			->get('email_template')
			->row();

		// {NAME},{EMAIL},{PHONE},{COMPANY},{SENDER_NAME},{SENDER_EMAIL},{SENDER_PHONE},{SENDER_COMPANY},{SENDER_WEBSITE},{APPOINTMENT_TIME},{APPOINTMENT_AGENDA}
		$Subject = $get_email_template->subject;
		$message = $get_email_template->message;

		if ($data->user_type_from == 'officer') {
			$email_data = $this->db
				->where('id', $data->appointment_from)
				->get('es_officer')
				->row();

			$ReceiverEmail = $email_data->officer_email;

			$phone_number = $email_data->officer_phone;
			$text_msg = 'Your meeting with ' . $this->userdata->name . ' of '.$this->userdata->company.' for ' . date('M d', strtotime($data->appointment_date)) . ' at ' . date('H:i', strtotime($data->appointment_time)) . ' has been declined.';


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
				->where('id', $data->appointment_from)
				->get('es_customers')
				->row();

			$ReceiverEmail = $email_data->email;

			$phone_number = $email_data->phone;
			$text_msg = 'Your meeting with ' . $this->userdata->name . ' of '.$this->userdata->company.' for ' . date('M d', strtotime($data->appointment_date)) . ' at ' . date('H:i', strtotime($data->appointment_time)) . ' has been declined.';


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
		$Subject = str_replace('{APPOINTMENT_TIME}', date('M d', strtotime($data->appointment_date)) . ' at ' . date('H:i', strtotime($data->appointment_time)), $Subject);
		$Subject = str_replace('{APPOINTMENT_AGENDA}', $meeting->agenda_of_meeting, $Subject);

		$message = str_replace('{EVENT_NAME}', $title, $message);
		$message = str_replace('{SENDER_NAME}', $this->userdata->name, $message);
		$message = str_replace('{SENDER_EMAIL}', $this->userdata->email, $message);
		$message = str_replace('{SENDER_PHONE}', $this->userdata->phone, $message);
		$message = str_replace('{SENDER_COMPANY}', $this->userdata->company, $message);
		$message = str_replace('{SENDER_WEBSITE}', $this->userdata->url, $message);
		$message = str_replace('{APPOINTMENT_TIME}', date('M d', strtotime($data->appointment_date)) . ' at ' . date('H:i', strtotime($data->appointment_time)), $message);
		$message = str_replace('{APPOINTMENT_AGENDA}', $data->agenda_of_meeting, $message);

		if($ReceiverEmail !=""){
			$this->db->insert('es_emails_cron', array(
				'type' => 'APPOINTMENT_CANCELED',
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

        $this->session->set_flashdata('message', 'Meeting has been cancel successfully');
		redirect(base_url('my_meeting.html'));
    }

    function approved() {
		$data = $this->db
			->where(mycolumn(), $this->input->get('id'))
			->get('es_exhibition_appointments')
			->row();

		// check my schedules
		$condition = "((user_type_from = 'exhibitor' AND appointment_from = ".$this->userdata->id.") OR (user_type_to = 'exhibitor' AND appointment_to = ".$this->userdata->id."))";
		$check_my = $this->db
			->where('id !=', $data->id)
			->where('exhibition_id', $this->event->id)
			->where('is_approved', 1)
			->where('appointment_date', $data->appointment_date)
			->where('appointment_time', $data->appointment_time)
			->where($condition)
			->count_all_results('es_exhibition_appointments');
		if ($check_my > 0) {
			$this->session->set_flashdata('error', 'You already have another meeting schedule at this time!');
			redirect(base_url('my_meeting.html'));
			die();
		}

		$this->db
			->where(mycolumn(), $this->input->get('id'))
			->update('es_exhibition_appointments', array(
				'is_approved' => 1,
				'approved_on' => date('Y-m-d H:i:s'),
				'is_conducted' => 0,
				'appointment_feedback' => null,
				'is_canceled' => 0,
				'canceled_on' => null
			));

		// send email
		$email_data = null;
		$title = $this->event->exhibition_title;
		$ReceiverEmail = '';
		$phone_number = '';
		$text_msg = '';

		$get_email_template = $this->db
		->where("title" , "APPOINTMENT_ACCEPTED")
		->get('email_template')
		->row();

		// {NAME},{EMAIL},{PHONE},{COMPANY},{SENDER_NAME},{SENDER_EMAIL},{SENDER_PHONE},{SENDER_COMPANY},{SENDER_WEBSITE},{APPOINTMENT_TIME},{APPOINTMENT_AGENDA}
		$Subject = $get_email_template->subject;
		$message = $get_email_template->message;

		if ($data->user_type_from == 'officer') {
			$email_data = $this->db
				->where('id', $data->appointment_from)
				->get('es_officer')
				->row();

			$ReceiverEmail = $email_data->officer_email;

			$phone_number = $email_data->officer_phone;
			$text_msg = 'Your request for ' . $this->userdata->name . ' of '.$this->userdata->company.' for ' . date('M d', strtotime($data->appointment_date)) . ' at ' . date('H:i', strtotime($data->appointment_time)) . ' has been accepted. Please login for further details.';

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
				->where('id', $data->appointment_from)
				->get('es_customers')
				->row();

			$ReceiverEmail = $email_data->email;

			$phone_number = $email_data->phone;
			$text_msg = 'Your request for ' . $this->userdata->name . ' of '.$this->userdata->company.' for ' . date('M d', strtotime($data->appointment_date)) . ' at ' . date('H:i', strtotime($data->appointment_time)) . ' has been accepted. Please login for further details.';

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
		$Subject = str_replace('{APPOINTMENT_TIME}', date('M d', strtotime($data->appointment_date)) . ' at ' . date('H:i', strtotime($data->appointment_time)), $Subject);
		$Subject = str_replace('{APPOINTMENT_AGENDA}', $data->agenda_of_meeting, $Subject);

		$message = str_replace('{EVENT_NAME}', $title, $message);
		$message = str_replace('{SENDER_NAME}', $this->userdata->name, $message);
		$message = str_replace('{SENDER_EMAIL}', $this->userdata->email, $message);
		$message = str_replace('{SENDER_PHONE}', $this->userdata->phone, $message);
		$message = str_replace('{SENDER_COMPANY}', $this->userdata->company, $message);
		$message = str_replace('{SENDER_WEBSITE}', $this->userdata->url, $message);
		$message = str_replace('{APPOINTMENT_TIME}', date('M d', strtotime($data->appointment_date)) . ' at ' . date('H:i', strtotime($data->appointment_time)), $message);
		$message = str_replace('{APPOINTMENT_AGENDA}', $data->agenda_of_meeting, $message);

		if($ReceiverEmail !=""){
			$this->db->insert('es_emails_cron', array(
				'type' => 'APPOINTMENT_ACCEPTED',
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

		$this->session->set_flashdata('message', 'Meeting has been approved successfully');
		redirect(base_url('my_meeting.html'));
    }

    function schedule() {
		$this->load->view('includes/after_login/head');
		$this->load->view('meeting_report/meeting-reschedule');
    }

	function meeting_re_schedule_validate() {
        $this->form_validation->set_rules('meeting_location', 'meeting_location*Meeting location', 'trim|required');
		$this->form_validation->set_rules('booking_day', 'booking_day*Day', 'trim|required');
		$this->form_validation->set_rules('booking_date', 'booking_date*Date', 'trim|required');
		$this->form_validation->set_rules('booking_time', 'booking_time*Time', 'trim|required');

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function meeting_re_schedule_submit() {
		if ($this->meeting_re_schedule_validate() !== true)
			show_404();

		$meeting = $this->db
			->where(mycolumn(), $this->input->get('id'))
			->get('es_exhibition_appointments')
			->row();

		//print_r($meeting);die;

		$data = array(
			'exhibition_id' => $this->event->id,
			'appointment_from' => $this->userdata->id,
            'meeting_location' => $this->input->post('meeting_location'),
			'appointment_to' => $meeting->appointment_from,
			'agenda_of_meeting' => $meeting->agenda_of_meeting,
			'exhibition_day' => $this->input->post('booking_day'),
			'appointment_date' => $this->input->post('booking_date'),
			'appointment_time' => $this->input->post('booking_time') . ':00',
			'user_type_from' => 'exhibitor',
			'user_type_to' => $meeting->user_type_from,
			'created_on' => date('Y-m-d H:i:s'),
		);

		$this->db
			->insert('es_exhibition_appointments', $data);

		$this->db
			->where('id', $meeting->id)
			->update('es_exhibition_appointments', array(
				'is_conducted' => 0,
				'appointment_feedback' => null,
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
			->where("title" , "APPOINTMENT_RE_SCHEDULE")
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
			$text_msg = $this->userdata->name . ' has re-schedule a meeting for ' . date('M d', strtotime($this->input->post('booking_date'))) . ', ' . date('H:i', strtotime($this->input->post('booking_time')));


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
			$text_msg = $this->userdata->name . ' has re-schedule a meeting for ' . date('M d', strtotime($this->input->post('booking_date'))) . ', ' . date('H:i', strtotime($this->input->post('booking_time')));

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
				'type' => 'APPOINTMENT_RE_SCHEDULE',
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


		$this->session->set_flashdata('message', 'Meeting re-schedule request submitted successfully');
		redirect(base_url('my_meeting.html'));
	}

    function delete_meeting() {
		$this->db
			->where(mycolumn(), $this->input->get('id'))
			->update('es_exhibition_appointments', array(
				'is_approved' => 0,
				'approved_on' => null,
				'is_conducted' => 0,
				'appointment_feedback' => null,
				'is_canceled' => 0,
				'canceled_on' => null,
				'is_deleted' => 1,
				'deleted_on' => date('Y-m-d H:i:s'),
			));

		$this->session->set_flashdata('message', 'Meeting has been deleted successfully');
		redirect(base_url('my_meeting.html'));
	}

	function meeting_conducted_ajax() {

		$id = $this->input->post('id');
		$feedback = $this->input->post('feedback');

		$this->db
			->where(mycolumn(), $id)
			->update('es_exhibition_appointments', array(
				'is_conducted' => 1,
				'appointment_feedback' => $feedback,
			));

		echo json_encode(array(
			'error' => 0,
			'message' => 'Meeting conducted successfully!'
		));
		die;
	}
}