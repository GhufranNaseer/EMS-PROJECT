<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<?php
$tab = 'bare';

if ($this->input->get('tab') && $this->input->get('tab') != '') {
    $tab = $this->input->get('tab');
}

$has_bare_stall = false;
$has_shell_stall = false;

if ($tab == 'shell') {
	$has_shell_stall = true;
}
if ($tab == 'bare') {
	$has_bare_stall = true;
}

$event_contractors = explode(',', $this->event->stall_builder_contractors);

$all_contractors = $this->db
	->where('is_active', 1)
	->where('is_deleted', 0)
	->get('es_stall_builders')
	->result();

$filter_contractors = array();
foreach ($all_contractors as $key => $contractor) {
	if (in_array(str_replace(' ', '_', $contractor->company_name), $event_contractors)) {
		$filter_contractors[str_replace(' ', '_', $contractor->company_name)] = $contractor;
	}
}
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
</style>

<div class="content-wrapper" data-page="form_01_<?= $tab ?>">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="pull-left"><?= ($has_bare_stall) ? 'Form 1A: Stall Builder' : 'Form 1B: Fascia Name' ?></h1>

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
        <h1 class="text-center"><?= ($has_bare_stall) ? 'Bare Space Information' : 'Shell Stall Name' ?></h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <form action="<?= base_url('forms/form_01/form_01_submit') ?>" method="post"
                              id="crd_form"
                              enctype="multipart/form-data">


                            <?php if ($has_bare_stall) { ?>
                                <input type="hidden" name="has_bare_stall" value="1">

                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-sm-4">
                                            <label>Select our official Contractor’s Company:</label>
                                            <?php
                                            $stalls_count = $this->db
                                                ->where('booking_id', $this->booking->id)
                                                ->where('is_deleted', 0)
                                                ->count_all_results('es_exhibition_stalls');


											$old_contractor = array();
											if (!is_null($this->formdata) && array_key_exists('stall_building_contractor', $this->formdata)) {
												$old_contractor = $this->formdata['stall_building_contractor'];
											}

											foreach ($all_contractors as $contractor) {
                                                if (in_array(str_replace(' ', '_', $contractor->company_name), $event_contractors)) {
                                                    $checked = (in_array(str_replace(' ', '_', $contractor->company_name), $old_contractor)) ? 'checked' : '';
                                                    echo '<div class="checkbox">
                                                    <label>
                                                    <input type="checkbox" class="stall_building_contractor" name="stall_building_contractor[]" value="' . str_replace(' ', '_', $contractor->company_name) . '" ' . $checked . '> 
                                                    ' . $contractor->company_name . '
                                                    </label>
                                                    </div>';
                                                }
                                            }

                                            ?>
                                        </div>

                                        <div class="col-sm-6 col-sm-offset-2">
                                            <label>CONTRACTORS INFORMATION</label>
                                            <div id="contractors_details"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>


                            <?php if ($has_shell_stall) { ?>
                                <input type="hidden" name="has_shell_stall" value="1">

                                <div class="row">
                                    <div class="col-sm-4">
                                        <?php
										$stall_fascia_name = '';
										if (!is_null($this->formdata) && array_key_exists('stall_fascia_name', $this->formdata)) {
											$stall_fascia_name = $this->formdata->stall_fascia_name;
										}
                                        ?>

                                        <label>Enter your stall fascia name (<small class="text-danger">* Max 24 Character</small>)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control stall_fascia_name" placeholder="Enter your stall fascia name"
                                                   maxlength="24" name="stall_fascia_name" value="<?= $stall_fascia_name ?>">
                                            <div class="input-group-addon"><span class="name_count">0</span>/24</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-sm-offset-2 text-right">
                                        <img src="<?= base_url('../assets/img/stall-fascia-name.jpg') ?>" alt="Fascia Name" class="img-responsive">
                                    </div>
                                </div>
                            <?php } ?>

                            <p class="bg-danger js-msgbox"></p>

                            <div class="row">
                                <div class="col-xs-10">

                                </div>
                                <div class="col-xs-2">
                                    <a href="javascript:void(0);"
                                       class="btn btn-primary btn-block margin-bottom js-form_btn">Submit</a>
                                </div>
                            </div>

                        </form>

                    </div>
                </div><!-- /.box -->

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
			'urlValidator': "<?php echo base_url("forms/form_01/form_01_validate/doError"); ?>",
			'loadingImg':   "<?php echo base_url("../assets/img/load-indicator.gif"); ?>"
		});
	});


	var contractors = <?php
        echo json_encode($filter_contractors)
        ?>;

	function render_contractor_html() {

		$('#contractors_details').html('');


        var html = '<ul class="nav nav-tabs">';
		$('.stall_building_contractor:checked').each(function () {
			var v = $(this).val();
            html += '<li class=""><a data-toggle="tab" href="#'+v+'">'+v.replace(/_/g, ' ')+'</a></li>'
		});
		html += '</ul>';
		html += '<div class="tab-content">';

		$('.stall_building_contractor:checked').each(function () {
			var v = $(this).val();
			var data = contractors[v];
			html += '<div id="'+v+'" class="tab-pane fade">';
			html += '<table class="table table-bordered table-striped">\
                <tr>\
                    <td class="contractor_company_name"><strong>Contractor:</strong> '+ ((data.company_name != '') ? data.company_name : '-') +'</td>\
                    <td class="contractor_person_name"><strong>Person Name:</strong> '+ ((data.person_name != '') ? data.person_name : '-') +'</td>\
                    <td class="contractor_logo" rowspan="6"><img src="<?= base_url('../') ?>'+ ((data.company_logo != '') ? data.company_logo : 'uploads/profile/default.png') +'" class="img-responsive" width="100px" alt=""></td>\
                </tr>\
                <tr>\
                    <td class="contractor_phone"><strong>Company Phone:</strong> '+ ((data.phone != '') ? data.phone : '-') +'</td>\
                    <td class="contractor_designation"><strong>Person Designation:</strong> '+ ((data.designation != '') ? data.designation : '-') +'</td>\
                </tr>\
                <tr>\
                    <td class="contractor_fax"><strong>Fax:</strong> '+ ((data.fax != '') ? data.fax : '-')  +'</td>\
                    <td class="contractor_person_mobile"><strong>Mobile:</strong> '+ ((data.mobile != '') ? data.mobile : '-') +'</td>\
                </tr>\
                <tr>\
                    <td class="contractor_email"><strong>Company Email:</strong> '+ ((data.company_email != '') ? data.company_email : '-') +'</td>\
                    <td class="contractor_person_email"><strong>Person Email:</strong> '+ ((data.person_email != '') ? data.person_email : '-') +'</td>\
                </tr>\
                <tr>\
                    <td class="contractor_url"><strong>URL:</strong> '+ ((data.url != '') ? data.url : '-') +'</td>\
                    <td class=""></td>\
                </tr>\
                <tr>\
                    <td class="contractor_address" colspan="2"><strong>Address:</strong> '+ ((data.company_address != '') ? data.company_address : '-') +'</td>\
                </tr>\
            </table>';
			html += '</div>';
		});

		html += '</div>';

		$('#contractors_details').html(html);
		$('#contractors_details .nav-tabs li:first a').click();
	}

	$(document).on('change', '.stall_building_contractor', function (e) {
		e.stopImmediatePropagation();

		render_contractor_html();
	});

	render_contractor_html();

	$(document).on('keyup', '.stall_fascia_name', function (e) {
		e.stopImmediatePropagation();

		$('.name_count').html($(this).val().length);
	});

	$('.name_count').html($('.stall_fascia_name').val().length);
</script>
</body>
</html>
