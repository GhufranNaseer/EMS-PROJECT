<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form_12 extends MY_Controller {
	function index() {
		$this->load->view('includes/after_login/head');
		$this->load->view('forms/form_12');
	}

	function form_12_validate() {
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


		$this->form_validation->set_rules('name_1', 'name_1*name 1', 'trim|required');
		$this->form_validation->set_rules('designation_1', 'designation_1*designation 1', 'trim|required');
		$this->form_validation->set_rules('nationality_1', 'nationality_1*nationality 1', 'trim|required');
		$this->form_validation->set_rules('car_reg_no_1', 'car_reg_no_1*car registration no 1', 'trim|required');
		$this->form_validation->set_rules('name_2', 'name_2*name 2', 'trim|required');
		$this->form_validation->set_rules('designation_2', 'designation_2*designation 2', 'trim|required');
		$this->form_validation->set_rules('nationality_2', 'nationality_2*nationality 2', 'trim|required');
		$this->form_validation->set_rules('car_reg_no_2', 'car_reg_no_2*car registration no 2', 'trim|required');
		$this->form_validation->set_rules('name_3', 'name_3*name 3', 'trim|required');
		$this->form_validation->set_rules('designation_3', 'designation_3*designation 3', 'trim|required');
		$this->form_validation->set_rules('nationality_3', 'nationality_3*nationality 3', 'trim|required');
		$this->form_validation->set_rules('car_reg_no_3', 'car_reg_no_3*car registration no 3', 'trim|required');
		$this->form_validation->set_rules('name_4', 'name_4*name 4', 'trim|required');
		$this->form_validation->set_rules('designation_4', 'designation_4*designation 4', 'trim|required');
		$this->form_validation->set_rules('nationality_4', 'nationality_4*nationality 4', 'trim|required');
		$this->form_validation->set_rules('car_reg_no_4', 'car_reg_no_4*car registration no 4', 'trim|required');
		$this->form_validation->set_rules('name_5', 'name_5*name 5', 'trim|required');
		$this->form_validation->set_rules('designation_5', 'designation_5*designation 5', 'trim|required');
		$this->form_validation->set_rules('nationality_5', 'nationality_5*nationality 5', 'trim|required');
		$this->form_validation->set_rules('car_reg_no_5', 'car_reg_no_5*car registration no 5', 'trim|required');
		$this->form_validation->set_rules('name_6', 'name_6*name 6', 'trim|required');
		$this->form_validation->set_rules('designation_6', 'designation_6*designation 6', 'trim|required');
		$this->form_validation->set_rules('nationality_6', 'nationality_6*nationality 6', 'trim|required');
		$this->form_validation->set_rules('car_reg_no_6', 'car_reg_no_6*car registration no 6', 'trim|required');
		$this->form_validation->set_rules('name_7', 'name_7*name 7', 'trim|required');
		$this->form_validation->set_rules('designation_7', 'designation_7*designation 7', 'trim|required');
		$this->form_validation->set_rules('nationality_7', 'nationality_7*nationality 7', 'trim|required');
		$this->form_validation->set_rules('car_reg_no_7', 'car_reg_no_7*car registration no 7', 'trim|required');
		$this->form_validation->set_rules('name_8', 'name_8*name 8', 'trim|required');
		$this->form_validation->set_rules('designation_8', 'designation_8*designation 8', 'trim|required');
		$this->form_validation->set_rules('nationality_8', 'nationality_8*nationality 8', 'trim|required');
		$this->form_validation->set_rules('car_reg_no_8', 'car_reg_no_8*car registration no 8', 'trim|required');

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

	function form_12_submit() {
		echo "<pre>";
		print_r($this->input->post());
		echo "<pre>";
	}
}