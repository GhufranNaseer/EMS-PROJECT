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
            <li class="active"><i class="fa fa-shopping-cart"></i> Additional Items</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!--<div class="ecommerce-nav">
            <ul>
				<?php
/*				$categories = $this->db
					->where('parent_id', null)
					->where('is_deleted', 0)
					->limit(7)
					->get('es_inventory_category')
					->result();

				foreach ($categories as $category) {
					echo '<li><a href="'. base_url('shop/category/?cat='. myid($category->id) ) .'">'.$category->category_title.'</a></li>';
				}
				*/?>
                <li class='lamp'><span></span></li>
            </ul>
        </div>-->


        <div class="box">
            <div class="box-body">
                <h3 align="center"><b>Checkout</b></h3><br>
                <div class="col-sm-12 col-md-12">
                    <form action="order/place" id="placeorder" method="post">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th></th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Sub Total</th>

                                <th> </th>
                            </tr>
                            </thead>
                            <tbody>

							<?php foreach($this->cart->contents() as $items){ ?>
                                <tr>
                                    <td class="col-sm-7 col-md-5">
                                        <div class="media">
                                            <a class="thumbnail pull-left">
                                                <img class="media-object" src="<?php echo base_url();?>../<?php echo $items['image'];?>" style="width: 35px; height: 35px;">
                                            </a>
                                            <div class="media-body">
                                                <h4 class="media-heading"><b><?php echo $items['name'];?></b></h4>
                                                <span>Status: </span><span class="text-success"><strong>In Stock</strong></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="col-sm-1 col-md-1" style="text-align: center">
										<?php echo $items['qty'];?>
                                    </td>
                                    <td>   </td>
                                    <td class="col-sm-1 col-md-2 text-center"><strong><?php echo $items['price'];?> <?= strtoupper($this->booking->booking_price_type) ?></strong></td>

                                    <td class="col-sm-2 col-md-3 text-center">
										<?php echo $items['subtotal'];?> <?= strtoupper($this->booking->booking_price_type) ?>
                                    </td>

                                </tr>

							<?php } ?>

                            <tr>
                                <td>   </td>
                                <td>   </td>
                                <td>   </td>
                                <td><h5>Total Items</h5></td>
                                <td class="text-center"><h5><strong><?php echo count($this->cart->contents());?></strong></h5></td>
                            </tr>

                            <tr>
                                <td>   </td>
                                <td>   </td>
                                <td>   </td>
                                <td><h3>Total Amount</h3></td>
                                <td class="text-right"><h3><strong><?php echo $this->cart->format_number($this->cart->total()); ?> <?= strtoupper($this->booking->booking_price_type) ?></strong></h3></td>
                            </tr>
                            <tr>
                                <td>   </td>
                                <td>   </td>
                                <td>   </td>
                                <td>
                                    <a href="<?php echo base_url();?>view/cart"><button type="button" class="btn btn-default pull-right">
                                            <span class="glyphicon glyphicon-shopping-cart"></span> Edit Order
                                        </button></a>
                                </td>
                                <td>

                                    <a href="<?php echo base_url();?>order/place"><button type="button"  onclick="return confirm('You Want to place order?');" class="btn btn-success pull-right">
                                            Place Order <span class="glyphicon glyphicon-play"></span>
                                        </button></a>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
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