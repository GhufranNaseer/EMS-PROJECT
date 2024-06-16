<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<?php
$booking_stalls = $this->db
	->where('booking_id', $this->booking->id)
	->get('es_exhibition_stalls')
	->result();

$hall_data = $this->db
	->where('id', $booking_stalls[0]->hall_id)
	->get('es_location_halls')
	->row();

?>

<style>
    .nav-pills>li.active>a, .nav-pills>li.active>a:hover, .nav-pills>li.active>a:focus {
        border-top-color: transparent;
        background: #db4c3b;
    }
    .nav-pills>li>a {
        padding: 20px;
        position: relative;
        margin: 0;
        border: 0;
    }
    .nav-pills>li:not(:last-child).active>a:before {
        content: '';
        display: block;
        height: 34.5px;
        width: 34.5px;
        border: 18px solid #fff;
        position: absolute;
        top: 0;
        right: 0;
        border-bottom-color: transparent;
        border-left-color: transparent;
    }
    .nav-pills>li:not(:last-child).active>a:after {
        content: '';
        display: block;
        height: 34.5px;
        width: 34.5px;
        border: 18px solid #fff;
        position: absolute;
        bottom: 0;
        right: 0;
        border-top-color: transparent;
        border-left-color: transparent;
    }


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


    .select_list {
        margin: 0 0 20px;
        padding: 0;
        list-style: none;
        border: 1px solid #ccc;
        background: #f3f3f3;
        height: 150px;
        overflow: auto;
    }
    .select_list li {
        padding: 6px 12px;
        cursor: pointer;
        border-bottom: 1px solid #ddd;
    }
    .select_list li:hover {
        background: #ddd;
    }
    .select_list li.selected {
        background: #db4c3b;
        color: #fff;
    }
    .select_list_add_btn {
        padding: 10px 12px 8px;
        border: 1px solid #ccc;
        margin: 70px auto 0;
        display: block;
        background: #eee;
        width: 50px;
    }
    .select_list_add_btn i {
        display: block;
    }
    .business_selected_area,
    .other_business_selected_area,
    .product_selected_area {
        height: 150px;
        overflow: auto;
        background: #ecc488;
    }
    .business_selected_area .btn,
    .other_business_selected_area .btn,
    .product_selected_area .btn {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: block;
        margin: 0 auto;
        font-weight: bold;
    }
</style>

