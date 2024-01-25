<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="users">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            User
            <small>Add</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('users.html'); ?>"><i class="fa fa-dashboard"></i>User List</a></li>
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
                        <h3 class="box-title">User Add</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('user-submit.html') ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="active">
                                            <input name="active" id="active" type="checkbox" value="">
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
                                        <label for="user_first_name">First Name</label>
                                        <input type="text" class="form-control" name="user_first_name"
                                               id="user_first_name" placeholder="First Name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="user_last_name">Last Name</label>
                                        <input type="text" class="form-control" name="user_last_name"
                                               id="user_last_name" placeholder="Last Name">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="user_email">Email</label>
                                        <input type="text" class="form-control" name="user_email" id="user_email"
                                               placeholder="Email">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="user_phone">Phone</label>
                                        <input type="text" class="form-control" name="user_phone" id="user_phone"
                                               placeholder="Phone">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="user_password">Password</label>
                                        <input type="password" class="form-control" name="user_password"
                                               id="user_password" placeholder="Password">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="cuser_password">Confirm Password</label>
                                        <input type="password" class="form-control" name="cuser_password"
                                               id="cuser_password" placeholder="Confirm Password">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="user_group_id">User Group</label>
                                        <select class="form-control" name="user_group_id" id="user_group_id">
                                            <option value=""> - Select User Group -</option>
											<?php foreach ($this->db->where('id !=', CUSTOMER)->get('usergroup')->result_array() as $row): ?>
                                                <option
                                                    value="<?= ($row['id']) ?>"><?= html_escape(ucwords($row['usergroup_title'])) ?></option>
											<?php endforeach; ?>
                                        </select>

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

		var faqadd = {
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("user-validate.html"); ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});
</script>
</body>
</html>
