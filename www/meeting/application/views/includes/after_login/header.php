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

                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">

                        <a href="#" class="js-menu_sign_out" >
                            <img src="<?= base_url('../uploads/profile/default.png') ?>" class="user-image" alt="User Image">
                            <span class="hidden-xs"><?= "{$this->userdata->contact_person}"?></span>
                        </a>

                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="<?= base_url('../uploads/profile/default.png')?>" class="img-circle" alt="User Image">
                                <p>
									<?= "{$this->userdata->contact_person}"?>
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