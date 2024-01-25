<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<style>
    .view_details {
        cursor: pointer;
    }
    #copy_package_modal .modal-header {
        font-size: 16px;
        font-weight: bold;
    }
    .modal-header > .row > .col-sm-3:nth-child(3) {
        font-size: 14px;
        font-weight: normal;
    }
</style>
<div class="content-wrapper" data-page="vehicle_rent_report">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Vehicle Rent Report
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
                        <h3 class="box-title">Vehicle Rent Report</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <table width="100%" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr>
                                <th class="text-center" colspan="5">Exhibitor Details</th>
                                <th class="text-center" colspan="2">Vehicle Details</th>
                                <th class="text-center" colspan="2">Period</th>
                                <th class="text-center" colspan="2">Points</th>
                            </tr>
                            <tr class="">
                                <th>#</th>
                                <th>Exhibit Company Name</th>
                                <th>Exhibitor Name</th>
                                <th>Nationality</th>
                                <th>Passport Number</th>
                                <th>Vehicle Type</th>
                                <th>Quantity</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Pick-Up</th>
                                <th>Drop-Off</th>
                            </tr>
                            </thead>

                            <tbody>
							<?php

							$rows = $this->db
								->select('F.*, C.company')
								->where(mycolumn('F.exhibition_id'), $_GET['id'])
								->where('F.form_id', 17)
								//->where('B.is_canceled', 0)
								//->where('B.is_approved', 1)
								->join('es_exhibition_booking as B', 'F.booking_id = B.id', 'LEFT')
								->join('es_customers as C', 'B.customer_id = C.id', 'LEFT')
								->get('es_exhibition_booking_forms_data as F')
								->result();


							$count = 1;
							foreach ($rows as $row) {
								$data = (isset($row->form_data)) ? json_decode($row->form_data) : null;

								if (!is_null($data) && isset($data->booking)) {
									foreach ($data->booking as $d) {
										echo '<tr>
                                        <td>'.$count.'</td>
                                        <td>'.$row->company.'</td>
                                        <td>'.$d->exhibitor->name.'</td>
                                        <td>'.$d->exhibitor->nationality.'</td>
                                        <td>'.$d->exhibitor->passport.'</td>
                                        <td>'.$d->vehicle->name.'</td>
                                        <td>'.$d->vehicle->qty.'</td>
                                        <td>'.$d->vehicle->from_date.'</td>
                                        <td>'.$d->vehicle->to_date.'</td>
                                        <td>'.$d->pickup->location.'</td>
                                        <td>'.$d->dropoff->location.'</td>
                                        </tr>';

										$count++;
									}
								}

							}
							?>
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
			"bServerSide": false,
			"bProcessing": false,
			"fnRowCallback": null,
			"fnInitComplete": null,
		}));

		oTable.fnAdjustColumnSizing();

		$('#crud-table_wrapper .dataTables_filter')
			.append('<a href="<?= base_url('reports/vehicle_rent_report/export_report?id=' . $_GET['id']) ?>" target="_blank" class="btn btn-default btn-sm" style="padding: 3px 12px;vertical-align: top;margin-left: 5px;display: inline-block;">Export excel</a>');
	});
</script>


</body>
</html>
