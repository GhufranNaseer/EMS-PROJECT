<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="enhanced">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Enhanced Order List
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">List</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Enhanced Order List</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <form action="" method="get">
                            <input type="hidden" name="id" value="<?= $this->input->get('id') ?>">
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label>Status</label>
                                    <select name="filter_status" class="form-control">
                                        <option value="">- select -</option>
                                        <option value="approved" <?= (($this->input->get('filter_status') && $this->input->get('filter_status') == 'approved') ? 'selected' : '') ?>>Approved</option>
                                        <option value="pending" <?= (($this->input->get('filter_status') && $this->input->get('filter_status') == 'pending') ? 'selected' : '') ?>>Pending</option>
                                        <option value="canceled" <?= (($this->input->get('filter_status') && $this->input->get('filter_status') == 'canceled') ? 'selected' : '') ?>>Canceled</option>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                </div>
                            </div>
                        </form>

                        <table width="100%" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <!--<th class="text-center" width="8%">#</th>-->
                                <th>Order#</th>
                                <th>Customer Name</th>
                                <th>Total Items</th>
                                <th>Total Amount</th>
                                <th>Order Date</th>
								<th>Status</th>
                                <th width="13%" class="text-center">Action</th>
                            </tr>
                            </thead>

                        </table>
                    </div>
                </div>

            </div>
        </div>
        <?php
        $canceled = $this->db
            ->select('B.*,CONCAT(L.user_first_name," ", L.user_last_name) as canceled_by_user, C.company,CONCAT(U.user_first_name," ", U.user_last_name) as sales_person_name')
            ->where('B.exhibition_id', $this->formdata->id)
            ->where('B.is_canceled ', 1)
            ->join('users as L', 'B.canceled_by = L.id', 'LEFT')
            ->join('es_customers as C', 'C.id = B.customer_id', 'LEFT')
            ->join('users as U', 'B.booked_by = U.id', 'LEFT')
            ->get('es_exhibition_booking as B')
            ->result();
        ?>
        <div class="row">
            <div class="col-xs-12">
                <div class="box test_box" style="display: none;">
                    <div class="box-header">
                        <h3 class="box-title">Stall Cancellation Details</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">


                        <table width="100%" class="table table-bordered table-striped" >
                            <thead>
                            <tr class="">
                                <th width="8%">Order #</th>
                                <th width="12%">Company Name</th>
                                <th width="12%">Date of Booking </th>
                                <th width="12%">Date of Cancellation</th>
                                <th width="20%">Reason to Cancel</th>
                                <th width="12%">Sales Person</th>
                                <th width="12%">Cancelled By</th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php foreach ($canceled as $row){
                                $reason = '-';
                                $reason_data = $this->db
                                    ->where('booking_id', $row->id)
                                    ->like('message', 'has been canceled')
                                    ->get('es_exhibition_booking_logs')
                                    ->row();
                                if ($reason_data) {
                                    $reason = $reason_data->message;
                                }
                                ?>
                            <tr>
                                <td><?= ($row->id) ?></td>
                                <td><?= ($row->company) ?></td>
                                <td><?= ($row->booking_date) ?></td>
                                <td><?= ($row->canceled_on) ?></td>
                                <td><?= explode('Reason to cancel is:', $reason)[1] ?></td>
                                <td><?= ($row->sales_person_name) ?></td>
                                <td><?= ($row->canceled_by_user)?></td>
                            </tr> <?php } ?>
                            </tbody>

                        </table>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>

<?php $this->load->view('includes/after_login/footer'); ?>

<script type="text/javascript">
	$(document).ready(function () {
		oTable = $('#crud-table').dataTable($.extend(datatable_settings, {
			"sAjaxSource": '<?= base_url('enhanced-order-list-datatable.html'); ?>' + location.search,
		}));

	});

    $(document).ready(function(){
        $("#cancelled").click(function(){
            $(".test_box").toggle();
        });
    });
</script>


</body>
</html>
