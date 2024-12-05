jQuery(function ($) {

    /* Post Delete */
    $('#js-table-list').on('click', '.item-ajax-button', function (e) {
        // Keep ads item click from being executed.
        e.stopPropagation();
        // Prevent navigating to '#'.
        e.preventDefault();
        // Ask user if he is sure.
        var action = $(this).attr('href');
        var alert_mesg = $(this).data('alert-message');
        var $item = $(this).closest('.ajax-item-listing');
        if (confirm(alert_mesg)) {
            $.ajax({
                type: "DELETE",
                url: action,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        Snackbar.show({text: response.message});
                        $item.remove();
                    } else {
                        Snackbar.show({text: response.message});
                    }
                }
            });
        }
    });

    /* newsletter form */
    $('#newsletter-form').on('submit', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var data = new FormData(this),
            $form = $(this);

        var $btn = $(this).find('.button'),
            $error = $(this).find('.invalid-tooltip');
        $btn.addClass('button-progress').prop('disabled', true);

        $error.hide();
        $.ajax({
            type: "POST",
            url: $form.attr('action'),
            data: data,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                $btn.removeClass('button-progress').prop('disabled', false);
                if (response.success) {
                    $form.find('.valid-tooltip').removeClass('d-none');

                } else {
                    $error.text(response.message).show();
                }
                setTimeout(function () {
                    $form.find('.valid-tooltip').addClass('d-none');
                    $error.hide();
                    $form.trigger("reset");
                }, 2000);
            },
            error: function (xhr) {
                $btn.removeClass('button-progress').prop('disabled', false);
                $error.text(xhr.responseJSON.message).show();
            },
        });
    });

    /* Hearbeat */
    function quick_heartbeat() {
        $.ajax({
            type: "GET",
            url: heartBeatRoute,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                manage_orders(response.orders);
                manage_waiter_calls(response.waiterCalls);
            }
        });
    }

    setInterval(quick_heartbeat, 10000);

    // get all the order notifications
    var audioogg = new Audio(assetsUrl + '/global/audio/message.ogg');
    var audiomp3 = new Audio(assetsUrl + '/global/audio/message.mp3');
    var callWaiterMp3 = new Audio(assetsUrl + '/global/audio/call-waiter.mp3');

    /* Manage new orders */
    function manage_orders(orders) {
        if(!$('#qr-orders-table').length) {
            if (orders) {
                if (localStorage.notification_sound == 1) {
                    audiomp3.play().catch(function () {
                        quick_alert(LANG_NEW_ORDERS, 'error');
                    });
                    audioogg.play().catch(function () {
                        quick_alert(LANG_NEW_ORDERS, 'error');
                    });
                }
            }
        }
    }

    alertify.closeLogOnClick(true);
    alertify.delay(2000000);
    alertify.maxLogItems(10);

    /* Manage waiter calls */
    function manage_waiter_calls(response) {
        if (!jQuery.isEmptyObject(response)) {
            callWaiterMp3.play().catch(function () {});

            for (var i in response) {
                if (response.hasOwnProperty(i)) {
                    var message = response[i];
                    alertify.success(message);
                }
            }
        }
    }
});
