<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="form_16">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            FORM I-13
            <small></small>
        </h1>
        <h1 class="text-center">
            Furniture / Audio Visual Equipment Rental :
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Form</li>
        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h4 class=" text-center" style="color: #f5f5f5; background: grey; padding: 8px;">“To Be Submitted By Last Submission Date"</h4>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('inventory/order_forms/form_16_submit') ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">

                            <input type="hidden" name="form_id"
                                   id="form_id" placeholder="form_id" value="16">


                            <div class="row">
    <div class="col-sm-8">

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <input type="text" class="form-control" name="company_name"
                           id="company_name" placeholder="Company Name">
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="company_address">Address</label>
                    <input type="text" class="form-control" name="company_address"
                           id="company_address" placeholder="Address">
                </div>
            </div>

        </div>

    </div>
    <div class="col-sm-4">

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="hall_no">Hall No</label>
                    <input type="text" class="form-control" name="hall_no"
                           id="hall_no" placeholder="Hall No">
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="stand_no">Stand No</label>
                    <input type="text" class="form-control" name="stand_no"
                           id="stand_no" placeholder="Stand No">
                </div>
            </div>

        </div>



    </div>

</div>

                            <div class="row">
                                <div class="col-sm-8">


                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="company_country">Country</label>
                                                <input type="text" class="form-control" name="company_country" id="company_country"
                                                       placeholder="Country" list="data_country">
                                                <?php $options= $this->db->where('type','country')->get('input_data_list')->result();?>
                                                <datalist id="data_country">
                                                    <?php foreach ($options as $option) {?>
                                                    <option value="<?= $option->data ?>">
                                                        <?php } ?>
                                                </datalist>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="company_zip_code">Zip Code</label>
                                                <input type="text" class="form-control" name="company_zip_code" id="company_zip_code"
                                                       placeholder="Zip Code">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="company_telephone_no">Tel</label>
                                                <input type="text" class="form-control" name="company_telephone_no"
                                                       id="company_telephone_no" placeholder="Tel No">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="company_fax_no">Fax</label>
                                                <input type="text" class="form-control" name="company_fax_no"
                                                       id="company_fax_no" placeholder="Fax No">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="company_email">Email</label>
                                                <input type="text" class="form-control" name="company_email"
                                                       id="company_email" placeholder="Email">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="company_cell_no">Cell</label>
                                                <input type="text" class="form-control" name="company_cell_no"
                                                       id="company_cell_no" placeholder="Cell">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="company_contact_person">Contact Person</label>
                                                <input type="text" class="form-control" name="company_contact_person"
                                                       id="company_contact_person" placeholder="Contact Person">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="company_tittle">Title</label>
                                                <input type="text" class="form-control" name="company_tittle"
                                                       id="company_tittle" placeholder="Title">
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <div class="col-sm-4">

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div style="border: 1px solid #8080807a; height: 108px; margin-top: 24px;">

                                            </div>
                                            <h4 class="text-center">Co. Seal & Signature</h4>
                                        </div>

                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">

                                    <table class="table table-bordered">
                                        <caption style="padding-bottom: 0px;">Please indicate your requirements in the table below</caption>
                                        <thead>

                                        <tr>
                                            <th width="25%">Item No.</th>
                                            <th width="25%">Item </th>
                                            <th width="25%">Unit Cost US$  </th>
                                            <th width="25%">Quantity </th>
                                            <th width="25%">Total Cost US$ </th>

                                        </tr>
                                        </thead>
                                        <tbody>
                                      <tr>
                                          <td>BR-1 </td>
                                          <td>Brochure Rack</td>
                                          <td>$49.00</td>
                                          <td><input type="text" id="item_1" name="item_1"></td>
                                          <td>$0.00</td>
                                      </tr>

                                      <tr>
                                          <td>CS-1  </td>
                                          <td>Carpet Synthetic 12 sqm Each</td>
                                          <td>$55.00</td>
                                          <td><input type="text" id="item_2" name="item_2"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>C-1  </td>
                                          <td>Chair (black-pvc)</td>
                                          <td>$44.00</td>
                                          <td><input type="text" id="item_3" name="item_3"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>C-2  </td>
                                          <td>Chair (white-pvc)</td>
                                          <td>$28.00</td>
                                          <td><input type="text" id="item_4" name="item_4"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>C-3  </td>
                                          <td>Chair, Revolving</td>
                                          <td>$64.00</td>
                                          <td><input type="text" id="item_5" name="item_5"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>DB-1  </td>
                                          <td>Display Board</td>
                                          <td>$29.00</td>
                                          <td><input type="text" id="item_6" name="item_6"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>FS-1  </td>
                                          <td>Flat Shelf</td>
                                          <td>$22.00</td>
                                          <td><input type="text" id="item_7" name="item_7"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>IC-1  </td>
                                          <td>Information Counter</td>
                                          <td>$66.00</td>
                                          <td><input type="text" id="item_8" name="item_8"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>P-1  </td>
                                          <td>Partition</td>
                                          <td>$36.00</td>
                                          <td><input type="text" id="item_9" name="item_9"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>S-1  </td>
                                          <td>Sofa Seat (Single without arm)</td>
                                          <td>$80.00</td>
                                          <td><input type="text" id="item_10" name="item_10"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>T-1  </td>
                                          <td>Table (Square/Round/Rectangular)</td>
                                          <td>$50.00</td>
                                          <td><input type="text" id="item_11" name="item_11"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>WB-1   </td>
                                          <td>Waste Basket</td>
                                          <td>$6.00</td>
                                          <td><input type="text" id="item_12" name="item_12"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>CM-1   </td>
                                          <td>Coffee Machine</td>
                                          <td>$33.00</td>
                                          <td><input type="text" id="item_13" name="item_13"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>CP-1   </td>
                                          <td>Computer </td>
                                          <td>$145.00</td>
                                          <td><input type="text" id="item_14" name="item_14"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>DP-1  </td>
                                          <td>DVD Player</td>
                                          <td>$25.00</td>
                                          <td><input type="text" id="item_15" name="item_15"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>EK-1   </td>
                                          <td>Electric Kettle</td>
                                          <td>$22.00</td>
                                          <td><input type="text" id="item_16" name="item_16"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>P-1    </td>
                                          <td>LCD 32”</td>
                                          <td>$150.00</td>
                                          <td><input type="text" id="item_17" name="item_17"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>P-2   </td>
                                          <td>LCD 40”</td>
                                          <td>$180.00</td>
                                          <td><input type="text" id="item_18" name="item_18"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>P-3   </td>
                                          <td>Sound System</td>
                                          <td>$50.00</td>
                                          <td><input type="text" id="item_19" name="item_19"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>WD-1   </td>
                                          <td>Water Dispenser (Hot/Cold)</td>
                                          <td>$150.00</td>
                                          <td><input type="text" id="item_20" name="item_20"></td>
                                          <td>$0.00</td>
                                      </tr>
                                        </tbody>
                                    </table>


                                    <h4 style="float: right"><b>Total US$</b> $0.00</h4>
                                      <h4><b>Note :</b>Late orders will be subject to 15% additional charge</h4>
                                    <h4><b>Payment :</b>The Exhibitor’s order must be accompanied by full payment. Please contact the Event Manager.</h4>

                                </div>

                            </div>



                            <div class="row">
                                <div class="col-sm-6">
                                    <h4 class=" text-center" style="color: #f5f5f5; background: grey; padding: 8px;">For Event Manager:</h4>
                                    <div class="form-group">
                                        <label for="manager_name">Name</label>
                                        <input type="text" class="form-control" name="manager_name"
                                               id="manager_name" placeholder="Manager Name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h4 class=" text-center" style="color: #f5f5f5; background: grey; padding: 8px;">For Exhibitor’s Contractor:</h4>
                                    <div class="form-group">
                                        <label for="cont_company_name">Company Name</label>
                                        <input type="text" class="form-control" name="cont_company_name"
                                               id="cont_company_name" placeholder="Company Name">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="manager_title">Title</label>
                                        <input type="text" class="form-control" name="manager_title"
                                               id="manager_title" placeholder="Tittle">
                                    </div>
                                </div>
                                <div class="col-sm-6">

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="contractor_personnel">Personnel:</label>
                                                <input type="text" class="form-control" name="contractor_company_personnel"
                                                       id="contractor_company_personnel" placeholder="Personnel">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="contractor_title">Title:</label>
                                                <input type="text" class="form-control" name="contractor_title"
                                                       id="contractor_title" placeholder="Title">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="manager_date">Date</label>
                                        <input type="date" class="form-control" name="manager_date"
                                               id="manager_date" placeholder="Date">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="contractor_date">Date</label>
                                        <input type="date" class="form-control" name="contractor_date"
                                               id="contractor_date" placeholder="Date">
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="manager_signature">Signature</label>
                                        <input type="text" class="form-control" name="manager_signature"
                                               id="manager_signature" placeholder="Signature">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="contractor_signature">Signature</label>
                                        <input type="text" class="form-control" name="contractor_signature"
                                               id="contractor_signature" placeholder="Signature">
                                    </div>
                                </div>
                            </div>

                             <p class="bg-danger js-msgbox"></p>

                            <div class="row">
                                <div class="col-xs-10">

                                </div>
                                <div class="col-xs-2">
                                    <a href="javascript:void(0);"
                                       class="btn btn-primary btn-block margin-bottom js-form_btn">Submite</a>
                                </div>
                            </div>



                            <h4 class="text-center" style="color: #f5f5f5; background: grey; padding: 8px;">Contact Expo Solutions<sub><br/>
                                    Bungalow # C-175, Block -9, Gulshan-e-Iqbal, Near Aziz Bhatti Park, Karachi, Pakistan .<br/>
                                    Tel: +92-21-34821160, +92-21- 34821159, Fax: +92-21-34821179 <br/>
                                    URL: <a href="http://www.minimaxsolution.com"> minimaxsolution.com</a>, E-mail: info@minimaxsolution.com
                                </sub>

                            </h4>

                        </form>

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

		var faqadd = {
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("inventory/order_forms/form_16_validate/doError"); ?>",
			'loadingImg':   "<?php echo base_url("../assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});


    $('#company_fax_no').inputmask("999-9999999")
    $('#company_cell_no').inputmask("9999-9999999")
</script>
</body>
</html>
