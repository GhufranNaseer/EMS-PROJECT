<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Portal extends MY_Controller {

	function index() {



        $this->load->view('includes/after_login/head');
		$this->load->view('dashboard', array(
		));
	}

	### Start my_profile
	function my_profile() {
		$this->load->view('includes/after_login/head');
		$this->load->view('profile');
	}

	function my_profile_validate() {
		$this->load->library('validator');

		//$this->form_validation->set_rules('company', 'company*company', 'trim|required|set_edit_mood[id.' . $this->userdata->id . ']|is_unique[es_customers.company]');
		$this->form_validation->set_rules('contact_person', 'contact_person*Liason Officer Name', 'trim|required');
		//$this->form_validation->set_rules('officer_designation', 'officer_designation*designation', 'trim');
		//$this->form_validation->set_rules('email', 'email*email', 'trim|required|valid_email|set_edit_mood[id.' . $this->userdata->id . ']|is_unique[es_customers.email]');
		//$this->form_validation->set_rules('url', 'url*url', 'trim');
		$this->form_validation->set_rules('officer_phone', 'officer_phone*telephone', 'trim|numeric|min_length[8]');
		//$this->form_validation->set_rules('officer_fax', 'officer_fax*fax', 'trim|numeric');
		//$this->form_validation->set_rules('officer_country', 'officer_country*country', 'trim');
		//$this->form_validation->set_rules('officer_city', 'officer_city*city', 'trim');
		//$this->form_validation->set_rules('officer_zip_code', 'officer_zip_code*zip_code', 'trim|numeric');

		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		} else {
			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function my_profile_submit() {
		if ($this->my_profile_validate() !== true) {
			show_404();
		}


		$data = array(
			//'company' => $this->input->post('company'),
			//'name' => $this->input->post('name'),
			//'officer_designation' => $this->input->post('officer_designation'),
			//'email' => $this->input->post('email'),
			'officer_phone' => $this->input->post('officer_phone'),
			'contact_person' => $this->input->post('contact_person'),
			//'officer_fax' => $this->input->post('officer_fax'),
			//'officer_city' => $this->input->post('officer_city'),
			//'officer_country' => $this->input->post('officer_country'),
			//'officer_zip_code' => $this->input->post('officer_zip_code'),
		);


		$this->db->trans_start();
		$this->db
			->set($data)
			->where('id', $this->userdata->id)
			->update('es_officer');
		$this->db->trans_complete();

		$this->session->set_flashdata('message', 'Profile successfully updated.');
		redirect(base_url('my-profile'));
	}


	### End my_profile


    function my_profile_password_validate() {

            $this->form_validation->set_rules('opassword', 'opassword*Password', 'trim|required');
            $this->form_validation->set_rules('password', 'password*Password', 'trim|required|min_length[8]|password_check');
            $this->form_validation->set_rules('cpassword', 'cpassword*Confirm Password', 'trim|required|matches[password]');


        if ($this->form_validation->run() == false) {
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        } else {

            $check = $this->db
                ->where('id', $this->userdata->id)
                ->where('login_password', md5($this->input->post('opassword')))
                ->count_all_results('es_officer');

            if ($check == 0) {
                return $this->common->doError(func_num_args(), 'Current password is not correct!');
            }

            if ($this->input->post('password') != $this->input->post('cpassword')) {
                return $this->common->doError(func_num_args(), 'Confirm password does not match with new password');
            }

            return $this->common->doError(func_num_args(), "done", true);
        }
    }

    function my_profile_password(){
        if ($this->my_profile_password_validate() !== true) {
            show_404();
        }

        $new_password  = $this->input->post('password');
        $data = array(
            'login_password' =>md5($new_password),
        );

        $this->db->trans_start();
        $this->db
            ->set($data)
            ->where('id', $this->userdata->id)
            ->update('es_officer');
        $this->db->trans_complete();

        $this->session->set_flashdata('message', 'Password update successfully.');
        redirect(base_url('my-profile'));

    }
}