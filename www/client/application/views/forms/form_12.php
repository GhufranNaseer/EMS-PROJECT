<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="form_12">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            FORM I-9
            <small></small>
        </h1>
        <h1 class="text-center">
            Annexure Car :
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
                        <form action="<?= base_url('inventory/order_forms/form_12_submit') ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">

                            <input type="hidden" name="form_id"
                                   id="form_id" placeholder="form_id" value="12">


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

                           <p><b>NOTE :</b>It is absolutely essential that all exhibitor and their personnel requiring access to the exhibition
                               site, fill in this Order Form specifying the following: </p>
                            <p><b>We have appointed the following persons of our company to attend or visit our stall / IDEAS 2018 :</b></p>

                            <div class="row">
                                <div class="col-sm-12">

                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th width="10%">S.No.</th>
                                            <th width="25">Name</th>
                                            <th width="20%">Designation</th>
                                            <th width="25%">Nationality</th>
                                            <th width="20">Car Registration
                                                Number & Make</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td><input type="text" name="name_1" id="name_1"></td>
                                            <td><input type="text" name="designation_1" id="designation_1"></td>
                                            <td><input type="text" name="nationality_1" id="nationality_1"></td>
                                            <td><input type="text" name="car_reg_no_1" id="car_reg_no_1"></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td><input type="text" name="name_2" id="name_2"></td>
                                            <td><input type="text" name="designation_2" id="designation_2"></td>
                                            <td><input type="text" name="nationality_2" id="nationality_2"></td>
                                            <td><input type="text" name="car_reg_no_2" id="car_reg_no_2"></td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td><input type="text" name="name_3" id="name_3"></td>
                                            <td><input type="text" name="designation_3" id="designation_3"></td>
                                            <td><input type="text" name="nationality_3" id="nationality_3"></td>
                                            <td><input type="text" name="car_reg_no_3" id="car_reg_no_3"></td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td><input type="text" name="name_4" id="name_4"></td>
                                            <td><input type="text" name="designation_4" id="designation_4"></td>
                                            <td><input type="text" name="nationality_4" id="nationality_4"></td>
                                            <td><input type="text" name="car_reg_no_4" id="car_reg_no_4"></td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td><input type="text" name="name_5" id="name_1"></td>
                                            <td><input type="text" name="designation_5" id="designation_5"></td>
                                            <td><input type="text" name="nationality_5" id="nationality_5"></td>
                                            <td><input type="text" name="car_reg_no_5" id="car_reg_no_5"></td>
                                        </tr>
                                        <tr>
                                            <td>6</td>
                                            <td><input type="text" name="name_6" id="name_6"></td>
                                            <td><input type="text" name="designation_6" id="designation_6"></td>
                                            <td><input type="text" name="nationality_6" id="nationality_6"></td>
                                            <td><input type="text" name="car_reg_no_6" id="car_reg_no_6"></td>
                                        </tr>
                                        <tr>
                                            <td>7</td>
                                            <td><input type="text" name="name_7" id="name_7"></td>
                                            <td><input type="text" name="designation_7" id="designation_7"></td>
                                            <td><input type="text" name="nationality_7" id="nationality_7"></td>
                                            <td><input type="text" name="car_reg_no_7" id="car_reg_no_7"></td>
                                        </tr>
                                        <tr>
                                            <td>8</td>
                                            <td><input type="text" name="name_8" id="name_8"></td>
                                            <td><input type="text" name="designation_8" id="designation_8"></td>
                                            <td><input type="text" name="nationality_8" id="nationality_8"></td>
                                            <td><input type="text" name="car_reg_no_8" id="car_reg_no_8"></td>
                                        </tr>

                                        </tbody>
                                    </table>


                                </div>

                            </div>
                            <h2  style="font-size: 15px; font-weight: 600; color: red;">Note: Use additional sheets if required. </h2>
                            <h4>Remarks: </h4>
                            <ul>
                                <li>(CNIC copy & one passport size photograph of each person’s car driver must accompany this Form with
                                    person’s name, CNIC number and company name clearly marked on the reverse side of each photo).</li>
                                <li>Order Forms received without photograph or incomplete information will not be processed.</li>
                                <li>Car Stickers (Access Permit) will be ready for collection from 15thNovember,2018 from Show Management
                                    office at site.</li>
                            </ul>


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
