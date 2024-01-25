<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="discount">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Discount
            <small>List</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('discount.html'); ?>"><i class="fa fa-dashboard"></i> Discount</a></li>
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
                        <h3 class="box-title">Discount List</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <div class="row">
                            <div class="col-xs-10">

                            </div>

                            <div class="col-xs-2">
                                <a href="<?= base_url('discount-add.html') ?>"
                                   class="btn btn-primary btn-block margin-bottom">Add New</a>
                            </div>

                        </div>

                        <table width="100%" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th class="text-center" width="10%">#</th>
                                <th width="36%">Offer title</th>
                                <th width="36%">Discount in percentage</th>
                                <th width="18%">Action</th>
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
			"sAjaxSource": '<?php echo base_url('discount-datatable.html'); ?>',
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
