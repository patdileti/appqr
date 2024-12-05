jQuery(function ($) {
    "use strict";

    /**************
     * copy shortcode
     * *************/
    $('.quick-shortcode-box button').on('click',function () {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(this).data('code')).select();
        document.execCommand("copy");
        $temp.remove();
    });

    // translate picker
    $(document).off('change', ".translate-picker select").on('change', ".translate-picker select", function (e) {
        $('.translate-fields').hide();
        $('.translate-fields-' + $(this).val()).show();
        $('.translate-picker select').val($(this).val());
    });

    /**************
     * Select2
     * *************/
    let select2 = $(".select2");
    if (select2.length) {
        select2.select2();
    }

    /**************
     * tinymce editor
     * *************/
    if($('.tiny-editor').length){
        tinymce.init({
            selector: '.tiny-editor',
            height: 500,
            resize: true,
            plugins: 'quickbars image advlist lists code table codesample autolink link wordcount fullscreen help searchreplace media',
            toolbar:[
                "bold italic underline strikethrough | alignleft aligncenter alignright  | link image media | removeformat | table | bullist numlist | code fullscreen"
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

    /**************
     * selectFileBtn
     * *************/
    let selectFileBtn = $('#selectFileBtn'),
        selectedFileInput = $("#selectedFileInput"),
        filePreviewBox = $('.file-preview-box'),
        filePreviewImg = $('#filePreview');

    selectFileBtn.on('click', function() {
        selectedFileInput.trigger('click');
    });

    selectedFileInput.on('change', function() {
        var file = true,
            readLogoURL;
        if (file) {
            readLogoURL = function(input_file) {
                if (input_file.files && input_file.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        filePreviewBox.removeClass('d-none');
                        filePreviewImg.attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input_file.files[0]);
                }
            }
        }
        readLogoURL(this);
    });


});
