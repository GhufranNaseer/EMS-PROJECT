<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<div class="content-wrapper" data-page="shop">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Additional Items</h1>
        <ol class="breadcrumb">
            <li class="active"><i class="fa fa-shopping-cart"></i> Additional Items</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="box">
            <div class="box-body">
                <h3 align="center"><b>View Cart</b></h3><br>
                <div class="col-sm-12 col-md-12">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th></th>
                            <th class="text-center">Price</th>

                            <th> </th>
                        </tr>
                        </thead>
                        <tbody>

						<?php foreach($this->cart->contents() as $items){ ?>

                            <tr>
                                <td class="col-sm-7 col-md-5">
                                    <div class="media">
                                        <a class="thumbnail pull-left" href="#">
                                            <img class="media-object" src="<?php echo base_url();?>../<?php echo $items['image'];?>" style="width: 72px; height: 72px;"> </a>
                                        <div class="media-body">
                                            <h4 class="media-heading"><a href="#"><?php echo $items['name'];?></a></h4>
                                            <span>Status: </span><span class="text-success"><strong>In Stock</strong></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="col-sm-1 col-md-1" style="text-align: center">
                                    <input type="number" class="form-control" id="qty" value="<?php echo $items['qty'];?>">
                                </td>
                                <td>   </td>
                                <td class="col-sm-1 col-md-1 text-center"><strong><?php echo $items['price'];?> <?= strtoupper($this->booking->booking_price_type) ?></strong></td>


                                <td class="col-sm-2 col-md-3 ">

                                    <button type="button" class="btn btn-success btn-sm update_button" data-id="<?php echo $items['rowid']; ?>">
                                        <span class="fa fa-edit"></span> Update
                                    </button>

                                    <button type="button" class="btn btn-danger btn-sm delete_button" data-id="<?php echo $items['rowid']; ?>">
                                        <span class="glyphicon glyphicon-remove"></span> Remove
                                    </button>
                                </td>


                            </tr>

						<?php } ?>

                        <tr>
                            <td>   </td>
                            <td>   </td>
                            <td>   </td>
                            <td><h5>Subtotal</h5></td>
                            <td class="text-right"><h5><strong><?php echo $this->cart->format_number($this->cart->total()); ?> <?= strtoupper($this->booking->booking_price_type) ?></strong></h5></td>
                        </tr>




                        <tr>
                            <td colspan="2" rowspan="2">
                                <div class="well well-sm" style="height: 150px; overflow: auto; max-width: 600px">
                                    <strong>Term & Condition:</strong><br>
									<?php
									$note = $this->db
										->where('exhibition_id', $this->event->id)
										->where('type', 'additional_items_terms')
										->order_by('id', 'DESC')
										->get('es_exhibition_notification')
										->row();

									if ($note) {
										echo $note->title;
									}
									?>
                                </div>
                            </td>
                            <td>   </td>
                            <td><h3>Total</h3></td>
                            <td class="text-right"><h3><strong><?php echo $this->cart->format_number($this->cart->total()); ?> <?= strtoupper($this->booking->booking_price_type) ?></strong></h3></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="2">
                                <div class="term-conditions">
                                    <p style="font-weight: bold; text-decoration: underline;">Please select the mode of payment</p>
                                    <div class="row">
                                        <div class="col-md-3" style="border-right: 1px solid #040406">
                                            <label>
                                                <p style="font-size: 11px; font-weight: bold;">Cash Payment</p>
                                                <img class="media-object" src="<?php echo base_url();?>../assets/img/abbas-1.png" style="width: 72px; height: 72px;">
                                                <small class="text-red" style="float: right; margin-top: 19px;">Only for local Exhibitors
                                                    <input type="radio" name="payment_method" style="float: right;">
                                                </small>
                                            </label>
                                        </div>
                                        <div class="col-md-3" style="border-right: 1px solid #040406">
                                            <label>
                                            <p style="font-size: 11px; font-weight: bold;">Bank To Bank</p>
                                            <img class="media-object" src="<?php echo base_url();?>../assets/img/abbas-2.png" style="width: 72px; height: 72px;">
                                            <input type="radio" name="payment_method" style="float: right;     margin-top: 19px;">
                                            </label>
                                        </div>
                                        <!--<div class="col-md-3" style="border-right: 1px solid #040406">
                                            <label>
                                            <p style="font-size: 11px; font-weight: bold;">Check Or Payorder</p>
                                            <img class="media-object" src="<?php /*echo base_url();*/?>../assets/img/abbas-3.png" style="width: 72px; height: 72px;">
                                            <input type="radio" name="payment_method" style="float: right">
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>
                                            <p style="font-size: 11px; font-weight: bold;">Debit Or Credit</p>
                                            <img class="media-object" src="<?php /*echo base_url();*/?>../assets/img/abbas-4.png" style="width: 72px; height: 72px;">
                                            <input type="radio" name="payment_method" style="float: right;     margin-top: 19px;">
                                            </label>
                                        </div>-->
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>   </td>
                            <td>   </td>
                            <td>   </td>
                            <td>
                                <a href="<?= base_url('dashboard') ?>" class="btn btn-default">
                                    <span class="glyphicon glyphicon-shopping-cart"></span> Continue Shopping
                                </a>
                            </td>
                            <td class="text-right">
                                <a href="<?php echo base_url();?>order/place" onclick="return confirm('You Want to place order?');" class="btn btn-success pull-right">
                                    Place Order <span class="glyphicon glyphicon-play"></span>
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </section>
</div>

