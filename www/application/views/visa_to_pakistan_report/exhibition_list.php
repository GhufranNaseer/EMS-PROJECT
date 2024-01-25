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
<div class="content-wrapper" data-page="visa_to_pakistan_report">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Visa To Pakistan Report
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
                        <h3 class="box-title">Visa To Pakistan Report</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th>#</th>
                                <th>Exhibit Company Name</th>
                                <th>Exhibitor Name</th>
                                <th>Nationality</th>
                                <th>Passport Number</th>
                                <th>Concerned Pakistani Mission Location (Country/City)</th>
                                <th>Passport Issue Date</th>
                                <th>Passport Expire Date</th>
                                <th>Passport (Bio) Page or Page (1)</th>
                                <th>Download Passport Image</th>
                                <th>Date of Entry</th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php

                            $rows = $this->db
                                ->select('F.*, C.company')
								->where(mycolumn('F.exhibition_id'), $_GET['id'])
								->where('F.form_id', 21)
								//->where('B.is_canceled', 0)
								//->where('B.is_approved', 1)
								->join('es_exhibition_booking as B', 'F.booking_id = B.id', 'LEFT')
                                ->join('es_customers as C', 'B.customer_id = C.id', 'LEFT')
								->get('es_exhibition_booking_forms_data as F')
                                ->result();


                            $count = 1;
                            foreach ($rows as $row) {
                                $data = (isset($row->form_data)) ? json_decode($row->form_data) : null;

                                if (!is_null($data) && isset($data->visas)) {
                                    foreach ($data->visas as $d) {
										echo '<tr>
                                        <td>'.$count.'</td>
                                        <td>'.$row->company.'</td>
                                        <td>'.$d->visa_name.'</td>
                                        <td>'.$d->visa_nationality.'</td>
                                        <td>'.$d->visa_passport.'</td>
                                        <td>'.$d->concerned_city.', '.$d->concerned_country.'</td>
                                        <td>'.$d->visa_passport_issue_date.'</td>
                                        <td>'.$d->visa_passport_expiry_date.'</td>
                                        <td>';
										if (isset($d->contact_person_bio_image) && count($d->contact_person_bio_image) > 0) {
										    echo '<img src="'.base_url('client/'.$d->contact_person_bio_image[0]).'" width="50px">';
                                        } else {
										    echo '-';
                                        }
                                        echo '</td><td>';
										if (isset($d->contact_person_bio_image) && count($d->contact_person_bio_image) > 0) {
											echo '<a href="'.base_url('client/'.$d->contact_person_bio_image[0]).'" target="_blank">Download</a>';
										} else {
										    echo '-';
                                        }
                                        echo '</td>
                                        <td>'.((is_null($row->modified_on)) ? date('Y-m-d', strtotime($row->created_on)) : date('Y-m-d', strtotime($row->modified_on))).'</td>
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
//	$(document).ready(function () {
//		oTable = $('#crud-table').dataTable($.extend(datatable_settings, {
//			"sAjaxSource": '<?php //echo base_url('visa_to_pakistan_report-datatable.html'); ?>//?id=<?//= $this->input->get('id') ?>//',
//		}));
//	});
    $(document).ready(function () {
        oTable = $('#crud-table').dataTable($.extend(datatable_settings, {
            "bServerSide": false,
            "bProcessing": false,
            "fnRowCallback": null,
            "fnInitComplete": null,
        }));

		oTable.fnAdjustColumnSizing();

		$('#crud-table_wrapper .dataTables_filter')
			.append('<a href="<?= base_url('reports/visa_to_pakistan_report/export_report?id=' . $_GET['id']) ?>" target="_blank" class="btn btn-default btn-sm" style="padding: 3px 12px;vertical-align: top;margin-left: 5px;display: inline-block;">Export excel</a>');
    });
</script>


</body>
</html>
