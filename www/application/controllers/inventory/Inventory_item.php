<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_item extends MY_Controller {
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
		$this->myparent = base_url('inventory-item.html');
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
			->get('es_inventory_item_global');
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();

	}

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('inventory_item/list');
	}


	function crd_list_datatable() {
		$this->load->library('datatables');
		$this->datatables
			->select('I.id,
				  I.item_title,
				  C.category_title,
				  I.item_description,
				  I.item_stock,
				  I.item_image,
				  ', false)

			->unset_column('I.item_image')
			->add_column('stock_sold', function ($row) {
				$id = $row['id'];
				$event_used_stock = $this->db
					->select('SUM(BI.quantity) as total_quantity')
					->where('I.global_item_id', $id)
					->join('es_inventory_item as I', 'I.id = BI.item_id', 'LEFT')
					->get('es_exhibition_booking_items as BI')
					->row();

				$event_used_stock = ($event_used_stock) ? $event_used_stock->total_quantity : 0;

				return $event_used_stock;
			}, NULL)

			->add_column('stock_remaining', function ($row) {
				$id = $row['id'];
				$event_used_stock = $this->db
					->select('SUM(BI.quantity) as total_quantity')
					->where('I.global_item_id', $id)
					->join('es_inventory_item as I', 'I.id = BI.item_id', 'LEFT')
					->get('es_exhibition_booking_items as BI')
					->row();

				$event_used_stock = ($event_used_stock) ? $event_used_stock->total_quantity : 0;

				return $row['item_stock'] - $event_used_stock;
			}, NULL)

			->add_column('item_image_link', function ($row) {
				$image = $row['item_image'];

				$html = '<a href="'.base_url($image).'" target="_blank">View</a>';
				return $html;
			}, NULL)
			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'inventory-item-edit.html?id=' . urlencode(myid($id)) . '">Edit</a>';
				if (ALLOW_DELETION)
					$html .= ' | <a href="' . base_url() . 'inventory-item-delete.html?id=' . urlencode(myid($id)) . '" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';
				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where('I.is_deleted', 0)
			->join('es_inventory_category as C', 'C.id = I.category_id', 'LEFT')
			->from('es_inventory_item_global as I');

		print ($this->datatables->generate());
	}


	function crd_add() {
		$this->load->view('includes/after_login/head');
		$this->load->view('inventory_item/add');
	}

	function crd_add_validate() {

		$this->form_validation->set_rules('item_title', 'item_title*item Title', 'trim|required|is_unique[es_inventory_item_global.item_title]');
		$this->form_validation->set_rules('category_id', 'category_id*Item category', 'trim|required');
		$this->form_validation->set_rules('item_image[0]', 'item_image[0]*Item image', 'trim');
		$this->form_validation->set_rules('item_stock', 'item_stock*Item stock', 'trim|required|numeric');
		$this->form_validation->set_rules('item_price_usd', 'item_price_usd*Item price usd', 'trim|required|numeric');
		$this->form_validation->set_rules('item_price_pkr', 'item_price_pkr*Item price pkr', 'trim|required|numeric');
		$this->form_validation->set_rules('item_description', 'item_description*Item description', 'trim');
		$this->form_validation->set_rules('item_terms', 'item_terms*Item terms', 'trim');

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function crd_add_submit() {
		if ($this->crd_add_validate() !== true)
			show_404();


		$data = array(
			'category_id' => $this->input->post('category_id'),
			'item_title' => $this->input->post('item_title'),
			'item_image' => $this->funcs->make_image_string($this->input->post('item_image')),
			'item_stock' => $this->input->post('item_stock'),
			'item_price_usd' => $this->input->post('item_price_usd'),
			'item_price_pkr' => $this->input->post('item_price_pkr'),
			'item_description' => $this->input->post('item_description'),
			'item_terms' => $this->input->post('item_terms'),
			'created_on' => date('Y-m-d H:i:s'),
		);

		$this->db->trans_start();
		$this->db
			->set($data)
			->insert('es_inventory_item_global');
		$insert_id = $this->db->insert_id();
		$this->db->trans_complete();

		$this->session->set_flashdata('message', 'Item has been created successfully');
		redirect($this->myparent);
	}



	function crd_edit() {
		$this->checkEditId();
		$this->load->view('includes/after_login/head');
		$this->load->view('inventory_item/edit');
	}

	function crd_edit_validate() {
		$this->checkEditId();

		$this->form_validation->set_rules('item_title', 'item_title*item Title', 'trim|required|set_edit_mood[id.' . $this->formdata->id . ']|is_unique[es_inventory_item_global.item_title]');
		$this->form_validation->set_rules('category_id', 'category_id*Item category', 'trim|required');
		$this->form_validation->set_rules('item_image[0]', 'item_image[0]*Item image', 'trim');
		$this->form_validation->set_rules('item_stock', 'item_stock*Item stock', 'trim|required|numeric');
		$this->form_validation->set_rules('item_price_usd', 'item_price_usd*Item price usd', 'trim|required|numeric');
		$this->form_validation->set_rules('item_price_pkr', 'item_price_pkr*Item price pkr', 'trim|required|numeric');
		$this->form_validation->set_rules('item_description', 'item_description*Item description', 'trim');
		$this->form_validation->set_rules('item_terms', 'item_terms*Item terms', 'trim');

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else {

			if ($this->input->post('item_stock')) {
				$stock = $this->input->post('item_stock');

				$event_stock = $this->db
					->select('SUM(item_stock) as total_stock')
					->where('global_item_id', $this->formdata->id)
					->where('is_active', 1)
					->where('is_deleted', 0)
					->get('es_inventory_item')
					->row();

				if ($event_stock && $event_stock->total_stock > $stock) {
					return $this->common->doError(func_num_args(), 'Stock amount cannot be less then ' . $event_stock->total_stock);
				}

			}

			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function crd_edit_submit() {
		if ($this->crd_edit_validate() !== true)
			show_404();


		$data = array(
			'category_id' => $this->input->post('category_id'),
			'item_title' => $this->input->post('item_title'),
			'item_image' => $this->funcs->make_image_string($this->input->post('item_image')),
			'item_stock' => $this->input->post('item_stock'),
			'item_price_usd' => $this->input->post('item_price_usd'),
			'item_price_pkr' => $this->input->post('item_price_pkr'),
			'item_description' => $this->input->post('item_description'),
			'item_terms' => $this->input->post('item_terms'),
		);


		$this->db->trans_start();
		$this->db
			->set($data)
			->where('id', $this->formdata->id)
			->update('es_inventory_item_global');

		$this->db->trans_complete();
		$this->session->set_flashdata('message', 'Item has been updated successfully');
		redirect($this->myparent);
	}


	function crd_delete() {
		$this->checkEditId();
		if (!ALLOW_DELETION)
			show_404();

		$check_event_item = $this->db
			->where('global_item_id', $this->formdata->id)
			->where('is_active', 1)
			->where('is_deleted', 0)
			->count_all_results('es_inventory_item');

		if ($check_event_item > 0) {
			$this->session->set_flashdata('error', 'Item cannot be deleted, because its using in event');
			redirect($this->myparent);
			die();
		}

		$this->db
			->where('id', $this->formdata->id)
			->update('es_inventory_item_global', array(
				'is_deleted' => 1,
				'deleted_by' => $this->userdata->id,
				'deleted_on' => date('Y-m-d H:i:s')
			));

		$this->session->set_flashdata('message', 'Item has been deleted successfully');
		redirect($this->myparent);
	}
}