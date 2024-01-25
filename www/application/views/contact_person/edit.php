<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="contact_person">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Contact Person
            <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('contact_person.html'); ?>"><i class="fa fa-dashboard"></i> Contact Person</a></li>
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
                        <h3 class="box-title">Customer Edit</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('contact_person-edit-submit.html') ?>?id=<?= $this->input->get('id') ?>"
                              method="post" id="crd_form" enctype="multipart/form-data">

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Select Customer <span class="text-red">*</span></label>
                                    <select name="customer_id" class="form-control">
                                        <option value="">- select -</option>
                                        <?php
                                        $customers = $this->db
                                            ->where('is_active', 1)
                                            ->where('is_deleted', 0)
                                            ->get('es_customers')
                                            ->result();

                                        foreach ($customers as $customer) {
                                            $selected = ($customer->id == $this->formdata->customer_id) ? 'selected' : '';
                                            echo '<option value="'.$customer->id.'" '.$selected.'>'.$customer->company.'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>



                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Person Name <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="person_name" value="<?= html_escape(ucwords($this->formdata->person_name)) ?>" >
                                </div>
                                <div class="col-sm-6">
                                    <label>Designation <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="designation" value="<?= html_escape(ucwords($this->formdata->designation)) ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Primary Email <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="primary_email" value="<?= html_escape(ucwords($this->formdata->primary_email)) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>Secondary Email</label>
                                    <input type="text" class="form-control" name="secondary_email" value="<?= html_escape(ucwords($this->formdata->secondary_email)) ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Primary Phone <span class="text-red">*</span></label>
                                    <input type="number" class="form-control" name="primary_phone" value="<?= html_escape(ucwords($this->formdata->primary_phone)) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>Secondary Phone</label>
                                    <input type="number" class="form-control" name="secondary_phone" value="<?= html_escape(ucwords($this->formdata->secondary_phone)) ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Office Phone</label>
                                    <input type="number" class="form-control" name="office_phone" value="<?= html_escape(ucwords($this->formdata->office_phone)) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>Extension</label>
                                    <input type="number" class="form-control" name="office_phone_extention" value="<?= html_escape(ucwords($this->formdata->office_phone_extention)) ?>">
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
			'urlValidator': "<?php echo base_url("contact_person-edit-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});
</script>
</body>
</html>
