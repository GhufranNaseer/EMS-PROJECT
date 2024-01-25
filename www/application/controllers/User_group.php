<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_group extends MY_Controller {
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
				'rule' => '@',
				'manageUser' => 'checkEditId'
			),
			'crd_edit_validate' => array(
				'ajaxOnly' => true,
				'rule' => '@',
				'manageUser' => 'checkEditId'
			)
		,
			'crd_edit_submit' => array(
				'rule' => '@',
				'manageUser' => 'checkEditId'
			)
		);
		$this->load->model('usermdl');
		$this->myparent = base_url('user-groups.html');
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
			->get('usergroup');
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();
	}

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('user-groups/list');
	}

	##################################
	### Start List

	function crd_list_datatable() {
		$this->load->library('datatables');
		$this->datatables
			->select('id, usergroup_title, isActive')
			->unset_column('isActive')
			->add_column('active', function ($row) {
				$active = $row['isActive'];
				return ($active == 1) ? '<center><img src="' . base_url('assets/img/t.png') . '" /></center>' : '<center><img src="' . base_url('assets/img/cross.png') . '" /></center>';
			}, NULL)
			->add_column('col_action', function ($row) {
				$id = $row['id'];
				if ($id == SUPER_ADMIN || $id == SALES_PERSON || $id == MANAGER)
					return '';
				$html = '<a href="' . base_url() . 'user-groups-edit.html?id=' . urlencode(myid($id)) . '">Edit</a>';
				//if (ALLOW_DELETION)
				//$html .= ' | <a href="'.base_url().'user-groups-delete.html?id='. urlencode( myid($id) ).'" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';
				return "<center>{$html}</center>";
			}, NULL)
			->where('id !=', CUSTOMER)
			->from('usergroup');
		print ($this->datatables->generate());
	}

	function crd_add() {
		$this->load->view('includes/after_login/head');
		$this->load->view('user-groups/add');
	}
	### End List
	### Strat Add

	function crd_add_submit() {
		if ($this->crd_add_validate() !== true)
			show_404();
		$isActive = (is_null($this->input->post('active')) ? 0 : 1);
		$data = array(
			'isActive' => $isActive,
			'usergroup_title' => $this->input->post('group_title'),
			'createdon' => date('Y-m-d H:i:s'),
		);
		$allrights = $this->usermdl->getAllRights(true);
		if (is_null($this->input->post('superadmin'))) {
			$canaccess = ",";
			if (is_array($this->input->post('rights'))) {
				foreach ($this->input->post('rights') as $right) {
					if (!in_array($right, $allrights))
						continue;
					$canaccess .= $right . ',';
				}
			}
			$data ['usergroup_rights'] = $canaccess;
		} else {
			$data ['usergroup_rights'] = '*';
		}
		$this->db
			->set($data)
			->insert('usergroup');
		$this->session->set_flashdata('message', 'User group has been created successfully');
		redirect(base_url('user-groups.html'));
	}

	function crd_add_validate() {
		$this->form_validation->set_rules('group_title', 'group_title*User Group', 'trim|required|is_unique[usergroup.usergroup_title]');
		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		} else {
			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function crd_edit() {
		$this->load->view('includes/after_login/head');
		$this->load->view('user-groups/edit');
	}
	### End Add
	### Strat edit

	function crd_edit_submit() {
		if ($this->crd_edit_validate() !== true)
			show_404();
		$isActive = (is_null($this->input->post('active')) ? 0 : 1);
		$data = array(
			'isActive' => $isActive,
			'usergroup_title' => $this->input->post('group_title'),
			'modifiedon' => date('Y-m-d H:i:s'),
		);
		$allrights = $this->usermdl->getAllRights(true);
		if (is_null($this->input->post('superadmin'))) {
			$canaccess = ",";
			if (is_array($this->input->post('rights'))) {
				foreach ($this->input->post('rights') as $right) {
					if (!in_array($right, $allrights))
						continue;
					$canaccess .= $right . ',';
				}
			}
			$data ['usergroup_rights'] = $canaccess;
		} else {
			$data ['usergroup_rights'] = '*';
		}
		$this->db
			->set($data)
			->where('id', $this->formdata->id)
			->update('usergroup');
		$this->session->set_flashdata('message', 'User group has been updated successfully');
		redirect(base_url('user-groups.html'));
	}

	function crd_edit_validate() {
		$this->form_validation->set_rules('group_title', 'group_title*User Group', 'trim|required|set_edit_mood[id.' . $this->formdata->id . ']|is_unique[usergroup.usergroup_title]');
		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		} else {
			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function crd_delete() {
		if (!ALLOW_DELETION)
			show_404();
		if ($this->usermdl->isUserGroupUsedAnyWhere($this->formdata->id))
			$this->common->showMsg('User group already used', 'The user group, you trying to delete, is already linked with other entity. Please break the link first and then try again. ', 'gb');
		$this->db
			->where('id', $this->formdata->id)
			->delete('usergroup');
		$this->session->set_flashdata('message', 'User group has been deleted successfully');
		redirect(base_url('user-groups.html'));
	}


}