<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="discount">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Discount
            <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
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
                        <h3 class="box-title">Discunt Edit</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('discount-edit-submit.html') ?>?id=<?= $this->input->get('id') ?>"
                              method="post" id="crd_form" enctype="multipart/form-data">




                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Offer title <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="offer_title" id="offer_title" value="<?= html_escape(ucwords($this->formdata->offer_title)) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>Discount<span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="discount" id="discount" value="<?= html_escape(ucwords($this->formdata->discount)) ?>">
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

<script>
	$(function () {
		$('#passwordchange').click(function (e) {
			if (!$(this).is(':checked')) {
				$('#passwordbody').slideUp(1000);
			}
			else {
				$('#passwordbody').slideDown(1000);
			}
		});
		$('#passwordbody').hide();

		$("#user_group_id").change();
		var faqadd = {
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("discount-edit-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});
</script>
</body>
</html>
