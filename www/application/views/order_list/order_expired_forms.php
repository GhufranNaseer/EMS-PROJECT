<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>
<style>
    .radio label {
        padding-left: 0;
    }
</style>

<div class="content-wrapper" data-page="order_list">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Order
            <small>Expired Forms</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Expired Forms</li>
        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <ul class="nav nav-tabs h4" role="tablist">
                            <li class="<?= (!$this->input->get('tab') || $this->input->get('tab') == 'order_detail') ? 'active' : '' ?>">
                                <a href="<?= base_url('order_detail.html?id='.$this->input->get('id').'&order_id='.$this->input->get('order_id').'&tab=order_detail') ?>">Order Details</a>
                            </li>
                            <li class="<?= ($this->input->get('tab') && $this->input->get('tab') == 'edit_order') ? 'active' : '' ?>">
                                <a href="<?= base_url('order_detail.html?id='.$this->input->get('id').'&order_id='.$this->input->get('order_id').'&tab=edit_order') ?>">Edit Order</a>
                            </li>
							<?php if ($this->userdata->user_group_id != SALES_PERSON) { ?>
                                <li class="<?= ($this->input->get('tab') && $this->input->get('tab') == 'order_logs') ? 'active' : '' ?>">
                                    <a href="<?= base_url('order_detail.html?id='.$this->input->get('id').'&order_id='.$this->input->get('order_id').'&tab=order_logs') ?>">Logs</a>
                                </li>
                                <li class="<?= ($this->input->get('tab') && $this->input->get('tab') == 'expired_forms') ? 'active' : '' ?>">
                                    <a href="<?= base_url('order_detail.html?id='.$this->input->get('id').'&order_id='.$this->input->get('order_id').'&tab=expired_forms') ?>">Expired forms</a>
                                </li>
							<?php } ?>
                        </ul>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('order-extended-form-submit.html') ?>?id=<?= $this->input->get('id') ?>&order_id=<?= $this->input->get('order_id') ?>"
                              method="post" id="crd_form" enctype="multipart/form-data">
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th>S.no</th>
                                    <th>Form Number</th>
                                    <th>Form Name</th>
                                    <th>Expired On</th>
                                    <th>Extended</th>
                                    <th>Time</th>
                                </tr>
                                <?php
                                $old_data = $this->db
                                    ->where('exhibition_id', $this->formdata->id)
                                    ->where('booking_id', $this->orderdata->id)
                                    ->get('es_exhibition_forms_extended')
                                    ->result();
                                $forms = $this->db
                                    ->select('EF.*, F.form_number, F.form_name')
                                    ->where('EF.exhibition_id', $this->formdata->id)
                                    ->where('EF.is_active', 1)
                                    ->join('es_order_forms as F', 'F.id = EF.form_id', 'LEFT')
                                    ->get('es_exhibition_forms as EF')
                                    ->result();

                                $count = 0;
                                foreach ($forms as $key => $form) {
                                    $count++;
                                    $checked = '';
                                    $extended = 0;
                                    $disabled = 'readonly';

                                    if ($old_data) {
                                        foreach ($old_data as $old) {
                                            if ($old->form_id == $form->form_id) {
                                                $checked = 'checked';
                                                $extended = $old->extended_hours;
                                                $disabled = '';
                                            }
                                        }
                                    }

                                    echo '<tr>
                                        <td>'. $count .'</td>
                                        <td>'.$form->form_number.'</td>
                                        <td>'.$form->form_name.'</td>
                                        <td>'.$form->expiry_date.'</td>
                                        <td>
                                            <input type="hidden" name="forms['.$key.'][form_id]" value="'.$form->form_id.'">
                                            <input type="checkbox" class="extended_check" name="forms['.$key.'][is_extended]" '.$checked.'>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" name="forms['.$key.'][extended_hours]" class="form-control extended_hours" placeholder="Time" value="'.$extended.'" '.$disabled.'>
                                                <span class="input-group-addon">hours</span>
                                            </div>
                                        </td>
                                        </tr>';
                                }
                                ?>
                            </table>

                            <p class="bg-danger js-msgbox"></p>

                            <div class="row">
                                <div class="col-xs-10">

                                </div>
                                <div class="col-xs-2">
                                    <button type="submit" class="btn btn-primary btn-block">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
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
	$('.extended_check').iCheck({
		checkboxClass: 'icheckbox_square-grey',
		radioClass:    'iradio_square-grey',
		increaseArea:  '20%' // optional
	});


	$(document).on('ifChecked', '.extended_check', function (e) {
		e.stopImmediatePropagation();

		$(this).parents('tr').find('.extended_hours').attr('readonly', false);
	});
	$(document).on('ifUnchecked', '.extended_check', function (e) {
		e.stopImmediatePropagation();

		$(this).parents('tr').find('.extended_hours').attr('readonly', true);
	});
</script>
</body>
</html>
