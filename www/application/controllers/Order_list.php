<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_list extends MY_Controller
{
    protected function rule()
    {
        $this->activateRightsSystem();
        $crd = array(
            'crd_list,
			event_order_list,
			order_detail,
			print_order_invoice,
			confirm_order,
			cancel_order,
			approve_order,
			edit_order_submit,
			order_extended_form_submit,
			send_order_invitation_view,
			send_order_invitation_submit,
			' => array(
                'rule' => '@'
            ),
            'crd_list_datatable,
			order_list_datatable,
			send_order_invitation_validate,
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
		if ($this->userdata->user_group_id == SALES_PERSON) {
			$this->db->where('booked_by', $this->userdata->id);
		}
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
        $this->load->view('order_list/list');
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
				if ($this->userdata->user_group_id == SALES_PERSON) {
					$this->db->where('booked_by', $this->userdata->id);
				}
                return $this->db
                    ->where('exhibition_id', $row['id'])
                    ->where('is_canceled', 0)
                    ->count_all_results('es_exhibition_booking');
            }, NULL)
            ->add_column('col_action', function ($row) {
                $id = $row['id'];
                $html = '<a href="' . base_url() . 'event-order-list.html?id=' . urlencode(myid($id)) . '">View Orders</a>';

                return "<div class='text-center'>{$html}</div>";
            }, NULL)
            ->where('E.is_deleted', 0)
            ->join('es_locations as L', 'E.location_id = L.id', 'LEFT')
            ->from('es_exhibitions as E');

        print ($this->datatables->generate());
    }


    function event_order_list() {
        $this->checkEditId();

        $this->load->view('includes/after_login/head');
        $this->load->view('order_list/event_order_list');
    }

    function order_list_datatable() {
		$this->checkEditId();

        $this->load->library('datatables');
        $this->datatables
            ->select('B.id,
            		B.exhibition_id,
                     C.company,
                     CONCAT(U.user_first_name, " ", U.user_last_name) as booked_by,
                     B.booking_date,
                     
                     IF(B.is_approved = 1, "<span class=\'label label-success\'>Approved</span>",
                     IF(B.booking_type = "confirmed", "<span class=\'label label-success\'>Confirm Sale</span>",
				  	 IF(B.booking_type = "tentative", "<span class=\'label label-warning\'>Tentative</span>", "N/A"))) as booking_type,
				  	 
                     
                     CONCAT(B.booking_price_type, " ", B.booking_total) as booking_total
				  ', false)

			->unset_column('B.exhibition_id')
            ->add_column('col_action', function ($row) {
                $exhibition_id = $row['exhibition_id'];
                $id = $row['id'];
                $html = '<a href="' . base_url() . 'order_detail.html?id=' . urlencode(myid($exhibition_id)) . '&order_id=' . urlencode(myid($id)) . '">View Details</a>';

                return "<div class='text-center'>{$html}</div>";
            }, NULL)
            ->where('B.exhibition_id', $this->formdata->id)
            ->where('B.is_canceled', 0)
            ->join('es_customers as C', 'B.customer_id = C.id')
            ->join('users as U', 'B.booked_by = U.id')
			->from('es_exhibition_booking as B');

        if ($this->userdata->user_group_id == MANAGER) {
			$this->datatables->where('B.booking_type', 'confirmed');
		}

		if ($this->userdata->user_group_id == SALES_PERSON) {
			$this->datatables->where('B.booked_by', $this->userdata->id);
		}

        print ($this->datatables->generate());
    }

    function order_detail() {
		$this->checkEditId();
		$this->getOrderData();

		$customer_data = $this->db
			->where('id', $this->orderdata->customer_id)
			->get('es_customers')
			->row();

		$booking_stalls = $this->db
			->select('BS.*, 
			S.stall_price_usd, 
			S.stall_price_pkr,
			S.stall_name,
			')
			->where('BS.booking_id', $this->orderdata->id)
			->join('es_exhibition_stalls as S', 'S.id = BS.stall_id', 'LEFT')
			->get('es_exhibition_booking_stalls as BS')
			->result();

        $this->load->view('includes/after_login/head');

        if (!$this->input->get('tab') || $this->input->get('tab') == 'order_detail') {
			$this->load->view('order_list/order_detail', array(
				'customer_data' => $customer_data,
				'booking_stalls' => $booking_stalls,
			));
		} elseif ($this->input->get('tab') == 'edit_order') {

			if (date('Y-m-d') > date('Y-m-d', strtotime($this->formdata->booking_expire_date))) {
				$this->session->set_flashdata('error', 'Order cannot be edit because event is expired!');
				redirect(base_url('order_detail.html?id='.$this->input->get('id').'&order_id='.$this->input->get('order_id').'&tab=order_detail'));
				die();
			}

			if ($this->userdata->user_group_id == SALES_PERSON && $this->userdata->can_edit_orders == 0) {
				$this->session->set_flashdata('error', 'You do not have rights to edit orders, Please contact to your administrator!');
				redirect(base_url('order_detail.html?id='.$this->input->get('id').'&order_id='.$this->input->get('order_id').'&tab=order_detail'));
				die();
			}

        	/*if ($this->orderdata->is_approved == 1) {
				$this->session->set_flashdata('error', 'Order cannot be edit after approved!');
				redirect(base_url('order_detail.html?id='.$this->input->get('id').'&order_id='.$this->input->get('order_id').'&tab=order_detail'));
        		die();
			}*/

			$this->load->view('order_list/order_edit', array(
				'customer_data' => $customer_data,
				'booking_stalls' => $booking_stalls,
			));
		} elseif ($this->input->get('tab') == 'order_logs') {
			$this->load->view('order_list/order_logs');
		} elseif ($this->input->get('tab') == 'expired_forms') {
			$this->load->view('order_list/order_expired_forms');
		} else {
        	show_404();
		}
    }

    function print_order_invoice() {
		$this->checkEditId();
		$this->getOrderData();


		$html = '<page>';
		$html .= '<style>
	
		table {
		border: 1px solid #ccc;
		width: 430px;
		font-family: Helvetica, Arial, sans-serif;
		font-size: 14px;
		border-spacing: 0;
    	border-collapse: collapse;
    	-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		}
		table th,
		table td {
		border: 1px solid #ccc;
		padding: 5px;
		width: auto;
		}
		
		.text-right {
		text-align: right;
		}
		.text-center {
		text-align: center;
		}
		</style>';

		$html .= '<h2>'.$this->formdata->exhibition_title.'</h2>';

		$customer_data = $this->db
			->where('id', $this->orderdata->customer_id)
			->get('es_customers')
			->row();
		$contact_person_data = $this->db
			->where('id', $this->orderdata->contact_person_id)
			->get('es_customer_contact_persons')
			->row();
		$sales_person = $this->db
			->where('id', $this->orderdata->booked_by)
			->get('users')
			->row();

		$html .= '<table>
			<tr>
				<th colspan="4">Customer Information</th>
				<th>Order Date</th>
				<th>Payment Mode</th>
			</tr>
			<tr>
				<th>Company Name</th>
				<td>'. $customer_data->company .'</td>
				<th>Contact Person</th>
				<td>'. $customer_data->company .'</td>
				<td>'. date('d/m/Y', strtotime($this->orderdata->booking_date)) .'</td>
				<td>'. $this->orderdata->booking_price_type .'</td>
			</tr>
			<tr>
				<td colspan="3" id="contact_person_address_area">
					'. $contact_person_data->person_name .'<br>
					'. $contact_person_data->primary_email .'<br>
					'. $contact_person_data->primary_phone .'
				</td>
				<td colspan="3" id="company_address_area">
					'. $customer_data->company .'<br>
					'. $customer_data->email .'<br>
					'. $customer_data->phone .'
				</td>
			</tr>
			<tr>
				<th colspan="3">Billing Address</th>
				<th colspan="3">Sales Person</th>
			</tr>
			<tr>
				<td colspan="3">'. preg_replace("/(.{30})/", "$1<br>", $customer_data->address) .'</td>
				<td colspan="3">'. $sales_person->user_first_name .' '. $sales_person->user_last_name .'</td>
			</tr>
		</table><p>&nbsp;</p>';



		// Booking data
		$price_type = $this->orderdata->booking_price_type;
		$price_stalls = $this->orderdata->booking_amount;
		$price_additional_items = 0;
		$has_additional_discount = $this->orderdata->has_additional_discount;

		$html .= '<table>';
		$html .= '<tr>
			<th width="80%" colspan="3">Booking Order Description</th>
			<th width="20%" colspan="2">Amount</th>
			</tr>';

		// ORDER STALLS
		$html .= '<tr>';
		$html .= '<td colspan="2">
			<p>Stall Booking Description</p>';
		$html .= '<ol>';
		$stalls_by_packages = $this->db
			->where('BS.booking_id', $this->orderdata->id)
			->group_by('BS.package_id')
			->get('es_exhibition_booking_stalls as BS')
			->result();
		$total_stall_price = 0;
		foreach ($stalls_by_packages as $stall) {
			$location = $this->db
				->where('id', $stall->hall_id)
				->get('es_location_halls')
				->row();

			$package = $this->db
				->where('id', $stall->package_id)
				->get('es_packages')
				->row();

			$stall_check = $this->db
				->select('BS.*, 
				S.stall_price_usd, 
				S.stall_price_pkr,
				S.stall_name,
				')

				->where('BS.package_id', $stall->package_id)
				->where('BS.booking_id', $this->orderdata->id)
				->join('es_exhibition_stalls as S', 'S.id = BS.stall_id', 'LEFT')
				->get('es_exhibition_booking_stalls as BS')
				->result();

			$html .= '<li style="margin-bottom: 10px">
				<div>Booking Space: ['.strtoupper($stall->booking_stall_type).']</div>
				<div>Location: ['.$location->hall_title.']</div>
				<div>Stall Information: ['. implode(', ', array_map(function ($s){ return $s->stall_name; }, $stall_check)) .']</div>
				<div>Package: ['.$package->package_title.']</div>
				</li>';

			foreach ($stall_check as $c) {
				$total_stall_price += ($price_type == 'PKR') ? ($c->stall_price_pkr) : ($c->stall_price_usd);
			}
		}
		$html .= '</ol>';
		$html .= '</td>';

		$html .= '<td class="text-center" width="5%">'.$price_type.'</td>';
		$html .= '<td class="text-right" colspan="2">'.$total_stall_price.'</td>';//FIX ME
		$html .= '</tr>';


		// ORDER ITEMS
		$html .= '<tr>';
		$html .= '<td colspan="2">
			<p>Order Items Description</p>';
		$html .= '<ol>';

		// packages
		$html .= '<li>Packages<ul>';
		$all_packages_check = array();
		foreach ($stalls_by_packages as $stall) {
			$package = $this->db
				->where('id', $stall->package_id)
				->get('es_packages')
				->row();
			$count = 1;
			foreach ($all_packages_check as $key => $c) {
				if ($key == ('package_' . $stall->package_id)) $count++;
			}

			$all_packages_check['package_' . $stall->package_id] = array(
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
		$order_items = $this->db
			->where('booking_id', $this->orderdata->id)
			->get('es_exhibition_booking_items')
			->result();
		foreach ($order_items as $item) {
			if ($item->is_package_item == 0) {
				$item_details = $this->db
					->where('id', $item->item_id)
					->get('es_inventory_item')
					->row();

				$html .= '<li>'.$item_details->item_title.' x '.$item->quantity.'</li>';

				$item_price = ($price_type == 'PKR') ? $item_details->item_price_pkr : $item_details->item_price_usd;
				$price_additional_items += ($item_price * $item->quantity);
			}
		}
		$html .= '</ul></li>';

		$html .= '</ol></td>';
		$html .= '<td class="text-center" width="5%">'.$price_type.'</td>';
		$html .= '<td class="text-right" colspan="2">'.$price_additional_items.'</td>';
		$html .= '</tr>';

		// TOTAL BEFORE TAX
		$sub_total = $price_stalls;
		$html .= '<tr>';
		$html .= '<td rowspan="3" width="60%">
			<p><strong>Total Net Amount In words:</strong></p>
			<div id="invoice_word_amount">'. $this->funcs->convertNumberToWord($this->orderdata->booking_total) .'</div>
			</td>';
		$html .= '<td colspan="2" class="text-right">SUBTOTAL</td>';
		$html .= '<td class="text-center" width="5%">'.$price_type.'</td>';
		$html .= '<td class="text-right">'.$sub_total.'</td>';
		$html .= '</tr>';

		$discount_percentage = 0;
		$discount_price = 0;
		if ($this->orderdata->has_offer_discount == 1) {
			$offer = $this->db
				->where('id', $this->orderdata->offer_discount_id)
				->get('es_discount_offers')
				->row();

			if ($offer) {
				$discount_percentage = $offer->discount;
			}
			$discount_price = $this->orderdata->offer_discount_amount;
		}


		$html .= '<tr>';
		$html .= '<td class="text-right">LESS DISCOUNT</td>';
		$html .= '<td class="active-dark">'.$discount_percentage.'%</td>';
		$html .= '<td class="text-center" width="5%">'.$price_type.'</td>';
		$html .= '<td class="text-right"><p>('.$discount_price.')</p>';
		if ($has_additional_discount == 1) {
			$discount_price += $this->orderdata->additional_discount_amount;
			$html .= '<p>+ ('.$this->orderdata->additional_discount_amount.')</p>';
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
		if ($this->orderdata->has_booking_tax == 1) {
			$tax_data = $this->db
				->where('id', $this->orderdata->booking_tax_id)
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
					$tax_wht_amount = (($sub_total - $discount_price) * $tax_data->has_wht) / 100;
					$html .= '<div>WITHHOLDING TAX - ['.$tax_wht_amount.'] @ ['.$tax_data->wht_rate.' %]</div>';
				}

				$total_tax = $this->orderdata->booking_tax_amount;
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

		$html .= '</page>';


		//echo $html; die();
		set_time_limit(0);
		$this->load->helper ("pdf-loader");
		try {
			$html2pdf = new HTML2PDF('P', 'A4', 'en');
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->writeHTML($html);
			$html2pdf->Output('order-'.$this->orderdata->id.'.pdf');
		}
		catch(HTML2PDF_exception $e) {
			$msg = $e->getMessage ();
			//$msg = base64_encode($msg);
			//$msg = trim ($msg);
			print_r($msg);
		}
	}

    function confirm_order() {
		$this->checkEditId();
		$this->getOrderData();

		$this->db
			->where('id', $this->orderdata->id)
			->update('es_exhibition_booking', array(
				'booking_type' => 'confirmed'
			));

		$this->db
			->where('booking_id', $this->orderdata->id)
			->update('es_exhibition_booking_stalls', array(
				'is_sold' => 1
			));

		$this->db
			->insert('es_exhibition_booking_logs', array(
				'booking_id' => $this->orderdata->id,
				'user_id' => $this->userdata->id,
				'message' => 'Order has been confirmed by ' . $this->userdata->user_first_name . ' ' . $this->userdata->user_last_name,
				'created_on' => date('Y-m-d H:i:s')
			));

		$this->session->set_flashdata('message', 'Order has been confirmed successfully');
		redirect(base_url('event-order-list.html?id=' . myid($this->formdata->id)));
	}


    function cancel_order() {
		$this->checkEditId();
		$this->getOrderData();


		$this->db
			->where('id', $this->orderdata->id)
			->update('es_exhibition_booking', array(
				'is_active' => 0,
				'is_canceled' => 1,
				'canceled_by' => $this->userdata->id,
				'canceled_on' => date('Y-m-d H:i:s')
			));

        // remove tentative stalls
        $this->db
            ->where('booking_id', $this->orderdata->id)
            ->delete('es_exhibition_booking_stalls');

        // remove confirm stalls
        $this->db
			->where('booking_id', $this->orderdata->id)
			->update('es_exhibition_stalls', array(
				'is_booked' => 0,
				'is_sold' => 0,
				'booking_id' => null,
				'customer_id' => null,
			));

		/*$this->db
			->where('booking_id', $this->orderdata->id)
			->update('es_exhibition_booking_items', array(
				'is_active' => 0
			));*/

		$this->db
			->where('booking_id', $this->orderdata->id)
			->delete('es_exhibition_booking_items');


		// remove badges and invitation
		$this->db
			->where('booking_id', $this->orderdata->id)
			->update('es_exhibition_badges', array(
				'is_active' => 0,
				'modified_on' => date('Y-m-d H:i:s')
			));

		$this->db
			->where('booking_id', $this->orderdata->id)
			->delete('es_exhibition_badges_invitation');


		$log = 'Order has been canceled by ' . $this->userdata->user_first_name . ' ' . $this->userdata->user_last_name;
		$log .= '. Reason to cancel is: ' . $this->input->post('cancel_reason');
		$this->db
			->insert('es_exhibition_booking_logs', array(
				'booking_id' => $this->orderdata->id,
				'user_id' => $this->userdata->id,
				'message' => $log,
				'created_on' => date('Y-m-d H:i:s')
			));

		$this->session->set_flashdata('message', 'Order has been canceled successfully');
		redirect(base_url('event-order-list.html?id=' . myid($this->formdata->id)));
	}


	function approve_order() {
		$this->checkEditId();
		$this->getOrderData();

		$customer = $this->db
			->where('id', $this->orderdata->customer_id)
			->get('es_customers')
			->row();

		/*$login_name = "";
		$login_pass = "";
		$code= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$code.= "0123456789";
		for ($i=0; $i < 6; $i++) {
			$login_name .= $code[rand(0, (strlen($code)-1))];
		}
		$login_name = $login_name . $this->orderdata->id;
		$code .= '!@$^&';
		for ($i=0; $i < 12; $i++) {
			$login_pass .= $code[rand(0, (strlen($code)-1))];
		}*/

		//TODO: cancel other tentative stalls and mark sold in es_exhibition_stalls
		$stalls = $this->db
			->where('booking_id', $this->orderdata->id)
			->get('es_exhibition_booking_stalls')
			->result();
		foreach ($stalls as $stall) {
			$this->db
				->where('id', $stall->stall_id)
				->update('es_exhibition_stalls', array(
					'is_booked' => 1,
					'is_sold' => 1,
					'booking_id' => $this->orderdata->id,
					'customer_id' => $stall->customer_id,
				));
		}


		$this->db
			->where('id', $this->orderdata->id)
			->update('es_exhibition_booking', array(
				//'login_id' => $login_name,
				//'login_password' => md5($login_pass),
				'is_approved' => 1,
				'approved_by' => $this->userdata->id,
				'invitation_sent' => 0,
			));

		$this->db
			->insert('es_exhibition_booking_logs', array(
				'booking_id' => $this->orderdata->id,
				'user_id' => $this->userdata->id,
				'message' => 'Order has been approved by ' . $this->userdata->user_first_name . ' ' . $this->userdata->user_last_name,
				'created_on' => date('Y-m-d H:i:s')
			));



		/*$login_link = base_url('client/login/' . $this->formdata->id . '-' . str_replace(' ', '-', $this->formdata->exhibition_title));
		$message = '<p>Hello,</p>';
		$message .= '<p>Your order has been approved, you can now login to your portal!</p><br>';
		$message .= '<p><a href="'.$login_link.'" target="_blank">'.$login_link.'</a></p><br>';
		$message .= '<div><strong>User Name: </strong>'.$login_name.'</div>';
		$message .= '<div><strong>Password: </strong>'.$login_pass.'</div><br><br>';

		$this->funcs->send_email($customer->email, 'Login Details', $message, $this->formdata->exhibition_title);*/

		/*$message_text = $this->load->view('email', array(
			'message' => $message
		), true);
		echo $message_text; die();*/

		$this->session->set_flashdata('message', 'Order has been approved successfully');
		redirect(base_url('event-order-list.html?id=' . myid($this->formdata->id)));
	}


	function edit_order_submit() {
		$this->checkEditId();
		$this->getOrderData();

		//echo '<pre>'; print_r($this->input->post()); die();

		// recalculate amount
		$price_stalls = $this->input->post('order_stall_total_amount');
		$price_additional_items = 0;

		$additional_items = $this->db
			->where('booking_id', $this->orderdata->id)
			->where('is_package_item', 0)
			->get('es_exhibition_booking_items')
			->result();

		foreach ($additional_items as $additional_item) {
			$p = ($this->orderdata->booking_price_type == 'PKR') ? $additional_item->item_price_pkr : $additional_item->item_price_usd;

			$price_additional_items += ($p * $additional_item->quantity);
		}

		$sub_total = ($price_stalls + $price_additional_items);

		$discount_price = 0;
		$offer_discount_price = 0;

		if ($this->orderdata->has_offer_discount == 1) {
			$discount_percentage = 0;
			$offer = $this->db
				->where('id', $this->orderdata->offer_discount_id)
				->get('es_discount_offers')
				->row();

			if ($offer) {
				$discount_percentage = $offer->discount;
			}

			$discount_price = ($sub_total * $discount_percentage) / 100;
			$offer_discount_price = ($sub_total * $discount_percentage) / 100;
		}

		if ($this->orderdata->has_additional_discount == 1) {
			$discount_price += $this->orderdata->additional_discount_amount;
		}

		$total_tax = 0;

		if ($this->orderdata->has_booking_tax == 1) {
			$tax_data = $this->db
				->where('id', $this->orderdata->booking_tax_id)
				->where('tax_year', date('Y'))
				->get('es_tax_rate')
				->row();

			if ($tax_data) {
				$tax_gst_amount = 0;
				$tax_wht_amount = 0;
				if ($tax_data->has_gst == 1) {
					$tax_gst_amount = (($sub_total - $discount_price) * $tax_data->gst_rate) / 100;
				}
				if ($tax_data->has_wht == 1) {
					$tax_wht_amount = (($sub_total - $discount_price) * $tax_data->wht_rate) / 100;
				}

				$total_tax = ($tax_gst_amount + $tax_wht_amount);
			}
		}


		$booking_data = array();
		$booking_data['booking_amount'] = $sub_total;
		$booking_data['booking_total'] = (($sub_total - $discount_price) + $total_tax);

		$booking_data['offer_discount_amount'] = $offer_discount_price;
		$booking_data['booking_tax_amount'] = $total_tax;


		// if order is approved then check data in es_exhibition_stalls
		if ($this->orderdata->is_approved == 1) {
			$booking_data['is_approved'] = 0;
			$booking_data['approved_by'] = null;

			$this->db
				->where('booking_id', $this->orderdata->id)
				->update('es_exhibition_stalls', array(
					'is_booked' => 0,
					'is_sold' => 0,
					'booking_id' => null,
					'customer_id' => null,
				));
		}

		$this->db
			->where('id', $this->orderdata->id)
			->update('es_exhibition_booking', $booking_data);


		// remove old stalls
		$this->db
			->where('booking_id', $this->orderdata->id)
			->delete('es_exhibition_booking_stalls');

		$this->db
			->where('booking_id', $this->orderdata->id)
			->delete('es_exhibition_badges_limit');

		$all_stalls_data = array();
		$badge_data = array();
		$badge_invitation_check = array();
		foreach ($this->input->post('stalls') as $booking_stall) {
			$stalls = explode(',', $booking_stall['stall_ids']);

			foreach ($stalls as $stall) {
				$a = array(
					'exhibition_id' => $this->formdata->id,
					'stall_id' => $stall,
					'hall_id' => $booking_stall['hall_id'],
					'is_booked' => 1,
					'booking_id' => $this->orderdata->id,
					'customer_id' => $this->orderdata->customer_id,
					'package_id' => $booking_stall['package_id'],
					'booking_stall_type' => $booking_stall['type'],
				);
				if ($this->orderdata->booking_type == 'confirmed') {
					$a['is_sold'] = 1;
				}
				$all_stalls_data[] = $a;
			}


			//badges
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
						'exhibition_id' => $this->formdata->id,
						'booking_id' => $this->orderdata->id,
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


		// update package items
		$this->db
			->where('booking_id', $this->orderdata->id)
			->where('is_package_item', 1)
			->where('is_initial_item', 1)
			->delete('es_exhibition_booking_items');

		foreach ($this->input->post('order_items') as $item) {
			if ($item['is_package_item'] == 1) {
				if (array_key_exists('has_item', $item)) {
					$booking_items = array(
						'exhibition_id' => $this->formdata->id,
						'booking_id' => $this->orderdata->id,
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
			}
		}


		$this->db
			->insert('es_exhibition_booking_logs', array(
				'booking_id' => $this->orderdata->id,
				'user_id' => $this->userdata->id,
				'message' => 'Order has been modified by ' . $this->userdata->user_first_name . ' ' . $this->userdata->user_last_name,
				'created_on' => date('Y-m-d H:i:s')
			));

		//TODO: send email to customer


		$this->session->set_flashdata('message', 'Order has been successfully updated');
		redirect(base_url('order_detail.html?id='.$this->input->get('id').'&order_id='.$this->input->get('order_id').'&tab=order_detail'));
	}


	function order_extended_form_submit() {
		$this->checkEditId();
		$this->getOrderData();

		$this->db
			->where('booking_id', $this->orderdata->id)
			->delete('es_exhibition_forms_extended');

		$data = array();
		foreach ($this->input->post('forms') as $form){
			if (array_key_exists('is_extended', $form)) {
				if ($form['extended_hours'] < 1) {
					show_404();
					die();
				}
				$data[]=array(
					'exhibition_id' => $this->formdata->id,
					'booking_id' => $this->orderdata->id,
					'form_id' => $form['form_id'],
					'extended_hours' => $form['extended_hours'],
					'update_date' => date('Y-m-d H:i:s'),
				);
			}

		}

		if (count($data) > 0) {
			$this->db->insert_batch('es_exhibition_forms_extended',$data);
		}

		$this->session->set_flashdata('message', 'Forms has been successfully extended!');
		redirect(base_url('order_detail.html?id='.$this->input->get('id').'&order_id='.$this->input->get('order_id').'&tab=order_detail'));
	}


	function send_order_invitation_view() {
		$this->checkEditId();

		$this->load->view('includes/after_login/head');
		$this->load->view('order_list/order_invitation_view');
	}

	function send_order_invitation_validate() {
		$this->checkEditId();

		$this->form_validation->set_rules('invitation[]', 'invitation[]*invitation', 'trim|required');

		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else {
			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function send_order_invitation_submit() {
		$this->checkEditId();

		$orders = $this->db
			->select('B.*, C.email as company_email')
			->where_in('B.id', $this->input->post('invitation'))
			->group_by('B.id')
			->join('es_customers as C', 'B.customer_id = C.id', 'LEFT')
			->get('es_exhibition_booking as B')
			->result();

		$email_data = array();
		foreach ($orders as $order) {
			$code = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$code.= "0123456789";
			$code .= '!@$^&';
			$login_name = $order->company_email;
			$login_pass = "";
			for ($i=0; $i < 12; $i++) {
				$login_pass .= $code[rand(0, (strlen($code)-1))];
			}

			$login_link = base_url('client/login/' . $this->formdata->id . '-' . str_replace(' ', '-', $this->formdata->exhibition_title));
			$message = '<p>Dear Exhibitor,</p>';
			$message .= '<p>Team IDEAS 2018 are proud to welcome you as a prestigious exhibitors and contributor of 10th anniversary edition of International Defence Exhibition & Seminar – IDEAS 2018 to be held from 27th to 30th November 2018 at Karachi Expo Centre - Pakistan.</p>';
			$message .= '<p>Our teams are committed to supporting you through this experience. In order to assist you both before and during the event, we have developed and installed a user friendly online Exhibitor Facilitation Centre (EFC) that shall stay in constant communication with you for all your facilitation needs as and when required. </p>';
			$message .= '<p>We would request that you please appoint ONE key contact person for your participation and provide all their contact information to enable us to provide you with our best attention and services. </p>';
			$message .= '<hr>';
			$message .= '<div>Following are the login details.</div>';
			$message .= '<div><strong>User ID: </strong>'.$login_name.'</div>';
			$message .= '<div><strong>Password: </strong>'.$login_pass.'</div><br><br>';
			$message .= '<div><a href="'.$login_link.'" target="_blank">'.$login_link.'</a></div>';
			$message .= '<hr>';

			$message .= '<p>Furthermore, we highly recommend that you download Exhibitor Manual, this will allow for an in depth understanding of the event and exhibitor facilitation services. In the meantime please feel free to email or contact the undersigned in case of any query that you may have. Our teams look forward to welcoming you at IDEAS 2018.</p>';

			$message .= '<br>';
			$message .= '<br>';
			$message .= '<p>Thanks & Best Regards,</p>';
			$message .= '<br>';
			$message .= '<div>BXSS Facilitation Team - IDEAS 2018</div>';
			$message .= '<div>International Communications & Marketing Manager</div>';
			$message .= '<div>Badar Expo Solutions (Pvt.) Ltd.</div>';
			$message .= '<div>Tel: +92-21-34821159-60</div>';
			$message .= '<div>Fax: +92.21.34821179</div>';
			$message .= '<div>Cell: +92-300-0228560</div>';
			$message .= '<div>Email: facilitation@exhibit.com.pk</div>';


			$email_data[] = array(
				'type' => 'order_invitation',
				'data' => json_encode(array('order_id' => $order->id)),
				'email' => $order->company_email,
				'subject' => 'Details',
				'message' => $message,
				'created_on' => date('Y-m-d H:i:s'),
			);

			$this->db
				->where('id', $order->id)
				->update('es_exhibition_booking', array(
					'login_id' => $login_name,
					'login_password' => md5($login_pass),
				));

			$this->db
				->insert('es_exhibition_booking_logs', array(
					'booking_id' => $order->id,
					'user_id' => $this->userdata->id,
					'message' => 'Order login re-generated and send to customer by ' . $this->userdata->user_first_name . ' ' . $this->userdata->user_last_name,
					'created_on' => date('Y-m-d H:i:s')
				));
		}

		if (count($email_data) > 0) {
			$this->db->insert_batch('es_emails_cron', $email_data);
		}


		$this->session->set_flashdata('message', 'Order Invitations successfully sent to customers');
		redirect(base_url('event-order-list.html?id=' . myid($this->formdata->id)));
	}
}