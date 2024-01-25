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
            <small>Logs</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Logs</li>
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
                        <table class="table table_border">
                            <tr>
                                <th width="10%">S.no</th>
                                <th>Order #</th>
                                <th>User Name</th>
                                <th>Log</th>
                                <th>Timestamp</th>
                            </tr>
                            <?php
                            $logs = $this->db

                                ->where('booking_id', $this->orderdata->id)
                                ->order_by('id', 'DESC')
                                ->get('es_exhibition_booking_logs')
                                ->result();
                            $count = 0;
                            $user_name=$this->db
                                ->where('id', $this->orderdata->booked_by)
                                ->get('users')
                                ->row();
                            foreach ($logs as $log) {
                                $count++;
                                ?>

                                <tr>
                                    <td><?= $count ?></td>
                                    <td><?= $this->orderdata->id ?></td>
                                    <td><?= $user_name->user_first_name .' '. $user_name->user_last_name ?></td>
                                    <td><?= $log->message ?></td>
                                    <td><?= $log->created_on ?></td>
                                </tr>

                            <?php } ?>

                        </table>
                        <?php
                        //print_r($logs);
                        ?>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->

                <!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.content -->
    <!-- /.content -->
</div>




<?php $this->load->view('includes/after_login/footer'); ?>

</body>
</html>
