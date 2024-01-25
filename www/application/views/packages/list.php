<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<style>
    .package_icon {
        font-size: 40px;
        margin: 20px 0;
    }
</style>

<div class="content-wrapper" data-page="packages">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Packages
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
                        <h3 class="box-title">Packages List</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th class="text-center">Event Logo</th>
                                    <th>Event</th>
                                    <td><?= $this->formdata->exhibition_title ?></td>
                                </tr>
                                <tr>
                                    <td rowspan="3" class="text-center">
                                        <img src="<?= base_url($this->formdata->event_logo) ?>" alt="" class="img-thumbnail" style="width: 100px">
                                    </td>
                                    <th>Price Type</th>
                                    <td><?= $this->formdata->price_type ?></td>
                                </tr>
                                <tr>
                                    <th>Booking Expire Date</th>
                                    <td><?= $this->formdata->booking_expire_date ?></td>
                                </tr>
                                <tr>
                                    <th>Event Location</th>
                                    <td><?= $this->db->where('id', $this->formdata->location_id)->get('es_locations')->row()->location_title ?></td>
                                </tr>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <button type="button" class="btn btn-default btn-lg margin-bottom copy_package_modal_btn">Copy Packages for Other Event</button>
                            </div>
                            <div class="col-sm-6 text-right">
                                <a href="<?= base_url('packages-add.html') ?>?id=<?= $this->input->get('id') ?>"
                                   class="btn btn-primary btn-lg margin-bottom">Add New Package</a>
                            </div>
                        </div>

                        <table width="100%" class="table table-bordered table-striped" id="gallery_table">
                            <thead>
                            <tr class="">
                                <th>Package Title</th>
                                <th>Package Icon</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th class="text-center" width="10%">Action</th>
                            </tr>
                            </thead>

                        </table>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>

<!-- Lightbox for  -->
<div class="modal fade" id="copy_package_modal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Copy Packages from Other event</h4>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('inventory/packages/copy_other_event_packages') ?>?id=<?= $this->input->get('id') ?>" method="post" id="copy_form"
                      enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Select Event</label>
                        <select name="event_id" class="form-control">
                            <?php
                            $events = $this->db
                                ->where('id !=', $this->formdata->id)
                                ->where('is_deleted', 0)
                                ->get('es_exhibitions')
                                ->result();
                            $has_data = false;
                            foreach ($events as $event) {
                                $check = $this->db
                                    ->where('exhibition_id', $event->id)
                                    ->count_all_results('es_packages');

                                if ($check > 0) {
									$has_data = true;
									echo '<option value="'.$event->id.'">'.$event->exhibition_title.'</option>';
								}
                            }

                            if (!$has_data) {
								echo '<option value="">No event found!</option>';
                            }

                            ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success" form="copy_form">Save</button>
            </div>
        </div>
    </div>
</div> <!-- /.modal -->

<?php $this->load->view('includes/after_login/footer'); ?>

<script type="text/javascript">
	$(document).ready(function () {
		oTable = $('#gallery_table').dataTable($.extend(datatable_settings, {
			"sAjaxSource": '<?php echo base_url('packages-datatable.html'); ?>?id=<?= $this->input->get('id') ?>',
			"fnRowCallback":   function (nRow, aData, iDisplayIndex) {
				//var oSettings = oTable.fnSettings();
				//$("td:first", nRow).html(oSettings._iDisplayStart + iDisplayIndex + 1);
				return nRow;
			}
		}));
	});

	$(document).on('click', '.copy_package_modal_btn', function (e) {
		e.stopImmediatePropagation();

		$('#copy_package_modal').modal({backdrop: 'static', keyboard: false, show: true}); // open lightbox
	});
</script>


</body>
</html>
