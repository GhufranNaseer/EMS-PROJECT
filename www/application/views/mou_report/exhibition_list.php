<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>
<style>
    .col-srialno
    {
        width: 40px !important;
    }

</style>

<div class="content-wrapper" data-page="mou_signing_request">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            My MoU's Signing
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
                        <h3 class="box-title">My MoU's Signing</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <form action="" method="get">
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label>Status</label>
                                    <select name="filter_status" class="form-control">
                                        <option value="">- select -</option>
                                        <option value="approved" <?= (($this->input->get('filter_status') && $this->input->get('filter_status') == 'approved') ? 'selected' : '') ?>>Approved</option>
                                        <option value="pending" <?= (($this->input->get('filter_status') && $this->input->get('filter_status') == 'pending') ? 'selected' : '') ?>>Pending</option>
                                        <option value="my_requests" <?= (($this->input->get('filter_status') && $this->input->get('filter_status') == 'my_requests') ? 'selected' : '') ?>>My Requests</option>
                                    </select>
                                </div>
                                
                                <div class="col-sm-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                </div>
                            </div>
                        </form>

                        <table class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th class="text-center col-srialno" >#</th>
                                <th>Appointment From</th>
                                <th>Appointment To</th>
                                <th>Exhibition</th>
                                <th>Event Day</th>
                                <th>Appointment Date</th>
                                <th>Appointment Time</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>

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
			"sAjaxSource": '<?php echo base_url('mou_sign-datatable.html'); ?>' + location.search,
		}));
		my_datatable(oTable, {
			exportable: true,
			file_name: 'Mou List',
			export_type: ['excel', 'pdf'],
			event_id: '<?= myid($this->event->id) ?>',
		})
	});


</script>


</body>
</html>
