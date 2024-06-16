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
        padding: 3px 7px 0px 7px;
    }
    .drop_pickup {
        border: 1px solid #d2d6de;
        padding: 5px;
    }
</style>
<?php $options = $this->db->where('type', 'country')->get('input_data_list')->result(); ?>
<datalist id="data_country">
	<?php
    foreach ($options as $option) {
		echo '<option value="'.$option->data.'">';
	}
	?>
</datalist>

<div class="content-wrapper" data-page="form_10">
    <!-- Content Header (Page header) -->

    <section class="content-header">
        <h1 class="pull-left">FORM 7</h1>

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
        <h1 class="text-center">Trade Visitors Badge Details</h1>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3>Visitor Personal Information</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form
                            action="<?= (is_null($this->editdata)) ? base_url('forms/form_10/form_10_submit') : base_url('forms/form_10/edit_submit?id=' . $this->editdata->id) ?>"
                            method="post" id="crd_form"
                            enctype="multipart/form-data">


                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Visitor Full name:
                                                    <span class="text-red">*</span>
                                                </label>
                                                <input type="text" class="form-control" name="visitor_name">
                                            </div>
                                            <div class="form-group">
                                                <label>Personal Landline Number:</label>
                                                <input type="number" class="form-control" name="visitor_phone">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Father Name:</label>
                                                <input type="text" class="form-control" name="visitor_father_name">
                                            </div>
                                            <div class="form-group">
                                                <label>Cell Phone Number:
                                                    <span class="text-red">*</span>
                                                </label>
                                                <input type="text" class="form-control" name="visitor_mobile">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Job Title:
                                                    <span class="text-red">*</span>
                                                </label>
                                                <input type="text" class="form-control" name="visitor_job_title">
                                            </div>
                                            <div class="form-group">
                                                <label>Email Address:</label>
                                                <input type="email" class="form-control" name="visitor_email">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <div class="form-group">
                                                <label>Home Address:</label>
                                                <textarea class="form-control" name="visitor_address" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <label>Image Upload(optional)</label>
                                            <div id="image_container"></div>
                                            <p class="help-block">Please upload CNIC front & Back or Passport Bio page scanned copy</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label>Field Must Be Filled</label>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th colspan="2" width="50%">CNIC Number (Local Visitor)</th>
                                            <th colspan="2" width="50%">Passport Number (Intl Visitor)</th>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><input type="text" class="form-control" name="visitor_cnic"></td>
                                            <td colspan="2"><input type="text" class="form-control" name="visitor_passport"></td>
                                        </tr>
                                        <tr>
                                            <th>Date Of Issuance</th>
                                            <th>Date Of Expiry</th>
                                            <th colspan="2">Date Of Expiry</th>
                                        </tr>
                                        <tr>
                                            <td><input type="date" class="form-control" name="visitor_cnic_date_issue"></td>
                                            <td><input type="date" class="form-control" name="visitor_cnic_date_expire"></td>
                                            <td colspan="2"><input type="date" class="form-control" name="visitor_passport_date_expire"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="text-right"><label class="text-red">Lifetime Expire <input type="checkbox" name="visitor_cnic_has_lifetime"></label></td>
                                            <th>Gender <span class="text-red">*</span></th>
                                            <td>
                                                <label><input type="radio" name="visitor_gender" value="male"> Male</label>
                                                <label><input type="radio" name="visitor_gender" value="female"> Female</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">Country <span class="text-red">*</span></th>
                                            <th colspan="2">Date of Birth <span class="text-red">*</span></th>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><input type="text" class="form-control" name="visitor_country" list="data_country"></td>
                                            <td colspan="2"><input type="date" class="form-control" name="visitor_dob"></td>
                                        </tr>
                                    </table>

                                </div>
                            </div>

                            <h3>Visitor's Organization Detail</h3>

                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Organization Name:
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" class="form-control" name="company_name">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Organization Business Sector:
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" class="form-control" name="company_business_sector">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>Country
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" class="form-control" name="company_country" list="data_country">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>City:</label>
                                        <input type="text" class="form-control" name="company_city">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>Telephone Number:
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" class="form-control" name="company_phone">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Fax Number:</label>
                                        <input type="text" class="form-control" name="company_fax">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Email:
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="email" class="form-control" name="company_email">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Business Address:</label>
                                        <input type="text" class="form-control" name="company_address">
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
											->where('type', 'trade_visitor_badge_terms')
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
                                <th>Exhibit Name</th>
                                <th>Visitor Name</th>
                                <th>CNIC Number</th>
                                <th>Gender</th>
                                <th>Date Of Birth</th>
                                <th>Current Age</th>
                                <th>CNIC Expiry</th>
                                <th>Passport Expiry</th>
                                <th>Country</th>
                                <th>Cell Phone #</th>
                                <th>Organization Name</th>
                                <th>Business Sector</th>
                                <th>Action</th
                            </tr>
                            </thead>
                            <tbody>
							<?php
							if (!is_null($this->formdata)) {
								foreach ($this->formdata->badge as $count => $row) {
								    $age = 0;
								    if ($row->visitor_dob && $row->visitor_dob != '') {
										$age = (date('Y') - date('Y',strtotime($row->visitor_dob)));
                                    }

                                    $badge_data = $this->db
                                        ->where('id', $row->badge_id)
										->where('badge_type', 'visitor')
                                        ->where('is_active', 1)
                                        ->get('es_exhibition_badges')
                                        ->row();

									$html = '<tr>
                                        <td>'. ($count + 1) .'</td>
                                        <td>'. $this->userdata->company.'</td>
                                        <td>'. $row->visitor_name.'</td>
                                        <td>'. $row->visitor_cnic.'</td>
                                        <td>'. $row->visitor_gender.'</td>
                                        <td>'. $row->visitor_dob.'</td>
                                        <td>'. $age.'</td>
                                        <td>'. (($row->visitor_cnic_date_expire) ? $row->visitor_cnic_date_expire : '-') .'</td>
                                        <td>'. $row->visitor_passport_date_expire.'</td>
                                        <td>'. $row->visitor_country.'</td>
                                        <td>'. $row->visitor_mobile.'</td>
                                        <td>'. $row->company_name.'</td>
                                        <td>'. $row->company_business_sector.'</td>
                                        <td>';
									if ($badge_data && $badge_data->is_printed == 1) {
										$html .= '<a href="' . base_url('forms/form_10/delete_badge?id=' . $row->id) . '">Remove</a>';
									}
									$html .= '</td>
                                        </tr>';
									echo $html;
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
			'urlValidator': "<?php echo base_url("forms/form_10/form_10_validate/doError"); ?>",
			'loadingImg':   "<?php echo base_url("../assets/img/load-indicator.gif"); ?>"
		});
	});
	var file = new file_upload_preview({
		selector:       '#image_container',
		ajax_src:       '<?= base_url('welcome/file_upload') ?>',
		extensions:     'jpg|jpeg|png|PNG',
		base_url:       '../uploads/badge_cnic/',
		post_file_name: 'visitor_image',
		has_rotation:   false,
		max_upload:     1,
		on_upload:      function (file_name) {
			$('#image_container .imgbox img').attr('src', '<?= base_url('../uploads/badge_cnic/') ?>' + file_name)
		}
	});
	$(document).on('change', '[name="visitor_cnic_has_lifetime"]', function (e) {
		e.stopImmediatePropagation();

        $('[name="visitor_cnic_date_expire"]').attr('readonly', ($(this).is(':checked')))
	});
</script>
</body>
</html>
