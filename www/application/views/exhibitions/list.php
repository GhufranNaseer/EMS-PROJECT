<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="exhibitions">
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
                        <h3 class="box-title">Events List</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <div class="row">
                            <div class="col-xs-10">

                            </div>

                            <div class="col-xs-2">
                                <a href="<?= base_url('exhibitions-add.html') ?>"
                                   class="btn btn-primary btn-block margin-bottom">Add New</a>
                            </div>

                        </div>

                        <table width="100%" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th class="text-center" width="10%">#</th>
                                <th>Event Logo</th>
                                <th>Event</th>
                                <th>Venue</th>
                                <th>Booking Expire Date</th>
                                <th>Price Type</th>
                                <th class="text-center" width="15%">Action</th>
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
			"sAjaxSource": '<?php echo base_url('exhibitions-datatable.html'); ?>',
		}));

	});
</script>


</body>
</html>
