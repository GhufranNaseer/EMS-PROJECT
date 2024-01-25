<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="inventory_item">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Inventory
            <small>Item</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
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
                        <h3 class="box-title">Item Edit</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('inventory-item-edit-submit.html') ?>?id=<?= $this->input->get('id') ?>"
                              method="post" id="crd_form" enctype="multipart/form-data">

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="user_first_name">Item Name</label>
                                        <input type="text" class="form-control" name="item_title"
                                               value="<?= html_escape($this->formdata->item_title) ?>"
                                               placeholder="Item Name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label>Item Category</label>
                                    <select name="category_id" class="form-control">
                                        <option value="">- select -</option>
										<?php
										$categories = $this->db
											->where('is_deleted', 0)
											->get('es_inventory_category')
											->result();

										foreach ($categories as $category) {
										    $selected = ($category->id == $this->formdata->category_id) ? 'selected' : '';
											echo '<option value="'.$category->id.'" '.$selected.'>'.$category->category_title.'</option>';
										}

										?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Item Stock</label>
                                        <input type="number" class="form-control" name="item_stock"
                                               value="<?= html_escape($this->formdata->item_stock) ?>"
                                               placeholder="Item Stock">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Per Item Stock Price - USD</label>
                                        <input type="number" class="form-control" name="item_price_usd"
                                               value="<?= html_escape($this->formdata->item_price_usd) ?>"
                                               placeholder="Item Price">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Per Item Stock Price - PKR</label>
                                        <input type="number" class="form-control" name="item_price_pkr"
                                               value="<?= html_escape($this->formdata->item_price_pkr) ?>"
                                               placeholder="Item Price">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <label>Item Image</label>
                                    <div id="image_container"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Item Description</label>
                                    <textarea name="item_description" class="form-control" rows="3"><?= html_escape($this->formdata->item_description) ?></textarea>
                                </div>
                                <div class="col-sm-6">
                                    <label>Terms & Condition</label>
                                    <textarea name="item_terms" class="form-control" rows="3"><?= html_escape($this->formdata->item_terms) ?></textarea>
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

                <!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.content -->
    <!-- /.content -->
</div>

<?php $this->load->view('includes/after_login/footer'); ?>

<script>
	$(function () {

		var file = new file_upload_preview({
			selector: '#image_container',
			ajax_src: '<?= base_url('welcome/file_upload') ?>',
			extensions: 'jpg|jpeg|png|PNG',
			base_url: 'uploads/inventory_item/',
			post_file_name: 'item_image',
			has_rotation: false,
			max_upload: 1,
			predefined_images: ['<?= html_escape($this->formdata->item_image) ?>']
		});

		doFormValidation({
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("inventory-item-edit-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		});
	});
</script>
</body>
</html>
