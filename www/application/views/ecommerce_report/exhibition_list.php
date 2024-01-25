<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<?php

$category_id = null;

if ($this->input->get('category_id') && $this->input->get('category_id') != '') {
    $category_id = $this->input->get('category_id');

	$category = $this->db
		->where('id', $this->input->get('category_id'))
		->get('es_inventory_category')
		->row();
}

?>

<div class="content-wrapper" data-page="ecommerce_report_<?= $this->input->get('category_id'); ?>">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Additional Items Report
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
                        <h3 class="box-title"><?= $category->category_title ?> Report</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <div class="row">
                            <form method="get" action="">
                                <input type="hidden" name="id" value="<?= $this->input->get('id') ?>">
                                <div class="col-xs-3">

                                    <div class="form-group">
                                        <select class="form-control" name="category_id" id="filter_event">
                                            <?php
											$categories =  $this->db
                                                ->where('parent_id', null)
                                                ->where('is_deleted', 0)
                                                ->get('es_inventory_category')
                                                ->result();
                                            foreach ($categories as $cat) {
                                                $selected = ($cat->id == $category_id) ? 'selected' : '';
                                                echo '<option value="'.$cat->id.'" '.$selected.'>'.$cat->category_title.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xs-2"><button class="btn btn-primary btn-block">Search</button></div>
                            </form>
                            <div class="col-xs-7"></div>

                        </div>

                        <div class="table-responsive">
                            <table  class="table table-bordered table-striped" id="crud-table">
                                <?php
                                $rows = $this->db
                                    ->where('I.category_id', $category_id)
                                    ->where(mycolumn('I.exhibition_id'), $this->input->get('id'))
                                    ->get('es_inventory_item as I')
                                    ->result();
                                ?>
                                <thead>

                                <tr class="">
                                    <th>#</th>
                                    <th width="15%">Exhibit Company Name</th>
                                    <th width="10%">Hall</th>
                                    <th width="10%">Stall</th>
                                    <?php
                                    foreach ($rows as $row) {
                                        echo '<th>'.$row->item_title.'</th>';
                                    }
                                    ?>
                                </tr>
                                </thead>

                                <tbody>
                                <?php

                                $customers= $this->db
                                    ->select('B.*, C.company')
                                    ->where(mycolumn('B.exhibition_id'), $this->input->get('id'))
									->where('B.is_canceled', 0)
									->where('B.is_approved', 1)
                                    ->group_by('B.id')
                                    ->join('es_customers as C', 'B.customer_id = C.id', 'LEFT')
                                    ->join('es_exhibition_order as O', 'O.booking_id = B.id', 'LEFT')
                                    ->get('es_exhibition_booking as B')
                                    ->result();
                                $row_html = '';
                                $mycount = 1;
                                foreach ($customers as $count => $customer) {
									$row_html = '<tr>';
									$row_html .= '<td>'.$mycount.'</td>';
									$row_html .= '<td width="15%">'.$customer->company.'</td>';
                                    $halls = $this->db
                                        ->where('S.booking_id', $customer->id)
                                        ->join('es_location_halls as C', 'S.hall_id = C.id')
                                        ->get('es_exhibition_booking_stalls as S')
                                        ->result();
                                    $hall_name="";
                                    $check_halls = array();
                                    foreach ($halls as $hall)
                                    {
                                        if(in_array($hall->hall_title, $check_halls)) {
                                            continue;
                                        }
                                        $hall_name .=$hall->hall_title.' ,';
                                        $check_halls[] = $hall->hall_title;
                                    }
									$row_html .= '<td width="10%">'.rtrim($hall_name,',').'</td>';

                                    $stalls = $this->db
                                        ->where('S.booking_id', $customer->id)
                                        ->join('es_exhibition_stalls as C', 'S.stall_id = C.id')
                                        ->get('es_exhibition_booking_stalls as S')
                                        ->result();
                                    $stall_name="";
                                    foreach ($stalls as $stall) {
                                        $stall_name .=$stall->stall_name.' ,';
                                    }

									$row_html .= '<td width="10%">'.rtrim($stall_name,',').'</td>';
                                    $has_row_data = false;
                                    foreach ($rows as $row) {

                                        $count_data = $this->db
                                            ->select('SUM(item_quantity) as total_quantity')
                                            //->where('customer_id', $customer->customer_id)
                                            ->where('booking_id', $customer->id)
                                            ->where('item_id', $row->id)
                                            //->where(mycolumn('exhibition_id'), $this->input->get('id'))
                                            ->get('es_exhibition_order_items')
                                            ->row();

                                        if ($count_data->total_quantity) {
											$has_row_data = true;
                                        }
										$row_html .= '<td>'.(($count_data->total_quantity) ? $count_data->total_quantity : '-').'</td>';
                                        //echo '<td>'.$row->id.'</td>';
                                    }
									$row_html .= '</tr>';
                                    if ($has_row_data) {
										echo $row_html;
										$mycount++;
									}
                                }

                                ?>
                                </tbody>
                            </table>
                        </div>
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

//		$('#crud-table_wrapper .dataTables_filter')
//			.append('<a href="<?//= base_url('reports/visa_to_pakistan_report/export_report?id=' . $_GET['id']) ?>//" target="_blank" class="btn btn-default btn-sm" style="padding: 3px 12px;vertical-align: top;margin-left: 5px;display: inline-block;">Export excel</a>');
	});
</script>

</body>
</html>
