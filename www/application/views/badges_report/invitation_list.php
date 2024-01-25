<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<style>
    #crud-table i span {
        display: none;
    }
</style>

<div class="content-wrapper" data-page="badges_report">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Invitation List
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
                        <h3 class="box-title">Invitation List</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="" method="get">
                            <input type="hidden" name="id" value="<?= $this->input->get('id'); ?>">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Invitation</label>
                                        <select name="filter_invitation" class="form-control">
                                            <option value="">- select -</option>
                                            <option value="inauguration" <?= (($this->input->get('filter_invitation') == 'inauguration') ? 'selected' : '') ?>>Inauguration</option>
                                            <option value="seminar" <?= (($this->input->get('filter_invitation') == 'seminar') ? 'selected' : '') ?>>Seminar</option>
                                            <option value="sideline_conference" <?= (($this->input->get('filter_invitation') == 'sideline_conference') ? 'selected' : '') ?>>Side Line</option>
                                            <option value="governor_reception" <?= (($this->input->get('filter_invitation') == 'governor_reception') ? 'selected' : '') ?>>Gov. Reception</option>
                                            <option value="gala_dinner" <?= (($this->input->get('filter_invitation') == 'gala_dinner') ? 'selected' : '') ?>>Gala Dinner</option>
                                            <option value="closing_ceremony" <?= (($this->input->get('filter_invitation') == 'closing_ceremony') ? 'selected' : '') ?>>Closing Cer</option>
                                            <option value="karachi_air_show" <?= (($this->input->get('filter_invitation') == 'karachi_air_show') ? 'selected' : '') ?>>Karachi Air Show</option>
                                            <option value="cm_reception" <?= (($this->input->get('filter_invitation') == 'cm_reception') ? 'selected' : '') ?>>CM Reception</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="crud-table" width="2800">
                                <thead>
                                <tr class="">
                                    <th class="text-center" width="4%">#</th>
                                    <th style="width: 60px">Name</th>
                                    <th style="width: 60px">Designation</th>
                                    <th style="width: 60px">CNIC Number</th>
                                    <th style="width: 60px">Passport Number</th>
                                    <th style="width: 60px">Contact #</th>
                                    <th style="width: 60px">Company Name</th>
                                    <th style="width: 200px">Company Address</th>
                                    <th style="width: 60px">Last Update Date</th>
                                    <?php
                                    if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'inauguration') {
                                        echo '<th style="width: 100px">Inauguration</th>';
                                    }
                                    if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'seminar') {
                                        echo '<th style="width: 100px">Seminar</th>';
                                    }
                                    if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'sideline_conference') {
                                        echo '<th style="width: 100px">Side Line</th>';
                                    }
                                    if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'governor_reception') {
                                        echo '<th style="width: 100px">Gov. Reception</th>';
                                    }
                                    if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'gala_dinner') {
                                        echo '<th style="width: 100px">Gala Dinner</th>';
                                    }
                                    if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'closing_ceremony') {
                                        echo '<th style="width: 100px">Closing Cer</th>';
                                    }
                                    if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'karachi_air_show') {
                                        echo '<th style="width: 100px">Karachi Air Show</th>';
                                    }
                                    if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'cm_reception') {
                                        echo '<th style="width: 100px">CM Reception</th>';
                                    }
                                    ?>
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
			"sAjaxSource": '<?php echo base_url('invitation-report-datatable.html'); ?>' + location.search,
			"aoColumns": [
				{ "bSearchable": false },
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
                <?php
				if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'inauguration') {
				    echo '{ "bSearchable": false },';
                }
				if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'seminar') {
				    echo '{ "bSearchable": false },';
                }
				if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'sideline_conference') {
				    echo '{ "bSearchable": false },';
                }
				if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'governor_reception') {
				    echo '{ "bSearchable": false },';
                }
				if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'gala_dinner') {
				    echo '{ "bSearchable": false },';
                }
				if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'closing_ceremony') {
				    echo '{ "bSearchable": false },';
                }
				if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'karachi_air_show') {
				    echo '{ "bSearchable": false },';
                }
				if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'cm_reception') {
				    echo '{ "bSearchable": false },';
                }
                ?>
			]
		}));
		my_datatable(oTable, {
			exportable: true,
			file_name: 'Badges List',
			export_type: ['excel'],
			event_id: '<?= $this->input->get('id') ?>',
		})
	});


</script>


</body>
</html>
