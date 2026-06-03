<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="badges_report">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Badges List
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
                        <h3 class="box-title">Badges List</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <div class="row">
                        <form action="" method="get">
                            <input type="hidden" name="id" value="<?= $this->input->get('id'); ?>">

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Filter Type</label>
                                        <select name="filter_badge" class="form-control">
                                            <option value="">Both</option>
                                            <option value="exhibitor" <?= (($this->input->get('filter_badge') == 'exhibitor') ? 'selected' : '') ?>>Exhibitor</option>
                                            <option value="visitor" <?= (($this->input->get('filter_badge') == 'visitor') ? 'selected' : '') ?>>Visitor</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Exhibit Company</label>
                                        <select name="filter_customer" class="form-control">
                                            <option value="">- All -</option>
                                            <?php
                                            $customers = $this->db
                                                ->select('C.*')
                                                ->where(mycolumn('exhibition_id'), $this->input->get('id'))
                                                ->where('is_approved', 1)
                                                ->where('is_canceled', 0)
                                                ->join('es_customers as C', 'B.customer_id = C.id', 'LEFT')
                                                ->get('es_exhibition_booking as B')
                                                ->result();

                                            foreach ($customers as $customer) {
                                                $selected = ($this->input->get('filter_customer') == $customer->id) ? 'selected' : '';
                                                echo '<option value="'.$customer->id.'" '.$selected.'>'.$customer->company.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                </div></form>
                        </div>

                        <div class="row">
                            <div class="col-sm-8"></div>
                            <div class="col-sm-2">
                                <a href="" disabled="disabled" target="_blank" class="btn btn-default btn-block print_selected_btn" style="margin-bottom: 10px;">Print Selected</a>
                            </div>
                            <div class="col-sm-2">

                                <button type="button" class="btn btn-default check_box_btn btn-block" style="margin-bottom: 10px;">Check All</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                        <table width="2000" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th class="text-center" width="4%">#</th>
                                <th>Country</th>
                                <th>Exhibit Company Name</th>
                                <th>Hall #</th>
                                <th>Stall #</th>
                                <th>Exhibitor Name</th>
                                <th>Designation</th>
                                <th>CNIC Number</th>
                                <th>Passport Number</th>
                                <th>Picture</th>
                                <th>Barcode Data</th>
                                <th>Details input date</th>
                                <th>Collection Person Name</th>
                                <th>Collection Person NIC</th>
                                <th>Print Status</th>
                                <th>Action</th>
                                <th>Edit</th>
                                <th>Select</th>
                            </tr>
                            </thead>

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
			"sAjaxSource": '<?php echo base_url('badges-report-datatable.html'); ?>' + location.search,
			"aaSorting":       [[1, 'asc']],
			"aoColumns": [
				{ "bSearchable": false },
				null,
				null,
				null,
				null,
				null,
				null,
				{ "bSearchable": false },
				null,
				{ "bSearchable": false },
                null,
				null,
				{ "bSearchable": false },
				{ "bSearchable": false },
				{ "bSearchable": false },
				{ "bSearchable": false },
				{ "bSearchable": false },
				{ "bSearchable": false },
			]
		}));
		my_datatable(oTable, {
			exportable: true,
			file_name: 'Badges List',
			export_type: ['excel'],
			event_id: '<?= $this->input->get('id') ?>',
		})

		$(oTable.selector + '_wrapper .dataTables_filter')
			.append('<a href="<?= base_url('reports/badges_report/pdf_export_report') ?>'+location.search+'" target="_blank" class="btn btn-default btn-sm" style="padding: 3px 12px;vertical-align: top;margin-left: 5px;display: inline-block;">Export PDF</a>');
	});
    $(document).on('click', '.check_box_btn', function (e) {
        e.stopImmediatePropagation();

      // alert("sdfs");
        $(".check_box").prop( "checked", true );
        update_print_btn_link();
    });
    $(document).on('change', '.check_box', function (e) {
        e.stopImmediatePropagation();

        update_print_btn_link();
    });


    function update_print_btn_link() {
		var filter_badge = '<?= $this->input->get('filter_badge'); ?>';
        $('.print_selected_btn').attr('href', '');
        $('.print_selected_btn').attr('disabled', true);
        var selected = [];
        $(".check_box:checked").each(function () {
            selected.push($(this).val())
        });


        if (selected.length > 0) {
            $('.print_selected_btn').attr('href', '<?= base_url('print-multiple-badges.html?booking_id=' . time()) ?>&&badges=' + selected.join(',') + '&filter_badge=' + filter_badge);
            $('.print_selected_btn').attr('disabled', false);
        }
    }

</script>


</body>
</html>
