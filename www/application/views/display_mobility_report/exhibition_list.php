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
<div class="content-wrapper" data-page="display_mobility_report">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Display Mobility Report
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
                        <h3 class="box-title">Display Mobility Report</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <table width="100%" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr>
                                <th class="text-center" colspan="2">Exhibitor Details</th>
                                <th class="text-center" colspan="2">Vehicle Details</th>
                                <th class="text-center" colspan="2">Period</th>
                            </tr>
                            <tr class="">
                                <th>#</th>
                                <th>Exhibit Company Name</th>
                                <th>Item Description ( Incl. Caliber, Wheeled Etc.)</th>
                                <th>Quantity</th>
                                <th>Date</th>
                                <th>Time</th>
                            </tr>
                            </thead>

                            <tbody>
							<?php

							$rows = $this->db
								->select('F.*, C.company')
								->where(mycolumn('F.exhibition_id'), $_GET['id'])
								->where('F.form_id', 23)
								//->where('B.is_canceled', 0)
								//->where('B.is_approved', 1)
								->join('es_exhibition_booking as B', 'F.booking_id = B.id', 'LEFT')
								->join('es_customers as C', 'B.customer_id = C.id', 'LEFT')
								->get('es_exhibition_booking_forms_data as F')
								->result();


							$count = 1;
							foreach ($rows as $row) {
								$data = (isset($row->form_data)) ? json_decode($row->form_data) : null;

								if (!is_null($data) && isset($data->vehicle)) {
									foreach ($data->vehicle as $d) {
									    $event_date = $this->db
                                            ->where('exhibition_id', $row->exhibition_id)
                                            ->get('es_exhibition_date')
                                            ->result();
										echo '<tr>
                                        <td>'.$count.'</td>
                                        <td>'.$row->company.'</td>
                                        <td>'.$d->description.'</td>
                                        <td>'.$d->quantity.'</td>
                                        <td>'.$event_date[$d->event_day - 1]->date.'</td>
                                        <td>'.$event_date[$d->event_day - 1]->open_time.'</td>
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
			.append('<a href="<?= base_url('reports/display_mobility_report/export_report?id=' . $_GET['id']) ?>" target="_blank" class="btn btn-default btn-sm" style="padding: 3px 12px;vertical-align: top;margin-left: 5px;display: inline-block;">Export excel</a>');
	});
</script>


</body>
</html>
