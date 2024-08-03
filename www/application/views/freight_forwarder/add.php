<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="freight_forwarder">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
			Freight Forwarder
            <small>Add</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('freight-forwarder.html'); ?>"><i class="fa fa-dashboard"></i>Freight Forwarder List</a></li>
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
                        <h3 class="box-title">Freight Forwarder Add</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('freight-forwarder-submit.html') ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="active">
                                            <input name="active" id="active" type="checkbox" value="" checked>
                                            Active
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        &nbsp;
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Company Name *</label>
                                        <input type="text" class="form-control" name="company_name"
                                               placeholder="Company Name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Person Name</label>
                                        <input type="text" class="form-control" name="person_name"
                                               placeholder="Person Name">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Company Phone</label>
                                        <input type="text" class="form-control" name="phone"
                                               placeholder="Company Phone">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Person Designation</label>
                                        <input type="text" class="form-control" name="designation"
                                               placeholder="Person Designation">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Company Fax</label>
                                        <input type="text" class="form-control" name="fax"
                                               placeholder="Company Fax">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Mobile</label>
                                        <input type="text" class="form-control" name="mobile"
                                               placeholder="Mobile">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Company Email</label>
                                        <input type="text" class="form-control" name="company_email"
                                               placeholder="Company Email">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Person Email</label>
                                        <input type="text" class="form-control" name="person_email"
                                               placeholder="Person Email">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Company URL</label>
                                        <input type="text" class="form-control" name="url"
                                               placeholder="Company URL">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Attachments</label>
                                        <div id="event_logo_container"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Company Address</label>
                                        <textarea name="company_address" class="form-control" rows="3"></textarea>
                                    </div>
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
</div><!-- /.content-wrapper -->

<?php $this->load->view('includes/after_login/footer'); ?>

<script>
	$(function () {


        var file_event_logo = new file_upload_preview({
            selector: '#event_logo_container',
            ajax_src: '<?= base_url('welcome/file_upload') ?>',
            extensions: [
                'jpg',
                'jpeg',
                'png',
                'PNG'
            ],
            base_url: 'uploads/notifications/',
            post_file_name: 'attachment',
            has_rotation: false,
            max_upload: 1
        });

		var faqadd = {
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("freight-forwarder-validate.html"); ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});
</script>
</body>
</html>
