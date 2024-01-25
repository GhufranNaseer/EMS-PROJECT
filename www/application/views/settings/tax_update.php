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

<div class="content-wrapper" data-page="tax_update">
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
                        <h3 class="box-title">Tax Form</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <form action="<?= base_url('tex-update-submit.html') ?>" method="post" id="crd_form" enctype="multipart/form-data">
                            <table class="table table-bordered table-striped" width="100%">
                                <tr>
                                    <th width="33.33%">Name</th>
                                    <th width="33.33%">GST Rate %</th>
                                    <th width="33.33%">WHT Rate %</th>
                                </tr>

                                <?php
                                $rates = $this->db
                                    ->get('es_tax_rate')
                                    ->result();
                                foreach ($rates as $rate) {
                                    $gst_disable = ($rate->has_gst == 0) ? 'readonly' : '';
                                    $wht_disable = ($rate->has_wht == 0) ? 'readonly' : '';
                                    ?>
                                    <tr>
                                        <td><?= $rate->tax_name ?><input type="hidden" name="text_id[<?= $rate->id ?>]" value="<?= $rate->id ?>"></td>
                                        <td><input value="<?= $rate->gst_rate ?>" class="form-control" name="gst_rate[<?= $rate->id ?>]" <?= $gst_disable ?>></td>
                                        <td><input value="<?= $rate->wht_rate ?>" class="form-control" name="wht_rate[<?= $rate->id ?>]" <?= $wht_disable ?>></td>
                                    </tr>
                                 <?php } ?>
                            </table>


                                    <div class="row">
                                        <div class="col-xs-10">

                                        </div>
                                        <div class="col-xs-2">
                                            <a href="javascript:void(0);"
                                               class="btn btn-primary btn-block margin-bottom js-form_btn">Save</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
	</section>

</div>


<?php $this->load->view('includes/after_login/footer'); ?>

<script type="text/javascript">


    $(function () {
        var faqadd = {
            'form':         '#crd_form',
            'msgbox':       '#crd_form .js-msgbox',
            'btnClick':     '#crd_form .js-form_btn',
            'urlValidator': "<?php echo base_url("tex-update-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
            'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
        };
        doFormValidation(faqadd);
    });

</script>


</body>
</html>
