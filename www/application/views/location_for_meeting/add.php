<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="event_for_meeting">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
		<?= ($this->input->get('type') == "mou_location" ? 'MoU Signing Location' : 'Meeting Location') ?>
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
						<h3><?= ($this->input->get('type') == "mou_location" ? 'MoU Signing Location' : 'Meeting Location') ?> Add</h3>

						<form action="<?= base_url('Location_for_meeting-submit.html') ?>" method="post" id="crd_form" enctype="multipart/form-data">
							<input type="hidden" name="exhibition_id" value="<?= $this->input->get('exhibition_id') ?>">
							<input type="hidden" name="type" value="<?= $this->input->get('type') ?>">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>Location</label>
										<input type="text" class="form-control" name="location" value="" placeholder="Enter Location">
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
			'urlValidator': "<?php echo base_url("Location_for_meeting-validate.html"); ?>",
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