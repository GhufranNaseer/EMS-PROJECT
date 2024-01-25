<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form_24 extends MY_Controller {
	public $form_id = 24;

	function index() {
		$this->formdata = null;
		$this->editdata = null;

		$check = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->get('es_exhibition_booking_forms_data')
			->row();

		if (isset($check)) {
			$this->formdata = json_decode($check->form_data);
		}

		$this->load->view('includes/after_login/head');
		$this->load->view('forms/form_24');
	}

	function form_24_validate() {
		$form_expire_date = $this->db->where('exhibition_id', $this->event->id)->where('form_id', $this->form_id)->get('es_exhibition_forms')->row()->expiry_date . ' 24:00:00';
		$check_extend_date = $this->db
			->where('exhibition_id', $this->event->id)
			->where('form_id', $this->form_id)
			->where('booking_id', $this->booking->id)
			->get('es_exhibition_forms_extended')
			->row();
		if (isset($check_extend_date) && !empty($check_extend_date)) {
			if ($check_extend_date->update_date && !is_null($check_extend_date->update_date) && strtotime($check_extend_date->update_date) >= strtotime($form_expire_date)) {
				$form_expire_date = $check_extend_date->update_date;
			}
			$form_expire_date = strtotime('+ '.(int)$check_extend_date->extended_hours.' hours', strtotime($form_expire_date));
		} else {
			$form_expire_date = strtotime($form_expire_date);
		}
		if (strtotime(date('Y-m-d H:i:s')) > $form_expire_date) {
			return $this->common->doError(func_num_args(), 'Form submission date is already expired!');
		}

		$this->form_validation->set_rules('flight', 'flight*flight', 'trim|required');
		$this->form_validation->set_rules('flight_check_in_date', 'flight_check_in_date*flight_check_in_date', 'trim|required');
		$this->form_validation->set_rules('flight_check_in_time', 'flight_check_in_time*flight_check_in_time', 'trim|required');
		$this->form_validation->set_rules('flight_check_out_date', 'flight_check_out_date*flight_check_out_date', 'trim|required');
		$this->form_validation->set_rules('flight_check_out_time', 'flight_check_out_time*flight_check_out_time', 'trim|required');

		$this->form_validation->set_rules('hotel', 'hotel*hotel', 'trim|required');
		$this->form_validation->set_rules('hotel_room', 'hotel_room*hotel_room', 'trim|required');
		$this->form_validation->set_rules('person_name', 'person_name*person_name', 'trim|required');
		$this->form_validation->set_rules('sharing_person_name', 'sharing_person_name*sharing_person_name', 'trim');
		$this->form_validation->set_rules('room_smoking', 'room_smoking*room_smoking', 'trim|required');
		$this->form_validation->set_rules('shuttle_service', 'shuttle_service*shuttle_service', 'trim|required');

		if ($this->input->post('shuttle_service') == 'yes') {
			$this->form_validation->set_rules('pickup_location', 'pickup_location*pickup_location', 'trim|required');
		}

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function form_24_submit() {
		if ($this->form_24_validate() !== true)
			show_404();


		$data = $this->input->post();
		$data['id'] = uniqid(time().rand(1, 1000));

		if ($data['shuttle_service'] == 'no') {
			$data['pickup_location'] = '-';
			$data['pickup_point'] = '-';
			$data['drop_point'] = '-';
			$data['pickup_rate'] = '-';
		}

		$check = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->count_all_results('es_exhibition_booking_forms_data');

		if ($check > 0) {
			$old_form = $this->db
				->where('exhibition_id', $this->event->id)
				->where('booking_id', $this->booking->id)
				->where('form_id', $this->form_id)
				->get('es_exhibition_booking_forms_data')
				->row();

			$new_data = json_decode($old_form->form_data);
			$new_data->reservation[] = $data;

			$this->db
				->where('exhibition_id', $this->event->id)
				->where('booking_id', $this->booking->id)
				->where('form_id', $this->form_id)
				->update('es_exhibition_booking_forms_data', array(
					'form_data' => json_encode($new_data),
					'modified_on' => date('Y-m-d H:i:s'),
				));
		} else {
			$this->db
				->where('exhibition_id', $this->event->id)
				->where('booking_id', $this->booking->id)
				->where('form_id', $this->form_id)
				->insert('es_exhibition_booking_forms_data', array(
					'exhibition_id' => $this->event->id,
					'booking_id' => $this->booking->id,
					'form_id' => $this->form_id,
					'form_data' => json_encode(array('reservation' => array($data))),
					'created_on' => date('Y-m-d H:i:s'),
				));
		}

		$this->session->set_flashdata('message', 'Form has been submitted successfully');
		redirect(base_url('forms/form_24'));
	}

	function delete_reservation() {
		$id = $this->input->get('id');

		$old_form = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->get('es_exhibition_booking_forms_data')
			->row();

		$all_data = json_decode($old_form->form_data);
		$new_data = array();
		foreach ($all_data->reservation as $data) {
			if ($data->id != $id) {
				$new_data[] = $data;
			}
		}
		$all_data->reservation = $new_data;

		$this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->update('es_exhibition_booking_forms_data', array(
				'form_data' => json_encode($all_data),
				'modified_on' => date('Y-m-d H:i:s'),
			));

		$this->session->set_flashdata('message', 'Record has been successfully deleted!');
		redirect(base_url('forms/form_24'));
	}
}