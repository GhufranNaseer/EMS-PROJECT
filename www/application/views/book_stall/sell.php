<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<?php

$exhibition_dates = $this->db
    ->where('exhibition_id', $this->formdata->id)
    ->get('es_exhibition_date')
    ->result();

$exhibition_halls = $this->db
    ->select('H.*')
	->where('EH.exhibition_id', $this->formdata->id)
    ->join('es_location_halls as H', 'H.id = EH.hall_id', 'LEFT')
    ->get('es_exhibition_halls as EH')
    ->result();

?>

<style>
    .nav-pills>li.active>a, .nav-pills>li.active>a:hover, .nav-pills>li.active>a:focus {
        border-top-color: transparent;
        background: #db4c3b;
    }
    .nav-pills>li>a {
        padding: 20px;
        position: relative;
        margin: 0;
        border: 0;
    }
    .nav-pills>li:not(:last-child).active>a:before {
        content: '';
        display: block;
        height: 34.5px;
        width: 34.5px;
        border: 18px solid #fff;
        position: absolute;
        top: 0;
        right: 0;
        border-bottom-color: transparent;
        border-left-color: transparent;
    }
    .nav-pills>li:not(:last-child).active>a:after {
        content: '';
        display: block;
        height: 34.5px;
        width: 34.5px;
        border: 18px solid #fff;
        position: absolute;
        bottom: 0;
        right: 0;
        border-top-color: transparent;
        border-left-color: transparent;
    }

    table th,
    table td {
        vertical-align: middle !important;
    }
    table tr.active-dark,
    table th.active-dark,
    table td.active-dark {
        background: #eee;
    }
    table th label,
    table td label {
        margin: 0;
    }

    input[readonly], select[readonly] {
        pointer-events: none;
        cursor: not-allowed;
    }

    #add_stalls_area {
        background: #f3f3f3;
        padding: 5px;
        height: 300px;
        overflow: auto;
        border: 1px solid #ccc;
    }
    #add_stalls_area:after {
        content: '';
        display: block;
        clear: both;
    }
    #add_stalls_area .stall_box {
        float: left;
        width: calc((100% / 6) - 10px);
        background: #fff;
        margin: 5px;
        padding: 5px;
        box-shadow: 0 0 5px #ccc;
        border-radius: 4px;
        cursor: pointer;
    }
    #add_stalls_area .stall_box[data-confirmed="1"],
    #add_stalls_area .stall_box[data-sold="1"] {
        cursor: not-allowed;
    }
    #add_stalls_area .stall_name {
        font-size: 18px;
    }
    #add_stalls_area .stall_size {
        color: #777;
        font-size: 12px;
    }
    #add_stalls_area .stall_status {}
    #add_stalls_area .stall_box.selected {
        background: #3c8dbc;
    }
    #add_stalls_area .stall_box.selected .stall_name,
    #add_stalls_area .stall_box.selected .stall_size,
    #add_stalls_area .stall_box.selected .stall_hall,
    #add_stalls_area .stall_box.selected .stall_price_view {
        color: #fff;
    }

    #add_stalls_package_details .form-control {
        max-width: 100px;
    }
    #order_items_list .disable_package_item td:first-child,
    #add_badges_list .disable_package_item td:first-child {
        text-decoration: line-through;
    }
</style>

