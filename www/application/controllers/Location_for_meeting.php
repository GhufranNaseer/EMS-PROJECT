<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Location_for_meeting extends MY_Controller
{
	protected function rule()
	{
		$this->activateRightsSystem();
		$crd = array(
			'crd_list, location_list,crd_add,crd_add_submit' => array(
				'rule' => '@'
			),
			'crd_list_datatable, location_list_datatable,crd_add_validate' => array(
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
			->get('location_for_meeting');


		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();
	}

	function crd_list()
	{
		$this->load->view('includes/after_login/head');
		$this->load->view('location_for_meeting/list');
	}


	function crd_list_datatable()
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
                    ->count_all_results('location_for_meeting');
            }, NULL)
            ->add_column('col_action', function ($row) {
                $id = $row['id'];
				$html = '<a href="' . base_url() . 'Location_meeting.html?exhibition_id=' . $id . '&type=meeting_location" class="btn btn-link">Meeting Locations</a>';
                $html .= '<a href="' . base_url() . 'Location_meeting.html?exhibition_id=' . $id . '&type=mou_location" class="btn btn-link">MoU Locations</a> ';
                return "<div class='text-center'>{$html}</div>";
            }, NULL)
            ->where('E.is_deleted', 0)
            ->join('es_locations as L', 'E.location_id = L.id', 'LEFT')
            ->from('es_exhibitions as E');

        print ($this->datatables->generate());
	}

	function location_list()
	{
		$this->load->view('includes/after_login/head');
		$this->load->view('location_for_meeting/location_list');
	}

	function location_list_datatable() {
		$type = $this->input->get('type');
		$exhibition_id = $this->input->get('exhibition_id');
		$this->load->library('datatables');
        $this->datatables
            ->select('E.id,
				  E.location,
				  E.type,
				  ', false)
			->unset_column('E.type')
            ->add_column('col_action', function ($row) {
                $id = $row['id'];
                $html = '<a href="' . base_url() . 'Location_for_meeting-edit.html?id=' . urlencode(myid($id))  . '&type='.$row['type'].'">Edit</a>';
				return "<div class='text-center'>{$html}</div>";
            }, NULL)
            ->where('E.exhibition_id', $exhibition_id)
			->where('E.type', $type)
            ->from('location_for_meeting as E');

        print ($this->datatables->generate());
	}

	function crd_add() {
        $this->load->view('includes/after_login/head');
        $this->load->view('location_for_meeting/add');
    }

	function crd_add_validate()
	{
		$this->form_validation->set_rules('location', 'location*location', 'trim|required');

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
            'type' => $this->input->post('type'),
            'location' => $this->input->post('location'),
            'created_on' => date('Y-m-d H:i:s')
        );

		$this->db->trans_start();
        $this->db
            ->set($data)
            ->insert('location_for_meeting');
        //$id = $this->db->insert_id();
        $this->db->trans_complete();

        $this->session->set_flashdata('message', 'Location has been added successfully');
		redirect(base_url('Location_meeting.html?exhibition_id='.$this->input->post('exhibition_id').'&type='.$this->input->post('type')));
	}

	function crd_edit()
	{
		$this->checkEditId();
		$this->load->view('includes/after_login/head');
		$this->load->view('location_for_meeting/edit');
	}

	function crd_edit_validate()
	{
		$this->checkEditId();

		//$this->form_validation->set_rules('email_template_title', 'email_template_title*Email Title', 'trim|required');
		$this->form_validation->set_rules('location', 'location*location', 'trim|required');

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
			'location' => $this->input->post('location')
		);


		$this->db
			->set($data)
			->where('id', $this->formdata->id)
			->update('location_for_meeting');

		$this->session->set_flashdata('message', 'Location been updated successfully');
		redirect(base_url('Location_meeting.html?exhibition_id='.$this->formdata->exhibition_id.'&type='.$this->formdata->type));
	}

}
