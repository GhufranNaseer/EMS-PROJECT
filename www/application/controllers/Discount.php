<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Discount extends MY_Controller {
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
		$this->myparent = base_url('discount.html');
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
			->get('es_discount_offers');
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();

	}

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('discount/list');
	}


	function crd_list_datatable() {
		$this->load->library('datatables');
		$this->datatables
			->select('id, 
				  offer_title,  
				  discount, 
				  ', false)
			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'discount-edit.html?id=' . urlencode(myid($id)) . '">Edit</a>';
				if (ALLOW_DELETION)
					$html .= ' | <a href="' . base_url() . 'discount-delete.html?id=' . urlencode(myid($id)) . '" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';
				return "<center>{$html}</center>";
			}, NULL)
			->from('es_discount_offers')
			->where('is_deleted', 0);
		print ($this->datatables->generate());
	}



    function crd_add() {
        $this->load->view('includes/after_login/head');
        $this->load->view('discount/add');
    }

    function crd_add_submit() {
        if ($this->crd_add_validate() !== true)
            show_404();

        $data = array(
            'offer_title ' => $this->input->post('offer_title'),
            'discount' => $this->input->post('discount'),
        );

        $this->db->trans_start();
        $this->db
            ->set($data)
            ->insert('es_discount_offers');
        //$id = $this->db->insert_id();
        $this->db->trans_complete();

        $this->session->set_flashdata('message', 'Discount has been created successfully');

        if ($this->input->post('back_url') && $this->input->post('back_url') != '') {
        	redirect(base_url($this->input->post('back_url')));
		}
        else {
			redirect(base_url('discount.html'));
		}
    }

    function crd_add_validate() {

        $this->form_validation->set_rules('offer_title', 'offer_title*offer title', 'trim|required');
        $this->form_validation->set_rules('discount', 'discount*discount', 'trim|required');



        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else
            return $this->common->doError(func_num_args(), "done", true);
    }

	function crd_edit() {
		$this->checkEditId();
		$this->load->view('includes/after_login/head');
		$this->load->view('discount/edit');
	}



	function crd_edit_submit() {
		if ($this->crd_edit_validate() !== true)
			show_404();
		$data = array(
            'offer_title ' => $this->input->post('offer_title'),
            'discount' => $this->input->post('discount'),

        );


		$this->db->trans_start();
		$this->db
			->set($data)
			->where('id', $this->formdata->id)
			->update('es_discount_offers');

		$this->db->trans_complete();
		$this->session->set_flashdata('message', 'Discount has been updated successfully');
		redirect(base_url('discount.html'));
	}

	function crd_edit_validate() {
		$this->checkEditId();

        $this->form_validation->set_rules('offer_title', 'offer_title*offer title', 'trim|required');
        $this->form_validation->set_rules('discount', 'discount*discount', 'trim|required');

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

        $this->db
            ->where('id', $this->formdata->id)
            ->update('es_discount_offers', array(
                'is_deleted' => 1,
                'deleted_by' => $this->userdata->id,
                'deleted_on' => date('Y-m-d H:i:s')
            ));

        $this->session->set_flashdata('message', 'User has been deleted successfully');
        redirect(base_url('discount.html'));
    }
}