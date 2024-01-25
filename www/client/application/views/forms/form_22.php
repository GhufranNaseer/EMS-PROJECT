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
<div class="content-wrapper" data-page="form_22">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="pull-left">FORM 19</h1>

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
        <h1 class="text-center">Display of Exhibit – End User Certificate:</h1>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <form action="<?= (is_null($this->editdata)) ? base_url('forms/form_22/form_22_submit') : base_url('forms/form_22/edit_submit?id=' . $this->editdata->id) ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">

                            <h4>Exhibitors are require to complete the following declaration:</h4>
                            <hr>

                            <div class="row">
                                <div class="col-sm-6">
                                    <h4>Product Information</h4>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Product Name <span class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="product_name"
                                                       value="<?= (!is_null($this->editdata)) ? $this->editdata->product_name : '' ?>">
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label>Quantity <span class="text-red">*</span></label>
                                                        <input type="number" class="form-control" name="product_quantity"
                                                               value="<?= (!is_null($this->editdata)) ? $this->editdata->product_quantity : '' ?>">
                                                    </div>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="form-group">
                                                        <label>Product Category <span class="text-red">*</span></label>
                                                        <select name="product_category" class="form-control">
                                                            <option value="">- select -</option>
                                                            <option value="Model" <?= (!is_null($this->editdata) && $this->editdata->product_category == 'Model') ? 'selected' : '' ?>>Model</option>
                                                            <option value="Dummy" <?= (!is_null($this->editdata) && $this->editdata->product_category == 'Dummy') ? 'selected' : '' ?>>Dummy</option>
                                                            <option value="Inert" <?= (!is_null($this->editdata) && $this->editdata->product_category == 'Inert') ? 'selected' : '' ?>>Inert</option>
                                                            <option value="Actual" <?= (!is_null($this->editdata) && $this->editdata->product_category == 'Actual') ? 'selected' : '' ?>>Actual</option>
                                                            <option value="Cutaway" <?= (!is_null($this->editdata) && $this->editdata->product_category == 'Cutaway') ? 'selected' : '' ?>>Cutaway</option>
                                                            <option value="Others" <?= (!is_null($this->editdata) && $this->editdata->product_category == 'Others') ? 'selected' : '' ?>>Others</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Description <span class="text-red">*</span></label>
                                                <textarea name="product_description" class="form-control" rows="5"><?= (!is_null($this->editdata)) ? $this->editdata->product_description : '' ?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <p><strong>Please return this certificate.</strong></p>
                                    <p>The official freight forwarder will obtain the release of your consignment from the Pakistan Customs by showing this document.</p>
                                    <div class="form-group">
                                        <div class="well well-sm" style="height: 100px; overflow: auto">
                                            <?php
                                            $note = $this->db
                                                ->where('exhibition_id', $this->event->id)
                                                ->where('type', 'end_user_note')
                                                ->order_by('id', 'DESC')
                                                ->get('es_exhibition_notification')
                                                ->row();

                                            if ($note) {
                                                echo $note->title;
                                            } else {
                                                echo '[Important Note:]';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label><input type="checkbox" class="icheck" name="is_agree" <?= (!is_null($this->editdata)) ? 'checked' : '' ?>> Read & Agreed!</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h4>Packaging Information</h4>
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label>Package Quantity <span class="text-red">*</span></label>
                                            <input type="number" class="form-control" min="1" name="package_quantity"
                                                   value="<?= (!is_null($this->editdata)) ? $this->editdata->package_quantity : '' ?>">
                                        </div>
                                        <div class="col-sm-4">
                                            <label>Mode of shipping <span class="text-red">*</span></label>
                                            <select name="package_mode" class="form-control">
                                                <option value="">- select -</option>
                                                <option value="Land" <?= (!is_null($this->editdata) && $this->editdata->package_mode == 'Land') ? 'selected' : '' ?>>Land</option>
                                                <option value="Air" <?= (!is_null($this->editdata) && $this->editdata->package_mode == 'Air') ? 'selected' : '' ?>>Air</option>
                                                <option value="Sea" <?= (!is_null($this->editdata) && $this->editdata->package_mode == 'Sea') ? 'selected' : '' ?>>Sea</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-4">
                                            <label>Packing Type <span class="text-red">*</span></label>
                                            <select name="package_mode_type" class="form-control">
                                                <option value="">- select -</option>
                                                <option value="Corrugated_Boxes" <?= (!is_null($this->editdata) && $this->editdata->package_mode_type == 'Corrugated_Boxes') ? 'selected' : '' ?>>Corrugated Boxes</option>
                                                <option value="Boxboard_Cartons" <?= (!is_null($this->editdata) && $this->editdata->package_mode_type == 'Boxboard_Cartons') ? 'selected' : '' ?>>Boxboard Cartons</option>
                                                <option value="Paperboard_Cartons" <?= (!is_null($this->editdata) && $this->editdata->package_mode_type == 'Paperboard_Cartons') ? 'selected' : '' ?>>Paperboard Cartons</option>
                                                <option value="Paper_bags" <?= (!is_null($this->editdata) && $this->editdata->package_mode_type == 'Paper_bags') ? 'selected' : '' ?>>Paper bags</option>
                                                <option value="Sacks" <?= (!is_null($this->editdata) && $this->editdata->package_mode_type == 'Sacks') ? 'selected' : '' ?>>Sacks</option>
                                                <option value="Wooden_Box" <?= (!is_null($this->editdata) && $this->editdata->package_mode_type == 'Wooden_Box') ? 'selected' : '' ?>>Wooden Box</option>
                                                <option value="Metal_Box" <?= (!is_null($this->editdata) && $this->editdata->package_mode_type == 'Metal_Box') ? 'selected' : '' ?>>Metal Box</option>
                                                <option value="Container_20ft" <?= (!is_null($this->editdata) && $this->editdata->package_mode_type == 'Container_20ft') ? 'selected' : '' ?>>Container 20ft</option>
                                                <option value="Container_40ft" <?= (!is_null($this->editdata) && $this->editdata->package_mode_type == 'Container_40ft') ? 'selected' : '' ?>>Container 40ft</option>
                                            </select>
                                        </div>
                                    </div>

                                    <p><strong>Package Dimensions</strong></p>
                                    <div class="form-group row">
                                        <div class="col-sm-2">
                                            <label>Length <span class="text-red">*</span></label>
                                            <input type="number" class="form-control" min="1" name="package_length"
                                                   value="<?= (!is_null($this->editdata)) ? $this->editdata->package_length : '' ?>">
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Breth <span class="text-red">*</span></label>
                                            <input type="number" class="form-control" min="1" name="package_breth"
                                                   value="<?= (!is_null($this->editdata)) ? $this->editdata->package_breth : '' ?>">
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Height <span class="text-red">*</span></label>
                                            <input type="number" class="form-control" min="1" name="package_height"
                                                   value="<?= (!is_null($this->editdata)) ? $this->editdata->package_height : '' ?>">
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Size <span class="text-red">*</span></label>
                                            <select name="package_size_units" class="form-control">
                                                <option value="">- select -</option>
                                                <option value="cm" <?= (!is_null($this->editdata) && $this->editdata->package_size_units == 'cm') ? 'selected' : '' ?>>cm</option>
                                                <option value="in" <?= (!is_null($this->editdata) && $this->editdata->package_size_units == 'in') ? 'selected' : '' ?>>in"</option>
                                                <option value="ft" <?= (!is_null($this->editdata) && $this->editdata->package_size_units == 'ft') ? 'selected' : '' ?>>Ft.</option>
                                                <option value="meter" <?= (!is_null($this->editdata) && $this->editdata->package_size_units == 'meter') ? 'selected' : '' ?>>Meter</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Weight <span class="text-red">*</span></label>
                                            <input type="number" class="form-control" min="1" name="package_weight"
                                                   value="<?= (!is_null($this->editdata)) ? $this->editdata->package_weight : '' ?>">
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Units <span class="text-red">*</span></label>
                                            <select name="package_weight_units" class="form-control">
                                                <option value="">- select -</option>
                                                <option value="lbs" <?= (!is_null($this->editdata) && $this->editdata->package_weight_units == 'lbs') ? 'selected' : '' ?>>lbs.</option>
                                                <option value="kgs" <?= (!is_null($this->editdata) && $this->editdata->package_weight_units == 'kgs') ? 'selected' : '' ?>>Kgs</option>
                                                <option value="ltr" <?= (!is_null($this->editdata) && $this->editdata->package_weight_units == 'ltr') ? 'selected' : '' ?>>ltr.</option>
                                                <option value="tons" <?= (!is_null($this->editdata) && $this->editdata->package_weight_units == 'tons') ? 'selected' : '' ?>>Tons</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <textarea name="package_remarks" class="form-control" rows="7"><?= (!is_null($this->editdata)) ? $this->editdata->package_remarks : '' ?></textarea>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Select Official Freight Forwarder</label>
                                            <select name="official_freight_forwarder" class="form-control">
                                                <option value="logistics">Logistics Link & Supplies Pvt. Let. (LLS)</option>
                                                <option value="bay_west">Bay West Pvt. Ltd.</option>
                                                <option value="both">Both the Forwader's</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <p class="bg-danger js-msgbox"></p>
                            <div class="row">
                                <div class="col-xs-10"></div>
                                <div class="col-xs-2">
                                    <a href="javascript:void(0);"
                                       class="btn btn-primary btn-block margin-bottom js-form_btn"><?= (is_null($this->editdata)) ? 'ADD' : 'UPDATE' ?></a>
                                </div>
                            </div>

                        </form>

                        <table class="table table-bordered table-striped" style="margin: 20px 0 40px;">
                            <thead>
                            <tr>
                                <th>Category</th>
                                <th>Description Of Exhibit</th>
                                <th>Quantity</th>
                                <th>Length</th>
                                <th>Breth</th>
                                <th>Height</th>
                                <th>Package Qty</th>
                                <th>Weight</th>
                                <th>Remarks</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (!is_null($this->formdata)) {
                                foreach ($this->formdata->products as $row) {
                                    echo '<tr>
                                        <td>'. $row->product_category .'</td>
                                        <td>'. $row->product_description .'</td>
                                        <td>'. $row->product_quantity .'</td>
                                        <td>'. $row->package_length .' '. $row->package_size_units .'</td>
                                        <td>'. $row->package_breth .' '. $row->package_size_units .'</td>
                                        <td>'. $row->package_height .' '. $row->package_size_units .'</td>
                                        <td>'. $row->package_quantity .'</td>
                                        <td>'. $row->package_weight .' '. $row->package_weight_units .'</td>
                                        <td>'. $row->package_remarks .'</td>
                                        <td>
                                        <a href="'. base_url('forms/form_22/edit_certificate?id='. $row->id ) .'">Edit</a>
                                        | <a href="'. base_url('forms/form_22/delete_certificate?id='. $row->id ) .'">Delete</a>
                                        </td>
                                        </tr>';
                                }
                            }
                            ?>
                            </tbody>
                        </table>

                        <?php if (isset($this->formdata->products) && count($this->formdata->products) > 0) { ?>
                        <div class="row">
                            <div class="col-sm-2 col-sm-offset-8">
                                <a href="<?= base_url('forms/form_22/print_certificate') ?>" class="btn btn-primary btn-block" target="_blank">View/Print Certificate</a>
                                <p class="text-center help-block">Print certificated for sign and stamp before upload</p>
                            </div>
                            <div class="col-sm-2">
                                <form action="<?= base_url('forms/form_22/upload_certificate') ?>" method="post" id="certificate_form"
                                      enctype="multipart/form-data">
                                    <a href="javascript:void(0)" class="btn btn-primary btn-block">
                                        Upload Certificate
                                        <input type="file" name="attested_certificate" class="hidden_file_input" id="attested_certificate">
                                    </a>
                                </form>

                                <?php if (isset($this->formdata->certificate)) { ?>
                                    <div class="text-center" style="margin-top: 10px">
                                        <a href="<?= base_url('../'. $this->formdata->certificate) ?>" target="_blank">
                                            View Signed Certificate
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>

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
			'urlValidator': "<?php echo base_url("forms/form_22/form_22_validate/doError"); ?>",
			'loadingImg':   "<?php echo base_url("../assets/img/load-indicator.gif"); ?>"
		});
	});
	$(document).on('change', '#attested_certificate', function (e) {
		e.stopImmediatePropagation();

		$('#certificate_form').submit();
	});

</script>
</body>
</html>
