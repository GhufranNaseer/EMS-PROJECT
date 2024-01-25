<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stall_builder extends MY_Controller {
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
		$this->myparent = base_url('stall-builder.html');
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
			->get('es_stall_builders');
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();
		//if ($this->formdata->id == $this->userdata->id)
		//	show_404();

	}

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('stall_builder/list');
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
				$html = '<a href="' . base_url() . 'stall-builder-edit.html?id=' . urlencode(myid($id)) . '">Edit</a>';
				if (ALLOW_DELETION)
					$html .= ' | <a href="' . base_url() . 'stall-builder-delete.html?id=' . urlencode(myid($id)) . '" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';
				return "<center>{$html}</center>";
			}, NULL)
			->from('es_stall_builders')
			->where('is_deleted', 0);
		print ($this->datatables->generate());
	}


	function crd_add() {
		$this->load->view('includes/after_login/head');
		$this->load->view('stall_builder/add');
	}

	function crd_add_validate() {

		$this->form_validation->set_rules('company_name', 'company_name*company name', 'trim');
		/*$this->form_validation->set_rules('person_name', 'person_name*person name', 'trim');
		$this->form_validation->set_rules('phone', 'phone*phone', 'trim|numeric|min_length[8]');
		$this->form_validation->set_rules('designation', 'designation*designation', 'trim');
		$this->form_validation->set_rules('fax', 'fax*fax', 'trim|numeric');
		$this->form_validation->set_rules('mobile', 'mobile*mobile', 'trim|numeric|min_length[8]');
		$this->form_validation->set_rules('company_email', 'company_email*company email', 'trim');
		$this->form_validation->set_rules('person_email', 'person_email*person email', 'trim');
		$this->form_validation->set_rules('url', 'url*url', 'trim');
		$this->form_validation->set_rules('company_address', 'company_address*company address', 'trim');*/

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
			->insert('es_stall_builders');
		$id = $this->db->insert_id();
		$this->db->trans_complete();

		$this->session->set_flashdata('message', 'Stall Builder has been created successfully');
		redirect(base_url('stall-builder.html'));
	}



	function crd_edit() {
		$this->checkEditId();
		$this->load->view('includes/after_login/head');
		$this->load->view('stall_builder/edit');
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
			->update('es_stall_builders');

		$this->db->trans_complete();
		$this->session->set_flashdata('message', 'Stall Builder has been updated successfully');
		redirect(base_url('stall-builder.html'));
	}

	function crd_edit_validate() {
		$this->checkEditId();

		$this->form_validation->set_rules('company_name', 'company_name*company name', 'trim');
		/*$this->form_validation->set_rules('person_name', 'person_name*person name', 'trim');
		$this->form_validation->set_rules('phone', 'phone*phone', 'trim|numeric|min_length[8]');
		$this->form_validation->set_rules('designation', 'designation*designation', 'trim');
		$this->form_validation->set_rules('fax', 'fax*fax', 'trim|numeric');
		$this->form_validation->set_rules('mobile', 'mobile*mobile', 'trim|numeric|min_length[8]');
		$this->form_validation->set_rules('company_email', 'company_email*company email', 'trim');
		$this->form_validation->set_rules('person_email', 'person_email*person email', 'trim');
		$this->form_validation->set_rules('url', 'url*url', 'trim');
		$this->form_validation->set_rules('company_address', 'company_address*company address', 'trim');*/


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

		/*$event_check = $this->db
			->where('event_organizer', $this->formdata->id)
			->count_all_results('es_exhibitions');

		if ($event_check > 0) {
			$this->session->set_flashdata('error', 'Organizer cannot be deleted, because its assigned to some events!');
			redirect(base_url('organizer.html'));
			die();
		}*/

		$this->db
			->where('id', $this->formdata->id)
			->update('es_stall_builders', array(
				'is_deleted' => 1,
				'deleted_by' => $this->userdata->id,
				'deleted_on' => date('Y-m-d H:i:s')
			));

		$this->session->set_flashdata('message', 'Stall Builder has been deleted successfully');
		redirect(base_url('stall-builder.html'));
	}
}