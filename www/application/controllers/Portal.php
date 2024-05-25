<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// QA: Rule is correct here;
class Portal extends MY_Controller {

	protected function rule() {

		$common = array(
			'index' => array(
				'rule' => '@',
				'manageUser' => 'manageUser',
				'header' => 'loadHeader'
			)
		);


		$myprofile = array(
			'my_profile' => array(
				'rule' => '@',
				'manageUser' => 'manageUser',
				'header' => 'loadHeader'
			),
			'my_profile_validate,crd_list_datatable' => array(
				'rule' => '@',
				'ajaxOnly' => true
			),
			'my_profile_submit' => array(
				'rule' => '@'
			)
		);

		return array_merge($common, $myprofile);
	}

	protected function manageUser($method) {

		$this->activateRightsSystem();
	}

	protected function loadHeader() {
		$this->load->view('includes/after_login/head');
	}

	##################################


	### Start my_profile
	function my_profile() {
		$this->load->view('profile');
	}

	function my_profile_validate() {
		$this->load->library('validator');


		$this->form_validation->set_rules('user_first_name', 'user_first_name*nombre', 'required|trim|username_check');
		$this->form_validation->set_rules('user_last_name', 'user_last_name*apellido', 'required|trim|username_check');


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


		$image = $this->common->uploadSingleFile("user_image", "uploads/profile/", array('jpg', 'png', 'jpeg'));

		if ($image !== false) {
			@unlink("uploads/profile/{$this->userdata->user_image}");
		} else {
			$image = base_url('uploads/profile/') . $this->userdata->user_image;
		}


		$data = array(
			'user_image' => $image,
			'user_first_name' => $this->input->post('user_first_name'),
			'user_last_name' => $this->input->post('user_last_name'),
		);


		$this->db
			->where('id', $this->userdata->id)
			->set($data)
			->update('users');

		$this->session->set_flashdata('message', 'Profile successfully updated.');

		redirect(base_url('my-profile'));
	}
	### End my_profile


	function index() {
		$this->load->view('dashboard', array());
	}
    function crd_list_datatable() {
        $this->load->library('datatables');
        $this->datatables
            ->select('id,
            user_first_name')
            ->add_column('bookings', function ($row) {
                return $this->db
                    ->where('booked_by',$row['id'])
                    ->count_all_results('es_exhibition_booking');

            }, NULL)
            ->add_column('stalls', function ($row) {
                $bookings = $this->db
                    ->where('booked_by',$row['id'])
                    ->get('es_exhibition_booking')
                    ->result();

                $count = 0;
                foreach ($bookings as $booking) {
                    $stall = $this->db
                        ->where('booking_id', $booking->id)
                        ->count_all_results('es_exhibition_stalls');
                    $count += $stall;
                }

                return $count;

            }, NULL)
            ->from('users');
        print ($this->datatables->generate());
    }
}