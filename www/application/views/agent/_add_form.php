
<div class="form-group row">
    <div class="col-sm-6">
        <label>Exhibitor Company <span class="text-red">*</span></label>
        <input type="text" class="form-control" name="agent_company" id="agent_company">
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-6">
        <label>Executive Name <span class="text-red">*</span></label>
        <input type="text" class="form-control" name="agent_name" id="agent_name">
    </div>
    <div class="col-sm-6">
        <label>Designation <span class="text-red">*</span></label>
        <input type="text" class="form-control" name="agent_designation" id="agent_designation">
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-6">
        <label>Email <span class="text-red">*</span></label>
        <input type="text" class="form-control" name="agent_email" id="agent_email">
    </div>
    <div class="col-sm-6">
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-6">
        <label>Telephone <span class="text-red">*</span></label>
        <input type="number" class="form-control" name="agent_phone" id="agent_phone">
    </div>
    <div class="col-sm-6">
        <label>Fax</label>
        <input type="number" class="form-control" name="agent_fax" id="agent_fax">
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-4">
        <label>Country <span class="text-red">*</span></label>
        <input type="text" class="form-control" name="agent_country" id="agent_country" list="country_list" value="">
        <datalist id="country_list">
			<?= get_instance()->funcs->print_input_data_list('country'); ?>
        </datalist>
    </div>
    <div class="col-sm-4">
        <label>City <span class="text-red">*</span></label>
        <input type="text" class="form-control" name="city" list="cities_list" id="city" value="">
        <datalist id="cities_list">
			<?= get_instance()->funcs->print_input_data_list('city'); ?>
        </datalist>
    </div>
    <div class="col-sm-4">
        <label>Zip Code</label>
        <input type="number" class="form-control" name="agent_zip_code" id="agent_zip_code">
    </div>
</div>

<div class="form-group">
    <label>Address <span class="text-red">*</span></label>
    <textarea name="agent_address" class="form-control" id="agent_address" rows="3"></textarea>
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
