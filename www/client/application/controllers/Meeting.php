<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Meeting extends MY_Controller
{

	protected function checkEditId()
	{
		$id = $this->input->get('id');
		if (is_null($id))
			show_404();
		$id = $this->db->escape($id);
		$key = config_item('encryption_key');
		$key = $this->db->escape($key);
		$r = $this->db
			->where("MD5(CONCAT($key,`id`)) = $id")
			->get('es_exhibitions');
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();
	}


    function crd_exhibitors_list() {
        $this->load->view('includes/after_login/head');
        $this->load->view('meeting/exhibitors');
    }
    function crd_exhibitors_list_datatable()
    {

        $this->load->library('datatables');
        $this->datatables
            ->select('B.id,
            		B.exhibition_id,
            		B.customer_id,
                     C.company,
                     C.country,
                     C.city,
                     C.name', false)
            ->add_column('col_action', function ($row) {
                $id = $row['customer_id'];
                $html = '<a href="' . base_url() . 'meeting_schedule.html?type=exhibitor&id=' . urlencode(myid($id)) . '">Schedule Appointment</a>';
                return "<center>{$html}</center>";
            }, NULL)


            ->unset_column('B.exhibition_id')
            ->unset_column('B.customer_id')
            ->where('B.exhibition_id', $this->event->id)
            ->where('B.id !=', $this->booking->id)
            ->where('B.is_approved', 1)
            ->join('es_customers as C', 'B.customer_id = C.id')
            ->join('es_customer_contact_persons as A', 'B.contact_person_id = A.id')
            ->from('es_exhibition_booking as B');


        print ($this->datatables->generate());
    }

    function crd_officer_list() {
        $this->load->view('includes/after_login/head');
        $this->load->view('meeting/officer');
    }

    function crd_officer_list_datatable(){

        $this->load->library('datatables');
        $this->datatables
            ->select('id, 
				  officer_country, 
				  IF(is_representative=1, 
				  		CONCAT(officer_designation, " (Representative)"),
				  		officer_designation) as designation,
				  contact_person, 
				  officer_phone, 
				  ')
            ->add_column('col_action', function ($row) {
                $id = $row['id'];
                $html = '<a href="' . base_url() . 'meeting_schedule.html?type=officer&id=' . urlencode(myid($id)) . '">Schedule Appointment</a>';
                return "<center>{$html}</center>";
            }, NULL)
            ->from('es_officer')
            ->where('exhibition_id', $this->event->id)
            ->where('is_deleted', 0);

        if ($this->input->get('type') && $this->input->get('type') != '') {
			$this->datatables->where('officer_type', $this->input->get('type'));
		}

        print ($this->datatables->generate());
    }


	function meeting_form()
	{
		$this->load->view('includes/after_login/head');
		$this->load->view('meeting/meeting');
	}

	function meeting_form_validate() {
		$this->form_validation->set_rules('meeting_location', 'meeting_location*Meeting location', 'trim|required');
		$this->form_validation->set_rules('booking_day', 'booking_day*Day', 'trim|required');
		$this->form_validation->set_rules('agenda_of_meeting', 'agenda_of_meeting*Booking Agenda', 'trim|required');
		$this->form_validation->set_rules('booking_date', 'booking_date*Date', 'trim|required');
		$this->form_validation->set_rules('booking_time', 'booking_time*Time', 'trim|required');

		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		} else {

			$check_same_user = $this->db
				->where('exhibition_id', $this->event->id)
				->where('appointment_from', $this->userdata->id)
				->where('appointment_to', $this->input->post('booking_to'))
				->where('user_type_to', $this->input->post('user_type'))
				->where('appointment_date', $this->input->post('booking_date'))
				->where('appointment_time', $this->input->post('booking_time') . ':00')
				->where('is_deleted', 0)
				->where('is_canceled', 0)
				->count_all_results('es_exhibition_appointments');

			if ($check_same_user > 0) {
				return $this->common->doError(func_num_args(), 'You already made appointment schedule with this person for this time.');
			}

			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function meeting_form_submit() {
		if ($this->meeting_form_validate() !== true)
			show_404();

    	$data = array(
    		'exhibition_id' => $this->event->id,
    		'appointment_from' => $this->userdata->id,
    		'meeting_location' => $this->input->post('meeting_location'),
    		'agenda_of_meeting' => $this->input->post('agenda_of_meeting'),
    		'appointment_to' => $this->input->post('booking_to'),
    		'exhibition_day' => $this->input->post('booking_day'),
    		'appointment_date' => $this->input->post('booking_date'),
    		'appointment_time' => $this->input->post('booking_time') . ':00',
            'user_type_from' => 'exhibitor',
            'user_type_to' => $this->input->post('user_type'),
    		'created_on' => date('Y-m-d H:i:s'),
		);

    	$this->db
			->insert('es_exhibition_appointments', $data);



    	// send email
		$email_data = null;
		$title = $this->event->exhibition_title;
		$ReceiverName = '';
		$ReceiverEmail = '';
		$SenderName = $title;
		$SenderEmail = 'donotreply@exhibit.com.pk';
		$Subject = 'Appointment Schedule';
		$phone_number = '';
		$text_msg = '';
		$CcEmail = '';

		if ($this->input->post('user_type') == 'officer') {
			$email_data = $this->db
				->where('id', $this->input->post('booking_to'))
				->get('es_officer')
				->row();

			$ReceiverName = $email_data->officer_email;
			$ReceiverEmail = $email_data->officer_email;

			$phone_number = $email_data->officer_phone;
			$text_msg = $this->userdata->name . ' has requested a meeting for ' . date('M d', strtotime($this->input->post('booking_date'))) . ', ' . date('H:i', strtotime($this->input->post('booking_time')));

			$msg = 'Dear '.$email_data->contact_person.',<br><br>';
			$msg .= $text_msg . '. <br>';
			$msg .= $this->input->post('agenda_of_meeting');
			$msg .= '<br><br>Best,<br><br>';
			$msg .= '<b>'.$this->userdata->company.'</b><br><br>';
			$msg .= $this->userdata->phone . '<br>';
			$msg .= $this->userdata->url . '<br>';
			$msg .= $this->userdata->email;

		} else {
			$email_data = $this->db
				->where('id', $this->input->post('booking_to'))
				->get('es_customers')
				->row();

			$ReceiverName = $email_data->email;
			$ReceiverEmail = $email_data->email;

			$phone_number = $email_data->phone;
			$text_msg = $this->userdata->name . ' has requested a meeting for ' . date('M d', strtotime($this->input->post('booking_date'))) . ', ' . date('H:i', strtotime($this->input->post('booking_time')));

			$msg = 'Dear '.$email_data->name.',<br><br>';
			$msg .= $text_msg . '. <br>';
			$msg .= $this->input->post('agenda_of_meeting');
			$msg .= '<br><br>Best,<br><br>';
			$msg .= '<b>'.$this->userdata->company.'</b><br><br>';
			$msg .= $this->userdata->phone . '<br>';
			$msg .= $this->userdata->url . '<br>';
			$msg .= $this->userdata->email;
		}

		$Message = $this->load->view('email', array(
			'event_name' => $title,
			'message' => $msg
		), true);


		if($ReceiverEmail !=""){
			$this->load->helper('phpmailer');
			$mail = sendMail($ReceiverName,$ReceiverEmail,$Subject,$Message,$SenderName,$SenderEmail,$CcEmail);
		}
		//$this->funcs->send_email($ReceiverEmail, $Subject, $Message, $title);
		if ($phone_number != '') {
			$this->funcs->send_sms($phone_number, $text_msg);
		}

		$this->session->set_flashdata('message', 'Meeting schedule submitted successfully');
		redirect(base_url('dashboard'));
	}

	function get_available_time() {
    	$day = $this->input->post('day');
    	$date = $this->input->post('date');
		$open_time = strtotime("09:00");
		$close_time = strtotime("19:00");
		$html = '';
		$condition = "((user_type_from = 'exhibitor' AND appointment_from = ".$this->userdata->id.") OR (user_type_to = 'exhibitor' AND appointment_to = ".$this->userdata->id."))";
		for( $i=$open_time; $i<$close_time; $i+=1800) {

			$check = $this->db
				->where('exhibition_id', $this->event->id)
				->where('is_approved', 1)
				->where('appointment_date', $date)
				->where('appointment_time', date('H:i:s', $i))
				->where($condition)
				->count_all_results('es_exhibition_appointments');

			$other_user = 0;
			if ($this->input->post('type') && $this->input->post('id')) {
				if ($this->input->post('type') == 'officer') {
					$book_to_data = $this->db
						->where(mycolumn(), $this->input->post('id'))
						->get('es_officer')
						->row();
					$condition_other = "((user_type_from = 'officer' AND appointment_from = ".$book_to_data->id.") OR (user_type_to = 'officer' AND appointment_to = ".$book_to_data->id."))";
				} else {
					$book_to_data = $this->db
						->where(mycolumn(), $this->input->post('id'))
						->get('es_customers')
						->row();
					$condition_other = "((user_type_from = 'exhibitor' AND appointment_from = ".$book_to_data->id.") OR (user_type_to = 'exhibitor' AND appointment_to = ".$book_to_data->id."))";
				}
				$other_user = $this->db
					->where('exhibition_id', $this->event->id)
					->where('is_approved', 1)
					->where('appointment_date', $date)
					->where('appointment_time', date('H:i:s', $i))
					->where($condition_other)
					->count_all_results('es_exhibition_appointments');
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
}