<?php $this->load->view('includes/after_login/footer'); ?>

<script>

	$(document).ready(function() {

		$('.delete_button').click(function(){
			var id = $(this).attr('data-id');

			$.ajax({
				type:'POST',
				url:"<?PHP echo base_url('delete/cart'); ?>",
				data: {rowid:id},
				success: function (data) {
					location.reload();
				},
				error:   function () {
				}
			});
		});
		$(document).on('click', '.update_button', function (e) {
			e.stopImmediatePropagation();

			var update_id = $(this).attr('data-id');
			var qty_val = $(this).parents('tr').find("#qty").val();

			$(this).a

			$.ajax({
				type:    'post',
				url:     '<?PHP echo base_url('update/cart'); ?>',
				data:    {rowid:update_id,qty_value:qty_val},
				success: function (data) {
					location.reload();
				},
				error:   function () {
				}
			});
		});




	});












</script>



<script>
	$(function(){
		if($('.ecommerce-nav>ul>li').hasClass('selected')){
			$('.ecommerce-nav .selected').addClass('active');
			var currentleft=$('.ecommerce-nav .selected').position().left+"px";
			var currentwidth=$('.ecommerce-nav .selected').css('width');
			$('.ecommerce-nav .lamp').css({"left":currentleft,"width":currentwidth});
		}
		else{
			$('.ecommerce-nav>ul>li').first().addClass('active');
			var currentleft=$('.ecommerce-nav .active').position().left+"px";
			var currentwidth=$('.ecommerce-nav .active').css('width');
			$('.ecommerce-nav .lamp').css({"left":currentleft,"width":currentwidth});
		}
		$('.ecommerce-nav>ul>li').hover(function(){
			$('.ecommerce-nav ul li').removeClass('active');
			$(this).addClass('active');
			var currentleft=$('.ecommerce-nav .active').position().left+"px";
			var currentwidth=$('.ecommerce-nav .active').css('width');
			$('.ecommerce-nav .lamp').css({"left":currentleft,"width":currentwidth});
		},function(){
			if($('.ecommerce-nav>ul>li').hasClass('selected')){
				$('.ecommerce-nav .selected').addClass('active');
				var currentleft=$('.ecommerce-nav .selected').position().left+"px";
				var currentwidth=$('.ecommerce-nav .selected').css('width');
				$('.ecommerce-nav .lamp').css({"left":currentleft,"width":currentwidth});
			}
			else{
				$('.ecommerce-nav>ul>li').first().addClass('active');
				var currentleft=$('.ecommerce-nav .active').position().left+"px";
				var currentwidth=$('.ecommerce-nav .active').css('width');
				$('.ecommerce-nav .lamp').css({"left":currentleft,"width":currentwidth});
			}
		});
	});
</script>

</body>
</html>