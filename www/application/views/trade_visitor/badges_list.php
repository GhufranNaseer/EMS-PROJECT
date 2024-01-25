<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="trade_visitor">
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
                        <form action="" method="get">
                            <input type="hidden" name="id" value="<?= $this->input->get('id') ?>">
                            <div class="row">
                                <?php
                                if ($this->userdata->user_group_id == SALES_PERSON) {
                                    echo '<div class="col-xs-5"></div>';
								} else {
                                ?>
                                <div class="col-xs-3">
                                    <label>Sales Person</label>
                                    <select name="filter_sales_person" class="form-control">
                                        <option value="">- select -</option>
                                        <?php
                                        $users = $this->db
                                            ->where('is_deleted', 0)
                                            ->where('user_group_id', SALES_PERSON)
                                            ->get('users')
                                            ->result();
                                        foreach ($users as $user) {
                                            $total = $this->db
												->where('is_active', 1)
												->where('added_by', $user->id)
												->where('badge_type', 'trade_visitor')
												->count_all_results('es_exhibition_badges');
                                            echo '<option value="'.$user->id.'">'.$user->user_first_name.' '.$user->user_last_name.' ('.$total.')</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-xs-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                </div>
                                <?php } ?>
                                <div class="col-xs-5">

                                </div>

                                <div class="col-xs-2">
                                    <a href="<?= base_url('trade-visitor-add.html') ?>?id=<?= $this->input->get('id') ?>"
                                       class="btn btn-primary btn-block margin-bottom">Add New Badge</a>
                                </div>
                            </div>
                        </form>

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
                            <table class="table table-bordered table-striped" id="crud-table">
                                <thead>
                                <tr class="">
                                    <th class="text-center" width="4%">#</th>
                                    <th>Sales Person</th>
                                    <th>Visitor Name</th>
                                    <th>Designation</th>
                                    <th>CNIC/Passport Number</th>
                                    <th>Barcode Data</th>
                                    <th>Details input date</th>
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
			"sAjaxSource": '<?php echo base_url('trade-visitor-badges-datatable.html'); ?>' + location.search,
			"aoColumns": [
				{ "bSearchable": false },
				null,
				null,
				null,
				null,
				null,
				null,
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
	});
    $(document).on('click', '.check_box_btn', function (e) {
        e.stopImmediatePropagation();

        $(".check_box").prop( "checked", true );
        update_print_btn_link();
    });
    $(document).on('change', '.check_box', function (e) {
        e.stopImmediatePropagation();

        update_print_btn_link();
    });


    function update_print_btn_link() {

        $('.print_selected_btn').attr('href', '');
        $('.print_selected_btn').attr('disabled', true);
        var selected = [];
        $(".check_box:checked").each(function () {
            selected.push($(this).val())
        });


        if (selected.length > 0) {
			$('.print_selected_btn').attr('href', '<?= base_url('print-multiple-badges.html?booking_id=' . time()) ?>&&badges=' + selected.join(','));
            $('.print_selected_btn').attr('disabled', false);
        }
    }

</script>


</body>
</html>
