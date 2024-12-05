<p>{{___('Get the API details from')}} <a
        href="https://developer.paypal.com/developer/applications/create"
        target="_blank">{{___('here')}} <i class="far fa-external-link"></i></a></p>
<div class="mb-3">
    <label class="form-label" for="paypal_sandbox_mode">{{___('Sandbox Mode')}}</label>
    <select name="paypal_sandbox_mode" id="paypal_sandbox_mode" class="form-control">
        <option value="Yes"
                @if(@$settings->paypal_sandbox_mode == 'Yes') selected @endif>{{___('Enable')}}</option>
        <option value="No"
                @if(@$settings->paypal_sandbox_mode == 'No') selected @endif>{{___('Disable')}}</option>
    </select>
</div>
<div class="mb-3">
    <label class="form-label" for="paypal_payment_mode">{{___('Payment Mode')}}</label>
    <select name="paypal_payment_mode" id="paypal_payment_mode" class="form-control">
        <option value="one_time"
                @if(@$settings->paypal_payment_mode == 'one_time') selected @endif>{{___('One Time')}}</option>
        <option value="recurring"
                @if(@$settings->paypal_payment_mode == 'recurring') selected @endif>{{___('Recurring')}}</option>
        <option value="both"
                @if(@$settings->paypal_payment_mode == 'both') selected @endif>{{___('Both')}}</option>
    </select>
</div>
<div class="mb-3">
    <label class="form-label" for="paypal_api_client_id">{{___('Client ID')}} *</label>
    <input name="paypal_api_client_id" id="paypal_api_client_id" type="text" class="form-control"
           value="{{@$settings->paypal_api_client_id}}">
</div>
<div class="mb-3">
    <label class="form-label" for="paypal_api_secret">{{___('Client Secret')}} *</label>
    <input name="paypal_api_secret" id="paypal_api_secret" type="text" class="form-control"
           value="{{@$settings->paypal_api_secret}}">
</div>
<div class="mb-3">
    <label class="form-label" for="paypal_api_app_id">{{___('APP ID')}} *</label>
    <input name="paypal_api_app_id" id="paypal_api_app_id" type="text" class="form-control"
           value="{{@$settings->paypal_api_app_id}}">
</div>
<div class="mb-3">
    <label class="form-label" for="paypal_webhook">{{___('WebHook Url')}}</label>
    <input type="text" id="paypal_webhook" class="form-control"
           value="{{url('webhook/'.$gateway->payment_folder)}}" disabled>
</div>
<small>{!! ___('Select the :EVENTS events for webhook.', ['EVENTS' => '<code>PAYMENT.SALE.COMPLETED</code>']) !!}</small>
