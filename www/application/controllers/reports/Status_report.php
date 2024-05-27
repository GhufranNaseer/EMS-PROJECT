<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Status_report extends MY_Controller
{
	protected function rule()
	{
		$this->activateRightsSystem();
		$crd = array(
			'crd_list,crd_exhibition_list,
			' => array(
				'rule' => '@'
			),
			'crd_list_datatable,crd_exhibition_list_datatable,ajax_get_form_data,
			' => array(
				'rule' => '@',
				'ajaxOnly' => true
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
		$this->load->view('status_report/list');
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
					->where('is_canceled', 0)
					->where('is_approved', 1)
					->count_all_results('es_exhibition_booking');
			}, NULL)
			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'exhibitions_form_status_2-list.html?id=' . urlencode(myid($id)) . '">Report</a>';

				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where('E.is_deleted', 0)
			->join('es_locations as L', 'E.location_id = L.id', 'LEFT')
			->from('es_exhibitions as E');

		print ($this->datatables->generate());
	}

	function crd_exhibition_list()
	{
		$this->load->view('includes/after_login/head');
		$this->load->view('status_report/exhibition_list');
	}

	function crd_exhibition_list_datatable()
	{
		$this->checkEditId();
		$this->load->library('datatables');
		$this->datatables
			->select('B.id,
            		B.exhibition_id,
                     C.Company,
				  ', false)

			->add_column('fascia', function ($row) {
				$has_data = '<a class="text-center" style="color: red;">Pending</a>';

				$check_data = $this->db
					->where('exhibition_id', $row['exhibition_id'])
					->where('booking_id', $row['id'])
					->where('form_id', 1)
					->get('es_exhibition_booking_forms_data')
					->row();

				if ($check_data && array_key_exists('shell_stall_data', json_decode($check_data->form_data, true))) {
					$has_data = '<a class="text-center view_details" data-bookingid="'.$row['id'].'" data-exhibitionid="'.$row['exhibition_id'].'" data-formid="1&shell">Completed</a>';
				}

				return $has_data;
			}, NULL)


			->add_column('stall_builder', function ($row) {
				$has_data = '<a class="text-center" style="color: red;">Pending</a>';

				$check_data = $this->db
					->where('exhibition_id', $row['exhibition_id'])
					->where('booking_id', $row['id'])
					->where('form_id', 1)
					->get('es_exhibition_booking_forms_data')
					->row();

				if ($check_data && array_key_exists('bare_stall_data', json_decode($check_data->form_data, true))) {
					$has_data = '<a class="text-center view_details" data-bookingid="'.$row['id'].'" data-exhibitionid="'.$row['exhibition_id'].'" data-formid="1&bare">Completed</a>';
				}

				return $has_data;
			}, NULL)


			->add_column('catalog_entry', function ($row) {
				$has_data = '<a class="text-center" style="color: red;">Pending</a>';

				$check = $this->db
					->where('exhibition_id', $row['exhibition_id'])
					->where('booking_id', $row['id'])
					->where('form_id', 3)
					->count_all_results('es_exhibition_booking_forms_data');

				if ($check > 0) {
					$has_data = '<a class="text-center view_details" data-bookingid="'.$row['id'].'" data-exhibitionid="'.$row['exhibition_id'].'" data-formid="3">Completed</a>';
				}

				return $has_data;
			}, NULL)

			->add_column('budges_invitations', function ($row) {
				$has_data = '<a class="text-center" style="color: red;">Pending</a>';

				$check = $this->db
					->where('exhibition_id', $row['exhibition_id'])
					->where('booking_id', $row['id'])
					->where('form_id', 19)
					->count_all_results('es_exhibition_booking_forms_data');

				if ($check > 0) {
					$has_data = '<a class="text-center view_details" data-bookingid="'.$row['id'].'" data-exhibitionid="'.$row['exhibition_id'].'" data-formid="19">Completed</a>';
				}

				return $has_data;
			}, NULL)

			->add_column('end_user_certificate', function ($row) {
				$has_data = '<a class="text-center" style="color: red;">Pending</a>';

				$check = $this->db
					->where('exhibition_id', $row['exhibition_id'])
					->where('booking_id', $row['id'])
					->where('form_id', 22)
					->count_all_results('es_exhibition_booking_forms_data');

				if ($check > 0) {
					$has_data = '<a class="text-center view_details" data-bookingid="'.$row['id'].'" data-exhibitionid="'.$row['exhibition_id'].'" data-formid="22">Completed</a>';
				}

				return $has_data;
			}, NULL)


			->add_column('visit_to_pakistan', function ($row) {
				$has_data = '<a class="text-center" style="color: red;">Pending</a>';

				$check = $this->db
					->where('exhibition_id', $row['exhibition_id'])
					->where('booking_id', $row['id'])
					->where('form_id', 21)
					->count_all_results('es_exhibition_booking_forms_data');

				if ($check > 0) {
					$has_data = '<a class="text-center view_details" data-bookingid="'.$row['id'].'" data-exhibitionid="'.$row['exhibition_id'].'" data-formid="21">Completed</a>';
				}

				return $has_data;
			}, NULL)

			->add_column('hotel_booking', function ($row) {
				$has_data = '<a class="text-center" style="color: red;">Pending</a>';

				$check = $this->db
					->where('exhibition_id', $row['exhibition_id'])
					->where('booking_id', $row['id'])
					->where('form_id', 24)
					->count_all_results('es_exhibition_booking_forms_data');

				if ($check > 0) {
					$has_data = '<a class="text-center view_details" data-bookingid="'.$row['id'].'" data-exhibitionid="'.$row['exhibition_id'].'" data-formid="24">Completed</a>';
				}

				return $has_data;
			}, NULL)

			->add_column('vehical_rent', function ($row) {
				$has_data = '<a class="text-center" style="color: red;">Pending</a>';

				$check = $this->db
					->where('exhibition_id', $row['exhibition_id'])
					->where('booking_id', $row['id'])
					->where('form_id', 17)
					->count_all_results('es_exhibition_booking_forms_data');

				if ($check > 0) {
					$has_data = '<a class="text-center view_details" data-bookingid="'.$row['id'].'" data-exhibitionid="'.$row['exhibition_id'].'" data-formid="17">Completed</a>';
				}

				return $has_data;
			}, NULL)


			->add_column('disply_mobility', function ($row) {
				$has_data = '<a class="text-center" style="color: red;">Pending</a>';

				$check = $this->db
					->where('exhibition_id', $row['exhibition_id'])
					->where('booking_id', $row['id'])
					->where('form_id', 23)
					->count_all_results('es_exhibition_booking_forms_data');

				if ($check > 0) {
					$has_data = '<a class="text-center view_details" data-bookingid="'.$row['id'].'" data-exhibitionid="'.$row['exhibition_id'].'" data-formid="23">complete</a>';
				}

				return $has_data;
			}, NULL)



			->unset_column('B.exhibition_id')
			->where('B.exhibition_id', $this->formdata->id)
			->where('B.is_canceled', 0)
			->where('B.is_approved', 1)
			->join('es_customers as C', 'B.customer_id = C.id')
			->from('es_exhibition_booking as B');


		print ($this->datatables->generate());
	}

	function ajax_get_form_data() {
		$exhibition_id = $this->input->post('exhibition_id');
		$booking_id = $this->input->post('booking_id');
		$form_id = $this->input->post('form_id');

		$form_id = explode('&', $form_id)[0];

		$customer = $this->db
			->select('C.*')
			->where('B.id', $booking_id)
			->join('es_customers as C', 'C.id = B.customer_id', 'LEFT')
			->get('es_exhibition_booking as B')
			->row();

		$form_data = $this->db
			->where('exhibition_id', $exhibition_id)
			->where('booking_id', $booking_id )
			->where('form_id', $form_id )
			->get('es_exhibition_booking_forms_data')
			->row();

		$data = json_decode($form_data->form_data);

		$html = '';

		$header_stall_html = '';

		// halls
		$halls = $this->db
			->where('S.booking_id', $booking_id)
			->join('es_location_halls as C', 'S.hall_id = C.id')
			->get('es_exhibition_booking_stalls as S')
			->result();
		$hall_name="";
		$check_halls = array();
		foreach ($halls as $hall) {
			if(in_array($hall->hall_title, $check_halls)) {
				continue;
			}
			$hall_name .=$hall->hall_title.' ,';
			$check_halls[] = $hall->hall_title;
		}
		$header_stall_html .= 'Hall #: ' . rtrim($hall_name,',') . '<br>';
		//stall
		$stalls = $this->db
			->where('S.booking_id', $booking_id)
			->join('es_exhibition_stalls as C', 'S.stall_id = C.id')
			->get('es_exhibition_booking_stalls as S')
			->result();
		$stall_name="";
		foreach ($stalls as $stall) {
			$stall_name .=$stall->stall_name.' ,';
		}
		$header_stall_html .= 'Stall #: ' . rtrim($stall_name,',') . '<br>';
		//size
		$stall_size = $this->db
			->select('SUM(C.stall_size) as total_size')
			->where('S.booking_id', $booking_id)
			->join('es_exhibition_stalls as C', 'S.stall_id = C.id')
			->get('es_exhibition_booking_stalls as S')
			->row();
		$header_stall_html .= 'Stall Size: ' . $stall_size->total_size . ' sqm';

		if ($form_id == 1) {
			if (explode('&', $this->input->post('form_id'))[1] == 'shell') {
				$html = '<div class="modal-header">
							<div class="row">
								<div class="col-sm-3">Fascia Form</div>
								<div class="col-sm-3 text-center">'.$customer->company.'</div>
								<div class="col-sm-3">'.$header_stall_html.'</div>
								<div class="col-sm-3 text-right"><button type="button" class="close" data-dismiss="modal">&times;</button></div>
							</div>
						</div>';
				$html .= '<table class="table table-striped">';
				$html .= '<tr>';
				$html .= '<th>Stall Fascia Name:</th>';
				$html .= '<td>'.$data->shell_stall_data->stall_fascia_name.'</td>';
				$html .= '</tr>';
				$html .= '</table>';
			} else {
				$html = '<div class="modal-header">
							<div class="row">
								<div class="col-sm-3">Stall Builder Form</div>
								<div class="col-sm-3 text-center">'.$customer->company.'</div>
								<div class="col-sm-3">'.$header_stall_html.'</div>
								<div class="col-sm-3 text-right"><button type="button" class="close" data-dismiss="modal">&times;</button></div>
							</div>
						</div>';
				$html .= '<table class="table table-striped">';
				$html .= '<tr><th>S.No</th><th>Stall Builder Contractors</th></tr>';

				foreach ($data->bare_stall_data->stall_building_contractor as $count => $contractor) {
					$html .= '<tr><td>'.($count + 1).'</td><td>'. str_replace('_', ' ', $contractor) .'</td></tr>';
				}
				$html .= '</table>';
			}
		}
		else if ($form_id == 3) {
			//echo '<pre>'; print_r($data); die();
			$company_image = '';
			if (isset($data->exhibit->company_logo)) {
				$company_image = '<img src="'.base_url('client/'.$data->exhibit->company_logo[0]).'" alt="" width="150px">';
			}
			$html = '<div class="modal-header">
							<div class="row">
								<div class="col-sm-3">Catalog Entry Form</div>
								<div class="col-sm-3 text-center">'.$customer->company.'</div>
								<div class="col-sm-3">'.$header_stall_html.'</div>
								<div class="col-sm-3 text-right"><button type="button" class="close" data-dismiss="modal">&times;</button></div>
							</div>
						</div>';
			$html .= '<div class="modal-body">';
			$html .= '<h4>Exhibit Information</h4>';
			$html .= '<table class="table table-striped">';
			$html .= '<tr><th>Country</th><td>'.$data->exhibit->country.'</td><th>Company Logo</th></tr>';
			$html .= '<tr><th>Postal / ZIP Code</th><td>'.$data->exhibit->postal.'</td><td rowspan="5">'.$company_image.'</td></tr>';
			$html .= '<tr><th>Telephone Number</th><td>'.$data->exhibit->telephone.'</td></tr>';
			$html .= '<tr><th>Fax Number</th><td>'.$data->exhibit->fax.'</td></tr>';
			$html .= '<tr><th>Email Address</th><td>'.$data->exhibit->email.'</td></tr>';
			$html .= '<tr><th>Business Address</th><td>'.$data->exhibit->address.'</td></tr>';
			$html .= '</table>';

			$html .= '<h4>Contact Person Information</h4>';
			$html .= '<table class="table table-striped">';
			$html .= '<tr><th>Person Full Name</th><td>'.$data->exhibit->contact_person->name.'</td><th>Designation</th><td>'.$data->exhibit->contact_person->designation.'</td></tr>';
			$html .= '<tr><th>Mobile #</th><td>'.$data->exhibit->contact_person->mobile.'</td><th>Email Address</th><td>'.$data->exhibit->contact_person->email.'</td></tr>';
			$html .= '<tr><th>Telephone Number</th><td>'.$data->exhibit->contact_person->phone.'</td><th>Fax Number</th><td>'.$data->exhibit->contact_person->fax.'</td></tr>';
			$html .= '</table>';

			$html .= '<h4>Company Profile</h4>';
			$html .= '<div class="well well-sm">'.$data->profile.'</div>';

			$html .= '<h4>Product & Category</h4>';
			$html .= '<table class="table table-striped">';
			$html .= '<tr><th class="text-center" colspan="4">Business Sector</th></tr>';
			$html .= '<tr><th>Type</th><th>Business sector</th><th>Business Type</th><th>Business Area</th></tr>';
			if (isset($data->products->main)) {
				foreach ($data->products->main as $d) {
					$html .= '<tr><td>Main</td><td>'.$d->sector.'</td><td>'.$d->type.'</td><td>'.$d->area.'</td></tr>';
				}
			}
			if (isset($data->products->other)) {
				foreach ($data->products->other as $d) {
					$html .= '<tr><td>Other</td><td>'.$d->sector.'</td><td>'.$d->type.'</td><td>'.$d->area.'</td></tr>';
				}
			}
			$html .= '<tr><th class="text-center" colspan="4">Product List</th></tr>';
			$html .= '<tr><th>Category</th><th>Product Type</th><th colspan="2">Product Name</th></tr>';
			if (isset($data->products->product)) {
				foreach ($data->products->product as $d) {
					$html .= '<tr><td>'.$d->category.'</td><td>'.$d->type.'</td><td colspan="2">'.$d->name.'</td></tr>';
				}
			}
			$html .= '</table>';

			$html .= '<h4>Principal Company</h4>';
			$html .= '<div class="table-responsive"><table class="table table-striped">';
			$html .= '<tr>
						<th>Company Name</th>
						<th>Country</th>
						<th>Tel #</th>
						<th>Fax #</th>
						<th>Org Email</th>
						<th>Address</th>
						<th>Person Full Name</th>
						<th>Designation</th>
						<th>Mobile #</th>
						<th>P. Email</th>
					</tr>';
			if (isset($data->principle) && $data->principle->is_active == 1) {
				foreach ($data->principle->principles_list as $list) {
					$html .= '<tr>
							<td>'.$list->full_name.'</td>
							<td>'.$list->country.'</td>
							<td>'.$list->phone.'</td>
							<td>'.$list->fax.'</td>
							<td>'.$list->email.'</td>
							<td>'.$list->address.'</td>
							<td>'.$list->person_name.'</td>
							<td>'.$list->designation.'</td>
							<td>'.$list->person_mobile.'</td>
							<td>'.$list->person_email.'</td>
							</tr>';
				}
			}
			$html .= '</table></div></div>';
		}
		else if ($form_id == 19) {
			//echo '<pre>'; print_r($data); die();
			$exhibitors_badges = $this->db
				->where('exhibition_id', $exhibition_id)
				->where('booking_id', $booking_id)
				//->where('badge_type', 'exhibitor')
				->where('is_active', 1)
				->get('es_exhibition_badges')
				->result();
			$html = '<div class="modal-header">
							<div class="row">
								<div class="col-sm-3">Budges Invitations Form</div>
								<div class="col-sm-3 text-center">'.$customer->company.'</div>
								<div class="col-sm-3">'.$header_stall_html.'</div>
								<div class="col-sm-3 text-right"><button type="button" class="close" data-dismiss="modal">&times;</button></div>
							</div>
						</div>';
			$html .= '<div class="modal-body">';
			$html .= '<div class="table-responsive"><table class="table table-striped">';
			$html .= '<tr>
					<th>Person Full Name</th>
					<th>Designation</th>
					<th>Mobile #</th>
					<th>Nationality</th>
					<th>CNIC / Passport</th>
					<th>Email Address</th>
					<th>Created Date</th>
					<th>Badge Type</th>
					<th>Picture</th>
				</tr>';
			foreach ($exhibitors_badges as $badge) {
				$html .= '<tr>
						<td>'. $badge->full_name .'</td>
						<td>'. $badge->designation .'</td>
						<td>'. $badge->mobile .'</td>
						<td>'. $badge->nationality .'</td>
						<td>'. (($badge->nationality == 'Pakistani') ? $badge->cnic : $badge->passport) .'</td>
						<td>'. $badge->email .'</td>
						<td>'. date('d/m/Y', strtotime($badge->created_on)) .'</td>
						<td>'. ucfirst($badge->badge_type) .'</td>
						<td>
						<a href="'. base_url('client/'.$badge->user_image) .'" target="_blank">
						<img src="'. base_url('client/'.$badge->user_image) .'" alt="" width="50px">
						</a>
						</td>
						</tr>';
			}
			$html .= '</table></div></div>';
		}
		else if ($form_id == 22) {
			//echo '<pre>'; print_r($data); die();
			$html = '<div class="modal-header">
							<div class="row">
								<div class="col-sm-3">End User Certificate Form</div>
								<div class="col-sm-3 text-center">'.$customer->company.'</div>
								<div class="col-sm-3">'.$header_stall_html.'</div>
								<div class="col-sm-3 text-right"><button type="button" class="close" data-dismiss="modal">&times;</button></div>
							</div>
						</div>';
			$html .= '<div class="modal-body">';
			$html .= '<div class="table-responsive"><table class="table table-striped">';
			$html .= '<tr>
					<th>Category</th>
					<th>Description Of Exhibit</th>
					<th>Quantity</th>
					<th>Length</th>
					<th>Breth</th>
					<th>Height</th>
					<th>Package Qty</th>
					<th>Weight</th>
					<th>Remarks</th>
				</tr>';
			if (isset($data->products)) {
				foreach ($data->products as $row) {
					$html .= '<tr>
						<td>'. $row->product_category .'</td>
						<td>'. $row->product_description .'</td>
						<td>'. $row->product_quantity .'</td>
						<td>'. $row->package_length .' '. $row->package_size_units .'</td>
						<td>'. $row->package_breth .' '. $row->package_size_units .'</td>
						<td>'. $row->package_height .' '. $row->package_size_units .'</td>
						<td>'. $row->package_quantity .'</td>
						<td>'. $row->package_weight .' '. $row->package_weight_units .'</td>
						<td>'. $row->package_remarks .'</td>
						</tr>';
				}
			}
			$html .= '</table></div>';

			if (isset($data->certificate)) {
				$html .= '<div class="text-right"><a href="'.base_url($data->certificate).'" target="_blank">Download Certificate</a></div>';
			} else {
				$html .= '<div class="text-right">Certificate not uploaded!</div>';
			}

			$html .= '</div>';
		}
		else if ($form_id == 21) {
			//echo '<pre>'; print_r($data); die();
			$html = '<div class="modal-header">
							<div class="row">
								<div class="col-sm-3">Visit To Pakistan Form</div>
								<div class="col-sm-3 text-center">'.$customer->company.'</div>
								<div class="col-sm-3">'.$header_stall_html.'</div>
								<div class="col-sm-3 text-right"><button type="button" class="close" data-dismiss="modal">&times;</button></div>
							</div>
						</div>';
			$html .= '<div class="table-responsive"><table class="table table-striped">';
			$html .= '<tr>
				<th>S.No</th>
				<th>Name</th>
				<th>Country</th>
				<th>City</th>
				<th>Nationality</th>
				<th>Passport<br>Number</th>
				<th>Issue Date</th>
				<th>Expiry Date</th>
				<th>Pakistani<br>Mission<br>Country</th>
				<th>Pakistani<br>Mission<br>City</th>
				<th>Passport<br>Picture</th>
			</tr>';
			if (isset($data->visas)) {
				foreach ($data->visas as $count => $row) {
					$html .= '<tr>
							<td>'. ($count + 1) .'</td>
							<td>'. $row->visa_name .'</td>
							<td>'. $row->visa_country .'</td>
							<td>'. $row->visa_city .'</td>
							<td>'. $row->visa_nationality .'</td>
							<td>'. $row->visa_passport .'</td>
							<td>'. $row->visa_passport_issue_date .'</td>
							<td>'. $row->visa_passport_expiry_date .'</td>
							<td>'. $row->concerned_country .'</td>
							<td>'. $row->concerned_city .'</td>
							<td>
							<a href="'. base_url('client/'.$row->contact_person_bio_image[0] ) .'" target="_blank"><img src="'. base_url('client/'.$row->contact_person_bio_image[0] ) .'" alt="" width="50px"></a>
							</td>
							</tr>';
				}
			}
			$html .= '</table></div>';
		}
		else if ($form_id == 24) {
			$html = '<div class="modal-header">
							<div class="row">
								<div class="col-sm-3">Hotel Booking Form</div>
								<div class="col-sm-3 text-center">'.$customer->company.'</div>
								<div class="col-sm-3">'.$header_stall_html.'</div>
								<div class="col-sm-3 text-right"><button type="button" class="close" data-dismiss="modal">&times;</button></div>
							</div>
						</div>';
			$html .= '<div class="table-responsive"><table class="table table-striped">';
			$html .= '<tr>
				<th>#</th>
				<th>Exhibit Company Name</th>
				<th>Exhibitor Name</th>
				<th>Flight Number</th>
				<th>Flight Date</th>
				<th>Flight Time</th>
				<th>Flight Number</th>
				<th>Flight Date</th>
				<th>Flight Time</th>
				<th>Hotel</th>
				<th>Room Type</th>
			</tr>';
			if (isset($data->reservation)) {
				foreach ($data->reservation as $count => $d) {
					$html .= '<tr>
						<td>'.($count+1).'</td>
						<td>'.$customer->company.'</td>
						<td>'.$d->person_name.'</td>
						<td>'.$d->flight.'</td>
						<td>'.$d->flight_check_in_date.'</td>
						<td>'.$d->flight_check_in_time.'</td>
						<td>'.$d->flight.'</td>
						<td>'.$d->flight_check_out_date.'</td>
						<td>'.$d->flight_check_out_time.'</td>
						<td>'.$d->hotel.'</td>
						<td>'.$d->hotel_room.'</td>
						</tr>';
				}
			}
			$html .= '</table></div>';
		}
		else if ($form_id == 17) {
			$html = '<div class="modal-header">
							<div class="row">
								<div class="col-sm-3">Vehicle Rent Form</div>
								<div class="col-sm-3 text-center">'.$customer->company.'</div>
								<div class="col-sm-3">'.$header_stall_html.'</div>
								<div class="col-sm-3 text-right"><button type="button" class="close" data-dismiss="modal">&times;</button></div>
							</div>
						</div>';
			$html .= '<div class="table-responsive"><table class="table table-striped">';
			$html .= '<tr>
				<th>#</th>
				<th>Exhibit Company Name</th>
				<th>Exhibitor Name</th>
				<th>Nationality</th>
				<th>Passport Number</th>
				<th>Vehicle Type</th>
				<th>Quantity</th>
				<th>From</th>
				<th>To</th>
				<th>Pick-Up</th>
				<th>Drop-Off</th>
			</tr>';
			if (isset($data->booking)) {
				foreach ($data->booking as $count => $d) {
					$html .= '<tr>
						<td>'.($count+1).'</td>
						<td>'.$customer->company.'</td>
						<td>'.$d->exhibitor->name.'</td>
						<td>'.$d->exhibitor->nationality.'</td>
						<td>'.$d->exhibitor->passport.'</td>
						<td>'.$d->vehicle->name.'</td>
						<td>'.$d->vehicle->qty.'</td>
						<td>'.$d->vehicle->from_date.'</td>
						<td>'.$d->vehicle->to_date.'</td>
						<td>'.$d->pickup->location.'</td>
						<td>'.$d->dropoff->location.'</td>
						</tr>';
				}
			}
			$html .= '</table></div>';
		}
		else if ($form_id == 23) {
			$html = '<div class="modal-header">
							<div class="row">
								<div class="col-sm-3">Display Mobility Form</div>
								<div class="col-sm-3 text-center">'.$customer->company.'</div>
								<div class="col-sm-3">'.$header_stall_html.'</div>
								<div class="col-sm-3 text-right"><button type="button" class="close" data-dismiss="modal">&times;</button></div>
							</div>
						</div>';
			$html .= '<div class="table-responsive"><table class="table table-striped">';
			$html .= '<tr>
				<th>#</th>
				<th>Exhibit Company Name</th>
				<th>Item Description ( Incl. Caliber, Wheeled Etc.)</th>
				<th>Quantity</th>
				<th>Date</th>
				<th>Time</th>
			</tr>';
			if (isset($data->vehicle)) {
				foreach ($data->vehicle as $count => $d) {
					$event_date = $this->db
						->where('exhibition_id', $exhibition_id)
						->get('es_exhibition_date')
						->result();

					$html .= '<tr>
						<td>'.($count+1).'</td>
						<td>'.$customer->company.'</td>
						<td>'.$d->description.'</td>
						<td>'.$d->quantity.'</td>
						<td>'.$event_date[$d->event_day - 1]->date.'</td>
						<td>'.$event_date[$d->event_day - 1]->open_time.'</td>
						</tr>';
				}
			}
			$html .= '</table></div>';
		}
		else {
			//echo '<pre>'; print_r($data); die();
			$html = '<div class="modal-header">
							<div class="row">
								<div class="col-sm-3">Not Found</div>
								<div class="col-sm-3 text-center"></div>
								<div class="col-sm-3 text-center"></div>
								<div class="col-sm-3 text-right"></div>
							</div>
						</div>';
			$html .= '<div class="modal-body">';
			$html .= '<h4 class="text-center">Form Data Not Available</h4>';
			$html .= '</div>';
		}

		echo $html;
	}

}