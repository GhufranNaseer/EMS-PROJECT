<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="exhibitions">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
		Bank Detail Add
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
						<h3>Bank Details</h3>

						<form action="<?= base_url('bank-details-submit.html') ?>" method="post" id="crd_form" enctype="multipart/form-data">
							<input type="hidden" name="exhibition_id" value="<?= $this->input->get('exhibition_id') ?>">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>Bank Name</label>
										<input type="text" class="form-control" name="bank_name" value="<?= html_escape(ucwords($bank_data->bank_name)) ?>" placeholder="Enter Bank Name">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Title of Account</label>
										<input type="text" class="form-control" name="title" value="<?= html_escape(ucwords($bank_data->title)) ?>" placeholder="Title of Account">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Branch Name</label>
										<input type="text" class="form-control" name="branch_name" value="<?= html_escape(ucwords($bank_data->branch_name)) ?>" placeholder="Branch Name">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Branch Code</label>
										<input type="text" class="form-control" name="branch_code" value="<?= html_escape(ucwords($bank_data->branch_code)) ?>" placeholder="Branch Name">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Account No.</label>
										<input type="text" class="form-control" name="account_no" value="<?= html_escape(ucwords($bank_data->account_no)) ?>" placeholder="Account No">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>IBAN No.</label>
										<input type="text" class="form-control" name="iban_no" value="<?= html_escape(ucwords($bank_data->iban_no)) ?>" placeholder="IBAN No">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Swift Code</label>
										<input type="text" class="form-control" name="swift_code" value="<?= html_escape(ucwords($bank_data->swift_code)) ?>" placeholder="Swift Code">
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
			'urlValidator': "<?php echo base_url("bank-details-validate.html"); ?>",
			'loadingImg': "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		});
	});
</script>
</body>

</html>