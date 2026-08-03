<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<?php
$exhibitors_badges = $this->db
	->where('exhibition_id', $this->event->id)
	->where('booking_id', $this->booking->id)
	->where('badge_type', 'exhibitor')
	->where('is_active', 1)
	->get('es_exhibition_badges')
	->result();


$active_invitation_types = $this->db
	->where('exhibition_id', $this->event->id)
	->where('booking_id', $this->booking->id)
	->where('is_active', 1)
	->where('badge_type', 'exhibitor')
	->get('es_exhibition_badges_limit')
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
    .nav-pills>li:not(:last-child).active a {
        padding-right: 40px;
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

    .additional-box {
        text-align: center;
        background: #db4c3b;
        padding: 10px 0;
        color: #fff;
        border-radius: 4px;
    }
    .additional-box h4 {
        margin: 0;
    }

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

    .invitation_select_table.table > tbody > tr > td,
    .invitation_select_table.table > tbody > tr > th {
        vertical-align: middle;
    }
</style>
<div class="content-wrapper" data-page="form_19">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="pull-left">FORM 16</h1>

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
        <h1 class="text-center">Exhibitors’ Badges</h1>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-10">
                                <ul class="nav nav-pills thumbnail setup-panel">
                                    <li class="active" data-step="1">
                                        <a href="#step-1">
                                            <h4 class="list-group-item-heading">Exhibitor Badges Entry</h4>
                                        </a>
                                    </li>
                                    <li class="" data-step="2">
                                        <a href="#step-2">
                                            <h4 class="list-group-item-heading">Invitation Selection</h4>
                                        </a>
                                    </li>
                                    <li class="" data-step="3">
                                        <a href="#step-3">
                                            <h4 class="list-group-item-heading">Collection Person</h4>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-sm-2">
                                <div class="additional-box">
                                    <p>Total Badges</p>
                                    <h4><?= count($exhibitors_badges) ?>/<?= $this->booking->badges_total_limit ?></h4>
                                </div>
                            </div>
                            <!--<div class="col-sm-2">
                                <div class="additional-box">
                                    <p>Additional Badges Purchased</p>
                                    <h4>--/--</h4>
                                </div>
                            </div>-->
                        </div>


                        <div class="setup-content" id="step-1">
                            <form action="<?= base_url('forms/form_19/form_19_submit') ?>" method="post"
                                  id="crd_form"
                                  enctype="multipart/form-data">

                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label>Person Full Name (Max 36) <span class="text-red">*</span></label>
                                        <input type="text" class="form-control" name="full_name" maxlength="36">
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Designation (Max 36) <span class="text-red">*</span></label>
                                        <input type="text" class="form-control" name="designation" maxlength="36">
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Mobile Number <span class="text-red">*</span></label>
                                        <input type="text" class="form-control" name="mobile">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label>Nationality <span class="text-red">*</span></label>
                                        <select class="form-control nationality" name="nationality">
                                            <option value="">- select -</option>
											<?= get_instance()->funcs->print_input_data_list('nationality'); ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Email Address</label>
                                        <input type="text" class="form-control" name="email">
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Picture <span class="text-red">*</span></label>
                                        <div id="pic_container"></div>
                                    </div>
                                </div>

                                <div class="form-group row cnic_area" style="display: none">
                                    <div class="col-sm-4">
                                        <label>CNIC <span class="text-red">*</span></label>
                                        <input type="text" class="form-control cnic_field" name="cnic">
                                        <p class="small help-block">Only valid NIC Will be entertain</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Upload CNIC <small>(Front Side)</small></label>
                                        <div id="cnic_container"></div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Upload CNIC <small>(Back Side)</small></label>
                                        <div id="cnic_back_container"></div>
                                    </div>
                                </div>

                                <div class="form-group row passport_area" style="display: none">
                                    <div class="col-sm-4">
                                        <label>Passport number</label>
                                        <input type="text" class="form-control" name="passport">
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Upload Passport <small>(First Page)</small></label>
                                        <div id="passport_container"></div>
                                    </div>
                                    <div class="col-sm-4 passport_visa_container">
                                        <label>Upload Passport <small>(Visa Page)</small></label>
                                        <div id="passport_back_container"></div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-xs-10">

                                    </div>
                                    <div class="col-xs-2">
                                        <a href="javascript:void(0);"
                                           class="btn btn-primary btn-lg btn-block margin-bottom js-form_btn">Add</a>
                                    </div>
                                </div>
                            </form>

                            <hr>

                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Person Full Name</th>
                                    <th>Designation</th>
                                    <th>Mobile #</th>
                                    <th>Nationality</th>
                                    <th>CNIC / Passport</th>
                                    <th>Email Address</th>
                                    <th>Created Date</th>
                                    <th>Picture</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (!is_null($this->formdata)) {
									foreach ($exhibitors_badges as $badge) {
										$action = '';
										if($badge->is_printed == 1){
											$action .= 'Your card is printed';
										} else{
											$action .= '<a href="javascript:void(0)" class="edit_badge_btn" data-id="' . $badge->id . '">Edit</a>';
											$action .= '| <a href="javascript:void(0)" class="delete_badge_btn" data-id="' . $badge->id . '">Delete</a>';
										}	
										
										
                                        $html = '<tr>
                                        <td>'. $badge->full_name .'</td>
                                        <td>'. $badge->designation .'</td>
                                        <td>'. $badge->mobile .'</td>
                                        <td>'. $badge->nationality .'</td>
                                        <td>'. (($badge->nationality == 'Pakistani') ? $badge->cnic : $badge->passport) .'</td>
                                        <td>'. $badge->email .'</td>
                                        <td>'. date('d/m/Y', strtotime($badge->created_on)) .'</td>
                                        <td>
                                        <a href="'. base_url($badge->user_image) .'" target="_blank">
                                        <img src="'. base_url($badge->user_image) .'" alt="" width="50px">
                                        </a>
                                        </td>
                                        <td>'. $action .'</td>
                                        </tr>';

                                        echo $html;
                                    }
                                } else {
                                    echo '<tr><td colspan="9">No record found!</td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="setup-content" id="step-2">
                            <table class="table table-bordered invitation_select_table">
                                <tr>
                                    <th width="20%" rowspan="2" style="vertical-align: bottom">Person Name</th>
									<?php
									foreach ($active_invitation_types as $invitation_type) {
										echo '<th width="12%">'. ucwords(str_replace('_', ' ', $invitation_type->invitation_type)) .'</th>';
									}
									?>
                                    <!-- <th width="12%">Inauguration</th>
                                    <th width="12%">Seminar</th>
                                    <th width="12%">Governor Reception</th>
                                    <th width="12%">Gala Dinner</th>
                                    <th width="12%">Karachi Air Show</th>
                                    <th width="12%">CM Reception</th> -->
                                    <th width="8%" rowspan="2"></th>
                                </tr>
                                <tr class="invitation_limit_row">
									<?php
									foreach ($active_invitation_types as $invitation_type) {
										echo '<td data-type="'.$invitation_type->invitation_type.'">[0/1]</td>';
									}
									?>
                                    <!-- <td data-type="inauguration">[0/1]</td>
                                    <td data-type="seminar">[0/1]</td>
                                    <td data-type="governor_reception">[0/1]</td>
                                    <td data-type="gala_dinner">[0/1]</td>
                                    <td data-type="karachi_air_show">[0/1]</td>
                                    <td data-type="cm_reception">[0/1]</td> -->
                                </tr>
                                <tr class="bg-warning inv_update_row">
                                    <td>
                                        <select class="form-control badge_id">
                                            <option value="">- select -</option>
                                            <?php
                                            foreach ($exhibitors_badges as $exhibitors_badge) {
                                                echo '<option value="'.$exhibitors_badge->id.'">'.$exhibitors_badge->full_name.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
									<?php
									foreach ($active_invitation_types as $invitation_type) {
										echo '<td><input type="checkbox" class="icheck invitations" value="'.$invitation_type->invitation_type.'"></td>';
									}
									?>
                                    <!-- <td><input type="checkbox" class="icheck invitations" value="inauguration"></td>
                                    <td><input type="checkbox" class="icheck invitations" value="seminar"></td>
                                    <td><input type="checkbox" class="icheck invitations" value="governor_reception"></td>
                                    <td><input type="checkbox" class="icheck invitations" value="gala_dinner"></td>
                                    <td><input type="checkbox" class="icheck invitations" value="karachi_air_show"></td>
                                    <td><input type="checkbox" class="icheck invitations" value="cm_reception"></td> -->
                                    <td>
                                        <button type="button" class="btn btn-success btn-block add_invitations_btn">SAVE</button>
                                    </td>
                                </tr>
                            </table>

                            <hr>

                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th width="20%">Exhibitor Name</th>
									<?php
									foreach ($active_invitation_types as $invitation_type) {
										echo '<th width="12%">'.$invitation_type->invitation_type.'</th>';
									}
									?>
                                    <!-- <th width="12%">Inauguration</th>
                                    <th width="12%">Seminar</th>
                                    <th width="12%">Governor Reception</th>
                                    <th width="12%">Gala Dinner</th>
                                    <th width="12%">Karachi Air Show</th>
                                    <th width="12%">CM Reception</th> -->
                                    <th width="8%"></th>
                                </tr>
                                </thead>
                                <tbody id="exhibitor_invited_list"></tbody>
                            </table>

                        </div>
                        <div class="setup-content" id="step-3">
                            <form action="<?= base_url('forms/form_19/update_collection_person') ?>" method="post">
                                <?php
                                $collection_data = $this->db
									->where('exhibition_id', $this->event->id)
									->where('booking_id', $this->booking->id)
									->get('es_exhibition_badges_collection')
                                    ->row();
                                ?>
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="collection_person_name">Collection Person Name</label>
                                        <input type="text" class="form-control" name="collection_person_name"
                                               value="<?= (isset($collection_data)) ? $collection_data->collection_person_name : ''; ?>"
                                               placeholder="Collection Person Name">
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Collection Person CNIC</label>
                                        <input type="number" class="form-control" name="collection_person_cnic"
                                               value="<?= (isset($collection_data)) ? $collection_data->collection_person_cnic : ''; ?>"
                                               placeholder="Collection Person CNIC">
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Collection Person Phone</label>
                                        <input type="number" class="form-control" name="collection_person_phone"
                                               value="<?= (isset($collection_data)) ? $collection_data->collection_person_phone : ''; ?>"
                                               placeholder="Collection Person Phone">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 text-right">
                                        <button type="submit" class="btn btn-primary btn-lg">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->

                <!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.content -->
    <!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- Lightbox for  -->
<div class="modal fade" id="edit_exhibitor_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Edit Badge</h4>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('forms/form_19/edit_exhibitor_submit') ?>" method="post"
                      id="edit_exhibitor_form"
                      enctype="multipart/form-data">

                    <input type="hidden" name="badge_id">

                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label>Person Full Name (Max 36) <span class="text-red">*</span></label>
                            <input type="text" class="form-control" name="full_name" maxlength="36">
                        </div>
                        <div class="col-sm-4">
                            <label>Designation (Max 36) <span class="text-red">*</span></label>
                            <input type="text" class="form-control" name="designation" maxlength="36">
                        </div>
                        <div class="col-sm-4">
                            <label>Mobile Number <span class="text-red">*</span></label>
                            <input type="text" class="form-control" name="mobile">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label>Nationality <span class="text-red">*</span></label>
                            <select class="form-control edit_nationality" name="nationality">
                                <option value="">- select -</option>
								<?= get_instance()->funcs->print_input_data_list('nationality'); ?>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Email Address</label>
                            <input type="text" class="form-control" name="email">
                        </div>
                        <div class="col-sm-4">
                            <label>Picture <span class="text-red">*</span></label>
                            <div id="edit_pic_container"></div>
                        </div>
                    </div>

                    <div class="form-group row edit_cnic_area" style="display: none">
                        <div class="col-sm-4">
                            <label>CNIC <span class="text-red">*</span></label>
                            <input type="text" class="form-control cnic_field" name="cnic">
                        </div>
                        <div class="col-sm-4">
                            <label>Upload CNIC <small>(Front Side)</small></label>
                            <div id="edit_cnic_container"></div>
                        </div>
                        <div class="col-sm-4">
                            <label>Upload CNIC <small>(Back Side)</small></label>
                            <div id="edit_cnic_back_container"></div>
                        </div>
                    </div>

                    <div class="form-group row edit_passport_area">
                        <div class="col-sm-4">
                            <label>Passport</label>
                            <input type="text" class="form-control" name="passport">
                        </div>
                        <div class="col-sm-4">
                            <label>Upload Passport <small>(First Page)</small></label>
                            <div id="edit_passport_container"></div>
                        </div>
                        <div class="col-sm-4">
                            <label>Upload Passport <small>(Visa Page)</small></label>
                            <div id="edit_passport_back_container"></div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success edit_exhibitor_form_btn" form="edit_exhibitor_form">Update</button>
            </div>
        </div>
    </div>
</div> <!-- /.modal -->

<?php $this->load->view('includes/after_login/footer'); ?>

<script>
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

	$(function () {
		doFormValidation({
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("forms/form_19/form_19_validate/doError"); ?>",
			'loadingImg':   "<?php echo base_url("../assets/img/load-indicator.gif"); ?>"
		});

		doFormValidation({
			'form':         '#edit_exhibitor_form',
			'msgbox':       '#edit_exhibitor_form .js-msgbox',
			'btnClick':     '.edit_exhibitor_form_btn',
			'urlValidator': "<?php echo base_url("forms/form_19/edit_exhibitor_validate/doError"); ?>",
			'loadingImg':   "<?php echo base_url("../assets/img/load-indicator.gif"); ?>"
		});

		$('.cnic_field').inputmask("99999-9999999-9")
		$('[name="mobile"]').inputmask("+99-999-99999999");
	});

	var file = new file_upload_preview({
		selector: '#pic_container',
		ajax_src: '<?= base_url('welcome/file_upload') ?>',
		extensions: 'jpg|jpeg|png|PNG',
		base_url: '../uploads/badge_cnic/',
		post_file_name: 'user_image',
		has_rotation: false,
		max_upload: 1,
		on_upload: function (file_name) {
			$('#pic_container .imgbox img').attr('src', '<?= base_url('../uploads/badge_cnic/') ?>' + file_name)
		}
	});

	var file2 = new file_upload_preview({
		selector: '#cnic_container',
		ajax_src: '<?= base_url('welcome/file_upload') ?>',
		extensions: 'jpg|jpeg|png|PNG',
		base_url: '../uploads/badge_cnic/',
		post_file_name: 'cnic_image',
		has_rotation: false,
		max_upload: 1,
		on_upload: function (file_name) {
			$('#cnic_container .imgbox img').attr('src', '<?= base_url('../uploads/badge_cnic/') ?>' + file_name)
		}
	});
	var file3 = new file_upload_preview({
		selector: '#cnic_back_container',
		ajax_src: '<?= base_url('welcome/file_upload') ?>',
		extensions: 'jpg|jpeg|png|PNG',
		base_url: '../uploads/badge_cnic/',
		post_file_name: 'cnic_back_image',
		has_rotation: false,
		max_upload: 1,
		on_upload: function (file_name) {
			$('#cnic_back_container .imgbox img').attr('src', '<?= base_url('../uploads/badge_cnic/') ?>' + file_name)
		}
	});
	var file4 = new file_upload_preview({
		selector: '#passport_container',
		ajax_src: '<?= base_url('welcome/file_upload') ?>',
		extensions: 'jpg|jpeg|png|PNG',
		base_url: '../uploads/badge_cnic/',
		post_file_name: 'passport_image',
		has_rotation: false,
		max_upload: 1,
		on_upload: function (file_name) {
			$('#passport_container .imgbox img').attr('src', '<?= base_url('../uploads/badge_cnic/') ?>' + file_name)
		}
	});
	var file5 = new file_upload_preview({
		selector: '#passport_back_container',
		ajax_src: '<?= base_url('welcome/file_upload') ?>',
		extensions: 'jpg|jpeg|png|PNG',
		base_url: '../uploads/badge_cnic/',
		post_file_name: 'passport_back_image',
		has_rotation: false,
		max_upload: 1,
		on_upload: function (file_name) {
			$('#passport_back_container .imgbox img').attr('src', '<?= base_url('../uploads/badge_cnic/') ?>' + file_name)
		}
	});

	$(document).on('click', '.edit_badge_btn', function (e) {
		e.stopImmediatePropagation();

		var id = $(this).attr('data-id');
		$('#edit_exhibitor_form')[0].reset();
		$.ajax({
			type:    'post',
			url:     '<?= base_url("forms/form_19/get_badge_invitations"); ?>',
			data:    {badge_id: id},
			success: function (data) {
				data = JSON.parse(data);

				if (data.error) {
					alert(data.message);
					return;
				}

				data = data.data[0];
				console.log(data);

				$('#edit_pic_container').html('');
				$('#edit_cnic_container').html('');
				$('#edit_cnic_back_container').html('');
				$('#edit_passport_container').html('');
				$('#edit_passport_back_container').html('');

				$('#edit_exhibitor_form [name="badge_id"]').val(data.id);
				$('#edit_exhibitor_form [name="full_name"]').val(data.full_name);
				$('#edit_exhibitor_form [name="designation"]').val(data.designation);
				$('#edit_exhibitor_form [name="mobile"]').val(data.mobile);
				$('#edit_exhibitor_form [name="nationality"]').val(data.nationality);
				$('#edit_exhibitor_form [name="cnic"]').val(data.cnic);
				$('#edit_exhibitor_form [name="passport"]').val(data.passport);
				$('#edit_exhibitor_form [name="email"]').val(data.email);

				if (data.nationality == 'Pakistani') {
					$('#edit_exhibitor_form .edit_cnic_area').show();
                } else {
					$('#edit_exhibitor_form .edit_cnic_area').hide();
                }

				var file_edit = new file_upload_preview({
					selector: '#edit_pic_container',
					ajax_src: '<?= base_url('welcome/file_upload') ?>',
					extensions: 'jpg|jpeg|png|PNG',
					base_url: '../uploads/badge_cnic/',
					post_file_name: 'user_image',
					has_rotation: false,
					max_upload: 1,
					on_upload: function (file_name) {
						$('#edit_pic_container .imgbox img').attr('src', '<?= base_url('../uploads/badge_cnic/') ?>' + file_name)
					},
					on_init: function () {
						$('#edit_pic_container .imgbox img').attr('src', '<?= base_url() ?>' + data.user_image.split(',')[0])
					},
					predefined_images: data.user_image.split(',')
				});

				var file2_edit = new file_upload_preview({
					selector: '#edit_cnic_container',
					ajax_src: '<?= base_url('welcome/file_upload') ?>',
					extensions: 'jpg|jpeg|png|PNG',
					base_url: '../uploads/badge_cnic/',
					post_file_name: 'cnic_image',
					has_rotation: false,
					max_upload: 1,
					on_upload: function (file_name) {
						$('#edit_cnic_container .imgbox img').attr('src', '<?= base_url('../uploads/badge_cnic/') ?>' + file_name)
					},
					on_init: function () {
						$('#edit_cnic_container .imgbox img').attr('src', '<?= base_url() ?>' + data.cnic_image.split(',')[0])
					},
					predefined_images: data.cnic_image.split(',')
				});
				var file3_edit = new file_upload_preview({
					selector: '#edit_cnic_back_container',
					ajax_src: '<?= base_url('welcome/file_upload') ?>',
					extensions: 'jpg|jpeg|png|PNG',
					base_url: '../uploads/badge_cnic/',
					post_file_name: 'cnic_back_image',
					has_rotation: false,
					max_upload: 1,
					on_upload: function (file_name) {
						$('#edit_cnic_back_container .imgbox img').attr('src', '<?= base_url('../uploads/badge_cnic/') ?>' + file_name)
					},
					on_init: function () {
						$('#edit_cnic_back_container .imgbox img').attr('src', '<?= base_url() ?>' + data.cnic_back_image.split(',')[0])
					},
					predefined_images: data.cnic_back_image.split(',')
				});
				var file4_edit = new file_upload_preview({
					selector: '#edit_passport_container',
					ajax_src: '<?= base_url('welcome/file_upload') ?>',
					extensions: 'jpg|jpeg|png|PNG',
					base_url: '../uploads/badge_cnic/',
					post_file_name: 'passport_image',
					has_rotation: false,
					max_upload: 1,
					on_upload: function (file_name) {
						$('#edit_passport_container .imgbox img').attr('src', '<?= base_url('../uploads/badge_cnic/') ?>' + file_name)
					},
					on_init: function () {
						$('#edit_passport_container .imgbox img').attr('src', '<?= base_url() ?>' + data.passport_image.split(',')[0])
					},
					predefined_images: data.passport_image.split(',')
				});
				var file5_edit = new file_upload_preview({
					selector: '#edit_passport_back_container',
					ajax_src: '<?= base_url('welcome/file_upload') ?>',
					extensions: 'jpg|jpeg|png|PNG',
					base_url: '../uploads/badge_cnic/',
					post_file_name: 'passport_back_image',
					has_rotation: false,
					max_upload: 1,
					on_upload: function (file_name) {
						$('#edit_passport_back_container .imgbox img').attr('src', '<?= base_url('../uploads/badge_cnic/') ?>' + file_name)
					},
					on_init: function () {
						$('#edit_passport_back_container .imgbox img').attr('src', '<?= base_url() ?>' + data.passport_back_image.split(',')[0])
					},
					predefined_images: data.passport_back_image.split(',')
				});

				$('#edit_exhibitor_modal').modal({backdrop: 'static', keyboard: false, show: true}); // open lightbox
			},
			error:   function () {

			}
		});
	});

	$(document).on('click', '.delete_badge_btn', function (e) {
		e.stopImmediatePropagation();

		var id = $(this).attr('data-id');

		swal({
			title: '',
			text: 'Are you sure you want delete this badge?',
			type: "warning",
			showCancelButton: true,
			confirmButtonText: "Yes",
		}, function () {
			$.ajax({
				type:    'post',
				url:     '<?= base_url("forms/form_19/disable_exhibitor_badge"); ?>',
				data:    {badge_id: id},
				success: function (data) {
					data = JSON.parse(data);
					console.log(data);

					if (data.error) {
						alert(data.message);
						return;
					}

					window.location = data.data;
				},
				error:   function () {
				}
			});
		});

	});

	$(document).on('change', '.nationality', function (e) {
		e.stopImmediatePropagation();

		$('.cnic_area').hide();
		$('.passport_area').show();
		$('.passport_visa_container').show();
		if ($(this).val() == 'Pakistani') {
			$('.cnic_area').show();
			$('.passport_visa_container').hide();
        }
	});

	$(document).on('change', '.edit_nationality', function (e) {
		e.stopImmediatePropagation();

		$('.edit_cnic_area').hide();
		$('.edit_passport_area').show();
		if ($(this).val() == 'Pakistani') {
			$('.edit_cnic_area').show();
        }
	});

	// invite exhibitor
	$(document).on('click', '.add_invitations_btn', function (e) {
		e.stopImmediatePropagation();
		var badge_id = $('.badge_id').val();
		var inv = [];
		$('.invitations:checked').each(function () {
            inv.push($(this).val())
		});

		if (!badge_id || badge_id == '') {
			alert('Please select person!');
			return;
        }
        if (inv.length == 0) {
			//alert('Please select atleast one invitation!');
			//return;
        }

		$.ajax({
			type:    'post',
			url:     '<?= base_url("forms/form_19/update_badge_invitations"); ?>',
			data:    {
				badge_id: badge_id,
                invitations: inv
            },
			success: function (data) {
                data = JSON.parse(data);
				console.log(data);

				if (data.error) {
					swal({
						title: '',
						text: data.message,
						type: "warning"
					});
					return;
                }

				$('.badge_id').val('');
				$('.invitations').iCheck('uncheck');

				swal({
					title: 'Success',
					text: data.message,
					type: "success"
				});

				render_exhibitor_invited_list();
				render_invitation_limit_row();
			},
			error:   function () {

			}
		});
	});


	$(document).on('click', '.edit_invitation_btn', function (e) {
		e.stopImmediatePropagation();

        var id = $(this).attr('data-id');

		$('.badge_id').val(id);
		$('.invitations').iCheck('uncheck');
		$.ajax({
			type:    'post',
			url:     '<?= base_url("forms/form_19/get_badge_invitations"); ?>',
			data:    {badge_id: id},
			success: function (data) {
				data = JSON.parse(data);

				if (data.error) {
					alert(data.message);
					return;
				}

				for (var i=0; i<data.data.length; i++) {
					for (var j=0; j<data.data[i].invitations.length; j++) {
						$('.invitations[value="'+data.data[i].invitations[j]+'"]').iCheck('check');
                    }
                }

				$('.inv_update_row').effect('highlight');
			},
			error:   function () {

			}
		});
	});

	function render_exhibitor_invited_list() {
        $('#exhibitor_invited_list').html('');

		$.ajax({
			type:    'post',
			url:     '<?= base_url("forms/form_19/get_badge_invitations"); ?>',
			data:    {},
			success: function (data) {
				data = JSON.parse(data);
				console.log(data);

				if (data.error) {
					alert(data.message);
					return;
				}

				var html = '';
				for (var i=0; i<data.data.length; i++) {

					html = '<tr class="text-center">';
                    html += '<td class="text-left">'+data.data[i].full_name+'</td>';
					<?php foreach ($active_invitation_types as $invitation_type) { ?>
					html += ((data.data[i].invitations.indexOf('<?= $invitation_type->invitation_type ?>') >= 0) ? '<td class="bg-success"><i class="fa fa-check text-success"></i></td>' : '<td class="bg-danger"><i class="fa fa-times text-danger"></i></td>');	
					<?php } ?>
                    // html += ((data.data[i].invitations.indexOf('inauguration') >= 0) ? '<td class="bg-success"><i class="fa fa-check text-success"></i></td>' : '<td class="bg-danger"><i class="fa fa-times text-danger"></i></td>');
                    // html += ((data.data[i].invitations.indexOf('seminar') >= 0) ? '<td class="bg-success"><i class="fa fa-check text-success"></i></td>' : '<td class="bg-danger"><i class="fa fa-times text-danger"></i></td>');
                    // html += ((data.data[i].invitations.indexOf('governor_reception') >= 0) ? '<td class="bg-success"><i class="fa fa-check text-success"></i></td>' : '<td class="bg-danger"><i class="fa fa-times text-danger"></i></td>');
                    // html += ((data.data[i].invitations.indexOf('gala_dinner') >= 0) ? '<td class="bg-success"><i class="fa fa-check text-success"></i></td>' : '<td class="bg-danger"><i class="fa fa-times text-danger"></i></td>');
                    // html += ((data.data[i].invitations.indexOf('karachi_air_show') >= 0) ? '<td class="bg-success"><i class="fa fa-check text-success"></i></td>' : '<td class="bg-danger"><i class="fa fa-times text-danger"></i></td>');
                    // html += ((data.data[i].invitations.indexOf('cm_reception') >= 0) ? '<td class="bg-success"><i class="fa fa-check text-success"></i></td>' : '<td class="bg-danger"><i class="fa fa-times text-danger"></i></td>');
					<?php if (isset($this->booking->allow_invitation_edit) && $this->booking->allow_invitation_edit == 1) { ?>
                    html += '<td><a href="javascript:void(0)" class="edit_invitation_btn" data-id="'+data.data[i].id+'">Edit</a></td>';
					<?php } else { ?>
                    html += '<td>-</td>';
					<?php } ?>
					html += '</tr>';
					$('#exhibitor_invited_list').append(html);
                }
			},
			error:   function () {

			}
		});
	}

	function render_invitation_limit_row() {
		$.ajax({
			type:    'post',
			url:     '<?= base_url("forms/form_19/get_badge_invitation_limit"); ?>',
			data:    {},
			success: function (data) {
				data = JSON.parse(data);
				console.log('aaaa', data);

				if (data.error) {
					alert(data.message);
					return;
				}

				for (var i=0; i<data.data.length; i++) {
					$('.invitation_limit_row td[data-type="'+data.data[i].invitation_type+'"]').html('['+data.data[i].total+'/'+data.data[i].quantity+']');
				}
			},
			error:   function () {
			}
		});
	}

	render_exhibitor_invited_list();
	render_invitation_limit_row();
</script>
</body>
</html>
