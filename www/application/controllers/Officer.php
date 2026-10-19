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
			download_template,
			bulk_import_confirm,
			crd_list_datatable,
			crd_print' => array(
                'rule' => '@'
            ),
            'exhibitor_crd_list_datatable,
			crd_add_validate,
			bulk_import_validate,
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
        $raw_id = $this->input->get('id');
        $exhibition_id = $this->resolve_exhibition_id($raw_id);

        $event_days = array();
        if ($exhibition_id) {
            $event_dates = $this->db
                ->select('date')
                ->where('exhibition_id', $exhibition_id)
                ->order_by('date', 'ASC')
                ->get('es_exhibition_date')
                ->result();

            $app_days = $this->db
                ->select('exhibition_day, MIN(appointment_date) as app_date')
                ->where('exhibition_id', $exhibition_id)
                ->where('is_deleted', 0)
                ->where('is_canceled', 0)
                ->where("exhibition_day IS NOT NULL AND exhibition_day != ''")
                ->group_by('exhibition_day')
                ->order_by('exhibition_day', 'ASC')
                ->get('es_exhibition_appointments')
                ->result();

            $normalized_days = array();
            $day_counter = 1;
            foreach ($event_dates as $ed) {
                $d_formatted = date('d M Y', strtotime($ed->date));
                $normalized_days[$day_counter] = array(
                    'title' => 'Day ' . $day_counter,
                    'date'  => $d_formatted,
                    'label' => 'Day ' . $day_counter . ' (' . $d_formatted . ')',
                    'value' => 'Day ' . $day_counter,
                );
                $day_counter++;
            }

            foreach ($app_days as $ad) {
                $clean_day = trim($ad->exhibition_day);
                $num = preg_replace('/[^0-9]/', '', $clean_day);
                $d_formatted = !empty($ad->app_date) ? date('d M Y', strtotime($ad->app_date)) : '';

                if (!empty($num) && isset($normalized_days[(int)$num])) {
                    if (!empty($d_formatted)) {
                        $normalized_days[(int)$num]['date'] = $d_formatted;
                        $normalized_days[(int)$num]['label'] = 'Day ' . (int)$num . ' (' . $d_formatted . ')';
                    }
                } elseif (!empty($num)) {
                    $normalized_days[(int)$num] = array(
                        'title' => 'Day ' . (int)$num,
                        'date'  => $d_formatted,
                        'label' => 'Day ' . (int)$num . (!empty($d_formatted) ? ' (' . $d_formatted . ')' : ''),
                        'value' => 'Day ' . (int)$num,
                    );
                } else {
                    $normalized_days[$clean_day] = array(
                        'title' => $clean_day,
                        'date'  => $d_formatted,
                        'label' => $clean_day . (!empty($d_formatted) ? ' (' . $d_formatted . ')' : ''),
                        'value' => $clean_day,
                    );
                }
            }

            ksort($normalized_days);
            $event_days = $normalized_days;
        }

        $this->load->view('includes/after_login/head');
        $this->load->view('officer/list', array(
            'event_days' => $event_days
        ));
    }

    function crd_list_datatable()
    {
        $officer_ids = null;
        if ($this->input->get('filter_day') && $this->input->get('filter_day') != '') {
            $filter_day = trim($this->input->get('filter_day'));
            $day_num = preg_replace('/[^0-9]/', '', $filter_day);
            $raw_exh_id = $this->input->get('id');
            $exh_id = $this->resolve_exhibition_id($raw_exh_id);

            $day_cond = "";
            if (!empty($day_num)) {
                $safe_day_text = $this->db->escape('Day ' . $day_num);
                $safe_day_num  = $this->db->escape($day_num);
                $day_cond = "(exhibition_day = $safe_day_text OR exhibition_day = $safe_day_num)";
            } else {
                $safe_day = $this->db->escape($filter_day);
                $day_cond = "exhibition_day = $safe_day";
            }

            $exh_cond = $exh_id ? "AND exhibition_id = " . (int)$exh_id : "";

            $sql = "SELECT appointment_to, appointment_from 
                    FROM es_exhibition_appointments 
                    WHERE is_deleted = 0 AND is_canceled = 0 {$exh_cond} AND {$day_cond}";
            
            $app_rows = $this->db->query($sql)->result();
            $officer_ids = array();
            foreach ($app_rows as $ar) {
                if (!empty($ar->appointment_to)) $officer_ids[] = (int)$ar->appointment_to;
                if (!empty($ar->appointment_from)) $officer_ids[] = (int)$ar->appointment_from;
            }
            $officer_ids = array_unique($officer_ids);
        }

        $this->load->library('datatables');
        $this->datatables
            ->select('es_officer.id as id, 
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
            ->unset_column('officer_type');

        if (!$this->input->get('export_type')) {
            $this->datatables->add_column('col_action', function ($row) {
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
            }, NULL);
        }

        $this->datatables
            ->from('es_officer')
            ->where('es_officer.is_deleted', 0)
            ->where(mycolumn('es_officer.exhibition_id'), $this->input->get('id'));


        if ($this->input->get('filter_event')) {
            $this->datatables->join('es_exhibition_booking', 'es_exhibition_booking.agent_id = es_agent.id', 'LEFT');
            $this->datatables->where('es_exhibition_booking.exhibition_id', $this->input->get('filter_event'));
        }

        if ($this->input->get('filter_type') && $this->input->get('filter_type') != '') {
            $this->datatables->where('es_officer.officer_type', $this->input->get('filter_type'));
        }
        if ($this->input->get('filter_representative') && $this->input->get('filter_representative') != '') {
            if ($this->input->get('filter_representative') == 'representative') {
                $this->datatables->where('es_officer.is_representative', 1);
            }
            if ($this->input->get('filter_representative') == 'self') {
                $this->datatables->where('es_officer.is_representative', 0);
            }
        }

        if ($officer_ids !== null) {
            if (!empty($officer_ids)) {
                $ids_str = implode(',', array_map('intval', $officer_ids));
                $this->datatables->where("es_officer.id IN ($ids_str)", NULL, FALSE);
            } else {
                $this->datatables->where("es_officer.id = 0", NULL, FALSE);
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

        $this->_send_officer_credentials_email(
            $this->input->post('officer_name'),
            $this->input->post('officer_email'),
            $this->input->post('login_password'),
            'officer',
            isset($this->formdata->id) ? $this->formdata->id : null,
            isset($this->formdata->exhibition_title) ? $this->formdata->exhibition_title : null
        );

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

        $this->_send_officer_credentials_email(
            $this->input->post('contact_person_name'),
            $this->input->post('email'),
            $this->input->post('password'),
            'foreign_delegates',
            isset($this->formdata->id) ? $this->formdata->id : null,
            isset($this->formdata->exhibition_title) ? $this->formdata->exhibition_title : null
        );

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

        $this->_send_officer_credentials_email(
            $this->input->post('contact_person_name'),
            $this->input->post('email'),
            $this->input->post('password'),
            'local_delegates',
            isset($this->formdata->id) ? $this->formdata->id : null,
            isset($this->formdata->exhibition_title) ? $this->formdata->exhibition_title : null
        );

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

        $this->_send_officer_credentials_email(
            $this->input->post('contact_person_name'),
            $this->input->post('email'),
            $this->input->post('password'),
            'chief_of_servicing',
            isset($this->formdata->id) ? $this->formdata->id : null,
            isset($this->formdata->exhibition_title) ? $this->formdata->exhibition_title : null
        );

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

        $this->_send_officer_credentials_email(
            $this->input->post('contact_person_name'),
            $this->input->post('email'),
            $this->input->post('password'),
            'armed_force',
            isset($this->formdata->id) ? $this->formdata->id : null,
            isset($this->formdata->exhibition_title) ? $this->formdata->exhibition_title : null
        );

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

        $this->_send_officer_credentials_email(
            $this->input->post('contact_person_name'),
            $this->input->post('email'),
            $this->input->post('password'),
            'government_officials',
            isset($this->formdata->id) ? $this->formdata->id : null,
            isset($this->formdata->exhibition_title) ? $this->formdata->exhibition_title : null
        );

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

        $this->_send_officer_credentials_email(
            $this->input->post('contact_person_name'),
            $this->input->post('email'),
            $this->input->post('password'),
            'organizer',
            isset($this->formdata->id) ? $this->formdata->id : null,
            isset($this->formdata->exhibition_title) ? $this->formdata->exhibition_title : null
        );

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

    private function _send_officer_credentials_email($receiver_name, $receiver_email, $password, $officer_type = '', $exhibition_id = null, $exhibition_title = null)
    {
        if (empty($receiver_email) || empty($password)) {
            return false;
        }

        $type_labels = array(
            'foreign_delegates'   => 'Foreign Delegate',
            'local_delegates'     => 'Local Delegate',
            'chief_of_servicing' => 'Gov. Chief of Servicing',
            'armed_force'        => 'Armed Forces Officer',
            'government_officials' => 'Government Official',
            'organizer'           => 'Organizer',
            'officer'             => 'Officer'
        );

        $officer_type_label = isset($type_labels[$officer_type]) ? $type_labels[$officer_type] : 'B2B User';

        // Auto-resolve exhibition title if missing but exhibition_id is present
        if (!empty($exhibition_id) && empty($exhibition_title)) {
            $exh = $this->db->select('exhibition_title')->where('id', $exhibition_id)->get('es_exhibitions')->row();
            if ($exh) {
                $exhibition_title = $exh->exhibition_title;
            }
        }

        // Construct dynamic meeting portal login URL (e.g. meeting/login/27-Dummy-Event-2)
        if (!empty($exhibition_id) && !empty($exhibition_title)) {
            $slug_title = str_replace(' ', '-', trim($exhibition_title));
            $portal_url = base_url('meeting/login/' . $exhibition_id . '-' . $slug_title);
        } else {
            $portal_url = base_url('meeting/');
        }

        $this->load->model('email_configuration_model');
        $settings = $this->email_configuration_model->get_mailer_settings();

        $message_body = $this->load->view('officer/email_credentials_template', array(
            'receiver_name'      => $receiver_name ?: 'Valued User',
            'receiver_email'     => $receiver_email,
            'password'           => $password,
            'officer_type_label' => $officer_type_label,
            'portal_url'         => $portal_url
        ), true);

        $subject = PROJECT_NAME . ' - Your B2B Account Credentials';

        if (isset($settings['enable_email_queue']) && $settings['enable_email_queue'] === 'no') {
            // Direct SMTP dispatch if queue is explicitly disabled
            $this->load->helper('phpmailer');
            $sendResult = sendMail(
                $receiver_name ?: 'B2B User',
                $receiver_email,
                $subject,
                $message_body,
                $settings['mail_from_name'],
                $settings['mail_from_email'],
                !empty($settings['cc_email']) ? $settings['cc_email'] : '',
                array(
                    'settings' => $settings,
                    'echo'     => false,
                    'debug'    => ($settings['smtp_debug'] === 'yes')
                )
            );

            if (method_exists($this->email_configuration_model, 'log_email')) {
                $this->email_configuration_model->log_email(array(
                    'recipient_email' => $receiver_email,
                    'recipient_name'  => $receiver_name,
                    'subject'          => $subject,
                    'message_body'     => $message_body,
                    'status'           => (!empty($sendResult['success']) ? 'sent' : 'failed'),
                    'error_message'    => (!empty($sendResult['error']) ? $sendResult['error'] : null),
                    'sent_at'          => date('Y-m-d H:i:s')
                ));
            }

            return $sendResult;
        } else {
            // Queue into es_emails_cron for instant non-blocking form save (0.001s response time)
            $queue_data = array(
                'type'       => 'B2B_CREDENTIALS',
                'from_name'  => !empty($settings['mail_from_name']) ? $settings['mail_from_name'] : PROJECT_NAME,
                'email'      => $receiver_email,
                'cc_email'   => !empty($settings['cc_email']) ? $settings['cc_email'] : null,
                'subject'    => $subject,
                'message'    => $message_body,
                'status'     => 'pending',
                'created_on' => date('Y-m-d H:i:s')
            );

            return $this->db->insert('es_emails_cron', $queue_data);
        }
    }

    /**
     * Helper to resolve integer exhibition ID from numeric ID or encrypted hash
     */
    protected function resolve_exhibition_id($raw_id)
    {
        if (empty($raw_id)) {
            return null;
        }

        if (is_numeric($raw_id) && (int)$raw_id > 0) {
            $check = $this->db->select('id')->where('id', (int)$raw_id)->where('is_deleted', 0)->get('es_exhibitions')->row();
            if ($check) {
                return (int)$check->id;
            }
        }

        $key = config_item('encryption_key');
        $key_esc = $this->db->escape($key);
        $id_esc = $this->db->escape($raw_id);
        $r = $this->db->select('id')->where("MD5(CONCAT({$key_esc}, `id`)) = {$id_esc}")->where('is_deleted', 0)->get('es_exhibitions')->row();
        if ($r) {
            return (int)$r->id;
        }

        return null;
    }

    /**
     * Download structured Excel template for bulk B2B User import
     */
    public function download_template()
    {
        $raw_id = $this->input->get('exhibition_id');
        if (empty($raw_id)) {
            show_error('Exhibition ID is required to download the template.');
        }

        $exhibition_id = $this->resolve_exhibition_id($raw_id);
        if (!$exhibition_id) {
            show_error('Invalid Exhibition ID specified.');
        }

        $exhibition = $this->db->select('id, exhibition_title')
            ->where('id', $exhibition_id)
            ->where('is_deleted', 0)
            ->get('es_exhibitions')
            ->row();

        if (!$exhibition) {
            show_error('Selected Exhibition does not exist.');
        }

        // 1. Fetch valid countries list
        $countries_data = array(
            array('Country Name')
        );
        $countries_res = get_instance()->funcs->get_input_data_list('country');
        if (!empty($countries_res)) {
            foreach ($countries_res as $c) {
                $countries_data[] = array($c->data);
            }
        } else {
            $countries_data[] = array('Pakistan');
        }

        // 2. Officer Categories Reference
        $categories_data = array(
            array('Category Name', 'Slug / Code', 'Description'),
            array('Foreign Delegates', 'foreign_delegates', 'International foreign delegations and dignitaries'),
            array('Local Delegates', 'local_delegates', 'Domestic Pakistani VIP delegates and organization leads'),
            array('Armed Forces (Pakistan)', 'armed_force', 'Pakistani Armed Forces personnel (Army, Navy, Air Force)'),
            array('Government Officials', 'government_officials', 'Pakistani Government and Ministry dignitaries/officials'),
            array('Organizers', 'organizer', 'Event organizing team members and coordinators'),
            array('Gov. Services Chief', 'chief_of_servicing', 'Chiefs of government civil/defence servicing departments')
        );

        // 3. Main Import Sheet
        $main_sheet_data = array(
            array('EVENT ID', (int)$exhibition_id),
            array('EVENT NAME', $exhibition->exhibition_title),
            array('INSTRUCTIONS: Please do not modify rows 1-3. Fill B2B User details from Row 5 onwards. Reference tabs contain valid categories and countries.'),
            array(
                'Officer Type',
                'Contact Person Name',
                'Designation',
                'Email Address',
                'Mobile Number',
                'Country',
                'Organization',
                'Rank',
                'Profile',
                'Representative (Yes/No)',
                'Login Password (Optional)'
            ),
            array(
                'Foreign Delegates',
                'John Doe',
                'Director General',
                'john.doe@example.com',
                '+1234567890',
                'United Kingdom',
                'Ministry of Defence',
                'Colonel',
                'Head of Delegation',
                'No',
                'Pass12345'
            )
        );

        $lib_path = APPPATH . 'libraries' . DIRECTORY_SEPARATOR . 'SimpleXLSXGen.php';
        $lib_path = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $lib_path);
        require_once($lib_path);

        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($main_sheet_data, 'B2B Users Import');
        $xlsx->addSheet($categories_data, 'Valid Categories');
        $xlsx->addSheet($countries_data, 'Valid Countries');

        $safe_title = preg_replace('/[^A-Za-z0-9_\-]/', '_', $exhibition->exhibition_title);
        $filename = 'b2b_users_bulk_import_' . $safe_title . '.xlsx';
        $xlsx->downloadAs($filename);
        exit;
    }

    /**
     * Validate uploaded Excel file for B2B Users
     */
    public function bulk_import_validate()
    {
        $raw_id = $this->input->post('exhibition_id');
        if (empty($raw_id)) {
            echo json_encode(array('status' => 'error', 'message' => 'Please select an Exhibition first.'));
            return;
        }

        $exhibition_id = $this->resolve_exhibition_id($raw_id);
        if (!$exhibition_id) {
            echo json_encode(array('status' => 'error', 'message' => 'Invalid Exhibition specified.'));
            return;
        }

        $exhibition = $this->db->select('id, exhibition_title')
            ->where('id', $exhibition_id)
            ->where('is_deleted', 0)
            ->get('es_exhibitions')
            ->row();

        if (!$exhibition) {
            echo json_encode(array('status' => 'error', 'message' => 'Selected Exhibition does not exist.'));
            return;
        }

        if (empty($_FILES['import_file']['name'])) {
            echo json_encode(array('status' => 'error', 'message' => 'Please upload an Excel file (.xlsx).'));
            return;
        }

        $config['upload_path'] = FCPATH . 'uploads' . DIRECTORY_SEPARATOR;
        $config['upload_path'] = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $config['upload_path']);

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, true);
        }

        $config['allowed_types'] = 'xlsx';
        $config['max_size'] = 5120;
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('import_file')) {
            echo json_encode(array('status' => 'error', 'message' => $this->upload->display_errors('', '')));
            return;
        }

        $upload_data = $this->upload->data();
        $file_path = $upload_data['full_path'];

        $lib_path = APPPATH . 'libraries' . DIRECTORY_SEPARATOR . 'SimpleXLSX.php';
        $lib_path = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $lib_path);
        require_once($lib_path);

        if ($xlsx = \Shuchkin\SimpleXLSX::parse($file_path)) {
            $rows = $xlsx->rows();

            if (is_file($file_path)) {
                unlink($file_path);
            }

            if (count($rows) <= 4) {
                echo json_encode(array('status' => 'error', 'message' => 'Excel file is empty or contains no records. It must contain metadata in rows 1-3, headers in row 4, and data starting at row 5.'));
                return;
            }

            // Validate Event ID metadata
            $excel_event_id = isset($rows[0][1]) ? (int)trim($rows[0][1]) : 0;
            if ($excel_event_id !== (int)$exhibition_id) {
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Event mismatch! The uploaded template belongs to Event ID ' . $excel_event_id . ', but you selected "' . $exhibition->exhibition_title . '" (ID: ' . $exhibition_id . '). Please upload the correct template for this event.'
                ));
                return;
            }

            // Category normalization map
            $type_map = array(
                'foreign delegates'       => 'foreign_delegates',
                'foreign_delegates'       => 'foreign_delegates',
                'foreign delegate'        => 'foreign_delegates',
                'local delegates'         => 'local_delegates',
                'local_delegates'         => 'local_delegates',
                'local delegate'          => 'local_delegates',
                'armed forces (pakistan)' => 'armed_force',
                'armed forces'            => 'armed_force',
                'armed force'             => 'armed_force',
                'armed_force'             => 'armed_force',
                'government officials'    => 'government_officials',
                'government official'     => 'government_officials',
                'government_officials'    => 'government_officials',
                'government_official'     => 'government_officials',
                'organizers'              => 'organizer',
                'organizer'               => 'organizer',
                'chief of servicing'      => 'chief_of_servicing',
                'gov. services chief'     => 'chief_of_servicing',
                'chief_of_servicing'      => 'chief_of_servicing'
            );

            $type_display_labels = array(
                'foreign_delegates'    => 'Foreign Delegate',
                'local_delegates'      => 'Local Delegate',
                'armed_force'          => 'Armed Force (Pakistan)',
                'government_officials' => 'Government Official',
                'organizer'            => 'Organizer',
                'chief_of_servicing'   => 'Gov. Services Chief'
            );

            // Fetch existing emails for this exhibition
            $existing_emails = $this->db->select('LOWER(officer_email) as email')
                ->where('exhibition_id', $exhibition_id)
                ->where('is_deleted', 0)
                ->get('es_officer')
                ->result();

            $existing_email_set = array();
            foreach ($existing_emails as $ee) {
                if (!empty($ee->email)) {
                    $existing_email_set[trim($ee->email)] = true;
                }
            }

            $validated_rows = array();
            $batch_emails = array();
            $has_errors = false;
            $row_number = 4; // Excel row numbering (0-based idx 4 is Excel Row 5)

            for ($idx = 4; $idx < count($rows); $idx++) {
                $row = $rows[$idx];
                $row_number++;

                $is_empty_row = true;
                foreach ($row as $cell) {
                    if (trim((string)$cell) !== '') {
                        $is_empty_row = false;
                        break;
                    }
                }
                if ($is_empty_row) {
                    continue;
                }

                $row_errors = array();

                $raw_type       = trim($row[0] ?? '');
                $contact_person = trim($row[1] ?? '');
                $designation    = trim($row[2] ?? '');
                $email          = trim($row[3] ?? '');
                $phone          = trim($row[4] ?? '');
                $country        = trim($row[5] ?? '');
                $organization   = trim($row[6] ?? '');
                $rank           = trim($row[7] ?? '');
                $profile        = trim($row[8] ?? '');
                $representative = trim($row[9] ?? '');
                $password       = trim($row[10] ?? '');

                // 1. Validate Officer Type
                $type_key = strtolower($raw_type);
                $canonical_type = '';
                if (empty($raw_type)) {
                    $row_errors[] = 'Officer Type is required.';
                } elseif (isset($type_map[$type_key])) {
                    $canonical_type = $type_map[$type_key];
                } else {
                    $row_errors[] = 'Invalid Officer Type "' . $raw_type . '". See "Valid Categories" sheet.';
                }

                // 2. Validate Contact Person
                if (empty($contact_person)) {
                    $row_errors[] = 'Contact Person Name is required.';
                }

                // 3. Validate Designation
                if (empty($designation)) {
                    $row_errors[] = 'Designation is required.';
                }

                // 4. Validate Email
                if (empty($email)) {
                    $row_errors[] = 'Email Address is required.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $row_errors[] = 'Invalid Email Address format: "' . $email . '".';
                } else {
                    $norm_email = strtolower($email);
                    if (isset($batch_emails[$norm_email])) {
                        $row_errors[] = 'Duplicate email within this Excel sheet (already in Row ' . $batch_emails[$norm_email] . ').';
                    } else {
                        $batch_emails[$norm_email] = $row_number;
                    }

                    if (isset($existing_email_set[$norm_email])) {
                        $row_errors[] = 'Email "' . $email . '" is already registered for this event.';
                    }
                }

                // 5. Country handling
                if ($canonical_type === 'foreign_delegates') {
                    if (empty($country)) {
                        $row_errors[] = 'Country is required for Foreign Delegates.';
                    }
                } else {
                    if (empty($country)) {
                        $country = 'Pakistan';
                    }
                }

                // 6. Organization handling
                if (in_array($canonical_type, array('local_delegates', 'armed_force', 'organizer')) && empty($organization)) {
                    $organization = $designation; // Fallback to designation if empty
                }

                // 7. Representative flag
                $is_rep = 0;
                $rep_lower = strtolower($representative);
                if (in_array($rep_lower, array('yes', '1', 'true', 'representative', 'y'))) {
                    $is_rep = 1;
                }

                // 8. Password generation
                if (empty($password)) {
                    $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                    $password = substr(str_shuffle($chars), 0, 8);
                }

                if (!empty($row_errors)) {
                    $has_errors = true;
                }

                $validated_rows[] = array(
                    'row_idx'          => $row_number,
                    'officer_type'     => $canonical_type,
                    'type_label'       => isset($type_display_labels[$canonical_type]) ? $type_display_labels[$canonical_type] : $raw_type,
                    'contact_person'   => $contact_person,
                    'designation'      => $designation,
                    'email'            => $email,
                    'phone'            => $phone,
                    'country'          => $country,
                    'organization'     => $organization,
                    'rank'             => $rank,
                    'profile'          => $profile,
                    'is_representative'=> $is_rep,
                    'password'         => $password,
                    'status'           => empty($row_errors) ? 'valid' : 'error',
                    'errors'           => $row_errors
                );
            }

            $valid_count = count(array_filter($validated_rows, function($r) { return $r['status'] === 'valid'; }));
            $error_count = count(array_filter($validated_rows, function($r) { return $r['status'] === 'error'; }));

            echo json_encode(array(
                'status'         => 'success',
                'has_errors'     => $has_errors,
                'total_records'  => count($validated_rows),
                'valid_count'    => $valid_count,
                'error_count'    => $error_count,
                'rows'           => $validated_rows
            ));
            return;
        } else {
            echo json_encode(array('status' => 'error', 'message' => \Shuchkin\SimpleXLSX::parseError()));
            return;
        }
    }

    /**
     * Confirm and batch insert validated B2B Users
     */
    public function bulk_import_confirm()
    {
        $raw_id = $this->input->post('exhibition_id');
        $rows_json = $this->input->post('rows');
        $send_credentials = $this->input->post('send_credentials') ? true : false;

        if (empty($raw_id) || empty($rows_json)) {
            echo json_encode(array('status' => 'error', 'message' => 'Invalid submission data.'));
            return;
        }

        $exhibition_id = $this->resolve_exhibition_id($raw_id);
        if (!$exhibition_id) {
            echo json_encode(array('status' => 'error', 'message' => 'Invalid Exhibition specified.'));
            return;
        }

        $exhibition = $this->db->select('id, exhibition_title')->where('id', $exhibition_id)->where('is_deleted', 0)->get('es_exhibitions')->row();
        if (!$exhibition) {
            echo json_encode(array('status' => 'error', 'message' => 'Selected Exhibition does not exist.'));
            return;
        }

        $import_rows = is_array($rows_json) ? $rows_json : json_decode($rows_json, true);
        if (empty($import_rows)) {
            echo json_encode(array('status' => 'error', 'message' => 'No valid rows found to import.'));
            return;
        }

        $inserted = 0;
        $this->db->trans_start();

        foreach ($import_rows as $row) {
            if (($row['status'] ?? '') !== 'valid') {
                continue;
            }

            $officer_type = trim($row['officer_type'] ?? '');
            $contact_person = trim($row['contact_person'] ?? '');
            $designation = trim($row['designation'] ?? '');
            $email = trim($row['email'] ?? '');
            $phone = trim($row['phone'] ?? '');
            $country = trim($row['country'] ?? '');
            $organization = trim($row['organization'] ?? '');
            $rank = trim($row['rank'] ?? '');
            $profile = trim($row['profile'] ?? '');
            $is_rep = (int)($row['is_representative'] ?? 0);
            $password = trim($row['password'] ?? '');

            if (empty($officer_type) || empty($contact_person) || empty($email)) {
                continue;
            }

            $data = array(
                'exhibition_id'        => $exhibition_id,
                'officer_type'         => $officer_type,
                'contact_person'       => $contact_person,
                'officer_designation'  => $designation,
                'officer_email'        => $email,
                'officer_phone'        => $phone,
                'officer_country'      => !empty($country) ? $country : 'Pakistan',
                'officer_company'      => $organization,
                'officer_rank'         => $rank,
                'profile'              => $profile,
                'is_representative'    => $is_rep,
                'login_password'       => $password,
                'created_on'           => date('Y-m-d H:i:s'),
                'is_deleted'           => 0
            );

            $this->db->insert('es_officer', $data);
            $inserted++;

            if ($send_credentials) {
                $this->_send_officer_credentials_email(
                    $contact_person,
                    $email,
                    $password,
                    $officer_type,
                    $exhibition_id,
                    $exhibition->exhibition_title
                );
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(array('status' => 'error', 'message' => 'Database error occurred while importing records.'));
            return;
        }

        echo json_encode(array(
            'status'         => 'success',
            'imported_count' => $inserted,
            'message'        => "Successfully imported {$inserted} B2B Users!" . ($send_credentials ? " Credentials emails have been sent." : "")
        ));
    }
}

