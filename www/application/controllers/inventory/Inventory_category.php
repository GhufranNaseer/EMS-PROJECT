<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_category extends MY_Controller {
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
		$this->myparent = base_url('inventory-category.html');
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
			->get('es_inventory_category');
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();

	}

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('inventory_category/list');
	}


	function crd_list_datatable() {
		$this->load->library('datatables');
		$this->datatables
			->select('C.id,
				  C.category_title,
				  P.category_title as parent_title,
				  C.created_on', false)

			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'inventory-category-edit.html?id=' . urlencode(myid($id)) . '">Edit</a>';
				if (ALLOW_DELETION)
					$html .= ' | <a href="' . base_url() . 'inventory-category-delete.html?id=' . urlencode(myid($id)) . '" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';
				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where('C.is_deleted', 0)
			->join('es_inventory_category as P', 'C.parent_id = P.id', 'LEFT')
			->from('es_inventory_category as C');

		print ($this->datatables->generate());
	}


	function crd_add() {
		$this->load->view('includes/after_login/head');
		$this->load->view('inventory_category/add');
	}

	function crd_add_validate() {

		$this->form_validation->set_rules('category_title', 'category_title*Category Title', 'trim|required|is_unique[es_inventory_category.category_title]');
		$this->form_validation->set_rules('parent_category', 'parent_category*Parent category', 'trim');

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function crd_add_submit() {
		if ($this->crd_add_validate() !== true)
			show_404();


		$data = array(
			'category_title' => $this->input->post('category_title'),
			'created_on' => date('Y-m-d H:i:s'),
		);

		if ($this->input->post('parent_category')) {
			$data['parent_id'] = $this->input->post('parent_category');
		}

		$this->db->trans_start();
		$this->db
			->set($data)
			->insert('es_inventory_category');
		$insert_id = $this->db->insert_id();
		$this->db->trans_complete();

		$this->session->set_flashdata('message', 'Category has been created successfully');
		redirect($this->myparent);
	}



	function crd_edit() {
		$this->checkEditId();
		$this->load->view('includes/after_login/head');
		$this->load->view('inventory_category/edit');
	}

	function crd_edit_validate() {
		$this->checkEditId();

		$this->form_validation->set_rules('category_title', 'category_title*Category Title', 'trim|required|set_edit_mood[id.' . $this->formdata->id . ']|is_unique[es_inventory_category.category_title]');
		$this->form_validation->set_rules('parent_category', 'parent_category*Parent category', 'trim');

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
			'category_title' => $this->input->post('category_title'),
		);

		if ($this->input->post('parent_category')) {
			$data['parent_id'] = $this->input->post('parent_category');
		}

		$this->db->trans_start();
		$this->db
			->set($data)
			->where('id', $this->formdata->id)
			->update('es_inventory_category');

		$this->db->trans_complete();
		$this->session->set_flashdata('message', 'Category has been updated successfully');
		redirect($this->myparent);
	}


	function crd_delete() {
		$this->checkEditId();
		if (!ALLOW_DELETION)
			show_404();

		$this->db
			->where('id', $this->formdata->id)
			->update('es_inventory_category', array(
				'is_deleted' => 1,
				'deleted_by' => $this->userdata->id,
				'deleted_on' => date('Y-m-d H:i:s')
			));

		$this->session->set_flashdata('message', 'Category has been deleted successfully');
		redirect($this->myparent);
	}
}