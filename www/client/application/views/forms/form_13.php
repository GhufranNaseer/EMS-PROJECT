<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="form_13">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            FORM I-10
            <small></small>
        </h1>
        <h1 class="text-center">
            Telecommunications & Internet Facilities:
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
                        <form action="<?= base_url('inventory/order_forms/form_13_submit') ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">

                            <input type="hidden" name="form_id"
                                   id="form_id" placeholder="form_id" value="13">


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

                            <h4 class=" text-center" style="color: #f5f5f5; background: grey; padding: 8px;">The following communication facilities shall be available on order at the stands: </h4>

                            <div class="row">
                                <div class="col-sm-12">

                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th width="14%">Facilities </th>
                                            <th width="14%">Required Unit No. </th>
                                            <th width="14%">Tariff (US$) Per Unit </th>
                                            <th width="14%">Tariff (US$) Total </th>
                                            <th width="14%">Deposit Per Unit</th>
                                            <th width="14%">Deposit Total </th>
                                            <th width="14%">Total
                                                Per Unit US$ </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Local Telephone.</td>
                                            <td><input type="text" name="communication_facilities_tel_unit" id="communication_facilities_tel_unit"></td>
                                            <td>$100.00</td>
                                            <td>$0.00</td>
                                            <td>Nill</td>
                                            <td>$0.00</td>
                                            <td>$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>Local Fax Machine.</td>
                                            <td><input type="text" name="communication_facilities_fax_unit" id="communication_facilities_fax_unit"></td>
                                            <td>$200.00</td>
                                            <td>$0.00</td>
                                            <td>$100.00</td>
                                            <td>$0.00</td>
                                            <td>$0.00</td>
                                        </tr>

                                        </tbody>
                                    </table>


                                    <h4 style="float: right"><b>Total</b> $0.00</h4>


                                </div>

                            </div>
                            <h4 class=" text-center" style="color: #f5f5f5; background: grey; padding: 8px;">The following Internet facilities shall be available on order:  </h4>
                            <h2  style="font-size: 17px; font-weight: 600;">Internet DSL standard Package : </h2>

<div class="row">
    <div class="col-sm-12">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th width="14%">Internet
                    Connection </th>
                <th width="14%">No. of
                    Units </th>
                <th width="14%">Hire Charges US$ Per Unit </th>
                <th width="14%">Hire Charges US$ Amount </th>
                <th width="14%">Security Deposit US$ Per Unit</th>
                <th width="14%">Security Deposit US$ Amount </th>
                <th width="14%">Total
                    US$ </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>2MBPS (SHARED)</td>
                <td><input type="text" id="internet_facilities_2mbps_shared" name="internet_facilities_2mbps_shared"></td>
                <td>$125.00</td>
                <td>$0.00</td>
                <td>$100.00</td>
                <td>$0.00</td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>4MBPS (SHARED)</td>
                <td><input type="text" name="internet_facilities_4mbps_shared" id="internet_facilities_4mbps_shared"></td>
                <td>$200.00</td>
                <td>$0.00</td>
                <td>$100.00</td>
                <td>$0.00</td>
                <td>$0.00</td>
            </tr>

            <tr>
                <td>10MBPS (SHARED)</td>
                <td><input type="text" id="internet_facilities_10mbps_shared" name="internet_facilities_10mbps_shared"></td>
                <td>$300.00</td>
                <td>$0.00</td>
                <td>$100.00</td>
                <td>$0.00</td>
                <td>$0.00</td>
            </tr>
            </tbody>
        </table>
        <h4 style="float: right"><b>Total</b> $0.00</h4>
        <table class="table table-bordered">

            <tbody>
            <tr>
                <td width="14%">2MBPS (CIR+IP)</td>
                <td width="14%"><input type="text" name="internet_facilities_2mbps_cir" id="internet_facilities_2mbps_cir"></td>
                <td width="14%">$200.00</td>
                <td width="14%">$0.00</td>
                <td width="14%">$100.00</td>
                <td width="14%">$0.00</td>
                <td width="14%">$0.00</td>
            </tr>
            <tr>
                <td>4MBPS (CIR+IP)</td>
                <td><input type="text" name="internet_facilities_4mbps_cir" id="internet_facilities_4mbps_cir"></td>
                <td>$300.00</td>
                <td>$0.00</td>
                <td>$150.00</td>
                <td>$0.00</td>
                <td>$0.00</td>
            </tr>

            <tr>
                <td>10MBPS (CIR+IP)</td>
                <td><input type="text" name="internet_facilities_10mbps_cir" id="internet_facilities_10mbps_cir"></td>
                <td>$400.00</td>
                <td>$0.00</td>
                <td>$200.00</td>
                <td>$0.00</td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>20MBPS (CIR+IP)</td>
                <td><input type="text" id="internet_facilities_20mbps_cir" name="internet_facilities_20mbps_cir"></td>
                <td>$500.00</td>
                <td>$0.00</td>
                <td>$250.00</td>
                <td>$0.00</td>
                <td>$0.00</td>
            </tr>
            </tbody>
        </table>

        <div class="row">
            <div class="col-sm-9"><h4>Additional Equipment's (Rental):<i class="fa fa-wifi"></i> Wifi Router 100$, Network Switch 5Port 100$</h4>
                <h4><b>Note:</b></h4>
                <ul>
                    <li>The Telephone, Fax & ISDN Units (Modems) equipment shall have to be returned to Badar Expo Solutions for clearance.</li>
                    <li>The Exhibitor’s order must be accompanied by full payment. Please contact the Event Manager.</li>
                    <li>All the charges mentioned above are applicable for the entire show.</li>
                </ul>
            </div>
            <div class="col-sm-3">
                <h4 class="text-right"><b>Total</b> $0.00</h4>
                <h4 class="text-right"><b>Grand Total</b> $0.00</h4>
            </div>

        </div>

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
                                                <label for="contractor_company_personnel">Personnel:</label>
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
			'urlValidator': "<?php echo base_url("inventory/order_forms/form_13_validate/doError"); ?>",
			'loadingImg':   "<?php echo base_url("../assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});


    $('#company_fax_no').inputmask("999-9999999")
    $('#company_cell_no').inputmask("9999-9999999")
</script>
</body>
</html>
