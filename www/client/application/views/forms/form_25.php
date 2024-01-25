<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<?php
$booking_stalls = $this->db
    ->where('booking_id', $this->booking->id)
    ->get('es_exhibition_stalls')
    ->result();

$contact_person = $this->db
    ->where('customer_id', $this->userdata->id)
    ->get('es_customer_contact_persons')
    ->row();


$hall_data = $this->db
    ->where('id', $booking_stalls[0]->hall_id)
    ->get('es_location_halls')
    ->row();

?>

<style>

    .content-header > h1 {
        margin-top: 20px;
    }

    .date-box {
        width: 300px;
        padding: 10px;
        text-align: center;
        margin: 0 0 15px;
        background: #db4c3b;
        color: #fff;
        border-radius: 8px;
    }

    .check {
        border: 1px solid #d2d6de;
        padding: 7px;
    }
    .check p {
        font-weight: bold;
    }

</style>

<div class="content-wrapper" data-page="form_25">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="pull-left">FORM I-25</h1>

        <div class="pull-right">
            <h4>FORM SUBMISSION DUE DATE</h4>
            <?php
            $form_expire_date = $this->db->where('exhibition_id', $this->event->id)->where('form_id', $this->form_id)->get('es_exhibition_forms')->row()->expiry_date . ' 24:00:00';
            $check_extend_date = $this->db
                ->where('exhibition_id', $this->event->id)
                ->where('form_id', $this->form_id)
                ->where('booking_id', $this->booking->id)
                ->get('es_exhibition_forms_extended')
                ->row();
            $extend_hours_html = '';
            if (isset($check_extend_date) && !empty($check_extend_date)) {
				if ($check_extend_date->update_date && !is_null($check_extend_date->update_date) && strtotime($check_extend_date->update_date) >= strtotime($form_expire_date)) {
					$form_expire_date = $check_extend_date->update_date;
				}
                $new_expire_time = strtotime('+ '.(int)$check_extend_date->extended_hours.' hours', strtotime($form_expire_date));

                $remaining_hours = (($new_expire_time - strtotime(date('Y-m-d H:i:s')) ) / 60 ) / 60;

                $extend_hours_html = '<div id="extend_hours_html"></div>';
                $extend_hours_html .= '<script>
                var countDownDate = new Date("'.date('Y-m-d H:i:s', ($new_expire_time)).'").getTime();
                var x = setInterval(function() {
                
                  // Get todays date and time
                  var now = new Date().getTime();
                
                  // Find the distance between now and the count down date
                  var distance = countDownDate - now;
                
                  // Time calculations for days, hours, minutes and seconds
                  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                  // Display the result in the element with id="demo"
                  document.getElementById("extend_hours_html").innerHTML = "<h4>Extended Time<h4><h3 class=\'date-box\'>" + days + "d " + hours + "h "
                  + minutes + "m " + seconds + "s </h3>";
                
                  // If the count down is finished, write some text 
                  if (distance < 0) {
                    clearInterval(x);
                    document.getElementById("extend_hours_html").innerHTML = "";
                  }
                }, 1000);
                </script>
                ';
            }
            ?>
            <h3 class="date-box"><?= date('d F Y', strtotime($form_expire_date)) ?></h3>
            <?= $extend_hours_html ?>
        </div>
        <div class="clear-fix"></div>
        <h1 class="text-center">Branding Orders</h1>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('forms/form_25/form_25_submit') ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">

                            <input type="hidden" name="date" value="<?= date('Y-m-d') ?>">
                            <input type="hidden" name="time" value="<?= date('H:i:s') ?>">
                            <input type="hidden" name="price_type" value="<?= $this->booking->booking_price_type ?>">

                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <label>Exhibitor Company Name</label>
                                    <input type="text" class="form-control" name="company" value="<?= $this->userdata->company ?>" readonly>
                                </div>
                                <div class="col-sm-2">
                                    <label>Hall #</label>
                                    <input type="text" class="form-control" value="<?= $hall_data->hall_title ?>" disabled>
                                </div>
                                <div class="col-sm-2">
                                    <label>Stand #</label>
                                    <input type="text" class="form-control" value="<?= implode(', ', array_map(function ($stall){ return $stall->stall_name; }, $booking_stalls)) ?>" disabled>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="contact_person">Contact Person:</label>
                                        <input type="text" class="form-control" name="contact_person"
                                               id="contact_person" value="<?= $contact_person->person_name ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="phone">Email #:</label>
                                        <input type="email" class="form-control" name="phone"
                                               id="phone" value="<?= $contact_person->primary_email ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="mobile">Phone Number #:</label>
                                        <input type="number" class="form-control" name="mobile"
                                               id="mobile" value="<?= $contact_person->primary_phone ?>" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <?php
                                $main_category = $this->db
                                    ->where('parent_id', NULL)
                                    ->where('for_branding', 1)
                                    ->get('es_inventory_category')
                                    ->result();
                                ?>
                                <div class="col-sm-3">
                                    <label> Main Category:</label>
                                    <select class="form-control" id="sel1" name="main_category">
                                        <option>-Select-</option>
                                        <?php foreach ($main_category as $main) { ?>
                                            <option value="<?= $main->id?>"><?= $main->category_title ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label>Sub Category:</label>
                                    <select class="form-control" id="sub_sel1" name="sub_category">
                                        <option>-Select-</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label id="cat_items_label">Item Name</label>
                                            <select class="form-control" id="cat_items" name="item">
                                                <option>-Select-</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Quantity </label>
                                            <input name="quantity" class="form-control" id="quantity" type="number">
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Cost <?= $this->booking->booking_price_type ?></label>
                                            <input name="cost" class="form-control" type="number" id="cost" readonly>
                                        </div>
                                        <div class="col-sm-4">
                                            <label>Product Image</label><br>
                                            <div id="item_image_container"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-10"></div>
                                <div class="col-sm-2" style="margin-top: 115px;">
                                    <a href="javascript:void(0);"
                                       class="btn btn-primary btn-block margin-bottom js-form_btn" style="float: right;">Add</a>
                                </div>
                            </div>
                            <p class="bg-danger js-msgbox"></p>

                        </form>

                    </div>


                    <table class="table table-bordered table-striped" style="margin: 20px 0 40px;">
                        <thead>
                        <tr>
                            <th>S.no</th>
                            <th>Company Name</th>
                            <th>Branding Category</th>
                            <th>Branding Sub Category</th>
                            <th>Quantity</th>
                            <th>Ad Size(MB's)</th>
                            <th>Upload Status</th>
                            <th>Update Date</th>
                            <th>Time</th>
                            <th>Cost <?= $this->booking->booking_price_type ?></th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        //print_r($this->formdata);die();
                        if (!is_null($this->formdata)) {
                            foreach ($this->formdata as $count => $row) {
                                $main=$this->db
                                    ->where('id', $row->main_category)
                                    ->get('es_inventory_category')
                                    ->row();
                                $sub=$this->db
                                    ->where('id', $row->sub_category)
                                    ->get('es_inventory_category')
                                    ->row();
                                echo '<tr>
                                        <td>'. ($count + 1) .'</td>
                                        <td>'. $row->company.'</td>
                                        <td>'. $main->category_title.'</td>
                                        <td>'. $sub->category_title.'</td>
                                        <td>'. $row->quantity.'</td>
                                        <td></td>
                                        <td></td>
                                        <td>'. $row->date.'</td>
                                        <td>'. $row->time.'</td>
                                        <td>'. $row->cost.'</td>
                                        <td>
                                        <a href="'. base_url('forms/form_25/delete_branding?id='. $row->id ) .'">Remove</a>
                                        </td>
                                        </tr>';
                            }
                        }
                        ?>
                        </tbody>
                    </table>

                </div><!-- /.box-body -->
            </div><!-- /.box -->

            <!-- /.box -->
        </div><!-- /.col -->
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
            'urlValidator': "<?php echo base_url("forms/form_25/form_25_validate/doError"); ?>",
            'loadingImg':   "<?php echo base_url("../assets/img/load-indicator.gif"); ?>"
        });
    });

	var file = new file_upload_preview({
		selector: '#item_image_container',
		ajax_src: '<?= base_url('welcome/file_upload') ?>',
		extensions: [
            'jpg',
            'jpeg',
            'png',
            'PNG',
            {
				type: 'psd',
				icon: '<i class="fa fa-file-image-o"></i>'
            },
            {
				type: 'eps',
				icon: '<i class="fa fa-file-image-o"></i>'
            },
            {
				type: 'ai',
				icon: '<i class="fa fa-file-image-o"></i>'
            },
            {
				type: 'tiff',
				icon: '<i class="fa fa-file-image-o"></i>'
            },
            {
				type: 'pdf',
				icon: '<i class="fa fa-file-image-o"></i>'
            }
        ],
		base_url: '../uploads/client_form_25/',
		post_file_name: 'item_image',
		has_rotation: false,
		max_upload: 1,
		//dimension: '300x300',
		on_upload: function (file_name) {
			console.log('sdsdfsfssf', file_name);
			$('#item_image_container .imgbox img').attr('src', '<?= base_url('../uploads/client_form_25/') ?>' + file_name)
		},
		on_init: function () {
			$('#item_image_container .imgbox').each(function () {
				var s = $(this).find('img').attr('src');
				$(this).find('img').attr('src', '<?= base_url() ?>' + s);
			})
		}
	});

    $(document).on('change', '#sel1', function (e) {
        e.stopImmediatePropagation();
		$('#form_class').html("");

        var selectedName = $('#sel1').val();
        $.ajax({
            type:    'post',
            url:     '<?= base_url("forms/form_25/get_main_company_detail"); ?>',
            data:    {"category":selectedName},
            success: function (data) {
                /// data = JSON.parse(data);
                console.log(data);
                $('#sub_sel1').html(data);
                $('#form_class').html('');

				update_cost();
            },
            error:   function () {
            }
        });


    });


    $(document).on('change', '#sub_sel1', function (e) {
        e.stopImmediatePropagation();
		$('#cat_items').html("");

        var selectedName = $('#sub_sel1').val();
        var selectedtext = $('#sub_sel1 option:selected').html();

        $.ajax({
            type:    'post',
            url:     '<?= base_url("forms/form_25/get_main_company_form"); ?>',
            data:    {"form_data":selectedName},
            success: function (data) {
                console.log(data);
                $('#cat_items').html(data);
                //$('#cat_items_label').html(selectedtext)
                update_cost();
            },
            error:   function () {
            }
        });


    });
	$(document).on('change', '#cat_items', function (e) {
		e.stopImmediatePropagation();

		update_cost();
	});

    $(document).on('keyup', '#quantity', function (e) {
        e.stopImmediatePropagation();

        update_cost();
    });

    function update_cost() {
		var q = $('#quantity').val();
		var p = $('#cat_items option:selected').attr('data-price');

		if ($('#quantity').val() == '') {
			$('#quantity').val(1);
			q = 1;
        }

		$('#cost').val(q*p);
	}

</script>
</body>
</html>
