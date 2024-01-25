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
<div class="content-wrapper" data-page="branding_report">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Branding Report
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
                        <h3 class="box-title">Branding Report</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th>#</th>
                                <th>Company Name</th>
                                <th>Branding Catagory</th>
                                <th>Branding Sub Catagory</th>
                                <th>Quantity</th>
                                <th>Ad Size(MB's)</th>
                                <th>Download</th>
                                <th>update Date</th>
                                <th>Time</th>
                                <th>Cost</th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php

                            $rows = $this->db
                               // ->select('F.*, C.company')
								->where(mycolumn('exhibition_id'), $_GET['id'])
								->where('form_id', 25)
								//->where('B.is_canceled', 0)
								//->where('B.is_approved', 1)
								->get('es_exhibition_booking_forms_data')
                                ->result();


                            $count = 1;
                            foreach ($rows as $row) {
                                $data = (isset($row->form_data)) ? json_decode($row->form_data) : null;

                                if (!is_null($data) && isset($data->branding)) {
                                    foreach ($data->branding as $d) {

                                        $main=$this->db
                                            ->where('id', $d->main_category)
                                            ->get('es_inventory_category')
                                            ->row();
                                        $sub=$this->db
                                            ->where('id', $d->sub_category)
                                            ->get('es_inventory_category')
                                            ->row();
										echo '<tr>
                                        <td>'.$count.'</td>
                                        <td>'.$d->company.'</td>
                                        <td>'.$main->category_title.'</td>
                                        <td>'.$sub->category_title.'</td>
                                        <td>'.$d->quantity.'</td>
                                        <td></td>
                                        <td></td>
                                        <td>'.$d->date.'</td>
                                        <td>'.$d->time.'</td>
                                        <td>'.$d->cost.' '.$d->price_type.'</td>
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
			.append('<a href="<?= base_url('reports/branding_report/export_report?id=' . $_GET['id']) ?>" target="_blank" class="btn btn-default btn-sm" style="padding: 3px 12px;vertical-align: top;margin-left: 5px;display: inline-block;">Export excel</a>');
    });
</script>


</body>
</html>
