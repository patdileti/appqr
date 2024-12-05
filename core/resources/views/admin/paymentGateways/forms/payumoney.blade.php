<div class="mb-3">
    <label class="form-label" for="payumoney_sandbox_mode">{{___('Sandbox Mode')}}</label>
    <select name="payumoney_sandbox_mode" id="payumoney_sandbox_mode" class="form-control">
        <option value="1"
                @if(@$settings->payumoney_sandbox_mode == '1') selected @endif>{{___('Enable')}}</option>
        <option value="0"
                @if(@$settings->payumoney_sandbox_mode == '0') selected @endif>{{___('Disable')}}</option>
    </select>
</div>
<div class="mb-3">
    <label class="form-label" for="payumoney_merchant_pos_id">{{___('Payumoney Merchant POS ID')}} *</label>
    <input name="payumoney_merchant_pos_id" id="payumoney_merchant_pos_id" type="text" class="form-control"
           value="{{@$settings->payumoney_merchant_pos_id}}">
</div>
<div class="mb-3">
    <label class="form-label" for="payumoney_signature_key">{{___('Payumoney Signature Key')}} *</label>
    <input name="payumoney_signature_key" id="payumoney_signature_key" type="text" class="form-control"
           value="{{@$settings->payumoney_signature_key}}">
</div>
<div class="mb-3">
    <label class="form-label" for="payumoney_oauth_client_id">{{___('Payumoney OAuth Client ID')}} *</label>
    <input name="payumoney_oauth_client_id" id="payumoney_oauth_client_id" type="text" class="form-control"
           value="{{@$settings->payumoney_oauth_client_id}}">
</div>
<div class="mb-3">
    <label class="form-label" for="payumoney_oauth_client_secret">{{___('Payumoney OAuth Client Secret')}} *</label>
    <input name="payumoney_oauth_client_secret" id="payumoney_oauth_client_secret" type="text" class="form-control"
           value="{{@$settings->payumoney_oauth_client_secret}}">
</div>
