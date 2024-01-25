<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="user_group">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            User Groups
            <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('super-admin-user-list.html'); ?>"><i class="fa fa-dashboard"></i> User
                    Groups</a></li>
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
                        <h3 class="box-title"> User Groups Edit</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('user-groups-edit-submit.html') ?>?id=<?= $this->input->get('id') ?>"
                              method="post" id="crd_form" enctype="multipart/form-data">

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="active">
                                            <input
                                                name="active" <?= ($this->formdata->isActive == 1) ? 'checked' : '' ?>
                                                id="active" type="checkbox" value="">
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
                                        <label for="company_title">User Groups Title</label>
                                        <input type="text" name="group_title"
                                               value="<?= html_escape($this->formdata->usergroup_title) ?>"
                                               id="group_title" placeholder="User Groups Title" class="form-control">

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
                                        <label for="superadmin">
                                            <input name="superadmin" id="superadmin"
                                                   type="checkbox" <?= ($this->formdata->usergroup_rights == '*') ? 'checked' : '' ?>
                                                   value="superadmin">
                                            <b> Is super admin? </b>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        &nbsp;
                                    </div>
                                </div>
                            </div>

                            <div class="row js-not-superadmin">
                                <div class="col-sm-12">

                                    <h2>Group Rights</h2>

									<?php $index = 0;
									foreach ($this->usermdl->getAllRights() as $area):
										$index++;
										?>
                                        <div class="form-group">
                                            <label for="rights_<?= $index ?>">
                                                <input name="rights[]"
                                                       id="rights_<?= $index ?>" <?= (strpos($this->formdata->usergroup_rights, ",{$area['filename']},") !== false) ? 'checked' : ''; ?>
                                                       type="checkbox" value="<?= html_escape($area['filename']) ?>">
												<?= $area['cbname'] ?>
                                            </label>
                                        </div>
									<?php endforeach; ?>
                                </div>
                            </div>
							<?php
							if (false):
								?>
                                <div class="row" id="reports_rights_div">
                                    <div class="col-xs-10">
                                        <h2>Report Rights</h2>

                                        <span>
                 <label>
                 <input type="checkbox" id="reports_rights_all" name="reports_rights_all"/> All Rights
                 </label>
                 </span>

                                        <br/>
                                        <hr/>

                                        <span id="reports_rights">
                 
                 <span>
                 <label>
                 <input name="report_rights[]" class="js-report_cb" type="checkbox" value="region_wise_report"/> Region Wise Report
                 </label>
                  | 
                 <label>
                 <input name="report_rights[]" type="checkbox" value="region_wise_report_excel"/> Download Excel
                 </label>
                 
                  <label>
                 <input name="report_rights[]" type="checkbox" value="region_wise_report_pdf"/> Download PDF
                 </label>
                 </span>
                 <br/>
                 <hr/>
                 
                 <span>
                 <label>
                 <input name="report_rights[]" class="js-report_cb" type="checkbox" value="user_wise_report"/> User Wise Report
                 </label>
                  | 
                 <label>
                 <input name="report_rights[]" type="checkbox" value="user_wise_report_excel"/> Download Excel
                 </label>
                 
                  <label>
                 <input name="report_rights[]" type="checkbox" value="user_wise_report_pdf"/> Download PDF
                 </label>
                 </span>
                 <br/>
                 <hr/>
                 
                 <span>
                  <label>
                 <input name="report_rights[]" class="js-report_cb" type="checkbox" value="branch_wise_report"/> Branch Wise Report
                 </label>
                  | 
                 <label>
                 <input name="report_rights[]" type="checkbox" value="branch_wise_report_excel"/> Download Excel
                 </label>
                 
                  <label>
                 <input name="report_rights[]" type="checkbox" value="branch_wise_report_pdf"/> Download PDF
                 </label>
                 </span>
                 <br/>
                 <hr/>
                 
                 <span>
                  <label>
                 <input name="report_rights[]" class="js-report_cb" type="checkbox" value="detail_report"/> Detail Report
                 </label>
                  | 
                 <label>
                 <input name="report_rights[]" type="checkbox" value="detail_report_excel"/> Download Excel
                 </label>
                 
                  <label>
                 <input name="report_rights[]" type="checkbox" value="detail_report_pdf"/> Download PDF
                 </label>
                 </span>
                 <br/>
                 <hr/>
                 
                 </span>

                                    </div>
                                    <div class="col-xs-2">
                                    </div>
                                </div>
								<?php
							endif;
							?>
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
		$('#superadmin').click(function (e) {
			if ($(this).is(':checked')) {
				$('.js-not-superadmin').slideUp(1000);
			}
			else {
				$('.js-not-superadmin').slideDown(1000);
			}
		});
		if ($('#superadmin').is(':checked'))
			$('.js-not-superadmin').hide();
		else
			$('.js-not-superadmin').show();
		var faqadd = {
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("user-groups-edit-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});
</script>

</body>
</html>
