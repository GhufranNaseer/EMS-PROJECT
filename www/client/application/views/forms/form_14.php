<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="form_14">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            FORM I-11
            <small></small>
        </h1>
        <h1 class="text-center">
            Electrical Services Supply :
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
                        <form action="<?= base_url('inventory/order_forms/form_14_submit') ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">

                            <input type="hidden" name="form_id"
                                   id="form_id" placeholder="form_id" value="14">


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

                           <p>The ‘Shell Scheme’ package includes 1 x 10 amp socket and 4 spotlights (100w) per 12 m2. For the ‘Space only’ exhibitors, their contractor
                               must carry out all electrical connections. The main supply of electricity should be ordered from section B, C or D of this Form, through
                               the Event Manager IDEAS-2018. </p>
                            <p style="border-bottom: 1px solid #8080807a"><b>Note:</b> All the charges mentioned above are applicable for the entire show.</p>

                            <div class="row">
                                <div class="col-sm-12">

                                    <table class="table table-bordered">
                                        <caption style="padding-bottom: 0px;">INDOOR SERVICE</caption>
                                        <caption style="padding-bottom: 0px;">Section A - Additional electrical fittings and cost (Shell Scheme Exhibitors)</caption>
                                        <thead>

                                        <tr>
                                            <th width="25%">Item </th>
                                            <th width="25%">Unit Cost US$  </th>
                                            <th width="25%">Quantity </th>
                                            <th width="25%">Total Cost US$ </th>

                                        </tr>
                                        </thead>
                                        <tbody>
                                      <tr>
                                          <td>100 W standard spot light</td>
                                          <td>$33.00</td>
                                          <td><input type="text" id="item_1" name="item_1"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>100 W arm spot light</td>
                                          <td>$36.00</td>
                                          <td><input type="text" id="item_2" name="item_2"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>300 W halogen light</td>
                                          <td>$50.00</td>
                                          <td><input type="text" id="item_3" name="item_3"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>500 W halogen floodlight</td>
                                          <td>$58.00</td>
                                          <td><input type="text" id="item_4" name="item_4"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>10 amp socket</td>
                                          <td>$25.00</td>
                                          <td><input type="text" name="item_5" id="item_5"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>15 amp socket</td>
                                          <td>$40.00</td>
                                          <td><input type="text" name="item_6" id="item_6"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>Multi pin plug adaptor</td>
                                          <td>$9.00</td>
                                          <td><input type="text" name="item_7" id="item_7"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>Extension lead</td>
                                          <td>$11.00</td>
                                          <td><input type="text" name="item_8" id="item_8"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>30/40 W fluorescent fixture</td>
                                          <td>$33.00</td>
                                          <td><input type="text" name="item_9" id="item_9"></td>
                                          <td>$0.00</td>
                                      </tr>
                                      <tr>
                                          <td>Connection of fitting per 1 KW</td>
                                          <td>$33.00</td>
                                          <td><input type="text" name="item_10" id="item_10"></td>
                                          <td>$0.00</td>
                                      </tr>

                                        </tbody>
                                    </table>


                                    <h4 style="float: right"><b>Sub Total</b> $0.00</h4>


                                    <table class="table table-bordered">
                                        <caption style="padding-bottom: 0px; border-top:1px solid #8080806b ">Section B - Main supply for small power and lights (Bare Space Exhibitors)</caption>

                                        <tbody>
                                        <tr>
                                            <td width="25%">15 amp SPNE 220V - 50Hz</td>
                                            <td width="25%">$275.00</td>
                                            <td width="25%"><input type="text" name="item_11" id="item_11"></td>
                                            <td width="25%">$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>15 amp TPNE 415V - 50Hzt</td>
                                            <td>$375.00</td>
                                            <td><input type="text" name="item_12" id="item_12"></td>
                                            <td>$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>32 amp SPNE 220V - 50Hz</td>
                                            <td>$429.00</td>
                                            <td><input type="text" name="item_13" id="item_13"></td>
                                            <td>$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>32 amp TPNE 415V - 50Hz</td>
                                            <td>$605.00</td>
                                            <td><input type="text" name="item_14" id="item_14"></td>
                                            <td>$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>63 amp SPNE 220V - 50Hz</td>
                                            <td>$825.00</td>
                                            <td><input type="text" name="item_15" id="item_15"></td>
                                            <td>$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>63 amp TPNE 415V - 50Hz</td>
                                            <td>$935.00</td>
                                            <td><input type="text" name="item_16" id="item_16"></td>
                                            <td>$0.00</td>
                                        </tr>


                                        </tbody>
                                    </table>


                                    <h4 style="float: right"><b>Sub Total</b> $0.00</h4>



                                    <table class="table table-bordered">
                                        <caption style="padding-bottom: 0px; border-top:1px solid #8080806b ">Section C - Heavy duty machines including isolator and connection (Bare Space Exhibitors)</caption>

                                        <tbody>
                                        <tr>
                                            <td width="25%">15 amp TPNE 415V - 50Hz</td>
                                            <td width="25%">$495.00</td>
                                            <td width="25%"><input type="text" name="item_17" id="item_17"></td>
                                            <td width="25%">$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>32 amp TPNE 415V - 50Hz</td>
                                            <td>$704.00</td>
                                            <td><input type="text" name="item_18" id="item_18"></td>
                                            <td>$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>63 amp TPNE 415V - 50Hz</td>
                                            <td>$1,078.00</td>
                                            <td><input type="text" name="item_19" id="item_19"></td>
                                            <td>$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>15 amp TPNE 415V - 50Hz</td>
                                            <td>$495.00</td>
                                            <td><input type="text" name="item_20" id="item_20"></td>
                                            <td>$0.00</td>
                                        </tr>

                                        </tbody>
                                    </table>


                                    <h4 style="float: right"><b>Sub Total</b> $0.00</h4>


                                    <table class="table table-bordered">
                                        <caption style="padding-bottom: 0px; border-top:1px solid #8080806b ">Section D - 24 hour supply (Bare Space Exhibitors)</caption>

                                        <tbody>
                                        <tr>
                                            <td width="25%">10 amp SPNE 220V - 50Hz Socket only </td>
                                            <td width="25%">$115.00</td>
                                            <td width="25%"><input type="text" name="item_21" id="item_21"></td>
                                            <td width="25%">$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>15 amp SPNE 220V - 50Hz Socket only </td>
                                            <td>$176.00</td>
                                            <td><input type="text" name="item_22" id="item_22"></td>
                                            <td>$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>15 amp TPNE 415V - 50Hz</td>
                                            <td>$517.00</td>
                                            <td><input type="text" name="item_23" id="item_23"></td>
                                            <td>$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>32 amp SPNE 220V - 50Hz</td>
                                            <td>$572.00</td>
                                            <td><input type="text" name="item_24" id="item_24"></td>
                                            <td>$0.00</td>
                                        </tr>

                                        <tr>
                                            <td>32 amp TPNE 415V - 50Hz</td>
                                            <td>$693.00</td>
                                            <td><input type="text" name="item_25" id="item_25"></td>
                                            <td>$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>63 amp SPNE 415V - 50Hz</td>
                                            <td>$913.00</td>
                                            <td><input type="text" name="item_26" id="item_26"></td>
                                            <td>$0.00</td>
                                        </tr>
                                        <tr>
                                            <td>63 amp TPNE 415V - 50Hz</td>
                                            <td>$1,073.00</td>
                                            <td><input type="text" name="item_27" id="item_27"></td>
                                            <td>$0.00</td>
                                        </tr>
                                        </tbody>
                                    </table>


                                    <h4 style="float: right"><b>Sub Total</b> $0.00<br/><b>Grand Total</b> $0.00</h4>


                                </div>

                            </div>
                            <h4 class=" text-center" style="color: #f5f5f5; background: grey; padding: 8px;"></h4>


