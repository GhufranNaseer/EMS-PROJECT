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
                        <h3 class="text-center">Welcome to <?= $this->event->exhibition_title ?></h3>
                        <h5 class="text-right">By <?= $organizer->organizer_company; ?></h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-9">
                <div class="box">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-4 border-right">
                                <div class="small-box bg-purple-active">
                                    <div class="inner">
                                        <h4 style="margin-bottom: 8px;">EVENT DATE</h4>
                                        <div><?= (isset($event_date[0]) && !empty($event_date[0]->date)) ? date('d F Y', strtotime($event_date[0]->date)) : '-' ?></div>
                                        <div style="margin: 5px 30px">To</div>
                                        <div><?= (!empty($event_date) && !empty(end($event_date)->date)) ? date('d F Y', strtotime(end($event_date)->date)) : '-' ?></div>
                                    </div>
                                    <div class="icon"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 border-right">
                                <div class="small-box bg-maroon-active">
                                    <div class="inner">
                                        <h4 style="margin-bottom: 8px;"><?= strtoupper('Hall') ?></h4>
                                        <div>Stalls: <?= count($stalls) ?></div>
                                        <div style="margin: 5px 0">Stalls size in SQM: <?= count($stalls) > 0 ? $stalls[0]->stall_size : '-' ?></div>
                                    </div>
                                    <div class="icon"><i class="fa fa-archive"></i></div>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4">
                                <div class="small-box bg-olive-active">
                                    <div class="inner">
                                        <h4 style="margin-bottom: 8px;">ITEMS</h4>
                                        <div>Stall Package Items: <?= $items ?></div>
                                        <div style="margin-top: 10px">Additional Items: <?= $additional_items ?></div>
                                    </div>
                                    <div class="icon"><i class="fa fa-cubes"></i></div>
                                </div>
                            </div>
                            <!-- /.col -->
                        </div>
                    </div>
                </div>

                <div class="box">
                    <table class="table table-bordered table-striped">
						<?php
						$stall_types = array();
						$check_stalls_types = $this->db
							->where('booking_id', $this->booking->id)
							->get('es_exhibition_booking_stalls')
							->result();
						foreach ($check_stalls_types as $stall_type) {
							$stall_types[$stall_type->booking_stall_type] = 1;
						}
						?>
                        <tr>
                            <th>Hall</th>
                            <td><?= 'Hall' ?></td>
                            <th>Stall Type</th>
                            <td><?= strtoupper(implode(', ', array_map(function ($stall){ return $stall; }, array_keys($stall_types)))) ?></td>
                            <th>Stalls</th>
                            <td><?= implode(', ', array_map(function ($stall){ return $stall->stall_name; }, $stalls)) ?></td>
                            <th>Package</th>
                            <td><?= count($booking_package_items) > 0 ? $booking_package_items[0]->package_title : '-' ?></td>
                        </tr>
                        <tr>
                            <th colspan="8" class="text-center">Items Summary</th>
                        </tr>
                        <tr>
                            <th colspan="2">Item Type</th>
                            <th colspan="2">Item Name</th>
                            <th colspan="2">Item Quantity</th>
                            <th colspan="2">Total Cost</th>
                        </tr>
						<?php

						foreach ($booking_package_items as $booking_package_item) {
							echo '<tr>
                                        <td colspan="2">Package Item</td>
                                        <td colspan="2">'. $booking_package_item->item_title .'</td>
                                        <td colspan="2">'. $booking_package_item->quantity .'</td>
                                        <td colspan="2" class="text-right">-</td>
                                        </tr>';
						}

						foreach ($booking_extra_items as $booking_extra_item) {
							$i_p = ($this->booking->booking_price_type == 'PKR') ? $booking_extra_item->item_price_pkr : $booking_extra_item->item_price_usd;
							$i_p = $i_p * $booking_extra_item->quantity; // multiply per item price to quantity
							$item_type = ($booking_extra_item->is_initial_item == 1) ? 'Extra Item' : 'Additional Purchase';
							echo '<tr>
                                        <td colspan="2">'.$item_type.'</td>
                                        <td colspan="2">'. $booking_extra_item->item_title .'</td>
                                        <td colspan="2">'. $booking_extra_item->quantity .'</td>
                                        <td colspan="2" class="text-right">'. $this->booking->booking_price_type .' '. number_format($i_p) .'</td>
                                        </tr>';
						}

						$additional_badges_items = $this->db
							->select('I.*, IT.item_title')
							->where('I.exhibition_id', $this->event->id)
							->where('I.booking_id', $this->booking->id)
							->where('IT.global_item_id', BADGE_GLOBAL_INVENTORY_ITEM)
							->where('O.is_approved', 1)
							->where('O.is_canceled', 0)
							->join('es_inventory_item as IT', 'IT.id = I.item_id', 'LEFT')
							->join('es_exhibition_order as O', 'O.id = I.order_id', 'LEFT')
							->get('es_exhibition_order_items as I')
							->result();
						foreach ($additional_badges_items as $additional_badges_item) {
							echo '<tr>
                                <td colspan="2">Additional Badge</td>
                                <td colspan="2">'. $additional_badges_item->item_title .'</td>
                                <td colspan="2">'. $additional_badges_item->item_quantity .'</td>
                                <td colspan="2" class="text-right">'. $this->booking->booking_price_type .' '. number_format($additional_badges_item->item_price) .'</td>
                                </tr>';
						}


						$pending_order_items = $this->db
							->select('I.*, IT.item_title')
                            ->where('I.exhibition_id', $this->event->id)
                            ->where('I.booking_id', $this->booking->id)
                            ->where('O.is_approved', 0)
                            ->where('O.is_canceled', 0)
							->join('es_inventory_item as IT', 'IT.id = I.item_id', 'LEFT')
							->join('es_exhibition_order as O', 'O.id = I.order_id', 'LEFT')
                            ->get('es_exhibition_order_items as I')
                            ->result();

						foreach ($pending_order_items as $pending_order_item) {
							echo '<tr>
                                <td colspan="2">Pending Additional Purchase</td>
                                <td colspan="2">'. $pending_order_item->item_title .'</td>
                                <td colspan="2">'. $pending_order_item->item_quantity .'</td>
                                <td colspan="2" class="text-right"><a href="'. base_url('print/order/invoice?id=' . $pending_order_item->order_id) .'" target="_blank">(view invoice)</a> '. $this->booking->booking_price_type .' '. number_format($pending_order_item->item_price) .'</td>
                                </tr>';
                        }

						?>
                    </table>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box">
                    <div class="box-header">
                        <div class="box-title">Downloads</div>
                    </div>
                    <div class="box-body">
						<?php
						$event_downloads = $this->db
							->where('exhibition_id', $this->event->id)
							->where('type', 'downloads')
							->where('is_deleted', 0)
							->get('es_exhibition_notification')
							->result();

						if (count($event_downloads) > 0) {
						    foreach ($event_downloads as $event_download) {
						        echo '<a href="'. base_url('../' . $event_download->attachments) .'" class="btn btn-block btn-lg btn-primary notification_button  download_btns" target="_blank">'.$event_download->title.'<p class="">'.date('d/m/Y', strtotime($event_download->created_on)).'</p></a>';
                            }
                        } else {
						    echo '<p>Nothing available to download</p>';
                        }
						?>
                    </div>
                </div>

                <div class="box">
                    <div class="box-header">
                        <div class="box-title">Event Update</div>
                    </div>
                    <div class="box-body" style="">
                        <marquee behavior="scroll" height="134px" scrollamount="1"  direction="up" onmouseover="this.stop();" onmouseout="this.start();">
                        <ul class="products-list product-list-in-box">
                            <?php
							$event_updates = $this->db
								->where('exhibition_id', $this->event->id)
								->where('type', 'update')
								->where('is_deleted', 0)
								->limit(5)
                                ->order_by('id', 'DESC')
								->get('es_exhibition_notification')
								->result();

							if (count($event_updates) > 0) {
								foreach ($event_updates as $event_update) {
								    $img_link = (!is_null($event_update->attachments) && $event_update->attachments != '') ? $event_update->attachments : 'uploads/profile/default.png';
									echo '<li class="item">
                                        <div class="product-img">
                                            <img src="'. base_url('../' . $img_link) .'" alt="">
                                        </div>
                                        <div class="product-info">
                                            <a href="javascript:void(0)" class="product-title open_event_updates_modal" data-id="'.myid($event_update->id).'">'. $event_update->title .'</a>
                                            <span class="product-description">'. substr(strip_tags($event_update->description), 0, 40) .'...</span>
                                            <span class="product-description small">'. date('d/m/Y', strtotime($event_update->created_on)) .'</span>
                                        </div>
                                    </li>';
								}
							} else {
								echo '<li class="item">
                                    <div class="product-info">No event updates available!</div>
                                    </li>';
							}
                            ?>
                        </ul>
                        </marquee></div>
                </div>
            </div>
        </div>
    </section>
