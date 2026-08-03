<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="exhibitor_list_report">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Exhibitors List
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
                        <h3 class="box-title">Exhibitors List</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <table width="2000" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th class="text-center" width="4%">#</th>
                                <th>Company Name</th>
                                <th>Sector Name</th>
                                <th>Country</th>
                                <th>City</th>
                                <th>Company Executive Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Contact Person Name</th>
                                <th>Email</th>
                                <th>Cell Phone</th>
                                <th>Stall Size</th>
                                <th>Stall Type</th>
                                <th>Stall Name</th>
                                <th>Hall Name</th>
                            </tr>
                            </thead>

                        </table>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>

<!-- Modal for Viewing / Copying Sectors -->
<div class="modal fade" id="sectorViewModal" tabindex="-1" role="dialog" aria-labelledby="sectorModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="sectorModalLabel" style="font-size: 15px; font-weight: bold; color: #3c8dbc;">Business Sectors</h4>
            </div>
            <div class="modal-body">
                <p id="sectorModalCompany" class="text-muted" style="font-size: 12px; margin-bottom: 10px; font-weight: bold;"></p>
                <div id="sectorModalContent" style="background: #f9f9f9; border: 1px solid #e3e3e3; padding: 10px; border-radius: 4px; font-size: 13px; max-height: 250px; overflow-y: auto; user-select: text; -webkit-user-select: text;"></div>
            </div>
            <div class="modal-footer" style="padding: 10px 15px;">
                <button type="button" class="btn btn-default btn-sm pull-left" id="copySectorsBtn"><i class="fa fa-copy"></i> Copy All</button>
                <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Close</button>
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
            "fnDrawCallback": function() {
                if (typeof $.fn.tooltip === 'function') {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            },
            "fnInitComplete": null,
			"sAjaxSource": '<?php echo base_url('exhibitions_form_status-datatable.html'); ?>?id=<?= $this->input->get('id') ?>',
			"aoColumns": [
				{ "mData": 0 },
				{ "mData": 1 },
				{ "mData": 14, "bSearchable": false },
				{ "mData": 2 },
				{ "mData": 3 },
				{ "mData": 4 },
				{ "mData": 5 },
				{ "mData": 6 },
				{ "mData": 7 },
				{ "mData": 8 },
				{ "mData": 9 },
				{ "mData": 10, "bSearchable": false },
				{ "mData": 11, "bSearchable": false },
				{ "mData": 12, "bSearchable": false },
				{ "mData": 13, "bSearchable": false },
			]
		}));
		my_datatable(oTable, {
			exportable: true,
			file_name: 'Exhibitors List',
			export_type: ['excel'],
            event_id: '<?= $this->input->get('id') ?>',
		});

        $(document).on('click', '.view-sectors-btn', function (e) {
            e.preventDefault();
            var company = $(this).attr('data-company');
            var rawSectors = $(this).attr('data-sectors');
            var sectors = [];
            try {
                sectors = JSON.parse(rawSectors);
            } catch (err) {
                console.error(err);
            }

            $('#sectorModalCompany').text(company);
            var html = '<ul style="padding-left: 18px; margin-bottom: 0;">';
            for (var i = 0; i < sectors.length; i++) {
                html += '<li style="margin-bottom: 4px; user-select: text; -webkit-user-select: text;">' + sectors[i] + '</li>';
            }
            html += '</ul>';

            $('#sectorModalContent').html(html);
            $('#sectorViewModal').modal('show');
        });

        $(document).on('click', '#copySectorsBtn', function () {
            var textToCopy = '';
            $('#sectorModalContent li').each(function() {
                textToCopy += $(this).text() + '\n';
            });
            textToCopy = textToCopy.trim();

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(textToCopy).then(function() {
                    alert('Sectors copied to clipboard!');
                }, function() {
                    fallbackCopyText(textToCopy);
                });
            } else {
                fallbackCopyText(textToCopy);
            }
        });

        function fallbackCopyText(text) {
            var dummy = $('<textarea>').val(text).appendTo('body').select();
            document.execCommand('copy');
            dummy.remove();
            alert('Sectors copied to clipboard!');
        }
	});
</script>


</body>
</html>
