<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="form_07">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            FORM I-4
            <small></small>
        </h1>
        <h1 class="text-center">
            Show Catalogue/ Show Dailies Advertisement :
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
                        <form action="<?= base_url('inventory/order_forms/form_07_submit') ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">

                            <input type="hidden" name="form_id"
                                   id="form_id" placeholder="form_id" value="7">


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

                            <h2  style="font-size: 17px; font-weight: 600;">PROMOTE YOUR COMPANY AT IDEAS-2018 </h2>
                            <ul>
                                <li>The IDEAS-2018 Exhibitors’ Catalogue shall be 210mm high x 140mm wide and contain theShow details, list of exhibitors
                                    and their profiles & advertisements.</li>
                                <li>The Ad should be of 300 dpi resolution and in Tiff format.</li>
                                <li>You can make sure that you reach all the VIP's , delegates ,exhibitors and visitor's to IDEAS 2018</li>
                               <p>Please order your requirement of advertisement in the IDEAS-2018 Exhibitor's catalogue as under and provide the artwork . For
                                   payment please contact Badar Expo Solutions</p>
                            </ul>

                            <div class="row">
                                <div class="col-sm-6">

                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th width="10%">S.No.</th>
                                            <th width="25">AD Specification</th>
                                            <th width="20%">Tariff (US$)</th>
                                            <th width="25%">No. of Ads</th>
                                            <th width="20"> Amt.(US$)</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Belly Band</td>
                                            <td>$2,700.00</td>
                                            <td><input type="text" name="band" id="band"></td>
                                            <td>$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Company Logo Display</td>
                                            <td>$200.00</td>
                                            <td><input type="text" id="logo" name="logo"></td>
                                            <td>$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Double Page Advert</td>
                                            <td>$2,200.00</td>
                                            <td><input type="text" name="page_advert" id="page_advert"></td>
                                            <td>$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>First Page</td>
                                            <td>$1,800.00</td>
                                            <td><input type="text" name="first_page" id="first_page"></td>
                                            <td>$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td>Full Page Advert</td>
                                            <td>$1,200.00</td>
                                            <td><input type="text" id="full_page" name="full_page"></td>
                                            <td>$0.00</td>
                                        </tr>

                                        </tbody>
                                    </table>


                                </div>
                                <div class="col-sm-6">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th width="10%">S.No.</th>
                                            <th width="25">AD Specification</th>
                                            <th width="20%">Tariff (US$)</th>
                                            <th width="25%">No. of Ads</th>
                                            <th width="20"> Amt.(US$)</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>6</td>
                                            <td>Inner Back</td>
                                            <td>$1,800.00</td>
                                            <td><input type="text" id="inner_back" name="inner_back"></td>
                                            <td>$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>7</td>
                                            <td>Inner Front</td>
                                            <td>$2,100.00</td>
                                            <td><input type="text" id="inner_front" name="inner_front"></td>
                                            <td>$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>8</td>
                                            <td>Outer Back</td>
                                            <td>$3,500.00</td>
                                            <td><input type="text" id="outer_back" name="outer_back"></td>
                                            <td>$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>9</td>
                                            <td>Quarter Page Advert</td>
                                            <td>$700.00</td>
                                            <td><input type="text" name="quarter_page" id="quarter_page"></td>
                                            <td>$0.00</td>
                                        </tr>


                                        </tbody>
                                    </table>
                                    <div class="form-group">
                                        <label for="g_total" class="col-sm-2 control-label">G.Total</label>
                                        <div class="col-sm-10">
                                            <input type="number" class="form-control" name="g_total" id="g_total" placeholder="$0.00">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <h2  style="font-size: 15px; font-weight: 600; color: red;">Upon availability of space </h2>
                            <p><b>The IDEAS-2018 Show Dailies shall</b> be 280mm high x 210mm wide and contain all the latest news from the Show. Three issues
                                of the Show Dailies shall be written on site and published. Interested advertisers may contact Badar Expo Solutions for booking
                                their Ads in the Show Dailies </p>
                               <h2 style="font-size: 15px; font-weight: 600;" class="text-center">IDEAS 2018 SHOW DAILY RATES & COMBO OFFER WITH ASIAN MILITARY REVIEW (all rates in USD)</h2>
                                <p class="text-center">IDEAS SHOW DAILY RATES (all rates in USD)</p>

                            <div class="row">
                                <div class="col-sm-6">

                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th width="25%">Insertions / Page</th>
                                            <th width="25%">Per Page
                                                Rate for 1 days</th>
                                            <th width="25%">Per Page
                                                Rate for 2 days</th>
                                            <th width="25%">Per Page
                                                Rate for 3 days</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Full Page</td>
                                            <td>$2,600 <input style="text-align: right;" type="checkbox" name="insertions_entry[]" value="r_1"></td>
                                            <td>$4,200 <input style="text-align: right;" type="checkbox" name="insertions_entry[]" value="r_2"></td>
                                            <td>$3,900 <input style="text-align: right;" type="checkbox" name="insertions_entry[]" value="r_3"></td>
                                        </tr>

                                        <tr>
                                            <td>Half Page</td>
                                            <td>$2,000 <input style="text-align: right;" type="checkbox" name="insertions_entry[]" value="r_4"></td>
                                            <td>$2,900 <input style="text-align: right;" type="checkbox" name="insertions_entry[]" value="r_5"></td>
                                            <td>$2,650 <input style="text-align: right;" type="checkbox" name="insertions_entry[]" value="r_6"></td>
                                        </tr>

                                        <tr>
                                            <td>1/4 Page</td>
                                            <td>$1,200 <input style="text-align: right;" type="checkbox" name="insertions_entry[]" value="r_7"></td>
                                            <td>$2,200 <input style="text-align: right;" type="checkbox" name="insertions_entry[]" value="r_8"></td>
                                            <td>$1,900 <input style="text-align: right;" type="checkbox" name="insertions_entry[]" value="r_9"></td>
                                        </tr>

                                        </tbody>
                                    </table>


                                </div>
                                <div class="col-sm-6">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th width="25%">Insertions / Page</th>
                                            <th width="25%">Per Page
                                                Rate for 1 days</th>
                                            <th width="25%">Per Page
                                                Rate for 2 days</th>
                                            <th width="25%">Per Page
                                                Rate for 3 days</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Issue Front Cover or
                                                Issue Front Cover or</td>
                                            <td>$3,200 <input style="text-align: right;" type="checkbox" name="insertions_entry[]" value="r_10"></td>
                                            <td>$2,800 <input style="text-align: right;" type="checkbox" name="insertions_entry[]" value="r_11"></td>
                                            <td>$2,200 <input style="text-align: right;" type="checkbox"name="insertions_entry[]" value="r_12"></td>
                                        </tr>

                                        <tr>
                                            <td>Inside Back Cover r</td>
                                            <td>$2,900 <input style="text-align: right;" type="checkbox" name="insertions_entry[]" value="r_13"></td>
                                            <td>$2,700 <input style="text-align: right;" type="checkbox" name="insertions_entry[]" value="r_14"></td>
                                            <td>$2,000 <input style="text-align: right;" type="checkbox" name="insertions_entry[]" value="r_15"></td>
                                        </tr>


                                        </tbody>
                                    </table>

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
			'urlValidator': "<?php echo base_url("inventory/order_forms/form_07_validate/doError"); ?>",
			'loadingImg':   "<?php echo base_url("../assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});


    $('#company_fax_no').inputmask("999-9999999")
    $('#company_cell_no').inputmask("9999-9999999")
</script>
</body>
</html>
