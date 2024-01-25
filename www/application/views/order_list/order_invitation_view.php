<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="order_list">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Order Invitation
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">List</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Select Order Invitation</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('send-order-invitation-submit.html') ?>?id=<?= $this->input->get('id') ?>" method="post" id="crd_form">

                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer Name</th>
                                    <th>Customer Email</th>
                                    <th>Booked By</th>
                                    <th>Booking Date</th>
                                    <th>Email Status</th>
                                    <th>Select </th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                $bookings = $this->db
                                    ->select('B.id,
                                        B.exhibition_id,
                                        C.company,
                                        C.email as company_email,
                                        CONCAT(U.user_first_name, " ", U.user_last_name) as booked_by,
                                        B.booking_date,
                                         
                                        IF(B.is_approved = 1, "<span class=\'label label-success\'>Approved</span>",
                                        IF(B.booking_type = "confirmed", "<span class=\'label label-success\'>Confirm Sale</span>",
                                        IF(B.booking_type = "tentative", "<span class=\'label label-warning\'>Tentative</span>", "N/A"))) as booking_type,
                                        
                                        IF(B.invitation_sent = 1, "<span class=\'label label-success\'>Sent</span>", 
                                        "<span class=\'label label-danger\'>Not Sent</span>") as invitation_sent_text,
                                        ')
                                    ->where('B.exhibition_id', $this->formdata->id)
                                    ->where('B.is_canceled', 0)
                                    ->where('B.is_approved', 1)
                                    ->join('es_customers as C', 'B.customer_id = C.id', 'LEFT')
                                    ->join('users as U', 'B.booked_by = U.id', 'LEFT')
                                    ->get('es_exhibition_booking as B')
                                    ->result();

                                foreach ($bookings as $booking) {
                                    echo '<tr>
                                        <td>'. $booking->id .'</td>
                                        <td>'. $booking->company .'</td>
                                        <td>'. $booking->company_email .'</td>
                                        <td>'. $booking->booked_by .'</td>
                                        <td>'. $booking->booking_date .'</td>
                                        <td>'. $booking->invitation_sent_text .'</td>
                                        <td class="text-center"><input type="checkbox" class="icheck" name="invitation[]" value="'. $booking->id .'"></td>
                                        </tr>';
                                }

                                ?>
                                </tbody>
                            </table>

                            <div class="js-msgbox"></div>

                            <div class="text-right">
                                <a href="javascript:void(0);"
                                   class="btn btn-success btn-lg margin-bottom js-form_btn">Send Invitations</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>

<?php $this->load->view('includes/after_login/footer'); ?>

<script type="text/javascript">
	doFormValidation({
		'form':         '#crd_form',
		'msgbox':       '#crd_form .js-msgbox',
		'btnClick':     '#crd_form .js-form_btn',
		'urlValidator': "<?php echo base_url("send-order-invitation-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
		'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
	});
</script>


</body>
</html>
