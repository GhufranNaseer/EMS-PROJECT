<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>
<style>
    .radio label {
        padding-left: 0;
    }
</style>

<div class="content-wrapper" data-page="event_form">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Event
            <small>Forms</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Forms</li>
        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Event Forms</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('event_form-submit.html') ?>?id=<?= $this->input->get('id') ?>"
                              method="post" id="crd_form" enctype="multipart/form-data">

                            <table class="table table-bordered">
                                <tr>
                                    <th class="text-center">Event Logo</th>
                                    <th>Event</th>
                                    <td><?= $this->formdata->exhibition_title ?></td>
                                </tr>
                                <tr>
                                    <td rowspan="3" class="text-center">
                                        <img src="<?= base_url($this->formdata->event_logo) ?>" alt="" class="img-thumbnail" style="width: 100px">
                                    </td>
                                    <th>Price Type</th>
                                    <td><?= $this->formdata->price_type ?></td>
                                </tr>
                                <tr>
                                    <th>Booking Expire Date</th>
                                    <td><?= $this->formdata->booking_expire_date ?></td>
                                </tr>
                                <tr>
                                    <th>Event Location</th>
                                    <td><?= $this->db->where('id', $this->formdata->location_id)->get('es_locations')->row()->location_title ?></td>
                                </tr>
                            </table>


                            <table class="table table-bordered table-striped">
                                <?php

                                $old_data = $this->db
                                    ->where('exhibition_id', $this->formdata->id)
                                    ->get('es_exhibition_forms')
                                    ->result();

                                $forms = $this->db
                                ->get('es_order_forms')
                                ->result();
                                echo '<tr>
                                        <th>S.no</th>
                                        <th>Active</th>
                                        <th>Form Number</th>
                                        <th>Form Name</th>
                                        <th>Expiry Date</th>
                                        </tr>';

                                $count = 0;
                                foreach ($forms as $key => $form) {
									$count++;
                                    $checked = 'checked';
                                    $expire = date('m/d/Y', strtotime($this->formdata->booking_expire_date));
                                    if ($old_data) {
                                        foreach ($old_data as $old) {
                                            if ($old->form_id == $form->id) {
                                                $checked = ($old->is_active == 1) ? 'checked' : '';
                                                $expire = date('m/d/Y', strtotime($old->expiry_date));
                                            }
                                        }

                                    }
                                    echo '
                                        <tr>
                                        <td>'. $count .'</td>
                                        <td width="60px">
                                        <input type="hidden" name="forms['.$key.'][form_id]" value="'.$form->id.'">
                                        <input type="checkbox" class="icheck" name="forms['.$key.'][is_active]" '.$checked.'>
                                        </td>
                                        <td>'.$form->form_number.'</td>
                                        <td>'.$form->form_name.'</td>
                                        <td><input type="text" name="forms['.$key.'][expiry_date]" id="form_expiry_date" class="expiry_date form-control" value="'. $expire .'"></td>
                                        </tr>';
                                }
                                ?>
                            </table>



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

<link rel="stylesheet" href="<?= base_url('assets') ?>/iCheck/square/grey.css">
<script src="<?= base_url('assets') ?>/iCheck/icheck.min.js"></script>

<script>

	$(function () {
		doFormValidation({
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("event_form-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		});
	});

    $('.expiry_date').datepicker({
        startDate: new Date()
    });

</script>
</body>
</html>
