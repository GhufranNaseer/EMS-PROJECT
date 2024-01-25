<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Event_form extends MY_Controller {
	protected function rule() {
		$this->activateRightsSystem();
		$crd = array(
			'crd_list,
			crd_add,
			crd_add_submit' => array(
				'rule' => '@'
			),
			'crd_list_datatable,
			crd_add_validate,
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
		$this->load->view('event_form/list');
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
					->where('is_active', 1)
					->count_all_results('es_exhibition_forms');
			}, NULL)
			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'event_form-add.html?id=' . urlencode(myid($id)) . '">View Forms</a>';

				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where('E.is_deleted', 0)
			->join('es_locations as L', 'E.location_id = L.id', 'LEFT')
			->from('es_exhibitions as E');

		print ($this->datatables->generate());
	}



	function crd_add() {
		$this->checkEditId();

		$this->load->view('includes/after_login/head');
		$this->load->view('event_form/add');
	}

    function  crd_add_submit(){
        $this->checkEditId();

        $exhibition_id = $this->formdata->id;
        $data=array();

        $this->db
            ->where('exhibition_id', $exhibition_id)
            ->delete('es_exhibition_forms');

        foreach ($this->input->post('forms') as $form){

            $data[]=array(
              'exhibition_id' => $exhibition_id,
              'form_id' => $form['form_id'],
               'expiry_date' => date('Y-m-d', strtotime($form['expiry_date'])),
                'is_active' => (isset($form['is_active']) ? 1 : 0)
            );
        }

        $this->db->insert_batch('es_exhibition_forms',$data);
        $this->session->set_flashdata('message', 'Form has been updated successfully');
        redirect(base_url('event_form.html'));


    }

    function crd_add_validate(){
        $this->checkEditId();

        $this->form_validation->set_rules('forms[]', 'forms[]*form expiry date', 'trim|required');

        if ($this->input->post('forms')) {
            foreach ($this->input->post('forms')as $form) {
                if (!$form['expiry_date'] || $form['expiry_date'] == '') {
                    return $this->common->doError(func_num_args(), 'Expire date is required');
                }
            }
        }

        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else
            return $this->common->doError(func_num_args(), "done", true);

    }

}