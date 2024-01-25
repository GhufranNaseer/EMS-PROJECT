<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<style>
    <?php if (!is_null($this->event->event_background) && $this->event->event_background != '') { ?>
    .bg-black-gradient {
        background-image: url("<?= base_url('../' . $this->event->event_background) ?>") !important;
        background-repeat: no-repeat;
        background-position: center center;
        background-attachment: fixed;
        -webkit-background-size: cover;
    }
    <?php } ?>
    .widget-user .widget-user-image>img {
        height: 90px;
        border: 0;
    }
    .small-box {
        min-height: 120px;
    }
    .small-box h4 {
        margin: 0;
    }
    .download_btns {
        width: 70%;
        margin: 0 auto;
    }
    .box-header .box-title {
        font-weight: bold;
    }

    .notification_button {
        position: relative;
    }
    .notification_button p {
        font-size: 12px;
        margin: 0;
        visibility: hidden;
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
    }
    .notification_button:hover p{
        visibility: visible;
    }
</style>
<div class="content-wrapper" data-page="dashboard">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Dashboard</h1>
        <ol class="breadcrumb">
            <li class="active"><i class="fa fa-dashboard"></i> Dashboard</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header bg-black-gradient">


                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<?php $this->load->view('includes/after_login/footer'); ?>

<script>



</script>

</body>
</html>