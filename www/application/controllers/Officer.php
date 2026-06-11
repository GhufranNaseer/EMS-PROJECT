<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Officer extends MY_Controller
{
    protected function rule()
    {
        $this->activateRightsSystem();
        $crd = array(
            'exhibitor_crd_list,
			crd_list,crd_add,
			crd_add_submit,
			foreign_delegations,
			foreign_delegations_add_submit,
			local_delegates,
			local_delegates_add_submit,
			chief_of_servicing,
			chief_of_servicing_add_submit,
			armed_force,
			armed_force_add_submit,
			armed_force_edit,
			armed_force_edit_submit,
			government_officials,
			government_officials_add_submit,
			government_officials_edit,
			government_officials_edit_submit,
			organizer,
			organizer_add_submit,
			organizer_edit,
			organizer_edit_submit,
			crd_print' => array(
                'rule' => '@'
            ),
            'exhibitor_crd_list_datatable,
			crd_list_datatable,
			crd_add_validate,
			foreign_delegations_add_validate,
			local_delegates_add_validate,
			chief_of_servicing_add_validate,
			armed_force_add_validate,
			armed_force_edit_validate,
			government_officials_add_validate,
			government_officials_edit_validate,
			organizer_add_validate,
			organizer_edit_validate' => array(
                'rule' => '@',
                'ajaxOnly' => true
            )
        );
        $crd_dit = array(
            'crd_edit,crd_delete,foreign_delegations_edit,local_delegates_edit,chief_of_servicing_edit' => array(
                'rule' => '@'
            ),
            'crd_edit_validate,foreign_delegations_edit_validate,local_delegates_edit_validate,chief_of_servicings_edit_validate' => array(
                'ajaxOnly' => true,
                'rule' => '@'
            ),
            'crd_edit_submit,foreign_delegations_edit_submit,local_delegates_edit_submit,chief_of_servicing_edit_submit' => array(
                'rule' => '@'
            )
        );
        $this->load->model('usermdl');
        $this->myparent = base_url('officer.html');
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
            ->get('es_exhibitions');
        if ($r->num_rows() == 0)
            show_404();
        $this->formdata = $r->row();
    }

    function exhibitor_crd_list()
    {
        $this->load->view('includes/after_login/head');
        $this->load->view('officer/exhibition_list');
    }

    function exhibitor_crd_list_datatable()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('E.id,
				  E.exhibition_title,
				  L.location_title				  
				  ', false)
            ->add_column('total_orders', function ($row) {
                return $this->db
                    ->where('exhibition_id', $row['id'])
                    ->where('is_canceled', 0)
                    ->count_all_results('es_exhibition_booking');
            }, NULL)
            ->add_column('col_action', function ($row) {
                $id = $row['id'];
                $html = '<a href="' . base_url() . 'officer.html?id=' . urlencode(myid($id)) . '">Users List</a>';
                return "<div class='text-center'>{$html}</div>";
            }, NULL)
            ->where('E.is_deleted', 0)
            ->join('es_locations as L', 'E.location_id = L.id', 'LEFT')
            ->from('es_exhibitions as E');

        print($this->datatables->generate());
    }

    function crd_list()
    {
        $this->load->view('includes/after_login/head');
        $this->load->view('officer/list');
    }

    function crd_list_datatable()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, 
				  IF(officer_type="foreign_delegates", \'Foreign Delegate\',
				  	IF(officer_type="local_delegates", \'Local Delegate\',
				  	IF(officer_type="armed_force", \'Armed Force (Pakistan)\',
				  	IF(officer_type="government_officials", \'Government Officials\',
				  	IF(officer_type="organizer", \'Organizer\',
				  	IF(officer_type="chief_of_servicing", \'Gov. Services Chief\', "-")))))) as type, 
				  officer_type,
				  IF(officer_type="foreign_delegates",
				  	IF(is_representative=1, CONCAT(officer_designation, " (Representative)"), CONCAT(officer_designation, " (Self)")),
				  	officer_designation) as designation, 
				  officer_country,
                  profile, 
				  contact_person, 
				  officer_phone, 
				  officer_email,
				  login_password')
            ->unset_column('officer_type')
            ->add_column('col_action', function ($row) {
                $id = $row['id'];
                $html = "";
                if ($row['officer_type'] == 'foreign_delegates') {
                    $html = '<a href="' . base_url() . 'officer-edit-foreign-delegations.html?id=' . urlencode(myid($id)) . '">Edit</a>';
                }
                if ($row['officer_type'] == 'local_delegates') {
                    $html = '<a href="' . base_url() . 'officer-edit-local-delegates.html?id=' . urlencode(myid($id)) . '">Edit</a>';
                }
                if ($row['officer_type'] == 'chief_of_servicing') {
                    $html = '<a href="' . base_url() . 'officer-edit-chief-of-servicing.html?id=' . urlencode(myid($id)) . '">Edit</a>';
                }
                if ($row['officer_type'] == 'armed_force') {
                    $html = '<a href="' . base_url() . 'officer-edit-armed-force.html?id=' . urlencode(myid($id)) . '">Edit</a>';
                }
                if ($row['officer_type'] == 'government_officials') {
                    $html = '<a href="' . base_url() . 'officer-edit-government-officials.html?id=' . urlencode(myid($id)) . '">Edit</a>';
                }
                if ($row['officer_type'] == 'organizer') {
                    $html = '<a href="' . base_url() . 'officer-edit-organizer.html?id=' . urlencode(myid($id)) . '">Edit</a>';
                }

                $html .= ' | <a href="' . base_url() . 'officer-print.html?id=' . urlencode(myid($id)) . '" target="_blank">Print</a>';

                if (ALLOW_DELETION)
                    $html .= ' | <a href="' . base_url() . 'officer-delete.html?id=' . urlencode(myid($id)) . '" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';
                return "<center>{$html}</center>";
            }, NULL)
            ->from('es_officer')
            ->where('is_deleted', 0)
            ->where(mycolumn('exhibition_id'), $this->input->get('id'));


        if ($this->input->get('filter_event')) {
            $this->datatables->join('es_exhibition_booking', 'es_exhibition_booking.agent_id = es_agent.id', 'LEFT');
            $this->datatables->where('es_exhibition_booking.exhibition_id', $this->input->get('filter_event'));
        }

        if ($this->input->get('filter_type') && $this->input->get('filter_type') != '') {
            $this->datatables->where('officer_type', $this->input->get('filter_type'));
        }
        if ($this->input->get('filter_representative') && $this->input->get('filter_representative') != '') {
            if ($this->input->get('filter_representative') == 'representative') {
                $this->datatables->where('is_representative', 1);
            }
            if ($this->input->get('filter_representative') == 'self') {
                $this->datatables->where('is_representative', 0);
            }
        }

        print($this->datatables->generate());
    }

    /* NOT IN USE */
    function crd_add()
    {
        $this->load->view('includes/after_login/head');
        $this->load->view('officer/add');
    }

    /* NOT IN USE */
    function crd_add_submit()
    {
        if ($this->crd_add_validate() !== true)
            show_404();

        $exhibition_id = $this->formdata->id;

        $data = array(
            'exhibition_id' => $exhibition_id,
            'officer_type' => $this->input->post('officer_type'),
            'officer_name' => $this->input->post('officer_name'),
            'officer_designation' => $this->input->post('officer_designation'),
            'officer_email' => $this->input->post('officer_email'),
            'login_password' => md5($this->input->post('officer_password')),
            'officer_phone' => $this->input->post('officer_phone'),
            'officer_fax' => $this->input->post('officer_fax'),
            'officer_city' => $this->input->post('officer_city'),
            'officer_country' => $this->input->post('officer_country'),
            'officer_zip_code' => $this->input->post('officer_zip_code'),
            'officer_address' => $this->input->post('officer_address'),
            'user_name_for_corresponding_officer' => $this->input->post('user_name_for_corresponding_officer'),
            'user_corresponding_email_address' => $this->input->post('user_corresponding_email_address'),
            'user_corresponding_phone_no' => $this->input->post('user_corresponding_phone_no'),
            'is_deleted' => 0
        );

        $this->db->trans_start();
        $this->db
            ->set($data)
            ->insert('es_officer');
        //$id = $this->db->insert_id();
        $this->db->trans_complete();

        $this->session->set_flashdata('message', 'officer has been created successfully');

        redirect(base_url('officer_exhibitor.html'));
    }

    /* NOT IN USE */
    function crd_add_validate()
    {
        $this->checkEditId();

        $this->form_validation->set_rules('officer_type', 'officer_type*Officer type', 'trim|required');
        $this->form_validation->set_rules('officer_name', 'officer_name*name', 'trim|required');
        $this->form_validation->set_rules('officer_designation', 'officer_designation*designation', 'trim');
        $this->form_validation->set_rules('officer_email', 'officer_email*email', 'trim|required|valid_email|is_unique[es_officer.officer_email]');
        $this->form_validation->set_rules('officer_password', 'officer_password*Password', 'trim|required|min_length[8]');
        $this->form_validation->set_rules('officer_phone', 'officer_phone*telephone', 'trim|numeric|min_length[8]');
        $this->form_validation->set_rules('officer_fax', 'officer_fax*fax', 'trim|numeric');
        $this->form_validation->set_rules('officer_country', 'officer_country*country', 'trim');
        $this->form_validation->set_rules('officer_city', 'officer_city*city', 'trim');
        $this->form_validation->set_rules('officer_zip_code', 'officer_zip_code*zip_code', 'trim|numeric');
        $this->form_validation->set_rules('officer_address', 'officer_address*address', 'trim');
        $this->form_validation->set_rules('user_name_for_corresponding_officer', 'user_name_for_corresponding_officer*User name for corresponding officer', 'trim');
        $this->form_validation->set_rules('user_corresponding_email_address', 'user_corresponding_email_address*User corresponding email address', 'trim');
        $this->form_validation->set_rules('user_corresponding_phone_no', 'user_corresponding_phone_no*user corresponding phone no', 'trim');



        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else
            return $this->common->doError(func_num_args(), "done", true);
    }

    /* NOT IN USE */
    function crd_edit()
    {
        //$this->checkEditId();
        $id = $this->input->get('id');
        if (is_null($id))
            show_404();
        $id = $this->db->escape($id);
        $key = config_item('encryption_key');
        $key = $this->db->escape($key);
        $r = $this->db
            ->where("MD5(CONCAT($key,`id`)) = $id")
            ->get('es_officer');
        if ($r->num_rows() == 0)
            show_404();
        $this->formdata = $r->row();

        $this->load->view('includes/after_login/head');
        $this->load->view('officer/edit');
    }

    /* NOT IN USE */
    function crd_edit_submit()
    {
        if ($this->crd_edit_validate() !== true)
            show_404();

        $data = array(
            'officer_type' => $this->input->post('officer_type'),
            'officer_name' => $this->input->post('officer_name'),
            'officer_designation' => $this->input->post('officer_designation'),
            //'officer_email' => $this->input->post('officer_email'),
            'officer_phone' => $this->input->post('officer_phone'),
            'officer_fax' => $this->input->post('officer_fax'),
            'officer_city' => $this->input->post('officer_city'),
            'officer_country' => $this->input->post('officer_country'),
            'officer_zip_code' => $this->input->post('officer_zip_code'),
            'officer_address' => $this->input->post('officer_address'),
            'user_name_for_corresponding_officer' => $this->input->post('user_name_for_corresponding_officer'),
            'user_corresponding_email_address' => $this->input->post('user_corresponding_email_address'),
            'user_corresponding_phone_no' => $this->input->post('user_corresponding_phone_no'),

        );


        $this->db->trans_start();
        $this->db
            ->set($data)
            ->where('id', $this->formdata->id)
            ->update('es_officer');

        $this->db->trans_complete();
        $this->session->set_flashdata('message', 'Officer has been updated successfully');
        redirect(base_url('officer_exhibitor.html'));
    }

    /* NOT IN USE */
    function crd_edit_validate()
    {
        $id = $this->input->get('id');
        if (is_null($id))
            show_404();
        $id = $this->db->escape($id);
        $key = config_item('encryption_key');
        $key = $this->db->escape($key);
        $r = $this->db
            ->where("MD5(CONCAT($key,`id`)) = $id")
            ->get('es_officer');
        if ($r->num_rows() == 0)
            show_404();
        $this->formdata = $r->row();


        $this->form_validation->set_rules('officer_type', 'officer_type*Officer type', 'trim|required');
        $this->form_validation->set_rules('officer_name', 'officer_name*name', 'trim|required');
        $this->form_validation->set_rules('officer_designation', 'officer_designation*designation', 'trim');
        //$this->form_validation->set_rules('officer_email', 'officer_email*email', 'trim|required|valid_email');
        $this->form_validation->set_rules('officer_phone', 'officer_phone*telephone', 'trim|numeric|min_length[8]');
        $this->form_validation->set_rules('officer_fax', 'officer_fax*fax', 'trim|numeric');
        $this->form_validation->set_rules('officer_country', 'officer_country*country', 'trim');
        $this->form_validation->set_rules('officer_city', 'officer_city*city', 'trim');
        $this->form_validation->set_rules('officer_zip_code', 'officer_zip_code*zip_code', 'trim|numeric');
        $this->form_validation->set_rules('officer_address', 'officer_address*address', 'trim');
        $this->form_validation->set_rules('user_name_for_corresponding_officer', 'user_name_for_corresponding_officer*User name for corresponding officer', 'trim');
        $this->form_validation->set_rules('user_corresponding_email_address', 'user_corresponding_email_address*User corresponding email address', 'trim');
        $this->form_validation->set_rules('user_corresponding_phone_no', 'user_corresponding_phone_no*user corresponding phone no', 'trim');

        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else {
            return $this->common->doError(func_num_args(), "done", true);
        }
    }

    function crd_delete()
    {
        $id = $this->input->get('id');
        if (is_null($id))
            show_404();
        $id = $this->db->escape($id);
        $key = config_item('encryption_key');
        $key = $this->db->escape($key);
        $r = $this->db
            ->where("MD5(CONCAT($key,`id`)) = $id")
            ->get('es_officer');
        if ($r->num_rows() == 0)
            show_404();
        $this->formdata = $r->row();


        $this->db
            ->where('id', $this->formdata->id)
            ->update('es_officer', array(
                'is_deleted' => 1,
                'deleted_by' => $this->userdata->id,
                'deleted_on' => date('Y-m-d H:i:s')
            ));

        $this->session->set_flashdata('message', 'Officer has been deleted successfully');
        redirect(base_url('officer_exhibitor.html'));
    }

    function crd_print()
    {
        $html = $this->load->view('officer/officer_print', array(), true);

        // echo $html; die;
        set_time_limit(0);
        $this->load->helper("pdf-loader");

        try {
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');

            // Suppress any warnings related to missing images or resources
            // This is handled by the image validation helper in the view
            error_reporting(E_ERROR | E_PARSE);
            $html2pdf->writeHTML($html);
            error_reporting(E_ALL);

            $html2pdf->Output('officer-' . $this->input->get('id') . '.pdf');
        } catch (HTML2PDF_exception $e) {
            // Log the error but don't block PDF generation
            log_message('error', 'PDF Generation Error in Officer Print: ' . $e->getMessage());

            $msg = $e->getMessage();

            // If it's an image loading error, try to provide helpful information
            if (strpos($msg, 'ERROR n°6') !== false || strpos($msg, 'image') !== false) {
                // Image validation in the view should prevent this, but just in case:
                log_message('error', 'Image loading issue - check that all image paths in officer_print.php are valid');
            }

            // Show user-friendly error message
            echo "PDF Generation failed. ";
            echo "Please ensure all image files exist. ";
            echo "System logs may have more details.";
            exit;
        }
    }

    function foreign_delegations()
    {
        $this->load->view('includes/after_login/head');
        $this->load->view('officer/add_foreign_delegations');
    }

    function foreign_delegations_add_submit()
    {
        if ($this->foreign_delegations_add_validate() !== true)
            show_404();

        $exhibition_id = $this->formdata->id;

        $data = array(
            'exhibition_id' => $exhibition_id,
            'officer_type' => 'foreign_delegates',
            'officer_designation' => $this->input->post('officer_designation'),
            'officer_country' => $this->input->post('officer_country'),
            'profile' => $this->input->post('profile'),
            'is_representative' => (is_null($this->input->post('representative')) ? 0 : 1),
            'contact_person' => $this->input->post('contact_person_name'),
            'officer_phone' => $this->input->post('mobile_number'),
            'officer_email' => $this->input->post('email'),
            'login_password' => $this->input->post('password'),
            'is_deleted' => 0
        );

        $this->db->trans_start();
        $this->db
            ->set($data)
            ->insert('es_officer');
        //$id = $this->db->insert_id();
        $this->db->trans_complete();

        $this->session->set_flashdata('message', 'Foreign delegates has been created successfully');

        redirect(base_url('officer.html?id=' . myid($this->formdata->id)));
    }

    function foreign_delegations_add_validate()
    {
        $this->checkEditId();

        $this->form_validation->set_rules('officer_designation', 'officer_designation*Officer designation', 'trim|required');
        $this->form_validation->set_rules('officer_country', 'officer_country*Officer country', 'trim|required');
        $this->form_validation->set_rules('profile', 'profile*Profile', 'trim');
        $this->form_validation->set_rules('representative', 'representative*Representative', 'trim');
        $this->form_validation->set_rules('contact_person_name', 'contact_person_name*Contact person name', 'trim|required');
        $this->form_validation->set_rules('mobile_number', 'mobile_number*Mobile number', 'trim');
        $this->form_validation->set_rules('email', 'email*Email', 'trim');
        $this->form_validation->set_rules('password', 'password*Password', 'trim|required');



        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else
            return $this->common->doError(func_num_args(), "done", true);
    }

    function local_delegates()
    {
        $this->load->view('includes/after_login/head');
        $this->load->view('officer/add_local_delegates');
    }

    function local_delegates_add_submit()
    {
        if ($this->local_delegates_add_validate() !== true)
            show_404();

        $exhibition_id = $this->formdata->id;

        $data = array(
            'exhibition_id' => $exhibition_id,
            'officer_type' => 'local_delegates',
            'officer_designation' => $this->input->post('officer_designation'),
            'officer_country' => $this->input->post('officer_country'),
            'profile' => $this->input->post('profile'),
            'officer_company' => $this->input->post('organization'),
            'contact_person' => $this->input->post('contact_person_name'),
            'officer_phone' => $this->input->post('mobile_number'),
            'officer_email' => $this->input->post('email'),
            'login_password' => $this->input->post('password'),
            'is_deleted' => 0
        );

        $this->db->trans_start();
        $this->db
            ->set($data)
            ->insert('es_officer');
        //$id = $this->db->insert_id();
        $this->db->trans_complete();

        $this->session->set_flashdata('message', 'Local delegates has been created successfully');
        redirect(base_url('officer.html?id=' . myid($this->formdata->id)));
    }

    function local_delegates_add_validate()
    {
        $this->checkEditId();

        $this->form_validation->set_rules('officer_designation', 'officer_designation*Officer designation', 'trim|required');
        $this->form_validation->set_rules('officer_country', 'officer_country*Officer country', 'trim|required');
        $this->form_validation->set_rules('profile', 'profile*Profile', 'trim');
        $this->form_validation->set_rules('organization', 'organization*Organization', 'trim|required');
        $this->form_validation->set_rules('contact_person_name', 'contact_person_name*Contact person name', 'trim|required');
        $this->form_validation->set_rules('mobile_number', 'mobile_number*Mobile number', 'trim|required');
        $this->form_validation->set_rules('email', 'email*Email', 'trim|required');
        $this->form_validation->set_rules('password', 'password*Password', 'trim|required');



        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else
            return $this->common->doError(func_num_args(), "done", true);
    }

    function chief_of_servicing()
    {
        $this->load->view('includes/after_login/head');
        $this->load->view('officer/add_chief_of_servicing');
    }

    function chief_of_servicing_add_submit()
    {
        if ($this->chief_of_servicing_add_validate() !== true)
            show_404();

        $exhibition_id = $this->formdata->id;

        $data = array(
            'exhibition_id' => $exhibition_id,
            'officer_type' => 'chief_of_servicing',
            'officer_designation' => $this->input->post('officer_designation'),
            'officer_country' => $this->input->post('officer_country'),
            'profile' => $this->input->post('profile'),
            'contact_person' => $this->input->post('contact_person_name'),
            'officer_phone' => $this->input->post('mobile_number'),
            'officer_email' => $this->input->post('email'),
            'login_password' => $this->input->post('password'),
            'is_deleted' => 0
        );

        $this->db->trans_start();
        $this->db
            ->set($data)
            ->insert('es_officer');
        //$id = $this->db->insert_id();
        $this->db->trans_complete();

        $this->session->set_flashdata('message', 'Chief of Servicing has been created successfully');
        redirect(base_url('officer.html?id=' . myid($this->formdata->id)));
    }

    function chief_of_servicing_add_validate()
    {
        $this->checkEditId();

        $this->form_validation->set_rules('officer_designation', 'officer_designation*Officer designation', 'trim|required');
        $this->form_validation->set_rules('officer_country', 'officer_country*Officer country', 'trim|required');
        $this->form_validation->set_rules('profile', 'profile*Profile', 'trim');
        $this->form_validation->set_rules('contact_person_name', 'contact_person_name*Contact person name', 'trim|required');
        $this->form_validation->set_rules('mobile_number', 'mobile_number*Mobile number', 'trim|required');
        $this->form_validation->set_rules('email', 'email*Email', 'trim|required');
        $this->form_validation->set_rules('password', 'password*Password', 'trim|required');



        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else
            return $this->common->doError(func_num_args(), "done", true);
    }

    function foreign_delegations_edit()
    {
        //$this->checkEditId();
        $id = $this->input->get('id');
        if (is_null($id))
            show_404();
        $id = $this->db->escape($id);
        $key = config_item('encryption_key');
        $key = $this->db->escape($key);
        $r = $this->db
            ->where("MD5(CONCAT($key,`id`)) = $id")
            ->get('es_officer');
        if ($r->num_rows() == 0)
            show_404();
        $this->formdata = $r->row();

        $this->load->view('includes/after_login/head');
        $this->load->view('officer/edit_foreign_delegations.php');
    }

    function foreign_delegations_edit_submit()
    {
        if ($this->foreign_delegations_edit_validate() !== true)
            show_404();

        $exhibition_id = $this->formdata->id;

        $data = array(
            //  'exhibition_id' => $exhibition_id,
            // 'officer_type' => 'foreign_delegations',
            'officer_designation' => $this->input->post('officer_designation'),
            'officer_country' => $this->input->post('officer_country'),
            'profile' => $this->input->post('profile'),
            'is_representative' => (is_null($this->input->post('representative')) ? 0 : 1),
            'contact_person' => $this->input->post('contact_person_name'),
            'officer_phone' => $this->input->post('mobile_number'),
            'officer_email' => $this->input->post('email'),
            'login_password' => $this->input->post('password'),
            'is_deleted' => 0
        );


        $this->db->trans_start();
        $this->db
            ->set($data)
            ->where('id', $this->formdata->id)
            ->update('es_officer');

        $this->db->trans_complete();
        $this->session->set_flashdata('message', 'Foreign delegates has been updated successfully');
        redirect(base_url('officer.html?id=' . myid($this->formdata->exhibition_id)));
    }

    function foreign_delegations_edit_validate()
    {
        $id = $this->input->get('id');
        if (is_null($id))
            show_404();
        $id = $this->db->escape($id);
        $key = config_item('encryption_key');
        $key = $this->db->escape($key);
        $r = $this->db
            ->where("MD5(CONCAT($key,`id`)) = $id")
            ->get('es_officer');
        if ($r->num_rows() == 0)
            show_404();
        $this->formdata = $r->row();

        $this->form_validation->set_rules('officer_designation', 'officer_designation*Officer designation', 'trim|required');
        $this->form_validation->set_rules('officer_country', 'officer_country*Officer country', 'trim|required');
        // $this->form_validation->set_rules('representative', 'representative*Representative', 'trim|required');
        $this->form_validation->set_rules('profile', 'profile*Profile', 'trim');
        $this->form_validation->set_rules('contact_person_name', 'contact_person_name*Contact person name', 'trim|required');
        $this->form_validation->set_rules('mobile_number', 'mobile_number*Mobile number', 'trim|required');
        $this->form_validation->set_rules('email', 'email*Email', 'trim|required');
        $this->form_validation->set_rules('password', 'password*Password', 'trim|required');

        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else {
            return $this->common->doError(func_num_args(), "done", true);
        }
    }

    function local_delegates_edit()
    {
        //$this->checkEditId();
        $id = $this->input->get('id');
        if (is_null($id))
            show_404();
        $id = $this->db->escape($id);
        $key = config_item('encryption_key');
        $key = $this->db->escape($key);
        $r = $this->db
            ->where("MD5(CONCAT($key,`id`)) = $id")
            ->get('es_officer');
        if ($r->num_rows() == 0)
            show_404();
        $this->formdata = $r->row();

        $this->load->view('includes/after_login/head');
        $this->load->view('officer/edit_local_delegates.php');
    }

    function local_delegates_edit_submit()
    {
        if ($this->local_delegates_edit_validate() !== true)
            show_404();

        $exhibition_id = $this->formdata->id;

        $data = array(
            // 'exhibition_id' => $exhibition_id,
            //'officer_type' => 'local_delegates',
            'officer_designation' => $this->input->post('officer_designation'),
            'officer_country' => $this->input->post('officer_country'),
            'profile' => $this->input->post('profile'),
            'officer_company' => $this->input->post('organization'),
            'contact_person' => $this->input->post('contact_person_name'),
            'officer_phone' => $this->input->post('mobile_number'),
            'officer_email' => $this->input->post('email'),
            'login_password' => $this->input->post('password'),
            'is_deleted' => 0
        );


        $this->db->trans_start();
        $this->db
            ->set($data)
            ->where('id', $this->formdata->id)
            ->update('es_officer');

        $this->db->trans_complete();
        $this->session->set_flashdata('message', 'Local delegates has been updated successfully');
        redirect(base_url('officer.html?id=' . myid($this->formdata->exhibition_id)));
    }

    function local_delegates_edit_validate()
    {
        $id = $this->input->get('id');
        if (is_null($id))
            show_404();
        $id = $this->db->escape($id);
        $key = config_item('encryption_key');
        $key = $this->db->escape($key);
        $r = $this->db
            ->where("MD5(CONCAT($key,`id`)) = $id")
            ->get('es_officer');
        if ($r->num_rows() == 0)
            show_404();
        $this->formdata = $r->row();


        $this->form_validation->set_rules('officer_designation', 'officer_designation*Officer designation', 'trim|required');
        //$this->form_validation->set_rules('officer_country', 'officer_country*Officer country', 'trim|required');
        $this->form_validation->set_rules('profile', 'profile*Profile', 'trim');
        $this->form_validation->set_rules('organization', 'organization*Organization', 'trim|required');
        $this->form_validation->set_rules('contact_person_name', 'contact_person_name*Contact person name', 'trim|required');
        $this->form_validation->set_rules('mobile_number', 'mobile_number*Mobile number', 'trim|required');
        $this->form_validation->set_rules('email', 'email*Email', 'trim|required');
        $this->form_validation->set_rules('password', 'password*Password', 'trim|required');

        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else {
            return $this->common->doError(func_num_args(), "done", true);
        }
    }

    function chief_of_servicing_edit()
    {
        //$this->checkEditId();
        $id = $this->input->get('id');
        if (is_null($id))
            show_404();
        $id = $this->db->escape($id);
        $key = config_item('encryption_key');
        $key = $this->db->escape($key);
        $r = $this->db
            ->where("MD5(CONCAT($key,`id`)) = $id")
            ->get('es_officer');
        if ($r->num_rows() == 0)
            show_404();
        $this->formdata = $r->row();

        $this->load->view('includes/after_login/head');
        $this->load->view('officer/edit_chief_of_servicing.php');
    }

    function chief_of_servicing_edit_submit()
    {
        if ($this->chief_of_servicings_edit_validate() !== true)
            show_404();

        $exhibition_id = $this->formdata->id;

        $data = array(
            //'exhibition_id' => $exhibition_id,
            //'officer_type' => 'chief_of_servicing',
            'officer_designation' => $this->input->post('officer_designation'),
            'officer_country' => $this->input->post('officer_country'),
            'profile' => $this->input->post('profile'),
            'contact_person' => $this->input->post('contact_person_name'),
            'officer_phone' => $this->input->post('mobile_number'),
            'officer_email' => $this->input->post('email'),
            'login_password' => $this->input->post('password'),
            'is_deleted' => 0
        );

        $this->db->trans_start();
        $this->db
            ->set($data)
            ->where('id', $this->formdata->id)
            ->update('es_officer');

        $this->db->trans_complete();
        $this->session->set_flashdata('message', 'Chief of Servicing has been updated successfully');
        redirect(base_url('officer.html?id=' . myid($this->formdata->exhibition_id)));
    }

    function chief_of_servicings_edit_validate()
    {
        $id = $this->input->get('id');
        if (is_null($id))
            show_404();
        $id = $this->db->escape($id);
        $key = config_item('encryption_key');
        $key = $this->db->escape($key);
        $r = $this->db
            ->where("MD5(CONCAT($key,`id`)) = $id")
            ->get('es_officer');
        if ($r->num_rows() == 0)
            show_404();
        $this->formdata = $r->row();


        $this->form_validation->set_rules('officer_designation', 'officer_designation*Officer designation', 'trim|required');
        //$this->form_validation->set_rules('officer_country', 'officer_country*Officer country', 'trim|required');
        $this->form_validation->set_rules('profile', 'profile*Profile', 'trim');
        $this->form_validation->set_rules('contact_person_name', 'contact_person_name*Contact person name', 'trim|required');
        $this->form_validation->set_rules('mobile_number', 'mobile_number*Mobile number', 'trim|required');
        $this->form_validation->set_rules('email', 'email*Email', 'trim|required');
        $this->form_validation->set_rules('password', 'password*Password', 'trim|required');

        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else {
            return $this->common->doError(func_num_args(), "done", true);
        }
    }

    /* *************** */
    function armed_force()
    {
        $this->load->view('includes/after_login/head');
        $this->load->view('officer/add_armed_force');
    }

    function armed_force_add_validate()
    {
        $this->checkEditId();

        $this->form_validation->set_rules('officer_designation', 'officer_designation*Officer designation', 'trim|required');
        $this->form_validation->set_rules('officer_country', 'officer_country*Officer country', 'trim|required');
        $this->form_validation->set_rules('officer_rank', 'officer_rank*officer rank', 'trim');
        $this->form_validation->set_rules('organization', 'organization*Organization', 'trim|required');
        $this->form_validation->set_rules('contact_person_name', 'contact_person_name*Contact person name', 'trim|required');
        $this->form_validation->set_rules('mobile_number', 'mobile_number*Mobile number', 'trim|required');
        $this->form_validation->set_rules('email', 'email*Email', 'trim|required');
        $this->form_validation->set_rules('password', 'password*Password', 'trim|required');
        $this->form_validation->set_rules('profile', 'profile*Profile', 'trim');


        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else
            return $this->common->doError(func_num_args(), "done", true);
    }

    function armed_force_add_submit()
    {
        if ($this->armed_force_add_validate() !== true)
            show_404();

        $exhibition_id = $this->formdata->id;

        $data = array(
            'exhibition_id' => $exhibition_id,
            'officer_type' => 'armed_force',
            'officer_designation' => $this->input->post('officer_designation'),
            'officer_country' => $this->input->post('officer_country'),
            'profile' => $this->input->post('profile'),
            'officer_rank' => $this->input->post('officer_rank'),
            'officer_company' => $this->input->post('organization'),
            'contact_person' => $this->input->post('contact_person_name'),
            'officer_phone' => $this->input->post('mobile_number'),
            'officer_email' => $this->input->post('email'),
            'login_password' => $this->input->post('password'),
            'is_deleted' => 0
        );

        $this->db->trans_start();
        $this->db
            ->set($data)
            ->insert('es_officer');
        //$id = $this->db->insert_id();
        $this->db->trans_complete();

        $this->session->set_flashdata('message', 'Armed force officer has been created successfully');
        redirect(base_url('officer.html?id=' . myid($this->formdata->id)));
    }

    function armed_force_edit()
    {
        //$this->checkEditId();
        $id = $this->input->get('id');
        if (is_null($id))
            show_404();
        $id = $this->db->escape($id);
        $key = config_item('encryption_key');
        $key = $this->db->escape($key);
        $r = $this->db
            ->where("MD5(CONCAT($key,`id`)) = $id")
            ->get('es_officer');
        if ($r->num_rows() == 0)
            show_404();
        $this->formdata = $r->row();

        $this->load->view('includes/after_login/head');
        $this->load->view('officer/edit_armed_force.php');
    }

    function armed_force_edit_validate()
    {
        $id = $this->input->get('id');
        if (is_null($id))
            show_404();
        $id = $this->db->escape($id);
        $key = config_item('encryption_key');
        $key = $this->db->escape($key);
        $r = $this->db
            ->where("MD5(CONCAT($key,`id`)) = $id")
            ->get('es_officer');
        if ($r->num_rows() == 0)
            show_404();
        $this->formdata = $r->row();


        $this->form_validation->set_rules('officer_designation', 'officer_designation*Officer designation', 'trim|required');
        //$this->form_validation->set_rules('officer_country', 'officer_country*Officer country', 'trim|required');
        $this->form_validation->set_rules('officer_rank', 'officer_rank*officer rank', 'trim');
        $this->form_validation->set_rules('organization', 'organization*Organization', 'trim|required');
        $this->form_validation->set_rules('contact_person_name', 'contact_person_name*Contact person name', 'trim|required');
        $this->form_validation->set_rules('mobile_number', 'mobile_number*Mobile number', 'trim|required');
        $this->form_validation->set_rules('email', 'email*Email', 'trim|required');
        $this->form_validation->set_rules('password', 'password*Password', 'trim|required');
        $this->form_validation->set_rules('profile', 'profile*Profile', 'trim');

        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else {
            return $this->common->doError(func_num_args(), "done", true);
        }
    }

    function armed_force_edit_submit()
    {
        if ($this->armed_force_edit_validate() !== true)
            show_404();

        $exhibition_id = $this->formdata->id;

        $data = array(
            // 'exhibition_id' => $exhibition_id,
            //'officer_type' => 'armed_force',
            'officer_designation' => $this->input->post('officer_designation'),
            'officer_country' => $this->input->post('officer_country'),
            'profile' => $this->input->post('profile'),
            'officer_rank' => $this->input->post('officer_rank'),
            'officer_company' => $this->input->post('organization'),
            'contact_person' => $this->input->post('contact_person_name'),
            'officer_phone' => $this->input->post('mobile_number'),
            'officer_email' => $this->input->post('email'),
            'login_password' => $this->input->post('password'),
            'is_deleted' => 0
        );


        $this->db->trans_start();
        $this->db
            ->set($data)
            ->where('id', $this->formdata->id)
            ->update('es_officer');

        $this->db->trans_complete();
        $this->session->set_flashdata('message', 'Armed force officer has been updated successfully');
        redirect(base_url('officer.html?id=' . myid($this->formdata->exhibition_id)));
    }


    /* *************** */
    function government_officials()
    {
        $this->load->view('includes/after_login/head');
        $this->load->view('officer/add_government_officials');
    }

    function government_officials_add_validate()
    {
        $this->checkEditId();

        $this->form_validation->set_rules('officer_designation', 'officer_designation*Officer designation', 'trim|required');
        $this->form_validation->set_rules('officer_country', 'officer_country*Officer country', 'trim|required');
        $this->form_validation->set_rules('profile', 'profile*Profile', 'trim');
        $this->form_validation->set_rules('contact_person_name', 'contact_person_name*Contact person name', 'trim|required');
        $this->form_validation->set_rules('mobile_number', 'mobile_number*Mobile number', 'trim');
        $this->form_validation->set_rules('email', 'email*Email', 'trim|required');
        $this->form_validation->set_rules('password', 'password*Password', 'trim|required');



        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else
            return $this->common->doError(func_num_args(), "done", true);
    }

    function government_officials_add_submit()
    {
        if ($this->government_officials_add_validate() !== true)
            show_404();

        $exhibition_id = $this->formdata->id;

        $data = array(
            'exhibition_id' => $exhibition_id,
            'officer_type' => 'government_officials',
            'officer_designation' => $this->input->post('officer_designation'),
            'officer_country' => $this->input->post('officer_country'),
            'profile' => $this->input->post('profile'),
            'is_representative' => (is_null($this->input->post('representative')) ? 0 : 1),
            'contact_person' => $this->input->post('contact_person_name'),
            'officer_phone' => $this->input->post('mobile_number'),
            'officer_email' => $this->input->post('email'),
            'login_password' => $this->input->post('password'),
            'is_deleted' => 0
        );

        $this->db->trans_start();
        $this->db
            ->set($data)
            ->insert('es_officer');
        //$id = $this->db->insert_id();
        $this->db->trans_complete();

        $this->session->set_flashdata('message', 'Government official has been created successfully');
        redirect(base_url('officer.html?id=' . myid($this->formdata->id)));
    }

    function government_officials_edit()
    {
        //$this->checkEditId();
        $id = $this->input->get('id');
        if (is_null($id))
            show_404();
        $id = $this->db->escape($id);
        $key = config_item('encryption_key');
        $key = $this->db->escape($key);
        $r = $this->db
            ->where("MD5(CONCAT($key,`id`)) = $id")
            ->get('es_officer');
        if ($r->num_rows() == 0)
            show_404();
        $this->formdata = $r->row();

        $this->load->view('includes/after_login/head');
        $this->load->view('officer/edit_government_officials.php');
    }

    function government_officials_edit_validate()
    {
        $id = $this->input->get('id');
        if (is_null($id))
            show_404();
        $id = $this->db->escape($id);
        $key = config_item('encryption_key');
        $key = $this->db->escape($key);
        $r = $this->db
            ->where("MD5(CONCAT($key,`id`)) = $id")
            ->get('es_officer');
        if ($r->num_rows() == 0)
            show_404();
        $this->formdata = $r->row();


        $this->form_validation->set_rules('officer_designation', 'officer_designation*Officer designation', 'trim|required');
        $this->form_validation->set_rules('contact_person_name', 'contact_person_name*Contact person name', 'trim|required');
        $this->form_validation->set_rules('profile', 'profile*Profile', 'trim');
        $this->form_validation->set_rules('mobile_number', 'mobile_number*Mobile number', 'trim');
        $this->form_validation->set_rules('email', 'email*Email', 'trim|required');
        $this->form_validation->set_rules('password', 'password*Password', 'trim|required');

        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else {
            return $this->common->doError(func_num_args(), "done", true);
        }
    }

    function government_officials_edit_submit()
    {
        if ($this->government_officials_edit_validate() !== true)
            show_404();

        $exhibition_id = $this->formdata->id;

        $data = array(
            // 'exhibition_id' => $exhibition_id,
            //'officer_type' => 'government_officials',
            'officer_designation' => $this->input->post('officer_designation'),
            'officer_country' => $this->input->post('officer_country'),
            'profile' => $this->input->post('profile'),
            'is_representative' => (is_null($this->input->post('representative')) ? 0 : 1),
            'contact_person' => $this->input->post('contact_person_name'),
            'officer_phone' => $this->input->post('mobile_number'),
            'officer_email' => $this->input->post('email'),
            'login_password' => $this->input->post('password'),
            'is_deleted' => 0
        );


        $this->db->trans_start();
        $this->db
            ->set($data)
            ->where('id', $this->formdata->id)
            ->update('es_officer');

        $this->db->trans_complete();
        $this->session->set_flashdata('message', 'Government official has been updated successfully');
        redirect(base_url('officer.html?id=' . myid($this->formdata->exhibition_id)));
    }


    /* *************** */
    function organizer()
    {
        $this->load->view('includes/after_login/head');
        $this->load->view('officer/add_organizer');
    }

    function organizer_add_validate()
    {
        $this->checkEditId();

        $this->form_validation->set_rules('officer_designation', 'officer_designation*Officer designation', 'trim|required');
        // $this->form_validation->set_rules('officer_country', 'officer_country*Officer country', 'trim|required');
        $this->form_validation->set_rules('profile', 'profile*Profile', 'trim');
        $this->form_validation->set_rules('organization', 'organization*Organization', 'trim|required');
        $this->form_validation->set_rules('contact_person_name', 'contact_person_name*Contact person name', 'trim|required');
        $this->form_validation->set_rules('mobile_number', 'mobile_number*Mobile number', 'trim');
        $this->form_validation->set_rules('email', 'email*Email', 'trim|required');
        $this->form_validation->set_rules('password', 'password*Password', 'trim|required');



        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else
            return $this->common->doError(func_num_args(), "done", true);
    }

    function organizer_add_submit()
    {
        if ($this->organizer_add_validate() !== true)
            show_404();

        $exhibition_id = $this->formdata->id;

        $data = array(
            'exhibition_id' => $exhibition_id,
            'officer_type' => 'organizer',
            'officer_designation' => $this->input->post('officer_designation'),
            'officer_company' => $this->input->post('organization'),
            'profile' => $this->input->post('profile'),
            'contact_person' => $this->input->post('contact_person_name'),
            'officer_phone' => $this->input->post('mobile_number'),
            'officer_email' => $this->input->post('email'),
            'login_password' => $this->input->post('password'),
            'is_deleted' => 0
        );

        $this->db->trans_start();
        $this->db
            ->set($data)
            ->insert('es_officer');
        //$id = $this->db->insert_id();
        $this->db->trans_complete();

        $this->session->set_flashdata('message', 'Organizer has been created successfully');
        redirect(base_url('officer.html?id=' . myid($this->formdata->id)));
    }

    function organizer_edit()
    {
        //$this->checkEditId();
        $id = $this->input->get('id');
        if (is_null($id))
            show_404();
        $id = $this->db->escape($id);
        $key = config_item('encryption_key');
        $key = $this->db->escape($key);
        $r = $this->db
            ->where("MD5(CONCAT($key,`id`)) = $id")
            ->get('es_officer');
        if ($r->num_rows() == 0)
            show_404();
        $this->formdata = $r->row();

        $this->load->view('includes/after_login/head');
        $this->load->view('officer/edit_organizer.php');
    }

    function organizer_edit_validate()
    {
        $id = $this->input->get('id');
        if (is_null($id))
            show_404();
        $id = $this->db->escape($id);
        $key = config_item('encryption_key');
        $key = $this->db->escape($key);
        $r = $this->db
            ->where("MD5(CONCAT($key,`id`)) = $id")
            ->get('es_officer');
        if ($r->num_rows() == 0)
            show_404();
        $this->formdata = $r->row();


        $this->form_validation->set_rules('officer_designation', 'officer_designation*Officer designation', 'trim|required');
        //$this->form_validation->set_rules('officer_country', 'officer_country*Officer country', 'trim|required');
        // $this->form_validation->set_rules('officer_rank', 'officer_rank*officer rank', 'trim');
        $this->form_validation->set_rules('profile', 'profile*Profile', 'trim');
        $this->form_validation->set_rules('organization', 'organization*Organization', 'trim|required');
        $this->form_validation->set_rules('contact_person_name', 'contact_person_name*Contact person name', 'trim|required');
        $this->form_validation->set_rules('mobile_number', 'mobile_number*Mobile number', 'trim');
        $this->form_validation->set_rules('email', 'email*Email', 'trim|required');
        $this->form_validation->set_rules('password', 'password*Password', 'trim|required');

        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else {
            return $this->common->doError(func_num_args(), "done", true);
        }
    }

    function organizer_edit_submit()
    {
        if ($this->organizer_edit_validate() !== true)
            show_404();

        $exhibition_id = $this->formdata->id;

        $data = array(
            // 'exhibition_id' => $exhibition_id,
            //'officer_type' => 'organizer',
            'profile' => $this->input->post('profile'),
            'officer_designation' => $this->input->post('officer_designation'),
            'officer_company' => $this->input->post('organization'),
            'contact_person' => $this->input->post('contact_person_name'),
            'officer_phone' => $this->input->post('mobile_number'),
            'officer_email' => $this->input->post('email'),
            'login_password' => $this->input->post('password'),
            'is_deleted' => 0
        );


        $this->db->trans_start();
        $this->db
            ->set($data)
            ->where('id', $this->formdata->id)
            ->update('es_officer');

        $this->db->trans_complete();
        $this->session->set_flashdata('message', 'Organizer has been updated successfully');
        redirect(base_url('officer.html?id=' . myid($this->formdata->exhibition_id)));
    }
}
