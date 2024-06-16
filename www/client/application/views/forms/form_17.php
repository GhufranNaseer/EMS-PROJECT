<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>
<style>

    .content-header > h1 {
        margin-top: 20px;
    }
    .date-box {
        width: 300px;
        padding: 10px;
        text-align: center;
        margin: 0 0 15px;
        background: #db4c3b;
        color: #fff;
        border-radius: 8px;
    }
    .check{
        border: 1px solid #d2d6de;
        padding: 7px;
    }
    .drop_pickup{
        border: 1px solid #d2d6de;
        padding: 5px;
    }

</style>

<div class="content-wrapper" data-page="form_17">
    <!-- Content Header (Page header) -->

    <section class="content-header">
        <h1 class="pull-left">FORM 14</h1>

        <div class="pull-right">
            <h4>FORM SUBMISSION DUE DATE</h4>
			<?php
			$form_expire_date = $this->db->where('exhibition_id', $this->event->id)->where('form_id', $this->form_id)->get('es_exhibition_forms')->row()->expiry_date . ' 00:00:00';
			$check_extend_date = $this->db
				->where('exhibition_id', $this->event->id)
				->where('form_id', $this->form_id)
				->where('booking_id', $this->booking->id)
				->get('es_exhibition_forms_extended')
				->row();
			$extend_hours_html = '';
			if (isset($check_extend_date) && !empty($check_extend_date)) {
				if ($check_extend_date->update_date && !is_null($check_extend_date->update_date) && strtotime($check_extend_date->update_date) >= strtotime($form_expire_date)) {
					$form_expire_date = $check_extend_date->update_date;
				}
				$new_expire_time = strtotime('+ '.(int)$check_extend_date->extended_hours.' hours', strtotime($form_expire_date));

				$remaining_hours = (($new_expire_time - strtotime(date('Y-m-d H:i:s')) ) / 60 ) / 60;

				$extend_hours_html = '<div id="extend_hours_html"></div>';
				$extend_hours_html .= '<script>
                var countDownDate = new Date("'.date('Y-m-d H:i:s', ($new_expire_time)).'").getTime();
                var x = setInterval(function() {
                
                  // Get todays date and time
                  var now = new Date().getTime();
                
                  // Find the distance between now and the count down date
                  var distance = countDownDate - now;
                
                  // Time calculations for days, hours, minutes and seconds
                  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                  // Display the result in the element with id="demo"
                  document.getElementById("extend_hours_html").innerHTML = "<h4>Extended Time<h4><h3 class=\'date-box\'>" + days + "d " + hours + "h "
                  + minutes + "m " + seconds + "s </h3>";
                
                  // If the count down is finished, write some text 
                  if (distance < 0) {
                    clearInterval(x);
                    document.getElementById("extend_hours_html").innerHTML = "";
                  }
                }, 1000);
                </script>
                ';
			}
			?>
            <h3 class="date-box"><?= date('d F Y', strtotime($form_expire_date)) ?></h3>
			<?= $extend_hours_html ?>
        </div>
        <div class="clear-fix"></div>
        <h1 class="text-center">Vehicle Rental/Rent A Car:</h1>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">

                    <div class="box-body">
                        <form action="<?= (is_null($this->editdata)) ? base_url('forms/form_17/form_17_submit') : base_url('forms/form_17/edit_submit?id=' . $this->editdata->id) ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">

                            <h4>Exhibitor Detail:</h4>
                            <div class="row">
                                <div class="col-sm-2">
                                    <label>Name(As In Passport) <span class="text-red">*</span></label>
                                    <input type="hidden" name="exhibitor[name]">
                                    <select name="exhibitor[id]" class="form-control person_badge">
                                        <option value="">- select -</option>
										<?php
										$badges = $this->db
											->where('exhibition_id', $this->event->id)
											->where('booking_id', $this->booking->id)
											->get('es_exhibition_badges')
											->result();
										foreach ($badges as $badge) {
											echo '<option value="'.$badge->id.'">'.$badge->full_name.'</option>';
										}
										?>
                                    </select>
                                </div>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Country:</label>
                                                <input type="text" class="form-control" name="exhibitor[country]">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label>City:</label>
                                                <input type="text" class="form-control" name="exhibitor[city]">
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label>Nationality:</label>
                                                <input type="text" class="form-control" name="exhibitor[nationality]" readonly>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label>CNIC / Passport No:</label>
                                                <input type="text" class="form-control" name="exhibitor[passport]" readonly>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Cellphone Number(Intl.):</label>
                                                <input type="text" class="form-control" name="exhibitor[phone_international]" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>Cell Local(Pak.network):</label>
                                        <input type="text" class="form-control" name="exhibitor[phone_local]">
                                    </div>
                                </div>
                            </div>


                            <h4>Vehicle Type:</h4>

                            <div class="row">
                                <div class="col-sm-2">
                                    <label for="flight_time">Description Of Vehicle <span class="text-red">*</span></label>
                                    <select name="vehicle[name]" class="form-control vehicle_name" onchange="update_price();">
                                        <option value="">- select -</option>
                                        <option value="1300 cc Sedan Car" data-price="150">1300 cc Sedan Car</option>
                                        <option value="1500-1800 cc Sedan Car" data-price="2500">1500-1800 cc Sedan Car</option>
                                        <option value="15 Seater Van" data-price="300">15 Seater Van</option>
                                        <option value="25 Seater Coaster" data-price="400">25 Seater Coaster</option>
                                    </select>
                                </div>

                                <div class="col-sm-1"> 
                                    <div class="form-group">
                                        <label>Quantity:</label>
                                        <input type="number" class="form-control vehicle_qty" name="vehicle[qty]" min="1" onkeyup="update_price();">
                                    </div> 
                                </div>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>From:</label>
                                        <input type="date" class="form-control vehicle_from_date" name="vehicle[from_date]" onchange="update_price();">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>To:</label>
                                        <input type="date" class="form-control vehicle_to_date" name="vehicle[to_date]" onchange="update_price();">
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label>Total days:</label>
                                        <input type="number" class="form-control vehicle_total_days" name="vehicle[total_days]" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>8 hour Rent Per Day</label>
                                        <div class="input-group">
                                            <div class="input-group-addon">$</div>
                                            <input type="text" class="form-control vehicle_price" name="vehicle[price]" readonly>
                                            <div class="input-group-addon">USD</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>Total Cost</label>
                                        <div class="input-group">
                                            <div class="input-group-addon">$</div>
                                            <input type="text" class="form-control vehicle_total_price" name="vehicle[total_price]" readonly>
                                            <div class="input-group-addon">USD</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <h4>Pickup / Dropoff Details</h4>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Please Specify The Pick-Up Point: <span class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="pickup[location]">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Date <span class="text-red">*</span></label>
                                                <input type="date" class="form-control" name="pickup[date]">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Time <span class="text-red">*</span></label>
                                                <input type="time" class="form-control" name="pickup[time]">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Please Specify The Drop-Off Point: <span class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="dropoff[location]">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Date <span class="text-red">*</span></label>
                                                <input type="date" class="form-control" name="dropoff[date]">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Time <span class="text-red">*</span></label>
                                                <input type="time" class="form-control" name="dropoff[time]">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="well well-sm" style="height: 150px;margin: 0;">
                                        <strong>Term And Conditions:</strong><br>
                                        <?php
										$note = $this->db
											->where('exhibition_id', $this->event->id)
											->where('type', 'vehicle_rental_terms')
											->order_by('id', 'DESC')
											->get('es_exhibition_notification')
											->row();

										if ($note) {
											echo $note->title;
										}
										?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="is_agree"> Read & Agreed</label>
                                    </div>
                                </div>
                                <div class="col-xs-4"></div>
                                <div class="col-xs-2">
                                    <a href="javascript:void(0);"
                                       class="btn btn-primary btn-block margin-bottom js-form_btn">Add</a>
                                </div>
                            </div>

                            <p class="bg-danger js-msgbox"></p>
                        </form>

                        <table class="table table-bordered table-striped" style="margin: 20px 0 40px;">
                            <thead>
                            <tr>
                                <th>S.no</th>
                                <th>Company Name</th>
                                <th>Client Name</th>
                                <th>Country</th>
                                <th>CNIC / Passport</th>
                                <th>Int Cell#</th>
                                <th>Local Cell#</th>
                                <th>Vehicle Type</th>
                                <th>Qty.</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Cost USD</th>
                                <th>Pickup Date</th>
                                <th>Time</th>
                                <th>Dropoff Date</th>
                                <th>Time</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
							<?php
							if (!is_null($this->formdata)) {
								foreach ($this->formdata->booking as $count => $row) {
									echo '<tr>
                                        <td>'. ($count + 1) .'</td>
                                        <td>'. $this->userdata->company.'</td>
                                        <td>'. $row->exhibitor->name.'</td>
                                        <td>'. $row->exhibitor->country.'</td>
                                        <td>'. $row->exhibitor->passport.'</td>
                                        <td>'. $row->exhibitor->phone_international.'</td>
                                        <td>'. $row->exhibitor->phone_local.'</td>
                                        <td>'. $row->vehicle->name.'</td>
                                        <td>'. $row->vehicle->qty.'</td>
                                        <td>'. $row->vehicle->from_date.'</td>
                                        <td>'. $row->vehicle->to_date.'</td>
                                        <td>'. $row->vehicle->total_price.'</td>
                                        <td>'. $row->pickup->date.'</td>
                                        <td>'. $row->pickup->time.'</td>
                                        <td>'. $row->dropoff->date.'</td>
                                        <td>'. $row->dropoff->time.'</td>
                                        <td>
                                        <a href="'. base_url('forms/form_17/delete_booking?id='. $row->id ) .'">Remove</a>
                                        </td>
                                        </tr>';
								}
							}
							?>
                            </tbody>
                        </table>

                    </div><!-- /.box-body -->
                </div><!-- /.box -->

                <!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.content -->
    <!-- /.content -->
