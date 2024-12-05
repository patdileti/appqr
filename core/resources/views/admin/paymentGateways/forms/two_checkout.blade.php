<p>{{___('Get the API details from')}} <a href="https://secure.2checkout.com/cpanel"
                                          target="_blank">{{___('here')}} <i
            class="far fa-external-link"></i></a></p>
<div class="mb-3">
    <label class="form-label" for="2checkout_sandbox_mode">{{___('Sandbox Mode')}}</label>
    <select name="2checkout_sandbox_mode" id="2checkout_sandbox_mode" class="form-control">
        <option value="sandbox"
                @if(@$settings->{'2checkout_sandbox_mode'} == 'sandbox') selected @endif>{{___('Enable')}}</option>
        <option value="production"
                @if(@$settings->{'2checkout_sandbox_mode'} == 'production') selected @endif>{{___('Disable')}}</option>
    </select>
</div>
<div class="mb-3">
    <label class="form-label" for="checkout_account_number">{{___('2Checkout Account Number')}} *</label>
    <input name="checkout_account_number" id="checkout_account_number" type="text" class="form-control"
           value="{{@$settings->checkout_account_number}}">
</div>
<div class="mb-3">
    <label class="form-label" for="checkout_public_key">{{___('Publishable Key')}} *</label>
    <input name="checkout_public_key" id="checkout_public_key" type="text" class="form-control"
           value="{{@$settings->checkout_public_key}}">
</div>
<div class="mb-3">
    <label class="form-label" for="checkout_private_key">{{___('Private API Key ')}} *</label>
    <input name="checkout_private_key" id="checkout_private_key" type="text" class="form-control"
           value="{{@$settings->checkout_private_key}}">
</div>
