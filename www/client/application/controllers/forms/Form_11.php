<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form_11 extends MY_Controller {
	function index() {
		$this->load->view('includes/after_login/head');
		$this->load->view('forms/form_11');
	}

	function form_11_validate() {
		$this->form_validation->set_rules('form_id', 'form_id*form id', 'trim|required');


		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function form_11_submit() {
		echo "<pre>";
		print_r($this->input->post());
		echo "<pre>";
	}
}