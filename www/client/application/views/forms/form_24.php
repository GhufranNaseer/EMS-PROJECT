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

    .check {
        border: 1px solid #d2d6de;
        padding: 7px;
    }
    .check p {
        font-weight: bold;
    }

</style>

<div class="content-wrapper" data-page="form_24">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="pull-left">FORM 21</h1>

        <div class="pull-right">
            <h4>FORM SUBMISSION DUE DATE</h4>
			<?php
			$form_expire_date = $this->db->where('exhibition_id', $this->event->id)->where('form_id', $this->form_id)->get('es_exhibition_forms')->row()->expiry_date . ' 24:00:00';
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
        <h1 class="text-center">Hotel Reservation</h1>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= (is_null($this->editdata)) ? base_url('forms/form_24/form_24_submit') : base_url('forms/form_24/edit_submit?id=' . $this->editdata->id) ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-2 text-center">
                                    <h4 class=""><strong>IDEAS 2018 Official Hotels</strong></h4>
                                    <img src="<?= base_url("../assets/img/avari-hotels.jpg") ?>" width="180px">
                                    <img src="<?= base_url("../assets/img/download.png") ?>" width="180px">
                                    <img src="<?= base_url("../assets/img/download.jpg") ?>" width="180px">
                                    <img src="<?= base_url("../assets/img/Ramada-logo-logotype-1024x768b-862x576.png") ?>"
                                         width="180px">
                                    <img src="<?= base_url("../assets/img/download (1).png") ?>" width="180px">
                                </div>
                                <div class="col-sm-10">
                                    <h4>Flight Detail</h4>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label style="margin-top: 39px;">Airline</label>
                                                <input type="text" class="form-control" name="flight">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="check">
                                                <p class="text-center">Check In</p>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="">
                                                            <label>Flight Date</label>
                                                            <input type="date" class="form-control" name="flight_check_in_date">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="">
                                                            <label>Flight Time</label>
                                                            <input type="time" class="form-control" name="flight_check_in_time">
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                        <div class="col-sm-4">
                                            <div class="check">
                                                <p class="text-center">Check out</p>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="">
                                                            <label>Flight Date</label>
                                                            <input type="date" class="form-control" name="flight_check_out_date">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="">
                                                            <label>Flight Time</label>
                                                            <input type="time" class="form-control" name="flight_check_out_time">
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <h4>Hotel Reservation</h4>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <label>Hotel</label>
                                            <select name="hotel" class="form-control hotel">
                                                <option value="">- select -</option>
                                                <?= get_instance()->funcs->print_input_data_list('reservation_hotel') ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-2">
                                            <label style="margin-top: 40px;">Room Type</label>
                                            <select name="hotel_room" class="form-control hotel_room">
                                                <option value="">- select -</option>

                                            </select>
                                        </div>
                                        <div class="col-sm-2">
                                            <label style="margin-top: 40px;">Person Name</label>
                                            <select name="person_name" class="form-control">
                                                <option value="">- select -</option>
                                                <?php
                                                $badges = $this->db
                                                    ->where('exhibition_id', $this->event->id)
                                                    ->where('booking_id', $this->booking->id)
                                                    ->get('es_exhibition_badges')
                                                    ->result();
                                                foreach ($badges as $badge) {
                                                    echo '<option value="'.$badge->full_name.'">'.$badge->full_name.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="checkbox">
                                                <label><input type="checkbox" name="has_room_sharing" class="has_room_sharing"> Room Is Shared </label>
                                            </div>

                                            <label>Person Name</label>
                                            <select name="sharing_person_name" class="form-control" disabled>
                                                <option value="">- select -</option>
                                                <?php
												foreach ($badges as $badge) {
													echo '<option value="'.$badge->full_name.'">'.$badge->full_name.'</option>';
												}
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="check">
                                                        <p class="text-center">Room Type</p>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="text-center">
                                                                    <label>
                                                                        Smoking
                                                                        <br>
                                                                        <input type="radio" name="room_smoking" value="yes" checked>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="text-center">
                                                                    <label>
                                                                        Not Smoking
                                                                        <br>
                                                                        <input type="radio" name="room_smoking" value="no">
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="check">
                                                        <p class="text-center">Shuttle Service</p>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="text-center">
                                                                    <label>
                                                                        Required
                                                                        <br>
                                                                        <input type="radio" name="shuttle_service" value="yes" checked>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="text-center">
                                                                    <label>
                                                                        Not Required
                                                                        <br>
                                                                        <input type="radio" name="shuttle_service" value="no">
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row has_shuttle_service">

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label style="margin-top: 10px;">Please Enter Pick-Up Point:
                                                    <span class="text-red">*</span>
                                                </label>
                                                <input type="text" class="form-control" name="pickup_location" value="Jinnah International Airport (Karachi)" readonly>
                                            </div>

                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label style="margin-top: 10px;">
                                                    Pickup
                                                </label>
                                                <input type="text" class="form-control" name="pickup_point" value="Karachi Airport" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label style="margin-top: 10px;">
                                                    Drop Point
                                                </label>
                                                <input type="text" class="form-control" name="drop_point" value="Hotel" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label style="margin-top: 10px;">
                                                    Charges in PKR
                                                </label>
                                                <input type="text" class="form-control" name="pickup_rate" value="2000" readonly>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <div class="well well-sm" style="height: 150px;">
                                                <strong>Term And Conditions:</strong><br>
												<?php
												$note = $this->db
													->where('exhibition_id', $this->event->id)
													->where('type', 'hotel_reservation_terms')
													->order_by('id', 'DESC')
													->get('es_exhibition_notification')
													->row();

												if ($note) {
													echo $note->title;
												}
												?>
                                            </div>
                                        </div>
                                        <div class="col-sm-2" style="margin-top: 115px;">
                                            <a href="javascript:void(0);"
                                               class="btn btn-primary btn-block margin-bottom js-form_btn">Add</a>
                                        </div>
                                    </div>

                                    <p class="bg-danger js-msgbox"></p>
                                </div>
                            </div>
                        </form>

                        <table class="table table-bordered table-striped" style="margin: 20px 0 40px;">
                            <thead>
                            <tr>
                                <th>S.no</th>
                                <th>Airline</th>
                                <th>Arrival Date</th>
                                <th>Arrival Time</th>
                                <th>Departure Date</th>
                                <th>Departure Time</th>
                                <th>Hotel Name</th>
                                <th>Room Type</th>
                                <th>Person Name</th>
                                <th>Shared Room Person</th>
                                <th>Smoking</th>
                                <th>Pick Up Location</th>
                                <th>Shuttle Service</th>
                                <th>Action</th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php
							if (!is_null($this->formdata)) {
								foreach ($this->formdata->reservation as $count => $row) {
									echo '<tr>
                                        <td>'. ($count + 1) .'</td>
                                        <td>'. $row->flight.'</td>
                                        <td>'. $row->flight_check_in_date.'</td>
                                        <td>'. $row->flight_check_in_time.'</td>
                                        <td>'. $row->flight_check_out_date.'</td>
                                        <td>'. $row->flight_check_out_time.'</td>
                                        <td>'. $row->hotel.'</td>
                                        <td>'. $row->hotel_room.'</td>
                                        <td>'. $row->person_name.'</td>
                                        <td>'. ((isset($row->sharing_person_name)) ? $row->sharing_person_name : '-') .'</td>
                                        <td>'. strtoupper($row->room_smoking) .'</td>
                                        <td>'. $row->pickup_location.'</td>
                                        <td>'. strtoupper($row->shuttle_service) .'</td>
                                        <td>
                                        <a href="'. base_url('forms/form_24/delete_reservation?id='. $row->id ) .'">Remove</a>
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
			'urlValidator': "<?php echo base_url("forms/form_24/form_24_validate/doError"); ?>",
			'loadingImg':   "<?php echo base_url("../assets/img/load-indicator.gif"); ?>"
		});
	});

	$(document).on('change', '.hotel', function (e) {
		e.stopImmediatePropagation();

		var h = $(this).val();

		$('.hotel_room').html('<option value="">- select -</option>');

		if (h == 'Avari Hotel' && false) {
			$('.hotel_room').append('<option value="Avari Business Class Suite">Avari Business Class Suite</option>');
			$('.hotel_room').append('<option value="Executive Club Room">Executive Club Room</option>');
			$('.hotel_room').append('<option value="Avari World Traveler Room">Avari World Traveler Room</option>');
			$('.hotel_room').append('<option value="Lady Avari Room">Lady Avari Room</option>');
			$('.hotel_room').append('<option value="Avari Business Club Room">Avari Business Club Room</option>');
			$('.hotel_room').append('<option value="Delux Suite">Delux Suite</option>');
			$('.hotel_room').append('<option value="Presidential Suit">Presidential Suit</option>');
        } else {
			$('.hotel_room').append('<option value="Standard Room">Standard Room</option>');
			$('.hotel_room').append('<option value="Delux Room">Delux Room</option>');
			$('.hotel_room').append('<option value="Executive Room">Executive Room</option>');
			$('.hotel_room').append('<option value="Suite">Suite</option>');
        }
	});


	$(document).on('change', '.has_room_sharing', function (e) {
		e.stopImmediatePropagation();

		$('[name="sharing_person_name"]').val('').attr('disabled', !($(this).is(':checked')))
	});

	$(document).on('change', '[name="shuttle_service"]', function (e) {
		e.stopImmediatePropagation();

		if ($('[name="shuttle_service"]:checked').val() == 'yes') {
			$('.has_shuttle_service').show();
        } else {
			$('.has_shuttle_service').hide();
        }
	});
</script>
</body>
</html>
