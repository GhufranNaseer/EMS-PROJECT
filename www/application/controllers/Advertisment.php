<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Advertisment extends MY_Controller {
    protected function rule() {
        $this->activateRightsSystem();
        $crd = array(
            'crd_list,
			advertisment_list,
			crd_add,
			crd_add_submit,
			crd_edit,
			crd_edit_submit,
			crd_delete' => array(
                'rule' => '@'
            ),
            'crd_list_datatable,
			advertisment_list_datatable,
			crd_add_validate,
			crd_edit_validate
			' => array(
                'rule' => '@',
                'ajaxOnly' => true
            )
        );
        $this->load->model('usermdl');
        $this->myparent = base_url('advertisment.html');
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
        $this->load->view('event_advertisment/list');
    }

    function crd_list_datatable() {
        $this->load->library('datatables');
        $this->datatables
            ->select('E.id,
				CONCAT("<img src=\'", E.event_logo, "\' height=\'40px\' />") as event_logo,
				  E.exhibition_title,
				  L.location_title,				  
				  ', false)
            ->add_column('total_notification', function ($row) {
                $id = $row['id'];
                $html = $this->db
                    ->where('exhibition_id', $id)
					->where('is_deleted', 0)
					->count_all_results('es_exhibition_advertisment');

                return "<div class='text-center'>{$html}</div>";
            }, NULL)
            ->add_column('col_action', function ($row) {
                $id = $row['id'];
                $html = '<a href="' . base_url() . 'advertisment-event.html?id=' . urlencode(myid($id)) . '">View Advertisements</a>';

                return "<div class='text-center'>{$html}</div>";
            }, NULL)
            ->where('E.is_deleted', 0)
            ->join('es_locations as L', 'E.location_id = L.id', 'LEFT')
            ->from('es_exhibitions as E');

        print ($this->datatables->generate());
    }


    function advertisment_list() {
        $exhibition_id = $this->input->get('id');
        if (!isset($exhibition_id)) {
            show_404();
        }

        $this->load->view('includes/after_login/head');
        $this->load->view('event_advertisment/advertisment_list');
    }

    function advertisment_list_datatable() {
        $exhibition_id = $this->input->get('id');

        $this->load->library('datatables');
        $this->datatables
            ->select('id,
                      CONCAT("<img src=\'", attachment, "\' height=\'40px\' />") as attachment,
				      title,
				      created_on
				  ', false)

            ->add_column('col_action', function ($row) {
                $id = $row['id'];
                $html = '<a href="' . base_url() . 'advertisment-edit.html?id=' . urlencode(myid($id)) . '">Edit</a>';
                if (ALLOW_DELETION)
                    $html .= ' | <a href="' . base_url() . 'advertisment-delete.html?id=' . urlencode(myid($id)) . '" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';

                return "<div class='text-center'>{$html}</div>";
            }, NULL)
            ->where(mycolumn('exhibition_id'), $exhibition_id)
            ->where('is_deleted', 0)
            ->from('es_exhibition_advertisment');

        print ($this->datatables->generate());
    }


    function crd_add() {
        $this->checkEditId();

        $this->load->view('includes/after_login/head');
        $this->load->view('event_advertisment/add');
    }

    function crd_add_validate() {
        $this->checkEditId();
        $this->form_validation->set_rules('title', 'title*title', 'trim|required');
        $this->form_validation->set_rules('attachment_link', 'attachment_link*attachment link', 'trim|required');
        $this->form_validation->set_rules('expire_on', 'expire_on*expire on date', 'trim|required');
        $this->form_validation->set_rules('attachment[0]', 'attachment[0]*attachment', 'trim');

        if ($this->form_validation->run() == false) {
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        }
        else {
            return $this->common->doError(func_num_args(), "done", true);
        }
    }

    function crd_add_submit() {
        if ($this->crd_add_validate() !== true)
            show_404();

        $exhibition_id = $this->formdata->id;


        $data = array(
            'exhibition_id' => $exhibition_id,
            'title' => $this->input->post('title'),
            'attachment_link' => $this->input->post('attachment_link'),
            'attachment' => $this->funcs->make_image_string($this->input->post('attachment')),
            'expire_on' => $this->input->post('expire_on'),
            'created_on' => date('Y-m-d H:i:s'),
            'is_deleted' => 0,
        );

        $this->db->trans_start();
        $this->db
            ->set($data)
            ->insert('es_exhibition_advertisment');
        $this->db->trans_complete();

        $this->session->set_flashdata('message', 'Advertisement has been add successfully');
        redirect(base_url('advertisment-event.html?id=' . myid($exhibition_id)));
    }


    function crd_edit() {
        $id = $this->input->get('id');
        if (is_null($id))
            show_404();
        $id = $this->db->escape($id);
        $key = config_item('encryption_key');
        $key = $this->db->escape($key);
        $r = $this->db
            ->where("MD5(CONCAT($key,`id`)) = $id")
            ->get('es_exhibition_advertisment');
        if ($r->num_rows() == 0)
            show_404();

        $this->formdata = $r->row();

        $this->load->view('includes/after_login/head');
        $this->load->view('event_advertisment/edit');
    }

	function crd_edit_validate(){
		$id = $this->input->get('id');
		if (is_null($id))
			show_404();
		$id = $this->db->escape($id);
		$key = config_item('encryption_key');
		$key = $this->db->escape($key);
		$r = $this->db
			->where("MD5(CONCAT($key,`id`)) = $id")
			->get('es_exhibition_advertisment');
		if ($r->num_rows() == 0)
			show_404();

		$this->formdata = $r->row();


        $this->form_validation->set_rules('title', 'title*title', 'trim|required');
        $this->form_validation->set_rules('attachment_link', 'attachment_link*attachment link', 'trim|required');
        $this->form_validation->set_rules('expire_on', 'expire_on*expire on date', 'trim|required');
        $this->form_validation->set_rules('attachment[0]', 'attachment[0]*attachment', 'trim');

        if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		}
		else {
			return $this->common->doError(func_num_args(), "done", true);
		}
	}

    function crd_edit_submit() {
        if ($this->crd_edit_validate() !== true)
            show_404();


        $data = array(

            'title' => $this->input->post('title'),
            'attachment_link' => $this->input->post('attachment_link'),
            'expire_on' => $this->input->post('expire_on'),
			'attachment' => $this->funcs->make_image_string($this->input->post('attachment')),
        );

        $this->db->trans_start();
        $this->db
            ->set($data)
            ->where('id', $this->formdata->id)
            ->update('es_exhibition_advertisment');

        $this->db->trans_complete();
        $this->session->set_flashdata('message', 'Advertisment has been updated successfully');
		redirect(base_url('advertisment-event.html?id=' . myid($this->formdata->exhibition_id)));
    }


    function crd_delete() {
        if (!ALLOW_DELETION)
            show_404();

		$id = $this->input->get('id');
		if (is_null($id))
			show_404();
		$id = $this->db->escape($id);
		$key = config_item('encryption_key');
		$key = $this->db->escape($key);
		$r = $this->db
			->where("MD5(CONCAT($key,`id`)) = $id")
			->get('es_exhibition_advertisment');
		if ($r->num_rows() == 0)
			show_404();

		$this->formdata = $r->row();

        $this->db
            ->where('id', $this->formdata->id)
            ->update('es_exhibition_advertisment', array(
                'is_deleted' => 1,
                'deleted_by' => $this->userdata->id,
                'deleted_on' => date('Y-m-d H:i:s')
            ));

        $this->session->set_flashdata('message', 'Advertisement has been deleted successfully');
        redirect(base_url('advertisment.html'));
    }


}