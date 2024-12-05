<div class="mb-3">
    <label class="form-label" for="telr_sandbox_mode">{{___('Sandbox Mode')}}</label>
    <select name="telr_sandbox_mode" id="telr_sandbox_mode" class="form-control">
        <option value="test"
                @if(@$settings->{'telr_sandbox_mode'} == 'test') selected @endif>{{___('Enable')}}</option>
        <option value="live"
                @if(@$settings->{'telr_sandbox_mode'} == 'live') selected @endif>{{___('Disable')}}</option>
    </select>
</div>
<div class="mb-3">
    <label class="form-label" for="telr_store_id">{{___('Telr Store ID')}}</label>
    <input name="telr_store_id" id="telr_store_id" type="text" class="form-control" value="{{@$settings->telr_store_id}}">
</div>
<div class="mb-3">
    <label class="form-label" for="telr_authkey">{{___('Telr Auth Key')}}</label>
    <input name="telr_authkey" id="telr_authkey" type="text" class="form-control" value="{{@$settings->telr_authkey}}">
</div>
