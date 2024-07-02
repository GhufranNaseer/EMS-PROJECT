<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<style>
	.event_logo {
		width: 100px;
	}
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" data-page="">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>Product Profile</h1>
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box">
					<div class="box-body">
						<table style="width: 100%;vertical-align: middle">
							<tr>
								<td style="width: 40%; vertical-align: middle">
									<div style="background: <?= $event->event_color ?>; height: 50px; width: 100%">&nbsp;</div>
								</td>
								<td style="width: 20%;text-align: center">
									<img src="<?= base_url('../' . $event->event_logo) ?>" alt="" class="event_logo">
								</td>
								<td style="width: 40%; vertical-align: middle">
									<div style="background: <?= $event->event_color ?>; height: 50px; width: 100%">&nbsp;</div>
								</td>
							</tr>
						</table>



						<h2 class="text-center" style="margin-top: 2rem; margin-bottom: 3rem;"><?= (isset($rows->company)) ? $rows->company : '' ?></h2>
						<div class="row">
							<div class="col-md-8">
								<table class="table table-bordered table-striped">
									<tr>
										<th class="text-center" colspan="2">Company Information</th>
									</tr>
									<tr>
										<th>Address:</th>
										<td style="width: 70%"><?= (isset($data->exhibit->address)) ? ucfirst(strtolower($data->exhibit->address)) : '' ?></td>
									</tr>
									<tr>
										<th>Country:</th>
										<td><?= (isset($data->exhibit->country)) ? $data->exhibit->country : '-' ?></td>
									</tr>
									<tr>
										<th>Telephone:</th>
										<td><?= (isset($data->exhibit->telephone)) ? $data->exhibit->telephone : '-' ?></td>
									</tr>
									<tr>
										<th>Fax:</th>
										<td><?= (isset($data->exhibit->fax)) ? $data->exhibit->fax : '-' ?></td>
									</tr>
									<tr>
										<th>Email:</th>
										<td><?= (isset($data->exhibit->email)) ? $data->exhibit->email : '-' ?></td>
									</tr>
									<tr>
										<th>Website:</th>
										<td><?= (isset($data->exhibit->website)) ? $data->exhibit->website : '-' ?></td>
									</tr>
									<tr>
										<th class="text-center" colspan="2">Contact Person Information</th>
									</tr>
									<tr>
										<th>Contact Person Name:</th>
										<td><?= (isset($data->exhibit->contact_person->name)) ? $data->exhibit->contact_person->name : '-' ?></td>
									</tr>
									<tr>
										<th>Designation:</th>
										<td><?= (isset($data->exhibit->contact_person->designation)) ? $data->exhibit->contact_person->designation : '-' ?></td>
									</tr>
									<tr>
										<th>Telephone:</th>
										<td><?= (isset($data->exhibit->contact_person->phone)) ? $data->exhibit->contact_person->phone : '-' ?></td>
									</tr>
									<tr>
										<th>Email:</th>
										<td><?= (isset($data->exhibit->contact_person->email)) ? $data->exhibit->contact_person->email : '-' ?></td>
									</tr>
									<tr>
										<th>Fax:</th>
										<td><?= (isset($data->exhibit->contact_person->fax)) ? $data->exhibit->contact_person->fax : '-' ?></td>
									</tr>
								</table>

								<h4>Company Profile</h4>
								<div class="well">
									<h5><?= (isset($data->profile)) ? $data->profile : '' ?></h5>
								</div>
							</div>
							<div class="col-md-4">
								<div class="text-center">
									<?php
									if (isset($data->exhibit->company_logo)) {
										$logo = $data->exhibit->company_logo[0];
										$logo = str_replace('uploaded:', '', $logo);
										echo '<img src="' . base_url($logo) . '" alt="" class="img-thumbnail" style="width: 300px;">';
									}
									?>
								</div>

								<table class="table" style="margin-top: 4rem;">
									<tr>
										<th>Hall #</th>
										<td><?= $hall_data->hall_title ?></td>
									</tr>
									<tr>
										<th>Stand #</th>
										<td><?= implode(', ', array_map(function ($stall){ return $stall->stall_name; }, $booking_stalls)) ?></td>
									</tr>
								</table>

								<div class="text-center">
									<?php
									if (isset($data->exhibit->company_ad)) {
										$logo = $data->exhibit->company_ad[0];
										$logo = str_replace('uploaded:', '', $logo);
										echo '<img src="' . base_url($logo) . '" alt="" class="img-thumbnail" style="width: 200px;">';
									}
									?>
								</div>
							</div>
						</div>

						

					</div>
				</div>



				<?php if (isset($data->products)) { ?>
					<div class="box">
						<div class="box-header">
							<h3 class="box-title">Business Categories</h3>
						</div>
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>#</th>
									<th>Business Sector</th>
									<th>Business Type</th>
									<th>Business Area</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$count = 0;
								if (isset($data->products->main) && count((array)$data->products->main) > 0) {
									echo '<tr><th class="text-center" colspan="4">Main Business</th></tr>';

									foreach ($data->products->main as $key => $main_business) {
										$count++;
										echo '<tr>
										<td>'. $count .'</td>
										<td>'. $main_business->sector .'</td>
										<td>'. $main_business->type .'</td>
										<td>'. $main_business->area .'</td>
										</tr>';
									}
								}
								
								if (isset($data->products->other) && count((array)$data->products->other) > 0) {
									echo '<tr><th class="text-center"colspan="4">Other Business</th></tr>';

									foreach ($data->products->other as $key => $other_business) {
										$count++;
										echo '<tr>
										<td>'. $count .'</td>
										<td>'. $other_business->sector .'</td>
										<td>'. $other_business->type .'</td>
										<td>'. $other_business->area .'</td>
										</tr>';
									}
								}
								
								?>
							</tbody>
						</table>
					</div>
				<?php } ?>
				
				
				<?php if (isset($data->products) && isset($data->products->product) && count((array)$data->products->product) > 0) { ?>
					<div class="box">
						<div class="box-header">
							<h3 class="box-title">Products</h3>
						</div>
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>#</th>
									<th>Product Category</th>
									<th>Product Type</th>
									<th>Product Name</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$count = 0;
								
								if (isset($data->products->product) && count((array)$data->products->product) > 0) {
									foreach ($data->products->product as $key => $product) {
										$count++;
										echo '<tr>
										<td>'. $count .'</td>
										<td>'. $product->category .'</td>
										<td>'. $product->type .'</td>
										<td>'. $product->name .'</td>
										</tr>';
									}
								}
								?>
							</tbody>
						</table>
					</div>
				<?php } ?>


				<?php if ($data->principle->is_active == 1) { ?>
					<div class="box">
						<div class="box-header">
							<h3 class="box-title">Company Principal</h3>
						</div>
						<table class="table table-bordered table-striped">
							<tr>
								<th>Company</th>
								<th>Country</th>
								<th>Phone</th>
								<th>Email</th>
								<th></th>
							</tr>
							<?php
							foreach ($data->principle->principles_list as $row) {

							?>
								<tr style="width: 100%;">
									<td style="width: 20%;"><?= isset($row->full_name) ? str_replace('+', ' ', $row->full_name) : '' ?></td>
									<td style="width: 20%;"><?= isset($row->country) ? $row->country : '' ?></td>
									<td style="width: 20%;"><?= isset($row->phone) ? $row->phone : '' ?></td>
									<td style="width: 20%;"><?= isset($row->email) ? $row->email : '' ?></td>
									<td style="width: 20%;">
										<?php
										if (isset($row->company_logo)) {
											$logo = $row->company_logo;
											$logo = str_replace('uploaded:', '', $logo);
											echo '<img src="' . base_url('client/' . $logo) . '" alt="" style="width: 60px; text-align: right; ">';
										}
										?>
									</td>
								</tr>
							<?php } ?>
						</table>
					</div>
				<?php } ?>

			</div>
		</div>
	</section>

</div>


<?php $this->load->view('includes/after_login/footer'); ?>

<script>

</script>
</body>

</html>