<?php $this->load->view ('includes/after_login/header'); ?>
<?php $this->load->view ('includes/after_login/sidebar'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
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

						<?php
						$message = $this->session->flashdata ('message');
						if (is_string($message)):
							?>
                            <p class="bg-success"><?= $message ?></p>
							<?php
						endif;
						?>
                        <form action="<?= base_url ('my-profile-submit')?>" method="post" id="profile" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>First Name</label>
                                        <input type="text" class="form-control" name="user_first_name" value="<?= html_escape($this->userdata->user_first_name); ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Last Name</label>
                                        <input type="text" class="form-control" name="user_last_name" value="<?= html_escape($this->userdata->user_last_name); ?>">
                                    </div>
                                </div>
                            </div>



                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="text" class="form-control" readonly="readonly" value="<?= html_escape($this->userdata->user_email) ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>User Group</label>
                                        <input type="text" class="form-control" readonly="readonly"  value="<?= html_escape($this->userdata->usergroup->usergroup_title); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">

                                        <label>Phone</label>
                                        <input type="text" class="form-control" readonly="readonly"  value="<?= html_escape($this->userdata->user_phone); ?>">


                                    </div>
                                </div>
                            </div>


							<?php if ($this->userdata->user_image != "" ) { ?>
                                <div class="row">
                                    <div class="col-sm-6 form-group">

                                        <a href="<?php
										$imageurl = base_url();
										echo @$imageurl .= "uploads/profile/{$this->userdata->user_image}";
										?>" target="_blank"><img src="<?= $this->common->getImageURL ($this->userdata->user_image , 200 , 200 , "uploads/profile") 	?>" /></a>

                                    </div>
                                </div>
                            <?php } ?>

                            <div class="row">
                                <div class="col-sm-6 form-group">
									Image
                                    <input name="user_image"  class="form-control" type="file" accept="image/*">
                                </div>
                            </div>


                            <p class="bg-danger js-msgbox"></p>

                            <div class="row">
                                <div class="col-sm-12 form-group text-right">
                                    <button type="button" class="btn btn-default js-form_btn">Save</button>
                                </div>
                            </div>

                        </form>


                        <div class="" style="padding:10px;">

                            <form action="<?= base_url ('my-password-submit')?>" method="post" id="password-form">


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
                                        <button type="button" class="btn btn-default js-form_btn">Save</button>
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
		var profile = {'form':'#profile', 'msgbox':'#profile .js-msgbox' , 'btnClick':'#profile .js-form_btn' ,'urlValidator': "<?php echo base_url("my-profile-validate"); ?>" ,'loadingImg':"<?php echo base_url("assets/img/load-indicator.gif"); ?>"};
		var password = {'form':'#password-form', 'msgbox':'#password-form .js-msgbox' , 'btnClick':'#password-form .js-form_btn' ,'urlValidator': "<?php echo base_url("my-password-validate"); ?>" ,'loadingImg':"<?php echo base_url("assets/img/load-indicator.gif"); ?>"};

		doFormValidation (profile);
		doFormValidation (password);
	});
</script>
</body>
</html>
