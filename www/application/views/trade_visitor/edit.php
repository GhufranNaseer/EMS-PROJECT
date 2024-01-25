<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="trade_visitor">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Trade Visitor
			<small>Edit</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i>Home</a></li>
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
						<h3 class="box-title">Office Staff Visitor Badge Issuance</h3>
					</div><!-- /.box-header -->

					<div class="box-body">
						<form action="<?= base_url('trade-visitor-edit-submit.html') ?>?id=<?= $this->input->get('id') ?>" method="post" id="trade_visitor_form"
							  enctype="multipart/form-data">

							<div class="form-group row">
								<div class="col-sm-3">
									<label>Visitor Full name <span class="text-red">*</span></label>
									<input type="text" class="form-control" name="full_name" value="<?= $data->full_name ?>">
								</div>
								<div class="col-sm-3">
									<label>Job Title <span class="text-red">*</span></label>
									<input type="text" class="form-control" name="designation" value="<?= $data->designation ?>">
								</div>
								<div class="col-sm-3">
									<label>Email Address</label>
									<input type="text" class="form-control" name="email" value="<?= $data->email ?>">
								</div>
								<div class="col-sm-3">
									<label>Cell Phone Number</label>
									<input type="number" class="form-control" name="mobile" value="<?= $data->mobile ?>">
								</div>
							</div>

							<div class="form-group row">
								<div class="col-sm-3">
									<label>Personal Landline Number</label>
									<input type="number" class="form-control" name="phone" value="<?= $data->phone ?>">
								</div>
								<div class="col-sm-3">
									<label>Country <span class="text-red">*</span></label>
									<input type="text" class="form-control" name="country" list="country_list" value="<?= ($data->nationality == 'Pakistani') ? 'Pakistan' : $data->nationality ?>">
									<datalist id="country_list">
										<?= get_instance()->funcs->print_input_data_list('country'); ?>
									</datalist>
								</div>
								<div class="col-sm-3">
									<label>C.N.I.C or Passport # <span class="text-red">*</span></label>
									<input type="text" class="form-control" name="cnin_passport" value="<?= ($data->nationality == 'Pakistani') ? $data->cnic : $data->passport ?>">
								</div>
								<div class="col-sm-3">
									<label>Date of Expiry</label>
									<input type="text" class="form-control expiry_date" name="expiry_date" value="<?= date('m/d/Y', strtotime($data->expiry_date)) ?>">
								</div>
							</div>

							<div class="form-group row">
                                <div class="col-sm-3">
                                    <label>Organization <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="company" value="<?= $data->company ?>">
                                </div>
								<!--<div class="col-sm-3">
									<label>User Image</label>
									<div id="image_container"></div>
								</div>-->
							</div>

							<h4>Collection Person Details</h4>
							<div class="form-group row">
								<div class="col-sm-3">
									<label>Collection Person Name</label>
									<input type="text" class="form-control" name="collection_person_name"
										   placeholder="Collection Person Name" value="<?= $data->collection_person_name ?>">
								</div>
								<div class="col-sm-3">
									<label>Collection Person CNIC</label>
									<input type="number" class="form-control" name="collection_person_cnic"
										   placeholder="Collection Person CNIC" value="<?= $data->collection_person_cnic ?>">
								</div>
								<div class="col-sm-3">
									<label>Collection Person Phone</label>
									<input type="number" class="form-control" name="collection_person_phone"
										   placeholder="Collection Person Phone" value="<?= $data->collection_person_phone ?>">
								</div>
							</div>

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
</div><!-- /.content-wrapper -->

<?php $this->load->view('includes/after_login/footer'); ?>

<script>
	$(function () {
		doFormValidation({
			'form':         '#trade_visitor_form',
			'msgbox':       '#trade_visitor_form .js-msgbox',
			'btnClick':     '#trade_visitor_form .js-form_btn',
			'urlValidator': "<?php echo base_url("trade-visitor-edit-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		});

		$('.expiry_date').datepicker();

		var file = new file_upload_preview({
			selector: '#image_container',
			ajax_src: '<?= base_url('welcome/file_upload') ?>',
			extensions: 'jpg|jpeg|png|PNG',
			base_url: 'uploads/trade_visitor/',
			post_file_name: 'user_image',
			has_rotation: false,
			max_upload: 1,
			on_init: function () {
				$('#image_container .imgbox').each(function () {
					var s = $(this).find('img').attr('src');
					$(this).find('img').attr('src', '<?= base_url('client/') ?>' + s);
				})
			},
            <?php if (!is_null($data->user_image) && $data->user_image != '') { ?>
			predefined_images: <?= json_encode(explode(',', $data->user_image)) ?>
            <?php } ?>
		});
	});
</script>
</body>
</html>
