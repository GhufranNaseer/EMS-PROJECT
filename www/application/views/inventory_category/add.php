<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="inventory_category">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Inventory
            <small>Category</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Add</li>
        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Category Add</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('inventory-category-submit.html') ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">


                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Category Title</label>
                                        <input type="text" class="form-control" name="category_title"
                                               placeholder="Category Title">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Parent Category</label>
                                        <select name="parent_category" class="form-control">
											<?php
											$categories = $this->db
												->where('is_deleted', 0)
												->get('es_inventory_category')
												->result();

											$html = '<option value="">- None -</option>';
											foreach ($categories as $category) {
												$html .= '<option value="'.$category->id.'">'.$category->category_title.'</option>';
											}
											echo $html;
											?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <p class="bg-danger js-msgbox"></p>

                            <div class="row">
                                <div class="col-xs-10">

                                </div>
                                <div class="col-xs-2">
                                    <a href="javascript:void(0);"
                                       class="btn btn-primary btn-block margin-bottom js-form_btn">Save</a>
                                </div>
                            </div>

                        </form>

                    </div><!-- /.box-body -->
                </div><!-- /.box -->

            </div>
        </div><!-- /.row -->
    </section><!-- /.content -->
    <!-- /.content -->
</div><!-- /.content-wrapper -->

<?php $this->load->view('includes/after_login/footer'); ?>

<script>
	$(function () {

		doFormValidation({
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("inventory-category-validate.html"); ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		});
	});
</script>
</body>
</html>
