<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form_21 extends MY_Controller {
	public $form_id = 21;

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
		$this->load->view('forms/form_21');
	}

	function form_21_validate() {
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


		$this->form_validation->set_rules('visa_name', 'visa_name*visa_name', 'trim|required');
		$this->form_validation->set_rules('visa_country', 'visa_country*visa_country', 'trim|required');
		$this->form_validation->set_rules('visa_city', 'visa_city*visa_city', 'trim|required');
		$this->form_validation->set_rules('visa_nationality', 'visa_nationality*visa_nationality', 'trim|required');
		$this->form_validation->set_rules('visa_passport', 'visa_passport*visa_passport', 'trim|required');
		$this->form_validation->set_rules('visa_passport_issue_date', 'visa_passport_issue_date*visa_passport_issue_date', 'trim|required');
		$this->form_validation->set_rules('visa_passport_expiry_date', 'visa_passport_expiry_date*visa_passport_expiry_date', 'trim|required');

		$this->form_validation->set_rules('contact_person_designation', 'contact_person_designation*contact_person_designation', 'trim|required');
		$this->form_validation->set_rules('contact_person_email', 'contact_person_email*contact_person_email', 'trim|required|valid_email');
		$this->form_validation->set_rules('contact_person_mobile', 'contact_person_mobile*contact_person_mobile', 'trim|required|numeric');
		$this->form_validation->set_rules('contact_person_telephone', 'contact_person_telephone*contact_person_telephone', 'trim|numeric');
		$this->form_validation->set_rules('contact_person_fax', 'contact_person_fax*contact_person_fax', 'trim|numeric');
		$this->form_validation->set_rules('contact_person_bio_image[0]', 'contact_person_bio_image[0]*contact_person_bio_image', 'trim|required');
		$this->form_validation->set_rules('contact_person_pic_image[0]', 'contact_person_pic_image[0]*contact_person_pic_image', 'trim');

		$this->form_validation->set_rules('concerned_country', 'concerned_country*concerned_country', 'trim|required');
		$this->form_validation->set_rules('concerned_city', 'concerned_city*concerned_city', 'trim|required');
		$this->form_validation->set_rules('concerned_telephone_number', 'concerned_telephone_number*concerned_telephone_number', 'trim|numeric');
		$this->form_validation->set_rules('concerned_fax_number', 'concerned_fax_number*concerned_fax_number', 'trim|numeric');
		$this->form_validation->set_rules('concerned_email', 'concerned_email*concerned_email', 'trim|valid_email');
		$this->form_validation->set_rules('concerned_address', 'concerned_address*concerned_address', 'trim');


		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function form_21_submit() {
		if ($this->form_21_validate() !== true)
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
			$new_data->visas[] = $data;

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
					'form_data' => json_encode(array('visas' => array($data))),
					'created_on' => date('Y-m-d H:i:s'),
				));
		}

		$this->session->set_flashdata('message', 'Form has been submitted successfully');
		redirect(base_url('forms/form_21'));
	}

	function delete_visa() {
		$id = $this->input->get('id');

		$old_form = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->get('es_exhibition_booking_forms_data')
			->row();

		$all_data = json_decode($old_form->form_data);
		$new_data = array();
		foreach ($all_data->visas as $data) {
			if ($data->id != $id) {
				$new_data[] = $data;
			}
		}
		$all_data->visas = $new_data;

		$this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->update('es_exhibition_booking_forms_data', array(
				'form_data' => json_encode($all_data),
				'modified_on' => date('Y-m-d H:i:s'),
			));

		$this->session->set_flashdata('message', 'Record has been successfully deleted!');
		redirect(base_url('forms/form_21'));
	}
}