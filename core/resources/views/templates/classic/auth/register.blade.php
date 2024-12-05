@extends($activeTheme.'layouts.auth')
@section('title', ___('Sign Up'))
@section('content')

    <!-- Welcome Text -->
    <div class="welcome-text">
        <h3 style="font-size: 26px;">{{ ___("Let's create your account!") }}</h3>
        <span>{{ ___('Already have an account?') }} <a href="{{ route('login') }}">{{ ___('Log In!') }}</a></span>
    </div>
    @if(@$settings->facebook_login == "1" || @$settings->google_login == "1")
        <div class="social-login-buttons text-center">
            @if(@$settings->facebook_login == "1")
                <button class="facebook-login ripple-effect" onclick="location.href = '{{ route('social.login','facebook') }}'"><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+CjxyZWN0IHg9IjIiIHk9IjIiIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMC4xMjU4IiBmaWxsPSJ1cmwoI3BhdHRlcm4wKSIvPgo8ZGVmcz4KPHBhdHRlcm4gaWQ9InBhdHRlcm4wIiBwYXR0ZXJuQ29udGVudFVuaXRzPSJvYmplY3RCb3VuZGluZ0JveCIgd2lkdGg9IjEiIGhlaWdodD0iMSI+Cjx1c2UgeGxpbms6aHJlZj0iI2ltYWdlMF8xMDAxNF8zNjIwMiIgdHJhbnNmb3JtPSJzY2FsZSgwLjAwNjI4OTMxIDAuMDA2MjUpIi8+CjwvcGF0dGVybj4KPGltYWdlIGlkPSJpbWFnZTBfMTAwMTRfMzYyMDIiIHdpZHRoPSIxNTkiIGhlaWdodD0iMTYwIiB4bGluazpocmVmPSJkYXRhOmltYWdlL3BuZztiYXNlNjQsaVZCT1J3MEtHZ29BQUFBTlNVaEVVZ0FBQUo4QUFBQ2dDQVlBQUFBU043NllBQUFBQ1hCSVdYTUFBQmNSQUFBWEVRSEtKdk0vQUFBSzdVbEVRVlI0bk8yZFg0aGNWeDNIei82SjdpYk1idElHNjFocXB3VkJNWmlWMHBlK1pQTW1GTW1HNGtPbGtBMkliK0lLTFF3K2JSQnhYc1FwUGdoUzZDN29ndy9pNUVGQm55YWdCVkZ4STJvZjFHYlh0TDAyYnByTVRzd203ZTZNSFAwTmU1bHo3cDM3NS95OTUvdUJZWko3Nyt6ZW1mM003L3o3blhPbWhzTWhBOEFHMC9qVWdTMGdIN0FHNUFQV2dIekFHclA0Nkkrb04vdExqTEdUakxGbGVsNmlrdzNHMkpQQ0M5SzVSbWUzNDQrb1ZldW12aW9nZ20zdGttakxKQmgvbkJVdTBzY09ZMnlMSHQxUWhReEd2bnF6ejZQWENnbkhINHZDUlhiaGtaSkwySWxhdFMzSDdrMExsWmFQb3RzcXlXWXlzcFdseHlVa0VUc2UzWGN1S2lkZkxNS3RGYWludWNoSXhIYlZJbUpsNUtzMyt5c1U1UzRJSjZzRHJ5dTJHV01iVWF0MjEvZDM1YlY4OVdiL0pFVzU5WXBFdWF5TW91RjYxS3B0KzNITElsN0tSOUt0MGNPMWhvTnBObjJWMER2NTZzMytPcVNUNHAyRTNzaFhiL1pYQXl4ZWkzQ0ZHaWZPMXdtZGw0KzZTM2dsKzV4d0VpVEI2NFJyVWF1MmtYRGVDWnlWaitwMVBOSjlYVGdKc3NJN3JsZGRMWXFkVEN5b04vdkxOUFFFOGNyQlM0c2JWRTkyRHVjaVg3M1piME02TFRnWEJaMlJqK3AyRzU0TmcvbUdVM1ZCSjRwZGFzbDJJWjUyZVBmVTYvVm0zd241ckVjK0ZMUFd1TTRUTG14MnlWaVRqMXF6R3hVZmkzV2RIZ2xvSldIQlNyRkw0blVobm5WNE1keWxwQXpqR0plUEdoYmJxTjg1QXhmd1oxVHZOb3BSK1VpOExzWmxuZVIxMHdJYWt3L2llWUZSQVkzSUIvRzh3cGlBMnVXRGVGNWlSRUN0WFMwMG4ySUw0bm5MWloyaklkcmtpM1dub0ZYck4rZDF6U3ZXV2V4Q3ZHclFvYXFUY3JUSVIyT0hFSzhhTEpLQUoxVy9HK1h5VVVYMWtuQUMrTXlUTkZ0T0tVcnJmQlNlL3lpY0NKalBmbUthTFQwK3MvK1ordlN0YzUrYW5aMlpaZ2VQbnBoYVdKaWJPalgrcVh4NHlQYmZ2anU0TmZyLzczY09aL2NlREEvZTJ4c3UvT0dmaDZkNkQ0YnNMKzhPYkg2WVY2SldUVmxpcWpMNUtDeHZoVDdCWjJGK2luM3h6T3oraTg4ZTIvM2M0ek9uajgyd2VlR2lrdHg3T0x6OTczdkRlMXpPdjkwYUhQOSs5d05CWkkwb2E0Q29sSzhUY3FMQWMwL1BzRzkrNGFNM24vbmt6QlBDU2MzVW0zMlR2NDVud2pSVXBHSXBXWitQc2lLQ0ZJOUw5KzBMYys5OStySHB4eGhqeHNXendDS2x3cFhPaENrZCthaTQzUTZ0SS9tSlU5T3MvYVc1M2VlZW5qa3RuRFNNNGNnMzRtTFpGYlJVdEhZM1FoUHYrVE96OTMvejhvbDlGOFN6eUViWjdwZFM4b1ZZM0g3M2hibmQxMTZhUDY2akllRVppelNadnpDRjVTUHJTLzF5MzNqdHBmbjN2L3pzc1pDajNUaVhhSTUxSWNwRXZxb3N2cGdKTHQ3eloyWWY4ZUJXVFZNNEFCV1NqN0pWMW9RVEZZVVh0UkF2a2JORjA2K0tScjcxVUJvWnZIR0JvbllpN1NLTmo5enlVZFFMWXV5V2Q2Zjg0TVg1S2VFRUdHZXhTRWxZSlBJNXVlaU1EbmcvSGxxMW1WbkxHLzF5eVJkUzFPTWpGNEgzNCtVbGQvVExHL21DaVhwOHlFdzRDQ2FoUno0S3FjRkVQUnFyQmZsWXpOUHl6UlA1Z3VsYTRka3B3a0dRbGN5ZTVKSFArSElLTnVENWVEYlNvaXJFMmF5akhwbmtvekhjSUVZemVDS29jQkRrSlZPZ3locjVnb2g2SEo2QkxCd0VlYm1VcGR0bG9uejBRNExKWE9HcDc4SkJVSVNKeWFaWk1wbXRyTjFtQXo3WngxYW44dDZENFoxZi92WGcvbTl2SEQ1eTQvYmdmL2Znd0lTaE1xeFFybWNpa0M4R24yWEdtRm41M3UwTmQ3LzJrLzNUYjd4MXlDY0JtWndJcEpzTHZOUk1tK3VSV3V5R1Z1VHk2WTNDUVkzOC9NOEg3ei96blh0Y1BOTnYxUlNwZ1N0VnZwQ2lIb2ZQcXhVT2FvS0w5NVVmN1ZjOVRTdlZuMG55RmM1UzlSRStvZHZFYmZmMmg3MEF4R09UU2sxRXZoanp4NmJtaElNYStOWXZIbjdFempzMFQxcUhjNko4dFBSRlVMUFNQcjR3cFgwOGx5K0o4ZVBmZlJoU21sWisrZEplQklyenAzY09RK3ZFVHZRSThobG02KzFEYnp2dUNwSzRUM0thZkZvV0JBeWRONlBCeDBMN0NKTHFmVkw1cUg4UDI4bHJZRFI2RVJqU1FDYVZMK2xpQUFyU2tMMHNTVDVwbUFTZ0lOSmdsaVNmOHZWM1FkQklHeDFKOGtsTkJhQW9zdnkrSlBta1pUUUFKUkFDV3BKOGFPa0M3UWp5eWNJakFBb1FHckdDZkxMd0NJQU9aUElCb0FPaFJJVjh3QlJDaVNxVFQ3Z0lBQjNJNUJQQ0l3Q201QVBBQ0pBUFdNUFliQzFkdlBIS2laMm5IcDMycGxQOHAxODlMaHdyaTZVZGlFcUR5T2M1ZkU2SXIrOEE4bmxPZkg5ZTM1REpsN2k4QVhBUHZ1ZHVsZVRiRW80QVorR2JQZnY2MTVISkJ6eUNiMy92eWQwS1FRM3llYzdOTzJwMmlqZUFVSjJUeVNjWUN0emw1aDEvcHdFTDhxV3Rwd2JjNHNidHdZNUhmNUx1K0FGQlBzS25OeFVzYiswT2t2NStYcEIwODl2Q0VlQWNmNzgxOEdtMUs2RTZseVNmY0NGd2oxKzllZUROTGtteTZseVNmTUtGd0QzNGd1R2VjRTEybTBueUNaVkQ0QjRlclZRdkxVbVQ1Sk5lRE56aFgzdERuM2JGbExZaHBQSlIrWXdXcjhPOGMzZndnVWUzS3cxbVV2blNYZ0Rjd0tkRkpxTldUVnFOUzVOUCtnTGdCci8reDZFdkNiVFN4Z2FEZlA2eXQrOU5TemZSbzBUNW9sYU5GN3M5NFFSd0FvOTJMY292SDlFUmpqakd3d05tWk84TWw3aHpmK2pOcEkyaytoN0xNSUdJdi9DU2NOUWh6bi92UDhwNithTldUVGltbWhkK2VGOUYxTkovbzJxNG12WlR2STk4d0dsUy9VbVZqL3I3VXUwRklJWGk4aEdwUHdDQUJLN0trZ2tnSHpEQlJHOG15b2VpRnhTa3ZIeEU2bDc1QUl5eG1XVTZSaWI1b2xhdGcwUURrSU5Nd1NyUEhBQkVQNUNGNjJrZHkzSHl5TmNXamdBZ2t0bVR6UEpSR2I0cG5BRGdpSjJvVmN0Y1F1YWRlcmN1SEFIZ2lGeFZzMXp5UmEzYU5xSWZTS0NYdDJwV1pOSXhvaCtRMGM2NzJrVnUrUkQ5Z0lTZElnM1Nvc3N0ckNQUkZNUllMN0xHVHlINUtQcWg2d1V3NnRjcjFBZGNacUdaTmtZOUFHTnNyZWlIVUZnK0NyT3J3Z2tRRXB0WlJ6TmtsRnBpaTM0eE1sN0NwRmNtNmpGRnkrS3VvdkVSSkt0bEZ4SXRMUitLM3lDNVNwbE9wVkN5c2lYZENQcit3cUNuS3Rpb1hGWjFEYTNmSUZoUnRXNjNNdm5vaGxhRUU2QktYQ25UdWgxSDZZTFN0TVRHWmVFRXFBTFhvbFpONmJpKzh0WE1xYmNiOWI5cWNWMUhxYVpsS2Yyb1ZWdE5XeG9MZUVWUFJiZUtESjM3T0t6UU53YjR6UXBWcDVTalRUNzZwaXlqQTlwckxxdHNZSXlqZFFjYkNPZzFsNHRtcTJSRisvWkpGTElob0Y5b0Y0K1oydklVQW5xRkVmR1l5ZjEySWFBWEdCT1BtZDdzT1NZZ2h1SGN3Nmg0ek1aTzR5VGdFcnBobklHWFJCZE5pOGRzYlhNZmF3VWpFZFV1WEx4bEZlbFJSYkMyV1RBWE1HclZlRWYwcThKSllBSmU4alIwZFNCbndmcE8xVkdyeGxPeExxSWhZcFJYbzFadFNjZVFXUjZjMkNhZHdqN3FnZm9aMWU5S3piMVFoVE43OVBPNXdQemJ5SFBHaEpOQUJUelJZOGxXL1U2R00vS05vSnl4OCtpT1VRYVBkdCtJV3JWbG11enZETTdKeDQ2bVpDSUtsbWNVN1p4Y1hjSkorZGhSYTVoSHdjOGpOekEzUGVvMGRpN2F4WEZXdmhHOEs0Qi9pTlFpUmxHY1RvOUtpNGFOVHVPOFROcjR6eG1vb3R5cE4vdHJ0RXJXb2kvM2JvaE5XaTNLMlVnM2p2T1JieHlxdnpUb0c0Nit3ZjlMOXhTZnV1Q1RlTXlueUJlSE9rZDU5RnV2Ti91cjlHOWZ0bjFYUVkvV1AyNzdKbHdjTCtXTFEzV2JqWHF6djB3VDF5OElGMVdINjdRMFhjZjI2SVFLdkpkdkJIWFBkT3ZOZm9NbUwvR0llRmE0MEQ5NnRJOVoyK1k0ckE0cUk5K0kyS3FwN1hxenYwUWlybmdtNGtpNGprc2pFcXFwbkh4eEtGSnNVZDJ3UVdsY0svVHNXbXVaOTJWMlNiaEtSYmdrS2kxZkhJcUlHNk9OU2lncXhoL25oQmZwZy9kWGpyNFlYWjNURTExbWFqZ2NodmkrazJqUVk1bk9qNTRiT1Z2VFBSS0wwZlBkMkhPUW9zbUFmTUFhM25VeWcrb0ErWUExSUIrd0J1UURkbUNNL1JjenBaTDVrNUxrcFFBQUFBQkpSVTVFcmtKZ2dnPT0iLz4KPC9kZWZzPgo8L3N2Zz4K">  {{ ___('Log In via Facebook') }}</button>
            @endif
            @if(@$settings->google_login == "1")
                <button class="google-login ripple-effect" onclick="location.href = '{{ route('social.login','google') }}'"><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0yMC42NCAxMi4yMDQ3QzIwLjY0IDExLjU2NjUgMjAuNTgyNyAxMC45NTI5IDIwLjQ3NjQgMTAuMzYzOEgxMlYxMy44NDUxSDE2Ljg0MzZDMTYuNjM1IDE0Ljk3MDEgMTYuMDAwOSAxNS45MjMzIDE1LjA0NzcgMTYuNTYxNVYxOC44MTk3SDE3Ljk1NjRDMTkuNjU4MiAxNy4yNTI5IDIwLjY0IDE0Ljk0NTYgMjAuNjQgMTIuMjA0N1oiIGZpbGw9IiM0Mjg1RjQiLz4KPHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xMS45OTk4IDIxLjAwMDFDMTQuNDI5OCAyMS4wMDAxIDE2LjQ2NyAyMC4xOTQyIDE3Ljk1NjEgMTguODE5NkwxNS4wNDc1IDE2LjU2MTRDMTQuMjQxNiAxNy4xMDE0IDEzLjIxMDcgMTcuNDIwNSAxMS45OTk4IDE3LjQyMDVDOS42NTU2NyAxNy40MjA1IDcuNjcxNTggMTUuODM3NCA2Ljk2Mzg1IDEzLjcxMDFIMy45NTcwM1YxNi4wNDE5QzUuNDM3OTQgMTguOTgzMyA4LjQ4MTU4IDIxLjAwMDEgMTEuOTk5OCAyMS4wMDAxWiIgZmlsbD0iIzM0QTg1MyIvPgo8cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGNsaXAtcnVsZT0iZXZlbm9kZCIgZD0iTTYuOTY0MDkgMTMuNzA5OEM2Ljc4NDA5IDEzLjE2OTggNi42ODE4MiAxMi41OTMgNi42ODE4MiAxMS45OTk4QzYuNjgxODIgMTEuNDA2NiA2Ljc4NDA5IDEwLjgyOTggNi45NjQwOSAxMC4yODk4VjcuOTU4MDFIMy45NTcyN0MzLjM0NzczIDkuMTczMDEgMyAxMC41NDc2IDMgMTEuOTk5OEMzIDEzLjQ1MjEgMy4zNDc3MyAxNC44MjY2IDMuOTU3MjcgMTYuMDQxNkw2Ljk2NDA5IDEzLjcwOThaIiBmaWxsPSIjRkJCQzA1Ii8+CjxwYXRoIGZpbGwtcnVsZT0iZXZlbm9kZCIgY2xpcC1ydWxlPSJldmVub2RkIiBkPSJNMTEuOTk5OCA2LjU3OTU1QzEzLjMyMTEgNi41Nzk1NSAxNC41MDc1IDcuMDMzNjQgMTUuNDQwMiA3LjkyNTQ1TDE4LjAyMTYgNS4zNDQwOUMxNi40NjI5IDMuODkxODIgMTQuNDI1NyAzIDExLjk5OTggM0M4LjQ4MTU4IDMgNS40Mzc5NCA1LjAxNjgyIDMuOTU3MDMgNy45NTgxOEw2Ljk2Mzg1IDEwLjI5QzcuNjcxNTggOC4xNjI3MyA5LjY1NTY3IDYuNTc5NTUgMTEuOTk5OCA2LjU3OTU1WiIgZmlsbD0iI0VBNDMzNSIvPgo8L3N2Zz4K"> {{ ___('Log In via Google') }}</button>
            @endif
        </div>
        <div class="social-login-separator"><span>{{ ___('or') }}</span></div>
    @endif

    <form method="post" id="register-account-form" action="#" accept-charset="UTF-8">
        @csrf
        <div class="form-group">
            <div class="input-with-icon-left">
                <i class="la la-user"></i>
                <input type="text" class="input-text with-border" placeholder="{{ ___('Full Name') }}"
                       value="{{ old('name') }}" id="name" name="name" onBlur="checkAvailabilityName()" required/>
            </div>
            <span id="name-availability-status">@error('name')<span class='status-not-available'>{{ $message }}</span>@enderror</span>
        </div>
        <div class="form-group">
            <div class="input-with-icon-left">
                <i class="la la-user"></i>
                <input type="text" class="input-text with-border" placeholder="{{ ___('Username') }}"
                       value="{{ old('username') }}" id="Rusername" name="username" onBlur="checkAvailabilityUsername()"
                       required/>
            </div>
            <span id="user-availability-status">@error('username')<span
                    class='status-not-available'>{{ $message }}</span>@enderror</span>
        </div>
        <div class="form-group">
            <div class="input-with-icon-left">
                <i class="la la-envelope"></i>
                <input type="text" class="input-text with-border" placeholder="{{ ___('Email Address') }}"
                       value="{{ old('email') }}" name="email" id="email" onBlur="checkAvailabilityEmail()" required/>
            </div>
            <span id="email-availability-status">@error('email')<span class='status-not-available'>{{ $message }}</span>@enderror</span>
        </div>
        <div class="form-group">
            <div class="input-with-icon-left">
                <i class="la la-unlock"></i>
                <input type="password" class="input-text with-border" placeholder="{{ ___('Password') }}" id="Rpassword"
                       name="password" onBlur="checkAvailabilityPassword()" required/>
            </div>
            <span id="password-availability-status">@error('password')<span
                    class='status-not-available'>{{ $message }}</span>@enderror</span>
        </div>
        <div class="form-group margin-bottom-15">
            <div class="text-center">
                {!! display_captcha() !!}
                @error('g-recaptcha-response')
                <span class="status-not-available">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="checkbox">
            <input type="checkbox" id="agree_for_term" name="agree_for_term" value="1" required>
            <label for="agree_for_term"><span
                    class="checkbox-icon"></span> {!! ___('By clicking on “Register” button you are agree to our :TERMS.', ['TERMS' => '<a href="'.config('settings.termcondition_link').'">'.___('Terms and Conditions').'</a>']) !!}
            </label>
        </div>
        <input type="hidden" name="submit" value="submit">
        <button id="submit-btn" class="button full-width button-sliding-icon ripple-effect margin-top-10"
                type="submit">{{ ___('Register') }} <i class="icon-feather-arrow-right"></i></button>
    </form>

