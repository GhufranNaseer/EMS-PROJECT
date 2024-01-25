
<div class="form-group row">
    <div class="col-sm-6">
        <label>Officer Category</label>
        <select name="officer_type" class="form-control">

            <option value="">- None -</option>
            <option value="government_official"> Government Official </option>
            <option value="delegations"> Delegations </option>
        </select>
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-6">
        <label>Name of Officer <span class="text-red">*</span></label>
        <input type="text" class="form-control" name="officer_name" id="officer_name">
    </div>
    <div class="col-sm-6">
        <label>Officer Designation <span class="text-red">*</span></label>
        <input type="text" class="form-control" name="officer_designation" id="officer_designation">
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-6">
        <label>Officer Email <span class="text-red">*</span></label>
        <input type="text" class="form-control" name="officer_email" id="officer_email">
        <p class="help-block">This will use to login officer portal.</p>
    </div>
    <div class="col-sm-6">
        <label>Officer Password <span class="text-red">*</span></label>
        <input type="password" class="form-control" name="officer_password" id="officer_password">
        <p class="help-block">This will use to login officer portal.</p>
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-6">
        <label>Officer Telephone <span class="text-red">*</span></label>
        <input type="number" class="form-control" name="officer_phone" id="officer_phone">
    </div>
    <div class="col-sm-6">
        <label>Officer Fax</label>
        <input type="number" class="form-control" name="officer_fax" id="officer_fax">
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-4">
        <label>Officer Country <span class="text-red">*</span></label>
        <input type="text" class="form-control" name="officer_country" id="officer_country" list="country_list" value="">
        <datalist id="country_list">
			<?= get_instance()->funcs->print_input_data_list('country'); ?>
        </datalist>
    </div>
    <div class="col-sm-4">
        <label>Officer City <span class="text-red">*</span></label>
        <input type="text" class="form-control" name="officer_city" list="cities_list" id="officer_city" value="">
        <datalist id="cities_list">
			<?= get_instance()->funcs->print_input_data_list('city'); ?>
        </datalist>
    </div>
    <div class="col-sm-4">
        <label>Officer Zip Code</label>
        <input type="number" class="form-control" name="officer_zip_code" id="officer_zip_code">
    </div>
</div>

<div class="form-group">
    <label>Officer Address <span class="text-red">*</span></label>
    <textarea name="officer_address" class="form-control" id="officer_address" rows="3"></textarea>
</div>


<div class="form-group row">
    <div class="col-sm-6">
        <label>Name for Corresponding Officer <span class="text-red">*</span></label>
        <input type="text" class="form-control" name="user_name_for_corresponding_officer" id="user_name_for_corresponding_officer">
    </div>
    <div class="col-sm-6">
        <label>Corresponding Officer Email address <span class="text-red">*</span></label>
        <input type="email" class="form-control" name="user_corresponding_email_address" id="user_corresponding_email_address">
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-6">
        <label>Corresponding Officer Phone No <span class="text-red">*</span></label>
        <input type="number" class="form-control" name="user_corresponding_phone_no" id="user_corresponding_phone_no">
    </div>
    <div class="col-sm-6">
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
