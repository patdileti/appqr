<div class="mb-3">
    <label class="form-label" for="iyzico_sandbox_mode">{{___('Sandbox Mode')}} *</label>
    <select name="iyzico_sandbox_mode" id="iyzico_sandbox_mode" class="form-control">
        <option value="test"
                @if(@$settings->iyzico_sandbox_mode == 'test') selected @endif>{{___('Enable')}}</option>
        <option value="live"
                @if(@$settings->iyzico_sandbox_mode == 'live') selected @endif>{{___('Disable')}}</option>
    </select>
</div>
<div class="mb-3">
    <label class="form-label" for="iyzico_api_key">{{___('Iyzico API Key')}} *</label>
    <input name="iyzico_api_key" id="iyzico_api_key" type="text" class="form-control" value="{{@$settings->iyzico_api_key}}">
</div>
<div class="mb-3">
    <label class="form-label" for="iyzico_secret_key">{{___('Iyzico Secret Key')}} *</label>
    <input id="iyzico_secret_key" name="iyzico_secret_key" type="text" class="form-control" value="{{@$settings->iyzico_secret_key}}">
</div>
