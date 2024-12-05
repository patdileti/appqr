<script type="text/javascript">
    "use strict";
    const BASE_URL = "{{ url(admin_url()) }}";
    const PRIMARY_COLOR = "{{ $settings->theme_color }}";
</script>
@stack('scripts_at_top')
<script src="{{ asset('assets/global/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/global/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/admin/js/tippy.all.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/jquery-slidePanel.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/sidePanel.js') }}"></script>
<script src="{{ asset('assets/admin/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/global/js/jquery.form.js') }}"></script>

<script src="{{ asset('assets/admin/js/clipboard.min.js') }}"></script>

<script src="{{ asset('assets/admin/js/helpers.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/admin/js/menu.js') }}"></script>
<script src="{{ asset('assets/admin/js/main.js') }}"></script>
@stack('scripts_vendor')
<script src="{{ asset('assets/admin/js/admin-ajax.js') }}"></script>
<script src="{{ asset('assets/admin/js/script.js') }}"></script>
<script src="{{ asset('assets/admin/js/quicklara.js') }}"></script>

@stack('scripts_at_bottom')

@if(\Session::has('quick_alert_message'))
    <script>
        quick_alert(@json(\Session::get('quick_alert_message')), '{{ \Session::get('quick_alert_type') }}')
    </script>
@endif
