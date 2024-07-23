<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="event_for_meeting">
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
                        <h3 class="box-title"><?= ($this->input->get('type') == "mou_location" ? 'MoU Signing Location' : 'Meeting Location') ?> List</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
					<div class="row">
                            <div class="col-xs-10">

                            </div>

                            <div class="col-xs-2">
                                <a href="<?= base_url('Location_for_meeting-add.html') ?>?exhibition_id=<?= $this->input->get('exhibition_id') ?>&type=<?= $this->input->get('type') ?>"
                                   class="btn btn-primary btn-block margin-bottom">Add New</a>
                            </div>

                        </div>

                        <table width="100%" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th class="text-center" width="10%">#</th>
                                <th>Location</th>
                                <th class="text-center" width="30%">Action</th>
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
			"sAjaxSource": "<?php echo base_url('Location_meeting-datatable.html'); ?>?exhibition_id=<?= $this->input->get('exhibition_id') ?>&type=<?= $this->input->get('type') ?> ",
		}));

	});
</script>


</body>
</html>
