<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<style>
    .view_details {
        cursor: pointer;
    }
    #copy_package_modal .modal-header {
        font-size: 16px;
        font-weight: bold;
    }
    .modal-header > .row > .col-sm-3:nth-child(3) {
        font-size: 14px;
        font-weight: normal;
    }
</style>
<div class="content-wrapper" data-page="end_user_certificate_report">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            End User Certificate Report
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
                        <h3 class="box-title">End User Certificate Report</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <!--<div class="row">
                            <div class="col-sm-7"></div>
                            <form method="GET" action="">
                                <div class="col-sm-3">
                                    <label>Exhibitors Wise </label>
                                    <select  class="form-control" style="margin-bottom: 10px;" name="company_id">
                                        <option value="" disabled selected>- select -</option>
                                        <?php
/*                                        $comps = $this->db
                                            ->get('es_customers')
                                            ->result();
                                        foreach ($comps as $comp) { */?>
                                            <option value="<?/*=$comp->id*/?>"><?/*=$comp->company*/?></option>
                                        <?php /*}
                                        */?>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <input type="hidden" name="id" value="<?/*=$_GET['id']*/?>"/>
                                    <button class="btn btn-primary btn-block" style="margin-top: 25px;">Search</button>
                                </div>
                            </form>
                        </div>-->
                        <table width="100%" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th>#</th>
                                <th>Company Name</th>
                                <th>Hall</th>
                                <th>Stall</th>
                                <th>Freight Forwader Name</th>
                                <th class="text-center">View Certificate/Print</th>
                            </tr>
                            </thead>

                        </table>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>

<!-- Modal -->



<?php $this->load->view('includes/after_login/footer'); ?>

<script type="text/javascript">
	$(document).ready(function () {
		oTable = $('#crud-table').dataTable($.extend(datatable_settings, {
			"sAjaxSource": '<?php echo base_url('end_user_certificate_report-datatable.html'); ?>' + location.search,
		}));
		my_datatable(oTable, {
			exportable: true,
			file_name: 'End User Certificate Report',
			export_type: ['excel'],
			event_id: '<?= $this->input->get('id') ?>',
		})
	});

</script>


</body>
</html>
