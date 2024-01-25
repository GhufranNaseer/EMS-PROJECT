<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form_07 extends MY_Controller {
	function index() {
		$this->load->view('includes/after_login/head');
		$this->load->view('forms/form_07');
	}

	function form_07_validate() {
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

		$this->form_validation->set_rules('band', 'band*band', 'trim|required', 'trim|required|numeric');
		$this->form_validation->set_rules('logo', 'logo*logo', 'trim|required', 'trim|required|numeric');
		$this->form_validation->set_rules('page_advert', 'page_advert*page advert', 'trim|required', 'trim|required|numeric');
		$this->form_validation->set_rules('first_page', 'first_page*first page', 'trim|required', 'trim|required|numeric');
		$this->form_validation->set_rules('full_page', 'full_page*full page', 'trim|required', 'trim|required|numeric');
		$this->form_validation->set_rules('inner_back', 'inner_back*inner back', 'trim|required', 'trim|required|numeric');
		$this->form_validation->set_rules('inner_front', 'inner_front*inner front', 'trim|required', 'trim|required|numeric');
		$this->form_validation->set_rules('outer_back', 'outer_back*outer back', 'trim|required', 'trim|required|numeric');
		$this->form_validation->set_rules('quarter_page', 'quarter_page*quarter page', 'trim|required', 'trim|required|numeric');

		$this->form_validation->set_rules('insertions_entry[]', 'insertions_entry*Insertionsentry', 'trim|required');

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

	function form_07_submit() {
		echo "<pre>";
		print_r($this->input->post());
		echo "<pre>";
	}
}