<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MY_Controller {
	protected function rule() {
		$this->activateRightsSystem();
		$crd = array(
			'view_settings,add_business_sector,remove_business_sector' => array(
				'rule' => '@'
			),
			'add_business_sector_validate,get_main_business_type' => array(
				'rule' => '@',
				'ajaxOnly' => true
			)
		);
        $crd_dit = array(
            'crd_edit_validate' => array(
                'ajaxOnly' => true,
                'rule' => '@'
            )
        ,
            'tax_update,crd_edit_submit' => array(
                'rule' => '@'
            )
        );
		$this->load->model('usermdl');
		$this->myparent = base_url('other-settings.html');
		return array_merge($crd, $crd_dit);
	}

	function view_settings() {
		$this->load->view('includes/after_login/head');
		$this->load->view('settings/view_settings');
	}

	function add_business_sector_validate() {
		$this->form_validation->set_rules('type', 'type*type', 'trim|required');
		$this->form_validation->set_rules('data', 'data*', 'trim|required');

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function add_business_sector() {
		if ($this->add_business_sector_validate() !== true)
			show_404();

		$type = $this->input->post('type');
		if ($type == 'main_business_type') {
		    $type .= '_' . $this->input->post('sector_id');
        }

        if ($type == 'main_business_area') {
		    $type .= '_' . $this->input->post('sector_id');
		    $type .= '_' . $this->input->post('main_business_area');
        }

		$this->db
			->insert('input_data_list', array(
			'type' => $type,
			'data' => $this->input->post('data'),
			));

		$this->session->set_flashdata('message', 'Data added successfully');
		redirect(base_url('other-settings.html'));
	}

	function remove_business_sector() {
		$id = $this->input->get('id');

		if (is_null($id)) {
			show_404();
			die();
		}

		$this->db
			->where('id', $id)
			->delete('input_data_list');

		$this->session->set_flashdata('message', 'Data removed successfully');
		redirect(base_url('other-settings.html'));
	}

	function get_main_business_type() {
	    $sector = $this->input->post('sector_id');

        $rows = $this->db
            ->where('type', 'main_business_type_' . $sector)
            ->get('input_data_list')
            ->result();

        if ($rows) {
            echo json_encode(array(
                'error' => false,
                'data' => $rows
            ));
        } else {
            echo json_encode(array(
                'error' => true,
                'message' => 'error'
            ));
        }
    }

    function tax_update() {
		$this->load->view('includes/after_login/head');
		$this->load->view('settings/tax_update');
	}

    function crd_edit_submit() {
        if ($this->crd_edit_validate() !== true)
            show_404();

       // print_r($this->input->post('text_id'));die();
        foreach ($this->input->post('text_id') as $key => $id) {
        $data = array(
            'gst_rate' => $this->input->post('gst_rate')[$key],
            'wht_rate' => $this->input->post('wht_rate')[$key],
        );

           // print_r($data);die();
        //$this->db->trans_start();

        $this->db
            ->set($data)
            ->where('id', $id)
            ->update('es_tax_rate');

            $this->db->trans_complete();
        }

        $this->session->set_flashdata('message', 'User has been updated successfully');
        redirect(base_url('other-settings.html'));
	}

    function crd_edit_validate() {
    //  print_r($this->input->post('text_id'));die();
       $this->form_validation->set_rules('text_id[]', 'text_id[]*First Name', 'trim|required');
        //$this->form_validation->set_rules('gst_rate[]', 'gst_rate[]*GST rate', 'trim|required');
        //$this->form_validation->set_rules('wht_rate[]', 'wht_rate[]*WHT Rate', 'trim|required');

        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else {
            return $this->common->doError(func_num_args(), "done", true);
        }
    }

}