<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {
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
		$this->myparent = base_url('users.html');
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
			->get('users');
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();
		if ($this->formdata->id == $this->userdata->id)
			show_404();

	}

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('user/list');
	}


	function crd_list_datatable() {
		$this->load->library('datatables');
		$this->datatables
			->select('users.id as user_id, 
				  user_first_name, 
				  user_last_name, 
				  user_email, 
				  user_phone, 
				  usergroup_title,
				  users.is_active as is_active', false)
			->unset_column('is_active')
			->add_column('active', function ($row) {
				$active = $row['is_active'];
				return ($active == 1) ? '<center><img src="' . base_url('assets/img/t.png') . '" /></center>' : '<center><img src="' . base_url('assets/img/cross.png') . '" /></center>';
			}, NULL)
			->add_column('col_action', function ($row) {
				$id = $row['user_id'];
				$html = '<a href="' . base_url() . 'user-edit.html?id=' . urlencode(myid($id)) . '">Edit</a>';
				if (ALLOW_DELETION)
					$html .= ' | <a href="' . base_url() . 'user-delete.html?id=' . urlencode(myid($id)) . '" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';
				return "<center>{$html}</center>";
			}, NULL)
			->from('users')
			->join('usergroup', 'users.user_group_id = usergroup.id')
			->where('users.id <>', $this->userdata->id)
			->where('users.user_group_id !=', CUSTOMER)
			->where('users.is_deleted', 0);
		print ($this->datatables->generate());
	}


	function crd_add() {
		$this->load->view('includes/after_login/head');
		$this->load->view('user/add');
	}

	function crd_add_submit() {
		if ($this->crd_add_validate() !== true)
			show_404();
		$isActive = (is_null($this->input->post('active')) ? 0 : 1);
		$data = array(
			'user_group_id' => $this->input->post('user_group_id'),
			'user_first_name' => $this->input->post('user_first_name'),
			'user_last_name' => $this->input->post('user_last_name'),
			'user_email' => $this->input->post('user_email'),
			'user_phone' => $this->input->post('user_phone'),
			'user_password' => md5($this->input->post('user_password')),
			'is_active' => $isActive,
			'created_on' => date('Y-m-d H:i:s'),
		);

		$this->db->trans_start();
		$this->db
			->set($data)
			->insert('users');
		$user_id = $this->db->insert_id();
		$this->db->trans_complete();

		$this->session->set_flashdata('message', 'User has been created successfully');
		redirect(base_url('users.html'));
	}

	function crd_add_validate() {

		$this->form_validation->set_rules('user_first_name', 'user_first_name*First Name', 'trim|required|username_check');
		$this->form_validation->set_rules('user_last_name', 'user_last_name*Last Name', 'trim|required|username_check');
		$this->form_validation->set_rules('user_email', 'user_email*Email', 'trim|required|valid_email|is_unique[users.user_email]');
		$this->form_validation->set_rules('user_phone', 'user_phone*Phone', 'trim|required|numeric|min_length[8]');
		$this->form_validation->set_rules('user_group_id', 'user_group_id*User Group', 'trim|required|numeric|isValidID[usergroup.id]');
		$this->form_validation->set_rules('user_password', 'user_password*Password', 'trim|required|min_length[8]|password_check');
		$this->form_validation->set_rules('cuser_password', 'cuser_password*Confirm Password', 'trim|required|matches[user_password]');


		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);
	}

	function crd_edit() {
		$this->checkEditId();
		$this->load->view('includes/after_login/head');
		$this->load->view('user/edit');
	}



	function crd_edit_submit() {
		if ($this->crd_edit_validate() !== true)
			show_404();
		$isActive = (is_null($this->input->post('active')) ? 0 : 1);
		$can_edit_orders = (is_null($this->input->post('can_edit_orders')) ? 0 : 1);
		$data = array(
			'user_group_id' => $this->input->post('user_group_id'),
			'user_first_name' => $this->input->post('user_first_name'),
			'user_last_name' => $this->input->post('user_last_name'),
			'user_email' => $this->input->post('user_email'),
			'user_phone' => $this->input->post('user_phone'),
			'is_active' => $isActive,
			'can_edit_orders' => $can_edit_orders,
			'modified_on' => date('Y-m-d H:i:s'),
		);
		if (!is_null($this->input->post('passwordchange')))
			$data['user_password'] = md5($this->input->post('user_password'));


		if (!is_null($this->input->post('has_extra_rights'))) {
			$allrights = $this->usermdl->getAllRights(true);

			$canaccess = ",";
			if (is_array($this->input->post('rights'))) {
				foreach ($this->input->post('rights') as $right) {
					if (!in_array($right, $allrights))
						continue;
					$canaccess .= $right . ',';
				}
			}
			$data['user_rights'] = $canaccess;
		} else {
			$data['user_rights'] = null;
		}


		$this->db->trans_start();
		$this->db
			->set($data)
			->where('id', $this->formdata->id)
			->update('users');

		$this->db->trans_complete();
		$this->session->set_flashdata('message', 'User has been updated successfully');
		redirect(base_url('users.html'));
	}

	function crd_edit_validate() {
		$this->checkEditId();

		$this->form_validation->set_rules('user_first_name', 'user_first_name*First Name', 'trim|required|username_check');
		$this->form_validation->set_rules('user_last_name', 'user_last_name*Last Name', 'trim|required|username_check');
		$this->form_validation->set_rules('user_email', 'user_email*Email', 'trim|required|valid_email|set_edit_mood[id.' . $this->formdata->id . ']|is_unique[users.user_email]');
		$this->form_validation->set_rules('user_phone', 'user_phone*Phone', 'trim|required|numeric|min_length[8]');
		$this->form_validation->set_rules('user_group_id', 'user_group_id*User Group', 'trim|required|numeric|isValidID[usergroup.id]');
		if (!is_null($this->input->post('passwordchange'))) {
			$this->form_validation->set_rules('user_password', 'user_password*Password', 'trim|required|min_length[8]|password_check');
			$this->form_validation->set_rules('cuser_password', 'cuser_password*Confirm Password', 'trim|required|matches[user_password]');
		}

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
			->update('users', array(
				'is_deleted' => 1,
				'deleted_by' => $this->userdata->id,
				'deleted_on' => date('Y-m-d H:i:s')
			));

		$this->session->set_flashdata('message', 'User has been deleted successfully');
		redirect(base_url('users.html'));
	}
}