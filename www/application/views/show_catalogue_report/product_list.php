<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="show_catalogue">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Product List
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
                        <h3 class="box-title">Product List</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <table width="100%" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th class="text-center" width="4%">#</th>
                                <th>Company Name</th>
                                <th>Product Category</th>
                                <th>Product Name</th>
                                <th>Product Type</th>
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

                                $products = (isset($data->products) && isset($data->products->product)) ? $data->products->product : array();

                                foreach ($products as $product) {
                                    echo '<tr>
                                    <td>'.$count.'</td>
                                    <td>'.$row->company.'</td>
                                    <td>'.$product->category.'</td>
                                    <td>'.$product->name.'</td>
                                    <td>'.$product->type.'</td>
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
            .append('<a href="<?= base_url('show_product-report-export') ?>'+location.search+'" target="_blank" class="btn btn-default btn-sm" style="padding: 3px 12px;vertical-align: top;margin-left: 5px;display: inline-block;">Export excel</a>');
	});


</script>


</body>
</html>
