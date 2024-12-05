<div class="mb-3">
    <label class="form-label" for="midtrans_sandbox_mode">{{___('Sandbox Mode')}}</label>
    <select name="midtrans_sandbox_mode" id="midtrans_sandbox_mode" class="form-control">
        <option value="test"
                @if(@$settings->{'midtrans_sandbox_mode'} == 'test') selected @endif>{{___('Enable')}}</option>
        <option value="live"
                @if(@$settings->{'midtrans_sandbox_mode'} == 'live') selected @endif>{{___('Disable')}}</option>
    </select>
</div>
<div class="mb-3">
    <label class="form-label" for="midtrans_client_key">{{___('Midtrans Client Key')}} *</label>
    <input name="midtrans_client_key" id="midtrans_client_key" type="text" class="form-control" value="{{@$settings->midtrans_client_key}}">
</div>

<div class="mb-3">
    <label class="form-label" for="midtrans_server_key">{{___('Midtrans Server Key')}} *</label>
    <input name="midtrans_server_key" id="midtrans_server_key" type="text" class="form-control" value="{{@$settings->midtrans_server_key}}">
</div>
