<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Book_stall extends MY_Controller {
	protected function rule() {
		$this->activateRightsSystem();
		$crd = array(
			'crd_list,
			book,
			' => array(
				'rule' => '@'
			),
			'crd_list_datatable,
			book_customer_validate,
			validate_add_stall,
			book_stalls_validate,
			order_item_list_validate,
			book_confirm_validate,
			book_submit,
			ajax_get_hall_stalls,
			ajax_get_packages,
			ajax_get_package_items,
			ajax_get_inventory_category,
			ajax_get_inventory_items,
			generate_order_invoice,
			' => array(
				'rule' => '@',
				'ajaxOnly' => true
			)
		);
		$this->load->model('usermdl');
		$this->myparent = base_url('stalls.html');
		return array_merge($crd);
	}

	private function convertNumberToWord($num = false) {
		$num = str_replace(array(',', ' '), '' , trim($num));
		if(! $num) {
			return false;
		}
		$num = (int) $num;
		$words = array();
		$list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
			'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
		);
		$list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
		$list3 = array('', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
			'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
			'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
		);
		$num_length = strlen($num);
		$levels = (int) (($num_length + 2) / 3);
		$max_length = $levels * 3;
		$num = substr('00' . $num, -$max_length);
		$num_levels = str_split($num, 3);
		for ($i = 0; $i < count($num_levels); $i++) {
			$levels--;
			$hundreds = (int) ($num_levels[$i] / 100);
			$hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
			$tens = (int) ($num_levels[$i] % 100);
			$singles = '';
			if ( $tens < 20 ) {
				$tens = ($tens ? ' ' . $list1[$tens] . ' ' : '' );
			} else {
				$tens = (int)($tens / 10);
				$tens = ' ' . $list2[$tens] . ' ';
				$singles = (int) ($num_levels[$i] % 10);
				$singles = ' ' . $list1[$singles] . ' ';
			}
			$words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' );
		} //end for loop
		$commas = count($words);
		if ($commas > 1) {
			$commas = $commas - 1;
		}
		return strtoupper(implode(' ', $words));
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
			->get('es_exhibitions');
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();

	}

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('book_stall/list');
	}

	function crd_list_datatable() {
		$this->load->library('datatables');
		$this->datatables
			->select('E.id,
				  E.exhibition_title,
				  L.location_title,				  
				  E.booking_expire_date
				  ', false)
			->unset_column('E.booking_expire_date')
			->add_column('total_stalls', function ($row) {
				return $this->db
					->where('exhibition_id', $row['id'])
					->count_all_results('es_exhibition_stalls');
			}, NULL)
			->add_column('available_stalls', function ($row) {
				return $this->db
					->where('exhibition_id', $row['id'])
					->where('is_booked', 0)
					->where('is_sold', 0)
					->count_all_results('es_exhibition_stalls');
			}, NULL)
			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '';
				$available = $this->db
					->where('exhibition_id', $row['id'])
					->where('is_sold', 0)
					->count_all_results('es_exhibition_stalls');
				if ($available > 0) {
					$html = '<a href="' . base_url() . 'book-stall.html?id=' . urlencode(myid($id)) . '">Book Order</a>';
				}

				if (date('Y-m-d') > date('Y-m-d', strtotime($row['booking_expire_date']))) {
					$html = '<span class="label label-danger">Expired</span>';
				}

				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where('E.is_deleted', 0)
			->join('es_locations as L', 'E.location_id = L.id', 'LEFT')
			->from('es_exhibitions as E');

		print ($this->datatables->generate());
	}


	function book() {
		$this->checkEditId();

		$this->load->view('includes/after_login/head');
		$this->load->view('book_stall/sell');
	}

	function book_customer_validate() {
		$this->checkEditId();

		$this->form_validation->set_rules('customer_id', 'customer_id*company name', 'trim|required');
		$this->form_validation->set_rules('customer_contact_person_id', 'customer_contact_person_id*contact person', 'trim|required');
		$this->form_validation->set_rules('booking_price_type', 'booking_price_type*payment mode', 'trim|required');

		if (!is_null($this->input->post('update_contact_person_info'))) {
			$this->form_validation->set_rules('update_contact_person_email', 'update_contact_person_email*email address', 'trim|required|valid_email');
			$this->form_validation->set_rules('update_contact_person_cell', 'update_contact_person_cell*cell phone', 'trim|required|numeric|min_length[8]');
		}
		if (!is_null($this->input->post('has_new_billing_address'))) {
			$this->form_validation->set_rules('new_billing_address', 'new_billing_address*new billing address', 'trim|required');
		}
		if (!is_null($this->input->post('has_sales_agent'))) {
			$this->form_validation->set_rules('sales_agent_id', 'sales_agent_id*sales agent', 'trim|required');
		}

		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		}
		else {
			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function validate_add_stall() {
		$this->checkEditId();

		//echo '<pre>'; print_r($this->input->post()); die();

		$order_id = $this->input->get('order_id');
		$is_edit = (!is_null($order_id));
		$order_data = null;
		if ($is_edit) {
			$order_data = $this->db
				->where(mycolumn(), $order_id)
				->get('es_exhibition_booking')
				->row();
		}

		$this->form_validation->set_rules('stall_type', 'stall_type*Booking Type', 'trim|required');
		$this->form_validation->set_rules('package_id', 'package_id*package', 'trim|required');
		$this->form_validation->set_rules('hall_id', 'hall_id*hall', 'trim|required');
		$this->form_validation->set_rules('stalls[]', 'stalls[]*stalls', 'trim|required');

		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		}
		else {
			// check if stall is still available
			foreach ($this->input->post('stalls') as $stall) {
				$stall_check = $this->db
					->where('id', $stall['id'])
					->get('es_exhibition_stalls')
					->row();
				if ($is_edit) {
					if (($stall_check->is_sold == 1) && $stall_check->booking_id != $order_data->id) {
						return $this->common->doError(func_num_args(), $stall_check->stall_name . ' is already booked by another customer!');
					}
				} else {
					if ($stall_check->is_sold == 1) {
						return $this->common->doError(func_num_args(), $stall_check->stall_name . ' is already booked by another customer!');
					}
				}
			}

			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function book_stalls_validate() {
		$this->checkEditId();

		$order_id = $this->input->get('order_id');
		$is_edit = (!is_null($order_id));
		$order_data = null;
		if ($is_edit) {
			$order_data = $this->db
				->where(mycolumn(), $order_id)
				->get('es_exhibition_booking')
				->row();
		}
		//echo '<pre>';
		//print_r($this->input->post()); die();

		$this->form_validation->set_rules('stalls[]', 'stalls[]*stalls', 'trim|required');
		$this->form_validation->set_rules('order_stall_total_amount', 'order_stall_total_amount*total amount', 'trim|required');


		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		}
		else {
			// check if stall is still available
			foreach ($this->input->post('stalls') as $booking) {
				if ($booking['type'] == '') {
					return $this->common->doError(func_num_args(), 'Something went wrong! Stall type is missing');
				}
				if ($booking['package_id'] == '') {
					return $this->common->doError(func_num_args(), 'Something went wrong! Package is missing');
				}
				if ($booking['hall_id'] == '') {
					return $this->common->doError(func_num_args(), 'Something went wrong! Location is missing');
				}
				if ($booking['stall_ids'] == '') {
					return $this->common->doError(func_num_args(), 'Something went wrong! Stalls is missing');
				}

				$stalls = explode(',', $booking['stall_ids']);

				foreach ($stalls as $stall) {
					$stall_check = $this->db
						->where('id', $stall)
						->get('es_exhibition_stalls')
						->row();

					if ($is_edit) {
						if (($stall_check->is_sold == 1) && $stall_check->booking_id != $order_data->id) {
							return $this->common->doError(func_num_args(), $stall_check->stall_name . ' is already booked by another customer!');
						}
					} else {
						if ($stall_check->is_sold == 1) {
							return $this->common->doError(func_num_args(), $stall_check->stall_name . ' is already booked by another customer!');
						}
					}
				}
			}

			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function order_item_list_validate() {
		$this->checkEditId();

		//echo '<pre>'; print_r($this->input->post()); die();

		$order_id = $this->input->get('order_id');
		$is_edit = (!is_null($order_id));
		$order_data = null;
		if ($is_edit) {
			$order_data = $this->db
				->where(mycolumn(), $order_id)
				->get('es_exhibition_booking')
				->row();
		}

		$this->form_validation->set_rules('order_items[]', 'order_items[]*order item', 'trim|required');

		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		}
		else {
			$all_extra_items = array();
			foreach ($this->input->post('order_items') as $item) {
				if ($item['is_package_item'] == 1) {
					if (array_key_exists('has_item', $item)) {
						if ($item['quantity'] < 1) {
							return $this->common->doError(func_num_args(), 'Atleast one package item is required');
						}
						$item_details = $this->db
							->where('id', $item['item_id'])
							->get('es_inventory_item')
							->row();

						$p_item = $this->db
							->where('package_id', $item['package_id'])
							->where('item_id', $item['item_id'])
							->get('es_package_items')
							->row();

						$stalls = $item['total_stalls'];
						$total_package_allow = $stalls * $p_item->initial_allow;
						if ($item['quantity'] > $total_package_allow) {
							return $this->common->doError(func_num_args(), 'Package item '.$item_details->item_title.' quantity cannot be greater then ' . $total_package_allow);
						}

						if ($is_edit) {
							$this->db->where('booking_id !=', $order_data->id);
						}
						$event_used_stock = $this->db
							->select('SUM(quantity) used_quantity')
							->where('exhibition_id', $this->formdata->id)
							->where('item_id', $item['item_id'])
							->get('es_exhibition_booking_items')
							->row();
						$event_used_stock = ($event_used_stock) ? $event_used_stock->used_quantity : 0;

						if (($event_used_stock + $item['quantity']) > $item_details->item_stock) {
							return $this->common->doError(func_num_args(), 'Does not have enough stock for package item '.$item_details->item_title . ', Remaning stock is ' . ($item_details->item_stock - $event_used_stock));
						}
					}
				}
				else {
					if (!array_key_exists('item_id', $item) || $item['item_id'] == '' || is_null($item['item_id'])) {
						return $this->common->doError(func_num_args(), 'Extra item field is required');
					}
					if (!array_key_exists('quantity', $item) || $item['quantity'] == '' || is_null($item['quantity']) || $item['quantity'] < 1) {
						return $this->common->doError(func_num_args(), 'Extra item quantity is required');
					}

					$item_details = $this->db
						->where('id', $item['item_id'])
						->get('es_inventory_item')
						->row();

					if (in_array($item['item_id'], $all_extra_items)) {
						return $this->common->doError(func_num_args(), 'Extra item '.$item_details->item_title.' is selected twice');
					}


					// get already used stock items
					if ($is_edit) {
						$this->db->where('booking_id !=', $order_data->id);
					}
					$event_used_stock = $this->db
						->select('SUM(quantity) used_quantity')
						->where('exhibition_id', $this->formdata->id)
						->where('item_id', $item['item_id'])
						->get('es_exhibition_booking_items')
						->row();
					$event_used_stock = ($event_used_stock) ? $event_used_stock->used_quantity : 0;
					// find same stock in selected package
					foreach ($this->input->post('order_items') as $pi) {
						if ($pi['is_package_item'] == 1) {
							if (array_key_exists('has_item', $pi) && $pi['item_id'] == $item['item_id']) {
								$event_used_stock += $pi['quantity'];
							}
						}
					}
					if (($event_used_stock + $item['quantity']) > $item_details->item_stock) {
						return $this->common->doError(func_num_args(), 'Does not have enough stock for extra item '.$item_details->item_title . ', Remaning stock is ' . ($item_details->item_stock - $event_used_stock));
					}

					$all_extra_items[] = $item['item_id'];
				}
			}

			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function book_confirm_validate() {
		$this->checkEditId();
		//echo '<pre>'; print_r($this->input->post()); die();
		$this->form_validation->set_rules('booking_type', 'booking_type*booking type', 'trim|required');
		$this->form_validation->set_rules('booking_offer_discount', 'booking_offer_discount*offer discount', 'trim');
		$this->form_validation->set_rules('booking_tax', 'booking_tax*tax', 'trim');
		$this->form_validation->set_rules('final_booking_amount', 'final_booking_amount*booking amount', 'trim|required');


		if (!is_null($this->input->post('has_additional_discount'))) {
			$this->form_validation->set_rules('additional_discount_type', 'additional_discount_type*discount_type', 'trim|required');
			$this->form_validation->set_rules('discount_remarks', 'discount_remarks*discount_remarks', 'trim|required');
			$this->form_validation->set_rules('discount_amount', 'discount_amount*discount_amount', 'trim|required|numeric');
		}

		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		}
		else {
			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function book_submit() {
		if ($this->book_customer_validate() !== true ||
			$this->book_stalls_validate() !== true ||
			$this->order_item_list_validate() !== true ||
			$this->book_confirm_validate() !== true) {
			echo json_encode(array(
				'error' => 1,
				'message' => 'Validation failed!'
			));
			die();
		}

		//echo '<pre>'; print_r($this->input->post()); die();

		$this->checkEditId();
		$exhibition_id = $this->formdata->id;

		$customer_id = $this->input->post('customer_id');
		$contact_person_id = $this->input->post('customer_contact_person_id');

		$agent_id = null;
		$has_agent = (is_null($this->input->post('has_sales_agent')) ? 0 : 1);
		if ($has_agent == 1) {
			$agent_id = $this->input->post('sales_agent_id');
		}

		$booking_data = array(
			'customer_id' => $customer_id,
			'contact_person_id' => $contact_person_id,
			'exhibition_id' => $exhibition_id,
			'booked_by' => $this->userdata->id,
			'booking_date' => date('Y-m-d', strtotime($this->input->post('order_date'))),
			'booking_type' => $this->input->post('booking_type'),
			'booking_price_type' => $this->input->post('booking_price_type'),
			'booking_amount' => $this->input->post('booking_sub_total'),
			'booking_total' => $this->input->post('final_booking_amount'),
			'is_active' => 1,
			'created_on' => date('Y-m-d H:i:s'),
			'has_agent' => $has_agent,
			'agent_id' => $agent_id,
		);

		if (!is_null($this->input->post('has_additional_discount'))) {
			$booking_data['has_additional_discount'] = 1;
			$booking_data['additional_discount_type'] = $this->input->post('additional_discount_type');
			$booking_data['additional_discount_remarks'] = $this->input->post('discount_remarks');
			$booking_data['additional_discount_amount'] = $this->input->post('discount_amount');
		}

		if ($this->input->post('booking_offer_discount') != '') {
			$offer = $this->db
				->where('id', $this->input->post('booking_offer_discount'))
				->get('es_discount_offers')
				->row();

			$booking_data['has_offer_discount'] = 1;
			$booking_data['offer_discount_id'] = $offer->id;
			$booking_data['offer_discount_amount'] = $this->input->post('booking_offer_discount_amount');
		}
		if ($this->input->post('booking_tax') != '') {
			$booking_data['has_booking_tax'] = 1;
			$booking_data['booking_tax_id'] = $this->input->post('booking_tax');
			$booking_data['booking_tax_amount'] = $this->input->post('booking_tax_total');
		}

		$this->db->insert('es_exhibition_booking', $booking_data);
		$booking_id = $this->db->insert_id();


		$all_stalls_data = array();
		$badge_data = array();
		$badge_invitation_check = array();
		$total_badges = 0;
		foreach ($this->input->post('stalls') as $booking_stall) {
			$stalls = explode(',', $booking_stall['stall_ids']);

			foreach ($stalls as $stall) {
				$a = array(
					'exhibition_id' => $exhibition_id,
					'stall_id' => $stall,
					'hall_id' => $booking_stall['hall_id'],
					'is_booked' => 1,
					'booking_id' => $booking_id,
					'customer_id' => $customer_id,
					'package_id' => $booking_stall['package_id'],
					'booking_stall_type' => $booking_stall['type'],
				);
				if ($this->input->post('booking_type') == 'confirmed') {
					$a['is_sold'] = 1;
				}
				$all_stalls_data[] = $a;
			}


			//badges
			$package_data = $this->db
				->where('id', $booking_stall['package_id'])
				->get('es_packages')
				->row();
			if (isset($package_data)) {
				$total_badges += $package_data->package_badges;
			}
			$package_badges = $this->db
				->where('package_id', $booking_stall['package_id'])
				->get('es_package_badges')
				->result();
			if (isset($package_badges)) {
				foreach ($package_badges as $badge) {
					$badge_qty = $badge->quantity;

					if (in_array($badge->invitation_type, $badge_invitation_check)) {
						$badge_qty = $badge_data[$badge->invitation_type]['quantity'] + $badge->quantity;
					} else {
						$badge_invitation_check[] = $badge->invitation_type;
					}

					$badge_data[$badge->invitation_type] = array(
						'exhibition_id' => $exhibition_id,
						'booking_id' => $booking_id,
						'badge_type' => $badge->badge_type,
						'invitation_type' => $badge->invitation_type,
						'quantity' => $badge_qty,
						'is_active' => $badge->is_active,
						'created_on' => date('Y-m-d H:i:s'),
					);
				}
			}

		}
		if (count($all_stalls_data) > 0) {
			$this->db->insert_batch('es_exhibition_booking_stalls', $all_stalls_data);
		}
		if (count($badge_data) > 0) {
			$this->db->insert_batch('es_exhibition_badges_limit', $badge_data);
		}

		if ($total_badges > 0) {
			$this->db
				->where('id', $booking_id)
				->update('es_exhibition_booking', array(
					'badges_total_limit' => $total_badges
				));
		}

		foreach ($this->input->post('order_items') as $item) {
			if ($item['is_package_item'] == 1) {
				if (array_key_exists('has_item', $item)) {
					$booking_items = array(
						'exhibition_id' => $exhibition_id,
						'booking_id' => $booking_id,
						'item_id' => $item['item_id'],
						'quantity' => $item['quantity'],
						'is_package_item' => 1,
						'package_id' => $item['package_id'],
						'is_initial_item' => 1,
						'item_price_usd' => 0,
						'item_price_pkr' => 0,
						'is_active' => 1,
						'added_by' => $this->userdata->id,
						'created_on' => date('Y-m-d H:i:s'),
					);
					$this->db->insert('es_exhibition_booking_items', $booking_items);
				}
			} else {
				$item_data = $this->db
					->where('id', $item['item_id'])
					->get('es_inventory_item')
					->row();
				$booking_items = array(
					'exhibition_id' => $exhibition_id,
					'booking_id' => $booking_id,
					'item_id' => $item['item_id'],
					'quantity' => $item['quantity'],
					'is_package_item' => 0,
					'is_initial_item' => 1,
					'item_price_usd' => $item_data->item_price_usd,
					'item_price_pkr' => $item_data->item_price_pkr,
					'is_active' => 1,
					'added_by' => $this->userdata->id,
					'created_on' => date('Y-m-d H:i:s'),
				);
				$this->db->insert('es_exhibition_booking_items', $booking_items);
			}

		}



		//TODO: send email to customer

		$this->session->set_flashdata('message', 'Stalls has been successfully sold to customer');
		echo json_encode(array(
			'error' => 0,
			'data' => base_url('book-stall-exhibitions.html')
		));
	}


	function ajax_get_hall_stalls() {
		$hall_id = $this->input->post('hall_id');
		$exhibition_id = $this->input->post('exhibition_id');
		if (is_null($hall_id) || is_null($exhibition_id)) {
			echo json_encode(array(
				'error' => 1,
				'message' => 'required parameters is missing'
			));
			exit;
		}

		$stalls = $this->db
			->where('hall_id', $hall_id)
			->where('exhibition_id', $exhibition_id)
			->where('is_deleted', 0)
			//->where('is_sold', 0)
			->get('es_exhibition_stalls')
			->result();

		if (!$stalls) {
			echo json_encode(array(
				'error' => 1,
				'message' => 'No stall available for booking'
			));
		} else {

			foreach ($stalls as $stall) {
				$stall->is_confirmed = 0;

				if ($stall->is_sold == 0) {
					$has_data = $this->db
						->where('stall_id', $stall->id)
						->get('es_exhibition_booking_stalls')
						->row();

					if (isset($has_data)) {
						$stall->is_booked = $has_data->is_booked;
						$stall->is_confirmed = $has_data->is_sold;
					}
				}
			}

			echo json_encode(array(
				'error' => 0,
				'data' => $stalls
			));
		}
	}

	function ajax_get_packages() {
		$type = $this->input->post('package_type');
		$exhibition_id = $this->input->post('exhibition_id');

		if (is_null($type) || is_null($exhibition_id)) {
			echo json_encode(array(
				'error' => 1,
				'message' => 'Required parameters is missing'
			));
			die();
		}

		$packages = $this->db
			->where('exhibition_id', $exhibition_id)
			->where('package_type', $type)
			->where('is_active', 1)
			->where('is_deleted', 0)
			->get('es_packages')
			->result();

		if (!$packages) {
			echo json_encode(array(
				'error' => 1,
				'message' => 'No package available for this type'
			));
		} else {
			echo json_encode(array(
				'error' => 0,
				'data' => $packages
			));
		}
	}

	function ajax_get_package_items() {
		$all_items = array();
		foreach ($this->input->post('stalls') as $booking) {
			$items = $this->db
				->select('P.*, 
			I.item_image,
			I.item_title,
			PA.package_title
			')
				->where(mycolumn('P.exhibition_id'), $this->input->get('id'))
				->where('P.package_id', $booking['package_id'])
				->join('es_inventory_item as I', 'I.id = P.item_id', 'LEFT')
				->join('es_packages as PA', 'PA.id = P.package_id', 'LEFT')
				->get('es_package_items as P')
				->result();

			if (!$items) {
				echo json_encode(array(
					'error' => 1,
					'message' => 'No items available for this package'
				));
			} else {
				foreach ($items as $item) {
					$stalls = explode(',', $booking['stall_ids']);
					$item->initial_allow = ($item->initial_allow * (count($stalls)));
					$item->total_stalls = count($stalls);
					$all_items[] = $item;
				}
			}
		}


		if (count($all_items) == 0) {
			echo json_encode(array(
				'error' => 1,
				'message' => 'No items available for this package'
			));
		} else {
			echo json_encode(array(
				'error' => 0,
				'data' => $all_items
			));
		}
	}

	function ajax_get_inventory_category() {

		$categories = $this->db
			->where('is_deleted', 0)
			->get('es_inventory_category')
			->result();

		echo json_encode(array(
			'error' => 0,
			'data' => $categories
		));
		exit;
	}

	function ajax_get_inventory_items() {
		$category = $this->input->post('category');
		$exhibition_id = $this->input->post('exhibition_id');
		if (is_null($category) || is_null($exhibition_id)) {
			echo json_encode(array(
				'error' => 1,
				'message' => 'Required parameters is missing'
			));
			exit;
		}

		$items = $this->db
			->where('exhibition_id', $exhibition_id)
			->where('category_id', $category)
			->where('is_active', 1)
			->where('is_deleted', 0)
			->get('es_inventory_item')
			->result();

		if (!$items) {
			echo json_encode(array(
				'error' => 1,
				'message' => 'No items available for this category'
			));
		} else {
			echo json_encode(array(
				'error' => 0,
				'data' => $items
			));
		}
	}

	function generate_order_invoice() {

		// echo '<pre>'; print_r($this->input->post()); die();

		$price_type = $this->input->post('booking_price_type');
		$price_stalls = $this->input->post('order_stall_total_amount');
		$price_additional_items = 0;
		$has_additional_discount = (is_null($this->input->post('has_additional_discount')) ? 0 : 1);

		$html = '<table class="table table-bordered">';
		$html .= '<tr>
			<th width="80%" colspan="3">Booking Order Description</th>
			<th width="20%" colspan="2">Amount</th>
			</tr>';

		// ORDER STALLS
		$html .= '<tr>';
		$html .= '<td colspan="2">
			<p>Stall Booking Description</p>';
		$html .= '<ol>';
		foreach ($this->input->post('stalls') as $stall) {
			$location = $this->db
				->where('id', $stall['hall_id'])
				->get('es_location_halls')
				->row();

			$package = $this->db
				->where('id', $stall['package_id'])
				->get('es_packages')
				->row();
			$stall_check = $this->db
				->where_in('id', explode(',', $stall['stall_ids']))
				->get('es_exhibition_stalls')
				->result();

			$html .= '<li style="margin-bottom: 10px">
				<div>Booking Space: ['.strtoupper($stall['type']).']</div>
				<div>Location: ['.$location->hall_title.']</div>
				<div>Stall Information: ['. implode(', ', array_map(function ($s){ return $s->stall_name; }, $stall_check)) .']</div>
				<div>Package: ['.$package->package_title.']</div>
				</li>';
		}
		$html .= '</ol>';
		$html .= '</td>';

		$html .= '<td class="text-center" width="5%">'.$price_type.'</td>';
		$html .= '<td class="text-right" colspan="2">'.$price_stalls.'</td>';
		$html .= '</tr>';

		// ORDER ITEMS
		$html .= '<tr>';
		$html .= '<tr>';
		$html .= '<td colspan="2">
			<p>Order Items Description</p>';
		$html .= '<ol>';

		// packages
		$html .= '<li>Packages<ul>';
		$all_packages_check = array();
		foreach ($this->input->post('stalls') as $stall) {
			$package = $this->db
				->where('id', $stall['package_id'])
				->get('es_packages')
				->row();
			$count = 1;
			foreach ($all_packages_check as $key => $c) {
				if ($key == ('package_' . $stall['package_id'])) $count++;
			}

			$all_packages_check['package_' . $stall['package_id']] = array(
				'package_title' => $package->package_title,
				'count' => $count,
			);
		}
		foreach ($all_packages_check as $pp) {
			$html .= '<li>['.$pp['package_title'].'] x '.$pp['count'].'</li>';
		}
		$html .= '</ul></li>';

		// additional
		$html .= '<li>Additional Item<ul>';
		foreach ($this->input->post('order_items') as $item) {
			if ($item['is_package_item'] == 0) {
				$item_details = $this->db
					->where('id', $item['item_id'])
					->get('es_inventory_item')
					->row();

				$html .= '<li>'.$item_details->item_title.' x '.$item['quantity'].'</li>';

				$item_price = ($price_type == 'PKR') ? $item_details->item_price_pkr : $item_details->item_price_usd;
				$price_additional_items += ($item_price * $item['quantity']);
			}
		}
		$html .= '</ul></li>';

		$html .= '</ol>';
		$html .= '<td class="text-center" width="5%">'.$price_type.'</td>';
		$html .= '<td class="text-right" colspan="2">'.$price_additional_items.'</td>';
		$html .= '</tr>';

		// TOTAL BEFORE TAX
		$sub_total = ($price_stalls + $price_additional_items);
		$html .= '<tr>';
		$html .= '<td rowspan="3" width="60%">
			<p><strong>Total Net Amount In words:</strong></p>
			<div id="invoice_word_amount"></div>
			</td>';
		$html .= '<td colspan="2" class="text-right">SUBTOTAL</td>';
		$html .= '<td class="text-center" width="5%">'.$price_type.'</td>';
		$html .= '<td class="text-right">'.$sub_total.'</td>';
		$html .= '</tr>';

		$discount_percentage = 0;

		if ($this->input->post('booking_offer_discount') && $this->input->post('booking_offer_discount') != '') {
			$offer = $this->db
				->where('id', $this->input->post('booking_offer_discount'))
				->get('es_discount_offers')
				->row();

			if ($offer) {
				$discount_percentage = $offer->discount;
			}
		}

		$discount_price = ($sub_total * $discount_percentage) / 100;
		$html .= '<tr>';
		$html .= '<td class="text-right">LESS DISCOUNT</td>';
		$html .= '<td class="active-dark">'.$discount_percentage.'%</td>';
		$html .= '<td class="text-center" width="5%">'.$price_type.'</td>';
		$html .= '<td class="text-right"><div>('.$discount_price.')</div>';
		if ($has_additional_discount == 1) {
			$discount_price += $this->input->post('discount_amount');
			$html .= '<div>+ ('.$this->input->post('discount_amount').')</div>';
		}
		$html .= '</td>';
		$html .= '</tr>';

		$html .= '<tr>';
		$html .= '<td colspan="2" class="text-right">EXCLUDING SALES TAX VALUE</td>';
		$html .= '<td class="text-center" width="5%">'.$price_type.'</td>';
		$html .= '<td class="text-right">'.($sub_total - $discount_price).'</td>';
		$html .= '</tr>';


		// TOTAL AFTER TAX
		$total_tax = 0;
		$html .= '<tr>';
		$html .= '<td rowspan="2" width="60%">
			<p>*APPLIED TAX RATE INFORMATION:</p>';
		// check has tax
		if ($this->input->post('booking_tax') && $this->input->post('booking_tax') != '') {
			$tax_data = $this->db
				->where('id', $this->input->post('booking_tax'))
				->where('tax_year', date('Y'))
				->get('es_tax_rate')
				->row();

			if ($tax_data) {
				$tax_gst_amount = 0;
				$tax_wht_amount = 0;
				if ($tax_data->has_gst == 1) {
					$tax_gst_amount = (($sub_total - $discount_price) * $tax_data->gst_rate) / 100;
					$html .= '<div>GENERAL SALES TAX - ['.$tax_gst_amount.'] @ ['.$tax_data->gst_rate.' %]</div>';
				}
				if ($tax_data->has_wht == 1) {
					$tax_wht_amount = (($sub_total - $discount_price) * $tax_data->wht_rate) / 100;
					$html .= '<div>WITHHOLDING TAX - ['.$tax_wht_amount.'] @ ['.$tax_data->wht_rate.' %]</div>';
				}

				$total_tax = ($tax_gst_amount + $tax_wht_amount);
			}
		}

		$html .= '</td>';
		$html .= '<td colspan="2" class="text-right">TOTAL TAXES VALUE </td>';
		$html .= '<td class="text-center" width="5%">'.$price_type.'</td>';
		$html .= '<td class="text-right">'.$total_tax.'</td>';
		$html .= '</tr>';

		$html .= '<tr>';
		$html .= '<th colspan="2" class="text-right">TOTAL VALUE INCLUDING TAX</th>';
		$html .= '<th class="text-center" width="5%">'.$price_type.'</th>';
		$html .= '<th class="text-right">'.(($sub_total - $discount_price) + $total_tax).'</th>';
		$html .= '</tr>';
		$html .= '</table>';
		$html .= '<input type="hidden" name="booking_sub_total" value="'. $sub_total .'">';
		$booking_offer_discount_amount = $discount_price;
		if ($this->input->post('discount_amount')) {
			$booking_offer_discount_amount -= $this->input->post('discount_amount');
		}
		$html .= '<input type="hidden" name="booking_offer_discount_amount" value="'. ($booking_offer_discount_amount) .'">';
		$html .= '<input type="hidden" name="booking_tax_total" value="'. $total_tax .'">';
		$html .= '<input type="hidden" name="final_booking_amount" value="'.(($sub_total - $discount_price) + $total_tax).'">';
		$html .= '<script>$("#invoice_word_amount").html("'.$this->convertNumberToWord(($sub_total - $discount_price) + $total_tax).'");</script>';

		echo $html;
		exit;
	}

}