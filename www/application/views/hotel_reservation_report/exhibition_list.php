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
<div class="content-wrapper" data-page="hotel_reservation_report">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Hotel Reservation Report
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
                        <h3 class="box-title">Hotel Reservation Report</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr>
                                <th class="text-center" colspan="3">Exhibitor Details</th>
                                <th class="text-center" colspan="3">Check-In</th>
                                <th class="text-center" colspan="3">Check-Out</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="">
                                <th>#</th>
                                <th>Exhibit Company Name</th>
                                <th>Exhibitor Name</th>
                                <th>Flight Number</th>
                                <th>Flight Date</th>
                                <th>Flight Time</th>
                                <th>Flight Number</th>
                                <th>Flight Date</th>
                                <th>Flight Time</th>
                                <th>Hotel</th>
                                <th>Room Type</th>
                            </tr>
                            </thead>

                            <tbody>
							<?php

							$rows = $this->db
								->select('F.*, C.company')
								->where(mycolumn('F.exhibition_id'), $_GET['id'])
								->where('F.form_id', 24)
								->join('es_exhibition_booking as B', 'F.booking_id = B.id', 'LEFT')
								->join('es_customers as C', 'B.customer_id = C.id', 'LEFT')
								->get('es_exhibition_booking_forms_data as F')
								->result();


							$count = 1;
							foreach ($rows as $row) {
								$data = (isset($row->form_data)) ? json_decode($row->form_data) : null;

								if (!is_null($data) && isset($data->reservation)) {
									foreach ($data->reservation as $d) {
										echo '<tr>
                                        <td>'.$count.'</td>
                                        <td>'.$row->company.'</td>
                                        <td>'.$d->person_name.'</td>
                                        <td>'.$d->flight.'</td>
                                        <td>'.$d->flight_check_in_date.'</td>
                                        <td>'.$d->flight_check_in_time.'</td>
                                        <td>'.$d->flight.'</td>
                                        <td>'.$d->flight_check_out_date.'</td>
                                        <td>'.$d->flight_check_out_time.'</td>
                                        <td>'.$d->hotel.'</td>
                                        <td>'.$d->hotel_room.'</td>
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
			.append('<a href="<?= base_url('reports/hotel_reservation_report/export_report?id=' . $_GET['id']) ?>" target="_blank" class="btn btn-default btn-sm" style="padding: 3px 12px;vertical-align: top;margin-left: 5px;display: inline-block;">Export excel</a>');
	});
</script>


</body>
</html>
