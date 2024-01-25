<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends MY_Controller {
    protected function rule() {
        $this->activateRightsSystem();
        $crd = array(
            'crd_list,
			notification_list,
			crd_add,
			crd_add_submit,
			crd_edit,
			crd_edit_submit,
			crd_delete' => array(
                'rule' => '@'
            ),
            'crd_list_datatable,
			notification_list_datatable,
			crd_add_validate,
			crd_edit_validate
			' => array(
                'rule' => '@',
                'ajaxOnly' => true
            )
        );
        $this->load->model('usermdl');
        $this->myparent = base_url('notification.html');
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
        $this->load->view('event_notification/list');
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
					->count_all_results('es_exhibition_notification');

                return "<div class='text-center'>{$html}</div>";
            }, NULL)
            ->add_column('col_action', function ($row) {
                $id = $row['id'];
                $html = '<a href="' . base_url() . 'notification2.html?id=' . urlencode(myid($id)) . '">View Notifications</a>';

                return "<div class='text-center'>{$html}</div>";
            }, NULL)
            ->where('E.is_deleted', 0)
            ->join('es_locations as L', 'E.location_id = L.id', 'LEFT')
            ->from('es_exhibitions as E');

        print ($this->datatables->generate());
    }


    function notification_list() {
        $exhibition_id = $this->input->get('id');
        if (!isset($exhibition_id)) {
            show_404();
        }

        $this->load->view('includes/after_login/head');
        $this->load->view('event_notification/notification_list');
    }

    function notification_list_datatable() {
        $exhibition_id = $this->input->get('id');

        $this->load->library('datatables');
        $this->datatables
            ->select('id,
				      UCASE(type),
				      title,
				      created_on
				  ', false)

            ->add_column('col_action', function ($row) {
                $id = $row['id'];
                $html = '<a href="' . base_url() . 'notification-edit.html?id=' . urlencode(myid($id)) . '">Edit</a>';
                if (ALLOW_DELETION)
                    $html .= ' | <a href="' . base_url() . 'notification-delete.html?id=' . urlencode(myid($id)) . '" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';

                return "<div class='text-center'>{$html}</div>";
            }, NULL)
            ->where(mycolumn('exhibition_id'), $exhibition_id)
            ->where('is_deleted', 0)
            ->from('es_exhibition_notification');

        print ($this->datatables->generate());
    }


    function crd_add() {
        $this->checkEditId();

        $this->load->view('includes/after_login/head');
        $this->load->view('event_notification/add');
    }

    function crd_add_validate() {
        $this->checkEditId();
        $this->form_validation->set_rules('title', 'title*title', 'trim|required');
        $this->form_validation->set_rules('type', 'type*type', 'trim|required');

		if ($this->input->post('type')) {
			if ($this->input->post('type') == 'update') {
				$this->form_validation->set_rules('description', 'description*description', 'trim|required');
				$this->form_validation->set_rules('attachment[0]', 'attachment[0]*attachment', 'trim');

			} else if ($this->input->post('type') == 'downloads') {
				$this->form_validation->set_rules('description', 'description*description', 'trim');
				$this->form_validation->set_rules('attachment[0]', 'attachment[0]*attachment', 'trim|required');

			} else if ($this->input->post('type') == 'notification') {
				$this->form_validation->set_rules('description', 'description*description', 'trim');
				$this->form_validation->set_rules('attachment[0]', 'attachment[0]*attachment', 'trim');

			} else if ($this->input->post('type') == 'end_user_note' ||
				$this->input->post('type') == 'additional_items_terms' ||
				$this->input->post('type') == 'trade_visitor_badge_terms' ||
				$this->input->post('type') == 'hotel_reservation_terms' ||
				$this->input->post('type') == 'display_mobility_terms' ||
				$this->input->post('type') == 'vehicle_rental_terms') {
				$this->form_validation->set_rules('description', 'description*description', 'trim');
				$this->form_validation->set_rules('attachment[0]', 'attachment[0]*attachment', 'trim');
			}
		}



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
            'type' => $this->input->post('type'),
            'title' => $this->input->post('title'),
            'description' => $this->input->post('description'),
            'attachments' => $this->funcs->make_image_string($this->input->post('attachment')),
            'created_on' => date('Y-m-d H:i:s'),
        );

        $this->db->trans_start();
        $this->db
            ->set($data)
            ->insert('es_exhibition_notification');
        $this->db->trans_complete();

        $this->session->set_flashdata('message', 'Notification has been add successfully');
        redirect(base_url('notification2.html?id=' . myid($exhibition_id)));
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
            ->get('es_exhibition_notification');
        if ($r->num_rows() == 0)
            show_404();

        $this->formdata = $r->row();

        $this->load->view('includes/after_login/head');
        $this->load->view('event_notification/edit');
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
			->get('es_exhibition_notification');
		if ($r->num_rows() == 0)
			show_404();

		$this->formdata = $r->row();

		$this->form_validation->set_rules('title', 'title*title', 'trim|required');
		$this->form_validation->set_rules('type', 'type*type', 'trim|required');

		if ($this->input->post('type')) {
			if ($this->input->post('type') == 'update') {
				$this->form_validation->set_rules('description', 'description*description', 'trim|required');
				$this->form_validation->set_rules('attachment[0]', 'attachment[0]*attachment', 'trim');

			} else if ($this->input->post('type') == 'downloads') {
				$this->form_validation->set_rules('description', 'description*description', 'trim');
				$this->form_validation->set_rules('attachment[0]', 'attachment[0]*attachment', 'trim|required');

			} else if ($this->input->post('type') == 'notification') {
				$this->form_validation->set_rules('description', 'description*description', 'trim');
				$this->form_validation->set_rules('attachment[0]', 'attachment[0]*attachment', 'trim');

			} else if ($this->input->post('type') == 'end_user_note' ||
				$this->input->post('type') == 'additional_items_terms' ||
				$this->input->post('type') == 'trade_visitor_badge_terms' ||
				$this->input->post('type') == 'hotel_reservation_terms' ||
				$this->input->post('type') == 'display_mobility_terms' ||
				$this->input->post('type') == 'vehicle_rental_terms') {
				$this->form_validation->set_rules('description', 'description*description', 'trim');
				$this->form_validation->set_rules('attachment[0]', 'attachment[0]*attachment', 'trim');
			}
		}

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
            'type' => $this->input->post('type'),
            'title' => $this->input->post('title'),
            'description' => $this->input->post('description'),
			'attachments' => $this->funcs->make_image_string($this->input->post('attachment')),
        );

        $this->db->trans_start();
        $this->db
            ->set($data)
            ->where('id', $this->formdata->id)
            ->update('es_exhibition_notification');

        $this->db->trans_complete();
        $this->session->set_flashdata('message', 'Notification has been updated successfully');
		redirect(base_url('notification2.html?id=' . myid($this->formdata->exhibition_id)));
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
			->get('es_exhibition_notification');
		if ($r->num_rows() == 0)
			show_404();

		$this->formdata = $r->row();

        $this->db
            ->where('id', $this->formdata->id)
            ->update('es_exhibition_notification', array(
                'is_deleted' => 1,
                'deleted_by' => $this->userdata->id,
                'deleted_on' => date('Y-m-d H:i:s')
            ));

        $this->session->set_flashdata('message', 'User has been deleted successfully');
        redirect(base_url('notification.html'));
    }


}