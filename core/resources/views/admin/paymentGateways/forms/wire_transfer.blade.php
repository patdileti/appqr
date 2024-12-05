<div class="mb-3">
    <label class="form-label" for="company_bank_info">{{___('Bank Information')}} *</label>
    <textarea id="company_bank_info" name="company_bank_info" rows="6" type="text" class="form-control tiny-editor" required>{{@$settings->company_bank_info}}</textarea>
</div>
<script src="{{ asset('assets/admin/plugins/tinymce/tinymce.min.js') }}"></script>
<script>
    if($('.tiny-editor').length){
        tinymce.init({
            selector: '.tiny-editor',
            height: 500,
            resize: true,
            plugins: 'quickbars image advlist lists code table codesample autolink link wordcount fullscreen help searchreplace media',
            toolbar:[
                "bold italic underline strikethrough | alignleft aligncenter alignright  | link image media",
                "removeformat | table | bullist numlist | code fullscreen"
            ],
            menubar: "",
            // link
            relative_urls : false,
            remove_script_host : false,
            convert_urls : false,
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
            }
        });
    }
</script>
