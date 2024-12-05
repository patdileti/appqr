<div class="mb-3">
    <label class="form-label" for="paytabs_region">{{___('Paytabs Region')}} *</label>
    @php
        $regions = [
            'ARE' => [
                'title' => 'United Arab Emirates',
                'endpoint' => 'https://secure.paytabs.com/'
            ],
            'SAU' => [
                'title' => 'Saudi Arabia',
                'endpoint' => 'https://secure.paytabs.sa/'
            ],
            'OMN' => [
                'title' => 'Oman',
                'endpoint' => 'https://secure-oman.paytabs.com/'
            ],
            'JOR' => [
                'title' => 'Jordan',
                'endpoint' => 'https://secure-jordan.paytabs.com/'
            ],
            'EGY' => [
                'title' => 'Egypt',
                'endpoint' => 'https://secure-egypt.paytabs.com/'
            ],
            'IRQ' => [
                'title' => 'Iraq',
                'endpoint' => 'https://secure-iraq.paytabs.com/'
            ],
            'PSE' => [
                'title' => 'Palestine',
                'endpoint' => 'https://secure-palestine.paytabs.com/'
            ],
            'GLOBAL' => [
                'title' => 'Global',
                'endpoint' => 'https://secure-global.paytabs.com/'
            ]
        ];
    @endphp
    <select name="paytabs_region" id="paytabs_region" class="form-control">
        @foreach($regions as $key => $region)
            <option value="{{$key}}" @if(@$settings->paytabs_region == $key) selected @endif>{{$region['title']}}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label" for="paytabs_profile_id">{{___('Paytabs Profile id')}} *</label>
    <input name="paytabs_profile_id" id="paytabs_profile_id" type="text" class="form-control"
           value="{{@$settings->paytabs_profile_id}}">
</div>
<div class="mb-3">
    <label class="form-label" for="paytabs_secret_key">{{___('Paytabs Server Key')}} *</label>
    <input name="paytabs_secret_key" id="paytabs_secret_key" type="text" class="form-control"
           value="{{@$settings->paytabs_secret_key}}">
</div>
