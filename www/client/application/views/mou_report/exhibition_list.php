<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>
<style>
    .col-srialno
    {
        width: 40px !important;
    }

</style>

<div class="content-wrapper" data-page="my_mou_sign">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            My Meetings
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
                                <div class="col-sm-3">
                                    <label>Event Day</label>
                                    <select name="filter_day" class="form-control">
                                        <option value="">- select -</option>
                                        <?php
										$event_days = $this->db
											->where('exhibition_id', $this->event->id)
											->get('es_exhibition_date')
											->result();
										foreach ($event_days as $count => $event_day) {
										    $day_selected = ($this->input->get('filter_day') && $this->input->get('filter_day') == ($count+1)) ? 'selected' : '';
										    echo '<option value="'.($count+1).'" '.$day_selected.'>Event day '.($count+1).'</option>';
										}
                                        ?>
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
                                <th>Schedule From</th>
                                <th>Schedule To</th>
                                <th>Event Day</th>
                                <th>Mou Signing Date</th>
                                <th>Mou Signing Time</th>
                                <th>Status</th>
                                <th>Description</th>
                                <th>Commercial Value</th>
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
			file_name: 'Meeting List',
			export_type: ['excel', 'pdf'],
			event_id: '<?= myid($this->event->id) ?>',
		})
	});


</script>


</body>
</html>
