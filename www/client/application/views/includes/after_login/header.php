<style type="text/css">
    .chosen-container-single .chosen-single span {
        padding-left: 10px;
    }
    ul.dropdown-cart li .item{
        display:block;
        padding:3px 10px;
        margin: 3px 0;
    }
    .cart{
        padding: 10px;
    }
    .dropdown-menu {
        position: absolute;
        right: 0;
        left: auto;
        width: 400px;
    }
    .price-text-color {
        color: #ee6666;
    }
</style>
<body class="sidebar-mini skin-black">
<div class="wrapper">
    <header class="main-header">
        <!-- Logo -->
        <a href="<?= base_url('dashboard');?>" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini">EMS</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><?= $this->event->exhibition_title ?></span>
        </a>

        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="navbar-custom-menu">

                <ul class="nav navbar-nav">

                    <li class="dropdown messages-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            Items
                            <i class="glyphicon glyphicon-shopping-cart"></i>
                            <span class="label label-danger"><?php echo count($this->cart->contents());?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">Add Product List</li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">

									<?php foreach($this->cart->contents() as $items){ ?>
                                        <li><!-- start message -->
                                            <a href="javascript:void(0)">
                                                <div class="pull-left">
                                                    <img src="<?php echo base_url();?>../<?php echo $items['image'];?>" height ="35px" class="img-circle" alt="User Image">
                                                </div>
                                                <h4>
                                                    <b><?php echo $items['name'];?> </b>
                                                    <small><span class="badge delete_form_cart_btn bg-red" data-id="<?php echo $items['rowid']; ?>">X</span></small>
                                                </h4>

                                                <p class="price-text-color"><b><?php echo $items['price']; ?> <?= strtoupper($this->booking->booking_price_type) ?></b></p>
                                                <small><b style="font-size: 13px; color:#333;">Qty: <?php echo $items['qty']; ?></b></small>
                                            </a>
                                            <input type="hidden" id="rowid" value="<?php echo $items['rowid']; ?>">
                                        </li>

									<?php } ?>

                                </ul>
                            </li>
                            <li>
                                <a class="text-center">
                                    <b>Total Price :</b> <?php echo $this->cart->format_number($this->cart->total()); ?> <?= strtoupper($this->booking->booking_price_type) ?>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <p align="center">
                                <a href="<?php echo base_url();?>view/cart">
                                    <button type="button" class="btn btn-secondary btn-sm">
                                        <span class="glyphicon glyphicon-shopping-cart"></span> View Cart
                                    </button>
                                </a>
                            </p>
                        </ul>
                    </li>





                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">

                        <a href="#" class="js-menu_sign_out" >
                            <img src="<?= base_url('../uploads/profile/default.png') ?>" class="user-image" alt="User Image">
                            <span class="hidden-xs"><?= "{$this->userdata->company}"?></span>
                        </a>

                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="<?= base_url('../uploads/profile/default.png')?>" class="img-circle" alt="User Image">
                                <p>
									<?= "{$this->userdata->company}"?>
                                    <small></small>
                                </p>
                            </li>
                            <!-- Menu Body -->

                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="<?= base_url('my-profile'); ?>" class="btn btn-default btn-flat">Profile</a>
                                </div>
                                <div class="pull-right">
                                    <a href="<?= base_url ('log-out')?>" class="btn btn-default btn-flat">Logout</a>
                                </div>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>
        </nav>
    </header>