<div class="row">
    <div class="col-sm-12">


        <table class="table table-bordered">
            <caption style="padding-bottom: 0px;">OUTDOOR SERVICE (Outdoor Bare Space Exhibitors)</caption>
            <caption style="padding-bottom: 0px;">Section A - Additional electrical fittings and cost </caption>
            <caption style="padding-bottom: 0px;">Note: All the charges mentioned above are applicable for the entire Show . </caption>
            <thead>

            <tr>
                <th width="25%">Item </th>
                <th width="25%">Unit Cost US$  </th>
                <th width="25%">Quantity </th>
                <th width="25%">Total Cost US$ </th>

            </tr>
            </thead>
            <tbody>
            <tr>
                <td>100 W standard spot light</td>
                <td>$44.00</td>
                <td><input type="text" name="item_28" id="item_28"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>100 W arm spot light</td>
                <td>$50.00</td>
                <td><input type="text" name="item_29" id="item_29"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>300 W halogen light</td>
                <td>$55.00</td>
                <td><input type="text" name="item_30" id="item_30"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>500 W halogen floodlight</td>
                <td>$72.00</td>
                <td><input type="text" name="item_31" id="item_31"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>10 amp socket</td>
                <td>$44.00</td>
                <td><input type="text" name="item_32" id="item_32"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>15 amp socket</td>
                <td>$55.00</td>
                <td><input type="text" name="item_33" id="item_33"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>Multi pin plug adaptor</td>
                <td>$7.00</td>
                <td><input type="text" name="item_34" id="item_34"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>Extension lead</td>
                <td>$10.00</td>
                <td><input type="text" name="item_35" id="item_35"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>30/40 W fluorescent fixture</td>
                <td>$44.00</td>
                <td><input type="text" name="item_36" id="item_36"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>Connection of fitting per 1 KW</td>
                <td>$33.00</td>
                <td><input type="text" name="item_37" id="item_37"></td>
                <td>$0.00</td>
            </tr>

            </tbody>
        </table>
        <h4 style="float: right"><b>Sub Total</b> $0.00</h4>

        <table class="table table-bordered">
            <caption style="padding-bottom: 0px; border-top:1px solid #8080806b ">Section B - Main supply for small power and lights</caption>

            <tbody>
            <tr>
                <td width="25%">15 amp SPNE 220V - 50Hz</td>
                <td width="25%">$682.00</td>
                <td width="25%"><input type="text" name="item_38" id="item_38"></td>
                <td width="25%">$0.00</td>
            </tr>
            <tr>
                <td>15 amp TPNE 415V - 50Hzt</td>
                <td>$825.00</td>
                <td><input type="text" name="item_39" id="item_39"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>32 amp SPNE 220V - 50Hz</td>
                <td>$880.00</td>
                <td><input type="text" name="item_40" id="item_40"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>32 amp TPNE 415V - 50Hz</td>
                <td>$1,650.00</td>
                <td><input type="text" name="item_41" id="item_41"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>63 amp SPNE 220V - 50Hz</td>
                <td>$1,815.00</td>
                <td><input type="text" name="item_42" id="item_42"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>63 amp TPNE 415V - 50Hz</td>
                <td>$2,310.00</td>
                <td><input type="text" name="item_43" id="item_43"></td>
                <td>$0.00</td>
            </tr>


            </tbody>
        </table>


        <h4 style="float: right"><b>Sub Total</b> $0.00</h4>

        <table class="table table-bordered">
            <caption style="padding-bottom: 0px; border-top:1px solid #8080806b ">Section C - Heavy duty machines including isolator and connection </caption>

            <tbody>
            <tr>
                <td width="25%">15 amp TPNE 415V - 50Hz</td>
                <td width="25%">$847.00</td>
                <td width="25%"><input type="text" name="item_44" id="item_44"></td>
                <td width="25%">$0.00</td>
            </tr>
            <tr>
                <td>15 amp TPNE 415V - 50Hz</td>
                <td>$1,000.00</td>
                <td><input type="text" name="item_45" id="item_45"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>32 amp SPNE 230V - 50Hz</td>
                <td>$935.00</td>
                <td><input type="text" name="item_46" id="item_46"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>32 amp TPNE 415V - 50Hz</td>
                <td>$1,782.00</td>
                <td><input type="text" name="item_47" id="item_47"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>63 amp TPNE 230V - 50Hz</td>
                <td>$2,145.00</td>
                <td><input type="text" name="item_48" id="item_48"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>15 amp TPNE 415V - 50Hz</td>
                <td>$2,623.00</td>
                <td><input type="text" name="item_49" id="item_49"></td>
                <td>$0.00</td>
            </tr>

            </tbody>
        </table>


        <h4 style="float: right"><b>Sub Total</b> $0.00</h4>



        <table class="table table-bordered">
            <caption style="padding-bottom: 0px; border-top:1px solid #8080806b ">Section D - 24 hour supply (Outdoor Bare Space Exhibitors) </caption>

            <tbody>
            <tr>
                <td width="25%">10 amp SPNE 230V - 50Hz</td>
                <td width="25%">$275.00</td>
                <td width="25%"><input type="text" name="item_50" id="item_50"></td>
                <td width="25%">$0.00</td>
            </tr>
            <tr>
                <td>15 amp SPNE 230V - 50Hz</td>
                <td>$748.00</td>
                <td><input type="text" name="item_51" id="item_51"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>15 amp TPNE 415V - 50Hz</td>
                <td>$2,060.00</td>
                <td><input type="text" name="item_52" id="item_52"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>32 amp SPNE 230V - 50Hz</td>
                <td>$1,100.00</td>
                <td><input type="text" name="item_53" id="item_53"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>32 amp TPNE 230V - 50Hz</td>
                <td>$2,255.00</td>
                <td><input type="text" name="item_54" id="item_54"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>63 amp SPNE 230V - 50Hz</td>
                <td>$2,420.00</td>
                <td><input type="text" name="item_56" id="item_55"></td>
                <td>$0.00</td>
            </tr>
            <tr>
                <td>63 amp TPNE 415V - 50Hz</td>
                <td>$2,888.00</td>
                <td><input type="text" name="item_57" id="item_56"></td>
                <td>$0.00</td>
            </tr>

            </tbody>
        </table>


        <h4 style="float: right"><b>Sub Total</b> $0.00<br/><b>Grand Total</b> $0.00</h4>

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
			'urlValidator': "<?php echo base_url("inventory/order_forms/form_14_validate/doError"); ?>",
			'loadingImg':   "<?php echo base_url("../assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});


    $('#company_fax_no').inputmask("999-9999999")
    $('#company_cell_no').inputmask("9999-9999999")
</script>
</body>
</html>
