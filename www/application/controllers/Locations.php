<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Locations extends MY_Controller {
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
			->get('es_locations');
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();

	}

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('locations/list');
	}


	function crd_list_datatable() {
		$this->load->library('datatables');
		$this->datatables
			->select('id,
				  location_title,
				  location_country,
				  location_city,
				  location_address
				  ', false)

			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'locations-edit.html?id=' . urlencode(myid($id)) . '">Edit</a>';
				if (ALLOW_DELETION)
					$html .= ' | <a href="' . base_url() . 'locations-delete.html?id=' . urlencode(myid($id)) . '" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';
				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where('is_deleted', 0)
			->from('es_locations');

		print ($this->datatables->generate());
	}


	function crd_add() {
		$this->load->view('includes/after_login/head');
		$this->load->view('locations/add');
	}

	function crd_add_validate() {

		$this->form_validation->set_rules('location_title', 'location_title*Location Title', 'trim|required');
		$this->form_validation->set_rules('location_country', 'location_country*Location Country', 'trim|required');
		$this->form_validation->set_rules('location_city', 'location_city*Location City', 'trim|required');
		$this->form_validation->set_rules('location_address', 'location_address*Location Address', 'trim|required');
		$this->form_validation->set_rules('hall[]', 'hall[]*Halls', 'trim|required');

		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		}
		else {
			foreach ($this->input->post('hall') as $key => $value) {
				if (trim($value['hall_title']) == '') {
					return $this->common->doError(func_num_args(), "hall title field is required");
				}
				else if (trim($value['covering_area']) == '') {
					return $this->common->doError(func_num_args(), "covering area field is required");
				}
				else if (trim($value['description']) == '') {
					return $this->common->doError(func_num_args(), "hall description field is required");
				}
			}

			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function crd_add_submit() {
		if ($this->crd_add_validate() !== true)
			show_404();

		$data = array(
			'location_title' => $this->input->post('location_title'),
			'location_country' => $this->input->post('location_country'),
			'location_city' => $this->input->post('location_city'),
			'location_address' => $this->input->post('location_address'),
			'location_lat' => $this->input->post('location_lat'),
			'location_lng' => $this->input->post('location_lng'),
			'created_on' => date('Y-m-d H:i:s'),
		);

		$this->db
			->set($data)
			->insert('es_locations');
		$insert_id = $this->db->insert_id();

		$halls_data = array();
		foreach ($this->input->post('hall') as $key => $value) {
			$halls_data[] = array(
				'location_id' => $insert_id,
				'hall_title' => $value['hall_title'],
				'covering_area' => $value['covering_area'],
				'description' => $value['description'],
			);
		}

		$this->db->insert_batch('es_location_halls', $halls_data);

		$this->session->set_flashdata('message', 'Location has been created successfully');
		redirect($this->myparent);
	}



	function crd_edit() {
		$this->checkEditId();
		$this->load->view('includes/after_login/head');
		$this->load->view('locations/edit');
	}

	function crd_edit_validate() {
		$this->checkEditId();

		$this->form_validation->set_rules('location_title', 'location_title*Location Title', 'trim|required');
		$this->form_validation->set_rules('location_country', 'location_country*Location Country', 'trim|required');
		$this->form_validation->set_rules('location_city', 'location_city*Location City', 'trim|required');
		$this->form_validation->set_rules('location_address', 'location_address*Location Address', 'trim|required');
		$this->form_validation->set_rules('hall[]', 'hall[]*Halls', 'trim|required');

		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		}
		else {
			foreach ($this->input->post('hall') as $key => $value) {
				if (trim($value['hall_title']) == '') {
					return $this->common->doError(func_num_args(), "hall title field is required");
				}
				else if (trim($value['covering_area']) == '') {
					return $this->common->doError(func_num_args(), "covering area field is required");
				}
				else if (trim($value['description']) == '') {
					return $this->common->doError(func_num_args(), "hall description field is required");
				}
			}

			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function crd_edit_submit() {
		if ($this->crd_edit_validate() !== true)
			show_404();


		$data = array(
			'location_title' => $this->input->post('location_title'),
			'location_country' => $this->input->post('location_country'),
			'location_city' => $this->input->post('location_city'),
			'location_address' => $this->input->post('location_address'),
			'location_lat' => $this->input->post('location_lat'),
			'location_lng' => $this->input->post('location_lng'),
		);


		$this->db
			->set($data)
			->where('id', $this->formdata->id)
			->update('es_locations');

		// remove deleted halls if any
		$check_halls = $this->db
			->where('location_id', $this->formdata->id)
			->where('is_deleted', 0)
			->get('es_location_halls')
			->result();
		foreach ($check_halls as $check_hall) {
			$is_remove = true;
			foreach ($this->input->post('hall') as $key => $value) {
				if (isset($value['hall_id']) && $value['hall_id'] != '' && $value['hall_id'] == $check_hall->id) {
					$is_remove = false;
				}
			}
			if ($is_remove) {
				$this->db
					->where('id', $check_hall->id)
					->update('es_location_halls', array(
						'is_deleted' => 1,
						'deleted_by' => $this->userdata->id,
						'deleted_on' => date('Y-m-d H:i:s'),
					));
			}
		}

		// insert or update halls data
		foreach ($this->input->post('hall') as $key => $value) {
			$halls_data = array(
				'location_id' => $this->formdata->id,
				'hall_title' => $value['hall_title'],
				'covering_area' => $value['covering_area'],
				'description' => $value['description'],
			);

			if (isset($value['hall_id']) && $value['hall_id'] != '') {
				$this->db
					->where('id', $value['hall_id'])
					->update('es_location_halls', $halls_data);
			} else {
				$this->db->insert('es_location_halls', $halls_data);
			}
		}


		$this->session->set_flashdata('message', 'Location has been updated successfully');
		redirect($this->myparent);
	}


	function crd_delete() {
		$this->checkEditId();
		if (!ALLOW_DELETION)
			show_404();

		$check_events = $this->db
			->where('location_id', $this->formdata->id)
			->count_all_results('es_exhibition_halls');

		if ($check_events > 0) {
			$this->session->set_flashdata('error', 'Location cannot be deleted, because its already assign in events!');
			redirect($this->myparent);
			die();
		}

		$this->db
			->where('id', $this->formdata->id)
			->update('es_locations', array(
				'is_deleted' => 1,
				'deleted_by' => $this->userdata->id,
				'deleted_on' => date('Y-m-d H:i:s')
			));

		$this->db
			->where('location_id', $this->formdata->id)
			->update('es_location_halls', array(
				'is_deleted' => 1,
				'deleted_by' => $this->userdata->id,
				'deleted_on' => date('Y-m-d H:i:s'),
			));
		$this->session->set_flashdata('message', 'Location has been deleted successfully');
		redirect($this->myparent);
	}

	function get_halls_json() {
		$location_id = $this->input->post('location_id');
		if (!isset($location_id)) {
			echo json_encode(array(
				'error' => 1,
				'message' => 'location_id is required'
			));
			exit;
		}
		$halls = $this->db
			->where('location_id', $location_id)
			->where('is_deleted', 0)
			->get('es_location_halls')
			->result();

		if ($halls) {
			echo json_encode($halls);
		} else {
			echo json_encode(array(
				'error' => 1,
				'message' => 'No halls found in this location'
			));
		}
		exit;
	}

	function check_hall_usage() {
		$hall_id = $this->input->post('hall_id');
		if (is_null($hall_id)) {
			echo json_encode(array(
				'error' => 1,
				'message' => 'Hall id is required'
			));
		}

		$check = $this->db
			->where('hall_id', $hall_id)
			->count_all_results('es_exhibition_halls');

		if ($check > 0) {
			echo json_encode(array(
				'error' => 1,
				'message' => 'Hall cannot be deleted, because its already assign in event'
			));
		} else {
			echo json_encode(array(
				'error' => 0,
				'message' => 'Hall can be deleted'
			));
		}
	}
}