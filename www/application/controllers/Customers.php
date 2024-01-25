<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends MY_Controller {
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
		$this->myparent = base_url('customers.html');
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
			->get('es_customers');
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();

	}

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('customer/list');
	}


	function crd_list_datatable() {
		$this->load->library('datatables');
		$this->datatables
			->select('es_customers.id, 
				  es_customers.company, 
				  es_customers.name, 
				  es_customers.country, 
				  es_customers.phone, 
				  es_customers.email,
				  es_customers.is_active', false)
			->unset_column('es_customers.is_active')
			->add_column('active', function ($row) {
				$active = $row['is_active'];
				return ($active == 1) ? '<center><img src="' . base_url('assets/img/t.png') . '" /></center>' : '<center><img src="' . base_url('assets/img/cross.png') . '" /></center>';
			}, NULL)
			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'customer-edit.html?id=' . urlencode(myid($id)) . '">Edit</a>';
				if (ALLOW_DELETION)
					$html .= ' | <a href="' . base_url() . 'customer-delete.html?id=' . urlencode(myid($id)) . '" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';
				return "<center>{$html}</center>";
			}, NULL)
			->from('es_customers')
			->where('is_deleted', 0);

        if ($this->input->get('filter_event')){
            $this->datatables->join('es_exhibition_booking', 'es_exhibition_booking.customer_id = es_customers.id', 'LEFT');
            $this->datatables->where('es_exhibition_booking.exhibition_id', $this->input->get('filter_event'));
        }
            print ($this->datatables->generate());
	}



    function crd_add() {
        $this->load->view('includes/after_login/head');
        $this->load->view('customer/add');
    }

    function crd_add_submit() {
        if ($this->crd_add_validate() !== true)
            show_404();

        $data = array(
            'company' => $this->input->post('company'),
            'name' => $this->input->post('name'),
            'designation' => $this->input->post('designation'),
            'email' => $this->input->post('email'),
            'url' => $this->input->post('url'),
            'phone' => $this->input->post('phone'),
            'fax' => $this->input->post('fax'),
            'city' => $this->input->post('city'),
            'country' => $this->input->post('country'),
            'zip_code' => $this->input->post('zip_code'),
            'address' => $this->input->post('address'),
            'is_active' => 1
        );

        $this->db->trans_start();
        $this->db
            ->set($data)
            ->insert('es_customers');
        //$id = $this->db->insert_id();
        $this->db->trans_complete();

        $this->session->set_flashdata('message', 'Customer has been created successfully');

        if ($this->input->post('back_url') && $this->input->post('back_url') != '') {
        	redirect(base_url($this->input->post('back_url')));
		}
        else {
			redirect(base_url('customers.html'));
		}
    }

    function crd_add_validate() {

        $this->form_validation->set_rules('company', 'company*company', 'trim|required|is_unique[es_customers.company]');
        $this->form_validation->set_rules('name', 'name*name', 'trim|required');
        $this->form_validation->set_rules('designation', 'designation*designation', 'trim');
        $this->form_validation->set_rules('email', 'email*email', 'trim|required|valid_email|is_unique[es_customers.email]');
        $this->form_validation->set_rules('url', 'url*url', 'trim');
        $this->form_validation->set_rules('telephone', 'telephone*telephone', 'trim|numeric|min_length[8]');
        $this->form_validation->set_rules('fax', 'fax*fax', 'trim|numeric');
        $this->form_validation->set_rules('country', 'country*country', 'trim');
        $this->form_validation->set_rules('city', 'city*city', 'trim');
        $this->form_validation->set_rules('zip_code', 'zip_code*zip_code', 'trim|numeric');
        $this->form_validation->set_rules('address', 'address*address', 'trim');



        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else
            return $this->common->doError(func_num_args(), "done", true);
    }

	function crd_edit() {
		$this->checkEditId();
		$this->load->view('includes/after_login/head');
		$this->load->view('customer/edit');
	}



	function crd_edit_submit() {
		if ($this->crd_edit_validate() !== true)
			show_404();
		$isActive = (is_null($this->input->post('active')) ? 0 : 1);
		$data = array(
			'company' => $this->input->post('company'),
			'name' => $this->input->post('name'),
			'designation' => $this->input->post('designation'),
			'email' => $this->input->post('email'),
			'url' => $this->input->post('url'),
			'phone' => $this->input->post('phone'),
			'fax' => $this->input->post('fax'),
			'city' => $this->input->post('city'),
			'country' => $this->input->post('country'),
			'zip_code' => $this->input->post('zip_code'),
			'address' => $this->input->post('address'),
			'is_active' => $isActive
		);


		$this->db->trans_start();
		$this->db
			->set($data)
			->where('id', $this->formdata->id)
			->update('es_customers');

		$this->db->trans_complete();
		$this->session->set_flashdata('message', 'User has been updated successfully');
		redirect(base_url('customers.html'));
	}

	function crd_edit_validate() {
		$this->checkEditId();

		$this->form_validation->set_rules('company', 'company*company', 'trim|required|set_edit_mood[id.' . $this->formdata->id . ']|is_unique[es_customers.company]');
		$this->form_validation->set_rules('name', 'name*name', 'trim|required');
		$this->form_validation->set_rules('designation', 'designation*designation', 'trim');
		$this->form_validation->set_rules('email', 'email*email', 'trim|required|valid_email|set_edit_mood[id.' . $this->formdata->id . ']|is_unique[es_customers.email]');
		$this->form_validation->set_rules('url', 'url*url', 'trim');
		$this->form_validation->set_rules('telephone', 'telephone*telephone', 'trim|numeric|min_length[8]');
		$this->form_validation->set_rules('fax', 'fax*fax', 'trim|numeric');
		$this->form_validation->set_rules('country', 'country*country', 'trim');
		$this->form_validation->set_rules('city', 'city*city', 'trim');
		$this->form_validation->set_rules('zip_code', 'zip_code*zip_code', 'trim|numeric');
		$this->form_validation->set_rules('address', 'address*address', 'trim');



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

		$booking_check = $this->db
			->where('customer_id', $this->formdata->id)
			->where('is_canceled', 0)
			->count_all_results('es_exhibition_booking');

		if ($booking_check > 0) {
			$this->session->set_flashdata('error', 'Customer cannot be deleted, Please cancel all his booking before deleting customer');
			redirect(base_url('customers.html'));
			die();
		}

		$this->db
			->where('id', $this->formdata->id)
			->update('es_customers', array(
				'is_deleted' => 1,
				'deleted_by' => $this->userdata->id,
				'deleted_on' => date('Y-m-d H:i:s')
			));

		$this->session->set_flashdata('message', 'User has been deleted successfully');
		redirect(base_url('customers.html'));
	}
}