@endsection

@push('scripts_at_bottom')
    <script>
        "use strict";
        var error = "";

        function checkAvailabilityName() {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: @json(route('register.check_availability')),
                data: 'name=' + $("#name").val(),
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if (data != "success") {
                        error = 1;
                        $("#name").removeClass('has-success');
                        $("#name-availability-status").html(data);
                        $("#name").addClass('has-error mar-zero');
                    } else {
                        error = 0;
                        $("#name").removeClass('has-error mar-zero');
                        $("#name-availability-status").html("");
                        $("#name").addClass('has-success');
                    }
                    $("#loaderIcon").hide();
                },
                error: function () {
                }
            });
        }

        function checkAvailabilityUsername() {
            var $item = $("#Rusername").closest('.form-group');
            $("#loaderIcon").show();
            jQuery.ajax({
                url: @json(route('register.check_availability')),
                data: 'username=' + $("#Rusername").val(),
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if (data != "success") {
                        error = 1;
                        $item.removeClass('has-success');
                        $("#user-availability-status").html(data);
                        $item.addClass('has-error');
                    } else {
                        error = 0;
                        $item.removeClass('has-error');
                        $("#user-availability-status").html("");
                        $item.addClass('has-success');
                    }
                    $("#loaderIcon").hide();
                },
                error: function () {
                }
            });
        }

        function checkAvailabilityEmail() {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: @json(route('register.check_availability')),
                data: 'email=' + $("#email").val(),
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if (data != "success") {
                        error = 1;
                        $("#email").removeClass('has-success');
                        $("#email-availability-status").html(data);
                        $("#email").addClass('has-error mar-zero');
                    } else {
                        error = 0;
                        $("#email").removeClass('has-error mar-zero');
                        $("#email-availability-status").html("");
                        $("#email").addClass('has-success');
                    }
                    $("#loaderIcon").hide();
                },
                error: function () {
                }
            });
        }

        function checkAvailabilityPassword() {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: @json(route('register.check_availability')),
                data: 'password=' + $("#Rpassword").val(),
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if (data != "success") {
                        error = 1;
                        $("#Rpassword").removeClass('has-success');
                        $("#password-availability-status").html(data);
                        $("#Rpassword").addClass('has-error mar-zero');
                    } else {
                        error = 0;
                        $("#Rpassword").removeClass('has-error mar-zero');
                        $("#password-availability-status").html("");
                        $("#Rpassword").addClass('has-success');
                    }
                    $("#loaderIcon").hide();
                },
                error: function () {
                }
            });
        }

    </script>
@endpush