</div><!-- /.content-wrapper -->

<?php $this->load->view('includes/after_login/footer'); ?>

<script>
	$(function () {
		doFormValidation({
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("forms/form_17/form_17_validate/doError"); ?>",
			'loadingImg':   "<?php echo base_url("../assets/img/load-indicator.gif"); ?>"
		});
	});

	function update_price() {
		var price = $('.vehicle_name option:selected').attr('data-price');
		var qty = $('.vehicle_qty').val();
		var from_date = $('.vehicle_from_date').val();
		var to_date = $('.vehicle_to_date').val();
        var total = 0;
		var date1 = new Date(from_date);
		var date2 = new Date(to_date);
		var timeDiff = Math.abs(date2.getTime() - date1.getTime());
		var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

		if (!isNaN(diffDays) && diffDays > 0) {
			total = (price * qty) * diffDays;
			$('.vehicle_total_days').val(diffDays);
			$('.vehicle_price').val(price);
			$('.vehicle_total_price').val(total);
        } else {

			$('.vehicle_price').val('');
			$('.vehicle_total_price').val('');
        }

	}

	$(document).on('change', '.person_badge', function (e) {
		e.stopImmediatePropagation();

		var id = $(this).val();
		if (id != '') {
			$.ajax({
				type:    'post',
				url:     '<?php echo base_url("forms/form_17/get_badge_data"); ?>',
				data:    {id: id},
				success: function (data) {
					data = JSON.parse(data);
					console.log(data);
					if (data.error) {
						alert(data.message);
						return;
                    }

                    $('[name="exhibitor[name]"]').val(data.data.full_name);
                    $('[name="exhibitor[nationality]"]').val(data.data.nationality);
                    $('[name="exhibitor[passport]"]').val((data.data.nationality == 'Pakistani') ? data.data.cnic : data.data.passport);
                    $('[name="exhibitor[phone_international]"]').val(data.data.mobile);
				},
				error:   function () {
				}
			});
        }
	});
</script>
</body>
</html>
