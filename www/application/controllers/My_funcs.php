<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class My_funcs extends MY_Controller {
	protected function rule() {
		$this->activateRightsSystem();
		$crd = array(
			'
			crd_list
			' => array(
				'rule' => '@'
			),
			'
			autocomplete_customer_company,
			get_customer_data,
			get_all_customers,
			get_customer_contact_person,
			' => array(
				'rule' => '@',
				'ajaxOnly' => true
			)
		);
		$this->load->model('usermdl');
		return array_merge($crd);
	}

	function autocomplete_customer_company() {
		$term = $this->input->get('term');

		$companies = $this->db
			->like('company', $term)
			->limit(10)
			->get('es_customers')
			->result();

		$response = array();
		foreach ($companies as $company) {
			$response[] = array(
				'id' => $company->id,
				'value' => $company->company,
			);
		}
		echo json_encode($response);
		exit;
	}

	function get_customer_data() {
		$customer_id = $this->input->post('customer_id');

		$company = $this->db
			->where('id', $customer_id)
			->get('es_customers')
			->row();

		if ($company) {
			echo json_encode(array(
				'error' => 0,
				'data' => $company
			));
		} else {
			echo json_encode(array(
				'error' => 1,
				'message' => 'customer not found!'
			));
		}
	}

	function get_all_customers() {
		$es_customers = $this->db
			->where('is_active', 1)
			->where('is_deleted', 0)
			->get('es_customers')
			->result();

		if ($es_customers) {
			echo json_encode(array(
				'error' => 0,
				'data' => $es_customers
			));
		} else {
			echo json_encode(array(
				'error' => 1,
				'message' => 'customers not found!'
			));
		}
	}

	function get_customer_contact_person() {
		$customer_id = $this->input->post('customer_id');

		$es_customers = $this->db
			->where('customer_id', $customer_id)
			->where('is_active', 1)
			->where('is_deleted', 0)
			->get('es_customer_contact_persons')
			->result();

		if ($es_customers) {
			echo json_encode(array(
				'error' => 0,
				'data' => $es_customers
			));
		} else {
			echo json_encode(array(
				'error' => 1,
				'message' => 'No record found!'
			));
		}
	}
}