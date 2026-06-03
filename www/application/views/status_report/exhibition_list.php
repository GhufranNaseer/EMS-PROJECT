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
<div class="content-wrapper" data-page="form_status_report">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Form Status
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
                        <h3 class="box-title">Form Status List</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <table width="2000" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th width="4%">#</th>
                                <th width="7%">Company Name</th>
                                <th width="7%">Fascia</th>
                                <th width="7%">Stall Builder</th>
                                <th width="7%">Catalog Entry</th>
                                <th width="7%">Budges Invitations</th>
                                <th width="7%">End User Certificate</th>
                                <!--<th width="7%">Additional Items</th>-->
                                <th width="7%">Visit To Pakistan</th>
                                <th width="7%">Hotel Booking</th>
                                <th width="7%">Vehicle Rent</th>
                                <th width="7%">Display Mobility</th>
                            </tr>
                            </thead>

                        </table>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>


<div class="modal fade" id="copy_package_modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="detail_html"></div>
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
            "bServerSide": true,
            "bProcessing": true,
            "fnRowCallback": null,
            "fnInitComplete": null,
			"sAjaxSource": '<?php echo base_url('exhibitions_form_status_2-datatable.html'); ?>?id=<?= $this->input->get('id') ?>',
			"aoColumns": [
				null,
				null,
				{ "bSearchable": false },
				{ "bSearchable": false },
				{ "bSearchable": false },
				{ "bSearchable": false },
				{ "bSearchable": false },
				{ "bSearchable": false },
				{ "bSearchable": false },
				{ "bSearchable": false },
				{ "bSearchable": false },
			]
		}));

	});


	$(document).on('click', '.view_details', function (e) {
        e.stopImmediatePropagation();

        var booking = $(this).attr('data-bookingid');
        var event_id = $(this).attr('data-exhibitionid');
        var form_id = $(this).attr('data-formid');

        $.ajax({
            type:    'post',
            url:     '<?= base_url("reports/status_report/ajax_get_form_data"); ?>',
            data: {
                'booking_id': booking,
                'exhibition_id': event_id,
                'form_id': form_id
            },
            success: function (data) {
                //data = JSON.parse(data);

                //formData = data.form_data;

               // alert(data)
                $('#copy_package_modal .detail_html').html(data);
                $('#copy_package_modal').modal({backdrop: 'static', keyboard: false, show: true});

            },
            error:   function () {
            }
        });


    });



</script>


</body>
</html>
