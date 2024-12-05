@extends($activeTheme.'layouts.app')
@section('title', ___('Edit Restaurant'))
@section('content')

    <form method="post" action="{{ route('restaurants.update', $post->id) }}" enctype="multipart/form-data">
        @csrf
        @method('put')

        <div class="dashboard-box margin-top-0">
            <!-- Headline -->
            <div class="headline">
                <h3><i class="icon-feather-folder-plus"></i>{{ ___('Edit Restaurant') }}</h3>
            </div>
            <div class="content with-padding padding-bottom-10">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="submit-field">
                            <h5>{{ ___('Name') }}</h5>
                            <div class="d-flex align-items-baseline">
                                <div class="qr-restaurant-color-wrapper padding-right-15">
                                    <button class="bm-color-picker"></button>
                                    <input type="hidden" class="color-input" name="color"
                                           value="{{ $post->color }}">
                                </div>
                                <input type="text" class="with-border" name="title" value="{{ $post->title }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="submit-field">
                            <h5>{{ ___('Slug') }}</h5>
                            <input type="text" id="store-slug" class="with-border" name="slug"
                                   value="{{ $post->slug }}">
                            <div id="slug-availability-status"></div>
                            <small>{{ ___('Use only alphanumeric value without space. (Hyphen(-) allow). Slug will be used for restaurant url.') }}</small>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="submit-field">
                            <h5>{{ ___('Subtitle') }}</h5>
                            <input type="text" class="with-border" name="sub_title" value="{{ $post->sub_title }}">
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="submit-field">
                            <h5>{{ ___('Timing') }}</h5>
                            <input type="text" class="with-border" name="timing" value="{{ $post->timing }}">
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="submit-field">
                            <h5>{{ ___('Phone') }}</h5>
                            <input type="text" class="with-border" name="phone" value="{{ $post->phone }}">
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="submit-field">
                            <h5>{{ ___('Description') }}</h5>
                            <textarea class="with-border tiny-editor"
                                      name="description">{{ $post->description }}</textarea>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="submit-field">
                            <h5>{{ ___('Address') }}</h5>
                            <input class="with-border" type="text" name="address" value="{{ $post->address }}" required>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="submit-field">
                            <h5>{{ ___('Logo') }}</h5>
                            <div class="input-file">
                                <img src="{{asset('storage/restaurant/logo/'.$post->main_image)}}" id="restro_image">
                            </div>

                            <div class="uploadButton margin-top-30">
                                <input class="uploadButton-input" type="file" accept="image/*"
                                       onchange="readImageURL(this,'restro_image')" id="image_upload"
                                       name="main_image"/>
                                <label class="uploadButton-button ripple-effect"
                                       for="image_upload">{{ ___('Upload image') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="submit-field">
                            <h5>{{ ___('Cover Image') }}</h5>
                            <div class="input-file">
                                <img src="{{asset('storage/restaurant/cover/'.$post->cover_image)}}"
                                     id="restro_cover_image">
                            </div>
                            <div class="uploadButton margin-top-30">
                                <input class="uploadButton-input" type="file" accept="image/*"
                                       onchange="readImageURL(this,'restro_cover_image')" id="cover_upload"
                                       name="cover_image"/>
                                <label class="uploadButton-button ripple-effect"
                                       for="cover_upload">{{ ___('Upload image') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="submit-field">
                            <h5>{{ ___('Restaurant Template') }}</h5>
                            <div class="account-type row template-chooser">
                                @foreach($templates as $template)
                                    <div class="col-md-3 margin-right-0">
                                        <input type="radio" name="restaurant_template"
                                               value="{{ $template['folder'] }}" id="{{ $template['folder'] }}"
                                               class="account-type-radio" @checked($template['folder'] == $postOptions->restaurant_template)>
                                        <label for="{{ $template['folder'] }}" class="ripple-effect-dark">
                                            <img class="margin-bottom-5" src="{{ $template['image'] }}">
                                            <strong>{{ $template['name'] }}
                                                @if("flipbook" == $template['folder'])
                                                    <i class="icon-feather-image" data-tippy-placement="top"
                                                       title="{{ ___('This template supports image only.') }}"></i>
                                                @endif</strong>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="js-accordion">
            <div class="dashboard-box js-accordion-item">
                <!-- Headline -->
                <div class="headline js-accordion-header">
                    <h3><i class="icon-feather-settings"></i>{{ ___('Restaurant Settings') }}</h3>
                </div>
                <div class="content with-padding padding-bottom-10 js-accordion-body" style="display: none">
                    <div class="submit-field" id="menu-layout-field">
                        <h5>{{ ___('Menu Layout') }}</h5>
                        <select name="menu_layout" class="with-border selectpicker">
                            <option
                                value="both" @selected(@$postOptions->menu_layout == 'both')>{{ ___('Both Layouts') }}</option>
                            <option
                                value="grid" @selected(@$postOptions->menu_layout == 'grid')>{{ ___('Grid Layout') }}</option>
                            <option
                                value="list" @selected(@$postOptions->menu_layout == 'list')>{{ ___('List Layout') }}</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="submit-field">
                                <h5>{{ ___('Menu Languages') }}</h5>
                                <select name="menu_languages[]" class="with-border selectpicker" title="{{ ___('Choose') }}" multiple>
                                    @foreach(get_active_languages() as $code => $language)
                                        <option value="{{$code}}"
                                            @selected(in_array($code, (array) @$postOptions->menu_languages))>
                                            {{ $language['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <small>{{___('Select multiple options for multi-language menu.')}}</small>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="submit-field">
                                <h5>{{ ___('Default Language') }}</h5>
                                <select name="default_language" class="with-border selectpicker">
                                    @if(!empty(@$postOptions->menu_languages))
                                        @foreach(get_active_languages() as $code => $language)
                                            @if(in_array($code, (array) @$postOptions->menu_languages))
                                                <option value="{{$code}}"
                                                    @selected(@$postOptions->default_language == $code)>
                                                    {{ $language['name'] }}
                                                </option>
                                            @endif
                                        @endforeach
                                    @else
                                        <option value="">{{___('Please select menu languages first')}}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="submit-field">
                                <h5>{{ ___('Currency Code') }}</h5>
                                <input class="with-border" type="text" name="currency_code"
                                       value="{{ @$postOptions->currency_code }}"
                                       placeholder="{{config('settings.currency_code')}}" required>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="submit-field">
                                <h5>{{ ___('Currency Sign') }}</h5>
                                <input class="with-border" type="text" name="currency_sign"
                                       value="{{ @$postOptions->currency_sign }}"
                                       placeholder="{!! config('settings.currency_sign') !!}" required>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="submit-field">
                                <h5>{{ ___('Currency Position') }}</h5>
                                <select class="with-border selectpicker" name="currency_pos">
                                    <option value="1" @selected(@$postOptions->currency_pos == 1)>
                                        {{ ___('Before price') }}</option>
                                    <option value="0" @selected(@$postOptions->currency_pos == 0)>
                                        {{ ___('After price') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="submit-field">
                        <h5>{{ ___('Allow Call to Waiter') }}</h5>
                        <select class="selectpicker with-border" name="allow_call_waiter">
                            <option value="1" @selected(@$postOptions->allow_call_waiter == 1)>{{ ___('Yes') }}</option>
                            <option value="0" @selected(@$postOptions->allow_call_waiter == 0)>{{ ___('No') }}</option>
                        </select>
                    </div>

                    @if(request()->user()->plan()->settings->allow_ordering)
                        <div class="submit-field">
                            <h5>{{ ___('Allow on-table order') }}</h5>
                            <select class="selectpicker with-border" name="restaurant_on_table_order">
                                <option
                                    value="1" @selected(@$postOptions->restaurant_on_table_order == 1)>{{ ___('Yes') }}</option>
                                <option
                                    value="0" @selected(@$postOptions->restaurant_on_table_order == 0)>{{ ___('No') }}</option>
                            </select>
                        </div>
                        <div class="submit-field">
                            <h5>{{ ___('Allow takeaway order') }}</h5>
                            <select class="selectpicker with-border" name="restaurant_takeaway_order">
                                <option
                                    value="1" @selected(@$postOptions->restaurant_takeaway_order == 1)>{{ ___('Yes') }}</option>
                                <option
                                    value="0" @selected(@$postOptions->restaurant_takeaway_order == 0)>{{ ___('No') }}</option>
                            </select>
                        </div>
                        <div class="submit-field">
                            <h5>{{ ___('Allow delivery order') }}</h5>
                            <select class="selectpicker with-border" name="restaurant_delivery_order">
                                <option
                                    value="1" @selected(@$postOptions->restaurant_delivery_order == 1)>{{ ___('Yes') }}</option>
                                <option
                                    value="0" @selected(@$postOptions->restaurant_delivery_order == 0)>{{ ___('No') }}</option>
                            </select>
                        </div>
                        <div class="submit-field" id="delivery-field">
                            <h5>{{ ___('Delivery Charge') }}</h5>
                            <input type="number" class="with-border" name="restaurant_delivery_charge"
                                   value="{{@$postOptions->restaurant_delivery_charge}}">
                            <small>{{ ___('Additional fee for delivery order.') }}</small>
                        </div>

                        @if(config('settings.admin_allow_online_payment'))
                            <div class="submit-field">
                                <h5>{{ ___('Send New Order Notification') }}</h5>
                                <select class="selectpicker with-border" name="restaurant_send_order_notification">
                                    <option
                                        value="1" @selected(@$postOptions->restaurant_send_order_notification == 1)>{{ ___('Yes') }}</option>
                                    <option
                                        value="0" @selected(@$postOptions->restaurant_send_order_notification == 0)>{{ ___('No') }}</option>
                                </select>
                            </div>
                        @endif

                        @if(config('settings.admin_send_order_notification'))
                            <div class="submit-field">
                                <h5>{{ ___('Allow Online Payment') }}</h5>
                                <select class="selectpicker with-border" name="restaurant_online_payment">
                                    <option
                                        value="1" @selected(@$postOptions->restaurant_online_payment == 1)>{{ ___('Yes') }}</option>
                                    <option
                                        value="0" @selected(@$postOptions->restaurant_online_payment == 0)>{{ ___('No') }}</option>
                                </select>
                            </div>
                            <div class="js-accordion" id="payment-gateways">
                                <div class="dashboard-box margin-top-0 margin-bottom-15 js-accordion-item">
                                    <!-- Headline -->
                                    <div class="headline js-accordion-header">
                                        <h3>{{ ___('Paypal') }}</h3>
                                    </div>
                                    <div class="content with-padding padding-bottom-10 js-accordion-body"
                                         style="display: none">
                                        <p>
                                            <small><strong>
                                                    {{___('Get the API details from')}} <a
                                                        href="https://developer.paypal.com/developer/applications/create"
                                                        target="_blank">{{___('here')}} <i
                                                            class="far fa-external-link"></i></a></strong></small>
                                        </p>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Turn On/Off') }}</label>
                                                    <select name="restaurant_paypal_install"
                                                            id="restaurant_paypal_install"
                                                            class="selectpicker with-border">
                                                        <option
                                                            value="1" @selected(@$postOptions->restaurant_paypal_install == 1)>{{ ___('On') }}</option>
                                                        <option
                                                            value="0" @selected(@$postOptions->restaurant_paypal_install == 0)>{{ ___('Off') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Title') }}</label>
                                                    <input name="restaurant_paypal_title" type="text"
                                                           class="with-border"
                                                           value="{{ @$postOptions->restaurant_paypal_title }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Sandbox Mode') }}</label>
                                                    <select name="restaurant_paypal_sandbox_mode"
                                                            class="selectpicker with-border">
                                                        <option
                                                            value="Yes" @selected(@$postOptions->restaurant_paypal_sandbox_mode == 'Yes')>{{ ___('On') }}</option>
                                                        <option
                                                            value="No" @selected(@$postOptions->restaurant_paypal_sandbox_mode == 'No')>{{ ___('Off') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Client ID') }}</label>
                                                    <input name="restaurant_paypal_api_client_id" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_paypal_api_client_id}}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Client Secret') }}</label>
                                                    <input name="restaurant_paypal_api_secret" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_paypal_api_secret}}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('APP ID') }}</label>
                                                    <input name="restaurant_paypal_api_app_id" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_paypal_api_app_id}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="dashboard-box margin-top-0 margin-bottom-15 js-accordion-item">
                                    <!-- Headline -->
                                    <div class="headline js-accordion-header">
                                        <h3>{{ ___('Stripe') }}</h3>
                                    </div>
                                    <div class="content with-padding padding-bottom-10 js-accordion-body"
                                         style="display: none">
                                        <p>
                                            <small><strong>
                                                    {{___('Get the API details from')}} <a
                                                        href="https://dashboard.stripe.com/apikeys"
                                                        target="_blank">{{___('here')}} <i
                                                            class="far fa-external-link"></i></a>
                                                </strong></small>
                                        </p>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Turn On/Off') }}</label>
                                                    <select name="restaurant_stripe_install"
                                                            id="restaurant_stripe_install"
                                                            class="selectpicker with-border">
                                                        <option
                                                            value="1" @selected(@$postOptions->restaurant_stripe_install == 1)>{{ ___('On') }}</option>
                                                        <option
                                                            value="0" @selected(@$postOptions->restaurant_stripe_install == 0)>{{ ___('Off') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Title') }}</label>
                                                    <input name="restaurant_stripe_title" type="text"
                                                           class="with-border"
                                                           value="{{ @$postOptions->restaurant_stripe_title }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Secret Key') }}</label>
                                                    <input name="restaurant_stripe_secret_key" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_stripe_secret_key}}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Publishable Key') }}</label>
                                                    <input name="restaurant_stripe_publishable_key" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_stripe_publishable_key}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="dashboard-box margin-top-0 margin-bottom-15 js-accordion-item">
                                    <!-- Headline -->
                                    <div class="headline js-accordion-header">
                                        <h3>{{ ___('Razorpay') }}</h3>
                                    </div>
                                    <div class="content with-padding padding-bottom-10 js-accordion-body"
                                         style="display: none">
                                        <p>
                                            <small><strong>
                                                    {{___('Get the API details from')}} <a
                                                        href="https://dashboard.razorpay.com/app/keys"
                                                        target="_blank">{{___('here')}} <i
                                                            class="far fa-external-link"></i></a>
                                                </strong></small>
                                        </p>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Turn On/Off') }}</label>
                                                    <select name="restaurant_razorpay_install"
                                                            id="restaurant_razorpay_install"
                                                            class="selectpicker with-border">
                                                        <option
                                                            value="1" @selected(@$postOptions->restaurant_razorpay_install == 1)>{{ ___('On') }}</option>
                                                        <option
                                                            value="0" @selected(@$postOptions->restaurant_razorpay_install == 0)>{{ ___('Off') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Title') }}</label>
                                                    <input name="restaurant_razorpay_title" type="text"
                                                           class="with-border"
                                                           value="{{ @$postOptions->restaurant_razorpay_title }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label
                                                        class="control-label">{{ ___('API Key') }}</label>
                                                    <input name="restaurant_razorpay_api_key" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_razorpay_api_key}}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label
                                                        class="control-label">{{ ___('Secret Key') }}</label>
                                                    <input name="restaurant_razorpay_secret_key" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_razorpay_secret_key}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="dashboard-box margin-top-0 margin-bottom-15 js-accordion-item">
                                    <!-- Headline -->
                                    <div class="headline js-accordion-header">
                                        <h3>{{ ___('Mollie') }}</h3>
                                    </div>
                                    <div class="content with-padding padding-bottom-10 js-accordion-body"
                                         style="display: none">
                                        <p>
                                            <small><strong>
                                                    {{___('Get the API details from')}} <a
                                                        href="https://www.mollie.com/dashboard"
                                                        target="_blank">{{___('here')}} <i
                                                            class="far fa-external-link"></i></a>
                                                </strong></small>
                                        </p>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Turn On/Off') }}</label>
                                                    <select name="restaurant_mollie_install"
                                                            id="restaurant_mollie_install"
                                                            class="selectpicker with-border">
                                                        <option
                                                            value="1" @selected(@$postOptions->restaurant_mollie_install == 1)>{{ ___('On') }}</option>
                                                        <option
                                                            value="0" @selected(@$postOptions->restaurant_mollie_install == 0)>{{ ___('Off') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Title') }}</label>
                                                    <input name="restaurant_mollie_title" type="text"
                                                           class="with-border"
                                                           value="{{ @$postOptions->restaurant_mollie_title }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Mollie API Key') }}</label>
                                                    <input name="restaurant_mollie_api_key" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_mollie_api_key}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="dashboard-box margin-top-0 margin-bottom-15 js-accordion-item">
                                    <!-- Headline -->
                                    <div class="headline js-accordion-header">
                                        <h3>{{ ___('Paytm') }}</h3>
                                    </div>
                                    <div class="content with-padding padding-bottom-10 js-accordion-body"
                                         style="display: none">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Turn On/Off') }}</label>
                                                    <select name="restaurant_paytm_install"
                                                            id="restaurant_paytm_install"
                                                            class="selectpicker with-border">
                                                        <option
                                                            value="1" @selected(@$postOptions->restaurant_paytm_install == 1)>{{ ___('On') }}</option>
                                                        <option
                                                            value="0" @selected(@$postOptions->restaurant_paytm_install == 0)>{{ ___('Off') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Title') }}</label>
                                                    <input name="restaurant_paytm_title" type="text"
                                                           class="with-border"
                                                           value="{{ @$postOptions->restaurant_paytm_title }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Sandbox Mode') }}</label>
                                                    <select name="restaurant_paytm_sandbox_mode"
                                                            class="selectpicker with-border">
                                                        <option
                                                            value="TEST" @selected(@$postOptions->restaurant_paytm_sandbox_mode == 'TEST')>{{ ___('On') }}</option>
                                                        <option
                                                            value="PROD" @selected(@$postOptions->restaurant_paytm_sandbox_mode == 'PROD')>{{ ___('Off') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Paytm Merchant Key') }}</label>
                                                    <input name="restaurant_paytm_merchant_key" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_paytm_merchant_key}}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Paytm Merchant ID') }}</label>
                                                    <input name="restaurant_paytm_merchant_mid" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_paytm_merchant_mid}}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Paytm Website Name') }}</label>
                                                    <input name="restaurant_paytm_merchant_website" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_paytm_merchant_website}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="dashboard-box margin-top-0 margin-bottom-15 js-accordion-item">
                                    <!-- Headline -->
                                    <div class="headline js-accordion-header">
                                        <h3>{{ ___('Paystack') }}</h3>
                                    </div>
                                    <div class="content with-padding padding-bottom-10 js-accordion-body"
                                         style="display: none">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Turn On/Off') }}</label>
                                                    <select name="restaurant_paystack_install"
                                                            id="restaurant_paystack_install"
                                                            class="selectpicker with-border">
                                                        <option
                                                            value="1" @selected(@$postOptions->restaurant_paystack_install == 1)>{{ ___('On') }}</option>
                                                        <option
                                                            value="0" @selected(@$postOptions->restaurant_paystack_install == 0)>{{ ___('Off') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Title') }}</label>
                                                    <input name="restaurant_paystack_title" type="text"
                                                           class="with-border"
                                                           value="{{ @$postOptions->restaurant_paystack_title }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label
                                                        class="control-label">{{ ___('Secret Key') }}</label>
                                                    <input name="restaurant_paystack_secret_key" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_paystack_secret_key}}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Public Key') }}</label>
                                                    <input name="restaurant_paystack_public_key" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_paystack_public_key}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="dashboard-box margin-top-0 margin-bottom-15 js-accordion-item">
                                    <!-- Headline -->
                                    <div class="headline js-accordion-header">
                                        <h3>{{ ___('Payumoney') }}</h3>
                                    </div>
                                    <div class="content with-padding padding-bottom-10 js-accordion-body"
                                         style="display: none">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Turn On/Off') }}</label>
                                                    <select name="restaurant_payumoney_install"
                                                            id="restaurant_payumoney_install"
                                                            class="selectpicker with-border">
                                                        <option
                                                            value="1" @selected(@$postOptions->restaurant_payumoney_install == 1)>{{ ___('On') }}</option>
                                                        <option
                                                            value="0" @selected(@$postOptions->restaurant_payumoney_install == 0)>{{ ___('Off') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Title') }}</label>
                                                    <input name="restaurant_payumoney_title" type="text"
                                                           class="with-border"
                                                           value="{{ @$postOptions->restaurant_payumoney_title }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Sandbox Mode') }}</label>
                                                    <select name="restaurant_payumoney_sandbox_mode"
                                                            class="selectpicker with-border">
                                                        <option
                                                            value="1" @selected(@$postOptions->restaurant_payumoney_sandbox_mode == '1')>{{ ___('On') }}</option>
                                                        <option
                                                            value="0" @selected(@$postOptions->restaurant_payumoney_sandbox_mode == '0')>{{ ___('Off') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label
                                                        class="control-label">{{ ___('Payumoney Merchant POS ID') }}</label>
                                                    <input name="restaurant_payumoney_merchant_pos_id" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_payumoney_merchant_pos_id}}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label
                                                        class="control-label">{{ ___('Payumoney Signature Key') }}</label>
                                                    <input name="restaurant_payumoney_signature_key" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_payumoney_signature_key}}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label
                                                        class="control-label">{{ ___('Payumoney OAuth Client ID') }}</label>
                                                    <input name="restaurant_payumoney_oauth_client_id" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_payumoney_oauth_client_id}}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label
                                                        class="control-label">{{ ___('Payumoney OAuth Client Secret') }}</label>
                                                    <input name="restaurant_payumoney_oauth_client_secret" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_payumoney_oauth_client_secret}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="dashboard-box margin-top-0 margin-bottom-15 js-accordion-item">
                                    <!-- Headline -->
                                    <div class="headline js-accordion-header">
                                        <h3>{{ ___('Iyzico') }}</h3>
                                    </div>
                                    <div class="content with-padding padding-bottom-10 js-accordion-body"
                                         style="display: none">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Turn On/Off') }}</label>
                                                    <select name="restaurant_iyzico_install"
                                                            id="restaurant_iyzico_install"
                                                            class="selectpicker with-border">
                                                        <option
                                                            value="1" @selected(@$postOptions->restaurant_iyzico_install == 1)>{{ ___('On') }}</option>
                                                        <option
                                                            value="0" @selected(@$postOptions->restaurant_iyzico_install == 0)>{{ ___('Off') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Title') }}</label>
                                                    <input name="restaurant_iyzico_title" type="text"
                                                           class="with-border"
                                                           value="{{ @$postOptions->restaurant_iyzico_title }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Sandbox Mode') }}</label>
                                                    <select name="restaurant_iyzico_sandbox_mode"
                                                            class="selectpicker with-border">
                                                        <option
                                                            value="test" @selected(@$postOptions->restaurant_iyzico_sandbox_mode == 'test')>{{ ___('On') }}</option>
                                                        <option
                                                            value="live" @selected(@$postOptions->restaurant_iyzico_sandbox_mode == 'live')>{{ ___('Off') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label
                                                        class="control-label">{{ ___('Iyzico API Key') }}</label>
                                                    <input name="restaurant_iyzico_api_key" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_iyzico_api_key}}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Iyzico Secret Key') }}</label>
                                                    <input name="restaurant_iyzico_secret_key" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_iyzico_secret_key}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="dashboard-box margin-top-0 margin-bottom-15 js-accordion-item">
                                    <!-- Headline -->
                                    <div class="headline js-accordion-header">
                                        <h3>{{ ___('Midtrans') }}</h3>
                                    </div>
                                    <div class="content with-padding padding-bottom-10 js-accordion-body"
                                         style="display: none">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Turn On/Off') }}</label>
                                                    <select name="restaurant_midtrans_install"
                                                            id="restaurant_midtrans_install"
                                                            class="selectpicker with-border">
                                                        <option
                                                            value="1" @selected(@$postOptions->restaurant_midtrans_install == 1)>{{ ___('On') }}</option>
                                                        <option
                                                            value="0" @selected(@$postOptions->restaurant_midtrans_install == 0)>{{ ___('Off') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Title') }}</label>
                                                    <input name="restaurant_midtrans_title" type="text"
                                                           class="with-border"
                                                           value="{{ @$postOptions->restaurant_midtrans_title }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Sandbox Mode') }}</label>
                                                    <select name="restaurant_midtrans_sandbox_mode"
                                                            class="selectpicker with-border">
                                                        <option
                                                            value="test" @selected(@$postOptions->restaurant_midtrans_sandbox_mode == 'test')>{{ ___('On') }}</option>
                                                        <option
                                                            value="live" @selected(@$postOptions->restaurant_midtrans_sandbox_mode == 'live')>{{ ___('Off') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label
                                                        class="control-label">{{ ___('Midtrans Client Key') }}</label>
                                                    <input name="restaurant_midtrans_client_key" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_midtrans_client_key}}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label
                                                        class="control-label">{{ ___('Midtrans Server Key') }}</label>
                                                    <input name="restaurant_midtrans_server_key" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_midtrans_server_key}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="dashboard-box margin-top-0 margin-bottom-15 js-accordion-item">
                                    <!-- Headline -->
                                    <div class="headline js-accordion-header">
                                        <h3>{{ ___('Paytabs') }}</h3>
                                    </div>
                                    <div class="content with-padding padding-bottom-10 js-accordion-body"
                                         style="display: none">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Turn On/Off') }}</label>
                                                    <select name="restaurant_paytabs_install"
                                                            id="restaurant_paytabs_install"
                                                            class="selectpicker with-border">
                                                        <option
                                                            value="1" @selected(@$postOptions->restaurant_paytabs_install == 1)>{{ ___('On') }}</option>
                                                        <option
                                                            value="0" @selected(@$postOptions->restaurant_paytabs_install == 0)>{{ ___('Off') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Title') }}</label>
                                                    <input name="restaurant_paytabs_title" type="text"
                                                           class="with-border"
                                                           value="{{ @$postOptions->restaurant_paytabs_title }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Paytabs Region') }}</label>
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
                                                    <select name="restaurant_paytabs_region"
                                                            id="restaurant_paytabs_region"
                                                            class="selectpicker with-border">
                                                        @foreach($regions as $key => $region)
                                                            <option
                                                                value="{{$key}}"
                                                                @selected(@$postOptions->restaurant_paytabs_region == $key)>
                                                                {{$region['title']}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label
                                                        class="control-label">{{ ___('Paytabs Profile ID') }}</label>
                                                    <input name="restaurant_paytabs_profile_id" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_paytabs_profile_id}}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label
                                                        class="control-label">{{ ___('Paytabs Server Key') }}</label>
                                                    <input name="restaurant_paytabs_secret_key" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_paytabs_secret_key}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="dashboard-box margin-top-0 margin-bottom-15 js-accordion-item">
                                    <!-- Headline -->
                                    <div class="headline js-accordion-header">
                                        <h3>{{ ___('Telr') }}</h3>
                                    </div>
                                    <div class="content with-padding padding-bottom-10 js-accordion-body"
                                         style="display: none">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Turn On/Off') }}</label>
                                                    <select name="restaurant_telr_install"
                                                            id="restaurant_telr_install"
                                                            class="selectpicker with-border">
                                                        <option
                                                            value="1" @selected(@$postOptions->restaurant_telr_install == 1)>{{ ___('On') }}</option>
                                                        <option
                                                            value="0" @selected(@$postOptions->restaurant_telr_install == 0)>{{ ___('Off') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Title') }}</label>
                                                    <input name="restaurant_telr_title" type="text"
                                                           class="with-border"
                                                           value="{{ @$postOptions->restaurant_telr_title }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Sandbox Mode') }}</label>
                                                    <select name="restaurant_telr_sandbox_mode"
                                                            class="selectpicker with-border">
                                                        <option
                                                            value="test" @selected(@$postOptions->restaurant_telr_sandbox_mode == 'test')>{{ ___('On') }}</option>
                                                        <option
                                                            value="live" @selected(@$postOptions->restaurant_telr_sandbox_mode == 'live')>{{ ___('Off') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label
                                                        class="control-label">{{ ___('Telr Store ID') }}</label>
                                                    <input name="restaurant_telr_store_id" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_telr_store_id}}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label
                                                        class="control-label">{{ ___('Telr Auth Key') }}</label>
                                                    <input name="restaurant_telr_authkey" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_telr_authkey}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="dashboard-box margin-top-0 margin-bottom-15 js-accordion-item">
                                    <!-- Headline -->
                                    <div class="headline js-accordion-header">
                                        <h3>{{ ___('2checkout') }}</h3>
                                    </div>
                                    <div class="content with-padding padding-bottom-10 js-accordion-body"
                                         style="display: none">
                                        <p>
                                            <small><strong>
                                                    {{___('Get the API details from')}} <a
                                                        href="https://secure.2checkout.com/cpanel"
                                                        target="_blank">{{___('here')}} <i
                                                            class="far fa-external-link"></i></a>
                                                </strong></small>
                                        </p>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Turn On/Off') }}</label>
                                                    <select name="restaurant_2checkout_install"
                                                            id="restaurant_2checkout_install"
                                                            class="selectpicker with-border">
                                                        <option
                                                            value="1" @selected(@$postOptions->restaurant_2checkout_install == 1)>{{ ___('On') }}</option>
                                                        <option
                                                            value="0" @selected(@$postOptions->restaurant_2checkout_install == 0)>{{ ___('Off') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Title') }}</label>
                                                    <input name="restaurant_2checkout_title" type="text"
                                                           class="with-border"
                                                           value="{{ @$postOptions->restaurant_2checkout_title }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Sandbox Mode') }}</label>
                                                    <select name="restaurant_2checkout_sandbox_mode"
                                                            class="selectpicker with-border">
                                                        <option
                                                            value="sandbox" @selected(@$postOptions->restaurant_2checkout_sandbox_mode == 'sandbox')>{{ ___('On') }}</option>
                                                        <option
                                                            value="production" @selected(@$postOptions->restaurant_2checkout_sandbox_mode == 'production')>{{ ___('Off') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label
                                                        class="control-label">{{ ___('2Checkout Account Number') }}</label>
                                                    <input name="restaurant_2checkout_account_number" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_2checkout_account_number}}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Publishable Key') }}</label>
                                                    <input name="restaurant_2checkout_public_key" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_2checkout_public_key}}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Private API Key') }}</label>
                                                    <input name="restaurant_2checkout_private_key" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_2checkout_private_key}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="dashboard-box margin-top-0 margin-bottom-15 js-accordion-item">
                                    <!-- Headline -->
                                    <div class="headline js-accordion-header">
                                        <h3>{{ ___('CCAvenue') }}</h3>
                                    </div>
                                    <div class="content with-padding padding-bottom-10 js-accordion-body"
                                         style="display: none">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Turn On/Off') }}</label>
                                                    <select name="restaurant_ccavenue_install"
                                                            id="restaurant_ccavenue_install"
                                                            class="selectpicker with-border">
                                                        <option
                                                            value="1" @selected(@$postOptions->restaurant_ccavenue_install == 1)>{{ ___('On') }}</option>
                                                        <option
                                                            value="0" @selected(@$postOptions->restaurant_ccavenue_install == 0)>{{ ___('Off') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label class="control-label">{{ ___('Title') }}</label>
                                                    <input name="restaurant_ccavenue_title" type="text"
                                                           class="with-border"
                                                           value="{{ @$postOptions->restaurant_ccavenue_title }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label
                                                        class="control-label">{{ ___('CCAvenue Merchant key') }}</label>
                                                    <input name="restaurant_ccavenue_merchant_key" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_ccavenue_merchant_key}}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label
                                                        class="control-label">{{ ___('CCAvenue Access Code') }}</label>
                                                    <input name="restaurant_ccavenue_access_code" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_ccavenue_access_code}}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="submit-field">
                                                    <label
                                                        class="control-label">{{ ___('CCAvenue Working Key') }}</label>
                                                    <input name="restaurant_ccavenue_working_key" type="text"
                                                           class="with-border"
                                                           value="{{@$postOptions->restaurant_ccavenue_working_key}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <button type="submit" name="submit" class="button ripple-effect margin-top-30">{{ ___('Save') }}</button>

    </form>

