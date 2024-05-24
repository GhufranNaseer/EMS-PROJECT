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
						<h3><?= ucwords(strtolower(str_replace('_', ' ', $this->formdata->title))) ?></h3>
						<div class="alert alert-info"><?= $this->formdata->description ?></div>

						<form action="<?= base_url('email_template-edit-submit.html') ?>?id=<?= $this->input->get('id') ?>" method="post" id="crd_form" enctype="multipart/form-data">

							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>Subject</label>
										<input type="text" class="form-control" name="email_template_subject" value="<?= html_escape($this->formdata->subject) ?>" placeholder="Subject">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label for="email_template_message">Message</label>
										<textarea class="form-control" name="email_template_message" id="email_template_message" rows="5"><?= html_escape($this->formdata->message) ?></textarea>
									</div>
								</div>
							</div>

							<div>
								<p><strong>Available Placeholders</strong></p>
								<div style="margin-bottom: 10px;">
									<?php
									if (is_null($this->formdata->placeholders)) {
										echo '<div class="text-muted">No placeholders available for this template</div>';
									} else {
										$placeholders = explode(',', $this->formdata->placeholders);
	
										foreach ($placeholders as $placeholder) {
											echo '<span class="badge" onclick="navigator.clipboard.writeText(\''. trim($placeholder) .'\')" title="Click to copy text" style="
											padding: 0.6rem 1rem;
											margin-right: 5px;
											font-size: 1.3rem;
										" >' . trim($placeholder) . '</span> ';
										}
									}
									?>
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
			'urlValidator': "<?php echo base_url("email_template-edit-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
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