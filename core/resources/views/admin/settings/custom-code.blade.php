<div class="tab-pane" id="quick_custom_code">
    <form method="post" class="ajax_submit_form" data-action="{{ route('admin.settings.update') }}" data-ajax-sidepanel="true">
        <div class="quick-card card">
            <div class="card-header">
                <h5>{{ ___('External Javascript or Css In header') }}</h5>
            </div>
            <div class="card-body">
                <div>
                    <textarea name="external_code" id="external_code" class="form-control" rows="5">{{ @$settings->external_code }}</textarea>
                    <small class="form-text">{{ ___('You can add any javascript code and style css. This code will be placed on the head part.') }}</small>
                </div>
            </div>
            <div class="card-footer">
                <input type="hidden" name="custom_code_setting" value="1">
                <button name="submit" type="submit" class="btn btn-primary">{{ ___('Save Changes') }}</button>
            </div>
        </div>
    </form>
</div>
@push('styles_vendor')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/codemirror/codemirror.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/codemirror/monokai.min.css') }}">
@endpush
@push('scripts_vendor')
    <script src="{{ asset('assets/admin/plugins/codemirror/codemirror.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/codemirror/css.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/codemirror/javascript.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/codemirror/htmlmixed.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/codemirror/sublime.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/codemirror/autorefresh.js') }}"></script>
@endpush
@push('scripts_at_bottom')
    <script>
        $(function() {
            var element = document.getElementById("external_code");
            var editor = CodeMirror.fromTextArea(element, {
                lineNumbers: true,
                mode: "text/javascript",
                theme: "monokai",
                keyMap: "sublime",
                autoCloseBrackets: true,
                matchBrackets: true,
                showCursorWhenSelecting: true,
                autoRefresh:true,
            });
        });
    </script>
@endpush
