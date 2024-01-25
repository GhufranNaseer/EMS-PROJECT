<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<div class="content-wrapper" data-page="enhanced">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Enhanced Order Details
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
                        <h3 class="box-title">Enhanced Order Details</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <!-- START CUSTOMER INFORMAION -->
						<?php if($order_views){?>
                            <div class="row">
                                <div class="col-xs-6">
                                    <table class="table table-sm table-bordered">

                                        <tbody>
                                        <tr>
                                            <td><b>Order #</b></td>
                                            <td><?php echo $order_views[0]['order_id'] ?></td>
                                        </tr>
                                        <tr>

                                        <tr>
                                            <td><b>Order Date</b></td>
                                            <td><?php echo $order_views[0]['created_on'] ?></td>
                                        </tr>
                                        <tr>


                                            <td><b>Customer Name</b></td>
                                            <td colspan="3"><?php echo $order_views[0]['name'] ?></td>

                                        </tr>
                                        <tr>
                                            <td><b>Customer Email</b></td>
                                            <td colspan="3"><?php echo $order_views[0]['email'] ?></td>
                                        </tr>

                                        <tr>
                                            <td><b>Customer Phone</b></td>
                                            <td colspan="3"><?php echo $order_views[0]['phone'] ?></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>


                                <div class="col-xs-6">
                                    <table class="table table-sm table-bordered">

                                        <tbody>
                                        <tr>
                                            <td><b>Company</b></td>
                                            <td><?php echo $order_views[0]['company'] ?></td>
                                        </tr>
                                        <tr>

                                            <td><b>City</b></td>
                                            <td colspan="3"><?php echo $order_views[0]['city'] ?></td>

                                        </tr>
                                        <tr>
                                            <td><b>Address</b></td>
                                            <td colspan="4"><?php echo $order_views[0]['address'] ?></td>
                                        </tr>

                                        <tr>
                                            <td><b>Website</b></td>
                                            <td colspan="3"><?php echo $order_views[0]['url'] ?></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- END CUSTOMER INFORMAION -->

                            </div>




                            <div class="row">
                                <div class="col-sm-12 col-md-12">

                                    <form action="enhanced-order-approve.html" id="placeorder" method="post">

                                        <table class="table table-sm table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th></th>
                                                <th class="text-center">Price</th>
                                                <th class="text-center">Sub Total</th>
                                            </tr>
                                            </thead>
                                            <tbody>

											<?php

											foreach($order_views as $order_view){?>


                                                <tr>
                                                    <td class="col-sm-7 col-md-5">
                                                        <div class="media">
                                                            <a class="thumbnail pull-left" style="border:0px; margin-bottom:0px;">
                                                                <img class="media-object" src="<?php echo base_url();?><?php echo $order_view['item_image'];?>" style="width: 35px; height: 35px;"> </a>
                                                            <div class="media-body">
                                                                <p></p>
                                                                <h4 class="media-heading"><b><?php echo $order_view['item_title'];?></b></h4>
                                                            </div>
                                                        </div></td>
                                                    <td class="col-sm-1 col-md-1" style="text-align: center"><?php echo $order_view['item_quantity'];?></td>
                                                    <td> &nbsp; </td>
                                                    <td class="col-sm-1 col-md-2 text-center"><strong><?= ($booking->booking_price_type == 'PKR') ? $order_view['item_price_pkr'] : $order_view['item_price_usd'];?> <?= $booking->booking_price_type ?></strong></td>

                                                    <td class="col-sm-2 col-md-3 text-center"><?php echo $order_view['item_price'];?> <?= $booking->booking_price_type ?></td>

                                                </tr>

											<?php } ?>

                                            <tr>
                                                <td> &nbsp; </td>
                                                <td> &nbsp; </td>
                                                <td> &nbsp; </td>
                                                <td><h4>Total Items</h4></td>
                                                <td class="text-center"><h4><strong><?php echo $order_views[0]['total_items'];?></strong></h4></td>
                                            </tr>

                                            <tr>
                                                <td> &nbsp; </td>
                                                <td> &nbsp; </td>
                                                <td> &nbsp; </td>
                                                <td><h4>Total Amount</h4></td>
                                                <td class="text-right"><h3 align="center"><strong><?php echo $order_views[0]['total_amount'];?> <?= $booking->booking_price_type ?></strong></h3></td>
                                            </tr>
                                            <tr>
                                                <td> &nbsp; </td>
                                                <td> &nbsp; </td>
                                                <td> &nbsp; </td>
                                                <td>
                                                </td>
                                                <td></td>


                                            </tr>


                                            </tbody>
                                        </table>

										<?php
										$order_status = $this->db
											->where('id',$order_views[0]['order_id'])
											->get('es_exhibition_order')
											->result_array();
										?>

                                        <input type="hidden" name="order_details" value="<?php echo htmlspecialchars(json_encode($order_views));?>">

										<?php if($order_status[0]['is_canceled']==0 && $order_status[0]['is_approved']==0){ ?>

                                            <a href="#"><button type="submit" onclick="return confirm('Are you sure you want to Approve order?');" class="btn btn-success  btn-lg pull-right">
                                                    Approve Order
                                                </button></a>

										<?php } ?>

                                    </form>

                                    <form action="enhanced-order-cancel.html" method="post">

										<?php if($order_status[0]['is_canceled']==0 && $order_status[0]['is_approved']==0){ ?>	&nbsp; &nbsp;
                                            <a href="#"><button type="submit" onclick="return confirm('Are you sure you want to cancel order?');" class="btn btn-danger btn-lg btn_cancel_order btn-lg pull-right" style="margin-left: 10px; margin-right:10px;">Cancel Order
                                                </button></a>
                                            <input type="hidden" name="order_no" value="<?php echo urlencode(myid($order_views[0]['order_id']));?>">
                                            <input type="hidden" name="exhibition_id" value="<?php echo urlencode(myid($order_views[0]['exhibition_id']));?>">
										<?php } ?>
                                    </form>




                                </div>

                            </div>

							<?php
						} // End if condition data display or not
						else{
							echo"<h3 align='center'><b>"."Order Detail Not Found"."</b></h3>";
						}
						?>


                    </div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="box test_box" style="display: none;">
                    <div class="box-header">
                        <h3 class="box-title"></h3>
                    </div><!-- /.box-header -->


                </div>

            </div>
        </div>
    </section>

</div>

<?php $this->load->view('includes/after_login/footer'); ?>



</body>
</html>
