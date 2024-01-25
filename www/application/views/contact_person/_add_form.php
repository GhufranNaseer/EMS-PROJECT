<div class="form-group row">
	<div class="col-sm-6">
		<label>Select Customer <span class="text-red">*</span></label>
		<select name="customer_id" class="form-control">
			<option value="">- select -</option>
			<?php
			$customers = $this->db
				->where('is_active', 1)
				->where('is_deleted', 0)
				->get('es_customers')
				->result();

			foreach ($customers as $customer) {
				echo '<option value="'.$customer->id.'">'.$customer->company.'</option>';
			}
			?>
		</select>
	</div>
</div>

<div class="form-group row">
	<div class="col-sm-6">
		<label>Person Name <span class="text-red">*</span></label>
		<input type="text" class="form-control" name="person_name" >
	</div>
	<div class="col-sm-6">
		<label>Designation <span class="text-red">*</span></label>
		<input type="text" class="form-control" name="designation">
	</div>
</div>

<div class="form-group row">
	<div class="col-sm-6">
		<label>Primary Email <span class="text-red">*</span></label>
		<input type="text" class="form-control" name="primary_email">
	</div>
	<div class="col-sm-6">
		<label>Secondary Email</label>
		<input type="text" class="form-control" name="secondary_email">
	</div>
</div>

<div class="form-group row">
	<div class="col-sm-6">
		<label>Primary Phone <span class="text-red">*</span></label>
		<input type="number" class="form-control" name="primary_phone">
	</div>
	<div class="col-sm-6">
		<label>Secondary Phone</label>
		<input type="number" class="form-control" name="secondary_phone">
	</div>
</div>

<div class="form-group row">
	<div class="col-sm-6">
		<label>Office Phone</label>
		<input type="number" class="form-control" name="office_phone">
	</div>
	<div class="col-sm-6">
		<label>Extension</label>
		<input type="number" class="form-control" name="office_phone_extention">
	</div>
</div>

<p class="bg-danger js-msgbox"></p>

<div class="row">
	<div class="col-xs-10">

	</div>
	<div class="col-xs-2">
		<a href="javascript:void(0);"
		   class="btn btn-primary btn-block margin-bottom js-form_btn">Save</a>
	</div>
</div>