<div class="content-wrapper" data-page="form_03">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="pull-left">FORM 3: Show Catalogue Entry:</h1>
        <div class="pull-right">
            <h4>FORM SUBMISSION DUE DATE</h4>
			<?php
			$form_expire_date = $this->db->where('exhibition_id', $this->event->id)->where('form_id', $this->form_id)->get('es_exhibition_forms')->row()->expiry_date . ' 00:00:00';
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
        <h1 class="text-center">Exhibitor Company Profile and Information</h1>
    </section>


    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">

                    <div class="box-body">
                        <ul class="nav nav-pills nav-justified thumbnail setup-panel">
                            <li class="active" data-step="1">
                                <a href="#step-1">
                                    <h4 class="list-group-item-heading">Exhibit Information</h4>
                                </a>
                            </li>
                            <li class="" data-step="2">
                                <a href="#step-2">
                                    <h4 class="list-group-item-heading">Enter Your Profile</h4>
                                </a>
                            </li>
                            <li class="" data-step="3">
                                <a href="#step-3">
                                    <h4 class="list-group-item-heading">Product and Category</h4>
                                </a>
                            </li>
                            <li class="" data-step="4">
                                <a href="#step-4">
                                    <h4 class="list-group-item-heading">Principle Company</h4>
                                </a>
                            </li>
                        </ul>

                        <div class="setup-content" id="step-1">
                            <form action="#" method="post" id="exhibit_information" enctype="multipart/form-data">

                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Exhibitor Company Name</label>
                                        <input type="text" class="form-control" value="<?= $this->userdata->company ?>" disabled>
                                    </div>
                                    <div class="col-sm-2">
                                        <label>Hall #</label>
                                        <input type="text" class="form-control" value="<?= $hall_data->hall_title ?>" disabled>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Stand #</label>
                                        <input type="text" class="form-control" value="<?= implode(', ', array_map(function ($stall){ return $stall->stall_name; }, $booking_stalls)) ?>" disabled>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label>Select your Country <span class="text-red">*</span></label>
                                        <select class="form-control" name="exhibit[country]">
                                            <option value="">- select -</option>
											<?= get_instance()->funcs->print_input_data_list('country', ((!is_null($this->formdata)) ? $this->formdata->exhibit->country : '')); ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Enter Postal / ZIP Code</label>
                                        <input type="number" class="form-control" name="exhibit[postal]"
                                               value="<?= (!is_null($this->formdata)) ? $this->formdata->exhibit->postal : '' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <label>Telephone Number <span class="text-red">*</span></label>
                                        <input type="text" class="form-control" name="exhibit[telephone]"
                                               value="<?= (!is_null($this->formdata)) ? $this->formdata->exhibit->telephone : '' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <label>Fax Number</label>
                                        <input type="text" class="form-control" name="exhibit[fax]"
                                               value="<?= (!is_null($this->formdata)) ? $this->formdata->exhibit->fax : '' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <label>Email Address <span class="text-red">*</span></label>
                                        <input type="text" class="form-control" name="exhibit[email]"
                                               value="<?= (!is_null($this->formdata)) ? $this->formdata->exhibit->email : '' ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Your Business Address for Print in Show Catalogue <span class="text-red">*</span></label>
                                            <textarea name="exhibit[address]" class="form-control" rows="6"><?= (!is_null($this->formdata)) ? $this->formdata->exhibit->address : '' ?></textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label>Upload your Company Official Logo</label>
                                                <div id="company_logo_container"></div>
                                                <p class="small text-red">* Logo must be on 300 Dpi <br>Format: JPEG, PNG, Gif</p>
                                            </div>
                                            <div class="col-sm-6">
                                                <label>Upload your Company Ad</label>
                                                <div id="company_ad_container"></div>
                                                <p class="small text-red">Ad Artwork must be on 12(W) X 16(H) Cm <br>Format: JPEG, PNG, Gif</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <h3>Your Contact Person Information <small>(to be printed show catalog)</small></h3>

                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Person Full Name <span class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="exhibit[contact_person][name]"
                                                       value="<?= (!is_null($this->formdata)) ? $this->formdata->exhibit->contact_person->name : '' ?>">
                                            </div>
                                            <div class="col-sm-6">
                                                <label>Designation</label>
                                                <input type="text" class="form-control" name="exhibit[contact_person][designation]"
                                                       value="<?= (!is_null($this->formdata)) ? $this->formdata->exhibit->contact_person->designation : '' ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Mobile #</label>
                                                <input type="text" class="form-control" name="exhibit[contact_person][mobile]"
                                                       value="<?= (!is_null($this->formdata)) ? $this->formdata->exhibit->contact_person->mobile : '' ?>">
                                            </div>
                                            <div class="col-sm-6">
                                                <label>Email Address <span class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="exhibit[contact_person][email]"
                                                       value="<?= (!is_null($this->formdata)) ? $this->formdata->exhibit->contact_person->email : '' ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Telephone Number</label>
                                                <input type="text" class="form-control" name="exhibit[contact_person][phone]"
                                                       value="<?= (!is_null($this->formdata)) ? $this->formdata->exhibit->contact_person->phone : '' ?>">
                                            </div>
                                            <div class="col-sm-6">
                                                <label>Fax Number</label>
                                                <input type="text" class="form-control" name="exhibit[contact_person][fax]"
                                                       value="<?= (!is_null($this->formdata)) ? $this->formdata->exhibit->contact_person->fax : '' ?>">
                                            </div>
                                        </div>
                                        <div class="well well-sm">
                                            Disclaimer: Kindly note that the information provided by the exhibitor will be used or printed AS IS and organizer / Event Manager shall not be responsible for any of the spelling or grammar mistake.
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-2 col-sm-offset-10">
                                        <button type="button" class="btn btn-primary btn-block btn-lg save_step_1">Next</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="setup-content" id="step-2">
                            <form action="#" method="post" id="profile_form" enctype="multipart/form-data">

                                <div class="form-group">
                                    <h4>Write your complete Company Profile to show in catalogue (<span class="text-danger">Max 300 words</span>) <span class="text-red">*</span></h4>
                                    <textarea name="profile" class="form-control input-lg complete_profile" rows="6"><?= (!is_null($this->formdata)) ? $this->formdata->profile : '' ?></textarea>
                                    <div class="text-right"><span class="complete_profile_count">0</span>/300</div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-2 col-sm-offset-10">
                                        <button type="button" class="btn btn-primary btn-block btn-lg save_step_2">Next</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="setup-content" id="step-3">
                            <form action="#" method="post" id="products_form" enctype="multipart/form-data">

                                <!-- main sectors -->
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label>Select Main Business sector</label>
                                        <ul class="select_list" id="main_business_sector"></ul>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Select Your Business Type</label>
                                        <ul class="select_list" id="main_business_type"></ul>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Select Your Business Area</label>
                                        <ul class="select_list" id="main_business_area"></ul>
                                    </div>
                                    <div class="col-sm-1">
                                        <button type="button" class="select_list_add_btn"><i class="fa fa-chevron-right"></i>Add</button>
                                    </div>
                                    <div class="col-sm-2">
                                        <label>Your Business Add List</label>
                                        <div class="business_selected_area">
                                            <table class="table table-bordered" id="selected_business_area">
                                                <?php
                                               if ((!is_null($this->formdata)) && isset($this->formdata->products) && count((array)$this->formdata->products->main) > 0) {
                                                    foreach ($this->formdata->products->main as $key => $main_business) {
														echo '<tr>
                                                        <td>'. $main_business->area .'</td>
                                                        <td>
                                                        <input type="hidden" name="products[main]['.$key.'][sector]" value="'. $main_business->sector .'">
                                                            <input type="hidden" name="products[main]['.$key.'][type]" value="'. $main_business->type .'">
                                                            <input type="hidden" name="products[main]['.$key.'][area]" value="'. $main_business->area .'">
                                                            <button type="button" class="btn btn-default btn-xs remove_selected_main_area">x</button>
                                                        </td>
                                                        </tr>';
                                                    }
                                                }
                                                ?>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <fieldset>
                                    <legend>Other Main Business Sector</legend>

                                    <div class="row">
                                        <div class="col-sm-3">
                                            <label>Enter Your Main Business Sector</label>
                                            <input type="text" class="form-control" id="other_business_sector">
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Enter Your Business Type</label>
                                            <input type="text" class="form-control" id="other_business_type">
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Enter Your Business Area</label>
                                            <input type="text" class="form-control" id="other_business_area">
                                        </div>
                                        <div class="col-sm-1">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-primary btn-block other_business_add_btn">Add</button>
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Your Other Business Add List</label>
                                            <div class="other_business_selected_area">
                                                <table class="table table-bordered" id="other_business_selected_area">
													<?php
													if ((!is_null($this->formdata)) && isset($this->formdata->products) && property_exists($this->formdata->products, 'other') && count((array)$this->formdata->products->other) > 0) {
														foreach ($this->formdata->products->other as $key => $other_business) {
															echo '<tr>
                                                            <td>'.$other_business->area.'</td>
                                                            <td>
                                                            <input type="hidden" name="products[other]['.$key.'][sector]" value="'. $other_business->sector .'">
                                                            <input type="hidden" name="products[other]['.$key.'][type]" value="'. $other_business->type .'">
                                                            <input type="hidden" name="products[other]['.$key.'][area]" value="'. $other_business->area .'">
                                                            <button type="button" class="btn btn-default btn-xs remove_selected_other_area">x</button>
                                                            </td>
                                                            </tr>';
														}
													}
													?>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <legend>Add Your Product List</legend>

                                    <div class="row">
                                        <div class="col-sm-3">
                                            <label>Product Category</label>
                                            <input type="text" class="form-control" id="product_category">
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Product Type</label>
                                            <input type="text" class="form-control" id="product_type">
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Product Name</label>
                                            <input type="text" class="form-control" id="product_name">
                                        </div>
                                        <div class="col-sm-1">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-primary btn-block product_add_btn">Add</button>
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Your Products Add List</label>
                                            <div class="product_selected_area">
                                                <table class="table table-bordered" id="product_selected_area">
													<?php
													if ((!is_null($this->formdata)) && isset($this->formdata->products) && count((array)$this->formdata->products->product) > 0) {
														foreach ($this->formdata->products->product as $key => $product) {
															echo '<tr>
                                                            <td>'. $product->name .'</td>
                                                            <td>
                                                            <input type="hidden" name="products[product]['.$key.'][category]" value="'.$product->category.'">
                                                            <input type="hidden" name="products[product]['.$key.'][type]" value="'.$product->type.'">
                                                            <input type="hidden" name="products[product]['.$key.'][name]" value="'.$product->name.'">
                                                            <button type="button" class="btn btn-default btn-xs remove_selected_product_btn">x</button>
                                                            </td>
                                                            </tr>';
														}
													}
													?>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <div class="row">
                                    <div class="col-sm-2 col-sm-offset-10">
                                        <button type="button" class="btn btn-primary btn-block btn-lg save_step_3">Next</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="setup-content" id="step-4">
                            <form action="#" method="post" id="principles_form" enctype="multipart/form-data">

                                <div class="checkbox">
                                    <label class="h4">
                                        <input type="checkbox" class="principles_is_active" name="principles[is_active]"
                                        <?= (!is_null($this->formdata)) ? (($this->formdata->principle->is_active == 1) ? 'checked' : '') : 'checked' ?>>
                                        Enter your company principle information and contact details for show catalogue profile
                                    </label>
                                </div>

                                <div id="principles_container" style="display: <?= (!is_null($this->formdata)) ? (($this->formdata->principle->is_active == 1) ? 'block' : 'none') : 'block' ?>">
                                    <div class="form-group row">
                                        <div class="col-sm-2">
                                            <label>Principle Company <span class="text-red">*</span></label>
                                            <input type="text" class="form-control" name="principles[full_name]">
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Select Country <span class="text-red">*</span></label>
                                            <select class="form-control" name="principles[country]">
                                                <option value="">- select -</option>
												<?= get_instance()->funcs->print_input_data_list('country'); ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Status</label>
                                            <select name="principles[status]" class="form-control">
                                                <option value="principal">Overseas Principal</option>
                                                <option value="distributor">Distributor</option>
                                                <option value="local_distributor">Local Distributor</option>
                                                <option value="local_representative">Local Representative</option>
                                                <option value="local_all">Principal/Distributor/Representative (Local)</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Telephone Number</label>
                                            <input type="text" class="form-control" name="principles[phone]">
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Fax Number</label>
                                            <input type="number" class="form-control" name="principles[fax]">
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Email Address</label>
                                            <input type="text" class="form-control" name="principles[email]">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label>Person Full Name</label>
                                            <input type="text" class="form-control" name="principles[person_name]">
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Designation</label>
                                            <input type="text" class="form-control" name="principles[designation]">
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Mobile Number</label>
                                            <input type="text" class="form-control" name="principles[person_mobile]">
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Person Email Address</label>
                                            <input type="text" class="form-control" name="principles[person_email]">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-9">
                                            <label>Complete Business Address</label>
                                            <textarea name="principles[address]" class="form-control" rows="3"></textarea>
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Principle official Logo</label>
                                            <div id="principles_company_logo_container"></div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-primary btn-lg principles_add_btn">ADD</button>
                                    </div>

                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>Company Name</th>
                                            <th>Country</th>
                                            <th>Tel #</th>
                                            <th>Fax #</th>
                                            <th>Org Email</th>
                                            <th>Address</th>
                                            <th>Person Full Name</th>
                                            <th>Designation</th>
                                            <th>Mobile #</th>
                                            <th>P. Email</th>
                                            <th>P. Logo</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody id="company_principle_list"></tbody>
                                    </table>
                                </div>

                                <div class="row">
                                    <div class="col-sm-2 col-sm-offset-10">
                                        <button type="button" class="btn btn-primary btn-block btn-lg save_step_4">Finish & Submit</button>
                                    </div>
                                </div>

                            </form>
                        </div>

                    </div><!-- /.box-body -->
                </div><!-- /.box -->

                <!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.content -->
    <!-- /.content -->