@endsection

@push('scripts_vendor')
    <link rel="stylesheet"
          href="{{ asset($activeThemeAssets.'css/color-picker.min.css') }}">
    <script
        src="{{ asset($activeThemeAssets.'js/color-picker.es5.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/tinymce/tinymce.min.js') }}"></script>
@endpush

@push('scripts_at_bottom')
    <script>
        initColorPicker('.qr-restaurant-color-wrapper');

        function initColorPicker(container) {
            var $element = container + ' .bm-color-picker';
            var $input = jQuery($element).siblings('.color-input');
            var picker = Pickr.create({
                container: container,
                el: $element,
                theme: 'monolith',
                comparison: false,
                closeOnScroll: true,
                position: 'bottom-start',
                default: $input.val() || '#333333',
                components: {
                    preview: false,
                    opacity: false,
                    hue: true,
                    interaction: {
                        input: true
                    }
                }
            });
            picker.on('change', function (color, instance) {
                $input.val(color.toHEXA().toString()).trigger('change');
            });
        }

        @if(config('settings.restaurant_text_editor'))
        tinymce.init({
            selector: '.tiny-editor',
            height: 250,
            resize: true,
            plugins: 'quickbars image advlist lists autolink link wordcount help searchreplace media',
            toolbar: [
                "bold italic underline strikethrough | alignleft aligncenter alignright  | link image media | bullist numlist | removeformat"
            ],
            menubar: "",
            // link
            relative_urls: false,
            remove_script_host: false,
            convert_urls: false,
            link_assume_external_targets: true,
            // images
            image_advtab: true,
            extended_valid_elements: 'i[*]',
            content_style: 'body { font-size:16px }',
            smart_paste: false,
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave();
                });
            },
            @if(current_language()->direction == 'rtl')
            directionality: "rtl"
            @endif
        });
        @endif

        $('[name="restaurant_delivery_order"]').on('change', function () {

            if ($(this).val() == '1')
                $('#delivery-field').slideDown();
            else
                $('#delivery-field').slideUp();
        }).trigger('change');

        $('[name="restaurant_online_payment"]').on('change', function () {

            if ($(this).val() == '1')
                $('#payment-gateways').slideDown();
            else
                $('#payment-gateways').slideUp();
        }).trigger('change');

        $('[name="restaurant_template"]').on('change', function () {

            if ($(this).val() == 'classic-theme')
                $('#menu-layout-field').slideDown();
            else
                $('#menu-layout-field').slideUp();
        })
        $('[name="restaurant_template"]:checked').trigger('change');

        $('[name="menu_languages[]"]').on('change', function () {
            let $select = $('[name="default_language"]'),
                default_language = $select.val(),
                languages = $(this).val();

            // Clear existing options
            $select.empty();

            // Check if any options are selected
            if (languages && languages.length > 0) {
                // Add new options based on selected values
                $.each(languages, function (index, value) {
                    $select.append($('<option>', {
                        value: value,
                        text: $(`[name="menu_languages[]"] [value="${value}"]`).html()
                    }));
                });

                $select.val(default_language).selectpicker('refresh');
            } else {
                // Add a default option if no options are selected
                $select.append($('<option>', {
                    value: '',
                    text: @json(___('Please select menu languages first'))
                })).selectpicker('refresh');
            }
        });
    </script>
@endpush
