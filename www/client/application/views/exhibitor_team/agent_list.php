<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="agent-list">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Agent list
            <small></small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Agent List</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <table width="100%" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th class="text-center" width="8%">#</th>
                                <th width="13%">Company</th>
                                <th width="14%">Name</th>
                                <th width="17%">Country</th>
                                <th width="15%">Phone</th>
                                <th width="13%">Email</th>
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

<!-- Modal -->
<div id="detailModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Exhibitor Badges</h4>
            </div>
            <div class="modal-body">
                <p id="modal-content">Loading...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<?php $this->load->view('includes/after_login/footer'); ?>

<script type="text/javascript">
	$(document).ready(function () {
		oTable = $('#crud-table').dataTable($.extend(datatable_settings, {
			"sAjaxSource": '<?php echo base_url('agent-datatable.html'); ?>',
		}));
        // Handle detail button click
        $('#crud-table').on('click', '.btn-detail', function() {
            var id = $(this).data('id');
            // Make an AJAX request to fetch the details
            $.ajax({
                url: "<?php echo base_url('exhibitor_team/get_agent_details'); ?>",
                method: 'POST',
                data: { id: id },
                success: function(response) {
                    // Show the modal and display the details
                    $('#modal-content').html(response);
                    $('#detailModal').modal('show');
                }
            });
        });
	});


</script>


</body>
</html>
