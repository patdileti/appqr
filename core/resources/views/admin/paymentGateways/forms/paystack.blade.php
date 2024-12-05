<div class="mb-3">
    <label class="form-label" for="paystack_secret_key">{{___('Secret Key')}} *</label>
    <input name="paystack_secret_key" id="paystack_secret_key" type="text" class="form-control"
           value="{{@$settings->paystack_secret_key}}">
</div>
<div class="mb-3">
    <label class="form-label" for="paystack_public_key">{{___('Public Key')}} *</label>
    <input name="paystack_public_key" id="paystack_public_key" type="text" class="form-control"
           value="{{@$settings->paystack_public_key}}">
</div>
