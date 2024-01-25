<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="organizer">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Organizer
            <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('organizer.html'); ?>"><i class="fa fa-dashboard"></i>Organizer List</a></li>
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
                        <h3 class="box-title">Organizer Edit</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('organizer-edit-submit.html') ?>?id=<?= $this->input->get('id') ?>"
                              method="post" id="crd_form" enctype="multipart/form-data">




                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="organizer_name">Organizer Name</label>
                                        <input type="text" class="form-control" name="organizer_name"
                                               id="organizer_name"
                                               value="<?= html_escape(ucwords($this->formdata->organizer_name)) ?>"
                                               placeholder="Name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="organizer_company">Organizer Company</label>
                                        <input type="text" class="form-control" name="organizer_company"
                                               id="organizer_company"
                                               value="<?= html_escape(ucwords($this->formdata->organizer_company)) ?>"
                                               placeholder="Company">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="organizer_email">Email</label>
                                        <input type="text" class="form-control" name="organizer_email"
                                               value="<?= html_escape($this->formdata->organizer_email) ?>" id="organizer_email"
                                               placeholder="Email">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="organizer_phone">Phone</label>
                                        <input type="text" class="form-control" name="organizer_phone"
                                               value="<?= html_escape(ucwords($this->formdata->organizer_phone)) ?>"
                                               id="organizer_phone" placeholder="Phone">
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="organizer_country">Country</label>
                                        <input type="text" class="form-control" name="organizer_country"
                                               value="<?= html_escape($this->formdata->organizer_country) ?>" id="organizer_country"
                                               placeholder="Country" list="country_list">
                                        <datalist id="country_list">
											<?= get_instance()->funcs->print_input_data_list('country'); ?>
                                        </datalist>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="organizer_city">City</label>
                                        <input type="text" class="form-control" name="organizer_city"
                                               value="<?= html_escape(ucwords($this->formdata->organizer_city)) ?>"
                                               id="organizer_city" placeholder="City" list="cities_list">
                                        <datalist id="cities_list">
											<?= get_instance()->funcs->print_input_data_list('city'); ?>
                                        </datalist>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <label>Organizer Logo</label>
                                    <div id="organizer_logo_container"></div>
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
		var faqadd = {
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("organizer-edit-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});

	var file = new file_upload_preview({
		selector: '#organizer_logo_container',
		ajax_src: '<?= base_url('welcome/file_upload') ?>',
		extensions: 'jpg|jpeg|png|PNG',
		base_url: 'uploads/profile/',
		post_file_name: 'organizer_logo',
		has_rotation: false,
		max_upload: 1,
        <?php
        if (!is_null($this->formdata->organizer_image) && $this->formdata->organizer_image != '') { ?>
		predefined_images: ['<?= html_escape($this->formdata->organizer_image) ?>']
        <?php } ?>
	});
</script>
</body>
</html>
