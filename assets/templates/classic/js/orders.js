jQuery(function ($) {
    var $notification_sound = $('.order-notification-sound');

    var audioogg = new Audio(assetsUrl + '/global/audio/message.ogg');
    var audiomp3 = new Audio(assetsUrl + '/global/audio/message.mp3');

    localStorage.notification_sound = localStorage.notification_sound || 1;
    if (localStorage.notification_sound == 1) {
        $notification_sound.html('<i class="icon-feather-volume-2"></i>');
    } else {
        $notification_sound.html('<i class="icon-feather-volume-x"></i>');
    }

    /* complete order */
    $(document).on('click', '.qr-complete-order', function (e) {
        e.preventDefault();
        var $this = $(this);

        if (confirm(LANG_ARE_YOU_SURE)) {
            $this.addClass('button-progress').prop('disabled', true);
            $.ajax({
                type: "POST",
                url: $this.data('route'),
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        $this.closest('tr').find('.order-status')
                            .removeClass('gray').addClass('green')
                            .attr('title', LANG_COMPLETED)
                            .html('<i class="icon-feather-check"></i>');
                    }
                    $this.removeClass('button-progress').prop('disabled', false).remove();
                }
            });
        }
    });

    /* delete order */
    $(document).on('click', '.qr-delete-order', function (e) {
        e.preventDefault();
        var $this = $(this);
        if (confirm(LANG_ARE_YOU_SURE)) {
            $this.addClass('button-progress').prop('disabled', true);
            $.ajax({
                type: "POST",
                url: $this.data('route'),
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        $this.closest('tr').remove();
                    }
                    $this.removeClass('button-progress').prop('disabled', false);
                }
            });
        }
    });

    /* view order */
    $(document).on('click', '.qr-view-order', function (e) {
        e.preventDefault();
        var id = $(this).data('id'),
            $this = $(this);

        $('#order-print-content').html($('.order-print-tpl-' + id).html());

        $.magnificPopup.open({
            items: {
                src: '#view-order',
                type: 'inline',
                fixedContentPos: false,
                fixedBgPos: true,
                overflowY: 'auto',
                closeBtnInside: true,
                preloader: false,
                midClick: true,
                removalDelay: 300,
                mainClass: 'my-mfp-zoom-in'
            }
        });
    });

    /* mute notification */
    $(document).on('click', '.order-notification-sound', function (e) {
        e.preventDefault();
        if (localStorage.notification_sound == 1) {
            localStorage.notification_sound = 0;
            $notification_sound.html('<i class="icon-feather-volume-x"></i>');
        } else {
            localStorage.notification_sound = 1;
            $notification_sound.html('<i class="icon-feather-volume-2"></i>');
            audiomp3.play();
            audioogg.play();
        }
    });

    /* print order */
    $(document).on('click', '.order-print-button', function (e) {
        var mywindow = window.open('', 'qr_print', 'height=400,width=600');
        mywindow.document.write('<html><head><title>Print</title> <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><meta http-equiv="X-UA-Compatible" content="ie=edge">');
        mywindow.document.write('<link rel="stylesheet" href="' + assetsUrl + '/templates/' + template_name + '/css/style.css" type="text/css" />');
        mywindow.document.write('</head><body><div class="order-print">');
        mywindow.document.write($('.order-print').html());
        mywindow.document.write('</div></body></html>');

        mywindow.print();
        //mywindow.close();
        mywindow.document.close();
        //return true;
    });

    /* Hearbeat */
    function getOrders() {
        $.ajax({
            type: "GET",
            url: location.href,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                let orders = response.orders;
                if(!jQuery.isEmptyObject( orders )) {
                    for (var i in orders) {
                        if (orders.hasOwnProperty(i)) {
                            let $row = $(orders[i]);
                            $row.addClass('row-highlight');
                            $('#qr-orders-table').find('#order-rows').prepend($row);
                        }
                    }

                    $('.no-order-found').remove();

                    if(localStorage.notification_sound == 1) {
                        audiomp3.play().catch(function () {});
                        audioogg.play().catch(function () {});
                    }

                    setTimeout(function() {
                        $('.row-highlight').removeClass("row-highlight");
                    }, 1000);
                }
            }
        });
    }

    setInterval(getOrders, 10000);
});