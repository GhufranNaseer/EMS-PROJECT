<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form_03 extends MY_Controller {
	public $form_id = 3;

	function index() {
		$this->formdata = null;

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
		$this->load->view('forms/form_03');
	}

	function form_03_exhibit_validate() {
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
		

		if ($this->input->post('exhibit[telephone]')) {
			$_POST['exhibit[telephone]'] = str_replace(array('+','-','_'), '', $this->input->post('exhibit[telephone]'));
		}
		if ($this->input->post('exhibit[contact_person][mobile]')) {
			$_POST['exhibit[contact_person][mobile]'] = str_replace(array('+','-','_'), '', $this->input->post('exhibit[contact_person][mobile]'));
		}
		if ($this->input->post('exhibit[contact_person][phone]')) {
			$_POST['exhibit[contact_person][phone]'] = str_replace(array('+','-','_'), '', $this->input->post('exhibit[contact_person][phone]'));
		}

		$this->form_validation->set_rules('exhibit[country]', 'exhibit[country]*country', 'trim|required');
		$this->form_validation->set_rules('exhibit[postal]', 'exhibit[postal]*postal/zip', 'trim|numeric');
		$this->form_validation->set_rules('exhibit[telephone]', 'exhibit[telephone]*telephone', 'trim|required|min_length[12]');
		$this->form_validation->set_rules('exhibit[fax]', 'exhibit[fax]*fax', 'trim');
		$this->form_validation->set_rules('exhibit[email]', 'exhibit[email]*email', 'trim|required|valid_email');
		$this->form_validation->set_rules('exhibit[address]', 'exhibit[address]*Address', 'trim|required');
		$this->form_validation->set_rules('exhibit[company_logo]', 'exhibit[company_logo]*company logo', 'trim');
		$this->form_validation->set_rules('exhibit[company_ad]', 'exhibit[company_ad]*company ad', 'trim');
		$this->form_validation->set_rules('exhibit[contact_person][name]', 'exhibit[contact_person][name]*contact person name', 'trim|required');
		$this->form_validation->set_rules('exhibit[contact_person][designation]', 'exhibit[contact_person][designation]*contact person designation', 'trim');
		$this->form_validation->set_rules('exhibit[contact_person][mobile]', 'exhibit[contact_person][mobile]*contact person mobile', 'trim|min_length[12]');
		$this->form_validation->set_rules('exhibit[contact_person][email]', 'exhibit[contact_person][email]*contact person email', 'trim|required|valid_email');
		$this->form_validation->set_rules('exhibit[contact_person][phone]', 'exhibit[contact_person][phone]*contact person phone', 'trim|min_length[12]');
		$this->form_validation->set_rules('exhibit[contact_person][fax]', 'exhibit[contact_person][fax]*contact person fax', 'trim');

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function form_03_profile_validate() {
		//$this->form_validation->set_rules('profile', 'profile*company profile', 'trim|required|max_length[300]');
		$this->form_validation->set_rules('profile', 'profile*company profile', 'trim|required');

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function form_03_products_validate() {
		//print_r($this->input->post()); die();
		$this->form_validation->set_rules('products[main][]', 'products[main][]*your business', 'trim|required');
		$this->form_validation->set_rules('products[other][]', 'products[other][]*your other business', 'trim');
		$this->form_validation->set_rules('products[product][]', 'products[product][]*your products', 'trim|required');

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function form_03_principle_add_validate() {
		if ($this->input->post('principles[phone]')) {
			$_POST['principles[phone]'] = str_replace(array('+','-','_'), '', $this->input->post('principles[phone]'));
		}
		if ($this->input->post('principles[person_mobile]')) {
			$_POST['principles[person_mobile]'] = str_replace(array('+','-','_'), '', $this->input->post('principles[person_mobile]'));
		}
		$this->form_validation->set_rules('principles[full_name]', 'principles[full_name]*principle name', 'trim|required');
		$this->form_validation->set_rules('principles[country]', 'principles[country]*country', 'trim|required');
		$this->form_validation->set_rules('principles[phone]', 'principles[phone]*telephone number', 'trim|min_length[12]');
		$this->form_validation->set_rules('principles[fax]', 'principles[fax]*fax number', 'trim');
		$this->form_validation->set_rules('principles[email]', 'principles[email]*email', 'trim|valid_email');

		$this->form_validation->set_rules('principles[person_name]', 'principles[person_name]*person name', 'trim');
		$this->form_validation->set_rules('principles[designation]', 'principles[designation]*designation', 'trim');
		$this->form_validation->set_rules('principles[person_mobile]', 'principles[person_mobile]*person mobile', 'trim|min_length[12]');
		$this->form_validation->set_rules('principles[person_email]', 'principles[person_email]*person email', 'trim|valid_email');

		$this->form_validation->set_rules('principles[address]', 'principles[address]*address', 'trim');
		$this->form_validation->set_rules('principles[company_logo]', 'principles[company_logo]*company logo', 'trim');

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function form_03_principles_validate() {
		if ($this->input->post('is_active') && $this->input->post('is_active') == 1) {
			$this->form_validation->set_rules('principles_list[]', 'principles_list[]*company principles', 'trim|required');
		} else {
			return $this->common->doError(func_num_args(), "done", true);
		}

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), 'Atleast one company principles is required.');
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function form_03_submit() {
		if ($this->form_03_exhibit_validate() !== true ||
			$this->form_03_profile_validate() !== true ||
			$this->form_03_products_validate() !== true ||
			$this->form_03_principles_validate() !== true) {
			echo json_encode(array(
				'error' => 1,
				'message' => 'Validation failed!'
			));
			die();
		}


		$data = json_encode($this->input->post());

		$check = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->count_all_results('es_exhibition_booking_forms_data');

		if ($check > 0) {
			$this->db
				->where('exhibition_id', $this->event->id)
				->where('booking_id', $this->booking->id)
				->where('form_id', $this->form_id)
				->update('es_exhibition_booking_forms_data', array(
					'form_data' => $data,
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
					'form_data' => $data,
					'created_on' => date('Y-m-d H:i:s'),
				));
		}

		$this->session->set_flashdata('message', 'Form has been submitted successfully');
		echo json_encode(array(
			'error' => 0,
			'data' => base_url('dashboard')
		));
	}



	function get_main_business_sector() {

		$data = $this->db
			->select('id, type, data as title')
			->where('type', 'main_business_sector')
			->get('input_data_list')
			->result();

		echo json_encode(array(
			'error' => false,
			'data' => $data
		));
	}
	function get_main_business_type() {
		$sector_id = $this->input->post('sector_id');

		$data = $this->db
			->select('id, type, data as title')
			->where('type', 'main_business_type_' . $sector_id)
			->get('input_data_list')
			->result();

		echo json_encode(array(
			'error' => false,
			'data' => $data
		));
	}
	function get_main_business_area() {
		$sector_id = $this->input->post('sector_id');
		$type_id = $this->input->post('type_id');

		$data = $this->db
			->select('id, type, data as title')
			->where('type', 'main_business_area_' . $sector_id . '_' . $type_id)
			->get('input_data_list')
			->result();

		echo json_encode(array(
			'error' => false,
			'data' => $data
		));
	}
}