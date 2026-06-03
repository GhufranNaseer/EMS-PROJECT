<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="officer">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            B2B Users
            <small>List</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('officer.html'); ?>"><i class="fa fa-dashboard"></i> B2B Users</a></li>
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
                        <h3 class="box-title">B2B Users List</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <div class="row">
                            <!--<div class="col-xs-3">
                                <a href="<?/*= base_url('officer-add.html') */?>?id=<?/*= $this->input->get('id') */?>"
                                                     class="btn btn-primary btn-block margin-bottom">Add New</a>
                            </div>-->
                            <div class="col-xs-2">
                                <a href="<?= base_url('officer-add-foreign-delegations.html') ?>?id=<?= $this->input->get('id') ?>"
                                   class="btn btn-primary btn-block margin-bottom">Add Foreign Delegations</a>
                            </div>
                            <div class="col-xs-2">
                                <a href="<?= base_url('officer-add-local-delegates.html') ?>?id=<?= $this->input->get('id') ?>"
                                   class="btn btn-primary btn-block margin-bottom">Add Local Delegations</a>
                            </div>
							<div class="col-xs-2">
                                <a href="<?= base_url('officer-add-armed-force.html') ?>?id=<?= $this->input->get('id') ?>"
                                   class="btn btn-primary btn-block margin-bottom">Armed Forces (Pakistan)</a>
                            </div>
                            <div class="col-xs-2">
                                <a href="<?= base_url('officer-add-government-officials.html') ?>?id=<?= $this->input->get('id') ?>"
                                   class="btn btn-primary btn-block margin-bottom">Government Officials</a>
                            </div>
                            <div class="col-xs-2">
                                <a href="<?= base_url('officer-add-organizer.html') ?>?id=<?= $this->input->get('id') ?>"
                                   class="btn btn-primary btn-block margin-bottom">Organizers</a>
                            </div>
                            <div class="col-xs-2">
                                <a href="<?= base_url('officer-add-chief-of-servicing.html') ?>?id=<?= $this->input->get('id') ?>"
                                   class="btn btn-primary btn-block margin-bottom">Add Chief of Servicing</a>
                            </div>
                        </div>


                        <form action="" method="get">
                            <input type="hidden" name="id" value="<?= $this->input->get('id') ?>">
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label>Type</label>
                                    <select name="filter_type" class="form-control">
                                        <option value="">- select -</option>
                                        <option value="foreign_delegates" <?= (($this->input->get('filter_type') && $this->input->get('filter_type') == 'foreign_delegates') ? 'selected' : '') ?>>Foreign Delegates</option>
                                        <option value="local_delegates" <?= (($this->input->get('filter_type') && $this->input->get('filter_type') == 'local_delegates') ? 'selected' : '') ?>>Local Delegates</option>
                                        <option value="chief_of_servicing" <?= (($this->input->get('filter_type') && $this->input->get('filter_type') == 'chief_of_servicing') ? 'selected' : '') ?>>Gov. Chief of Servicing</option>
                                        <option value="armed_force" <?= (($this->input->get('filter_type') && $this->input->get('filter_type') == 'armed_force') ? 'selected' : '') ?>>Armed Forces (Pakistan)</option>
                                        <option value="government_officials" <?= (($this->input->get('filter_type') && $this->input->get('filter_type') == 'government_officials') ? 'selected' : '') ?>>Government Officials</option>
                                        <option value="organizer" <?= (($this->input->get('filter_type') && $this->input->get('filter_type') == 'organizer') ? 'selected' : '') ?>>Organizers</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label>Is Representative</label>
                                    <select name="filter_representative" class="form-control">
                                        <option value="">Both</option>
                                        <option value="self" <?= (($this->input->get('filter_representative') && $this->input->get('filter_representative') == 'self') ? 'selected' : '') ?>>Self</option>
                                        <option value="representative" <?= (($this->input->get('filter_representative') && $this->input->get('filter_representative') == 'representative') ? 'selected' : '') ?>>Representative</option>
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
                                <th class="text-center" width="8%">#</th>
                                <th>Type</th>
                                <th>Designation</th>
                                <th>Country</th>
                                <th>Profile</th>
                                <th>Contact person</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th class="text-center" width="11%">Action</th>
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
		var oTable = $('#crud-table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"sScrollX":    '100%',
			"sAjaxSource": '<?php echo base_url('officer-datatable.html'); ?>'+ location.search,
			"bJQueryUI":   true,
			"fnRowCallback":   function (nRow, aData, iDisplayIndex) {
				var oSettings = oTable.fnSettings();
				$("td:first", nRow).html(oSettings._iDisplayStart + iDisplayIndex + 1);
				return nRow;
			},
			"aaSorting":       [[0, 'desc']],
			"sPaginationType": "full_numbers",
			"iDisplayStart ":  20,
			"oLanguage":       {
				"sProcessing": "<img src='<?php echo base_url(); ?>assets/images/ajax-loader_dark.gif'>"
			},
			"fnInitComplete":  function () {
				oTable.fnAdjustColumnSizing();
			},
			'fnServerData':    function (sSource, aoData, fnCallback) {
				$.ajax({
					'dataType': 'json',
					'type':     'POST',
					'url':      sSource,
					'data':     aoData,
					'success':  fnCallback
				});
			}
		});

	});
</script>


</body>
</html>
