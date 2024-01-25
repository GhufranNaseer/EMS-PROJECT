<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agent extends MY_Controller {
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
			->get('es_agent');
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();

	}

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('agent/list');
	}


	function crd_list_datatable() {
		$this->load->library('datatables');
		$this->datatables
			->select('es_agent.id, 
				  es_agent.agent_company, 
				  es_agent.agent_name, 
				  es_agent.agent_country, 
				  es_agent.agent_phone, 
				  es_agent.agent_email')
			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'agent-edit.html?id=' . urlencode(myid($id)) . '">Edit</a>';
				if (ALLOW_DELETION)
					$html .= ' | <a href="' . base_url() . 'agent-delete.html?id=' . urlencode(myid($id)) . '" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';
				return "<center>{$html}</center>";
			}, NULL)
			->from('es_agent')
            ->where('is_deleted', 0);


        if ($this->input->get('filter_event')){
            $this->datatables->join('es_exhibition_booking', 'es_exhibition_booking.agent_id = es_agent.id', 'LEFT');
            $this->datatables->where('es_exhibition_booking.exhibition_id', $this->input->get('filter_event'));
        }

		print ($this->datatables->generate());
	}



    function crd_add() {
        $this->load->view('includes/after_login/head');
        $this->load->view('agent/add');
    }

    function crd_add_submit() {
        if ($this->crd_add_validate() !== true)
            show_404();

        $data = array(
            'agent_company' => $this->input->post('agent_company'),
            'agent_name' => $this->input->post('agent_name'),
            'agent_designation' => $this->input->post('agent_designation'),
            'agent_email' => $this->input->post('agent_email'),
            'agent_phone' => $this->input->post('agent_phone'),
            'agent_fax' => $this->input->post('agent_fax'),
            'agent_city' => $this->input->post('agent_city'),
            'agent_country' => $this->input->post('agent_country'),
            'agent_zip_code' => $this->input->post('agent_zip_code'),
            'agent_address' => $this->input->post('agent_address'),
            'is_deleted' => 0
        );

        $this->db->trans_start();
        $this->db
            ->set($data)
            ->insert('es_agent');
        //$id = $this->db->insert_id();
        $this->db->trans_complete();

        $this->session->set_flashdata('message', 'Agent has been created successfully');

        if ($this->input->post('back_url') && $this->input->post('back_url') != '') {
            redirect(base_url($this->input->post('back_url')));
        }
        else {
            redirect(base_url('agent.html'));
        }
    }

    function crd_add_validate() {

        $this->form_validation->set_rules('agent_company', 'agent_company*company', 'trim|required|is_unique[es_customers.company]');
        $this->form_validation->set_rules('agent_name', 'agent_name*name', 'trim|required');
        $this->form_validation->set_rules('agent_designation', 'agent_designation*designation', 'trim');
        $this->form_validation->set_rules('agent_email', 'agent_email*email', 'trim|required|valid_email|is_unique[es_customers.email]');
        $this->form_validation->set_rules('agent_phone', 'agent_phone*telephone', 'trim|numeric|min_length[8]');
        $this->form_validation->set_rules('agent_fax', 'agent_fax*fax', 'trim|numeric');
        $this->form_validation->set_rules('agent_country', 'agent_country*country', 'trim');
        $this->form_validation->set_rules('agent_city', 'agent_city*city', 'trim');
        $this->form_validation->set_rules('agent_zip_code', 'agent_zip_code*zip_code', 'trim|numeric');
        $this->form_validation->set_rules('agent_address', 'agent_address*address', 'trim');



        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else
            return $this->common->doError(func_num_args(), "done", true);
    }



    function crd_edit() {
		$this->checkEditId();
		$this->load->view('includes/after_login/head');
		$this->load->view('agent/edit');
	}



	function crd_edit_submit() {
		if ($this->crd_edit_validate() !== true)
			show_404();

		$data = array(
			'agent_company' => $this->input->post('agent_company'),
			'agent_name' => $this->input->post('agent_name'),
			'agent_designation' => $this->input->post('agent_designation'),
			'agent_email' => $this->input->post('agent_email'),
            'agent_phone' => $this->input->post('agent_phone'),
            'agent_fax' => $this->input->post('agent_fax'),
            'agent_city' => $this->input->post('agent_city'),
            'agent_country' => $this->input->post('agent_country'),
            'agent_zip_code' => $this->input->post('agent_zip_code'),
            'agent_address' => $this->input->post('agent_address')
        );


		$this->db->trans_start();
		$this->db
			->set($data)
			->where('id', $this->formdata->id)
			->update('es_agent');

		$this->db->trans_complete();
		$this->session->set_flashdata('message', 'User has been updated successfully');
		redirect(base_url('agent.html'));
	}

	function crd_edit_validate() {
		$this->checkEditId();

        $this->form_validation->set_rules('agent_company', 'agent_company*agent company', 'trim|required|set_edit_mood[id.' . $this->formdata->id . ']|is_unique[es_agent.agent_company]');
        $this->form_validation->set_rules('agent_name', 'agent_name*agent name', 'trim|required');
        $this->form_validation->set_rules('agent_designation', 'agent_designation*agent designation', 'trim');
        $this->form_validation->set_rules('agent_email', 'agent_email*agent_email', 'trim|required|valid_email');
        $this->form_validation->set_rules('agent_telephone', 'agent_telephone*agent telephone', 'trim|numeric|min_length[8]');
        $this->form_validation->set_rules('agent_fax', 'agent_fax*agent fax', 'trim|numeric');
        $this->form_validation->set_rules('agent_country', 'agent_country*agent country', 'trim');
        $this->form_validation->set_rules('agent_city', 'agent_city*agent city', 'trim');
        $this->form_validation->set_rules('agent_zip_code', 'agent_zip_code*agent zip_code', 'trim|numeric');
        $this->form_validation->set_rules('agent_address', 'agent_address*agent address', 'trim');


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

		$check_booking = $this->db
			->where('agent_id', $this->formdata->id)
			->where('is_canceled', 0)
			->count_all_results('es_exhibition_booking');

		if ($check_booking > 0) {
			$this->session->set_flashdata('error', 'Agent cannot be deleted, Please cancel all his booking before deleting agent');
			redirect(base_url('agent.html'));
			die();
		}

		$this->db
			->where('id', $this->formdata->id)
			->update('es_agent', array(
				'is_deleted' => 1,
				'deleted_by' => $this->userdata->id,
				'deleted_on' => date('Y-m-d H:i:s')
			));

		$this->session->set_flashdata('message', 'Agent has been deleted successfully');
		redirect(base_url('agent.html'));
	}
}