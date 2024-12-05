<p>{{___('Get the API details from')}} <a href="https://dashboard.razorpay.com/app/keys"
                                          target="_blank">{{___('here')}} <i
            class="far fa-external-link"></i></a></p>
<div class="mb-3">
    <label class="form-label" for="razorpay_api_key">{{___('API Key')}} *</label>
    <input name="razorpay_api_key" id="razorpay_api_key" type="text" class="form-control" value="{{@$settings->razorpay_api_key}}">
</div>
<div class="mb-3">
    <label class="form-label" for="razorpay_secret_key">{{___('Secret Key')}} *</label>
    <input name="razorpay_secret_key" id="razorpay_secret_key" type="text" class="form-control" value="{{@$settings->razorpay_secret_key}}">
</div>
