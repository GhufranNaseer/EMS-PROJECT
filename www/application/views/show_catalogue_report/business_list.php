<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="show_catalogue">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Business Sector Report
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
                        <h3 class="box-title">Business Sector Report</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <table width="100%" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th class="text-center" width="4%">#</th>
                                <th>Company Name</th>
                                <th>Type</th>
                                <th>Business Sector</th>
                                <th>Business Type</th>
                                <th>Business Area</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            if ($this->input->get('booking_id')) {
                                $this->db->where('F.booking_id', $this->input->get('booking_id'));
                            }

                            $rows = $this->db
                                ->select('F.*, C.company')
                                ->where(mycolumn('F.exhibition_id'), $_GET['id'])
                                ->where('F.form_id', 3)
                                ->join('es_exhibition_booking as B', 'F.booking_id = B.id', 'LEFT')
                                ->join('es_customers as C', 'B.customer_id = C.id', 'LEFT')
                                ->get('es_exhibition_booking_forms_data as F')
                                ->result();


                            $count = 1;
                            foreach ($rows as $row) {
                                $data = (isset($row->form_data)) ? json_decode($row->form_data) : null;

                                $main_sectors = (isset($data->products) && isset($data->products->main)) ? $data->products->main : array();
                                $other_sectors = (isset($data->products) && isset($data->products->other)) ? $data->products->other : array();

                                foreach ($main_sectors as $main_sector) {
                                    echo '<tr>
                                    <td>'.$count.'</td>
                                    <td>'.$row->company.'</td>
                                    <td>Main</td>
                                    <td>'.$main_sector->sector.'</td>
                                    <td>'.$main_sector->type.'</td>
                                    <td>'.$main_sector->area.'</td>
                                    </tr>';

                                    $count++;
                                }

                                foreach ($other_sectors as $other_sector) {
                                    echo '<tr>
                                    <td>'.$count.'</td>
                                    <td>'.$row->company.'</td>
                                    <td>Other</td>
                                    <td>'.$other_sector->sector.'</td>
                                    <td>'.$other_sector->type.'</td>
                                    <td>'.$other_sector->area.'</td>
                                    </tr>';

                                    $count++;
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
            .append('<a href="<?= base_url('show_business_sector-report-export') ?>'+location.search+'" target="_blank" class="btn btn-default btn-sm" style="padding: 3px 12px;vertical-align: top;margin-left: 5px;display: inline-block;">Export excel</a>');
	});


</script>


</body>
</html>
