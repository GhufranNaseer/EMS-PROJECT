<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<style>
	.business_table {
		height: 250px;
		overflow: auto;
	}
	.business_table td:last-child {
		text-align: center;
	}
	.business_table td:last-child a {
		color: #333;
	}
</style>

<div class="content-wrapper" data-page="other_settings">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Settings
			<small>View</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="<?= base_url('other-settings.html'); ?>"><i class="fa fa-dashboard"></i>Settings</a></li>
			<li class="active">View</li>
		</ol>
	</section>

	<!-- Main content -->
	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title">View Settings</h3>
					</div><!-- /.box-header -->

					<div class="box-body">

						<div class="row">
							<div class="col-sm-4">
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title">Main Business Sectors</div>
									</div>
									<div class="panel-body">
										<form action="<?= base_url('settings/add_business_sector') ?>" method="post" id="main_business_sector_form">
											<div class="input-group">
												<input type="hidden" name="type" value="main_business_sector">
												<input type="text" class="form-control" placeholder="Main Business Sector" name="data">
												<div class="input-group-btn">
													<button class="btn btn-primary js-form_btn" type="button">Add</button>
												</div>
											</div>
											<div class="js-msgbox"></div>
										</form>
									</div>
									<div class="business_table">
										<table class="table table-striped">
											<?php
											$rows = $this->db
												->where('type', 'main_business_sector')
												->get('input_data_list')
												->result();
											$count = 0;
											foreach ($rows as $row) {
												$count++;
												echo '<tr>
												<td>'.$count.'</td>
												<td>'.$row->data.'</td>
												<td><a href="'. base_url('settings/remove_business_sector?id=' . $row->id) .'"><i class="fa fa-times"></i></a></td>
												</tr>';
											}
											?>
										</table>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title">Main Business Types</div>
									</div>
									<div class="panel-body">
										<form action="<?= base_url('settings/add_business_sector') ?>" method="post" id="main_business_type_form">
											<div class="input-group">
												<div class="input-group-btn">
													<button class="btn btn-primary" type="button" data-toggle="modal" data-target="#myModal" style="float: right;">Add</button>
												</div>
											</div>
											<div class="js-msgbox"></div>
										</form>
									</div>
									<div class="business_table">
										<table class="table table-striped">
											<?php
											$rows = $this->db
												->like('type', 'main_business_type')
												->get('input_data_list')
												->result();
											$count = 0;
											foreach ($rows as $row) {
											    $sec_id = explode('_', $row->type);
											    $sec_id = $sec_id[count($sec_id) - 1];
											    $sec = $this->db
                                                    ->where('id', $sec_id)
                                                    ->get('input_data_list')
                                                    ->row();

												$count++;
												echo '<tr>
												<td>'.$count.'</td>
												<td>'.$sec->data.' > '.$row->data.'</td>
												<td><a href="'. base_url('settings/remove_business_sector?id=' . $row->id) .'"><i class="fa fa-times"></i></a></td>
												</tr>';
											}
											?>
										</table>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title">Main Business Area</div>
									</div>
									<div class="panel-body">
										<form action="<?= base_url('settings/add_business_sector') ?>" method="post" id="main_business_area_form">
											<div class="input-group">
												<div class="input-group-btn">
													<button class="btn btn-primary" style="float: right;" data-toggle="modal" data-target="#myModal2"" type="button">Add</button>
												</div>
											</div>
											<div class="js-msgbox"></div>
										</form>
									</div>
									<div class="business_table">
										<table class="table table-striped">
											<?php
											$rows = $this->db
												->like('type', 'main_business_area')
												->get('input_data_list')
												->result();
											$count = 0;
											foreach ($rows as $row) {
												$sec_id = explode('_', $row->type);
												$sec_id = $sec_id[count($sec_id) - 2];
												$type_id = explode('_', $row->type);
												$type_id = $type_id[count($type_id) - 1];
												$sec = $this->db
													->where('id', $sec_id)
													->get('input_data_list')
													->row();
												$typ = $this->db
													->where('id', $type_id)
													->get('input_data_list')
													->row();

												$count++;
												echo '<tr>
												<td>'.$count.'</td>
												<td>'.$sec->data.' > '.$typ->data.' > '.$row->data.'</td>
												<td><a href="'. base_url('settings/remove_business_sector?id=' . $row->id) .'"><i class="fa fa-times"></i></a></td>
												</tr>';
											}
											?>
										</table>
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>

			</div>
		</div>

	</section>

