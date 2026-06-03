<?php

$location = $this->db
	->where('id', $this->event->location_id)
	->get('es_locations')
	->row();

$contact_person_data = $this->db
	->where('id', $this->booking->contact_person_id)
	->get('es_customer_contact_persons')
	->row();

$halls = $this->db
	->select('BS.*, L.hall_title')
	->where('BS.booking_id', $this->booking->id)
	->group_by('BS.hall_id')
	->join('es_location_halls as L', 'L.id = BS.hall_id', 'LEFT')
	->get('es_exhibition_booking_stalls as BS')
	->result();

$stalls = $this->db
	->select('BS.*, S.stall_name')
	->where('BS.booking_id', $this->booking->id)
	->join('es_exhibition_stalls as S', 'S.id = BS.stall_id', 'LEFT')
	->get('es_exhibition_booking_stalls as BS')
	->result();

?>
<page>
	<style>
		* {
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
			margin: 0;
			padding: 0;
			font-family: Helvetica, Arial, sans-serif;
		}
		page {
			font-family: Helvetica, Arial, sans-serif;
			font-size: 12px;
		}

		.text-right {
			text-align: right;
		}
		.text-left {
			text-align: left;
		}
		.text-center {
			text-align: center;
		}
		.text-muted {
			color: #777;
		}
        .text-justify {
            text-align: justify;
        }

		table {
			border-spacing: 0;
			border-collapse: collapse;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
		}
		table th,
		table td {
			padding: 5px;
			word-break: break-all;
			white-space: normal;
			vertical-align: top;
		}


		table.table-bordered th,
		table.table-bordered td {
			border: 1px solid #ccc;
		}

		.light-bg {
			background: #f9f9f9;
		}
		.dark-bg {
			background: #eee;
		}

		th.bottom-border,
		td.bottom-border {
			border-bottom: 1px solid #ccc;
		}

		.event_logo {
			width: 100px;
		}

		.company_stamp_area {
			width: 100%;
			height: 200px;
			border: 1px solid #ccc;
			margin-bottom: 5px;
		}
	</style>

	<table style="width: 100%">
		<tr>
			<td style="width: 15%;">
				<img src="<?= LOCAL_EXHIBIT_URL . ($this->event->event_logo) ?>" alt="" class="event_logo">
			</td>
			<td style="width: 85%;">
				<p style="font-size: 20px;"><?= $this->event->exhibition_title ?></p>
				<p style="font-size: 14px;"><?= $location->location_title ?></p>
				<p class="text-right text-muted"><?= date('d/m/Y') ?></p>
			</td>
		</tr>
	</table>

	<p style="font-size: 20px;" class="text-center">End User Certificate</p>

	<table class="table-bordered" style="width: 100%; margin-top: 20px;">
		<tr>
			<td style="width: 15%">Company Name:</td>
			<td style="width: 20%"><?= $this->userdata->company ?></td>
			<td style="width: 15%">Company Address:</td>
			<td style="width: 30%"><?= $this->userdata->address ?></td>
			<td style="width: 10%">Country:</td>
			<td style="width: 10%"><?= $this->userdata->country ?></td>
		</tr>
		<tr>
			<td style="width: 15%">Email Address:</td>
			<td style="width: 20%"><?= $this->userdata->email ?></td>
			<td style="width: 15%">Telephone:</td>
			<td style="width: 30%"><?= $this->userdata->phone ?></td>
			<td style="width: 10%">Contact Person:</td>
			<td style="width: 10%"><?= $contact_person_data->person_name ?></td>
		</tr>
		<tr>
			<td style="width: 15%">Hall Number:</td>
			<td style="width: 20%"><?= implode(', ', array_map(function ($h){ return $h->hall_title; }, $halls)) ?></td>
			<td style="width: 15%">Stall Number:</td>
			<td style="width: 50%" colspan="3"><?= implode(', ', array_map(function ($s){ return $s->stall_name; }, $stalls)) ?></td>
		</tr>
	</table>

	<table class="table table-bordered table-striped" style="width: 100%; margin-top: 40px;">
		<thead>
		<tr class="dark-bg">
			<td style="width: 10%">Category</td>
			<td style="width: 18%">Description Of Exhibit</td>
			<td style="width: 7%">Quantity</td>
			<td style="width: 7%">Length</td>
			<td style="width: 7%">Breth</td>
			<td style="width: 7%">Height</td>
			<td style="width: 10%">Package Qty</td>
			<td style="width: 7%">Weight</td>
			<td style="width: 12%">Freight Forwarder</td>
			<td style="width: 15%">Remarks</td>
		</tr>
		</thead>
		<tbody>
		<?php
		if (!is_null($this->formdata)) {
			$even_child = 'light-bg';
			$freight_forwarders = array();
			if (isset($this->event->event_freight_forwarders) && !is_null($this->event->event_freight_forwarders)) {
				$event_freight_forwarders = explode(',', $this->event->event_freight_forwarders);
				foreach ($event_freight_forwarders as $forwarder) {
					$freight_forwarders[$forwarder] = str_replace('_', ' ', $forwarder);
				}
			}
			foreach ($this->formdata->products as $row) {
				$even_child = ($even_child == '') ? 'light-bg' : '';
				echo '<tr class="'.$even_child.'">
				<td style="width: 10%">'. $row->product_category .'</td>
				<td style="width: 18%">'. $row->product_description .'</td>
				<td style="width: 7%">'. $row->product_quantity .'</td>
				<td style="width: 7%">'. $row->package_length .' '. $row->package_size_units .'</td>
				<td style="width: 7%">'. $row->package_breth .' '. $row->package_size_units .'</td>
				<td style="width: 7%">'. $row->package_height .' '. $row->package_size_units .'</td>
				<td style="width: 10%">'. $row->package_quantity .'</td>
				<td style="width: 7%">'. $row->package_weight .' '. $row->package_weight_units .'</td>
				<td style="width: 12%">'. (array_key_exists($row->official_freight_forwarder, $freight_forwarders) ? $freight_forwarders[$row->official_freight_forwarder] : '') .'</td>
				<td style="width: 15%">'. $row->package_remarks .'</td>
				</tr>';
			}
		}
		?>
		</tbody>
	</table>

	<table style="width: 100%; margin-top: 30px" class="text-muted text-justify">
		<tr>
			<td style="width: 70%">
				<p style="margin-bottom: 14px">Exhibitors are required to complete the following declaration:</p>

				<p style="margin-bottom: 14px">DECLARATION We understand that it is a condition of the licensing authority that no weapon, ammunition, explosive or toxic material will be exhibited at our <?= $this->event->exhibition_title ?> stand other than models, dummies, inert ammunition, cutaways and weapons which have been rendered irreversibly unserviceable. We confirm that all our exhibits comply with this rule. Within the consignment of exhibits and materials shipped to Pakistan by us for display <?= $this->event->exhibition_title ?> are the following items, which under the definition stated above can be classified as 'weapons' 'ammunition' or 'projectile', including mines etc. as per category/sub-category</p>

				<p style="margin-bottom: 14px">Note: Dummy or and inert is defined "as any item or armament or any projectile, which contains no explosive, incendiary or toxic material and has been rendered inert to a degree from which it cannot be repaired to become a usable weapon or projectile in a weapon"</p>
			</td>
			<td style="width: 30%">
				<div class="company_stamp_area"></div>
				<p class="text-center">Company Stamp</p>
			</td>
		</tr>
	</table>

	<table  style="width: 100%; margin-top: 30px">
		<tr>
			<td style="width: 40%;" colspan="2">Name Of Aurthorized Signatury: </td>
			<td style="width: 60%" colspan="2" class="bottom-border"></td>
        </tr>
        <tr>
			<td style="width: 10%;padding-top: 50px">Title: </td>
			<td style="width: 30%;padding-top: 50px" class="bottom-border"></td>
			<td style="width: 15%;padding-top: 50px">Signature: </td>
			<td style="width: 45%;padding-top: 50px" class="bottom-border"></td>
		</tr>
	</table>
</page>