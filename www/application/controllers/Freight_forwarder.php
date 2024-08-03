<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Freight_forwarder extends MY_Controller {
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
			'crd_edit,crd_delete' => array(
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
		$this->myparent = base_url('freight-forwarder.html');
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
			->get('es_freight_forwarders');
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();
		//if ($this->formdata->id == $this->userdata->id)
		//	show_404();

	}

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('freight_forwarder/list');
	}


	function crd_list_datatable() {
		$this->load->library('datatables');
		$this->datatables
			->select('id, 
				  company_name, 
				  person_name, 
				  phone, 
				  mobile, 
				  company_email,
				  is_active', false)
			->unset_column('is_active')
			->add_column('active', function ($row) {
				$active = $row['is_active'];
				return ($active == 1) ? '<center><img src="' . base_url('assets/img/t.png') . '" /></center>' : '<center><img src="' . base_url('assets/img/cross.png') . '" /></center>';
			}, NULL)
			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'freight-forwarder-edit.html?id=' . urlencode(myid($id)) . '">Edit</a>';
				if (ALLOW_DELETION)
					$html .= ' | <a href="' . base_url() . 'freight-forwarder-delete.html?id=' . urlencode(myid($id)) . '" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';
				return "<center>{$html}</center>";
			}, NULL)
			->from('es_freight_forwarders')
			->where('is_deleted', 0);
		print ($this->datatables->generate());
	}


	function crd_add() {
		$this->load->view('includes/after_login/head');
		$this->load->view('freight_forwarder/add');
	}

	function crd_add_validate() {

		$this->form_validation->set_rules('company_name', 'company_name*company name', 'trim|required');

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function crd_add_submit() {
		if ($this->crd_add_validate() !== true)
			show_404();

		$isActive = (is_null($this->input->post('active')) ? 0 : 1);

		$data = array(
			'company_name' => $this->input->post('company_name'),
			'person_name' => $this->input->post('person_name'),
			'phone' => $this->input->post('phone'),
			'designation' => $this->input->post('designation'),
			'fax' => $this->input->post('fax'),
			'mobile' => $this->input->post('mobile'),
			'company_email' => $this->input->post('company_email'),
			'person_email' => $this->input->post('person_email'),
			'url' => $this->input->post('url'),
			'company_address' => $this->input->post('company_address'),
            'company_logo' => $this->funcs->make_image_string($this->input->post('attachment')),
			'is_active' => $isActive,
			'created_on' => date('Y-m-d H:i:s'),
		);

		$this->db->trans_start();
		$this->db
			->set($data)
			->insert('es_freight_forwarders');
		$id = $this->db->insert_id();
		$this->db->trans_complete();

		$this->session->set_flashdata('message', 'Freight forwarder has been created successfully');
		redirect(base_url('freight-forwarder.html'));
	}



	function crd_edit() {
		$this->checkEditId();
		$this->load->view('includes/after_login/head');
		$this->load->view('freight_forwarder/edit');
	}



	function crd_edit_submit() {
		if ($this->crd_edit_validate() !== true)
			show_404();
		$isActive = (is_null($this->input->post('active')) ? 0 : 1);

		$data = array(
			'company_name' => $this->input->post('company_name'),
			'person_name' => $this->input->post('person_name'),
			'phone' => $this->input->post('phone'),
			'designation' => $this->input->post('designation'),
			'fax' => $this->input->post('fax'),
			'mobile' => $this->input->post('mobile'),
			'company_email' => $this->input->post('company_email'),
			'person_email' => $this->input->post('person_email'),
			'url' => $this->input->post('url'),
            'company_logo' => $this->funcs->make_image_string($this->input->post('attachment')),
			'company_address' => $this->input->post('company_address'),
			'is_active' => $isActive,
		);


		$this->db->trans_start();
		$this->db
			->set($data)
			->where('id', $this->formdata->id)
			->update('es_freight_forwarders');

		$this->db->trans_complete();
		$this->session->set_flashdata('message', 'Freight forwarder has been updated successfully');
		redirect(base_url('freight-forwarder.html'));
	}

	function crd_edit_validate() {
		$this->checkEditId();

		$this->form_validation->set_rules('company_name', 'company_name*company name', 'trim|required');


		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else {
			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function crd_delete() {
		$this->checkEditId();
		if (!ALLOW_DELETION)
			show_404();

		$this->db
			->where('id', $this->formdata->id)
			->update('es_freight_forwarders', array(
				'is_deleted' => 1,
				'deleted_by' => $this->userdata->id,
				'deleted_on' => date('Y-m-d H:i:s')
			));

		$this->session->set_flashdata('message', 'Freight forwarder has been deleted successfully');
		redirect(base_url('freight-forwarder.html'));
	}
}