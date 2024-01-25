<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<style>
    .ecommerce-nav a{
        text-decoration:none;
    }
    .ecommerce-nav{
        height:70px;
        background:#222;

        margin-bottom: 20px;
        z-index: 9999;
    }
    .ecommerce-nav>ul{
        position:relative;
        list-style:none;
        padding:0;
        margin:0;
    }
    .ecommerce-nav>ul>li>ul{
        position:absolute;
        left:0;
        padding:0;
        margin:0;
        list-style:none;
    }
    .ecommerce-nav>ul>li:hover>ul li a{
        opacity:1;
        height:50px;
        transition:all .3s linear;
        -o-transition:all .3s linear;
        -moz-transition:all .3s linear;
        -webkit-transition:all .3s linear;
    }
    .ecommerce-nav>ul>li>ul a{
        display:block;
        color:#222;
        width:250px;
        line-height:50px !important;
        font:700 14px 'pt sans',sans-serif;
        background:#eee;
        border-bottom:1px solid #ddd;
        text-align:left;
        padding:0 10px;
        height:0;
        overflow:hidden;
        opacity:0;
        transition:all .3s linear .2s;
        -o-transition:all .3s linear .2s;
        -moz-transition:all .3s linear .2s;
        -webkit-transition:all .3s linear .2s;
    }
    .ecommerce-nav>ul>li{
        float:left;
        position:relative;
    }
    .ecommerce-nav>ul>li>a{
        padding:0 20px;
        color:#fff;
        display:block;
        line-height:70px !important;
        font:400 15px 'PT Sans', sans-serif;
        text-transform:uppercase;
        text-decoration:none;
    }
    .ecommerce-nav .lamp span{
        display:block;
        height:4px;
        background:#ee6666;
        position: relative;
    }
    .ecommerce-nav .lamp span:after {
        bottom: 100%;
        left: 50%;
        border: solid transparent;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
        border-color: rgba(238, 102, 102, 0);
        border-bottom-color: #ee6666;
        border-width: 4px;
        margin-left: -4px;
    }
    .ecommerce-nav .lamp{
        position:absolute !important;
        height:4px;
        top:66px;
        background:#333;
        transition:all .3s linear;
        -o-transition:all .3s linear;
        -moz-transition:all .3s linear;
        -webkit-transition:all .3s linear;
    }
    .ecommerce-nav .selected.active>a,.active>a{
        transition:all .3s linear;
        -o-transition:all .3s linear;
        -moz-transition:all .3s linear;
        -webkit-transition:all .3s linear;
        color:#fff;
    }

    .media-body{
        padding-left: 10px;
    }

</style>

<div class="content-wrapper" data-page="shop">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Additional Items</h1>
        <ol class="breadcrumb">
            <li class="active"><i class="fa fa-shopping-cart"></i> Additional Items </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">



        <div class="box">
            <div class="box-body">
                <h3><b>Product Details</b></h3><br>

				<?php foreach($product_details as $product_detail){?>
                    <form id="addcart" action="<?php echo base_url();?>add/cart" method="post">
                        <div class="row">
                            <div class="col-md-4 product_img">
                                <img src="<?php echo base_url('../');?><?php echo $product_detail['item_image'];?>" class="img-responsive img-thumbnail">
                            </div>
                            <div class="col-md-5 product_content">
                                <h2 style="margin-top: 0px"><?php echo $product_detail['item_title'];?></h2>

                                <div class="rating">
                                </div>
                                <p><?= $product_detail['item_description'];?></p>
                                <h3 class="cost">
                                    <?php
                                    if ($this->booking->booking_price_type == 'PKR') {
                                        echo $product_detail['item_price_pkr'] . ' ' . strtoupper($this->booking->booking_price_type);
                                    } else {
                                        echo $product_detail['item_price_usd'] . ' ' . strtoupper($this->booking->booking_price_type);
                                    }
                                    ?>
                                </h3>
                                <div class="row">

                                    <!-- end col -->

                                    <!-- end col -->
                                    <div class="col-md-4">
                                        <label><b>QTY</b></label>
                                        <input type="number" class="form-control" name="qty" min="1" value="1">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="btn-ground">
                                            <label>&nbsp;</label>
                                            <a href="#" onclick="$(this).closest('form').submit()">
                                                <button type="button" class="btn btn-secondary btn-block">
                                                    <span class="glyphicon glyphicon-shopping-cart"></span> Add To Cart
                                                </button>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- end col -->
                                </div>
                                <div class="space-ten"></div>
                                <!-- Start hidden -->
                                <input type="hidden" name="item_id" value="<?php echo $product_detail['id'];?>">
                                <input type="hidden" name="item_title" value="<?php echo $product_detail['item_title'];?>">
                                <input type="hidden" name="item_price" value="<?php
								if ($this->booking->booking_price_type == 'PKR') {
									echo $product_detail['item_price_pkr'];
								} else {
									echo $product_detail['item_price_usd'];
								}
								?>">
                                <input type="hidden" name="item_image" value="<?php echo $product_detail['item_image'];?>">
                                <!-- End Hidden -->
                                <br><br>
                            </div>
                            <div class="col-md-3">
                                <h4><b>Category:</b> <span> <a href=""><?php echo $product_detail['category_title'];?></a></span></h4>
                            </div>
                        </div>
                    </form>
				<?php } ?>

            </div>
        </div></section></div>


<?php $this->load->view('includes/after_login/footer'); ?>


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