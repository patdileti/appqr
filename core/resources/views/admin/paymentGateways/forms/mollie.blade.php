<p>{{___('Get the API details from')}} <a href="https://www.mollie.com/dashboard"
                                          target="_blank">{{___('here')}} <i
            class="far fa-external-link"></i></a></p>
<div class="mb-3">
    <label class="form-label" for="mollie_api_key">{{___('API Key')}} *</label>
    <input id="mollie_api_key" class="form-control" type="text"
           name="mollie_api_key"
           value="{{@$settings->mollie_api_key}}">
</div>
