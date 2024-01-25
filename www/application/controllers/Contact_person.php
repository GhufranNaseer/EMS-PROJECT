<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contact_person extends MY_Controller {
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
		$this->myparent = base_url('contact_person.html');
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
			->get('es_customer_contact_persons');
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();

	}

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('contact_person/list');
	}


	function crd_list_datatable() {
		$this->load->library('datatables');
		$this->datatables
			->select('E.id, 
				  E.person_name,
				  C.company,
				  E.designation, 
				  E.primary_email, 
				  E.primary_phone,
				  E.is_active', false)
			->unset_column('E.is_active')
			->add_column('active', function ($row) {
				$active = $row['is_active'];
				return ($active == 1) ? '<center><img src="' . base_url('assets/img/t.png') . '" /></center>' : '<center><img src="' . base_url('assets/img/cross.png') . '" /></center>';
			}, NULL)
			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'contact_person-edit.html?id=' . urlencode(myid($id)) . '">Edit</a>';
				if (ALLOW_DELETION)
					$html .= ' | <a href="' . base_url() . 'contact_person-delete.html?id=' . urlencode(myid($id)) . '" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';
				return "<center>{$html}</center>";
			}, NULL)
            ->join('es_customers as C', 'C.id = E.customer_id', 'LEFT')
			->from('es_customer_contact_persons as E')
			->where('E.is_deleted', 0);

        if ($this->input->get('filter_event')){
            $this->datatables->join('es_exhibition_booking', 'es_exhibition_booking.contact_person_id = E.id', 'LEFT');
            $this->datatables->where('es_exhibition_booking.exhibition_id', $this->input->get('filter_event'));
        }

		print ($this->datatables->generate());
	}



    function crd_add() {
        $this->load->view('includes/after_login/head');
        $this->load->view('contact_person/add');
    }

	function crd_add_validate() {

		$this->form_validation->set_rules('customer_id', 'customer_id*customer', 'trim|required');
		$this->form_validation->set_rules('person_name', 'person_name*person_name', 'trim|required');
		$this->form_validation->set_rules('designation', 'designation*designation', 'trim|required');
		$this->form_validation->set_rules('primary_email', 'primary_email*primary_email', 'trim|required|valid_email|is_unique[es_customer_contact_persons.primary_email]');
		$this->form_validation->set_rules('secondary_email', 'secondary_email*secondary_email', 'trim|valid_email');
		$this->form_validation->set_rules('primary_phone', 'primary_phone*primary_phone', 'trim|numeric|required|min_length[8]');
		$this->form_validation->set_rules('secondary_phone', 'secondary_phone*secondary_phone', 'trim|numeric|min_length[8]');
		$this->form_validation->set_rules('office_phone', 'office_phone*office_phone', 'trim|numeric|min_length[8]');
		$this->form_validation->set_rules('office_phone_extention', 'office_phone_extention*office_phone_extention', 'trim|numeric');


		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

    function crd_add_submit() {
        if ($this->crd_add_validate() !== true)
            show_404();

        $data = array(
            'customer_id' => $this->input->post('customer_id'),
            'person_name' => $this->input->post('person_name'),
            'designation' => $this->input->post('designation'),
            'primary_email' => $this->input->post('primary_email'),
            'secondary_email' => $this->input->post('secondary_email'),
            'primary_phone' => $this->input->post('primary_phone'),
            'secondary_phone' => $this->input->post('secondary_phone'),
            'office_phone' => $this->input->post('office_phone'),
            'office_phone_extention' => $this->input->post('office_phone_extention'),
            'is_active' => 1,
            'created_on' => date('Y-m-d H:i:s')
        );

        $this->db->trans_start();
        $this->db
            ->set($data)
            ->insert('es_customer_contact_persons');
        //$id = $this->db->insert_id();
        $this->db->trans_complete();

        $this->session->set_flashdata('message', 'Contact person has been created successfully');
		if ($this->input->post('back_url') && $this->input->post('back_url') != '') {
			redirect(base_url($this->input->post('back_url')));
		}
		else {
        	redirect(base_url('contact_person.html'));
		}
    }


	function crd_edit() {
		$this->checkEditId();
		//echo '<pre>'; print_r($this->formdata); die();
		$this->load->view('includes/after_login/head');
		$this->load->view('contact_person/edit');
	}



	function crd_edit_submit() {
		if ($this->crd_edit_validate() !== true)
			show_404();
		$data = array(
            'customer_id' => $this->input->post('customer_id'),
            'person_name' => $this->input->post('person_name'),
            'designation' => $this->input->post('designation'),
            'primary_email' => $this->input->post('primary_email'),
            'secondary_email' => $this->input->post('secondary_email'),
            'primary_phone' => $this->input->post('primary_phone'),
            'secondary_phone' => $this->input->post('secondary_phone'),
            'office_phone' => $this->input->post('office_phone'),
            'office_phone_extention' => $this->input->post('office_phone_extention'),
            'is_active' => 1,
            'created_on' => date('Y-m-d H:i:s')
		);


		$this->db->trans_start();
		$this->db
			->set($data)
			->where('id', $this->formdata->id)
			->update('es_customer_contact_persons');

		$this->db->trans_complete();
		$this->session->set_flashdata('message', 'Contact person has been updated successfully');
		redirect(base_url('contact_person.html'));
	}

	function crd_edit_validate() {
		$this->checkEditId();

        $this->form_validation->set_rules('customer_id', 'customer_id*customer', 'trim|required');
        $this->form_validation->set_rules('person_name', 'person_name*person_name', 'trim|required');
        $this->form_validation->set_rules('designation', 'designation*designation', 'trim|required');
        $this->form_validation->set_rules('primary_email', 'primary_email*primary_email', 'trim|required|valid_email');
        $this->form_validation->set_rules('secondary_email', 'secondary_email*secondary_email', 'trim|valid_email');
        $this->form_validation->set_rules('primary_phone', 'primary_phone*primary_phone', 'trim|numeric|required|min_length[8]');
        $this->form_validation->set_rules('secondary_phone', 'secondary_phone*secondary_phone', 'trim|numeric|min_length[8]');
        $this->form_validation->set_rules('office_phone', 'office_phone*office_phone', 'trim|numeric|min_length[8]');
        $this->form_validation->set_rules('office_phone_extention', 'office_phone_extention*office_phone_extention', 'trim|numeric');


        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else
            return $this->common->doError(func_num_args(), "done", true);
    }

	function crd_delete() {
		$this->checkEditId();
		if (!ALLOW_DELETION)
			show_404();

		$booking_check = $this->db
			->where('contact_person_id', $this->formdata->id)
			->where('is_canceled', 0)
			->count_all_results('es_exhibition_booking');

		if ($booking_check > 0) {
			$this->session->set_flashdata('error', 'Contact person cannot be deleted, Please cancel all his booking before deleting customer');
			redirect(base_url('customers.html'));
			die();
		}

		$this->db
			->where('id', $this->formdata->id)
			->update('es_customer_contact_persons', array(
				'is_deleted' => 1,
				'deleted_by' => $this->userdata->id,
				'deleted_on' => date('Y-m-d H:i:s')
			));

		$this->session->set_flashdata('message', 'User has been deleted successfully');
		redirect(base_url('contact_person.html'));
	}
}