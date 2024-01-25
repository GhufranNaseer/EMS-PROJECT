<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>
<style>
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

<div class="content-wrapper" data-page="order_list">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Order
			<small>Edit</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="active">Edit</li>
		</ol>
	</section>

	<!-- Main content -->
	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box">
					<div class="box-header">
						<ul class="nav nav-tabs h4" role="tablist">
							<li class="<?= (!$this->input->get('tab') || $this->input->get('tab') == 'order_detail') ? 'active' : '' ?>">
								<a href="<?= base_url('order_detail.html?id='.$this->input->get('id').'&order_id='.$this->input->get('order_id').'&tab=order_detail') ?>">Order Details</a>
							</li>
							<li class="<?= ($this->input->get('tab') && $this->input->get('tab') == 'edit_order') ? 'active' : '' ?>">
								<a href="<?= base_url('order_detail.html?id='.$this->input->get('id').'&order_id='.$this->input->get('order_id').'&tab=edit_order') ?>">Edit Order</a>
							</li>
                            <?php if ($this->userdata->user_group_id != SALES_PERSON) { ?>
                                <li class="<?= ($this->input->get('tab') && $this->input->get('tab') == 'order_logs') ? 'active' : '' ?>">
                                    <a href="<?= base_url('order_detail.html?id='.$this->input->get('id').'&order_id='.$this->input->get('order_id').'&tab=order_logs') ?>">Logs</a>
                                </li>
                                <li class="<?= ($this->input->get('tab') && $this->input->get('tab') == 'expired_forms') ? 'active' : '' ?>">
                                    <a href="<?= base_url('order_detail.html?id='.$this->input->get('id').'&order_id='.$this->input->get('order_id').'&tab=expired_forms') ?>">Expired forms</a>
                                </li>
                            <?php } ?>
						</ul>
					</div><!-- /.box-header -->

					<div class="box-body">
						<form action="<?= base_url('edit-order-submit.html') ?>?id=<?= $this->input->get('id') ?>&order_id=<?= $this->input->get('order_id') ?>" method="post" id="stalls_form" enctype="multipart/form-data">

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

                            <div id="order_items_list"></div>

							<div class="text-right">
								<button type="button" class="btn btn-success btn-lg stall_form_validate_btn">Save Stalls</button>
							</div>
						</form>
					</div>
				</div>

				<!-- /.box -->
			</div><!-- /.col -->
		</div><!-- /.row -->
	</section><!-- /.content -->
	<!-- /.content -->
</div>




<?php $this->load->view('includes/after_login/footer'); ?>

<link rel="stylesheet" href="<?= base_url('assets') ?>/iCheck/square/grey.css">
<script src="<?= base_url('assets') ?>/iCheck/icheck.min.js"></script>

<script>
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

	function validate_stalls(callback) {
		$.ajax({
			type:    'post',
			url:     '<?= base_url("book_stall/book_stalls_validate/doError"); ?>?id=<?= $this->input->get('id') ?>&order_id=<?= $this->input->get('order_id') ?>',
			data:    $('#stalls_form').serialize(),
			success: function (data) {
				console.log(data);
				$(".validation-failed").removeClass("validation-failed");

				if (data != "done") {
					show_validation_error(data);
				} else {
					if (callback) callback();
				}
			},
			error:   function () {
			}
		});
	}

	$(document).on('click', '.stall_form_validate_btn', function (e) {
		e.stopImmediatePropagation();

		swal({
				title: "Are you sure?",
				text: "You want to modify this order?",
				type: "warning",
				showCancelButton: true,
				confirmButtonClass: "btn-success",
				confirmButtonText: "Yes",
				closeOnConfirm: false,
				showLoaderOnConfirm: true
			},
			function(){
				validate_stalls(function() {
					$('#stalls_form').submit();
				})
			});

	});


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
		var package_name = (data.hasOwnProperty('package_title')) ? data.package_title : $('#stalls_form .add_stall_package').find('option[value="'+ data.package_id +'"]').text();
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
		html += '<td>'+stall_names.join(', ')+'<input type="hidden" class="stall_ids" name="stalls['+uid+'][stall_ids]" value="'+stall_ids.join()+'"></td>';
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

		render_package_items_list();
	}

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

					html = '<div>\
                        <input type="hidden" name="order_items['+uid+'][is_package_item]" value="1">\
                        <input type="hidden" name="order_items['+uid+'][package_id]" value="'+items[i].package_id+'">\
                        <input type="hidden" name="order_items['+uid+'][item_id]" value="'+items[i].item_id+'">\
                        <input type="hidden" name="order_items['+uid+'][total_stalls]" value="'+items[i].total_stalls+'">\
                        <input type="hidden" class="package_item" name="order_items['+uid+'][has_item]" value="1">\
                        <input type="hidden" class="form-control input-sm qty" name="order_items['+uid+'][quantity]" value="'+items[i].initial_allow+'" max="'+items[i].initial_allow+'">\
                        </div>';

					$('#order_items_list').append(html);
				}

				if (callback) callback();
			},
			error:   function () {
			}
		});
	}

	<?php

        $old_stall_groups = $this->db
            ->select('BS.*, P.package_title')
            ->where('BS.booking_id', $this->orderdata->id)
            ->group_by('BS.hall_id')
            ->group_by('BS.package_id')
			->join('es_packages as P', 'P.id = BS.package_id', 'LEFT')
            ->get('es_exhibition_booking_stalls as BS')
            ->result();

        foreach ($old_stall_groups as $group) {
            $old_stalls = $this->db
                ->select('BS.*, S.stall_name, S.stall_size, S.stall_price_usd, S.stall_price_pkr')
				->where('BS.booking_id', $this->orderdata->id)
                ->where('BS.hall_id', $group->hall_id)
                ->where('BS.package_id', $group->package_id)
				->join('es_exhibition_stalls as S', 'S.id = BS.stall_id', 'LEFT')
				->get('es_exhibition_booking_stalls as BS')
				->result();

            $stalls = array();

            foreach ($old_stalls as $old_stall) {
                $price = ($this->orderdata->booking_price_type == 'PKR') ? $old_stall->stall_price_pkr : $old_stall->stall_price_usd;

                $stalls[] = array(
                    'id' => $old_stall->stall_id,
                    'name' => $old_stall->stall_name,
                    'stall_size' => $old_stall->stall_size,
                    'price' => $price,
                );
            }

            $data = array(
                'stall_type' => $group->booking_stall_type,
                'package_id' => $group->package_id,
                'hall_id' => $group->hall_id,
				'package_title' => $group->package_title,
				'stalls' => $stalls,
            );

            echo 'add_stalls_in_order('. json_encode($data) .');';
        }

        echo 'render_package_items_list();';
    ?>

	load_packages();
</script>
</body>
</html>
