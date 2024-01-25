<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forms extends MY_Controller {
	protected function rule() {
		$this->activateRightsSystem();
		$crd = array(
			'crd_list,crd_add,crd_add_submit' => array(
				'rule' => '@'
			),
			'crd_list_datatable,crd_add_validate' => array(
				'rule' => '@',
				'ajaxOnly' => true
			)
		);
		$crd_dit = array(
			'crd_edit, crd_view' => array(
				'rule' => '@'
			),
			'crd_edit_validate' => array(
				'ajaxOnly' => true,
				'rule' => '@'
			)
		,
			'crd_edit_submit' => array(
				'rule' => '@'
			)
		);
		$this->load->model('usermdl');
		$this->myparent = base_url('forms.html');
		return array_merge($crd, $crd_dit);
	}

	protected function checkEditId() {
		$id = $this->input->get('id');
		if (is_null($id))
			show_404();
		$id = $this->db->escape($id);
		$key = config_item('encryption_key');
		$key = $this->db->escape($key);
		$r = $this->db
			->where("MD5(CONCAT($key,`id`)) = $id")
			->get('es_order_forms');
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();
	}

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('forms/list');
	}

	function crd_list_datatable() {
		$this->load->library('datatables');
		$this->datatables
			->select('id, 
				  form_number, 
				  form_name,
				  IF(is_essential=1, "Essential", "Optional") as form_type', false)
            ->add_column('col_action', function ($row) {
                $id = $row['id'];
                $html = '<a href="' . base_url() . 'forms-edit.html?id=' . urlencode(myid($id)) . '">Edit</a>';
                //$html .= ' | <a href="' . base_url() . 'forms-view.html?id=' . urlencode(myid($id)) . '">View</a>';

                return "<center>{$html}</center>";
            }, NULL)
			->from('es_order_forms');
		print ($this->datatables->generate());
	}


	function crd_edit() {
		$this->checkEditId();
		$this->load->view('includes/after_login/head');
		$this->load->view('forms/edit');
	}

	function crd_edit_submit() {
		if ($this->crd_edit_validate() !== true)
			show_404();
		$data = array(
			'form_number' => $this->input->post('form_number'),
			'form_name' => $this->input->post('form_name'),
		);
		$this->db->trans_start();
		$this->db
			->set($data)
			->where('id', $this->formdata->id)
			->update('es_order_forms');

		$this->db->trans_complete();
		$this->session->set_flashdata('message', 'Form has been updated successfully');
		redirect(base_url('forms.html'));
	}

	function crd_edit_validate() {
		$this->checkEditId();

		$this->form_validation->set_rules('form_number', 'form_number*Form Number', 'trim|required');
		$this->form_validation->set_rules('form_name', 'form_name*Form Name', 'trim|required');
		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else {
			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	/* NOT IN USE */
	function crd_view() {
		$this->checkEditId();
		$this->load->view('includes/after_login/head');
		$this->load->view('forms/view');
	}


}