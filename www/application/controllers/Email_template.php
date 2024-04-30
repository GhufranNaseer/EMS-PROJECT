<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_template extends MY_Controller {
	protected function rule() {
		$this->activateRightsSystem();
		$crd = array(
			'crd_list,crd_add,crd_add_submit' => array(
				'rule' => '@'
			),
			'crd_list_datatable,
			crd_add_validate,
			get_halls_json,
			check_hall_usage' => array(
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
			),
			'crd_edit_submit' => array(
				'rule' => '@'
			)
		);
		$this->load->model('usermdl');
		$this->myparent = base_url('locations.html');
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
			->get('email_template');
		
		
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();

	}

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('email_template/list');
	}


	function crd_list_datatable() {
		$this->load->library('datatables');
		$this->datatables
			->select('id,
					title,
					subject,
					message
				  ', false)

			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'email_template-edit.html?id=' . urlencode(myid($id)) . '">Edit</a>';
				if (ALLOW_DELETION)
					$html .= ' | <a href="' . base_url() . 'email_template-delete.html?id=' . urlencode(myid($id)) . '" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';
				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->from('email_template');

		print ($this->datatables->generate());
	}


	function crd_add() {
		$this->load->view('includes/after_login/head');
		$this->load->view('email_template/add');
	}

	function crd_add_validate() {

		$this->form_validation->set_rules('email_template_title', 'email_template_title*Email Title', 'trim|required');
		$this->form_validation->set_rules('email_template_subject', 'email_template_subject*Email Subject', 'trim|required');
		//$this->form_validation->set_rules('email_template_message', 'email_template_message*Email message', 'trim|required');;

		if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else
            return $this->common->doError(func_num_args(), "done", true);
	}

	function crd_add_submit() {
		if ($this->crd_add_validate() !== true)
			show_404();

		$data = array(
			'title' => $this->input->post('email_template_title'),
			'subject' => $this->input->post('email_template_subject'),
			'message' => $this->input->post('email_template_message'),
			'created_on' => date('Y-m-d H:i:s'),
		);

		$this->db
			->set($data)
			->insert('email_template');

		$this->session->set_flashdata('message', 'Email has been added successfully');
		redirect(base_url('email_template.html'));
	}



	function crd_edit() {
		$this->checkEditId();
		$this->load->view('includes/after_login/head');
		$this->load->view('email_template/edit');
	}

	function crd_edit_validate() {
		$this->checkEditId();

		$this->form_validation->set_rules('email_template_title', 'email_template_title*Email Title', 'trim|required');
		$this->form_validation->set_rules('email_template_subject', 'email_template_subject*Email Subject', 'trim|required');

		if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else
            return $this->common->doError(func_num_args(), "done", true);
	}

	function crd_edit_submit() {
		if ($this->crd_edit_validate() !== true)
			show_404();


		$data = array(
			'title' => $this->input->post('email_template_title'),
			'subject' => $this->input->post('email_template_subject'),
			'message' => $this->input->post('email_template_message'),
			'updated_on' => date('Y-m-d H:i:s'),
		);


		$this->db
			->set($data)
			->where('id', $this->formdata->id)
			->update('email_template');

		$this->session->set_flashdata('message', 'Email has been updated successfully');
		redirect(base_url('email_template.html'));
	}


	function crd_delete() {
		$this->checkEditId();
		if (!ALLOW_DELETION)
			show_404();

		// Define the where condition for deletion
		$where_condition = array(
			'id' => $this->formdata->id
		);

		// Execute the delete query
		$this->db->delete('email_template', $where_condition);

		$this->session->set_flashdata('message', 'Email has been deleted successfully');
		redirect(base_url('email_template.html'));
	}
}