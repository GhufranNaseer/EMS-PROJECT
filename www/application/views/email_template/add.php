<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="email_template">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Email Template
			<small></small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="active">List</li>
		</ol>
	</section>

	<!-- Main content -->
	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box">

					<div class="box-body">
						<h3>Email Template Add</h3>

						<form action="<?= base_url('email_template-submit.html') ?>" method="post" id="crd_form" enctype="multipart/form-data">
							<input type="hidden" name="exhibition_id" value="<?= $this->input->get('exhibition_id') ?>">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>Title</label>
										<select class="form-control" name="email_template_title">
											<option value="">- Select Title -</option>
											<option value="EVENT_INVITATION">EVENT INVITATION</option>
											<option value="RESET_PASSWORD_LINK">RESET PASSWORD LINK</option>
											<option value="APPOINTMENT_SCHEDULE">APPOINTMENT SCHEDULE</option>
											<option value="APPOINTMENT_ACCEPTED">APPOINTMENT ACCEPTED</option>
											<option value="APPOINTMENT_CANCELED">APPOINTMENT CANCELED</option>
											<option value="APPOINTMENT_RE_SCHEDULE">APPOINTMENT RE SCHEDULE</option>
											<option value="MOU SIGNING CANCELED">MOU SIGNING CANCELED</option>
											<option value="MOU_SIGNING_ACCEPTED">MOU SIGNING ACCEPTED</option>
											<option value="MOU_SIGNING_RE_SCHEDULE">MOU SIGNING RE SCHEDULE</option>
										</select>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Subject</label>
										<input type="text" class="form-control" name="email_template_subject" value="" placeholder="Subject">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label for="email_template_message">Message</label>
										<textarea class="form-control" name="email_template_message" id="email_template_message" rows="5"></textarea>
									</div>
								</div>
							</div>


							<p class="bg-danger js-msgbox"></p>

							<div class="row">
								<div class="col-xs-10">

								</div>
								<div class="col-xs-2">
									<a href="javascript:void(0);" class="btn btn-primary btn-block margin-bottom js-form_btn">Save</a>
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

<script src="<?= base_url('assets') ?>/ckeditor/ckeditor.js"></script>

<script>
	$(function() {

		doFormValidation({
			'form': '#crd_form',
			'msgbox': '#crd_form .js-msgbox',
			'btnClick': '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("email_template-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg': "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		});
	});

	var editor = CKEDITOR.replace('email_template_message');

	function copyText (API_key){
		let textToCopy = $('#' + API_key).val();
		navigator.clipboard.writeText(textToCopy);
	}
</script>
</body>

</html>