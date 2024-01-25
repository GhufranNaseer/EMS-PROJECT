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

</style>
<?php $options= $this->db->where('type','country')->get('input_data_list')->result();?>
<datalist id="data_country">
	<?php foreach ($options as $option) {?>
    <option value="<?= $option->data ?>">
		<?php } ?>
</datalist>

<?php $options= $this->db->where('type','nationality')->get('input_data_list')->result();?>
<datalist id="data_nationality">
	<?php foreach ($options as $option) {?>
    <option value="<?= $option->data ?>">
		<?php } ?>
</datalist>

<div class="content-wrapper" data-page="form_21">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="pull-left">FORM 18</h1>

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
        <h1 class="text-center">Visa To Pakistan</h1>
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
                        <form action="<?= (is_null($this->editdata)) ? base_url('forms/form_21/form_21_submit') : base_url('forms/form_21/edit_submit?id=' . $this->editdata->id) ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">

                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="visa_name">Name (As in Passport): <span class="text-red">*</span></label>
                                        <input type="text" class="form-control" name="visa_name"
                                               id="visa_name" placeholder="Name">
                                    </div>
                                </div>

                                <div class="col-sm-3">

                                    <div class="form-group">
                                        <label for="visa_country">Select Your Country <span class="text-red">*</span></label>
                                        <input type="text" class="form-control" name="visa_country" id="visa_country"
                                               placeholder="Country" list="data_country">
                                    </div>

                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="visa_city">City: <span class="text-red">*</span></label>
                                        <input type="text" class="form-control" name="visa_city"
                                               id="visa_city">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="visa_nationality">Nationality: <span class="text-red">*</span></label>
                                        <input type="text" class="form-control" name="visa_nationality"
                                               id="visa_nationality" list="data_nationality">
                                    </div>
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="visa_passport">Passport Number: <span class="text-red">*</span></label>
                                        <input type="text" class="form-control" name="visa_passport"
                                               id="visa_passport" placeholder="">
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="visa_passport_issue_date">Date of Passport Issue: <span class="text-red">*</span></label>
                                        <input type="date" class="form-control" name="visa_passport_issue_date"
                                               id="visa_passport_issue_date" placeholder="">
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="visa_passport_expiry_date">Date of Passport Expiry: <span class="text-red">*</span></label>
                                        <input type="date" class="form-control" name="visa_passport_expiry_date"
                                               id="visa_passport_expiry_date" placeholder="">
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <h3>Contact Person Information</h3>

                                    <div class="row">
                                        <div class="col-sm-6"> <div class="form-group">
                                                <label for="contact_person_designation">Designation: <span class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="contact_person_designation"
                                                       id="contact_person_designation" placeholder="">
                                            </div></div>
                                        <div class="col-sm-6"> <div class="form-group">
                                                <label for="contact_person_email">Email Address: <span class="text-red">*</span></label>
                                                <input type="email" class="form-control" name="contact_person_email"
                                                       id="contact_person_email" placeholder="">
                                            </div></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="contact_person_mobile">Mobile: <span class="text-red">*</span></label>
                                                <input type="tel" class="form-control" name="contact_person_mobile"
                                                       id="contact_person_mobile" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="contact_person_telephone">Telephone Number:</label>
                                                <input type="tel" class="form-control" name="contact_person_telephone"
                                                       id="contact_person_telephone" placeholder="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="contact_person_fax">Fax Number:</label>
                                                <input type="tel" class="form-control" name="contact_person_fax"
                                                       id="contact_person_fax" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Upload Bio Page or Page 1: <span class="text-red">*</span></label>
                                            <div id="img_bio_container"></div>
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Upload Your <br> Picture:</label>
                                            <div id="img_pic_container"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h3 class="text-center">Concerned Pakistani Mission(where will you apply for visa)</h3>

                                    <div class="row">
                                        <div class="col-sm-6"><div class="form-group">
                                                <label for="concerned_country">Mission Country: <span class="text-red">*</span></label>
                                                <input type="tel" class="form-control" name="concerned_country"
                                                       id="concerned_country" list="data_country" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-sm-6"><div class="form-group">
                                                <label for="concerned_city">City: <span class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="concerned_city"
                                                       id="concerned_city" placeholder="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6"><div class="form-group">
                                                <label for="concerned_telephone_number">Telephone Number:</label>
                                                <input type="tel" class="form-control" name="concerned_telephone_number"
                                                       id="concerned_telephone_number" placeholder="">
                                            </div>
                                            <div class="form-group">
                                                <label for="concerned_fax_number">Fax Number:</label>
                                                <input type="tel" class="form-control" name="concerned_fax_number"
                                                       id="concerned_fax_number" placeholder="">
                                            </div>
                                            <div class="form-group">
                                                <label for="concerned_email">Email Address:</label>
                                                <input type="email" class="form-control" name="concerned_email"
                                                       id="concerned_email" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-sm-6"><div class="form-group">
                                                <label for="concerned_address">Address:</label>
                                                <textarea style="height: 180px;" class="form-control" name="concerned_address"
                                                          id="concerned_address" placeholder=""></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-10">

                                </div>
                                <div class="col-xs-2">
                                    <a href="javascript:void(0);"
                                       class="btn btn-primary btn-block margin-bottom js-form_btn">Submit</a>
                                </div>
                            </div>

                             <p class="bg-danger js-msgbox"></p>

                        </form>

                        <table class="table table-bordered table-striped" style="margin: 20px 0 40px;">
                            <thead>
                            <tr>
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
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
							<?php
							if (!is_null($this->formdata)) {
								foreach ($this->formdata->visas as $count => $row) {
									echo '<tr>
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
                                        <a href="'. base_url($row->contact_person_bio_image[0] ) .'" target="_blank">View</a>
                                        </td>
                                        <td>
                                        <a href="'. base_url('forms/form_21/delete_visa?id='. $row->id ) .'">Delete</a>
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

	var file = new file_upload_preview({
		selector: '#img_bio_container',
		ajax_src: '<?= base_url('welcome/file_upload') ?>',
		extensions: 'jpg|jpeg|png|PNG',
		base_url: '../uploads/visa_images/',
		post_file_name: 'contact_person_bio_image',
		has_rotation: false,
		max_upload: 1,
		on_upload: function (file_name) {
			$('#img_bio_container .imgbox img').attr('src', '<?= base_url('../uploads/visa_images/') ?>' + file_name)
		}
	});
	var file2 = new file_upload_preview({
		selector: '#img_pic_container',
		ajax_src: '<?= base_url('welcome/file_upload') ?>',
		extensions: 'jpg|jpeg|png|PNG',
		base_url: '../uploads/visa_images/',
		post_file_name: 'contact_person_pic_image',
		has_rotation: false,
		max_upload: 1,
		on_upload: function (file_name) {
			$('#img_pic_container .imgbox img').attr('src', '<?= base_url('../uploads/visa_images/') ?>' + file_name)
		}
	});

	$(function () {
		doFormValidation({
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("forms/form_21/form_21_validate/doError"); ?>",
			'loadingImg':   "<?php echo base_url("../assets/img/load-indicator.gif"); ?>"
		});
	});

</script>
</body>
</html>
