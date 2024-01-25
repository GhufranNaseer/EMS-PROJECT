<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form_17 extends MY_Controller {
	public $form_id = 17;

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
		$this->load->view('forms/form_17');
	}

	function form_17_validate() {
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


		$this->form_validation->set_rules('is_agree', 'is_agree*Agree', 'trim|required');

		$this->form_validation->set_rules('exhibitor[id]', 'exhibitor[id]*Name', 'trim|required');
		$this->form_validation->set_rules('exhibitor[name]', 'exhibitor[name]*Name', 'trim');
		$this->form_validation->set_rules('exhibitor[country]', 'exhibitor[country]*country', 'trim');
		$this->form_validation->set_rules('exhibitor[city]', 'exhibitor[city]*city', 'trim');
		$this->form_validation->set_rules('exhibitor[nationality]', 'exhibitor[nationality]*nationality', 'trim');
		$this->form_validation->set_rules('exhibitor[passport]', 'exhibitor[passport]*passport', 'trim');
		$this->form_validation->set_rules('exhibitor[phone_international]', 'exhibitor[phone_international]*phone_international', 'trim');
		$this->form_validation->set_rules('exhibitor[phone_local]', 'exhibitor[phone_local]*phone_local', 'trim');

		$this->form_validation->set_rules('vehicle[name]', 'vehicle[name]*vehicle', 'trim|required');
		$this->form_validation->set_rules('vehicle[qty]', 'vehicle[qty]*quantity', 'trim|required');
		$this->form_validation->set_rules('vehicle[from_date]', 'vehicle[from_date]*from_date', 'trim|required');
		$this->form_validation->set_rules('vehicle[to_date]', 'vehicle[to_date]*to_date', 'trim|required');
		$this->form_validation->set_rules('vehicle[total_days]', 'vehicle[total_days]*total_days', 'trim');
		$this->form_validation->set_rules('vehicle[price]', 'vehicle[price]*price', 'trim');
		$this->form_validation->set_rules('vehicle[total_price]', 'vehicle[total_price]*total_price', 'trim');

		$this->form_validation->set_rules('pickup[location]', 'pickup[location]*pickup location', 'trim|required');
		$this->form_validation->set_rules('pickup[date]', 'pickup[date]*pickup date', 'trim|required');
		$this->form_validation->set_rules('pickup[time]', 'pickup[time]*pickup time', 'trim|required');
		$this->form_validation->set_rules('dropoff[location]', 'dropoff[location]*dropoff location', 'trim|required');
		$this->form_validation->set_rules('dropoff[date]', 'dropoff[date]*dropoff date', 'trim|required');
		$this->form_validation->set_rules('dropoff[time]', 'dropoff[time]*dropoff time', 'trim|required');

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function form_17_submit() {
		if ($this->form_17_validate() !== true)
			show_404();


		$data = $this->input->post();
		$data['id'] = uniqid(time().rand(1, 1000));

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
			$new_data->booking[] = $data;

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
					'form_data' => json_encode(array('booking' => array($data))),
					'created_on' => date('Y-m-d H:i:s'),
				));
		}

		$this->session->set_flashdata('message', 'Form has been submitted successfully');
		redirect(base_url('forms/form_17'));
	}


	function delete_booking() {
		$id = $this->input->get('id');

		$old_form = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->get('es_exhibition_booking_forms_data')
			->row();

		$all_data = json_decode($old_form->form_data);
		$new_data = array();
		foreach ($all_data->booking as $data) {
			if ($data->id != $id) {
				$new_data[] = $data;
			}
		}
		$all_data->booking = $new_data;

		$this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->update('es_exhibition_booking_forms_data', array(
				'form_data' => json_encode($all_data),
				'modified_on' => date('Y-m-d H:i:s'),
			));

		$this->session->set_flashdata('message', 'Record has been successfully deleted!');
		redirect(base_url('forms/form_17'));
	}


	function get_badge_data() {
		$id = $this->input->post('id');

		$badges = $this->db
			->where('id', $id)
			->get('es_exhibition_badges')
			->row();

		if (isset($badges)) {
			echo json_encode(array(
				'error' => 0,
				'data' => $badges
			));
		} else {
			echo json_encode(array(
				'error' => 1,
				'message' => 'Badge not found!'
			));
		}
	}
}