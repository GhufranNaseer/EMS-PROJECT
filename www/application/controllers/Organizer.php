<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Organizer extends MY_Controller {
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
		$this->myparent = base_url('organizer.html');
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
			->get('es_organizer');
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();
		//if ($this->formdata->id == $this->userdata->id)
		//	show_404();

	}

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('organizer/list');
	}


	function crd_list_datatable() {
		$this->load->library('datatables');
		$this->datatables
			->select('es_organizer.id, 
				  es_organizer.organizer_name, 
				  es_organizer.organizer_company, 
				  es_organizer.organizer_email, 
				  es_organizer.organizer_phone, 
				  es_organizer.organizer_country,
				  es_organizer.organizer_city', false)

			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'organizer-edit.html?id=' . urlencode(myid($id)) . '">Edit</a>';
				if (ALLOW_DELETION)
					$html .= ' | <a href="' . base_url() . 'organizer-delete.html?id=' . urlencode(myid($id)) . '" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';
				return "<center>{$html}</center>";
			}, NULL)
			->from('es_organizer')
			->where('es_organizer.is_deleted', 0);

        if ($this->input->get('filter_event')){
            $this->datatables->join('es_exhibitions', 'es_exhibitions.event_organizer = es_organizer.id', 'LEFT');
            $this->datatables->where('es_exhibitions.id', $this->input->get('filter_event'));
        }

		print ($this->datatables->generate());
	}


	function crd_add() {
		$this->load->view('includes/after_login/head');
		$this->load->view('organizer/add');
	}

	function crd_add_submit() {
		if ($this->crd_add_validate() !== true)
			show_404();

		$data = array(
			'organizer_name' => $this->input->post('organizer_name'),
			'organizer_company' => $this->input->post('organizer_company'),
			'organizer_email' => $this->input->post('organizer_email'),
			'organizer_phone' => $this->input->post('organizer_phone'),
            'organizer_country' => $this->input->post('organizer_country'),
            'organizer_city' => $this->input->post('organizer_city'),
			'organizer_image' => $this->funcs->make_image_string($this->input->post('organizer_logo')),
			'created_on' => date('Y-m-d H:i:s'),
		);

		$this->db->trans_start();
		$this->db
			->set($data)
			->insert('es_organizer');
		$id = $this->db->insert_id();
		$this->db->trans_complete();

		$this->session->set_flashdata('message', 'User has been created successfully');
		redirect(base_url('organizer.html'));
	}

	function crd_add_validate() {

		$this->form_validation->set_rules('organizer_name', 'user_first_name*Name', 'trim|required');
		$this->form_validation->set_rules('organizer_company', 'organizer_company*company', 'trim|required|is_unique[es_organizer.organizer_company]');
		$this->form_validation->set_rules('organizer_email', 'user_email*Email', 'trim|required');
		$this->form_validation->set_rules('organizer_phone', 'organizer_phone*Phone', 'trim|required|numeric|min_length[8]');
		$this->form_validation->set_rules('organizer_country', 'organizer_country*Country', 'trim|required');
        $this->form_validation->set_rules('organizer_city', 'organizer_city*City', 'trim|required');
        $this->form_validation->set_rules('organizer_logo[0]', 'organizer_logo[0]*organizer logo', 'trim');

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function crd_edit() {
		$this->checkEditId();
		$this->load->view('includes/after_login/head');
		$this->load->view('organizer/edit');
	}



	function crd_edit_submit() {
		if ($this->crd_edit_validate() !== true)
			show_404();
		$data = array(
			'organizer_name' => $this->input->post('organizer_name'),
			'organizer_company' => $this->input->post('organizer_company'),
			'organizer_email' => $this->input->post('organizer_email'),
			'organizer_phone' => $this->input->post('organizer_phone'),
            'organizer_country' => $this->input->post('organizer_country'),
            'organizer_city' => $this->input->post('organizer_city'),
			'organizer_image' => $this->funcs->make_image_string($this->input->post('organizer_logo')),
		);


		$this->db->trans_start();
		$this->db
			->set($data)
			->where('id', $this->formdata->id)
			->update('es_organizer');

		$this->db->trans_complete();
		$this->session->set_flashdata('message', 'User has been updated successfully');
		redirect(base_url('organizer.html'));
	}

	function crd_edit_validate() {
		$this->checkEditId();

		$this->form_validation->set_rules('organizer_name', 'organizer_name*Name', 'trim|required');
		$this->form_validation->set_rules('organizer_company', 'organizer_company*Company', 'trim|required|set_edit_mood[id.' . $this->formdata->id . ']|is_unique[es_organizer.organizer_company]');
		$this->form_validation->set_rules('organizer_email', 'organizer_email*Email', 'trim|required');
		$this->form_validation->set_rules('organizer_phone', 'organizer_phone*Phone', 'trim|required|numeric|min_length[8]');
        $this->form_validation->set_rules('organizer_country', 'organizer_country*Country', 'trim|required');
        $this->form_validation->set_rules('organizer_city', 'organizer_city*City', 'trim|required');
		$this->form_validation->set_rules('organizer_logo[0]', 'organizer_logo[0]*organizer logo', 'trim');

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

		$event_check = $this->db
			->where('event_organizer', $this->formdata->id)
			->count_all_results('es_exhibitions');

		if ($event_check > 0) {
			$this->session->set_flashdata('error', 'Organizer cannot be deleted, because its assigned to some events!');
			redirect(base_url('organizer.html'));
			die();
		}

		$this->db
			->where('id', $this->formdata->id)
			->update('es_organizer', array(
				'is_deleted' => 1,
				'deleted_by' => $this->userdata->id,
				'deleted_on' => date('Y-m-d H:i:s')
			));

		$this->session->set_flashdata('message', 'User has been deleted successfully');
		redirect(base_url('organizer.html'));
	}
}