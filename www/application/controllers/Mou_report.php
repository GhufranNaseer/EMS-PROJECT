<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mou_report extends MY_Controller {
	
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
                    C.company as request_to_comapny,
					E.exhibition_title as exhibition_title,
                    B.exhibition_day,
                    B.mou_sign_date,
                    B.mou_sign_time,
                    IF(B.is_canceled = 1, "<span class=\'label label-danger\'>Regretted</span>",
                    	IF(B.is_approved = 1, "<span class=\'label label-success\'>Accepted</span>", "<span class=\'label label-warning\'>Pending</span>")) as status,
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
                $html .= '<a href="' . base_url() . 'mou_sign-cancel.html?id=' . urlencode(myid($id)) . '" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you would like to cancel the MoU request?\')">Cancel</a> ';
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

	function cancel() {

    	$data = $this->db
			->where(mycolumn(), $this->input->get('id'))
			->get('es_exhibition_mou_sign')
			->row();

        $this->db
            ->where(mycolumn(), $this->input->get('id'))
            ->update('es_exhibition_mou_sign', array(
                'is_approved' => 0,
                'approved_on' => null,
                'is_canceled' => 1,
                'canceled_on' => date('Y-m-d H:i:s')
            ));

		$this->session->set_flashdata('message', 'MoU has been cancel successfully');
		redirect(base_url('mou_sign.html'));
		die;
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
			->get('es_exhibition_mou_sign')
			->row();

		$this->db
			->where(mycolumn(), $this->input->get('id'))
			->update('es_exhibition_mou_sign', array(
				'is_approved' => 1,
				'approved_on' => date('Y-m-d H:i:s'),
				'approved_by' => $this->userdata->id,
				'is_canceled' => 0,
				'canceled_on' => null
			));

		$this->session->set_flashdata('message', 'Mou has been approved successfully');
		redirect(base_url('mou_sign.html'));
		die;
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

}