</div>


<!-- Lightbox for event updates -->
<div class="modal fade" id="event_update_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> <!-- /.modal -->

<?php $this->load->view('includes/after_login/footer'); ?>

<script>
	$(document).on('click', '.open_event_updates_modal', function (e) {
		e.stopImmediatePropagation();

		var id = $(this).attr('data-id');
		$.ajax({
			type:    'post',
			url:     '<?php echo base_url("event_updates"); ?>',
			data:    {notification_id: id},
			success: function (data) {
				data = JSON.parse(data);
				console.log(data);
				if (data.error) {
					swal({
						title: '',
						text: data.message,
						type: "warning"
					});
					return;
				}

				$('#event_update_modal .modal-title').html(data.data.title);
				$('#event_update_modal .modal-body').html('');

				if (data.data.attachments != null && data.data.attachments != '') {
					$('#event_update_modal .modal-body').append('<img src="<?= base_url('../') ?>'+data.data.attachments+'" class="img-thumbnail pull-right" width="150px">');
				}

				$('#event_update_modal .modal-body').append(data.data.description);
				$('#event_update_modal .modal-body').append('<div class="clearfix"></div>');
				$('#event_update_modal').modal({backdrop: 'static', keyboard: false, show: true}); // open lightbox
			},
			error:   function () {
			}
		});
	});


	<?php
	$order_invoice = $this->session->flashdata('order_invoice');
	if (is_string($order_invoice)) {
	    echo 'window.open("'.$order_invoice.'")';
	}
    ?>
</script>

</body>
</html>