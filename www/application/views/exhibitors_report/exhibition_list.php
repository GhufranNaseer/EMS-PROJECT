<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="exhibitor_list_report">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Exhibitors List
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
                        <h3 class="box-title">Exhibitors List</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <table width="2000" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th class="text-center" width="4%">#</th>
                                <th>Company Name</th>
                                <th>Country</th>
                                <th>City</th>
                                <th>Company Executive Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Contact Person Name</th>
                                <th>Email</th>
                                <th>Cell Phone</th>
                                <th>Stall Size</th>
                                <th>Stall Type</th>
                                <th>Stall Name</th>
                                <th>Hall Name</th>
                            </tr>
                            </thead>

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
            "bServerSide": true,
            "bProcessing": true,
            "fnRowCallback": null,
            "fnInitComplete": null,
			"sAjaxSource": '<?php echo base_url('exhibitions_form_status-datatable.html'); ?>?id=<?= $this->input->get('id') ?>',
			"aoColumns": [
				null,
				null,
				null,
				null,
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
			file_name: 'Exhibitors List',
			export_type: ['excel'],
            event_id: '<?= $this->input->get('id') ?>',
		})
	});


</script>


</body>
</html>
