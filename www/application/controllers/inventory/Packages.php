<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Packages extends MY_Controller {
	protected function rule() {
		$this->activateRightsSystem();
		$crd = array(
			'crd_event_list,
			crd_list,
			copy_other_event_packages,
			crd_add,
			crd_add_submit' => array(
				'rule' => '@'
			),
			'crd_list_datatable,
			crd_event_list_datatable,
			ajax_get_inventory_items,
			get_inventory_category,
			crd_add_validate,
			get_package_items' => array(
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
		$this->myparent = base_url('packages.html');
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
			->get('es_exhibitions');
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();

	}

	function crd_event_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('packages/event_list');
	}

	function crd_event_list_datatable() {
		$this->load->library('datatables');
		$this->datatables
			->select('E.id,
				  E.exhibition_title,
				  L.location_title				  
				  ', false)
			->add_column('total_items', function ($row) {
				return $this->db
					->where('exhibition_id', $row['id'])
					->where('is_active', 1)
					->where('is_deleted', 0)
					->count_all_results('es_packages');
			}, NULL)
			->add_column('col_action', function ($row) {
				$id = $row['id'];

				$html = '<a href="' . base_url() . 'packages.html?id=' . urlencode(myid($id)) . '">View Packages</a>';

				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where('E.is_deleted', 0)
			->join('es_locations as L', 'E.location_id = L.id', 'LEFT')
			->from('es_exhibitions as E');

		print ($this->datatables->generate());
	}

	function crd_list() {
		$this->checkEditId();

		$this->load->view('includes/after_login/head');
		$this->load->view('packages/list');
	}


	function crd_list_datatable() {
		$this->checkEditId();

		$this->load->library('datatables');
		$this->datatables
			->select('id,
				  exhibition_id,
				  package_title,
				  CONCAT("<i class=\'fa fa-magic package_icon\' /></i>") as package_icon,
				  UCASE(package_type),
				  IF(is_active = 1, "<span class=\'label label-success\'>Active</span>", "<span class=\'label label-warning\'>InActive</span>")
				  ', false)

			->unset_column('id')
			->unset_column('exhibition_id')
			->add_column('col_action', function ($row) {
				$id = $row['exhibition_id'];
				$package_id = $row['id'];
				$html = '<a href="' . base_url() . 'packages-edit.html?id=' . urlencode(myid($id)) . '&package_id='.urlencode(myid($package_id)).'">Edit</a>';
				if (ALLOW_DELETION)
					$html .= ' | <a href="' . base_url() . 'packages-delete.html?id=' . urlencode(myid($id)) . '&package_id='.urlencode(myid($package_id)).'" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';
				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where('exhibition_id', $this->formdata->id)
			->where('is_deleted', 0)
			->from('es_packages');

		print ($this->datatables->generate());
	}


	function crd_add() {
		$this->checkEditId();

		$this->load->view('includes/after_login/head');
		$this->load->view('packages/add');
	}

	function crd_add_validate() {
		$this->checkEditId();

		$this->form_validation->set_rules('package_title', 'package_title*Package title', 'trim|required');
		$this->form_validation->set_rules('package_type', 'package_type*Package Type', 'trim|required');
		$this->form_validation->set_rules('package_badges', 'package_badges*package badges', 'trim|required|numeric');
		$this->form_validation->set_rules('inventory[]', 'inventory[]*inventory items', 'trim|required');

		if ($this->input->post('package_type') && $this->input->post('package_type') == 'shell') {
			$this->form_validation->set_rules('package_price_usd', 'package_price_usd*Package Shell Cost', 'trim|required|numeric');
			$this->form_validation->set_rules('package_price_pkr', 'package_price_pkr*Package Shell Cost', 'trim|required|numeric');
		}

		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		}
		else {
			$duplicate_check = array();
			foreach ($this->input->post('inventory') as $key => $value) {
				if (!array_key_exists('category', $value) || trim($value['category']) == '') {
					return $this->common->doError(func_num_args(), "inventory[".$key."][category]*Inventory category field is required");
				}
				else if (!array_key_exists('item', $value) || trim($value['item']) == '') {
					return $this->common->doError(func_num_args(), "inventory[".$key."][item]*Inventory item field is required");
				}
				else if (in_array($value['item'], $duplicate_check)) {
					return $this->common->doError(func_num_args(), "inventory[".$key."][item]*Inventory item is already used");
				}
				else if (!array_key_exists('initial_allow', $value) || trim($value['initial_allow']) == '') {
					return $this->common->doError(func_num_args(), "inventory[".$key."][initial_allow]*Inventory initial_allow field is required");
				}
				else if ($value['initial_allow'] < 1) {
					return $this->common->doError(func_num_args(), "inventory[".$key."][initial_allow]*Inventory initial_allow field is required");
				}

				$item_data = $this->db
					->where('id', $value['item'])
					->get('es_inventory_item')
					->row();

				if ($value['initial_allow'] > $item_data->item_stock) {
					return $this->common->doError(func_num_args(), "inventory[".$key."][initial_allow]*Inventory initial allow amount cannot be greater then " . $item_data->item_stock);
				}

				$duplicate_check[] = $value['item'];
			}

			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function crd_add_submit() {
		if ($this->crd_add_validate() !== true)
			show_404();

		$data = array(
			'exhibition_id' => $this->formdata->id,
			'package_title' => $this->input->post('package_title'),
			'package_type' => $this->input->post('package_type'),
			'package_badges' => $this->input->post('package_badges'),
			'visitor_badges'=> $this->input->post('visitor_badges'),
			'created_on' => date('Y-m-d H:i:s'),
		);

		if ($this->input->post('package_type') && $this->input->post('package_type') == 'shell') {
			$data['package_price_usd'] = $this->input->post('package_price_usd');
			$data['package_price_pkr'] = $this->input->post('package_price_pkr');
		}

		$this->db
			->set($data)
			->insert('es_packages');
		$insert_id = $this->db->insert_id();


		$items_data = array();
		foreach ($this->input->post('inventory') as $item) {
			$items_data[] = array(
				'exhibition_id' => $this->formdata->id,
				'package_id' => $insert_id,
				'item_id' => $item['item'],
				'initial_allow' => $item['initial_allow']
			);
		}
		if (count($items_data) > 0) {
			$this->db->insert_batch('es_package_items', $items_data);
		}


		if ($this->input->post('badges')) {
			$badge_data = array();
			foreach ($this->input->post('badges') as $key => $item) {
				$badge_data[] = array(
					'exhibition_id' => $this->formdata->id,
					'package_id' => $insert_id,
					'badge_type' => $item['badge_type'],
					'invitation_type' => trim($key),
					'quantity' => (array_key_exists('has_item', $item) ? $item['quantity'] : 0),
					'is_active' => (array_key_exists('has_item', $item) ? 1 : 0),
				);
			}
			if (count($badge_data) > 0) {
				$this->db->insert_batch('es_package_badges', $badge_data);
			}
		}


		$this->session->set_flashdata('message', 'Package has been created successfully');
		redirect($this->myparent . "?id=" . myid($this->formdata->id));
	}



	function crd_edit() {
		$this->checkEditId();

		$package_id = $this->input->get('package_id');

		$this->packagedata = $this->db
			->where(mycolumn(), $package_id)
			->get('es_packages')
			->row();

		$this->load->view('includes/after_login/head');
		$this->load->view('packages/edit');
	}

	function crd_edit_validate() {
		$this->checkEditId();

		$this->form_validation->set_rules('package_title', 'package_title*Package title', 'trim|required');
		$this->form_validation->set_rules('package_type', 'package_type*Package Type', 'trim|required');
		$this->form_validation->set_rules('package_badges', 'package_badges*package badges', 'trim|required|numeric');
		$this->form_validation->set_rules('inventory[]', 'inventory[]*Inventory', 'trim|required');

		if ($this->input->post('package_type') && $this->input->post('package_type') == 'shell') {
			$this->form_validation->set_rules('package_price_usd', 'package_price_usd*Package Shell Cost', 'trim|required|numeric');
			$this->form_validation->set_rules('package_price_pkr', 'package_price_pkr*Package Shell Cost', 'trim|required|numeric');
		}

		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		}
		else {
			$duplicate_check = array();
			foreach ($this->input->post('inventory') as $key => $value) {
				if (!array_key_exists('category', $value) || trim($value['category']) == '') {
					return $this->common->doError(func_num_args(), "inventory[".$key."][category]*Inventory category field is required");
				}
				else if (!array_key_exists('item', $value) || trim($value['item']) == '') {
					return $this->common->doError(func_num_args(), "inventory[".$key."][item]*Inventory item field is required");
				}
				else if (in_array($value['item'], $duplicate_check)) {
					return $this->common->doError(func_num_args(), "inventory[".$key."][item]*Inventory item is already used");
				}
				else if (!array_key_exists('initial_allow', $value) || trim($value['initial_allow']) == '') {
					return $this->common->doError(func_num_args(), "inventory[".$key."][initial_allow]*Inventory initial_allow field is required");
				}
				else if ($value['initial_allow'] < 1) {
					return $this->common->doError(func_num_args(), "inventory[".$key."][initial_allow]*Inventory initial_allow field is required");
				}

				$item_data = $this->db
					->where('id', $value['item'])
					->get('es_inventory_item')
					->row();

				if ($value['initial_allow'] > $item_data->item_stock) {
					return $this->common->doError(func_num_args(), "inventory[".$key."][initial_allow]*Inventory initial allow amount cannot be greater then inventory stock " . $item_data->item_stock);
				}

				$duplicate_check[] = $value['item'];
			}

			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function crd_edit_submit() {
		if ($this->crd_edit_validate() !== true)
			show_404();

		$package_id = $this->input->get('package_id');
		if (is_null($package_id))
			show_404();

		$this->packagedata = $this->db
			->where(mycolumn(), $package_id)
			->get('es_packages')
			->row();

		if (!$this->packagedata)
			show_404();

		$isActive = (is_null($this->input->post('active')) ? 0 : 1);

		$data = array(
			'exhibition_id' => $this->formdata->id,
			'package_title' => $this->input->post('package_title'),
			'package_type' => $this->input->post('package_type'),
			'package_badges' => $this->input->post('package_badges'),
			'visitor_badges' => $this->input->post('visitor_badges'),
			'is_active' => $isActive
		);

		if ($this->input->post('package_type') && $this->input->post('package_type') == 'shell') {
			$data['package_price_usd'] = $this->input->post('package_price_usd');
			$data['package_price_pkr'] = $this->input->post('package_price_pkr');
		}

		$this->db
			->set($data)
			->where('id', $this->packagedata->id)
			->update('es_packages');

		$this->db
			->where('package_id', $this->packagedata->id)
			->delete('es_package_items');

		$items_data = array();
		foreach ($this->input->post('inventory') as $item) {
			$items_data[] = array(
				'exhibition_id' => $this->formdata->id,
				'package_id' => $this->packagedata->id,
				'item_id' => $item['item'],
				'initial_allow' => $item['initial_allow']
			);
		}
		if (count($items_data) > 0) {
			$this->db->insert_batch('es_package_items', $items_data);
		}


		if ($this->input->post('badges')) {

			$this->db
				->where('package_id', $this->packagedata->id)
				->delete('es_package_badges');

			$badge_data = array();
			foreach ($this->input->post('badges') as $key => $item) {
				$badge_data[] = array(
					'exhibition_id' => $this->formdata->id,
					'package_id' => $this->packagedata->id,
					'badge_type' => $item['badge_type'],
					'invitation_type' => trim($key),
					'quantity' => (array_key_exists('has_item', $item) ? $item['quantity'] : 0),
					'is_active' => (array_key_exists('has_item', $item) ? 1 : 0),
				);
			}
			if (count($badge_data) > 0) {
				$this->db->insert_batch('es_package_badges', $badge_data);
			}
		}


		$this->session->set_flashdata('message', 'Package has been updated successfully');
		redirect($this->myparent . "?id=" . myid($this->formdata->id));
	}


	function crd_delete() {
		$this->checkEditId();
		if (!ALLOW_DELETION)
			show_404();

		$package_id = $this->input->get('package_id');

		if (is_null($package_id)) {
			show_404();
		}

		$this->packagedata = $this->db
			->where(mycolumn(), $package_id)
			->get('es_packages')
			->row();

		$check_booking = $this->db
			->where('package_id', $this->packagedata->id)
			->where('is_active', 1)
			->count_all_results('es_exhibition_booking_items');

		if ($check_booking > 0) {
			$this->session->set_flashdata('error', 'Package cannot be deleted, because its already used in booking');
			redirect($this->myparent . "?id=" . myid($this->formdata->id));
			die();
		}

		$this->db
			->where('id', $this->packagedata->id)
			->update('es_packages', array(
				'is_deleted' => 1,
				'deleted_by' => $this->userdata->id,
				'deleted_on' => date('Y-m-d H:i:s')
			));
		$this->session->set_flashdata('message', 'Package has been deleted successfully');
		redirect($this->myparent . "?id=" . myid($this->formdata->id));
	}

	function copy_other_event_packages() {
		$this->checkEditId();

		$event_id = $this->input->post('event_id');
		if (is_null($event_id) || $event_id == '')
			show_404();

		$packages = $this->db
			->where('exhibition_id', $event_id)
			->get('es_packages')
			->result();

		foreach ($packages as $package) {
			$data = array(
				'exhibition_id' => $this->formdata->id,
				'package_title' => $package->package_title,
				'package_type' => $package->package_type,
				'package_badges' => $package->package_badges,
				'created_on' => date('Y-m-d H:i:s'),
			);

			if ($package->package_type == 'shell') {
				$data['package_price_usd'] = $package->package_price_usd;
				$data['package_price_pkr'] = $package->package_price_pkr;
			}

			$this->db
				->set($data)
				->insert('es_packages');
			$new_package_id = $this->db->insert_id();


			$items = $this->db
				->where('package_id', $package->id)
				->get('es_package_items')
				->result();

			$items_data = array();
			foreach ($items as $item) {
				$items_data[] = array(
					'exhibition_id' => $this->formdata->id,
					'package_id' => $new_package_id,
					'item_id' => $item->item_id,
					'initial_allow' => $item->initial_allow
				);
			}
			if (count($items_data) > 0) {
				$this->db->insert_batch('es_package_items', $items_data);
			}

			$badges = $this->db
				->where('package_id', $package->id)
				->get('es_package_badges')
				->result();
			$badges_data = array();
			foreach ($badges as $badge) {
				$badges_data[] = array(
					'exhibition_id' => $this->formdata->id,
					'package_id' => $new_package_id,
					'badge_type' => $badge->badge_type,
					'invitation_type' => trim($badge->invitation_type),
					'quantity' => $badge->quantity,
					'is_active' => $badge->is_active,
				);
			}
			if (count($badges_data) > 0) {
				$this->db->insert_batch('es_package_badges', $badges_data);
			}
		}

		redirect($this->myparent . "?id=" . myid($this->formdata->id));
	}

	function get_package_items() {
		$package_id = $this->input->post('package_id');
		if (is_null($package_id)) {
			echo json_encode(array(
				'error' => 1,
				'message' => 'package_id is required'
			));
			exit;
		}

		$items = $this->db
			->select('P.*, I.item_title, I.category_id, C.category_title')
			->where('P.package_id', $package_id)
			->join('es_inventory_item as I', 'I.id = P.item_id', 'LEFT')
			->join('es_inventory_category as C', 'C.id = I.category_id', 'LEFT')
			->get('es_package_items as P')
			->result();

		echo json_encode(array(
			'error' => 0,
			'data' => $items
		));
		exit;
	}

	function ajax_get_inventory_items() {
		$category = $this->input->post('category');
		$exhibition_id = $this->input->post('exhibition_id');
		if (is_null($category) || is_null($exhibition_id)) {
			echo json_encode(array(
				'error' => 1,
				'message' => 'Required parameters is missing'
			));
			exit;
		}

		$items = $this->db
			->where('exhibition_id', $exhibition_id)
			->where('category_id', $category)
			->where('is_active', 1)
			->where('is_deleted', 0)
			->get('es_inventory_item')
			->result();

		if (!$items) {
			echo json_encode(array(
				'error' => 1,
				'message' => 'No items available for this category'
			));
		} else {
			echo json_encode(array(
				'error' => 0,
				'data' => $items
			));
		}
	}

	function get_inventory_category() {
		$parent_id = $this->input->post('parent_id');

		if ($parent_id && !is_null($parent_id)) {
			$this->db->where('parent_id', $parent_id);
		}
		$categories = $this->db
			->where('is_deleted', 0)
			->get('es_inventory_category')
			->result();

		if (isset($categories) && count($categories) > 0) {
			$html = '<option value="">- select -</option>';
			foreach ($categories as $category) {
				$html .= '<option value="'.$category->id.'">'.$category->category_title.'</option>';
			}
			echo $html;
		} else {
			echo null;
		}
		exit;
	}
}