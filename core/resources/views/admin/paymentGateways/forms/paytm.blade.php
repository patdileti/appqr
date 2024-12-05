<div class="mb-3">
    <label class="form-label" for="PAYTM_ENVIRONMENT">{{___('Sandbox Mode')}}</label>
    <select name="PAYTM_ENVIRONMENT" id="PAYTM_ENVIRONMENT" class="form-control">
        <option value="TEST"
                @if(@$settings->PAYTM_ENVIRONMENT == 'TEST') selected @endif>{{___('Enable')}}</option>
        <option value="PROD"
                @if(@$settings->PAYTM_ENVIRONMENT == 'PROD') selected @endif>{{___('Disable')}}</option>
    </select>
</div>
<div class="mb-3">
    <label class="form-label" for="PAYTM_MERCHANT_KEY">{{___('Paytm Merchant Key')}}</label>
    <input name="PAYTM_MERCHANT_KEY" id="PAYTM_MERCHANT_KEY" type="text" class="form-control"  value="{{@$settings->PAYTM_MERCHANT_KEY}}">
</div>
<div class="mb-3">
    <label class="form-label" for="PAYTM_MERCHANT_MID">{{___('Paytm Merchant ID')}}</label>
    <input name="PAYTM_MERCHANT_MID" id="PAYTM_MERCHANT_MID" type="text" class="form-control" value="{{@$settings->PAYTM_MERCHANT_MID}}">
</div>
<div class="mb-3">
    <label class="form-label" for="PAYTM_MERCHANT_WEBSITE">{{___('Paytm Website Name')}}</label>
    <input name="PAYTM_MERCHANT_WEBSITE" id="PAYTM_MERCHANT_WEBSITE" type="text" class="form-control" value="{{@$settings->PAYTM_MERCHANT_WEBSITE}}">
</div>
