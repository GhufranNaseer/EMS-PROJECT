<?php $this->load->view ('includes/after_login/header'); ?>
<?php $this->load->view ('includes/after_login/sidebar'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" data-page="profile">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Profile</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i>Profile</a></li>
            <li class="active">Update</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Profile</h3>
                    </div><!-- /.box-header -->


                    <div class="box-body">

                        <form action="<?= base_url ('my-profile-submit')?>" method="post" id="profile" enctype="multipart/form-data">



                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Email</label>
                                    <input type="text" class="form-control" name="officer_email" id="officer_email" value="<?= ($this->userdata->officer_email) ?>" disabled>
                                </div>
                                <div class="col-sm-6">
                                    <label>Contact Number</label>
                                    <input type="number" class="form-control" name="officer_phone"  id="officer_phone" value="<?= ($this->userdata->officer_phone) ?>">
                                </div>
                            </div>


                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Designation</label>
                                    <input type="text" class="form-control" name="officer_designation" id="officer_designation" readonly value="<?= html_escape(ucwords($this->userdata->officer_designation)) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>Liason Officer Name</label>
                                    <input type="text" class="form-control" name="contact_person" id="contact_person" value="<?= html_escape(ucwords($this->userdata->contact_person)) ?>">
                                </div>
                            </div>
							<?php if ($this->userdata->officer_type == 'local_delegates') { ?>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Company</label>
                                        <input type="text" class="form-control" name="officer_company" id="officer_company" value="<?= html_escape(ucwords($this->userdata->officer_company)) ?>">
                                    </div>
                                </div>
							<?php } ?>
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Country</label>
                                    <input type="text" class="form-control" name="officer_country" id="officer_country" readonly value="<?= ($this->userdata->officer_country) ?>">
                                </div>

                            </div>



                            <p class="bg-danger js-msgbox"></p>

                            <div class="row">
                                <div class="col-sm-12 form-group text-right">
                                    <button type="button" class="btn btn-primary btn-lg js-form_btn"><i class="fa fa-save"></i> Save</button>
                                </div>
                            </div>

                        </form>

                        <div class="" style="padding:10px;">

                            <form action="<?= base_url ('my-profile-password')?>" method="post" id="password-form">


                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="opassword">Current Password</label>
                                            <input type="password" class="form-control" name="opassword" id="opassword" placeholder="Current Password">
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="password">New Password</label>
                                            <input type="password" class="form-control" name="password" id="password" placeholder="New Password">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="cpassword">Confirm Password</label>
                                            <input type="password" class="form-control" name="cpassword" id="cpassword" placeholder="Confirm Password">
                                        </div>
                                    </div>
                                </div>

                                <p class="bg-danger js-msgbox"></p>

                                <div class="row">
                                    <div class="col-sm-12 form-group text-right">
                                        <button type="button" class="btn btn-primary btn-lg js-form_btn"><i class="fa fa-save"></i> Update Password</button>
                                    </div>
                                </div>
                            </form>
                        </div>


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
    var $ = jQuery;
	$(function () {

        var profile = {
            'form':'#profile',
            'msgbox':'#profile .js-msgbox' ,
            'btnClick':'#profile .js-form_btn' ,
            'urlValidator': "<?php echo base_url("my-profile-validate"); ?>" ,
            'loadingImg':"<?php echo base_url("../assets/img/load-indicator.gif"); ?>"};

        var password = {'form':'#password-form',
            'msgbox':'#password-form .js-msgbox' ,
            'btnClick':'#password-form .js-form_btn' ,
            'urlValidator': "<?php echo base_url("my-profile-password-validate"); ?>" ,
            'loadingImg':"<?php echo base_url("../assets/img/load-indicator.gif"); ?>"};

        doFormValidation (profile);
        doFormValidation (password);

	});
</script>
</body>
</html>
