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
			'crd_edit,crd_delete' => array(
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

		print($this->datatables->generate());
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

			->add_column('col_action', function ($row) use ($exhibition_id) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'email_template-edit.html?id=' . urlencode(myid($id)) . '">Edit</a>';
				if (empty($exhibition_id)) {
					$html .= ' | <a href="' . base_url() . 'email_template-delete.html?id=' . urlencode(myid($id)) . '" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';
				}
				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where("email_template.exhibition_id", $exhibition_id)
			->from('email_template')
			->join('users', 'users.id = email_template.updated_by', 'LEFT');

		print($this->datatables->generate());
	}

	function crd_add()
	{
		$this->load->view('includes/after_login/head');
		$this->load->view('email_template/add');
	}

	function crd_add_validate()
	{
		$this->form_validation->set_rules('email_template_title', 'email_template_title*Email Title', 'trim|required');
		$this->form_validation->set_rules('email_template_subject', 'email_template_subject*Email Subject', 'trim|required');

		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		} else {
			$exhibition_id = $this->input->post('exhibition_id');
			$title = $this->input->post('email_template_title');

			// Check for duplicate template of same type for the selected exhibition
			$exists = $this->db
				->where('exhibition_id', $exhibition_id)
				->where('title', $title)
				->count_all_results('email_template');

			if ($exists > 0) {
				return $this->common->doError(func_num_args(), "A template of type '" . str_replace('_', ' ', $title) . "' already exists for this exhibition. Please edit the existing template instead.");
			}

			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function crd_add_submit()
	{
		if ($this->crd_add_validate() !== true)
			show_404();


		$DESCRIPTIONS = array(
			'EVENT_INVITATION' => 'This template is use to send login credentials and login link to new customers.',
			'RESET_PASSWORD_LINK' => 'This template is use to send password reset link to user.',
			'APPOINTMENT_SCHEDULE' => 'This template is use to send appointment schedule email notification.',
			'APPOINTMENT_ACCEPTED' => 'This template is use to send appointment accept email notification.',
			'APPOINTMENT_CANCELED' => 'This template is use to send appointment cancel email notification.',
			'APPOINTMENT_RE_SCHEDULE' => 'This template is use to send appointment re-schedule email notification.',
			'MOU_SIGNING_CANCELED' => 'This template is use to send MoU sign cancel email notification.',
			'MOU_SIGNING_ACCEPTED' => 'This template is use to send MoU sign accepted email notification.',
			'MOU_SIGNING_RE_SCHEDULE' => 'This template is use to send MoU sign re-schedule email notification.',
			'THANK_YOU_EXHIBITOR' => 'This template is used to send a thank you email to exhibitors after event completion.',
		);

		$PLACEHOLDERS = array(
			'EVENT_INVITATION' => '{EMAIL},{PASSWORD},{LOGIN_URL},{EVENT_NAME}',
			'RESET_PASSWORD_LINK' => '{PASSWORD_RESET_LINK},{CUSTOMER_COMPANY},{CUSTOMER_NAME},{CUSTOMER_EMAIL},{EVENT_NAME}',
			'APPOINTMENT_SCHEDULE' => '{EVENT_NAME},{NAME},{EMAIL},{PHONE},{COMPANY},{SENDER_NAME},{SENDER_EMAIL},{SENDER_PHONE},{SENDER_COMPANY},{SENDER_WEBSITE},{APPOINTMENT_TIME},{APPOINTMENT_AGENDA}',
			'APPOINTMENT_ACCEPTED' => '{EVENT_NAME},{NAME},{EMAIL},{PHONE},{COMPANY},{SENDER_NAME},{SENDER_EMAIL},{SENDER_PHONE},{SENDER_COMPANY},{SENDER_WEBSITE},{APPOINTMENT_TIME},{APPOINTMENT_AGENDA}',
			'APPOINTMENT_CANCELED' => '{EVENT_NAME},{NAME},{EMAIL},{PHONE},{COMPANY},{SENDER_NAME},{SENDER_EMAIL},{SENDER_PHONE},{SENDER_COMPANY},{SENDER_WEBSITE},{APPOINTMENT_TIME},{APPOINTMENT_AGENDA}',
			'APPOINTMENT_RE_SCHEDULE' => '{EVENT_NAME},{NAME},{EMAIL},{PHONE},{COMPANY},{SENDER_NAME},{SENDER_EMAIL},{SENDER_PHONE},{SENDER_COMPANY},{SENDER_WEBSITE},{APPOINTMENT_TIME},{APPOINTMENT_AGENDA}',
			'MOU_SIGNING_CANCELED' => '{NAME},{EMAIL},{PHONE},{COMPANY},{SENDER_NAME},{SENDER_EMAIL},{SENDER_PHONE},{SENDER_COMPANY},{SENDER_WEBSITE},{SCHEDULE_TIME},{DESCRIPTION},{COMMERCIAL_VALUE}',
			'MOU_SIGNING_ACCEPTED' => '{NAME},{EMAIL},{PHONE},{COMPANY},{SENDER_NAME},{SENDER_EMAIL},{SENDER_PHONE},{SENDER_COMPANY},{SENDER_WEBSITE},{SCHEDULE_TIME},{DESCRIPTION},{COMMERCIAL_VALUE}',
			'MOU_SIGNING_RE_SCHEDULE' => '{NAME},{EMAIL},{PHONE},{COMPANY},{SENDER_NAME},{SENDER_EMAIL},{SENDER_PHONE},{SENDER_COMPANY},{SENDER_WEBSITE},{SCHEDULE_TIME},{DESCRIPTION},{COMMERCIAL_VALUE}',
			'THANK_YOU_EXHIBITOR' => '{EVENT_NAME},{EXHIBITOR_NAME},{EXHIBITOR_COMPANY}',
		);

		$data = array(
			'exhibition_id' => $this->input->post('exhibition_id'),
			'title' => $this->input->post('email_template_title'),
			'message' => $this->input->post('email_template_message'),
			'subject' => $this->input->post('email_template_subject'),
			'created_on' => date('Y-m-d H:i:s'),
			'placeholders' => $PLACEHOLDERS[$this->input->post('email_template_title')],
			'updated_by' => $this->userdata->id,
			'description' => $DESCRIPTIONS[$this->input->post('email_template_title')],
			'updated_on' => date('Y-m-d H:i:s'),
		);

		$this->db->trans_start();
		$this->db
			->set($data)
			->insert('email_template');
		//$id = $this->db->insert_id();
		$this->db->trans_complete();

		$this->session->set_flashdata('message', 'Email template has been Addedd successfully');
		redirect(base_url('email_template.html?exhibition_id=' . $this->input->post('exhibition_id')));
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
		redirect(base_url('email_template.html?exhibition_id=' . $this->formdata->exhibition_id));
	}

	function crd_delete()
	{
		$this->checkEditId();
		$exhibition_id = $this->formdata->exhibition_id;

		// Prevent deletion of event-specific templates (only allow if exhibition_id is NULL or 0)
		if (!empty($exhibition_id)) {
			$this->session->set_flashdata('error', 'You cannot delete event-specific templates.');
			redirect(base_url('email_template.html?exhibition_id=' . $exhibition_id));
			return;
		}

		$this->db
			->where('id', $this->formdata->id)
			->delete('email_template');

		$this->session->set_flashdata('message', 'Email template has been deleted successfully');
		redirect(base_url('email_template.html?exhibition_id=' . $exhibition_id));
	}
}
