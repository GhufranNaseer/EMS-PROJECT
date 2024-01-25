<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form_16 extends MY_Controller {
	function index() {
		$this->load->view('includes/after_login/head');
		$this->load->view('forms/form_16');
	}

	function form_16_validate() {
		$this->form_validation->set_rules('form_id', 'form_id*form id', 'trim|required');

		$this->form_validation->set_rules('company_name', 'company_name*company name', 'trim|required');
		$this->form_validation->set_rules('hall_no', 'hall_no*hall no', 'trim|required', 'trim|required|numeric');
		$this->form_validation->set_rules('company_address', 'company_address*company address', 'trim|required');
		$this->form_validation->set_rules('stand_no', 'stand_no*stand no', 'trim|required', 'trim|required|numeric');
		$this->form_validation->set_rules('company_country', 'company_country*company country', 'trim|required');
		$this->form_validation->set_rules('company_zip_code', 'company_zip_code*company zip code', 'trim|required', 'trim|required|numeric');
		$this->form_validation->set_rules('company_telephone_no', 'company_telephone_no*company telephone no', 'trim|required', 'trim|required|numeric');
		$this->form_validation->set_rules('company_fax_no', 'company_fax_no*company fax no', 'trim|required', 'trim|required|numeric');
		$this->form_validation->set_rules('company_email', 'company_email*company email', 'trim|required', 'trim|required|valid_email|is_unique[users.user_email]');
		$this->form_validation->set_rules('company_cell_no', 'company_cell_no*company cell no', 'trim|required', 'trim|required|numeric');
		$this->form_validation->set_rules('company_contact_person', 'company_contact_person*company contact person', 'trim|required');
		$this->form_validation->set_rules('company_tittle', 'company_tittle*company tittle', 'trim|required');

		$this->form_validation->set_rules('item_1', 'item_1*item 1', 'trim|required');
		$this->form_validation->set_rules('item_2', 'item_2*item 2', 'trim|required');
		$this->form_validation->set_rules('item_3', 'item_3*item 3', 'trim|required');
		$this->form_validation->set_rules('item_4', 'item_4*item 4', 'trim|required');
		$this->form_validation->set_rules('item_5', 'item_5*item 5', 'trim|required');
		$this->form_validation->set_rules('item_6', 'item_6*item 6', 'trim|required');
		$this->form_validation->set_rules('item_7', 'item_7*item 7', 'trim|required');
		$this->form_validation->set_rules('item_8', 'item_8*item 8', 'trim|required');
		$this->form_validation->set_rules('item_9', 'item_9*item 9', 'trim|required');
		$this->form_validation->set_rules('item_10', 'item_10*item 10', 'trim|required');
		$this->form_validation->set_rules('item_11', 'item_11*item 11', 'trim|required');
		$this->form_validation->set_rules('item_12', 'item_12*item 12', 'trim|required');
		$this->form_validation->set_rules('item_13', 'item_13*item 13', 'trim|required');
		$this->form_validation->set_rules('item_14', 'item_14*item 14', 'trim|required');
		$this->form_validation->set_rules('item_15', 'item_15*item 15', 'trim|required');
		$this->form_validation->set_rules('item_16', 'item_16*item 16', 'trim|required');
		$this->form_validation->set_rules('item_17', 'item_17*item 17', 'trim|required');
		$this->form_validation->set_rules('item_18', 'item_18*item 18', 'trim|required');
		$this->form_validation->set_rules('item_19', 'item_19*item 19', 'trim|required');
		$this->form_validation->set_rules('item_20', 'item_20*item 20', 'trim|required');

		$this->form_validation->set_rules('manager_name', 'manager_name*manager name', 'trim|required');
		$this->form_validation->set_rules('manager_title', 'manager_title*manager title', 'trim|required');
		$this->form_validation->set_rules('manager_date', 'manager_date*manager_date', 'trim|required');
		$this->form_validation->set_rules('manager_signature', 'manager_signature*manager signature', 'trim|required');
		$this->form_validation->set_rules('cont_company_name', 'cont_company_name*cont company name', 'trim|required');
		$this->form_validation->set_rules('contractor_company_personnel', 'contractor_company_personnel*contractor company personnel', 'trim|required');
		$this->form_validation->set_rules('contractor_title', 'contractor_title*contractor title', 'trim|required');
		$this->form_validation->set_rules('contractor_date', 'contractor_date*contractor date', 'trim|required');
		$this->form_validation->set_rules('contractor_signature', 'contractor_signature*contractor signature', 'trim|required');


		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function form_16_submit() {
		echo "<pre>";
		print_r($this->input->post());
		echo "<pre>";
	}
}