<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="form_11">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            FORM I-8
            <small></small>
        </h1>
        <h1 class="text-center">
            CAR STICKERS DISTRIBUTION POLICY :
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
                        <h4 class=" text-center" style="padding: 8px;"></h4>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('inventory/order_forms/form_11_submit') ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">

                            <input type="hidden" name="form_id"
                                   id="form_id" placeholder="form_id" value="11">
<p>Following is the distribution policy of car stickers, allocated as per your respective space category. The
    sticker(s) shell be provided to the authorized person of your company from the exhibition desk at expo
    centre.</p>
                            <table class="table table-bordered">

                                <tr>
                                    <th class="text-center" colspan="3">EXHIBITOR</th>
                                    <th class="text-center" colspan="3">PARKING STICKERS </th>
                                    <th class="text-center" >QTY.  </th>
                                </tr>
                                <tr>
                                    <th class="text-center">CATEGORY </th>
                                    <th class="text-center">TYPE</th>
                                    <th class="text-center">SIZE Sqm </th>
                                    <th class="text-center">RED</th>
                                    <th class="text-center">GREEN </th>
                                    <th class="text-center">BLUE </th>
                                    <th class="text-center">TOTAl</th>
                                </tr>
                                <tr>
                                    <td class="text-center">SHELL SPACE </td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center">0</td>
                                    <td class="text-center">0</td>
                                    <td class="text-center">2</td>
                                    <td class="text-center">2</td>
                                </tr>
                                <tr>
                                    <td class="text-center" rowspan="4">BARE SPACE  </td>
                                    <td class="text-center">A</td>
                                    <td class="text-center">24-36</td>
                                    <td class="text-center">0</td>
                                    <td class="text-center">1</td>
                                    <td class="text-center">2</td>
                                    <td class="text-center">3</td>
                                </tr>

                                <tr>
                                    <td class="text-center">B</td>
                                    <td class="text-center">42-60</td>
                                    <td class="text-center">0</td>
                                    <td class="text-center">2</td>
                                    <td class="text-center">4</td>
                                    <td class="text-center">6</td>
                                </tr>
                                <tr>
                                    <td class="text-center">C</td>
                                    <td class="text-center">72-100</td>
                                    <td class="text-center">1</td>
                                    <td class="text-center">3</td>
                                    <td class="text-center">6</td>
                                    <td class="text-center">10</td>
                                </tr>
                                <tr>
                                    <td class="text-center">D</td>
                                    <td class="text-center">100-Above</td>
                                    <td class="text-center">2</td>
                                    <td class="text-center">6</td>
                                    <td class="text-center">8</td>
                                    <td class="text-center">16</td>
                                </tr>
                            </table>

                             <p class="bg-danger js-msgbox"></p>

                            <div class="row">
                                <div class="col-xs-10">

                                </div>
                                <div class="col-xs-2">
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
			'urlValidator': "<?php echo base_url("inventory/order_forms/form_11_validate/doError"); ?>",
			'loadingImg':   "<?php echo base_url("../assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});


    $('#company_fax_no').inputmask("999-9999999")
    $('#company_cell_no').inputmask("9999-9999999")
</script>
</body>
</html>
