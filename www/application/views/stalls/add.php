<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>
<style>
    .radio label {
        padding-left: 0;
    }
</style>

<div class="content-wrapper" data-page="stalls">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Stalls
            <small>Add</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Add</li>
        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Stalls Add</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('stalls-submit.html') ?>?id=<?= $this->input->get('id') ?>"
                              method="post" id="crd_form" enctype="multipart/form-data">

                            <table class="table table-bordered">
                                <tr>
                                    <th class="text-center">Event Logo</th>
                                    <th>Event</th>
                                    <td><?= $this->formdata->exhibition_title ?></td>
                                </tr>
                                <tr>
                                    <td rowspan="3" class="text-center">
                                        <img src="<?= base_url($this->formdata->event_logo) ?>" alt="" class="img-thumbnail" style="width: 100px">
                                    </td>
                                    <th>Price Type</th>
                                    <td><?= $this->formdata->price_type ?></td>
                                </tr>
                                <tr>
                                    <th>Booking Expire Date</th>
                                    <td><?= $this->formdata->booking_expire_date ?></td>
                                </tr>
                                <tr>
                                    <th>Event Location</th>
                                    <td><?= $this->db->where('id', $this->formdata->location_id)->get('es_locations')->row()->location_title ?></td>
                                </tr>
                            </table>

                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <label>Stall Prefix</label>
                                    <select name="stall_prefix" class="form-control">
                                        <?php
										$azRange = range('A', 'Z');
										foreach ($azRange as $letter) {
											echo '<option value="'.strtoupper($letter).'">'.strtoupper($letter).'</option>';
										}
                                        ?>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label>Stalls Range Start</label>
                                    <input type="number" class="form-control" name="stalls_start" placeholder="Stalls Start" min="1" value="1">
                                </div>
                                <div class="col-sm-2">
                                    <label>Stalls Range End</label>
                                    <input type="number" class="form-control" name="stalls_end" placeholder="Stalls End" min="1" value="1">
                                </div>
                                <div class="col-sm-3">
                                    <label>Stall Price - USD</label>
                                    <input type="number" class="form-control" name="stall_price_usd" placeholder="Stall Price - USD">
                                </div>
                                <div class="col-sm-3">
                                    <label>Stall Price - PKR</label>
                                    <input type="number" class="form-control" name="stall_price_pkr" placeholder="Stall Price - PKR">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label>Stall Size</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="stall_size">
                                        <span class="input-group-addon">sqm</span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <label>Stall Category</label>
                                    <select name="stall_category" class="form-control">
                                        <option value="standard">Standard</option>
                                        <option value="upgraded">Upgraded</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label>Event Halls</label>
                                    <?php
                                    $halls = $this->db
                                        ->select('E.*, H.hall_title')
                                        ->where('exhibition_id', $this->formdata->id)
                                        ->join('es_location_halls as H', 'E.hall_id = H.id', 'LEFT')
                                        ->get('es_exhibition_halls as E')
                                        ->result();

                                    foreach ($halls as $hall) {
                                        echo '<div class="radio"><label><input type="radio" name="hall_id" value="'.$hall->hall_id.'"> '.$hall->hall_title.'</label></div>';
                                    }
                                    ?>
                                </div>
                                <div class="col-sm-3">
                                    <label>Location in Hall</label>
                                    <input type="text" class="form-control" name="location_in_hall" placeholder="Location in Hall">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Description</label>
                                    <textarea name="stall_description" class="form-control" cols="30" rows="3"></textarea>
                                </div>
                                <div class="col-sm-6">
                                    <label>Design and Map</label>
                                    <div id="stall_design"></div>
                                </div>
                            </div>




                            <p class="bg-danger js-msgbox"></p>

                            <div class="row">
                                <div class="col-xs-10">

                                </div>
                                <div class="col-xs-2">
                                    <a href="javascript:void(0);"
                                       class="btn btn-primary btn-block margin-bottom js-form_btn">Save</a>
                                </div>
                            </div>

                        </form>

                    </div><!-- /.box-body -->
                </div><!-- /.box -->

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

	$(function () {
		doFormValidation({
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("stalls-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		});
	});

	$('[name="hall_id"]').iCheck({
		checkboxClass: 'icheckbox_square-grey',
		radioClass:    'iradio_square-grey',
		increaseArea:  '20%' // optional
	});


	var file_stall_map = new file_upload_preview({
		selector: '#stall_design',
		ajax_src: '<?= base_url('welcome/file_upload') ?>',
		extensions: 'jpg|jpeg|png|PNG',
		base_url: 'uploads/exhibition/',
		post_file_name: 'stall_map',
		has_rotation: false,
		max_upload: 1,
	});
    

</script>
</body>
</html>
