<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="event_advertisment">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Advertisement
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
                        <h3 class="box-title">Advertisement List</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <div class="row">
                            <div class="col-xs-10">

                            </div>

                            <div class="col-xs-2">
                                <a href="<?= base_url('advertisment-add.html')?>?id=<?= $this->input->get('id') ?>"
                                   class="btn btn-primary btn-block margin-bottom">Add New</a>
                            </div>

                        </div>

                        <table width="100%" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th class="text-center" width="10%">#</th>
                                <th>Attachment</th>
                                <th>Title</th>
                                <th>Created on</th>
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
			"sAjaxSource": '<?= base_url('advertisment-event-datatable.html'); ?>?id=<?= $this->input->get('id') ?>',
		}));

	});
</script>


</body>
</html>
