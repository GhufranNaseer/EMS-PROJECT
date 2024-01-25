<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trade_visitor extends MY_Controller {
	protected function rule() {
		$this->activateRightsSystem();
		$crd = array(
			'exhibitor_crd_list,
			crd_list,crd_add,crd_delete,
			crd_add_submit,
			crd_list_datatable,
			' => array(
				'rule' => '@'
			),
			'exhibitor_crd_list_datatable,
			crd_add_validate' => array(
				'rule' => '@',
				'ajaxOnly' => true
			)
		);
		$crd_dit = array(
			'crd_edit' => array(
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
		$this->myparent = base_url('trade-visitor.html');
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

	function exhibitor_crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('trade_visitor/exhibition_list');
	}

	function exhibitor_crd_list_datatable()
	{
		$this->load->library('datatables');
		$this->datatables
			->select('E.id,
				  E.exhibition_title,
				  L.location_title				  
				  ', false)
			->add_column('total_orders', function ($row) {
				return $this->db
					->where('exhibition_id', $row['id'])
					->where('is_active', 1)
					->where('badge_type', 'trade_visitor')
					->count_all_results('es_exhibition_badges');
			}, NULL)
			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'trade-visitor-badges.html?id=' . urlencode(myid($id)) . '">Badges List</a>';
				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where('E.is_deleted', 0)
			->join('es_locations as L', 'E.location_id = L.id', 'LEFT')
			->from('es_exhibitions as E');

		print ($this->datatables->generate());
	}

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('trade_visitor/badges_list');
	}

	function crd_list_datatable() {
		$this->checkEditId();

		$this->load->library('datatables');
		$this->datatables
			->select('id,
				  exhibition_id,
				  (SELECT CONCAT(user_first_name, \' \', user_last_name) as name FROM users WHERE id = added_by) as sales_person,
				  full_name,
				  designation,
				  IF(cnic IS NULL, passport, cnic) as cnic_passport,
				  barcode_data,
				  DATE_FORMAT(created_on, "%d, %b %Y") as created_on,
				  is_printed,
                  is_hold,
                  IF(is_hold=1, "<span class=\"label label-info\">Hold</span>",IF(is_printed=1, "<span class=\"label label-success\">Printed</span>" , "<span class=\"label label-danger\">Not Printed</span>" )) as print_status
				  ', false)

			->unset_column('exhibition_id')
			->unset_column('is_printed')
			->unset_column('is_hold')

			->add_column('col_action', function ($row) {
				$badge_id = $row['id'];
				$html = '';
				if ($row['is_printed'] == 0 && $row['is_hold'] == 0) {
					$html = '<a href="'.base_url('view-badges.html?id=' . myid($badge_id)).'" target="_blank">View Badge</a> | ';
					$html .= '<a href="'.base_url('hold-badges.html?id=' . myid($badge_id)).'&event_id='.myid($this->formdata->id).'">Hold Badge</a>';
				} else {
					$html = '<a href="'.base_url('reset-badges-print.html?id=' . myid($badge_id)).'&event_id='.myid($this->formdata->id).'" onclick="return confirm(\'Are you sure you want to reset badge status?\')">Reset Print</a>';
				}
				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->add_column('col_edit', function ($row) {
				$id = $row['id'];
				$html = '';
				if ($row['is_printed'] == 0 && $row['is_hold'] == 0) {
					$html = '<a href="' . base_url() . 'trade-visitor-edit.html?id=' . myid($id) . '&event_id='.myid($this->formdata->id).'">Edit</a>';
					$html .= ' | <a href="' . base_url() . 'trade-visitor-delete.html?id=' . myid($id) . '&event_id=' . myid($this->formdata->id) . '">Delete</a>';
				}
				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->add_column('col_select', function ($row) {
				$id = $row['id'];
				$html = '';
				if ($row['is_printed'] == 0 && $row['is_hold'] == 0) {
					$html = '<input type="checkbox" class="check_box" value="'.$id.'">';
				}
				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where('exhibition_id', $this->formdata->id)
			->where('is_active', 1)
			->where('badge_type', 'trade_visitor')
			->from('es_exhibition_badges');

		if ($this->userdata->user_group_id == SALES_PERSON) {
			$this->datatables->where('added_by', $this->userdata->id);
		}
		if ($this->input->get('filter_sales_person') && $this->input->get('filter_sales_person') != '') {
			$this->datatables->where('added_by', $this->input->get('filter_sales_person'));
		}

		print ($this->datatables->generate());
	}

	function crd_add() {
		$this->load->view('includes/after_login/head');
		$this->load->view('trade_visitor/add');
	}
	function crd_add_validate() {
		$this->checkEditId();

		$this->form_validation->set_rules('full_name', 'full_name*Full name', 'trim|required');
		$this->form_validation->set_rules('designation', 'designation*Designation', 'trim|required');
		$this->form_validation->set_rules('email', 'email*Email', 'trim');
		$this->form_validation->set_rules('mobile', 'mobile*Cell Phone', 'trim');
		$this->form_validation->set_rules('phone', 'phone*Landline', 'trim');
		$this->form_validation->set_rules('country', 'country*country', 'trim|required');
		$this->form_validation->set_rules('cnin_passport', 'cnin_passport*CNIC or Passport', 'trim|required');
		$this->form_validation->set_rules('expiry_date', 'expiry_date*expiry_date', 'trim');
		$this->form_validation->set_rules('company', 'company*company', 'trim|required');
		$this->form_validation->set_rules('collection_person_name', 'collection_person_name*Collection Person Name', 'trim');
		$this->form_validation->set_rules('collection_person_cnic', 'collection_person_cnic*Collection Person CNIC', 'trim');
		$this->form_validation->set_rules('collection_person_phone', 'collection_person_phone*Collection Person Phone', 'trim');



		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}
	function crd_add_submit() {
		if ($this->crd_add_validate() !== true)
			show_404();

		$badge_data = array(
			'exhibition_id' => $this->formdata->id,
			'booking_id' => null,
			'badge_type' => 'trade_visitor',
			'full_name' => $this->input->post('full_name'),
			'designation' => $this->input->post('designation'),
			'mobile' => $this->input->post('mobile'),
			'nationality' => (($this->input->post('country') == 'Pakistan') ? 'Pakistani' : $this->input->post('country')),
			'email' => $this->input->post('email'),
			'cnic' => (($this->input->post('country') == 'Pakistan') ? $this->input->post('cnin_passport') : null),
			'cnic_image' => null,
			'cnic_back_image' => null,
			'passport' => (($this->input->post('country') == 'Pakistan') ? null : $this->input->post('cnin_passport')),
			'expiry_date' => date('Y-m-d', strtotime($this->input->post('expiry_date'))),
			'company' => $this->input->post('company'),
			'collection_person_name' => $this->input->post('collection_person_name'),
			'collection_person_cnic' => $this->input->post('collection_person_cnic'),
			'collection_person_phone' => $this->input->post('collection_person_phone'),
			'passport_image' => null,
			'passport_back_image' => null,
			'user_image' => (($this->input->post('user_image')) ? '../'.$this->funcs->make_image_string($this->input->post('user_image')) : null),
			'added_by' => $this->userdata->id,
			'is_active' => 1,
			'created_on' => date('Y-m-d H:i:s'),
		);

		$this->db->insert('es_exhibition_badges', $badge_data);

		$this->session->set_flashdata('message', 'Visitor badge has been created successfully');
		redirect(base_url('trade-visitor-badges.html?id=' . myid($this->formdata->id)));
	}

	function crd_edit() {

		$data = $this->db
			->where(mycolumn(), $this->input->get('id'))
			->where('is_active', 1)
			->get('es_exhibition_badges')
			->row();

		if (empty($data)) {
			show_404();
		}

		$this->load->view('includes/after_login/head');
		$this->load->view('trade_visitor/edit', array('data' => $data));
	}
	function crd_edit_validate() {
		$this->form_validation->set_rules('full_name', 'full_name*Full name', 'trim|required');
		$this->form_validation->set_rules('designation', 'designation*Designation', 'trim|required');
		$this->form_validation->set_rules('email', 'email*Email', 'trim');
		$this->form_validation->set_rules('mobile', 'mobile*Cell Phone', 'trim');
		$this->form_validation->set_rules('phone', 'phone*Landline', 'trim');
		$this->form_validation->set_rules('country', 'country*country', 'trim|required');
		$this->form_validation->set_rules('cnin_passport', 'cnin_passport*CNIC or Passport', 'trim|required');
		$this->form_validation->set_rules('expiry_date', 'expiry_date*expiry_date', 'trim');
		$this->form_validation->set_rules('company', 'company*company', 'trim|required');
		$this->form_validation->set_rules('collection_person_name', 'collection_person_name*Collection Person Name', 'trim');
		$this->form_validation->set_rules('collection_person_cnic', 'collection_person_cnic*Collection Person CNIC', 'trim');
		$this->form_validation->set_rules('collection_person_phone', 'collection_person_phone*Collection Person Phone', 'trim');


		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}
	function crd_edit_submit() {
		if ($this->crd_edit_validate() !== true)
			show_404();

		$old_data = $this->db
			->where(mycolumn(), $this->input->get('id'))
			->where('is_active', 1)
			->get('es_exhibition_badges')
			->row();

		$badge_data = array(
			'full_name' => $this->input->post('full_name'),
			'designation' => $this->input->post('designation'),
			'mobile' => $this->input->post('mobile'),
			'nationality' => (($this->input->post('country') == 'Pakistan') ? 'Pakistani' : $this->input->post('country')),
			'email' => $this->input->post('email'),
			'cnic' => (($this->input->post('country') == 'Pakistan') ? $this->input->post('cnin_passport') : null),
			'cnic_image' => null,
			'cnic_back_image' => null,
			'passport' => (($this->input->post('country') == 'Pakistan') ? null : $this->input->post('cnin_passport')),
			'expiry_date' => date('Y-m-d', strtotime($this->input->post('expiry_date'))),
			'company' => $this->input->post('company'),
			'collection_person_name' => $this->input->post('collection_person_name'),
			'collection_person_cnic' => $this->input->post('collection_person_cnic'),
			'collection_person_phone' => $this->input->post('collection_person_phone'),
			'passport_image' => null,
			'passport_back_image' => null,
			//'user_image' => (($this->input->post('user_image')) ? $this->funcs->make_image_string($this->input->post('user_image')) : null),
		);
		$this->db
			->where(mycolumn(), $this->input->get('id'))
			->update('es_exhibition_badges', $badge_data);

		$this->session->set_flashdata('message', 'Visitor badge has been updated successfully');
		redirect(base_url('trade-visitor-badges.html?id=' . myid($old_data->exhibition_id)));
	}

	function crd_delete() {
		$data = $this->db
			->where(mycolumn(), $this->input->get('id'))
			->get('es_exhibition_badges')
			->row();
		if (empty($data)) {
			show_404();
		}

		$this->db
			->where(mycolumn(), $this->input->get('id'))
			->update('es_exhibition_badges', array(
				'is_active' => 0
			));

		$this->session->set_flashdata('message', 'Visitor badge has been deleted successfully');
		redirect(base_url('trade-visitor-badges.html?id=' . myid($data->exhibition_id)));
	}


}