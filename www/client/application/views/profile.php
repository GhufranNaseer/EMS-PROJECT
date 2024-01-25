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
                        <h3 class="box-title">User Profile</h3>
                    </div><!-- /.box-header -->


                    <div class="box-body">

                        <form action="<?= base_url ('my-profile-submit')?>" method="post" id="profile" enctype="multipart/form-data">


                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Exhibitor Company</label>
                                    <input type="text" class="form-control" name="company" id="company" value="<?= html_escape(ucwords($this->userdata->company)) ?>" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Executive Name</label>
                                    <input type="text" class="form-control" name="name" id="name" value="<?= html_escape(ucwords($this->userdata->name)) ?>" disabled>
                                </div>
                                <div class="col-sm-6">
                                    <label>Designation</label>
                                    <input type="text" class="form-control" name="designation" id="designation" value="<?= html_escape(ucwords($this->userdata->designation)) ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Email</label>
                                    <input type="text" class="form-control" name="email" id="email" value="<?= ($this->userdata->email) ?>" disabled>
                                </div>
                                <div class="col-sm-6">
                                    <label>URL</label>
                                    <input type="text" class="form-control" name="url" id="url" value="<?= ($this->userdata->url) ?>"">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Telephone</label>
                                    <input type="number" class="form-control" name="phone"  id="phone" value="<?= ($this->userdata->phone) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>Fax</label>
                                    <input type="number" class="form-control" name="fax" id="fax" value="<?= ($this->userdata->fax) ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label>Country</label>
                                    <input type="text" class="form-control" name="country" id="country" list="country_list" value="<?= ($this->userdata->country) ?>">
                                    <datalist id="country_list">
										<?= get_instance()->funcs->print_input_data_list('country'); ?>
                                    </datalist>
                                </div>
                                <div class="col-sm-4">
                                    <label>City</label>
                                    <input type="text" class="form-control" name="city" list="cities_list" id="city" value="<?= ($this->userdata->city) ?>">
                                    <datalist id="cities_list">
										<?= get_instance()->funcs->print_input_data_list('city'); ?>
                                    </datalist>
                                </div>
                                <div class="col-sm-4">
                                    <label>Zip Code</label>
                                    <input type="number" class="form-control" name="zip_code" id="zip_code" value="<?= ($this->userdata->zip_code) ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address" class="form-control" id="address" rows="3"><?= ($this->userdata->address) ?></textarea>
                            </div>

                            <?php

                            $contact_person = $this->db
                                ->where('customer_id', $this->userdata->id)
                                ->get('es_customer_contact_persons')
                                ->row();

                            if (isset($contact_person)) {

                            ?>

                            <fieldset>
                                <legend>Contract person information:</legend>

                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Person Name <span class="text-red">*</span></label>
                                        <input type="text" class="form-control" name="contact_person" id="contact_person" value="<?= ($contact_person->person_name) ?>">
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Designation</label>
                                        <input type="text" class="form-control" name="contact_person_designation" id="contact_person_designation" value="<?= ($contact_person->designation) ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Person Email <span class="text-red">*</span></label>
                                        <input type="text" class="form-control" name="contact_person_email" id="contact_person_email" value="<?= ($contact_person->primary_email) ?>">
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Person Cell</label>
                                        <input type="number" class="form-control" name="contact_person_phone" id="contact_person_phone" value="<?= ($contact_person->primary_phone) ?>">
                                    </div>
                                </div>
                            </fieldset>

                            <?php } ?>




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
