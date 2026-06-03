<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<div class="content-wrapper" data-page="badges_report">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Badges
            <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('organizer.html'); ?>"><i class="fa fa-dashboard"></i>Organizer List</a></li>
            <li class="active">Edit</li>
        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Badges Edit</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('badges-edit-submit.html') ?>?id=<?= $this->input->get('id') ?>"
                              method="post" id="crd_form" enctype="multipart/form-data">


                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" name="name"
                                               id="name"
                                               value="<?= html_escape($data->full_name) ?>"
                                               placeholder="Name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="designation">Designation</label>
                                        <input type="text" class="form-control" name="designation"
                                               id="designation"
                                               value="<?= html_escape($data->designation) ?>"
                                               placeholder="Designation">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="cnic">CNIC Number</label>
                                        <input type="text" class="form-control" name="cnic"
                                               value="<?= html_escape($data->cnic) ?>" id="cnic"
                                               placeholder="CNIC">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="passport">Passport Number</label>
                                        <input type="text" class="form-control" name="passport"
                                               value="<?= html_escape($data->passport) ?>"
                                               id="passport" placeholder="Passport">
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" name="email"
                                               value="<?= html_escape($data->email) ?>"
                                               id="email" placeholder="Email">

                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="mobile">Mobile</label>
                                        <input type="number" class="form-control" name="mobile"
                                               value="<?= html_escape($data->mobile) ?>"
                                               id="mobile" placeholder="Mobile">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <label>Nationality <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="nationality" id="nationality" list="country_list" value="<?= ($data->nationality) ?>">
                                    <datalist id="country_list">
                                        <?= get_instance()->funcs->print_input_data_list('country'); ?>
                                    </datalist>
                                </div>
                            </div>
                            <h3>Collection Person Details</h3>
                            <div class="row">

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="collection_person_name">Collection Person Name</label>
                                        <input type="text" class="form-control" name="collection_person_name"
                                               value="<?= html_escape($data->collection_person_name) ?>"
                                               id="collection_person_name" placeholder="Collection Person Name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="collection_person_cnic">Collection Person CNIC</label>
                                        <input type="number" class="form-control" name="collection_person_cnic"
                                               value="<?= html_escape($data->collection_person_cnic) ?>"
                                               id="collection_person_cnic" placeholder="Collection Person CNIC">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="collection_person_phone">Collection Person Phone</label>
                                        <input type="number" class="form-control" name="collection_person_phone"
                                               value="<?= html_escape($data->collection_person_phone) ?>"
                                               id="collection_person_phone" placeholder="Collection Person Phone">
                                    </div>
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
                    </div>



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
        var faqadd = {
            'form':         '#crd_form',
            'msgbox':       '#crd_form .js-msgbox',
            'btnClick':     '#crd_form .js-form_btn',
            'urlValidator': "<?php echo base_url("badges-edit-validation.html"); ?>?id=<?= $this->input->get('id') ?>",
            'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
        };
        doFormValidation(faqadd);
    });


</script>
</body>
</html>
