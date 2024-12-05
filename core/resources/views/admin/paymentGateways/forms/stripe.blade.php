<p>{{___('Get the API details from')}} <a href="https://dashboard.stripe.com/apikeys"
                                          target="_blank">{{___('here')}} <i
            class="far fa-external-link"></i></a></p>
<div class="mb-3">
    <label class="form-label" for="stripe_payment_mode">{{___('Payment Mode')}}</label>
    <select name="stripe_payment_mode" id="stripe_payment_mode" class="form-control">
        <option value="one_time"
                @if(@$settings->stripe_payment_mode == 'one_time') selected @endif>{{___('One Time')}}</option>
        <option value="recurring"
                @if(@$settings->stripe_payment_mode == 'recurring') selected @endif>{{___('Recurring')}}</option>
        <option value="both"
                @if(@$settings->stripe_payment_mode == 'both') selected @endif>{{___('Both')}}</option>
    </select>
</div>
<div class="mb-3">
    <label class="form-label" for="stripe_publishable_key">{{___('Publishable Key')}} *</label>
    <input name="stripe_publishable_key" id="stripe_publishable_key" type="text" class="form-control"
           value="{{@$settings->stripe_publishable_key}}">
</div>
<div class="mb-3">
    <label class="form-label" for="stripe_secret_key">{{___('Secret Key')}} *</label>
    <input name="stripe_secret_key" id="stripe_secret_key" type="text" class="form-control"
           value="{{@$settings->stripe_secret_key}}">
</div>
<div class="mb-3">
    <label class="form-label" for="stripe_webhook_secret">{{___('Webhook Secret')}}</label>
    <input name="stripe_webhook_secret" id="stripe_webhook_secret" type="text" class="form-control"
           value="{{@$settings->stripe_webhook_secret}}">

</div>
<div class="mb-3">
    <label class="form-label" class="form-label" for="paypal_webhook">{{___('WebHook Url')}}</label>
    <input type="text" id="paypal_webhook" class="form-control"
           value="{{url('webhook/'.$gateway->payment_folder)}}" disabled>
</div>
<small>{!! ___('Select the :EVENTS events for webhook.', ['EVENTS' => '<code>checkout.session.completed</code>, <code>invoice.paid</code>, <code>invoice.upcoming</code>']) !!}</small>
