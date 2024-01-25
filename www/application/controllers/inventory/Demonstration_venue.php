<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Demonstration_venue extends MY_Controller {
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
			),
			'crd_edit_submit' => array(
				'rule' => '@'
			)
		);
		$this->load->model('usermdl');
		$this->myparent = base_url('demonstration-venue.html');
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
			->get('es_demonstration_venue');
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();

	}

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('demonstration_venue/list');
	}


	function crd_list_datatable() {
		$this->load->library('datatables');
		$this->datatables
			->select('id,
				  venue_title,
				  start_time,
				  end_time,
				  venue_cost_usd,
				  venue_cost_pkr', false)

			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'demonstration-venue-edit.html?id=' . urlencode(myid($id)) . '">Edit</a>';
				if (ALLOW_DELETION)
					$html .= ' | <a href="' . base_url() . 'demonstration-venue-delete.html?id=' . urlencode(myid($id)) . '" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';
				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where('is_deleted', 0)
			->from('es_demonstration_venue');

		print ($this->datatables->generate());
	}


	function crd_add() {
		$this->load->view('includes/after_login/head');
		$this->load->view('demonstration_venue/add');
	}

	function crd_add_validate() {

		$this->form_validation->set_rules('venue_title', 'venue_title*venue Title', 'trim|required');
		$this->form_validation->set_rules('venue_category', 'venue_category*Venue Category', 'trim|required');
		$this->form_validation->set_rules('start_time', 'start_time*Start Time', 'trim|required');
		$this->form_validation->set_rules('end_time', 'end_time*End Time', 'trim|required');
		$this->form_validation->set_rules('venue_cost_usd', 'venue_cost_usd*Venue Cost USD', 'trim|required|numeric');
		$this->form_validation->set_rules('venue_cost_pkr', 'venue_cost_pkr*Venue Cost PKR', 'trim|required|numeric');
		$this->form_validation->set_rules('venue_address', 'venue_address*Venue Address', 'trim|required');

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function crd_add_submit() {
		if ($this->crd_add_validate() !== true)
			show_404();


		$data = array(
			'venue_title' => $this->input->post('venue_title'),
			'venue_category' => $this->input->post('venue_category'),
			'start_time' => date('H:i:s', strtotime($this->input->post('start_time'))),
			'end_time' => date('H:i:s', strtotime($this->input->post('end_time'))),
			'venue_cost_usd' => $this->input->post('venue_cost_usd'),
			'venue_cost_pkr' => $this->input->post('venue_cost_pkr'),
			'venue_address' => $this->input->post('venue_address'),
			'venue_lat' => $this->input->post('venue_lat'),
			'venue_lng' => $this->input->post('venue_long'),
			'created_on' => date('Y-m-d H:i:s'),
		);

		$this->db->trans_start();
		$this->db
			->set($data)
			->insert('es_demonstration_venue');
		$insert_id = $this->db->insert_id();
		$this->db->trans_complete();

		$this->session->set_flashdata('message', 'Inventory venue has been created successfully');
		redirect($this->myparent);
	}



	function crd_edit() {
		$this->checkEditId();
		$this->load->view('includes/after_login/head');
		$this->load->view('demonstration_venue/edit');
	}

	function crd_edit_validate() {
		$this->checkEditId();

		$this->form_validation->set_rules('venue_title', 'venue_title*venue Title', 'trim|required');
		$this->form_validation->set_rules('venue_category', 'venue_category*Venue Category', 'trim|required');
		$this->form_validation->set_rules('start_time', 'start_time*Start Time', 'trim|required');
		$this->form_validation->set_rules('end_time', 'end_time*End Time', 'trim|required');
		$this->form_validation->set_rules('venue_cost_usd', 'venue_cost_usd*Venue Cost USD', 'trim|required|numeric');
		$this->form_validation->set_rules('venue_cost_pkr', 'venue_cost_pkr*Venue Cost PKR', 'trim|required|numeric');
		$this->form_validation->set_rules('venue_address', 'venue_address*Venue Address', 'trim|required');

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else {
			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function crd_edit_submit() {
		if ($this->crd_edit_validate() !== true)
			show_404();


		$data = array(
			'venue_title' => $this->input->post('venue_title'),
			'venue_category' => $this->input->post('venue_category'),
			'start_time' => date('H:i:s', strtotime($this->input->post('start_time'))),
			'end_time' => date('H:i:s', strtotime($this->input->post('end_time'))),
			'venue_cost_usd' => $this->input->post('venue_cost_usd'),
			'venue_cost_pkr' => $this->input->post('venue_cost_pkr'),
			'venue_address' => $this->input->post('venue_address'),
			'venue_lat' => $this->input->post('venue_lat'),
			'venue_lng' => $this->input->post('venue_long'),
		);

		$this->db->trans_start();
		$this->db
			->set($data)
			->where('id', $this->formdata->id)
			->update('es_demonstration_venue');

		$this->db->trans_complete();
		$this->session->set_flashdata('message', 'Inventory venue has been updated successfully');
		redirect($this->myparent);
	}


	function crd_delete() {
		$this->checkEditId();
		if (!ALLOW_DELETION)
			show_404();

		$this->db
			->where('id', $this->formdata->id)
			->update('es_demonstration_venue', array(
				'is_deleted' => 1,
				'deleted_by' => $this->userdata->id,
				'deleted_on' => date('Y-m-d H:i:s')
			));

		$this->session->set_flashdata('message', 'Inventory venue has been deleted successfully');
		redirect($this->myparent);
	}
}