$(function () {

    /* Extra Form Submit */
    $(".extra-ajax-form-submit").on('submit', function (e) {
        e.preventDefault();

        let $form = $(this);

        $form.find('button').addClass('button-progress').prop('disabled', true);
        $.ajax({
            type: "POST",
            url: $form.attr('action'),
            data: new FormData($form.get(0)),
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {
                    $form.closest('.dashboard-box')
                        .find('.extra-title')
                        .text(
                            response.title || $form.find('[name="title"]').val()
                        );

                    quick_alert(response.message);
                } else {
                    quick_alert(response.message, 'error');
                }
                $form.find('button').removeClass('button-progress').prop('disabled', false);
            }
        });
        return false;
    });

    /* Delete ajax */
    $('.delete-item').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var id = $(this).data('id'),
            $this = $(this);

        if (confirm(LANG_ARE_YOU_SURE)) {
            $this.addClass('button-progress').prop('disabled', true);
            $.ajax({
                type: "POST",
                url: $this.attr('href'),
                data: {
                    id: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function (response) {
                    $this.removeClass('button-progress').prop('disabled', false);
                    if (response.success) {
                        $this.closest('.dashboard-box').slideUp();
                        quick_alert(response.message)
                    } else {
                        quick_alert(response.message, 'error')
                    }

                }
            });
        }

    });
});