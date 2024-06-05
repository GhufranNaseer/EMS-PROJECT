<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Event_inventory extends MY_Controller {
	protected function rule() {
		$this->activateRightsSystem();
		$crd = array(
			'crd_list,
			crd_view,
			crd_view_submit' => array(
				'rule' => '@'
			),
			'crd_list_datatable,
			crd_view_validate,
			' => array(
				'rule' => '@',
				'ajaxOnly' => true
			)
		);
		$this->load->model('usermdl');
		$this->myparent = base_url('event-inventory.html');
		return array_merge($crd);
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
			->get('es_exhibitions');
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();

	}

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('event_inventory/list');
	}

	function crd_list_datatable() {
		$this->load->library('datatables');
		$this->datatables
			->select('E.id,
				  E.exhibition_title,
				  E.booking_expire_date as booking_expire_date,
				  L.location_title				  
				  ', false)
			->unset_column('booking_expire_date')
			->add_column('total_items', function ($row) {
				return $this->db
					->where('exhibition_id', $row['id'])
					->where('is_active', 1)
					->where('is_deleted', 0)
					->count_all_results('es_inventory_item');
			}, NULL)
			->add_column('col_action', function ($row) {
				$id = $row['id'];
				// if (strtotime($row['booking_expire_date'] . ' 23:59:59') < strtotime(date('Y-m-d H:i:s'))) {
				// 	return "<div class='text-center'><span class='label label-danger'>Expired</span></div>";
				// }
				$total_items = $this->db
					->where('exhibition_id', $row['id'])
					->where('is_active', 1)
					->where('is_deleted', 0)
					->count_all_results('es_inventory_item');
				$html = '';

				if ($total_items > 0) {
					$html = '<a href="' . base_url() . 'event-inventory-view.html?id=' . urlencode(myid($id)) . '">View Inventory</a>';
				} else {
					$html = '<a href="' . base_url() . 'event-inventory-view.html?id=' . urlencode(myid($id)) . '">Create Inventory</a>';
				}
				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where('E.is_deleted', 0)
			->join('es_locations as L', 'E.location_id = L.id', 'LEFT')
			->from('es_exhibitions as E');

		print ($this->datatables->generate());
	}



	function crd_view() {
		$this->checkEditId();

		$items = $this->db
			->select('I.*, C.category_title')
			->where('I.is_deleted', 0)
			->join('es_inventory_category as C', 'C.id = I.category_id', 'LEFT')
			->get('es_inventory_item_global as I')
			->result();

		$this->load->view('includes/after_login/head');
		$this->load->view('event_inventory/view', array(
			'items' => $items
		));
	}

	function crd_view_validate(){
		$this->checkEditId();

		$this->form_validation->set_rules('items[]', 'items[]*inventory items', 'trim|required');

		// $expired_event_ids = array();
		// $expired_events = $this->db
		// 	->select('id')
		// 	->where('is_deleted' , 0)
		// 	->where('booking_expire_date <', date('Y-m-d'))
		// 	->get('es_exhibitions')
		// 	->result();
		// foreach ($expired_events as $key => $value) {
		// 	$expired_event_ids[] = $value->id;
		// }

		if ($this->input->post('items')) {
			$exhibition_id = $this->formdata->id;
			foreach ($this->input->post('items') as $key => $item) {
				if (isset($item['is_active']) && !is_null($item['is_active'])) {
					$stock = $item['stock'];

					$global_item = $this->db
						->where('id', $item['global_item_id'])
						->get('es_inventory_item_global')
						->row();

					// Test 1: check stock amount is available in global stock
					// $used_stock = $this->db
					// 	->select('SUM(item_stock) as used_stock')
					// 	->where('global_item_id', $item['global_item_id'])
					// 	->where_not_in('exhibition_id', $expired_event_ids)
					// 	->where('exhibition_id !=', $exhibition_id)
					// 	->where('is_active', 1)
					// 	->where('is_deleted', 0)
					// 	->get('es_inventory_item')
					// 	->row();
					// $remaining = $global_item->item_stock - $used_stock->used_stock;
					$remaining = $global_item->item_stock;

					if ($stock > $remaining) {
						return $this->common->doError(func_num_args(), 'items['.$key.'][stock]*Stock not available for ' . $global_item->item_title. ', Minimum '.$remaining.' stock available!');
					}


					// Test 2: check stock amount must be greater then already used event stock
					$booking_data = $this->db
						->where('exhibition_id', $this->formdata->id)
						->where('global_item_id', $item['global_item_id'])
						->get('es_inventory_item')
						->row();

					if ($booking_data) {
						$event_used_stock = $this->db
							->select('SUM(quantity) as total_quantity')
							->where('exhibition_id', $this->formdata->id)
							->where('item_id', $booking_data->id)
							->group_by('item_id')
							->get('es_exhibition_booking_items')
							->row();

						$event_used_stock = ($event_used_stock) ? $event_used_stock->total_quantity : 0;

						if ($event_used_stock > $stock) {
							return $this->common->doError(func_num_args(), 'items['.$key.'][stock]*Stock amount of '.$global_item->item_title.' must be greater then ' . $event_used_stock);
						}
					}
				}
			}
		}

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);

	}

    function crd_view_submit(){
        $this->checkEditId();

        $exhibition_id = $this->formdata->id;


        foreach ($this->input->post('items') as $item) {
        	$item_data = $this->db
				->where('id', $item['global_item_id'])
				->get('es_inventory_item_global')
				->row();

			$is_active = (is_null($item['is_active']) ? 0 : 1);

        	$data = array(
        		'exhibition_id' => $exhibition_id,
				'category_id' => $item_data->category_id,
				'global_item_id' => $item_data->id,
				'item_title' => $item_data->item_title,
				'item_image' => $item_data->item_image,
				'item_stock' => $item['stock'],
				'item_price_usd' => $item['price_usd'],
				'item_price_pkr' => $item['price_pkr'],
				'is_active' => $is_active,
				'created_on' => date('Y-m-d H:i:s')
			);

        	if ($item['is_old_item'] == 1) {
				$this->db
					->where('exhibition_id', $this->formdata->id)
					->where('global_item_id', $item_data->id)
					->update('es_inventory_item', $data);
			} else {
				$this->db->insert('es_inventory_item', $data);
			}

		}


        $this->session->set_flashdata('message', 'Inventory has been updated successfully');
        redirect($this->myparent);

    }

}