</div><!-- /.content-wrapper -->

<datalist id="country_list">
	<?= get_instance()->funcs->print_input_data_list('country'); ?>
</datalist>

<?php $this->load->view('includes/after_login/footer'); ?>

<script>
	/* START Steps */
	var navListItems = $('ul.setup-panel li a'),
		allWells = $('.setup-content');

	allWells.hide();

	navListItems.click(function(e) {
		e.preventDefault();
		var $target = $($(this).attr('href')),
			$item = $(this).closest('li');

		if (!$item.hasClass('disabled')) {
			navListItems.closest('li').removeClass('active');
			$item.addClass('active');
			allWells.hide();
			$target.show();
		}
	});

	$('ul.setup-panel li.active a').trigger('click');
	/* END Steps */

	$('[name="exhibit[telephone]"]').inputmask("+99-999-99999999");
    $('[name="exhibit[fax]"]').inputmask("+99-999-99999999");
	$('[name="exhibit[contact_person][mobile]"]').inputmask("+99-999-99999999");
    $('[name="exhibit[contact_person][fax]"]').inputmask("+99-999-99999999");
	$('[name="exhibit[contact_person][phone]"]').inputmask("+99-999-99999999");
	$('[name="principles[phone]"]').inputmask("+99-999-99999999");
	$('[name="principles[person_mobile]"]').inputmask("+99-999-99999999");

	var file = new file_upload_preview({
		selector: '#company_logo_container',
		ajax_src: '<?= base_url('welcome/file_upload') ?>',
		extensions: 'jpg|jpeg|png|PNG',
		base_url: '../uploads/client_form_3/',
		post_file_name: 'exhibit[company_logo]',
		has_rotation: false,
		max_upload: 1,
		dimension: '300x300',
		on_upload: function (file_name) {
			console.log('sdsdfsfssf', file_name);
			$('#company_logo_container .imgbox img').attr('src', '<?= base_url('../uploads/client_form_3/') ?>' + file_name)
		},
		on_init: function () {
			$('#company_logo_container .imgbox').each(function () {
                var s = $(this).find('img').attr('src');
				$(this).find('img').attr('src', '<?= base_url() ?>' + s);
			})
		},
        <?php
		if (!is_null($this->formdata) && isset($this->formdata->exhibit->company_logo)) {
		    echo 'predefined_images: ' . json_encode($this->formdata->exhibit->company_logo);
        }
        ?>
	});

	var file2 = new file_upload_preview({
		selector: '#company_ad_container',
		ajax_src: '<?= base_url('welcome/file_upload') ?>',
		extensions: 'jpg|jpeg|png|PNG',
		base_url: '../uploads/client_form_3/',
		post_file_name: 'exhibit[company_ad]',
		has_rotation: false,
		max_upload: 1,
		dimension: '300x300',
		on_upload: function (file_name) {
			console.log('sdsdfsfssf', file_name);
			$('#company_ad_container .imgbox img').attr('src', '<?= base_url('../uploads/client_form_3/') ?>' + file_name)
		},
		on_init: function () {
			$('#company_ad_container .imgbox').each(function () {
				var s = $(this).find('img').attr('src');
				$(this).find('img').attr('src', '<?= base_url() ?>' + s);
			})
		},
		<?php
		if (!is_null($this->formdata) && isset($this->formdata->exhibit->company_ad)) {
			echo 'predefined_images: ' . json_encode($this->formdata->exhibit->company_ad);
		}
		?>
	});

	var file3 = new file_upload_preview({
		selector: '#principles_company_logo_container',
		ajax_src: '<?= base_url('welcome/file_upload') ?>',
		extensions: 'jpg|jpeg|png|PNG',
		base_url: '../uploads/client_form_3/',
		post_file_name: 'principles[company_logo]',
		has_rotation: false,
		max_upload: 1,
		dimension: '300x300',
		on_upload: function (file_name) {
			$('#principles_company_logo_container .imgbox img').attr('src', '<?= base_url('../uploads/client_form_3/') ?>' + file_name)
		}
	});

	function show_validation_error(data) {
		if (data.indexOf("*") != -1) {

			$(".validation-failed").removeClass("validation-failed");

			var serverresponse = data.split("*");

			var resp_field_name = serverresponse[0];
			var msg_start = '';
			if (serverresponse[0].indexOf(' ') != -1) {
				var str = serverresponse[0];

				var rest = str.substring(0, str.lastIndexOf(' ') + 1);
				var last = str.substring(str.lastIndexOf(' ') + 1, str.length);

				msg_start = rest + ' ';
				resp_field_name = last;
			}
			$("[name='" + resp_field_name + "']").addClass("validation-failed");
			fieldname__ = serverresponse[0];
			if (typeof swal === 'function') {
				swal({
					title: '',
					text: msg_start + serverresponse[1],
					type: "warning"
				}, function () {
					setTimeout(function () {
						$(window).scrollTop($("[name='" + resp_field_name + "']").offset().top - 100);
						$("[name='" + resp_field_name + "']").focus();
					}, 300)
				});
			} else {
				//$(obj.msgbox).html(msg_start + serverresponse[1]);
			}
		}
		else {
			if (typeof swal === 'function') {
				//$(obj.msgbox).html('');
				swal({
					title: '',
					text: data,
					type: "warning"
				});
			} else {
				//$(obj.msgbox).html(data);
			}
		}
	}


	function validate_exhibit(callback) {
		$.ajax({
			type:    'post',
			url:     '<?= base_url("forms/form_03/form_03_exhibit_validate/doError"); ?>',
			data:    $('#exhibit_information').serialize(),
			success: function (data) {
				console.log(data);
				$(".validation-failed").removeClass("validation-failed");

				if (data != "done") {
					allWells.hide();
					$('ul.setup-panel li[data-step="1"] a').trigger('click');
					show_validation_error(data);
				} else {
					if (callback) callback();
				}
			},
			error:   function () {
			}
		});
	}

	function validate_profile(callback) {
		$.ajax({
			type:    'post',
			url:     '<?= base_url("forms/form_03/form_03_profile_validate/doError"); ?>',
			data:    $('#profile_form').serialize(),
			success: function (data) {
				console.log(data);
				$(".validation-failed").removeClass("validation-failed");

				if (data != "done") {
					allWells.hide();
					$('ul.setup-panel li[data-step="2"] a').trigger('click');
					show_validation_error(data);
				} else {
					if (callback) callback();
				}
			},
			error:   function () {
			}
		});
	}

	function validate_product(callback) {
		$.ajax({
			type:    'post',
			url:     '<?= base_url("forms/form_03/form_03_products_validate/doError"); ?>',
			data:    $('#products_form').serialize(),
			success: function (data) {
				console.log(data);
				$(".validation-failed").removeClass("validation-failed");

				if (data != "done") {
					allWells.hide();
					$('ul.setup-panel li[data-step="3"] a').trigger('click');
					show_validation_error(data);
				} else {
					if (callback) callback();
				}
			},
			error:   function () {
			}
		});
	}

	function validate_principles(callback) {
		var data = [];
		$('#company_principle_list tr').each(function() {
			var v = $(this).find('.principles_list').val()
			console.log(v)
			v = v.replace(/'/g, '"');
			v = JSON.parse(v);
			data.push(v);
		});

		$.ajax({
			type:    'post',
			url:     '<?= base_url("forms/form_03/form_03_principles_validate/doError"); ?>',
			data:    {
				is_active: ($('.principles_is_active').is(':checked')) ? 1 : 0,
				principles_list: data
            },
			success: function (data) {
				console.log(data);
				$(".validation-failed").removeClass("validation-failed");

				if (data != "done") {
					allWells.hide();
					$('ul.setup-panel li[data-step="4"] a').trigger('click');
					show_validation_error(data);
				} else {
					if (callback) callback();
				}
			},
			error:   function () {
			}
		});
	}

	$(document).on('click', '.save_step_1', function (e) {
		e.stopImmediatePropagation();

		validate_exhibit(function () {
			allWells.hide();
			$('ul.setup-panel li[data-step="2"] a').trigger('click');
		})
	});

	$(document).on('click', '.save_step_2', function (e) {
		e.stopImmediatePropagation();

		validate_profile(function () {
			allWells.hide();
			$('ul.setup-panel li[data-step="3"] a').trigger('click');
		})
	});

	$(document).on('click', '.save_step_3', function (e) {
		e.stopImmediatePropagation();

		validate_product(function () {
			allWells.hide();
			$('ul.setup-panel li[data-step="4"] a').trigger('click');
		})
	});

	$(document).on('click', '.save_step_4', function (e) {
		e.stopImmediatePropagation();

		validate_principles(function () {
			validate_exhibit(function () {
				validate_profile(function () {
					validate_product(function () {
						submit_form();
					})
				})
			})
		})
	});

	$(document).on('keypress keyup', '.complete_profile', function (e) {
		e.stopImmediatePropagation();

		//$('.complete_profile_count').html($(this).val().length);
		var s = $(this).val();
		s = s.replace(/(^\s*)|(\s*$)/gi,"");//exclude  start and end white-space
		s = s.replace(/[ ]{2,}/gi," ");//2 or more space to 1
		s = s.replace(/\n /,"\n"); // exclude newline with a start spacing
        //v = v.split(' ');
		s = s.split(' ').filter(function(str){return str!="";})

        $('.complete_profile_count').html(s.length);

        if (s.length >= 300) {
			e.preventDefault();
			return false;
		}
	});
	$(document).on('blur', '.complete_profile', function (e) {
		e.stopImmediatePropagation();

		//$('.complete_profile_count').html($(this).val().length);
		var s = $(this).val();
		s = s.replace(/(^\s*)|(\s*$)/gi,"");//exclude  start and end white-space
		s = s.replace(/[ ]{2,}/gi," ");//2 or more space to 1
		s = s.replace(/\n /,"\n"); // exclude newline with a start spacing
        //v = v.split(' ');
		s = s.split(' ').filter(function(str){return str!="";})

        // alert(s.length)
		// console.log(s)

        if (s.length > 300) {
			alert('You have added more then 300 words!');
			$(this).val(s.slice(0, 300).join(' '))
			$('.complete_profile').keyup();
		}
	});
	$('.complete_profile').keyup();

	$(document).on('change', '.principles_is_active', function (e) {
		e.stopImmediatePropagation();

		if ($(this).is(':checked')) {
			$('#principles_container').slideDown(300);
        } else {
			$('#principles_container').slideUp(300);
        }
	});

	$(document).on('click', '.principles_add_btn', function (e) {
		e.stopImmediatePropagation();

		$.ajax({
			type:    'post',
			url:     '<?= base_url("forms/form_03/form_03_principle_add_validate/doError"); ?>',
			data:    $('#principles_form').serialize(),
			success: function (data) {
				console.log(data);
				$(".validation-failed").removeClass("validation-failed");

				if (data != "done") {
					allWells.hide();
					$('ul.setup-panel li[data-step="4"] a').trigger('click');
					show_validation_error(data);
				} else {
					// add in table

					var str = $('#principles_form').serialize()
					var obj = str.split("&").reduce(function(prev, curr, i, arr) {
						var p = curr.split("=");
						prev[decodeURIComponent(p[0])] = decodeURIComponent(p[1]);
						return prev;
					}, {});

					var final_obj = {
						full_name : obj['principles[full_name]'],
						country : obj['principles[country]'],
						phone : obj['principles[phone]'],
						fax : obj['principles[fax]'],
						email : obj['principles[email]'],
						person_name : obj['principles[person_name]'],
						designation : obj['principles[designation]'],
						person_mobile : obj['principles[person_mobile]'],
						person_email : obj['principles[person_email]'],
						address : obj['principles[address]'],
						company_logo : obj['principles[company_logo][0]'],
                    }
					console.log('fff', final_obj);

					add_in_principle_list(final_obj);
					$("#principles_form")[0].reset();
				}
			},
			error:   function () {
			}
		});
	});

	$(document).on('click', '.principles_remove_btn', function (e) {
		e.stopImmediatePropagation();

		$(this).parents('tr').remove();
	});

	function add_in_principle_list(data) {

		var html = '<tr>';

		html += '<td>'+data.full_name+'</td>';
		html += '<td>'+data.country+'</td>';
		html += '<td>'+data.phone+'</td>';
		html += '<td>'+data.fax+'</td>';
		html += '<td>'+data.email+'</td>';
		html += '<td>'+data.address+'</td>';
		html += '<td>'+data.person_name+'</td>';
		html += '<td>'+data.designation+'</td>';
		html += '<td>'+data.person_mobile+'</td>';
		html += '<td>'+data.person_email+'</td>';
		html += '<td><img src="<?= base_url('/') ?>'+data.company_logo+'" style="width: 40px"></td>';

		var value = JSON.stringify(data).replace(/"/g, "'");
		html += '<td>\
            <input type="hidden" name="principles[list][]" class="principles_list" value="'+value+'">\
            <button type="button" class="btn btn-link principles_remove_btn">Remove</button>\
            </td>';

		html += '</tr>';
		$('#company_principle_list').append(html)
	}

	<?php
    if (!is_null($this->formdata) && isset($this->formdata->principle) && $this->formdata->principle->is_active == 1) {
        foreach ($this->formdata->principle->principles_list as $list) {
            echo 'add_in_principle_list('. json_encode($list) .'); ';
        }
    }
    ?>

	function get_main_business_sector(callback) {
		$('#main_business_sector').html('');
		$('#main_business_type').html('');
		$('#main_business_area').html('');
		$.ajax({
			type:    'post',
			url:     '<?= base_url("forms/form_03/get_main_business_sector"); ?>',
			data:    {},
			success: function (data) {
				data = JSON.parse(data);
				console.log(data);
				if (data.error) {
					alert(data.message)
					return;
                }

                for (var i=0; i<data.data.length; i++) {
					$('#main_business_sector').append('<li data-id="'+data.data[i].id+'">'+data.data[i].title+'</li>');
                }

                if (callback) callback();
			},
			error:   function () {
			}
		});
	}

	function get_main_business_type(sector_id, callback) {
		$('#main_business_type').html('');
		$.ajax({
			type:    'post',
			url:     '<?= base_url("forms/form_03/get_main_business_type"); ?>',
			data:    {sector_id: sector_id},
			success: function (data) {
				data = JSON.parse(data);
				console.log(data);
				if (data.error) {
					alert(data.message)
					return;
				}

				for (var i=0; i<data.data.length; i++) {
					$('#main_business_type').append('<li data-id="'+data.data[i].id+'">'+data.data[i].title+'</li>');
				}

				if (callback) callback();
			},
			error:   function () {
			}
		});
	}

	function get_main_business_area(sector_id, type_id, callback) {
		$('#main_business_area').html('');
		$.ajax({
			type:    'post',
			url:     '<?= base_url("forms/form_03/get_main_business_area"); ?>',
			data:    {sector_id: sector_id, type_id: type_id},
			success: function (data) {
				data = JSON.parse(data);
				console.log(data);
				if (data.error) {
					alert(data.message)
					return;
				}

				for (var i=0; i<data.data.length; i++) {
					$('#main_business_area').append('<li data-id="'+data.data[i].id+'">'+data.data[i].title+'</li>');
				}

				if (callback) callback();
			},
			error:   function () {
			}
		});
	}

	$(document).on('click', '#main_business_sector li', function (e) {
		e.stopImmediatePropagation();

		$('#main_business_sector li').removeClass('selected');
		$(this).addClass('selected');

		var id = $(this).attr('data-id');
		get_main_business_type(id);
	});
	$(document).on('click', '#main_business_type li', function (e) {
		e.stopImmediatePropagation();

		$('#main_business_type li').removeClass('selected');
		$(this).addClass('selected');

		var sector_id = $('#main_business_sector li.selected').attr('data-id');
		var id = $(this).attr('data-id');
		get_main_business_area(sector_id, id);
	});
	$(document).on('click', '#main_business_area li', function (e) {
		e.stopImmediatePropagation();

		$('#main_business_area li').removeClass('selected');
		$(this).addClass('selected');
	});

	get_main_business_sector();

	// select main business area
	$(document).on('click', '.select_list_add_btn', function (e) {
		e.stopImmediatePropagation();

		if ($('#main_business_sector li.selected').length == 0 ||
            $('#main_business_type li.selected').length == 0 ||
            $('#main_business_area li.selected').length == 0) {
			return;
        }

		var sector = $('#main_business_sector li.selected').text();
		var type = $('#main_business_type li.selected').text();
		var area_title = $('#main_business_area li.selected').text();
		var uid = guid();

		var html = '<tr>\
            <td>'+area_title+'</td>\
            <td>\
            <input type="hidden" name="products[main]['+uid+'][sector]" value="'+sector+'">\
            <input type="hidden" name="products[main]['+uid+'][type]" value="'+type+'">\
            <input type="hidden" name="products[main]['+uid+'][area]" value="'+area_title+'">\
            <button type="button" class="btn btn-default btn-xs remove_selected_main_area">x</button>\
            </td>\
            </tr>';
		$('#selected_business_area').append(html);

		$('#main_business_sector li').removeClass('selected');
		$('#main_business_type').html('');
		$('#main_business_area').html('');
	});
	$(document).on('click', '.remove_selected_main_area', function (e) {
		e.stopImmediatePropagation();

		$(this).parents('tr').remove();
	});


	// other business
	$(document).on('click', '.other_business_add_btn', function (e) {
		e.stopImmediatePropagation();
		var sector = $('#other_business_sector').val();
		var type = $('#other_business_type').val();
		var area = $('#other_business_area').val();
		var uid = guid();

		if (!sector || sector == '' ||
            !type || type == '' ||
            !area || area == '') {
			return;
        }

		var html = '<tr>\
            <td>'+area+'</td>\
            <td>\
            <input type="hidden" name="products[other]['+uid+'][sector]" value="'+sector+'">\
            <input type="hidden" name="products[other]['+uid+'][type]" value="'+type+'">\
            <input type="hidden" name="products[other]['+uid+'][area]" value="'+area+'">\
            <button type="button" class="btn btn-default btn-xs remove_selected_other_area">x</button>\
            </td>\
            </tr>';
		$('#other_business_selected_area').append(html);

		$('#other_business_sector').val('');
		$('#other_business_type').val('');
		$('#other_business_area').val('');
	});
	$(document).on('click', '.remove_selected_other_area', function (e) {
		e.stopImmediatePropagation();

		$(this).parents('tr').remove();
	});


	// product business
	$(document).on('click', '.product_add_btn', function (e) {
		e.stopImmediatePropagation();
		var category = $('#product_category').val();
		var type = $('#product_type').val();
		var name = $('#product_name').val();
		var uid = guid();

		if (!category || category == '' ||
            !type || type == '' ||
            !name || name == '') {
			return;
        }

		var html = '<tr>\
            <td>'+name+'</td>\
            <td>\
            <input type="hidden" name="products[product]['+uid+'][category]" value="'+category+'">\
            <input type="hidden" name="products[product]['+uid+'][type]" value="'+type+'">\
            <input type="hidden" name="products[product]['+uid+'][name]" value="'+name+'">\
            <button type="button" class="btn btn-default btn-xs remove_selected_product_btn">x</button>\
            </td>\
            </tr>';
		$('#product_selected_area').append(html);

		$('#product_category').val('');
		$('#product_type').val('');
		$('#product_name').val('');
	});
	$(document).on('click', '.remove_selected_product_btn', function (e) {
		e.stopImmediatePropagation();

		$(this).parents('tr').remove();
	});


    function submit_form() {
		var data = [];
		$('#company_principle_list tr').each(function() {
			var v = $(this).find('.principles_list').val()
			console.log(v)
			v = v.replace(/'/g, '"');
			v = JSON.parse(v);
			data.push(v);
		});
		var principle_data = {
			principle: {
				is_active: ($('.principles_is_active').is(':checked')) ? 1 : 0,
				principles_list: data
            }
		}


		$('.save_step_4').attr('disabled', true);
		var form_data = $('#exhibit_information').serialize();
		form_data += '&' + $('#profile_form').serialize();
		form_data += '&' + $('#products_form').serialize();
		form_data += '&' + object_serialize(principle_data);
		$.ajax({
			type:    'post',
			url:     '<?= base_url("forms/form_03/form_03_submit"); ?>',
			data:    form_data,
			success: function (data) {
				$('.save_step_4').attr('disabled', false);
				console.log(data);
				data = JSON.parse(data);
				$(".validation-failed").removeClass("validation-failed");

				if (data.error && data.error == 1) {
					alert(data.message);
					return;
				}

				window.location = data.data;
			},
			error:   function () {
				$('.save_step_4').attr('disabled', false);
			}
		});
	}

	function object_serialize(obj, prefix) {
		var str = [],
			p;
		for (p in obj) {
			if (obj.hasOwnProperty(p)) {
				var k = prefix ? prefix + "[" + p + "]" : p,
					v = obj[p];
				str.push((v !== null && typeof v === "object") ?
					object_serialize(v, k) :
					encodeURIComponent(k) + "=" + encodeURIComponent(v));
			}
		}
		return str.join("&");
	}
</script>
</body>
</html>
