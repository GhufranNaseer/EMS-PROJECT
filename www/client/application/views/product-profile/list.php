<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>
<style>
	.col-srialno {
		width: 40px !important;
	}
	.col-name {
		width: 180px !important;
	}
	.col-data {
		width: 15% !important;
	}
	
	#crud-table ul {
		padding-left: 15px;
		margin: 0;
	}
</style>

<div class="content-wrapper" data-page="search-product-profile">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Search Exhibitors
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
						<h3 class="box-title">Search Exhibitors</h3>
					</div><!-- /.box-header -->

					<div class="box-body">

						<form action="" method="get">
							<div class="form-group row">
								<div class="col-sm-2">
									<label>Sector / Industry</label>
									<input type="text" class="form-control" name="filter_sector" value="<?= (isset($_GET['filter_sector']) ? $_GET['filter_sector'] : '') ?>">
								</div>
								<div class="col-sm-2">
									<label>Business Types</label>
									<input type="text" class="form-control" name="filter_type" value="<?= (isset($_GET['filter_type']) ? $_GET['filter_type'] : '') ?>">
								</div>
								<div class="col-sm-2">
									<label>Business Areas</label>
									<input type="text" class="form-control" name="filter_area" value="<?= (isset($_GET['filter_area']) ? $_GET['filter_area'] : '') ?>">
								</div>
								<div class="col-sm-2">
									<label>Products / Product Category</label>
									<input type="text" class="form-control" name="filter_product" value="<?= (isset($_GET['filter_product']) ? $_GET['filter_product'] : '') ?>">
								</div>
								
								<div class="col-sm-2">
									<label>&nbsp;</label>
									<button type="submit" class="btn btn-primary btn-block">Search</button>
								</div>
							</div>
						</form>

						<table class="table table-bordered table-striped" id="crud-table">
							<thead>
								<tr class="">
									<th class="text-center col-srialno">#</th>
									<th class="col-name">Exhibitor</th>
									<th>City</th>
									<th>Country</th>
									<th class="col-data">Business Sectors</th>
									<th class="col-data">Business Types</th>
									<th class="col-data">Business Areas</th>
									<th class="col-data">Products</th>
									<th class="text-center">Profile</th>
									<th class="text-center">Schedule</th>
									<th class="text-center">Action</th>
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
<div id="detailModal" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 50%;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Agent Details</h4>
            </div>
            <div class="modal-body">
                <p id="modal-content">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Person Full Name</th>
								<th>Designation</th>
								<th>Mobile #</th>
								<th>Nationality</th>
								<th>CNIC / Passport</th>
								<th>Email Address</th>
								<th>Created Date</th>
								<th>Picture</th>
							</tr>
						</thead>
						<tbody id="inner_badge_table">
							<tr><td colspan="8">Loading ...</td></tr>
						</tbody>
					</table>	
				</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<?php $this->load->view('includes/after_login/footer'); ?>

<script type="text/javascript">
	$(document).ready(function() {
		oTable = $('#crud-table').dataTable($.extend(datatable_settings, {
			aLengthMenu: [
				[25, 50, 100, 200, 500],
				[25, 50, 100, 200, 500]
			],
			iDisplayLength:    25,
			"bProcessing":     false,
		"bServerSide":     false,
			"sAjaxSource": '<?php echo base_url('product-profile-datatable.html'); ?>' + location.search,
		}));
		my_datatable(oTable, {
			exportable: false,
			file_name: 'Search Exhibitors',
			export_type: ['excel', 'pdf'],
			event_id: '<?= myid($this->event->id) ?>',
		})

		// Handle detail button click
        $('#crud-table').on('click', '.btn-detail', function() {
            var id = $(this).data('id');
            // Make an AJAX request to fetch the details
            $.ajax({
                url: "<?php echo base_url('exhibitor_team/get_badges'); ?>",
                method: 'POST',
                data: { id: id },
                success: function(response) {
                    // Show the modal and display the details
                    $('#inner_badge_table').html(response);
                    $('#detailModal').modal('show');
                }
            });
        });
	});
</script>


</body>

</html>