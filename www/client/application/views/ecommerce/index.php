<?php $this->load->view('includes/after_login/header'); ?>

<?php $this->load->view('includes/after_login/sidebar'); ?>
<style>
    .ecommerce-nav a{        text-decoration:none;    }
    .ecommerce-nav{        height:70px;        background:#222;        margin-bottom: 20px;        z-index: 9999;    }
    .ecommerce-nav>ul{        position:relative;        list-style:none;        padding:0;        margin:0;    }
    .ecommerce-nav>ul>li>ul{        position:absolute;        left:0;        padding:0;        margin:0;        list-style:none;    }
    .ecommerce-nav>ul>li:hover>ul li a{        opacity:1;        height:50px;        transition:all .3s linear;        -o-transition:all .3s linear;        -moz-transition:all .3s linear;        -webkit-transition:all .3s linear;    }
    .ecommerce-nav>ul>li>ul a{
        display:block;        color:#222;
        width:250px;        line-height:50px !important;
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
        position:relative;    }
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
        position: relative;    }
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
        margin-left: -4px;    }
    .ecommerce-nav .lamp{
        position:absolute !important;
        height:4px;
        top:66px;
        background:#333;
        transition:all .3s linear;
        -o-transition:all .3s linear;
        -moz-transition:all .3s linear;
        -webkit-transition:all .3s linear;    }
    .ecommerce-nav .selected.active>a,.active>a{
        transition:all .3s linear;
        -o-transition:all .3s linear;
        -moz-transition:all .3s linear;
        -webkit-transition:all .3s linear;
        color:#fff;    }
    .col-item    {
        border: 1px solid #E1E1E1;
        border-radius: 5px;
        background: #FFF;
        margin-top: 5%;    }
    .col-item a    {
        color: #333;
        font-weight: bold;    }
    .col-item .photo img    {
        margin: 0 auto;
        width: 100%;    }
    .col-item .info    {
        padding: 10px;
        border-radius: 0 0 5px 5px;
        margin-top: 1px;    }
    .col-item:hover .info {
        background-color: #f2f2f2;    }
    .col-item .price    {        /*width: 50%;*/
        float: left;
        margin-top: 5px;    }
    .col-item .price h5    {
        line-height: 24px;
        margin: 0;
        font-weight: bold;
        font-size: 16px;    }
    .price-text-color    {
        color: #ee6666;    }
    .col-item .info .rating    {
        color: #333;    }
    .col-item .rating    {
        /*width: 50%;*/
        float: left;
        font-size: 17px;
        text-align: right;
        line-height: 52px;
        margin-bottom: 10px;
        height: 52px;    }
    .col-item .separator    {
        border-top: 1px solid #E1E1E1;    }
    .clear-left    {
        clear: left;    }
    .col-item .separator p    {
        line-height: 20px;
        margin-bottom: 0;
        margin-top: 10px;
        text-align: center;    }
    .col-item .separator p i    {
        margin-right: 5px;    }
    .col-item .btn-add    {
        width: 50%;
        float: left;    }
    .col-item .btn-add    {
        border-right: 1px solid #E1E1E1;    }
    .col-item .btn-details    {
        width: 50%;
        float: left;
        padding-left: 10px;    }
    .controls    {
        margin-top: 20px;    }
</style>
<div class="content-wrapper" data-page="shop">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Ecommerce</h1>
        <ol class="breadcrumb">
            <li class="active"><i class="fa fa-shopping-cart"></i> Ecommerce</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="ecommerce-nav">
            <ul>
                <?php
                $categories = $this->db
                    ->where('parent_id', null)
                    ->where('is_deleted', 0)
                    ->limit(6)
                    ->get('es_inventory_category')
                    ->result();
                foreach ($categories as $category) {
                    echo '<li><a href="'. base_url('shop/category/?cat='. myid($category->id) ) .'">'.$category->category_title.'</a></li>';
                }				?>
                <li class='lamp'><span></span></li>
            </ul>
        </div>
        <div class="box">
            <div class="box-body" style="display: inline;">
                <h4><b>Top Products</b></h4>
                <?php
                $top_products = $this->db
                    ->where('exhibition_id',$this->event->id)
                    ->order_by('id','DESC')
                    ->limit(10)
                    ->get('es_inventory_item')
                    ->result_array();
                ?>
                <?php
                foreach($top_products as $i => $top_product){?>
                    <div class="col-sm-3">
                        <form id="addcart<?= $i?>" action="<?php echo base_url();?>add/cart" method="post">
                            <div class="col-item">
                                <div class="photo" style="height: 180px;">
                                    <img src="<?php echo base_url();?>../<?php echo $top_product['item_image'];?>" class="img-responsive" style="width: auto; height: 100px; margin-top: 25px;" alt="a">
                                </div>
                                <div class="info">
                                    <div class="row">
                                        <div class="price col-md-12">
                                            <h5 align="center" style=" white-space: nowrap;  width: 203px; overflow: hidden; text-overflow: ellipsis; ">
                                                <?php echo $top_product['item_title'];?></h5>
                                            <h5 class="price-text-color" align="center" style="white-space: nowrap; text-overflow: ellipsis;">
                                                Rs.<?php echo $top_product['item_price_pkr'];?></h5>
                                        </div>
                                    </div>
                                    <div class="separator clear-left">
                                        <input type="hidden" name="item_id" value="<?php echo $top_product['id'];?>">
                                        <input type="hidden" name="item_title" value="<?php echo $top_product['item_title'];?>">
                                        <input type="hidden" name="item_price" value="<?php echo $top_product['item_price_pkr'];?>">
                                        <input type="hidden" name="item_image" value="<?php echo $top_product['item_image'];?>">
                                        <p class="btn-add">
                                            <i class="fa fa-shopping-cart"></i>
                                            <a href="javascript:void(0)" onclick="$(this).closest('form').submit()" class="hidden-sm">Add to cart</a>
                                        </p>
                                        <p class="btn-details">
                                            <i class="fa fa-list"></i>
                                            <a href="<?php echo base_url();?>product/details?pid=<?php echo myid($top_product['id']);?>" class="hidden-sm">Details</a>
                                        </p>
                                    </div>
                                    <div class="clearfix">
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                <?php } ?>
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
        }		else{
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
            },
            function(){
            if($('.ecommerce-nav>ul>li').hasClass('selected'))
            {
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
            }		});	});</script>
</body>
</html>