<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form_02 extends MY_Controller {
	function index() {
		$this->load->view('includes/after_login/head');
		$this->load->view('forms/form_02');
	}


	function form_02_validate() {
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


		$this->form_validation->set_rules('fascia_name1', 'fascia_name1*fascia name1', 'trim|required');
		$this->form_validation->set_rules('fascia_name2', 'fascia_name2*fascia name2', 'trim|required');
		$this->form_validation->set_rules('fascia_name3', 'fascia_name3*fascia name3', 'trim|required');
		$this->form_validation->set_rules('fascia_name4', 'fascia_name4*fascia name4', 'trim|required');
		$this->form_validation->set_rules('fascia_name5', 'fascia_name5*fascia name5', 'trim|required');
		$this->form_validation->set_rules('fascia_name6', 'fascia_name6*fascia name6', 'trim|required');
		$this->form_validation->set_rules('fascia_name7', 'fascia_name7*fascia name7', 'trim|required');
		$this->form_validation->set_rules('fascia_name8', 'fascia_name8*fascia name8', 'trim|required');
		$this->form_validation->set_rules('fascia_name9', 'fascia_name9*fascia name9', 'trim|required');
		$this->form_validation->set_rules('fascia_name10', 'fascia_name10*fascia name10', 'trim|required');
		$this->form_validation->set_rules('fascia_name11', 'fascia_name11*fascia name11', 'trim|required');
		$this->form_validation->set_rules('fascia_name12', 'fascia_name12*fascia name12', 'trim|required');
		$this->form_validation->set_rules('fascia_name13', 'fascia_name13*fascia name13', 'trim|required');
		$this->form_validation->set_rules('fascia_name14', 'fascia_name14*fascia name14', 'trim|required');
		$this->form_validation->set_rules('fascia_name15', 'fascia_name15*fascia name15', 'trim|required');
		$this->form_validation->set_rules('fascia_name16', 'fascia_name16*fascia name16', 'trim|required');
		$this->form_validation->set_rules('fascia_name17', 'fascia_name17*fascia name17', 'trim|required');
		$this->form_validation->set_rules('fascia_name18', 'fascia_name18*fascia name18', 'trim|required');
		$this->form_validation->set_rules('fascia_name19', 'fascia_name19*fascia name19', 'trim|required');
		$this->form_validation->set_rules('fascia_name20', 'fascia_name20*fascia name20', 'trim|required');


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

	function form_02_submit() {
		echo "<pre>";
		print_r($this->input->post());
		echo "<pre>";
	}
}