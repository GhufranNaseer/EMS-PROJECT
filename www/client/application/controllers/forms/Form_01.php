<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form_01 extends MY_Controller {

	public $form_id = 1;

	function index() {
		$this->formdata = null;

		$check = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->get('es_exhibition_booking_forms_data')
			->row();

		if (isset($check)) {
			$data = json_decode($check->form_data, true);
			
			if ($this->input->get('tab') && $this->input->get('tab') == 'bare' && array_key_exists('bare_stall_data', $data)) {
				$this->formdata = $data['bare_stall_data'];
			} else if ($this->input->get('tab') == 'shell' && array_key_exists('shell_stall_data', $data)) {
				$this->formdata = $data['shell_stall_data'];
			}

		}

		

		$this->load->view('includes/after_login/head');
		$this->load->view('forms/form_01');
	}

	function form_01_validate() {
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

		if ($this->input->post('has_shell_stall') && $this->input->post('has_shell_stall') == 1) {
			$this->form_validation->set_rules('stall_fascia_name', 'stall_fascia_name*stall fascia name', 'trim|required|max_length[24]');
		}

		if ($this->input->post('has_bare_stall') && $this->input->post('has_bare_stall') == 1) {
			$this->form_validation->set_rules('stall_building_contractor[]', 'stall_building_contractor[]*stall building contractor', 'trim|required');
		}


		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function form_01_submit() {
		if ($this->form_01_validate() !== true)
			show_404();


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

			$old_data = json_decode($old_form->form_data, true);
			if ($this->input->post('has_bare_stall') && $this->input->post('has_bare_stall') == 1) {
				$old_data->bare_stall_data = $this->input->post();
			} else {
				$old_data->shell_stall_data = $this->input->post();
			}
			$data = json_encode($old_data);

			$this->db
				->where('exhibition_id', $this->event->id)
				->where('booking_id', $this->booking->id)
				->where('form_id', $this->form_id)
				->update('es_exhibition_booking_forms_data', array(
					'form_data' => $data,
					'modified_on' => date('Y-m-d H:i:s'),
				));
		} else {
			$data = array();
			if ($this->input->post('has_bare_stall') && $this->input->post('has_bare_stall') == 1) {
				$data = array('bare_stall_data' => $this->input->post());
			} else {
				$data = array('shell_stall_data' => $this->input->post());
			}
			$data = json_encode($data);

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
		redirect(base_url('dashboard'));
	}
}