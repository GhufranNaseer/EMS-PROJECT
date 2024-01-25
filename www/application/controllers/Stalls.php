<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stalls extends MY_Controller {
	protected function rule() {
		$this->activateRightsSystem();
		$crd = array(
			'crd_list,
			stall_list,
			crd_add,
			crd_add_submit,
			crd_edit,
			crd_edit_submit,
			crd_delete' => array(
				'rule' => '@'
			),
			'crd_list_datatable,
			stall_list_datatable,
			crd_add_validate,
			crd_edit_validate
			' => array(
				'rule' => '@',
				'ajaxOnly' => true
			)
		);
		$this->load->model('usermdl');
		$this->myparent = base_url('stalls.html');
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
		$this->load->view('stalls/list');
	}

	function crd_list_datatable() {
		$this->load->library('datatables');
		$this->datatables
			->select('E.id,
				  E.exhibition_title,
				  L.location_title				  
				  ', false)
			->add_column('total_stalls', function ($row) {
				return $this->db
					->where('exhibition_id', $row['id'])
					->count_all_results('es_exhibition_stalls');
			}, NULL)
			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'stalls-list.html?id=' . urlencode(myid($id)) . '">View Stalls</a>';

				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where('E.is_deleted', 0)
			->join('es_locations as L', 'E.location_id = L.id', 'LEFT')
			->from('es_exhibitions as E');

		print ($this->datatables->generate());
	}


	function stall_list() {
		$exhibition_id = $this->input->get('id');
		if (!isset($exhibition_id)) {
			show_404();
		}

		$this->load->view('includes/after_login/head');
		$this->load->view('stalls/stall_list');
	}

	function stall_list_datatable() {
		$exhibition_id = $this->input->get('id');

		$this->load->library('datatables');
		$this->datatables
			->select('S.id,
				  S.exhibition_id,
				  S.stall_name,
				  CONCAT("<a href=\''.base_url().'", S.stall_map, "\' target=\'_blank\'>Map Image</a>") as stall_map,
				  CONCAT(S.stall_size, " ", S.stall_size_unit) as stall_size,
				  IF(S.is_sold = 1, "<span class=\'label label-danger\'>Confirm Sale</span>",
				  IF(S.is_booked = 1, "<span class=\'label label-warning\'>Tentative</span>", "<span class=\'label label-success\'>Available</span>")) as stall_status,
				  UCASE(S.stall_category),
				  S.stall_price_usd,
				  S.stall_price_pkr,
				  S.is_booked,
				  S.is_sold,
				  ', false)

			->unset_column('S.exhibition_id')
			->unset_column('S.is_booked')
			->unset_column('S.is_sold')
			->add_column('col_action', function ($row) {
				$id = $row['exhibition_id'];
				$stall_id = $row['id'];
				$html = '<a href="' . base_url() . 'stalls-edit.html?id=' . urlencode(myid($id)) . '&stall_id=' . urlencode(myid($stall_id)) . '">Edit</a>';
				if (ALLOW_DELETION && $row['is_booked'] == 0)
					$html .= ' | <a href="' . base_url() . 'stalls-delete.html?id=' . urlencode(myid($id)) . '&stall_id=' . urlencode(myid($stall_id)) . '" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';

				if ($row['is_sold'] == 1 || $row['is_booked'] == 1) {
					$html = '';
				}
				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where(mycolumn('S.exhibition_id'), $exhibition_id)
			->where('S.is_deleted', 0)
			->from('es_exhibition_stalls as S');

		print ($this->datatables->generate());
	}


	function crd_add() {
		$this->checkEditId();

		$this->load->view('includes/after_login/head');
		$this->load->view('stalls/add');
	}

	function crd_add_validate() {
		$this->checkEditId();

		$this->form_validation->set_rules('stall_prefix', 'stall_prefix*stall prefix', 'trim|required');
		$this->form_validation->set_rules('stalls_start', 'stalls_start*stalls start', 'trim|required|numeric');
		$this->form_validation->set_rules('stalls_end', 'stalls_end*stalls end', 'trim|required|numeric');
		if ($this->input->post('stalls_start') < 1 || $this->input->post('stalls_end') < 1) {
			return $this->common->doError(func_num_args(), "Atleast one stall is required");
		}
		if ($this->input->post('stalls_start') > $this->input->post('stalls_end')) {
			return $this->common->doError(func_num_args(), "stalls_start*Start range cannot be greater then end range");
		}


		$this->form_validation->set_rules('stall_price_usd', 'stall_price_usd*stall price usd', 'trim|required|numeric');
		$this->form_validation->set_rules('stall_price_pkr', 'stall_price_pkr*stall price pkr', 'trim|required|numeric');
		$this->form_validation->set_rules('stall_size', 'stall_size*stall size', 'trim|required|numeric');
		$this->form_validation->set_rules('stall_category', 'stall_category*stall category', 'trim|required');
		$this->form_validation->set_rules('hall_id', 'hall_id*exhibition hall', 'trim|required');
		$this->form_validation->set_rules('location_in_hall', 'location_in_hall*location in hall', 'trim');
		$this->form_validation->set_rules('stall_description', 'stall_description*stall description', 'trim');
		$this->form_validation->set_rules('stall_map[0]', 'stall_map[0]*stall map', 'trim');

		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		}
		else {
			$exhibition_id = $this->formdata->id;
			$stall_prefix = $this->input->post('stall_prefix');
			$stalls_start = $this->input->post('stalls_start');
			$stalls_start = (int)$stalls_start;
			$stalls_end = $this->input->post('stalls_end');
			$stalls_end = (int)$stalls_end;

			$stall_names = array();
			for ($i=$stalls_start; $i<=$stalls_end; $i++) {
				$stall_name = $stall_prefix . $i;
				$stall_names[] = $stall_name;
			}

			$check = $this->db
				->where('exhibition_id', $exhibition_id)
				->where('is_deleted', 0)
				->where_in('stall_name', $stall_names)
				->count_all_results('es_exhibition_stalls');

			if ($check > 0) {
				return $this->common->doError(func_num_args(), 'Some stalls are already exists in database');
			}

			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function crd_add_submit() {
		if ($this->crd_add_validate() !== true)
			show_404();

		$exhibition_id = $this->formdata->id;
		$stall_prefix = $this->input->post('stall_prefix');
		$stalls_start = $this->input->post('stalls_start');
		$stalls_start = (int)$stalls_start;
		$stalls_end = $this->input->post('stalls_end');
		$stalls_end = (int)$stalls_end;

		/*echo '<pre>';
		print_r($exhibition_id);
		print_r($this->input->post());
		die();*/

		// stall data
		$group_id = null;
		for ($i=$stalls_start; $i<=$stalls_end; $i++) {
			$stall_data = array(
				'exhibition_id' => $exhibition_id,
				'stall_name' => $stall_prefix . $i,
				'stall_size' => $this->input->post('stall_size'),
				'stall_size_unit' => 'sqm',
				'stall_category' => $this->input->post('stall_category'),
				'stall_price_usd' => $this->input->post('stall_price_usd'),
				'stall_price_pkr' => $this->input->post('stall_price_pkr'),
				'hall_id' => $this->input->post('hall_id'),
				'location_in_hall' => $this->input->post('location_in_hall'),
				'description' => $this->input->post('stall_description'),
				'created_on' => date('Y-m-d H:i:s'),
			);
			if ($this->input->post('stall_map')) {
				$stall_data['stall_map'] = $this->funcs->make_image_string($this->input->post('stall_map'));
			}

			if (!is_null($group_id)) {
				$stall_data['stall_group_id'] = $group_id;
			}

			$this->db->insert('es_exhibition_stalls', $stall_data);
			$stall_id = $this->db->insert_id();

			if (is_null($group_id)) {
				$group_id = date('Ymd') . $stall_id;
				$this->db
					->where('id', $stall_id)
					->update('es_exhibition_stalls', array('stall_group_id' => $group_id));
			}
		}


		$this->session->set_flashdata('message', 'Stalls has been added successfully');
		redirect(base_url('stalls-list.html?id=' . $this->input->get('id')));
	}


	function crd_edit() {
		$this->checkEditId();

		$id = $this->input->get('stall_id');
		if (is_null($id))
			show_404();

		$stall = $this->db
			->where(mycolumn(), $id)
			->get('es_exhibition_stalls')
			->row();

		$this->load->view('includes/after_login/head');
		$this->load->view('stalls/edit', array(
			'stall' => $stall
		));
	}

	function crd_edit_validate() {
		$this->checkEditId();

		$this->form_validation->set_rules('stall_price_usd', 'stall_price_usd*stall price usd', 'trim|required|numeric');
		$this->form_validation->set_rules('stall_price_pkr', 'stall_price_pkr*stall price pkr', 'trim|required|numeric');
		$this->form_validation->set_rules('stall_size', 'stall_size*stall size', 'trim|required|numeric');
		$this->form_validation->set_rules('stall_category', 'stall_category*stall category', 'trim|required');
		$this->form_validation->set_rules('hall_id', 'hall_id*exhibition hall', 'trim|required');
		$this->form_validation->set_rules('location_in_hall', 'location_in_hall*location in hall', 'trim');
		$this->form_validation->set_rules('stall_description', 'stall_description*stall description', 'trim');
		$this->form_validation->set_rules('stall_map[0]', 'stall_map[0]*stall map', 'trim');


		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		}
		else {
			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function crd_edit_submit() {
		if ($this->crd_edit_validate() !== true)
			show_404();

		$id = $this->input->post('stall_id');
		if (is_null($id))
			show_404();

		$exhibition_id = $this->formdata->id;
		$stall = $this->db
			->where(mycolumn(), $id)
			->get('es_exhibition_stalls')
			->row();

		$stall_data = array(
			'stall_name' => $this->input->post('stall_name'),
			'stall_size' => $this->input->post('stall_size'),
			'stall_size_unit' => 'sqm',
			'stall_category' => $this->input->post('stall_category'),
			'stall_price_usd' => $this->input->post('stall_price_usd'),
			'stall_price_pkr' => $this->input->post('stall_price_pkr'),
			'hall_id' => $this->input->post('hall_id'),
			'location_in_hall' => $this->input->post('location_in_hall'),
			'description' => $this->input->post('stall_description'),
		);
		if ($this->input->post('stall_map')) {
			$stall_data['stall_map'] = $this->funcs->make_image_string($this->input->post('stall_map'));
		}
		$this->db
			->where('id', $stall->id)
			->update('es_exhibition_stalls', $stall_data);

		$this->session->set_flashdata('message', 'Stalls has been updated successfully');
		redirect(base_url('stalls-list.html?id=' . $this->input->get('id')));
	}

	function crd_delete() {
		if (!ALLOW_DELETION)
			show_404();
		$id = $this->input->get('stall_id');
		if (is_null($id))
			show_404();

		$check = $this->db
			->where(mycolumn(), $id)
			->get('es_exhibition_stalls')
			->row();

		if ($check->is_booked == 1 || $check->is_sold == 1) {
			$this->session->set_flashdata('error', 'Stall cannot be delete because its already booked by customer');
			redirect(base_url('stalls-list.html?id=' . $this->input->get('id')));
			exit;
		}

		$this->db
			->where('id', $check->id)
			->update('es_exhibition_stalls', array(
				'is_deleted' => 1,
				'deleted_by' => $this->userdata->id,
				'deleted_on' => date('Y-m-d H:i:s')
			));

		$this->session->set_flashdata('message', 'Stall has been deleted successfully');
		redirect(base_url('stalls-list.html?id=' . $this->input->get('id')));
	}


}