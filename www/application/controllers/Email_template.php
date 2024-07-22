<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Email_template extends MY_Controller
{
	protected function rule()
	{
		$this->activateRightsSystem();
		$crd = array(
			'crd_list, exhibition_crd_list,crd_add,crd_add_submit' => array(
				'rule' => '@'
			),
			'crd_list_datatable, exhibition_crd_list_datatable,crd_add_validate' => array(
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
			),
			'crd_edit_submit' => array(
				'rule' => '@'
			)
		);
		$this->load->model('usermdl');
		$this->myparent = base_url('locations.html');
		return array_merge($crd, $crd_dit);
	}

	protected function checkEditId()
	{
		$id = $this->input->get('id');
		if (is_null($id))
			show_404();
		$id = $this->db->escape($id);
		$key = config_item('encryption_key');
		$key = $this->db->escape($key);
		$r = $this->db
			->where("MD5(CONCAT($key,`id`)) = $id")
			->get('email_template');


		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();
	}

	function exhibition_crd_list() 
	{
		$this->load->view('includes/after_login/head');
		$this->load->view('email_template/exh_list');
	}

	function exhibition_crd_list_datatable()
	{
		$this->load->library('datatables');
        $this->datatables
            ->select('E.id,
				  E.exhibition_title,
				  L.location_title				  
				  ', false)
            ->add_column('total_emails', function ($row) {
                return $this->db
                    ->where('exhibition_id', $row['id'])
                    ->count_all_results('email_template');
            }, NULL)
            ->add_column('col_action', function ($row) {
                $id = $row['id'];
                $html = '<a href="' . base_url() . 'email_template.html?exhibition_id=' . $id . '">View Emails</a>';

                return "<div class='text-center'>{$html}</div>";
            }, NULL)
            ->where('E.is_deleted', 0)
            ->join('es_locations as L', 'E.location_id = L.id', 'LEFT')
            ->from('es_exhibitions as E');

        print ($this->datatables->generate());
	}

	function crd_list()
	{
		$exhibition_id = $this->input->get('exhibition_id');
		$this->load->view('includes/after_login/head');
		$this->load->view('email_template/list');
	}


	function crd_list_datatable()
	{
		$exhibition_id = $this->input->get('exhibition_id');
		
		$this->load->library('datatables');
		$this->datatables
			->select('
				email_template.id,
				email_template.title,
				email_template.subject,
				email_template.updated_on,
				CONCAT(users.user_first_name, " ", users.user_last_name) as last_update_by
			', false)

			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'email_template-edit.html?id=' . urlencode(myid($id)) . '">Edit '.$exhibition_id.'</a>';
				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where("email_template.exhibition_id" , $exhibition_id)
			->from('email_template')
			->join('users', 'users.id = email_template.updated_by', 'LEFT');

		print($this->datatables->generate());
	}

	function crd_add() {
        $this->load->view('includes/after_login/head');
        $this->load->view('email_template/add');
    }

	function crd_add_validate()
	{
		$this->form_validation->set_rules('email_template_subject', 'email_template_subject*Email Subject', 'trim|required');

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function crd_add_submit() {
		if ($this->crd_add_validate() !== true)
            show_404();

        $data = array(
            'exhibition_id' => $this->input->post('exhibition_id'),
            'title' => $this->input->post('email_template_title'),
            'message' => $this->input->post('email_template_message'),
            'subject' => $this->input->post('email_template_subject'),
            'created_on' => date('Y-m-d H:i:s'),
            'placeholders' => $this->input->post('email_template_placeholder'),
            'updated_by' => $this->userdata->id,
            'description' => $this->input->post('email_template_discription'),
            'updated_on' => date('Y-m-d H:i:s'),
        );

		$this->db->trans_start();
        $this->db
            ->set($data)
            ->insert('email_template');
        //$id = $this->db->insert_id();
        $this->db->trans_complete();

        $this->session->set_flashdata('message', 'Email template has been Addedd successfully');
		redirect(base_url('email_template.html?exhibition_id='.$this->input->post('exhibition_id')));
	}

	function crd_edit()
	{
		$this->checkEditId();
		$this->load->view('includes/after_login/head');
		$this->load->view('email_template/edit');
	}

	function crd_edit_validate()
	{
		$this->checkEditId();

		//$this->form_validation->set_rules('email_template_title', 'email_template_title*Email Title', 'trim|required');
		$this->form_validation->set_rules('email_template_subject', 'email_template_subject*Email Subject', 'trim|required');

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function crd_edit_submit()
	{
		if ($this->crd_edit_validate() !== true)
			show_404();


		$data = array(
			//'title' => $this->input->post('email_template_title'),
			'subject' => $this->input->post('email_template_subject'),
			'message' => $this->input->post('email_template_message'),
			'updated_on' => date('Y-m-d H:i:s'),
			'updated_by' => $this->userdata->id,
		);


		$this->db
			->set($data)
			->where('id', $this->formdata->id)
			->update('email_template');

		$this->session->set_flashdata('message', 'Email template has been updated successfully');
		redirect(base_url('email_template.html?exhibition_id='.$this->formdata->exhibition_id));
	}

}
