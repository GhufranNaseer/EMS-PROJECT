<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<div class="content-wrapper" data-page="print_job">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Print Job
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

                        <table width="100%" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th class="text-center" width="10%">#</th>
                                <th>Exhibit Company Name</th>
                                <th>Total Badges</th>
                                <th>Printed Badges</th>
                                <th>Remaning Print</th>
                                <th class="text-center" width="25%">Action</th>
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
			"sAjaxSource": '<?php echo base_url('print-job-exhibitors-datatable.html'); ?>?id=<?= $this->input->get('id') ?>',
			"aoColumns": [
				{ "bSearchable": false },
				null,
				{ "bSearchable": false },
				{ "bSearchable": false },
				{ "bSearchable": false },
				{ "bSearchable": false },
			]
		}));

		setInterval(function () {
			oTable._fnAjaxUpdate()
		}, 5000)
	});

</script>

</body>
</html>
