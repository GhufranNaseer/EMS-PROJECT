<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


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
            <div class="col-sm-4">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3>7</h3>
                        <p>Future Exhibition</p>
                    </div>
                    <div class="icon"><i class="fa fa-plane"></i></div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>20</h3>
                        <p>Total Exhibition</p>
                    </div>
                    <div class="icon"><i class="fa fa-suitcase"></i></div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>26</h3>
                        <p>Total Stalls</p>
                    </div>
                    <div class="icon"><i class="fa fa-bookmark"></i></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4">
                <div class="small-box bg-olive">
                    <div class="inner">
                        <h3>12</h3>
                        <p>Payment Request</p>
                    </div>
                    <div class="icon"><i class="fa fa-usd"></i></div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="small-box bg-light-blue-gradient">
                    <div class="inner">
                        <h3>23</h3>
                        <p>Total Payment</p>
                    </div>
                    <div class="icon"><i class="fa fa-usd"></i></div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="small-box bg-teal">
                    <div class="inner">
                        <h3>80</h3>
                        <p>Total Inventory</p>
                    </div>
                    <div class="icon"><i class="fa fa-cubes"></i></div>
                </div>
            </div>
        </div>

        <!-- BAR CHART -->
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Event Stalls</h3>
            </div>
            <div class="box-body">
                <div id="bar-example"></div>
            </div>
        </div>
        <!-- /.box -->
        <div class="row">
            <div class="col-sm-6">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Organizer Events</h3>
                    </div>
                    <div class="box-body">
                        <div id="required_organizer"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Sales Person's Bookings</h3>
                    </div>
                    <div class="box-body">
                        <table width="100%" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th width="5%">#</th>
                                <th>Agent Name</th>
                                <th>Booking</th>
                                <th>Stales</th>
                            </tr>
                            </thead>

                        </table>

                    </div>
                </div>
            </div>
        </div>

    </section>
</div>


<?php $this->load->view('includes/after_login/footer'); ?>

<link rel="stylesheet" href="<?= base_url('assets') ?>/morris/morris.css">
<script src="<?= base_url('assets') ?>/morris/raphael-min.js"></script>
<script src="<?= base_url('assets') ?>/morris/morris.min.js"></script>
<script type="text/javascript">
    Morris.Bar({
        element: 'bar-example',
        data: [
            <?php

            $exhibitions = $this->db
                ->select('exhibition_title,id')
                ->where('is_deleted', 0)
                ->get('es_exhibitions')
                ->result();

            foreach ($exhibitions as $exhibition) {

                $available = $this->db
                    ->where('exhibition_id', $exhibition->id)
                    ->where('is_booked', 0)
                    ->where('is_sold', 0)
                    ->count_all_results('es_exhibition_stalls');

                $tentative = $this->db
                    ->where('exhibition_id', $exhibition->id)
                    ->where('is_booked', 1)
                    ->where('is_sold', 0)
                    ->count_all_results('es_exhibition_stalls');

                $sold = $this->db
                    ->where('exhibition_id', $exhibition->id)
                    ->where('is_sold', 1)
                    ->count_all_results('es_exhibition_stalls');

                echo "{ y: '" . $exhibition->exhibition_title . "', a: '" . $available . "', b: '" . $tentative . "', c: '" . $sold . "' },";
            }

            ?>
        ],
        xkey: 'y',
        ykeys: ['a', 'b', 'c'],
        labels: ['Available', 'Tentative', 'Sold']
    });

    Morris.Bar({
        element: 'required_organizer',
        data: [
            <?php

            $organizers = $this->db
                ->select('organizer_company,id')
                ->get('es_organizer')
                ->result();

            foreach ($organizers as $organizer) {

                $available = $this->db
                    ->where('event_organizer', $organizer->id)
                    ->where('is_deleted', 0)
                    ->count_all_results('es_exhibitions');

                echo "{ y: '" . $organizer->organizer_company . "', a: " . $available . " },";
            }

            ?>
        ],
        xkey: 'y',
        ykeys: ['a'],
        lineColors: ['#fff'],
        labels: ['Available']
    });

    $(document).ready(function () {
        oTable = $('#crud-table').dataTable($.extend(datatable_settings, {
            "sAjaxSource": '<?php echo base_url('dashboard-salesperson-bookings.html'); ?>',
            aLengthMenu:       [
                [5,10,25],
                [5,10,25]
            ],
            iDisplayLength:    5,
        }));

    });

</script>

</body>
</html>