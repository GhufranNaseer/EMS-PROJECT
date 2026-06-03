<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Networking extends MY_Controller
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
        $this->load->view('networking/exhibitors');
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
                $html = '<a href="' . base_url() . 'mou_schedule.html?type=exhibitor&id=' . urlencode(myid($id)) . '">Schedule MoU Sign</a>';
                return "<center>{$html}</center>";
            }, NULL)


            ->unset_column('B.exhibition_id')
            ->unset_column('B.customer_id')
            ->where('B.exhibition_id', $this->event->id)
            ->where('B.id !=', $this->booking->id)
            ->where('B.is_approved', 1)
			->where('B.is_canceled', 0)
            ->join('es_customers as C', 'B.customer_id = C.id')
            ->join('es_customer_contact_persons as A', 'B.contact_person_id = A.id')
            ->from('es_exhibition_booking as B');


        print ($this->datatables->generate());
    }


	function mou_form()
	{
		$this->load->view('includes/after_login/head');
		$this->load->view('networking/meeting');
	}

	function mou_form_validate() {
		$this->form_validation->set_rules('meeting_location', 'meeting_location*Sign location', 'trim|required');
		$this->form_validation->set_rules('booking_day', 'booking_day*Day', 'trim|required');
		//$this->form_validation->set_rules('agenda_of_meeting', 'agenda_of_meeting*Booking Agenda', 'trim|required');
		$this->form_validation->set_rules('booking_date', 'booking_date*Date', 'trim|required');
		$this->form_validation->set_rules('booking_time', 'booking_time*Time', 'trim|required');
		$this->form_validation->set_rules('commercial_value', 'commercial_value*Commercial Value', 'trim|required');
		$this->form_validation->set_rules('description', 'description*Description', 'trim|required');

		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		} else {

			$check_same_user = $this->db
				->where('exhibition_id', $this->event->id)
				->where('request_from_id', $this->userdata->id)
				->where('request_to_id', $this->input->post('booking_to'))
				->where('user_type_to', $this->input->post('user_type'))
				->where('mou_sign_date', $this->input->post('booking_date'))
				->where('mou_sign_time', $this->input->post('booking_time') . ':00')
				->where('is_deleted', 0)
				->count_all_results('es_exhibition_mou_sign');

			if ($check_same_user > 0) {
				return $this->common->doError(func_num_args(), 'You already made schedule with this person for this time.');
			}

			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function mou_form_submit() {
		if ($this->mou_form_validate() !== true)
			show_404();

    	$data = array(
    		'exhibition_id' => $this->event->id,
    		'request_from_id' => $this->userdata->id,
    		'mou_sign_location' => $this->input->post('meeting_location'),
    		'request_to_id' => $this->input->post('booking_to'),
    		'exhibition_day' => $this->input->post('booking_day'),
    		'mou_sign_date' => $this->input->post('booking_date'),
    		'mou_sign_time' => $this->input->post('booking_time') . ':00',
            'user_type_from' => 'exhibitor',
            'user_type_to' => $this->input->post('user_type'),
			'description' =>$this->input->post('description'),
			'commercial_value' =>$this->input->post('commercial_value').' '.$this->input->post('currency'),
    		'created_on' => date('Y-m-d H:i:s'),
		);

    	$this->db
			->insert('es_exhibition_mou_sign', $data);

		$this->session->set_flashdata('message', 'MoU request forwarded successfully');
		redirect(base_url('dashboard'));
		die;
	}

	function get_available_time() {
    	$day = $this->input->post('day');
    	$date = $this->input->post('date');
		$open_time = strtotime("09:00");
		$close_time = strtotime("19:00");
		$html = '';
		$condition = "((user_type_from = 'exhibitor' AND request_from_id = ".$this->userdata->id.") OR (user_type_to = 'exhibitor' AND request_to_id = ".$this->userdata->id."))";
		for( $i=$open_time; $i<$close_time; $i+=1800) {

			$check = $this->db
				->where('exhibition_id', $this->event->id)
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
					->where('exhibition_id', $this->event->id)
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
}