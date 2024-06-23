<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_profile extends MY_Controller {

	public function view_profile() {
		$booking = $this->db
			->select('B.*')
			->where(mycolumn('B.id'), $_GET['id'])
			->get('es_exhibition_booking as B')
			->row();

		$event = $this->db
			->select('E.*')
			->where(mycolumn('B.id'), $_GET['id'])
			->join('es_exhibitions as E', 'E.id = B.exhibition_id')
			->get('es_exhibition_booking as B')
			->row();
		
		$organizer = $this->db
			->where('id', $event->event_organizer)
			->get('es_organizer')
			->row();

		$rows = $this->db
			->select('F.*, C.company')
			->where(mycolumn('F.booking_id'), $_GET['id'])
			->where('F.form_id', 3)
			->join('es_exhibition_booking as B', 'F.booking_id = B.id', 'LEFT')
			->join('es_customers as C', 'B.customer_id = C.id', 'LEFT')
			->get('es_exhibition_booking_forms_data as F')
			->row();
	
		$data = (isset($rows->form_data)) ? json_decode($rows->form_data,false) : null;

		$booking_stalls = $this->db
			->where('booking_id', $booking->id)
			->get('es_exhibition_stalls')
			->result();
			
		$hall_data = $this->db
			->where('id', $booking_stalls[0]->hall_id)
			->get('es_location_halls')
			->row();

		// echo '<pre>'; print_r($data); die;
		
		$this->load->view('includes/after_login/head');
		$this->load->view('product-profile/view', array(
			'booking' => $booking,
			'event' => $event,
			'organizer' => $organizer,
			'rows' => $rows,
			'data' => $data,
			'booking_stalls' => $booking_stalls,
			'hall_data' => $hall_data,
		));
	}

	public function profile_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('product-profile/list');
	}

	public function profile_list_datatable() {

		$offset = $this->input->post('iDisplayStart') || 0;
		$limit = $this->input->post('iDisplayLength') || 10;

		$rows = $this->db
			->select('F.*, C.company, C.city, C.country')
			->where('F.exhibition_id', $this->event->id)
			->where('F.form_id', 3)
			->where('F.booking_id !=', $this->booking->id)
			->join('es_exhibition_booking as B', 'F.booking_id = B.id', 'LEFT')
			->join('es_customers as C', 'B.customer_id = C.id', 'LEFT')
			->get('es_exhibition_booking_forms_data as F')
			->result();

		$aaData = array();

		$filter_sector = $this->input->get('filter_sector');
		$filter_type = $this->input->get('filter_type');
		$filter_area = $this->input->get('filter_area');
		$filter_product = $this->input->get('filter_product');
		// echo '<pre>'; print_r($this->input->get()); die;
		// echo '<pre>'; print_r($rows); die;

		foreach ($rows as $row) {
			$include_in_search = true;


			if ($filter_sector || $filter_type || $filter_area || $filter_product) {
				$include_in_search = false;
			}


			$id = $row->booking_id;
			$data = (isset($row->form_data)) ? json_decode($row->form_data, false) : null;

			$col_id = $id;
			$col_name = '';
			$col_city = $row->city;
			$col_country = $row->country;
			$col_b_sector = '';
			$col_b_type = '';
			$col_b_area = '';
			$col_product = '';
			$col_action = '';

			// name
			if (isset($data->exhibit->company_logo)) {
				$logo = $data->exhibit->company_logo[0];
				$logo = str_replace('uploaded:', '', $logo);
				$col_name = '<img src="' . base_url($logo) . '" alt="" class="img-circle" style="width: 40px; height: 40px;" > ';
			}
			$col_name .= $row->company;

			// b sector
			$col_b_sector = '<ul>';
			if (isset($data->products->main) && count((array)$data->products->main) > 0) {
				foreach ($data->products->main as $key => $main_business) {
					$col_b_sector .= '<li>' . $main_business->sector . '</li>';

					if ($filter_sector && str_contains(strtolower($main_business->sector), strtolower($filter_sector))) {
						$include_in_search = true;
					}

					break; //there is only one sector "Defence" so no need to print multiple times
				}
			}
			if (isset($data->products->other) && count((array)$data->products->other) > 0) {
				foreach ($data->products->other as $key => $other_business) {
					$col_b_sector .= '<li>' . $other_business->sector . '</li>';

					if ($filter_sector && str_contains(strtolower($other_business->sector), strtolower($filter_sector))) {
						$include_in_search = true;
					}
				}
			}
			$col_b_sector .= '</ul>';
			
			// b type
			$col_b_type = '<ul>';
			if (isset($data->products->main) && count((array)$data->products->main) > 0) {
				foreach ($data->products->main as $key => $main_business) {
					$col_b_type .= '<li>' . $main_business->type . '</li>';

					if ($filter_type && str_contains(strtolower($main_business->type), strtolower($filter_type))) {
						$include_in_search = true;
					}
				}
			}
			if (isset($data->products->other) && count((array)$data->products->other) > 0) {
				foreach ($data->products->other as $key => $other_business) {
					$col_b_type .= '<li>' . $other_business->type . '</li>';

					if ($filter_type && str_contains(strtolower($other_business->type), strtolower($filter_type))) {
						$include_in_search = true;
					}
				}
			}
			$col_b_type .= '</ul>';
			

			// b area
			$col_b_area = '<ul>';
			if (isset($data->products->main) && count((array)$data->products->main) > 0) {
				foreach ($data->products->main as $key => $main_business) {
					$col_b_area .= '<li>' . $main_business->area . '</li>';

					if ($filter_area && str_contains(strtolower($main_business->area), strtolower($filter_area))) {
						$include_in_search = true;
					}
				}
			}
			if (isset($data->products->other) && count((array)$data->products->other) > 0) {
				foreach ($data->products->other as $key => $other_business) {
					$col_b_area .= '<li>' . $other_business->area . '</li>';

					if ($filter_area && str_contains(strtolower($other_business->area), strtolower($filter_area))) {
						$include_in_search = true;
					}
				}
			}
			$col_b_area .= '</ul>';


			// products
			$col_product = '<ul>';
			if (isset($data->products->product) && count((array)$data->products->product) > 0) {
				foreach ($data->products->product as $key => $product) {
					$col_product .= '<li>' . $product->name . ' ('.$product->category.')</li>';

					if ($filter_product) {
						if (str_contains(strtolower($product->name), strtolower($filter_product)) || str_contains(strtolower($product->category), strtolower($filter_product))) {
							$include_in_search = true;
						}
					}
				}
			}
			$col_product .= '</ul>';

			// action
			$col_action = '<a href="' . base_url() . 'view-product-profile.html?id=' . urlencode(myid($id)) . '">View Profile</a>';
			$col_action .= ' / <a href="' . base_url() . 'meeting_schedule.html?type=exhibitor&id=' . urlencode(myid($id)) . '">Schedule Appointment</a>';
			$col_action .= ' / <button type="button" style="border: none; background: none; color: #337ab7;" class="btn-detail" data-id="' . $id . '">Exhibitor Badges</button>';


			if ($include_in_search) {
				$aaData[] = [
					$col_id,
					$col_name,
					$col_city,
					$col_country,
					$col_b_sector,
					$col_b_type,
					$col_b_area,
					$col_product,
					$col_action,
				];
			}
		}


		return print_r(json_encode([
			"sEcho" => 1,
			"iTotalRecords" => count($rows),
			"iTotalDisplayRecords" => count($rows),
			"aaData" => $aaData,
			"sColumns" => ""
		]));
	}
}