</div>

<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Main Business Type</h4>
            </div>
            <div class="modal-body" id="model-body-html">
                <form action="<?= base_url('settings/add_business_sector') ?>" method="post" id="main_business_type_form">
                    <input type="hidden" name="type" value="main_business_type">
                    <div class="form-group">
                        <label>Main Business Sector</label>
                        <select type="text" class="form-control" name="sector_id">
                            <option value="">- select -</option>
                            <?php
                            $rows = $this->db
                                ->where('type', 'main_business_sector')
                                ->get('input_data_list')
                                ->result();
                            foreach ($rows as $row) {
                                echo '<option value="'.$row->id.'">'.$row->data.'</option>';
                            }?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Main Business Type</label>
                        <input type="text" name="data" class="form-control">
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>







<div class="modal fade" id="myModal2" role="dialog">
    <div class="modal-dialog modal-sm">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Main Business Area</h4>
            </div>
            <div class="modal-body" id="model-body-html">
                <form action="<?= base_url('settings/add_business_sector') ?>" method="post" id="main_business_area_form">
                    <input type="hidden" name="type" value="main_business_area">
                    <div class="form-group business_sector">
                        <label>Main Business Sector</label>
                        <select type="text" class="form-control get_type_for_area" name="sector_id">
                            <option value="">- select -</option>
                            <?php
                            $rows = $this->db
                                ->where('type', 'main_business_sector')
                                ->get('input_data_list')
                                ->result();
                            foreach ($rows as $row) {
                                echo '<option value="'.$row->id.'">'.$row->data.'</option>';
                            }?>
                        </select>
                    </div>

                    <div class="form-group main_business_type">
                        <label>Main Business Type</label>
                        <select type="text" class="form-control load_main_business_type" name="main_business_area">

                        </select>
                    </div>

                    <div class="form-group">
                        <label>Main Business Area</label>
                        <input type="text" name="data" class="form-control">
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php $this->load->view('includes/after_login/footer'); ?>

<script type="text/javascript">

	doFormValidation({
		'form':         '#main_business_sector_form',
		'msgbox':       '#main_business_sector_form .js-msgbox',
		'btnClick':     '#main_business_sector_form .js-form_btn',
		'urlValidator': "<?php echo base_url("settings/add_business_sector_validate/doError"); ?>",
		'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
	});
	doFormValidation({
		'form':         '#main_business_type_form',
		'msgbox':       '#main_business_type_form .js-msgbox',
		'btnClick':     '#main_business_type_form .js-form_btn',
		'urlValidator': "<?php echo base_url("settings/add_business_sector_validate/doError"); ?>",
		'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
	});
	doFormValidation({
		'form':         '#main_business_area_form',
		'msgbox':       '#main_business_area_form .js-msgbox',
		'btnClick':     '#main_business_area_form .js-form_btn',
		'urlValidator': "<?php echo base_url("settings/add_business_sector_validate/doError"); ?>",
		'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
	});

    $(document).on('change', '.get_type_for_area', function (e) {
	    e.stopImmediatePropagation();

	    var v = $(this).val();
        $('.load_main_business_type').html('');
	    if (v == '') return;

        $.ajax({
            type:    'post',
            url:     '<?= base_url("settings/get_main_business_type"); ?>',
            data:    {sector_id: v},
            success: function (data) {
                data = JSON.parse(data);

                if (data.error) {
                    return;
                }

                $('.load_main_business_type').html('');
                for (var i=0; i<data.data.length; i++) {
                    $('.load_main_business_type').append('<option value="'+data.data[i].id+'">'+data.data[i].data+'</option>');
                }
            },
            error:   function () {
            }
        });
    });



</script>


</body>
</html>