<div class="content-wrapper" data-page="book_stall">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Order Booking
            <small>Form</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Booking</li>
        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Order Booking Form</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr>
                                <td width="15%" rowspan="4" class="text-center" style="vertical-align: middle">
                                    <img src="<?= base_url($this->formdata->event_logo) ?>" alt="" class="img-thumbnail" style="width: 100px">
                                </td>
                                <th><?= $this->formdata->exhibition_title ?></th>
                            </tr>
                            <tr>
                                <td><strong>Start Date:</strong> <?= date('d/m/Y', strtotime($exhibition_dates[0]->date)) ?> / <strong>End Date:</strong> <?= date('d/m/Y', strtotime(end($exhibition_dates)->date)) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Location:</strong> <?= $this->db->where('id', $this->formdata->location_id)->get('es_locations')->row()->location_title ?></td>
                            </tr>
                            <tr>
                                <td><strong>Venue Plan:</strong>
                                    <?php
                                    foreach ($exhibition_halls as $exhibition_hall) {
                                        echo '<span class="label label-default" style="margin-right: 4px">'.$exhibition_hall->hall_title.'</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>

                        <div class="row form-group">
                            <div class="col-xs-12">
                                <ul class="nav nav-pills nav-justified thumbnail setup-panel">
                                    <li class="active" data-step="1">
                                        <a href="#step-1">
                                            <h4 class="list-group-item-heading">Customer Information</h4>
                                        </a>
                                    </li>
                                    <li class="" data-step="2">
                                        <a href="#step-2">
                                            <h4 class="list-group-item-heading">Order Information</h4>
                                        </a>
                                    </li>
                                    <li class="disabled" data-step="3">
                                        <a href="#step-3">
                                            <h4 class="list-group-item-heading">Order Items List</h4>
                                        </a>
                                    </li>
                                    <li class="disabled" data-step="4">
                                        <a href="#step-4">
                                            <h4 class="list-group-item-heading">Sale Order</h4>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="setup-content" id="step-1">
                            <form action="#" method="post" id="exhibitor_form" enctype="multipart/form-data">

                                <table class="table table-bordered table-condensed">
                                    <tr>
                                        <th width="50%" colspan="3">Select Customer Information</th>
                                        <td width="16.66%" class="text-right">
                                            <button type="button" class="btn btn-primary btn-sm add_customer_btn">Add New Customer</button>
                                            <button type="button" class="btn btn-primary btn-sm add_contact_person_btn">Add Person</button>
                                        </td>
                                        <th width="16.66%">Order Date <span class="text-red">*</span></th>
                                        <th width="16.66%">Payment Mode <span class="text-red">*</span></th>
                                    </tr>
                                    <tr>
                                        <th>Company Name <span class="text-red">*</span></th>
                                        <td>
                                            <select name="customer_id" class="form-control customer_id"></select>
                                        </td>
                                        <th>Contact Person <span class="text-red">*</span></th>
                                        <td>
                                            <select name="customer_contact_person_id" class="form-control customer_contact_person_id"></select>
                                        </td>
                                        <td><input type="text" class="form-control order_date" name="order_date" value="<?= date('m/d/Y') ?>"></td>
                                        <td>
                                            <select name="booking_price_type" class="form-control booking_price_type">
                                                <option value="USD" <?= ($this->formdata->price_type == 'USD') ? 'selected' : '' ?>>USD</option>
                                                <option value="PKR" <?= ($this->formdata->price_type == 'PKR') ? 'selected' : '' ?>>PKR</option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="6">&nbsp;</td>
                                    </tr>

                                    <tr>
                                        <td colspan="2" rowspan="3" id="contact_person_address_area" class="active" style="vertical-align: top !important;">
                                            [Contact Person Name]<br>
                                            [Contact Person Address]<br>
                                            [Contact Person Phone]
                                        </td>
                                        <td colspan="2" rowspan="3" id="company_address_area" class="active" style="vertical-align: top !important;">
                                            [Company Name]<br>
                                            [Company Address]<br>
                                            [Company Phone]
                                        </td>
                                        <th class="active-dark" colspan="2">
                                            <label><input type="checkbox" name="update_contact_person_info"> Update Contact Information</label>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="active-dark">Email Address</th>
                                        <th class="active-dark">Cell #</th>
                                    </tr>
                                    <tr>
                                        <td class="active-dark">
                                            <input type="text" class="form-control" name="update_contact_person_email" disabled>
                                        </td>
                                        <td class="active-dark">
                                            <input type="number" class="form-control" name="update_contact_person_cell" disabled>
                                        </td>
                                    </tr>
                                </table>

                                <table class="table table-bordered table-condensed">
                                    <tr>
                                        <th width="50%">Billing Address</th>
                                        <th width="50%">
                                            <label><input type="checkbox" name="has_new_billing_address"> New Billing Address</label>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td class="active" id="company_billing_address" style="vertical-align: top !important;">
                                            [Customer Billing Address]
                                        </td>
                                        <td class="active">
                                            <textarea name="new_billing_address" class="form-control" rows="3" placeholder="[Input New Address]" disabled></textarea>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="2">&nbsp;</td>
                                    </tr>

                                    <tr>
                                        <th>Sales Person</th>
                                        <th>
                                            <label><input type="checkbox" name="has_sales_agent"> Sales Confirmation by Agent</label>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td class="active"><?= $this->userdata->user_first_name . ' ' . $this->userdata->user_last_name ?></td>
                                        <td class="active">
                                            <select name="sales_agent_id" class="form-control" disabled>
                                                <option value="">- select -</option>
                                                <?php
                                                $agents = $this->db
                                                    ->where('is_deleted', 0)
                                                    ->get('es_agent')
                                                    ->result();

                                                foreach ($agents as $agent) {
                                                    echo '<option value="'.$agent->id.'">'.$agent->agent_company.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                </table>


                                <div class="text-right">
                                    <button type="button" class="btn btn-success btn-lg exhibitor_form_validate">Next</button>
                                </div>
                            </form>
                        </div>

                        <div class="setup-content" id="step-2">
                            <form action="#" method="post" id="stalls_form" enctype="multipart/form-data">

                                <table class="table table-bordered">
                                    <tr>
                                        <th>Booking Type</th>
                                        <td>
                                            <select class="form-control add_stall_type" name="">
                                                <option value="shell">Shell</option>
                                                <option value="bare">Bare</option>
                                            </select>
                                        </td>
                                        <th>Select Package</th>
                                        <td>
                                            <select class="form-control add_stall_package" name="">
                                                <option value="">- select -</option>
                                            </select>
                                        </td>
                                        <th>Select Hall</th>
                                        <td>
                                            <select class="form-control add_stall_hall" name="">
                                                <option value="">- select -</option>
												<?php
												$halls = $this->db
													->select('E.*, H.hall_title')
													->where('E.exhibition_id', $this->formdata->id)
													->join('es_location_halls as H', 'H.id = E.hall_id', 'LEFT')
													->get('es_exhibition_halls as E')
													->result();

												foreach ($halls as $hall) {
													echo '<option value="'.$hall->hall_id.'">'.$hall->hall_title.'</option>';
												}
												?>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <div class="row form-group">
                                    <div class="col-sm-12">
                                        <label>Search Stall</label>
                                        <input type="text" class="form-control stall_filter" placeholder="Search...">
                                        <div id="add_stalls_area"></div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12 text-right">
                                        <button type="button" class="btn btn-primary add_order_btn">Add Order</button>
                                    </div>
                                </div>

                                <hr>

                                <h4>Stall Booking Description</h4>

                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Booking Type</th>
                                        <th>Package</th>
                                        <th>Location</th>
                                        <th>Stall</th>
                                        <th>Qty.</th>
                                        <th>Space (Sq.m)</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="selected_stalls_table"></tbody>
                                </table>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th class="text-right">Total Stall Booking Value</th>
                                        <th class="text-right order_stall_price_type" width="5%">PKR</th>
                                        <th width="20%"><input type="text" class="form-control order_stall_total_amount" name="order_stall_total_amount" readonly></th>
                                    </tr>
                                </table>


                                <div class="text-right">
                                    <button type="button" class="btn btn-success btn-lg stall_form_validate_btn">Next</button>
                                </div>
                            </form>
                        </div>

                        <div class="setup-content" id="step-3">
                            <form action="#" method="post" id="order_item_list_form" enctype="multipart/form-data">

                                <h4>Order Item Description</h4>

                                <div class="form-group">
                                    <button type="button" class="btn btn-primary add_extra_item_btn">Add Additional Item</button>
                                </div>

                                <table class="table table-bordered table-striped table-condensed">
                                    <thead>
                                    <tr>
                                        <th width="30%">Item Name</th>
                                        <th width="30%">Order Type</th>
                                        <th width="15%">Qty.</th>
                                        <th width="15%">Price</th>
                                        <th width="10%">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="order_items_list"></tbody>
                                </table>

                                <table class="table table-bordered">
                                    <tr>
                                        <th class="text-right">Total Booking Item Value</th>
                                        <th class="text-right extra_items_price_type" width="5%">PKR</th>
                                        <th width="20%" class="text-right extra_items_total_amount">0.00</th>
                                    </tr>
                                </table>

                                <div class="text-right">
                                    <button type="button" class="btn btn-success btn-lg order_item_list_validate_btn">Next</button>
                                </div>
                            </form>
                        </div>

                        <div class="setup-content" id="step-4">
                            <form action="#" method="post" id="confirm_form" enctype="multipart/form-data">

                                <table class="table table-bordered">
                                    <tr>
                                        <th width="16.66%">Order Status</th>
                                        <td width="16.66%">
                                            <select name="booking_type" class="form-control booking_type">
                                                <option value="tentative">Tentative</option>
                                                <option value="confirmed">Booked</option>
                                            </select>
                                        </td>
                                        <th width="16.66%">Apply Discount Offer Price</th>
                                        <td width="16.66%">
                                            <select name="booking_offer_discount" class="form-control booking_offer_discount">
                                                <option value="">- None -</option>
												<?php
												$offers = $this->db
													->where('is_deleted', 0)
													->get('es_discount_offers')
													->result();
												foreach ($offers as $offer) {
													echo '<option value="'.$offer->id.'">'.$offer->offer_title.'</option>';
												}
												?>
                                            </select>
                                        </td>
                                        <th width="16.66%">Tax</th>
                                        <td width="16.66%">
                                            <select name="booking_tax" class="form-control booking_tax">
                                                <option value="">- select -</option>
                                                <?php
                                                $taxes = $this->db
													->where('is_deleted', 0)
													->where('tax_year', date('Y'))
													->get('es_tax_rate')
													->result();
                                                foreach ($taxes as $tax) {
                                                    echo '<option value="'.$tax->id.'">'.$tax->tax_name.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <h4>Complete Order Booking Payment Details </h4>
                                <div id="invoice_area"></div>

                                <hr>
                                <label>
                                    <h4>
                                        <input type="checkbox" name="has_additional_discount" class="has_additional_discount"> Additional Discount
                                    </h4>
                                </label>
                                <div id="discount_area" style="display: none">
                                    <table class="table table-bordered">
                                        <tr class="bg-primary">
                                            <th width="20%">Sales Person</th>
                                            <th width="20%">Select Type <span class="text-red">*</span></th>
                                            <th width="40%">Remarks <span class="text-red">*</span></th>
                                            <th width="20%">Discount Amount <span class="text-red">*</span></th>
                                        </tr>
                                        <tr>
                                            <td class="active"><?= $this->userdata->user_first_name ?></td>
                                            <td style="vertical-align: top !important;">
                                                <select name="additional_discount_type" class="form-control">
                                                    <option value="">- select -</option>
                                                    <option value="package_discount">Package Discount</option>
                                                    <option value="package_additional_item_discount">Package + Additional Item Discount</option>
                                                    <option value="additional_item_discount">Only Additional Item</option>
                                                    <option value="other_discount">Others Discount</option>
                                                </select>
                                            </td>
                                            <td style="vertical-align: top !important;">
                                                <textarea name="discount_remarks" class="form-control" rows="3"></textarea>
                                            </td>
                                            <td style="vertical-align: top !important;">
                                                <input type="number" class="form-control" name="discount_amount">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-right">
                                                <button type="button" class="btn btn-primary apply_discount_btn">Apply Update</button>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="text-right">
                                    <button type="button" class="btn btn-success btn-lg confirm_form_validate_btn">Save & Finish</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>
</div>


<!-- Lightbox for  -->
<div class="modal fade" id="add_customer_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Add New Customer</h4>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('customer-submit.html') ?>" method="post" id="add_customer_form"
                      enctype="multipart/form-data">
                    <input type="hidden" name="back_url" value="book-stall.html?id=<?= $this->input->get('id') ?>">
				<?php $this->load->view('customer/_add_form'); ?>
                </form>
            </div>
        </div>
    </div>
</div> <!-- /.modal -->

<!-- Lightbox for  -->
<div class="modal fade" id="add_contact_person_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Add New Contact Person</h4>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('contact_person-submit.html') ?>" method="post" id="add_contact_person_form"
                      enctype="multipart/form-data">
                    <input type="hidden" name="back_url" value="book-stall.html?id=<?= $this->input->get('id') ?>">
				<?php $this->load->view('contact_person/_add_form'); ?>
                </form>
            </div>
        </div>
    </div>
</div> <!-- /.modal -->

<!-- Lightbox for  -->
<div class="modal fade" id="extra_items_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Additional Items</h4>
            </div>
            <div class="modal-body" id="extra_items_area">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success add_additional_item_btn">Add Item</button>
            </div>
        </div>
    </div>
</div> <!-- /.modal -->


<?php $this->load->view('includes/after_login/footer'); ?>

<link rel="stylesheet" href="<?= base_url('assets') ?>/iCheck/square/grey.css">
<script src="<?= base_url('assets') ?>/iCheck/icheck.min.js"></script>


<script>
	doFormValidation({
		'form':         '#add_customer_form',
		'msgbox':       '#add_customer_form .js-msgbox',
		'btnClick':     '#add_customer_form .js-form_btn',
		'urlValidator': "<?php echo base_url("customer-validate.html"); ?>",
		'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
	});
	doFormValidation({
		'form':         '#add_contact_person_form',
		'msgbox':       '#add_contact_person_form .js-msgbox',
		'btnClick':     '#add_contact_person_form .js-form_btn',
		'urlValidator': "<?php echo base_url("contact_person-validate.html"); ?>",
		'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
	});
	/*window.onbeforeunload = function() {
		return 'Changes you made may not be saved.';
	}*/
	/* START Steps */
	var navListItems = $('ul.setup-panel li a'),
		allWells = $('.setup-content');

	allWells.hide();

	navListItems.click(function(e) {
		e.preventDefault();
		var $target = $($(this).attr('href')),
			$item = $(this).closest('li');

		if (!$item.hasClass('disabled')) {
			navListItems.closest('li').removeClass('active');
			$item.addClass('active');
			allWells.hide();
			$target.show();
		}
	});

	$('ul.setup-panel li.active a').trigger('click');
	/* END Steps */

	function show_validation_error(data) {
		if (data.indexOf("*") != -1) {

			$(".validation-failed").removeClass("validation-failed");

			var serverresponse = data.split("*");

			var resp_field_name = serverresponse[0];
			var msg_start = '';
			if (serverresponse[0].indexOf(' ') != -1) {
				var str = serverresponse[0];

				var rest = str.substring(0, str.lastIndexOf(' ') + 1);
				var last = str.substring(str.lastIndexOf(' ') + 1, str.length);

				msg_start = rest + ' ';
				resp_field_name = last;
			}
			$("[name='" + resp_field_name + "']").addClass("validation-failed");
			fieldname__ = serverresponse[0];
			if (typeof swal === 'function') {
				swal({
					title: '',
					text: msg_start + serverresponse[1],
					type: "warning"
				}, function () {
					setTimeout(function () {
						$(window).scrollTop($("[name='" + resp_field_name + "']").offset().top - 100);
						$("[name='" + resp_field_name + "']").focus();
					}, 300)
				});
			} else {
				//$(obj.msgbox).html(msg_start + serverresponse[1]);
			}
		}
		else {
			if (typeof swal === 'function') {
				//$(obj.msgbox).html('');
				swal({
					title: '',
					text: data,
					type: "warning"
				});
			} else {
				//$(obj.msgbox).html(data);
			}
		}
	}


	function validate_exhibitor(callback) {
		$.ajax({
			type:    'post',
			url:     '<?= base_url("book_stall/book_customer_validate/doError"); ?>?id=<?= $this->input->get('id') ?>',
			data:    $('#exhibitor_form').serialize(),
			success: function (data) {
				console.log(data);
				$(".validation-failed").removeClass("validation-failed");

				if (data != "done") {
					allWells.hide();
					$('ul.setup-panel li[data-step="1"] a').trigger('click');
					show_validation_error(data);
				} else {
					if (callback) callback();
				}
			},
			error:   function () {
			}
		});
    }

    function validate_stalls(callback) {
		$.ajax({
			type:    'post',
			url:     '<?= base_url("book_stall/book_stalls_validate/doError"); ?>?id=<?= $this->input->get('id') ?>',
			data:    $('#stalls_form').serialize(),
			success: function (data) {
				console.log(data);
				$(".validation-failed").removeClass("validation-failed");

				if (data != "done") {
					allWells.hide();
					$('ul.setup-panel li[data-step="2"] a').trigger('click');
					show_validation_error(data);
				} else {
					if (callback) callback();
				}
			},
			error:   function () {
			}
		});
    }

    function validate_order_item_list(callback) {
		$.ajax({
			type:    'post',
			url:     '<?= base_url("book_stall/order_item_list_validate/doError"); ?>?id=<?= $this->input->get('id') ?>',
			data:    $('#order_item_list_form').serialize(),
			success: function (data) {
				console.log(data);
				$(".validation-failed").removeClass("validation-failed");

				if (data != "done") {
					allWells.hide();
					$('ul.setup-panel li[data-step="3"] a').trigger('click');
					show_validation_error(data);
				} else {
					if (callback) callback();
				}
			},
			error:   function () {
			}
		});
    }

    function validate_confirm_booking(callback) {
		$.ajax({
			type:    'post',
			url:     '<?= base_url("book_stall/book_confirm_validate/doError"); ?>?id=<?= $this->input->get('id') ?>',
			data:    $('#confirm_form').serialize(),
			success: function (data) {
				console.log(data);
				$(".validation-failed").removeClass("validation-failed");

				if (data != "done") {
					allWells.hide();
					$('ul.setup-panel li[data-step="4"] a').trigger('click');
					show_validation_error(data);
				} else {
					if (callback) callback();
				}
			},
			error:   function () {
			}
		});
    }

    function submit_booking() {
		$('.confirm_form_validate_btn').attr('disabled', true);
		var form_data = $('#exhibitor_form').serialize();
		form_data += '&' + $('#stalls_form').serialize();
		form_data += '&' + $('#order_item_list_form').serialize();
		form_data += '&' + $('#confirm_form').serialize();
		$.ajax({
			type:    'post',
			url:     '<?= base_url("book-stall-submit.html"); ?>?id=<?= $this->input->get('id') ?>',
			data:    form_data,
			success: function (data) {
				$('.confirm_form_validate_btn').attr('disabled', false);
				console.log(data);
				data = JSON.parse(data);

				if (data.error && data.error == 1) {
					alert(data.message);
					return;
				}

				window.onbeforeunload = null;
				window.location = data.data;
			},
			error:   function () {
				$('.confirm_form_validate_btn').attr('disabled', false);
			}
		});
    }


	$(document).on('click', '.exhibitor_form_validate', function (e) {
		e.stopImmediatePropagation();

		validate_exhibitor(function() {
			allWells.hide();
			$('ul.setup-panel li[data-step="2"] a').trigger('click');
        })
	});

	$(document).on('click', '.stall_form_validate_btn', function (e) {
		e.stopImmediatePropagation();

		validate_stalls(function() {

			render_package_items_list(function () {
				allWells.hide();
				$('ul.setup-panel li[data-step="3"]').removeClass('disabled');
				$('ul.setup-panel li[data-step="3"] a').trigger('click');
			});

        })
	});

	$(document).on('click', '.order_item_list_validate_btn', function (e) {
		e.stopImmediatePropagation();

		validate_order_item_list(function() {
			allWells.hide();
			$('ul.setup-panel li[data-step="4"]').removeClass('disabled');
			$('ul.setup-panel li[data-step="4"] a').trigger('click');

			render_invoice();
        })
	});

	$(document).on('click', '.confirm_form_validate_btn', function (e) {
		e.stopImmediatePropagation();

		validate_confirm_booking(function() {
			validate_exhibitor(function() {
				validate_stalls(function() {
					validate_order_item_list(function() {
						console.log('everything validate');
						submit_booking();
                    })
                })
            })
        })
	});



	/* STEP 1 */

	$(document).on('click', '.add_customer_btn', function (e) {
		e.stopImmediatePropagation();

		$('#add_customer_modal').modal({backdrop: 'static', keyboard: false, show: true}); // open lightbox
	});

	$(document).on('click', '.add_contact_person_btn', function (e) {
		e.stopImmediatePropagation();

		$('#add_contact_person_modal').modal({backdrop: 'static', keyboard: false, show: true}); // open lightbox
	});

	$(document).on('change', '.booking_price_type', function (e) {
		e.stopImmediatePropagation();

		var is_pkr = ($(this).val() == 'PKR');
		var h =  '';
		$('#add_stalls_area .stall_box').each(function () {
			h = (is_pkr) ? ('PKR ' + $(this).attr('data-price-pkr')) : ('USD ' + $(this).attr('data-price-usd'));
			$(this).find('.stall_price_view').html(h)
		})
	});

	$(document).on('change', '#exhibitor_form .customer_id', function (e) {
		e.stopImmediatePropagation();

		var v = $(this).val();
		var text = $(this).find('option[value="'+v+'"]').text();

		swal({
			title: '',
			text: 'Are you sure you want to load data from '+text+'?',
			type: "warning",
			showCancelButton: true,
			confirmButtonText: "Yes",
		}, function () {
			load_customer_contact_person(v);
			load_exhibitor_data(v);
		});
	});

	$(document).on('change', '#exhibitor_form .customer_contact_person_id', function (e) {
		e.stopImmediatePropagation();

        var v = $(this).val();
		console.log('vvv', v);
        var data = {};
        for (var i=0; i<window.all_contact_person.length; i++) {
           if (window.all_contact_person[i].id == v) {
               data = window.all_contact_person[i];
           }
        }
		console.log(data);
        var html = data.person_name + '<br>';
		html += data.primary_email + '<br>';
		html += 'Cell # ' + data.primary_phone;

		$('#contact_person_address_area').html(html)
	});

	function load_exhibitor_data(customer_id) {
		$.ajax({
			type:    'post',
			url:     '<?= base_url('my_funcs/get_customer_data') ?>',
			data:    {customer_id: customer_id},
			success: function (data) {
				data = JSON.parse(data);

				if (data.error) {
					return;
                }

                var html = data.data.company + '<br>';
                html += data.data.email + '<br>';
                html += 'Tel # ' + data.data.phone;
                if (data.data.fax && data.data.fax != '') {
					html += 'Fax # ' + data.data.fax;
                }

                $('#company_address_area').html(html);
                $('#contact_person_address_area').html('[Contact Person Name]<br>[Contact Person Address]<br>[Contact Person Phone]');
                $('#company_billing_address').html(data.data.address.replace(/(?:\r\n|\r|\n)/g, '<br>'));
			},
			error:   function () {
			}
		});
	}

	function load_customers() {
		$('.customer_id').html('<option value="">Loading...</option>');
		$.ajax({
			type:    'post',
			url:     '<?= base_url('my_funcs/get_all_customers') ?>',
			data:    {},
			success: function (data) {
				data = JSON.parse(data);

				if (data.error) {
					alert(data.message);
					return;
				}
				$('.customer_id').html('<option value="">- select -</option>');
				for (var i=0; i<data.data.length; i++) {
					$('.customer_id').append('<option value="'+data.data[i].id+'">'+data.data[i].company+'</option>');
                }

			},
			error:   function () {

			}
		});
    }

	function load_customer_contact_person(customer_id) {
		$('.customer_contact_person_id').html('<option value="">Loading...</option>');
		$.ajax({
			type:    'post',
			url:     '<?= base_url('my_funcs/get_customer_contact_person') ?>',
			data:    {customer_id: customer_id},
			success: function (data) {
				data = JSON.parse(data);

				if (data.error) {
					$('.customer_contact_person_id').html('<option value="">'+data.message+'</option>');
					return;
				}

				window.all_contact_person = data.data;
				$('.customer_contact_person_id').html('<option value="">- select -</option>');
				for (var i=0; i<data.data.length; i++) {
					$('.customer_contact_person_id').append('<option value="'+data.data[i].id+'">'+data.data[i].person_name+'</option>');
                }

			},
			error:   function () {

			}
		});
    }

	$(document).on('change', '[name="update_contact_person_info"]', function (e) {
		e.stopImmediatePropagation();

		$('[name="update_contact_person_email"]').attr('disabled', !($(this).is(':checked')));
		$('[name="update_contact_person_cell"]').attr('disabled', !($(this).is(':checked')));
	});

	$(document).on('change', '[name="has_new_billing_address"]', function (e) {
		e.stopImmediatePropagation();

		$('[name="new_billing_address"]').attr('disabled', !($(this).is(':checked')));
	});

	$(document).on('change', '[name="has_sales_agent"]', function (e) {
		e.stopImmediatePropagation();

		$('[name="sales_agent_id"]').attr('disabled', !($(this).is(':checked')));
	});

	$('.order_date').datepicker({
		startDate: new Date()
	});
	load_customers();
    /* END STEP 1 */


    /* START STEP 2 */
	$(document).on('change', '.add_stall_hall', function (e) {
		e.stopImmediatePropagation();

		var h = $(this).val();
		var hall_name = $(this).find('option[value="'+h+'"]').text();

		$('#add_stalls_area').html('');
		if (!h || h == '') {
			return;
		}

		$.ajax({
			type:    'post',
			url:     '<?= base_url('book_stall/ajax_get_hall_stalls') ?>',
			data:    {
				hall_id: h,
				exhibition_id: <?= $this->formdata->id ?>
			},
			success: function (data) {
				data = JSON.parse(data);
				console.log(data);
				if (data.error && data.error == 1) {
					$('#add_stalls_area').html('<div class="text-danger">'+data.message+'</div>');
					return;
				}

				// get selected stalls
                var selected_stalls = [];
                $('#selected_stalls_table tr').each(function () {
                    var ids = $(this).find('.stall_ids').val();

					selected_stalls = selected_stalls.concat(ids.split(','));
				})


				var html = '';
				var stall = data.data;
				var stall_status = '';
				var stall_price = '';
				for(var i=0; i<stall.length; i++) {
					if (selected_stalls.indexOf(stall[i].id) >= 0) continue;

					stall_status = '<span class="label label-success">Available</span>';
					if (stall[i].is_sold == 1) {
						stall_status = '<span class="label label-danger">Booked</span>'
                    } else if (stall[i].is_confirmed == 1) {
						stall_status = '<span class="label label-danger">Confirmed</span>'
                    } else if (stall[i].is_booked == 1) {
						stall_status = '<span class="label label-warning">Tentative</span>'
                    }

					stall_price = ($('.booking_price_type').val() == 'PKR') ? ('PKR ' + stall[i].stall_price_pkr): ('USD ' + stall[i].stall_price_usd);

					html = '<div class="stall_box" data-id="'+stall[i].id+'" data-booked="'+stall[i].is_booked+'" data-confirmed="'+stall[i].is_confirmed+'" data-sold="'+stall[i].is_sold+'" data-price-pkr="'+stall[i].stall_price_pkr+'" data-price-usd="'+stall[i].stall_price_usd+'">\
                        <div class="stall_name">'+stall[i].stall_name+'</div>\
                        <div class="stall_size">'+stall[i].stall_size+' '+stall[i].stall_size_unit+'</div>\
                        <div class="stall_hall">'+hall_name+'</div>\
						<div class="stall_price_view">'+stall_price+'</div>\
                        <div class="stall_status">'+stall_status+'</div>\
                        </div>';
					$('#add_stalls_area').append(html);
				}
			},
			error:   function () {
			}
		});
	});

	function load_packages(package_type) {
		package_type = package_type || 'shell';

		$('.add_stall_package').html('<option value="">- select -</option>');

		$.ajax({
			type:    'post',
			url:     '<?= base_url('book_stall/ajax_get_packages') ?>',
			data:    {package_type: package_type, exhibition_id: <?= $this->formdata->id ?>},
			success: function (data) {
				data = JSON.parse(data);
				console.log(data);
				if (data.error && data.error == 1) {
					$('.add_stall_package').html('<option value="">'+data.message+'</option>');
					return;
				}

				var items = data.data;
				for (var i=0; i<items.length; i++) {
					$('.add_stall_package').append('<option value="'+items[i].id+'" data-price-usd="'+items[i].package_price_usd+'" data-price-pkr="'+items[i].package_price_pkr+'">' + items[i].package_title + '</option>');
				}
				$('#add_stalls_package_details').html('');
			},
			error:   function () {
			}
		});
	}

	$(document).on('change', '.add_stall_type', function (e) {
		e.stopImmediatePropagation();

		if ($(this).val() != '') {
			load_packages($(this).val());
		}
	});

	$(document).on('keyup', '.stall_filter', function (e) {
		e.stopImmediatePropagation();

		var v = $(this).val();
		$('#add_stalls_area .stall_box:not(.selected)').hide();
		$('#add_stalls_area .stall_name:contains('+v+')').parents('.stall_box').show();
	});

	$(document).on('click', '#add_stalls_area .stall_box', function (e) {
		e.stopImmediatePropagation();

		if ($(this).attr('data-confirmed') == 1 || $(this).attr('data-sold') == 1) {
			return;
		}

		if ($(this).hasClass('selected')) {
			$(this).removeClass('selected');
		} else {
			$(this).addClass('selected');
		}
	});

	$(document).on('click', '#stalls_form .add_order_btn', function (e) {
		e.stopImmediatePropagation();

		var stall_type = $('#stalls_form .add_stall_type').val();
		var package_id = $('#stalls_form .add_stall_package').val();
		var hall_id = $('#stalls_form .add_stall_hall').val();

		if (package_id == '') {
			alert('Please select package!');
			return;
        }

		if (hall_id == '') {
			alert('Please select hall!');
			return;
        }

		if ($('#add_stalls_area .stall_box.selected').length == 0) {
			alert('Please select atleast one stall!');
			return;
        }

        $('#stalls_form .add_order_btn').attr('disabled', true);
        $('.booking_price_type').attr('readonly', true); // lock payment method so user cannot change it

        var stalls = [];
		$('#add_stalls_area .stall_box.selected').each(function (e) {
			var id = $(this).attr('data-id');
			var price = ($('.booking_price_type').val() == 'PKR') ? $(this).attr('data-price-pkr') : $(this).attr('data-price-usd');

			stalls.push({
                id: id,
                name: $(this).find('.stall_name').text(),
				stall_size: $(this).find('.stall_size').text(),
                price: price
            });
		});

		var form_data = {
			stall_type: stall_type,
			package_id: package_id,
			hall_id: hall_id,
			stalls: stalls,
		};

		$.ajax({
			type:    'post',
			url:     '<?= base_url("book_stall/validate_add_stall/doError"); ?>?id=<?= $this->input->get('id') ?>',
			data:    form_data,
			success: function (data) {
				console.log(data);
				$(".validation-failed").removeClass("validation-failed");

				if (data != "done") {
					allWells.hide();
					$('ul.setup-panel li[data-step="2"] a').trigger('click');
					show_validation_error(data);
				} else {
					add_stalls_in_order(form_data);
				}
			},
			error:   function () {
			}
		});

	});

	$(document).on('click', '.remove_from_order', function (e) {
		e.stopImmediatePropagation();

		$(this).parents('tr').remove();

		var total = 0;
		$('#selected_stalls_table > tr').each(function () {
			var p = $(this).find('.temp_price').val();
			total += parseInt(p);
		})
		$('.order_stall_total_amount').val(total);
	});

	function add_stalls_in_order(data) {
		console.log('dd', data);
		var uid = guid();
		var package_name = $('#stalls_form .add_stall_package').find('option[value="'+ data.package_id +'"]').text();
		var hall_name = $('#stalls_form .add_stall_hall').find('option[value="'+ data.hall_id +'"]').text();

		var stall_ids = [];
		var stall_names = [];
		var stall_space = 0;
		var total_price = 0;
		for (var i=0; i<data.stalls.length; i++) {
			stall_ids.push(data.stalls[i].id);
			stall_names.push(data.stalls[i].name);
			stall_space += parseInt(data.stalls[i].stall_size.split(' ')[0]);
			total_price += parseInt(data.stalls[i].price);
        }

		var html = '<tr>';
		html += '<td>'+data.stall_type.toUpperCase()+'<input type="hidden" name="stalls['+uid+'][type]" value="'+data.stall_type+'"></td>';
		html += '<td>'+package_name+'<input type="hidden" name="stalls['+uid+'][package_id]" value="'+data.package_id+'"></td>';
		html += '<td>'+hall_name+'<input type="hidden" name="stalls['+uid+'][hall_id]" value="'+data.hall_id+'"></td>';
		html += '<td>'+stall_names.join()+'<input type="hidden" class="stall_ids" name="stalls['+uid+'][stall_ids]" value="'+stall_ids.join()+'"></td>';
		html += '<td>'+data.stalls.length+'</td>';
		html += '<td>'+stall_space+'</td>';
		html += '<td>'+total_price+'<input type="hidden" name="stalls['+uid+'][temp_price]" class="temp_price" value="'+total_price+'"></td>';
		html += '<td><button type="button" class="btn btn-danger btn-xs remove_from_order"><i class="fa fa-times"></i></button></td>';
		html += '</tr>';

		$('#selected_stalls_table').append(html);

		var t = $('.order_stall_total_amount').val();
		t = (t && t != '') ? parseInt(t) : 0;
		$('.order_stall_total_amount').val(t + total_price);
		$('.order_stall_price_type').text($('.booking_price_type').val());

		// reset form
		$('#stalls_form .add_order_btn').attr('disabled', false);
		$('#stalls_form .add_stall_package').val('');
		$('#stalls_form .add_stall_hall').val('');
		$('#add_stalls_area').html('');
	}
	
	load_packages();
    /* END STEP 2 */


    /* START STEP 3 */
    function render_package_items_list(callback) {
		$('#order_items_list').html('');

		$.ajax({
			type:    'post',
			url:     '<?= base_url("book_stall/ajax_get_package_items"); ?>?id=<?= $this->input->get('id') ?>',
			data:    $('#stalls_form').serialize(),
			success: function (data) {
				console.log(data);
				data = JSON.parse(data);

				if (data.error && data.error == 1) {
					alert(data.message);
					return;
				}
				$('#order_items_list').html('');
				var items = data.data;
				var html = '';
				var uid = 0;
				for (var i=0; i<items.length; i++) {
					uid = guid();
                    html = '<tr data-qty="'+items[i].initial_allow+'">';
                    html += '<td>'+items[i].item_title+'</td>';
                    html += '<td>'+items[i].package_title+'</td>';
                    html += '<td>\
                        <input type="hidden" name="order_items['+uid+'][is_package_item]" value="1">\
                        <input type="hidden" name="order_items['+uid+'][package_id]" value="'+items[i].package_id+'">\
                        <input type="hidden" name="order_items['+uid+'][item_id]" value="'+items[i].item_id+'">\
                        <input type="hidden" name="order_items['+uid+'][total_stalls]" value="'+items[i].total_stalls+'">\
                        <input type="number" class="form-control input-sm qty" name="order_items['+uid+'][quantity]" value="'+items[i].initial_allow+'" max="'+items[i].initial_allow+'">\
                        </td>';
                    html += '<td class="text-right">0.00</td>';
                    html += '<td class="text-right"><input type="checkbox" class="package_item" name="order_items['+uid+'][has_item]" checked></td>';
                    html += '</tr>';

					$('#order_items_list').append(html);
                }

				if (callback) callback();
			},
			error:   function () {
			}
		});
	}
	// disable package items
	$(document).on('change', '.package_item', function (e) {
		e.stopImmediatePropagation();

		if ($(this).is(':checked')) {
			$(this).parents('tr').removeClass('disable_package_item');
			$(this).parents('tr').find('.qty').attr('readonly', false);
		} else {
			$(this).parents('tr').addClass('disable_package_item');
			$(this).parents('tr').find('.qty').attr('readonly', true);
		}
	});

	// START Extra Items
	$(document).on('click', '.add_extra_item_btn', function (e) {
		e.stopImmediatePropagation();

		var $this = $(this);
		$this.attr('disabled', true);
		$.ajax({
			type:    'post',
			url:     '<?= base_url('book_stall/ajax_get_inventory_category') ?>',
			data:    {},
			success: function (data) {
				$this.attr('disabled', false);
				console.log(data);
				data = JSON.parse(data);

				if (data.error && data.error == 1) {
					alert(data.message);
					return;
				}

				var html = '<div class="form-group row">\
                <div class="col-sm-4">\
                <label>Item Category</label>\
                <select class="form-control extra_category">\
                <option value="">- Select Category -</option>';
				for (var i=0; i<data.data.length; i++) {
					html += '<option value="'+data.data[i].id+'">'+data.data[i].category_title+'</option>';
				}
				html += '</select>\
                </div>\
                <div class="col-sm-4">\
                <label>Select Item</label>\
                <select class="form-control extra_item">\
                <option value="">- Select Item -</option>\
                </select>\
                </div>\
                <div class="col-sm-4">\
                <label>Item Quantity</label>\
                <input type="number" class="form-control extra_quantity" placeholder="Quantity" value="1"></div>\
                </div>';

				$('#extra_items_area').html(html);
				$('#extra_items_modal').modal({backdrop: 'static', keyboard: false, show: true}); // open lightbox
			},
			error:   function () {
			}
		});

	});

	$(document).on('change', '.extra_category', function (e) {
		e.stopImmediatePropagation();

		var id = $(this).val();
		var exhibition_id = <?= $this->formdata->id ?>;

		if (id == '') return;

		var $this = $(this);
		var $field = $('#extra_items_area .extra_item');
		$field.html('<option>Loading...</option>');

		$.ajax({
			type:    'post',
			url:     '<?= base_url('book_stall/ajax_get_inventory_items') ?>',
			data:    {category: id, exhibition_id: exhibition_id},
			success: function (data) {
				console.log(data);
				data = JSON.parse(data);

				if (data.error && data.error == 1) {
					alert(data.message);
					$field.html('<option value="">'+data.message+'</option>');
					return;
				}

				$field.html('<option value="">- Select Item -</option>');
				var item_price = 0;
				for (var i=0; i<data.data.length; i++) {
					$field.append('<option value="'+data.data[i].id+'" data-price-pkr="'+data.data[i].item_price_pkr+'" data-price-usd="'+data.data[i].item_price_usd+'">'+data.data[i].item_title+'</option>');
				}

			},
			error:   function () {
			}
		});
	});

	$(document).on('click', '.add_additional_item_btn', function (e) {
		e.stopImmediatePropagation();

		var category = $('#extra_items_area .extra_category').val();
		var item_id = $('#extra_items_area .extra_item').val();
		var item_name = $('#extra_items_area .extra_item option[value="'+item_id+'"]').text();
		var extra_quantity = $('#extra_items_area .extra_quantity').val();
		var item_price = ($('.booking_price_type').val() == 'PKR') ? $('#extra_items_area .extra_item option[value="'+item_id+'"]').attr('data-price-pkr') : $('#extra_items_area .extra_item option[value="'+item_id+'"]').attr('data-price-usd');

		item_price = item_price * extra_quantity;

		var uid = guid();
		var html = '<tr data-qty="'+extra_quantity+'">';
		html += '<td>'+item_name+'</td>';
		html += '<td>Additional Item</td>';
		html += '<td>\
        <input type="hidden" name="order_items['+uid+'][is_package_item]" value="0">\
        <input type="hidden" name="order_items['+uid+'][item_id]" value="'+item_id+'">\
        <input type="hidden" class="extra_item_price" name="order_items['+uid+'][item_price]" value="'+item_price+'">\
        <input type="number" class="form-control input-sm qty" name="order_items['+uid+'][quantity]" value="'+extra_quantity+'" max="'+extra_quantity+'">\
        </td>';
		html += '<td class="text-right">'+item_price+'</td>';
		html += '<td class="text-right"><button type="button" class="btn btn-danger btn-xs remove_extra_item"><i class="fa fa-times"></i></button></td>';
		html += '</tr>';

		$('#order_items_list').append(html);

		// calculate total extra price
        $('.extra_items_price_type').html($('.booking_price_type').val());
        var extra_total = 0;
		$('#order_items_list tr').each(function () {
			if ($(this).find('.extra_item_price').length > 0) {
				var p = $(this).find('.extra_item_price').val();
				extra_total += parseInt(p);
            }
		})
        $('.extra_items_total_amount').html(extra_total);

		$('#extra_items_area').html('');
		$('#extra_items_modal').modal('hide');
	});

	$(document).on('click', '.remove_extra_item', function (e) {
		e.stopImmediatePropagation();

		$(this).parents('tr').remove();
	});
	//Extra Items

    /* END STEP 3 */


    /* START STEP 4 */
    function render_invoice(callback) {
        $('#invoice_area').html('');

		var form_data = $('#exhibitor_form').serialize();
		form_data += '&' + $('#stalls_form').serialize();
		form_data += '&' + $('#order_item_list_form').serialize();
		form_data += '&' + $('#confirm_form').serialize();
		$.ajax({
			type:    'post',
			url:     '<?= base_url("book_stall/generate_order_invoice"); ?>?id=<?= $this->input->get('id') ?>',
			data:    form_data,
			success: function (data) {

				$('#invoice_area').html(data);
				if (callback) callback();
			},
			error:   function () {

			}
		});


	}

	$(document).on('change', '#confirm_form .booking_type', function (e) {
		e.stopImmediatePropagation();

		render_invoice();
	});
	$(document).on('change', '#confirm_form .booking_offer_discount', function (e) {
		e.stopImmediatePropagation();

		render_invoice();
	});
	$(document).on('change', '#confirm_form .booking_tax', function (e) {
		e.stopImmediatePropagation();

		render_invoice();
	});
	$(document).on('click', '#confirm_form .apply_discount_btn', function (e) {
		e.stopImmediatePropagation();

		var type = $('#discount_area [name="additional_discount_type"]').val();
		var remarks = $('#discount_area [name="discount_remarks"]').val();
		var amount = $('#discount_area [name="discount_amount"]').val();

		if (type == '') {
			alert('Discount type is required!');
			return;
        }
		if (remarks == '') {
			alert('Discount remarks is required!');
			return;
        }
		if (amount == '' || amount == 0 || amount < 0) {
			alert('Discount amount is required!');
			return;
        }


		render_invoice();
	});
	$(document).on('change', '.has_additional_discount', function (e) {
		e.stopImmediatePropagation();

		if ($(this).is(':checked')) {
			$('#discount_area').slideDown();
        } else {
			$('#discount_area').slideUp();
			render_invoice();
        }
	});
    /* END STEP 4 */
</script>
</body>
</html>
