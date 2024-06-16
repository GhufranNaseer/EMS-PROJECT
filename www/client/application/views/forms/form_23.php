<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<?php
$contact_person = $this->db
    ->where('customer_id', $this->userdata->id)
    ->get('es_customer_contact_persons')
    ->row();

$venue = $this->db
    ->where('id', $this->event->location_id)
    ->get('es_locations')
    ->row();
?>
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

<div class="content-wrapper" data-page="form_23">
    <!-- Content Header (Page header) -->

    <section class="content-header">
        <h1 class="pull-left">FORM 20</h1>

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
        <h1 class="text-center">Display Vehicle Mobility:</h1>
    </section>



    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">

                    <div class="box-body">
                        <form action="<?= (is_null($this->editdata)) ? base_url('forms/form_23/form_23_submit') : base_url('forms/form_23/edit_submit?id=' . $this->editdata->id) ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">


                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="company">Company Name:</label>
                                        <input type="text" class="form-control" name="company"
                                               id="company" value="<?= $this->userdata->company ?>" readonly>
                                    </div>

                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="country">Country:</label>
                                        <input type="text" class="form-control" name="country"
                                               id="country" value="<?= $this->userdata->country ?>" readonly>
                                    </div>

                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="city">City:</label>
                                        <input type="text" class="form-control" name="city"
                                               id="city" value="<?= $this->userdata->city ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="contact_person">Contact Person:</label>
                                        <input type="text" class="form-control" name="contact_person"
                                               id="contact_person" value="<?= $contact_person->person_name ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="phone">Telephone #:</label>
                                        <input type="number" class="form-control" name="phone"
                                               id="phone" value="<?= $contact_person->primary_phone ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="mobile">Cell Phone #:</label>
                                        <input type="number" class="form-control" name="mobile"
                                               id="mobile" value="<?= $contact_person->secondary_phone ?>" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="description">Item Description: <span class="text-red" style="font-size: 11px;">(Including Caliber,Wheeled etc)</span></label>
                                        <input type="text" class="form-control" name="description"
                                               id="description" >
                                    </div>

                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label for="weight">Weight:</label>
                                        <input type="number" class="form-control" name="weight"
                                               id="weight" >
                                    </div>

                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label for="quantity">Quantity:</label>
                                        <input type="number" class="form-control" name="quantity"
                                               id="quantity" >
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label for="price">Cost/Price USD:</label>
                                        <input type="number" class="form-control" name="price"
                                               id="price" value="10000" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="venue">Venue:</label>
                                        <input type="text" class="form-control" name="venue"
                                               id="venue" value="<?= $venue->location_title ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label for="event_day">Display On Event Day <span class="text-red">*</span></label>
                                    <select name="event_day" class="form-control">
                                        <option value="">- select -</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label>Product Image</label>
                                    <div id="organizer_logo_container"></div>
                                </div>
                            </div>

                            <p class="bg-danger js-msgbox"></p>
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="well well-sm" style="height: 150px;">
                                        <strong>Term And Conditions:</strong><br>
										<?php
										$note = $this->db
											->where('exhibition_id', $this->event->id)
											->where('type', 'display_mobility_terms')
											->order_by('id', 'DESC')
											->get('es_exhibition_notification')
											->row();

										if ($note) {
											echo $note->title;
										}
										?>
                                    </div>
                                </div>
                                <div class="col-xs-4"></div>
                                <div class="col-xs-2">
                                    <a href="javascript:void(0);"
                                       class="btn btn-primary btn-block margin-bottom js-form_btn">Add</a>
                                </div>
                            </div>

                            <table class="table table-bordered table-striped" style="margin: 20px 0 40px;">
                                <thead>
                                <tr>
                                    <th>S.no</th>
                                    <th>Company Name</th>
                                    <th>Country</th>
                                    <th>City</th>
                                    <th>Product Description</th>
                                    <th>Weight Kg.s</th>
                                    <th>Contact Person</th>
                                    <th>Telephone#</th>
                                    <th>Cell Phone Number</th>
                                    <th>Cost</th>
                                    <th>Product Image</th>
                                    <th>Action</th>

                                </tr>
                                </thead>
                                <tbody>
								<?php
								if (!is_null($this->formdata)) {
									foreach ($this->formdata->vehicle as $count => $row) {
										echo '<tr>
                                        <td>'. ($count + 1) .'</td>
                                        <td>'. $row->company .'</td>
                                        <td>'. $row->country .'</td>
                                        <td>'. $row->city .'</td>
                                        <td>'. $row->description .'</td>
                                        <td>'. $row->weight .'</td>
                                        <td>'. $row->contact_person .'</td>
                                        <td>'. $row->phone .'</td>
                                        <td>'. $row->mobile .'</td>
                                        <td>'. $row->price .'</td>
                                        <td><img src="'.base_url($row->vehicle_image).'" width="50px" alt=""></td>
                                        <td>
                                        <a href="'. base_url('forms/form_23/delete_vehicle?id='. $row->id ) .'">Remove</a>
                                        </td>
                                        </tr>';
									}
								}
								?>
                                </tbody>
                            </table>




                        </form>

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
			'urlValidator': "<?php echo base_url("forms/form_23/form_23_validate/doError"); ?>",
			'loadingImg':   "<?php echo base_url("../assets/img/load-indicator.gif"); ?>"
		});
	});


    var file = new file_upload_preview({
        selector: '#organizer_logo_container',
        ajax_src: '<?= base_url('welcome/file_upload') ?>',
        extensions: 'jpg|jpeg|png|PNG',
        base_url: '../uploads/form_23/',
        post_file_name: 'vehicle_image',
        has_rotation: false,
        max_upload: 1,
		on_upload: function (file_name) {
			$('#organizer_logo_container .imgbox img').attr('src', '<?= base_url('../uploads/form_23/') ?>' + file_name)
		}
    });

</script>
</body>
</html>
