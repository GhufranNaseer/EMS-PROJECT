<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="form_20">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            FORM I-17
            <small></small>
        </h1>
        <h1 class="text-center">
            Contractors’ Personnel Badges :
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
                        <form action="<?= base_url('inventory/order_forms/form_20_submit') ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">

                            <input type="hidden" name="form_id"
                                   id="form_id" placeholder="form_id" value="20">


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


                                    <table class="table">
                                        <caption>We have appointed the following company as our stand contractors: </caption>
                                        <tr>
                                            <td>Contractor Name:</td>
                                            <td><input type="text" class="form-control" id="item_13" name="item_13"></td>
                                            <td>Activity/Service:</td>
                                            <td><input type="text" class="form-control" id="item_14" name="item_14"></td>
                                        </tr>

                                        <tr>
                                            <td>Contact Person:</td>
                                            <td><input type="text" class="form-control" name="item_15" id="item_15"></td>
                                            <td>Title:</td>
                                            <td><input type="text" class="form-control" name="item_16" id="item_16"></td>
                                        </tr>
                                        <tr>
                                            <td>Tel:</td>
                                            <td><input type="text" class="form-control" name="item_17" id="item_17"></td>
                                            <td>Fax:</td>
                                            <td><input type="text" class="form-control" name="item_18" id="item_18"></td>
                                            <td>Email:</td>
                                            <td><input type="text" class="form-control" name="item_19" id="item_19"></td>
                                        </tr>
                                    </table>

                                    <h4><b>Remarks:</b></h4>
                                    <p>It is absolutely essential that all exhibitors and their representatives requiring access to the exhibition
                                        site, fill in this Order Form specifying the following: </p>
                                    <ol>
                                        <li>Name</li>
                                        <li>Nationality</li>
                                        <li>Passport CNIC Number</li>
                                        <li>Title.</li>
                                    </ol>
                                    <li>(Two passport size photographs & copy of passport of foreign national & CNIC of Pakistani National are required). </li>
                                    <li>Order Forms received without photograph or incomplete information will not be processed. </li>
                                    <li>Contractors badges will be ready for collection from 01stNovember,2018 from Show Management
                                        office at site till 20th November,2018.</li>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>No.</th>
                                            <th>Name</th>
                                            <th>Title</th>
                                            <th>Nationality</th>
                                            <th>Passport/CNIC</th>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td><input type="text" name="item_1" id="item_1"></td>
                                            <td><input type="text" name="item_2" id="item_2"></td>
                                            <td><input type="text" name="item_3" id="item_3"></td>
                                            <td><input type="text" name="item_4" id="item_4"></td>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td><input type="text" name="item_5" id="item_5"></td>
                                            <td><input type="text" name="item_6" id="item_6"></td>
                                            <td><input type="text" name="item_7" id="item_7"></td>
                                            <td><input type="text" name="item_8" id="item_8"></td>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td><input type="text" name="item_9" id="item_9"></td>
                                            <td><input type="text" name="item_10" id="item_10"></td>
                                            <td><input type="text" name="item_11" id="item_11"></td>
                                            <td><input type="text" name="item_12" id="item_12"></td>
                                        </tr>
                                    </table>

                                    <h4><b>Note: Use additional sheets if required.</b></h4>

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
			'urlValidator': "<?php echo base_url("inventory/order_forms/form_20_validate/doError"); ?>",
			'loadingImg':   "<?php echo base_url("../assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});


    $('#company_fax_no').inputmask("999-9999999")
    $('#company_cell_no').inputmask("9999-9999999")
</script>
</body>
</html>
