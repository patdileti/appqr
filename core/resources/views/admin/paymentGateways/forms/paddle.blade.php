<p>{{___('Get the API details from')}} <a
        href="https://vendors.paddle.com/"
        target="_blank">{{___('here')}} <i class="far fa-external-link"></i></a></p>
<div class="mb-3">
    <label class="form-label" for="paddle_sandbox_mode">{{___('Sandbox Mode')}}</label>
    <select name="paddle_sandbox_mode" id="paddle_sandbox_mode" class="form-control">
        <option value="Yes"
                @if(@$settings->paddle_sandbox_mode == 'Yes') selected @endif>{{___('Enable')}}</option>
        <option value="No"
                @if(@$settings->paddle_sandbox_mode == 'No') selected @endif>{{___('Disable')}}</option>
    </select>
</div>
<div class="mb-3">
    <label class="form-label" for="paddle_vendor_id">{{___('Vendor ID')}} *</label>
    <input name="paddle_vendor_id" id="paddle_vendor_id" type="text" class="form-control"
           value="{{@$settings->paddle_vendor_id}}">
</div>
<div class="mb-3">
    <label class="form-label" for="paddle_api_key">{{___('API Key')}} *</label>
    <input name="paddle_api_key" id="paddle_api_key" type="text" class="form-control"
           value="{{@$settings->paddle_api_key}}">
</div>
<div class="mb-3">
    <label class="form-label" for="paddle_public_key">{{___('Public Key')}} *</label>
    <textarea name="paddle_public_key" id="paddle_public_key" class="form-control" rows="2">{{@$settings->paddle_public_key}}</textarea>
</div>
