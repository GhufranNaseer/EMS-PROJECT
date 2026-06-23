<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="email_template">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Events
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
                        <h3 class="box-title" style="display: inline-block;">Events List</h3>
                        <a href="<?= base_url('email_template.html?exhibition_id=0') ?>" class="btn btn-primary pull-right">
                            <i class="fa fa-globe"></i> Manage Global Templates
                        </a>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <table width="100%" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th class="text-center" width="10%">#</th>
                                <th>Event</th>
                                <th>Venue</th>
                                <th>Total Emails</th>
                                <th class="text-center" width="20%">Action</th>
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
			"sAjaxSource": "<?php echo base_url('exhibition_email_template-datatable.html'); ?>",
		}));

	});
</script>


</body>
</html>
