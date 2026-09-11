<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Meeting_report extends MY_Controller
{
    protected function rule()
    {
        $this->activateRightsSystem();
        $crd = array(
            'crd_list,crd_exhibition_list,download_template,bulk_import_validate,bulk_import_confirm,
			' => array(
                'rule' => '@'
            ),
            'crd_list_datatable,crd_exhibition_list_datatable,get_meeting_details_ajax,
			' => array(
                'rule' => '@'
            )
        );
        $this->load->model('usermdl');
        $this->myparent = base_url('stalls.html');
        return array_merge($crd);
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

    protected function getOrderData()
    {
        $id = $this->input->get('order_id');
        if (is_null($id))
            show_404();
        $id = $this->db->escape($id);
        $key = config_item('encryption_key');
        $key = $this->db->escape($key);
        $r = $this->db
            ->where("MD5(CONCAT($key,`id`)) = $id")
            ->get('es_exhibition_booking');
        if ($r->num_rows() == 0)
            show_404();
        $this->orderdata = $r->row();
    }

    function crd_list()
    {
        $this->load->view('includes/after_login/head');
        $this->load->view('meeting_report/list');
    }

    function crd_list_datatable()
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
                    ->where('is_approved', 1)
                    ->count_all_results('my_appointments_datatable');
            }, NULL)
            ->add_column('col_action', function ($row) {
                $id = $row['id'];
                $html = '<a href="' . base_url() . 'meeting-report_status-list.html?id=' . urlencode(myid($id)) . '">Report</a>';

                return "<div class='text-center'>{$html}</div>";
            }, NULL)
            ->where('E.is_deleted', 0)
            ->join('es_locations as L', 'E.location_id = L.id', 'LEFT')
            ->from('es_exhibitions as E');

        print ($this->datatables->generate());
    }

    function crd_exhibition_list()
    {
		$this->checkEditId();
		$exhibition_id = $this->formdata->id;

		$event_halls = $this->db
			->select('EH.*, H.hall_title')
			->where('EH.exhibition_id', $exhibition_id)
			->join('es_location_halls as H', 'H.id = EH.hall_id')
			->get('es_exhibition_halls as EH')
			->result();

		// Fetch Event Days from official date schedule
		$event_dates = $this->db
			->select('date')
			->where('exhibition_id', $exhibition_id)
			->order_by('date', 'ASC')
			->get('es_exhibition_date')
			->result();

		// Fetch existing days & dates recorded in appointments
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

		// Calculate Summary Metrics
		$summary_stats = $this->get_meeting_summary_stats($exhibition_id, $event_halls);

        $this->load->view('includes/after_login/head');
        $this->load->view('meeting_report/exhibition_list', array(
			'event_halls'   => $event_halls,
			'event_days'    => $event_days,
			'summary_stats' => $summary_stats,
			'exhibition'    => $this->formdata,
		));
    }

    function crd_exhibition_list_datatable()
    {
		$customers_ids = array();
		if ($this->input->get('filter_hall') && $this->input->get('filter_hall') != '') {
			$hall_customers = $this->db
				->select('customer_id')
				->where('hall_id', $this->input->get('filter_hall'))
				->where(mycolumn('exhibition_id'), $this->input->get('id'))
				->group_by('customer_id')
				->get('es_exhibition_booking_stalls')
				->result();
				
			foreach ($hall_customers as $c) {
				$customers_ids[] = $c->customer_id;
			}
		}

        $this->load->library('datatables');
        $this->datatables
            ->select('id,
            		exhibition_id,
                   
                  appointment_from_company_name,
                  appointment_to_company_name,
                     exhibition_day,
                     appointment_date,
                     appointment_time,
                     user_type_to,
                     user_type_from,
                     appointment_to,
                     appointment_from,
                     IF(is_canceled = 1, "<span class=\'label label-danger\'>Canceled</span>",
                    	IF(is_approved = 1, "<span class=\'label label-success\'>Approved</span>", "<span class=\'label label-warning\'>Pending</span>")) as status,
					is_conducted,
					appointment_feedback,
					is_canceled,
                    is_approved,
   				  ', false)


			->unset_column('is_conducted')
			->unset_column('appointment_feedback')
			->unset_column('is_canceled')
			->unset_column('is_approved')
            ->unset_column('exhibition_id')
            ->unset_column('user_type_to')
            ->unset_column('user_type_from')
            ->unset_column('appointment_to')
            ->unset_column('appointment_from')

			->add_column('agenda', function ($row) {
				$id = $row['id'];
				
				return '<div class="text-center"><button type="button" class="btn btn-link" onclick="show_agenda(\''.myid($id).'\')">View Details</button></div>';
			}, NULL)

			// ->add_column('col_conducted', function ($row) {
			// 	$id = $row['id'];
			// 	$is_conducted = $row['is_conducted'];
				
			// 	if ($row['is_approved'] == 1) {
			// 		if ($row['is_conducted'] == 1) {
			// 			return '<div class="text-left small">'.$row['appointment_feedback'].'</div>';
			// 		} else {
			// 			return '<div class="text-center small">N/A</div>';
			// 		}
			// 	} else {
			// 		return '<div class="text-center">-</div>';
			// 	}
			// }, NULL)

            //->where('is_approved', 1)
			->where('is_deleted', 0)
			->where('is_canceled', 0)
            ->where(mycolumn('exhibition_id'), $this->input->get('id'))
            ->from('my_appointments_datatable');

		if ($this->input->get('export_type') && $this->input->get('export_type') != '') {
			
			$this->datatables->add_column('country', function ($row) {
				$customer_data = $this->db->select('appointment_from')->where('id', $row['id'])->get('es_exhibition_appointments')->row();
				$data = $this->db->select('country')->where('id', $customer_data->appointment_from)->get('es_customers')->row();
				return $data->country;
			}, NULL);

			$this->datatables->add_column('agenda_of_meeting', function ($row) {
				$data = $this->db->select('agenda_of_meeting')->where('id', $row['id'])->get('es_exhibition_appointments')->row();
				return $data->agenda_of_meeting;
			}, NULL);
			
			$this->datatables->add_column('discussion_points', function ($row) {
				$data = $this->db->select('discussion_points')->where('id', $row['id'])->get('es_exhibition_appointments')->row();
				return $data->discussion_points;
			}, NULL);

			$this->datatables->add_column('is_conducted', function ($row) {
				return ($row['is_conducted'] == 1) ? 'Yes' : 'No';
			}, NULL);

			$this->datatables->add_column('appointment_feedback', function ($row) {
				return $row['appointment_feedback'];
			}, NULL);

		} else {
			
		}


		if ($this->input->get('filter_status') && $this->input->get('filter_status') != '') {
			if ($this->input->get('filter_status') == 'approved') {
				$this->datatables->where('is_approved', 1);
			} else if ($this->input->get('filter_status') == 'pending') {
				$this->datatables->where('is_approved', 0);
			}
		}
        if ($this->input->get('filter_user_type') && $this->input->get('filter_user_type') != '') {
			if ($this->input->get('filter_user_type') == 'exhibitor_exhibitor') {
				$this->datatables->where('user_type_from', 'exhibitor');
                $this->datatables->where('user_type_to' , 'exhibitor');
			} else if ($this->input->get('filter_user_type') == 'exhibitor_others') {
				$this->datatables->where('user_type_from != user_type_to');
			}
		}

		if ($this->input->get('filter_day') && $this->input->get('filter_day') != '') {
			$filter_day = trim($this->input->get('filter_day'));
			$day_num = preg_replace('/[^0-9]/', '', $filter_day);
			if (!empty($day_num)) {
				$safe_day_text = $this->db->escape('Day ' . $day_num);
				$safe_day_num  = $this->db->escape($day_num);
				$this->datatables->where("(exhibition_day = $safe_day_text OR exhibition_day = $safe_day_num)", NULL, FALSE);
			} else {
				$safe_day = $this->db->escape($filter_day);
				$this->datatables->where("exhibition_day = $safe_day", NULL, FALSE);
			}
		}

		if ($this->input->get('filter_hall') && $this->input->get('filter_hall') != '') {
			if (!empty($customers_ids)) {
				$ids_str = implode(',', array_map('intval', $customers_ids));
				$this->datatables->where("(appointment_from IN ($ids_str) OR appointment_to IN ($ids_str))", NULL, FALSE);
			} else {
				$this->datatables->where("appointment_from = 0 AND appointment_to = 0", NULL, FALSE);
			}
		}

        print ($this->datatables->generate());
    }

	function get_meeting_details_ajax() {

		$id = $this->input->post('id');
		
		$meeting = $this->db->where(mycolumn(), $id)->get('es_exhibition_appointments')->row();


		echo json_encode(array(
			'error' => 0,
			'message' => 'Meeting details!',
			'data' => $meeting,
		));
		die;
	}

	/**
	 * Compute summary metrics (total, approved, pending, day-wise, hall-wise)
	 */
	protected function get_meeting_summary_stats($exhibition_id, $event_halls = array())
	{
		$total = $this->db
			->where('exhibition_id', $exhibition_id)
			->where('is_deleted', 0)
			->where('is_canceled', 0)
			->count_all_results('es_exhibition_appointments');

		$approved = $this->db
			->where('exhibition_id', $exhibition_id)
			->where('is_deleted', 0)
			->where('is_canceled', 0)
			->where('is_approved', 1)
			->count_all_results('es_exhibition_appointments');

		$pending = $this->db
			->where('exhibition_id', $exhibition_id)
			->where('is_deleted', 0)
			->where('is_canceled', 0)
			->where('is_approved', 0)
			->count_all_results('es_exhibition_appointments');

		$days_breakdown = $this->db
			->select('exhibition_day, COUNT(*) as count')
			->where('exhibition_id', $exhibition_id)
			->where('is_deleted', 0)
			->where('is_canceled', 0)
			->where("exhibition_day IS NOT NULL AND exhibition_day != ''")
			->group_by('exhibition_day')
			->order_by('exhibition_day', 'ASC')
			->get('es_exhibition_appointments')
			->result_array();

		$halls_breakdown = array();
		foreach ($event_halls as $eh) {
			$hall_id = $eh->hall_id;
			$hall_title = $eh->hall_title;

			$customers = $this->db
				->select('customer_id')
				->where('hall_id', $hall_id)
				->where('exhibition_id', $exhibition_id)
				->group_by('customer_id')
				->get('es_exhibition_booking_stalls')
				->result();

			$cust_ids = array();
			foreach ($customers as $c) {
				$cust_ids[] = (int)$c->customer_id;
			}

			$this->db->where('exhibition_id', $exhibition_id)
				->where('is_deleted', 0)
				->where('is_canceled', 0);

			if (!empty($cust_ids)) {
				$ids_str = implode(',', $cust_ids);
				$safe_title = $this->db->escape('%' . $hall_title . '%');
				$this->db->where("(appointment_from IN ($ids_str) OR appointment_to IN ($ids_str) OR meeting_location LIKE $safe_title)");
			} else {
				$safe_title = $this->db->escape('%' . $hall_title . '%');
				$this->db->where("meeting_location LIKE $safe_title");
			}

			$count = $this->db->count_all_results('es_exhibition_appointments');
			$halls_breakdown[] = array(
				'hall_id'    => $hall_id,
				'hall_title' => $hall_title,
				'count'      => $count,
			);
		}

		return array(
			'total'           => $total,
			'approved'        => $approved,
			'pending'         => $pending,
			'days_breakdown'  => $days_breakdown,
			'halls_breakdown' => $halls_breakdown,
		);
	}

	/**
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
	 * Download bulk import sample Excel template with reference tabs
	 */
	function download_template()
	{
		$raw_id = $this->input->get('exhibition_id') ? $this->input->get('exhibition_id') : $this->input->get('id');
		$exhibition_id = $this->resolve_exhibition_id($raw_id);

		if (empty($exhibition_id)) {
			show_error('Exhibition ID is required to download the template.');
		}

		$exhibition = $this->db->select('id, exhibition_title')
			->where('id', $exhibition_id)
			->where('is_deleted', 0)
			->get('es_exhibitions')
			->row();

		if (!$exhibition) {
			show_error('Selected Exhibition does not exist.');
		}

		// 1. Fetch active event exhibitors
		$exhibitors = $this->db->select('C.company, C.name, C.email')
			->from('es_exhibition_booking as B')
			->join('es_customers as C', 'B.customer_id = C.id')
			->where('B.exhibition_id', $exhibition_id)
			->where('B.is_canceled', 0)
			->where('C.is_deleted', 0)
			->where('C.is_active', 1)
			->group_by('C.id')
			->get()
			->result();

		$exhibitors_data = array(
			array('Company Name', 'Contact Person', 'Email Address')
		);
		$sample_from_email = 'sender@example.com';
		$sample_to_email = 'receiver@example.com';
		if (count($exhibitors) > 0) {
			$sample_from_email = $exhibitors[0]->email;
			if (isset($exhibitors[1])) {
				$sample_to_email = $exhibitors[1]->email;
			} else {
				$sample_to_email = $exhibitors[0]->email;
			}
		}
		foreach ($exhibitors as $e) {
			$exhibitors_data[] = array(
				$e->company,
				$e->name,
				$e->email
			);
		}

		// 2. Fetch event dates
		$dates = $this->db->select('date, open_time, closing_time')
			->where('exhibition_id', $exhibition_id)
			->order_by('date', 'ASC')
			->get('es_exhibition_date')
			->result();

		$dates_data = array(
			array('Exhibition Day', 'Meeting Date', 'Operating Hours')
		);
		$day_counter = 1;
		$sample_day = 'Day 1';
		$sample_date = date('Y-m-d');
		foreach ($dates as $d) {
			$day_name = 'Day ' . $day_counter;
			if ($day_counter === 1) {
				$sample_date = $d->date;
			}
			$open = !empty($d->open_time) ? date('H:i', strtotime($d->open_time)) : '09:00';
			$close = !empty($d->closing_time) ? date('H:i', strtotime($d->closing_time)) : '18:00';
			$dates_data[] = array(
				$day_name,
				$d->date,
				$open . ' - ' . $close
			);
			$day_counter++;
		}

		// 3. Fetch event halls & locations
		$halls = $this->db->select('H.hall_title')
			->from('es_exhibition_halls as EH')
			->join('es_location_halls as H', 'H.id = EH.hall_id')
			->where('EH.exhibition_id', $exhibition_id)
			->get()
			->result();

		$locations_data = array(
			array('Hall / Location Name')
		);
		$sample_location = 'Hall 1';
		if (count($halls) > 0) {
			$sample_location = $halls[0]->hall_title;
		}
		foreach ($halls as $h) {
			$locations_data[] = array($h->hall_title);
		}

		// 4. Generate Main Import Sheet
		$main_sheet_data = array(
			array('EVENT ID', (int)$exhibition_id),
			array('EVENT NAME', $exhibition->exhibition_title),
			array('INSTRUCTIONS: Please do not modify rows 1-3. Fill meeting schedules from Row 5 onwards. Reference tabs at the bottom contain valid exhibitors, dates, and halls.'),
			array(
				'Exhibition Day', 
				'Meeting Date', 
				'Meeting Time', 
				'Meeting From Email', 
				'Meeting To Email', 
				'Location', 
				'Agenda of Meeting', 
				'Discussion Points'
			),
			array(
				$sample_day,
				$sample_date,
				'11:00',
				$sample_from_email,
				$sample_to_email,
				$sample_location,
				'B2B Corporate Trade Meeting',
				"Discussion on mutual business partnerships and commercial cooperation."
			)
		);

		$lib_path = APPPATH . 'libraries' . DIRECTORY_SEPARATOR . 'SimpleXLSXGen.php';
		$lib_path = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $lib_path);
		require_once($lib_path);

		$xlsx = \Shuchkin\SimpleXLSXGen::fromArray($main_sheet_data, 'Meetings Import');
		$xlsx->addSheet($exhibitors_data, 'Valid Exhibitors');
		$xlsx->addSheet($dates_data, 'Event Days & Dates');
		$xlsx->addSheet($locations_data, 'Halls & Locations');

		$safe_title = preg_replace('/[^A-Za-z0-9_\-]/', '_', $exhibition->exhibition_title);
		$filename = 'meeting_bulk_import_' . $safe_title . '.xlsx';
		$xlsx->downloadAs($filename);
		exit;
	}

	/**
	 * AJAX validation for uploaded Excel file
	 */
	function bulk_import_validate()
	{
		$raw_id = $this->input->post('exhibition_id') ? $this->input->post('exhibition_id') : $this->input->get('id');
		$exhibition_id = $this->resolve_exhibition_id($raw_id);
		if (empty($exhibition_id)) {
			echo json_encode(array('status' => 'error', 'message' => 'Please select a valid Exhibition first.'));
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
			echo json_encode(array('status' => 'error', 'message' => 'Please upload an Excel file.'));
			return;
		}

		$config['upload_path'] = FCPATH . 'uploads' . DIRECTORY_SEPARATOR;
		$config['upload_path'] = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $config['upload_path']);
		
		if (!is_dir($config['upload_path'])) {
			@mkdir($config['upload_path'], 0777, true);
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
				@unlink($file_path);
			}

			if (count($rows) <= 4) {
				echo json_encode(array('status' => 'error', 'message' => 'Excel file is empty or contains no records. Records must start from Row 5.'));
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

			// Pre-load event exhibitors for fast validation
			$exhibitors = $this->db->select('C.id, C.company, C.email')
				->from('es_exhibition_booking as B')
				->join('es_customers as C', 'B.customer_id = C.id')
				->where('B.exhibition_id', $exhibition_id)
				->where('B.is_canceled', 0)
				->where('C.is_deleted', 0)
				->get()
				->result();

			$exhibitor_map = array();
			foreach ($exhibitors as $e) {
				$exhibitor_map[strtolower(trim($e->email))] = $e;
			}

			$parsed_rows = array();
			$valid_count = 0;
			$invalid_count = 0;

			for ($i = 4; $i < count($rows); $i++) {
				$row = $rows[$i];
				if (empty(array_filter($row))) {
					continue; // skip blank row
				}

				$row_num    = $i + 1;
				$day        = isset($row[0]) ? trim((string)$row[0]) : '';
				$date_val   = isset($row[1]) ? trim((string)$row[1]) : '';
				$time_val   = isset($row[2]) ? trim((string)$row[2]) : '';
				$from_mail  = isset($row[3]) ? strtolower(trim((string)$row[3])) : '';
				$to_mail    = isset($row[4]) ? strtolower(trim((string)$row[4])) : '';
				$location   = isset($row[5]) ? trim((string)$row[5]) : '';
				$agenda     = isset($row[6]) ? trim((string)$row[6]) : '';
				$discussion = isset($row[7]) ? trim((string)$row[7]) : '';

				$errors = array();

				if (empty($day)) {
					$errors[] = 'Exhibition Day is required.';
				}

				// Normalize date
				$formatted_date = '';
				if (!empty($date_val)) {
					$d_time = strtotime($date_val);
					if ($d_time) {
						$formatted_date = date('Y-m-d', $d_time);
					} else {
						$errors[] = 'Invalid date format (use YYYY-MM-DD).';
					}
				} else {
					$errors[] = 'Meeting Date is required.';
				}

				// Normalize time
				$formatted_time = '';
				if (!empty($time_val)) {
					$t_time = strtotime($time_val);
					if ($t_time) {
						$formatted_time = date('H:i', $t_time);
					} else {
						$errors[] = 'Invalid time format (use HH:MM).';
					}
				} else {
					$errors[] = 'Meeting Time is required.';
				}

				$from_obj = null;
				if (empty($from_mail)) {
					$errors[] = 'Meeting From Email is required.';
				} elseif (!isset($exhibitor_map[$from_mail])) {
					$errors[] = 'Meeting From email "' . htmlspecialchars($from_mail) . '" is not registered for this event.';
				} else {
					$from_obj = $exhibitor_map[$from_mail];
				}

				$to_obj = null;
				if (empty($to_mail)) {
					$errors[] = 'Meeting To Email is required.';
				} elseif (!isset($exhibitor_map[$to_mail])) {
					$errors[] = 'Meeting To email "' . htmlspecialchars($to_mail) . '" is not registered for this event.';
				} else {
					$to_obj = $exhibitor_map[$to_mail];
				}

				if ($from_mail === $to_mail && !empty($from_mail)) {
					$errors[] = 'Meeting From and Meeting To cannot be the same exhibitor.';
				}

				if (empty($agenda)) {
					$errors[] = 'Agenda of meeting is required.';
				}

				$is_valid = empty($errors);
				if ($is_valid) {
					$valid_count++;
				} else {
					$invalid_count++;
				}

				$parsed_rows[] = array(
					'excel_row_num'          => $row_num,
					'row_num'                => $row_num,
					'exhibition_day'         => $day,
					'day'                    => $day,
					'appointment_date'       => $formatted_date,
					'date'                   => $formatted_date,
					'appointment_time'       => $formatted_time,
					'time'                   => $formatted_time,
					'appointment_from_email' => $from_mail,
					'from_mail'              => $from_mail,
					'appointment_from_name'  => $from_obj ? $from_obj->company : $from_mail,
					'from_name'              => $from_obj ? $from_obj->company : $from_mail,
					'appointment_from'       => $from_obj ? (int)$from_obj->id : 0,
					'from_id'                => $from_obj ? (int)$from_obj->id : 0,
					'appointment_to_email'   => $to_mail,
					'to_mail'                => $to_mail,
					'appointment_to_name'    => $to_obj ? $to_obj->company : $to_mail,
					'to_name'                => $to_obj ? $to_obj->company : $to_mail,
					'appointment_to'         => $to_obj ? (int)$to_obj->id : 0,
					'to_id'                  => $to_obj ? (int)$to_obj->id : 0,
					'meeting_location'       => $location,
					'location'               => $location,
					'agenda_of_meeting'      => $agenda,
					'agenda'                 => $agenda,
					'discussion_points'      => $discussion,
					'discussion'             => $discussion,
					'status'                 => $is_valid ? 'valid' : 'invalid',
					'is_valid'               => $is_valid,
					'errors'                 => $errors,
				);
			}

			echo json_encode(array(
				'status'        => 'success',
				'total_rows'    => count($parsed_rows),
				'total_records' => count($parsed_rows),
				'valid_count'   => $valid_count,
				'invalid_count' => $invalid_count,
				'has_errors'    => ($invalid_count > 0),
				'rows'          => $parsed_rows,
			));
		} else {
			if (is_file($file_path)) {
				@unlink($file_path);
			}
			echo json_encode(array('status' => 'error', 'message' => 'Failed to parse Excel file: ' . \Shuchkin\SimpleXLSX::parseError()));
		}
	}

	/**
	 * Confirm and batch insert validated rows into es_exhibition_appointments
	 */
	function bulk_import_confirm()
	{
		$raw_id = $this->input->post('exhibition_id') ? $this->input->post('exhibition_id') : $this->input->get('id');
		$exhibition_id = $this->resolve_exhibition_id($raw_id);
		if (empty($exhibition_id)) {
			echo json_encode(array('status' => 'error', 'message' => 'Valid Exhibition ID is missing.'));
			return;
		}

		$rows_json = $this->input->post('rows_data');
		if (empty($rows_json)) {
			$rows_json = $this->input->post('rows');
		}
		if (empty($rows_json)) {
			echo json_encode(array('status' => 'error', 'message' => 'No import data provided.'));
			return;
		}

		$import_rows = json_decode($rows_json, true);
		if (!is_array($import_rows) || empty($import_rows)) {
			echo json_encode(array('status' => 'error', 'message' => 'Invalid or empty import rows.'));
			return;
		}

		$auto_approve = ($this->input->post('auto_approve') == 1 || $this->input->post('auto_approve') === 'true');
		$now = date('Y-m-d H:i:s');

		$this->db->trans_start();

		$insert_count = 0;
		foreach ($import_rows as $row) {
			$is_valid = !empty($row['is_valid']) || (isset($row['status']) && $row['status'] === 'valid');
			if (!$is_valid) {
				continue;
			}

			$from_id = !empty($row['appointment_from']) ? (int)$row['appointment_from'] : (!empty($row['from_id']) ? (int)$row['from_id'] : 0);
			$to_id   = !empty($row['appointment_to']) ? (int)$row['appointment_to'] : (!empty($row['to_id']) ? (int)$row['to_id'] : 0);
			$day     = !empty($row['exhibition_day']) ? $row['exhibition_day'] : (!empty($row['day']) ? $row['day'] : '');
			$date    = !empty($row['appointment_date']) ? $row['appointment_date'] : (!empty($row['date']) ? $row['date'] : '');
			$time    = !empty($row['appointment_time']) ? $row['appointment_time'] : (!empty($row['time']) ? $row['time'] : '');
			$loc     = !empty($row['meeting_location']) ? $row['meeting_location'] : (!empty($row['location']) ? $row['location'] : 'Exhibition Hall');
			$agenda  = !empty($row['agenda_of_meeting']) ? $row['agenda_of_meeting'] : (!empty($row['agenda']) ? $row['agenda'] : '');
			$disc    = !empty($row['discussion_points']) ? $row['discussion_points'] : (!empty($row['discussion']) ? $row['discussion'] : '');

			$data = array(
				'exhibition_id'     => (int)$exhibition_id,
				'appointment_from'  => $from_id,
				'user_type_from'    => 'exhibitor',
				'appointment_to'    => $to_id,
				'user_type_to'      => 'exhibitor',
				'exhibition_day'    => $day,
				'appointment_date'  => $date,
				'appointment_time'  => (strlen($time) === 5 ? $time . ':00' : $time),
				'meeting_location'  => $loc,
				'agenda_of_meeting' => $agenda,
				'discussion_points' => $disc,
				'is_approved'       => $auto_approve ? 1 : 0,
				'approved_on'       => $auto_approve ? $now : null,
				'is_conducted'      => 0,
				'is_canceled'       => 0,
				'is_deleted'        => 0,
				'created_on'        => $now,
			);

			$this->db->insert('es_exhibition_appointments', $data);
			$insert_count++;
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(array('status' => 'error', 'message' => 'Database transaction failed during bulk import.'));
			return;
		}

		$this->session->set_flashdata('message', "{$insert_count} Meeting schedules successfully imported.");
		echo json_encode(array(
			'status'  => 'success',
			'message' => "{$insert_count} Meeting schedules successfully imported!",
			'count'   => $insert_count,
		));
	}
}