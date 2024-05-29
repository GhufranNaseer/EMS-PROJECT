<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>
<style>
    .col-srialno
    {
        width: 40px !important;
    }

</style>

<div class="content-wrapper" data-page="meeting_report">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Meeting List
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
                        <h3 class="box-title">Meeting List</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <form action="" method="get">
                            <input type="hidden" name="id" value="<?= $this->input->get('id') ?>">
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label>Status</label>
                                    <select name="filter_status" class="form-control">
                                        <option value="">- select -</option>
                                        <option value="approved" <?= (($this->input->get('filter_status') && $this->input->get('filter_status') == 'approved') ? 'selected' : '') ?>>Approved</option>
                                        <option value="pending" <?= (($this->input->get('filter_status') && $this->input->get('filter_status') == 'pending') ? 'selected' : '') ?>>Pending</option>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                </div>
                            </div>
                        </form>

                        <table width="100%" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th class="text-center col-srialno"  >#</th>
                                <th>Meeting From</th>
                                <th>Meeting To</th>
                                <th>Exhibition Day</th>
                                <th>Meeting Date</th>
                                <th>Meeting Time</th>
                                <th>Status</th>
                                <th>Feedback</th>
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
            "sAjaxSource": '<?php echo base_url('meeting-report_status-datatable.html'); ?>' + location.search,
        }));
        my_datatable(oTable, {
            exportable: true,
            file_name: 'Meeting Report',
            export_type: ['excel'],
            event_id: '<?= $this->input->get('id') ?>',
        })
    });


</script>


</body>
</html>
