<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="meeting-officer-<?= $this->input->get('type'); ?>">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= ucwords(str_replace('_', ' ', $this->input->get('type'))) ?>
            <small>List</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">List</li>
        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><?= ucwords(str_replace('_', ' ', $this->input->get('type'))) ?> List</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <table width="100%" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th class="text-center" width="5%">#</th>
                                <th>Country</th>
                                <th>Designation</th>
                                <th>Contact Person</th>
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
			"sAjaxSource": '<?php echo base_url('officer-datatable.html'); ?>' + location.search,
		}));
	});
</script>


</body>
</html>
