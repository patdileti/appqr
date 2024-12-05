(function ($) {
    "use strict";

    // remove old localstorage data
    localStorage.setItem('quickqr_order', '[]');

    /* Check if the order paid */
    let current_url = new URL(window.location.href);

    if (current_url.searchParams.get('return') == 'success') {
        $('.your-order-content').hide();
        $('.order-success-message').show();

        $.magnificPopup.open({
            items: {
                src: '#your-order',
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

        current_url.searchParams.delete('return');
    }

    /* GALLERY - FILTERING FUCTION */
    $(".filter-button").on("click", function () {
        var value = $(this).data('filter');

        if (value == "gallery-show-all") {
            $('.boxed-list').removeClass("gallery-hidden");
        } else {
            $('.boxed-list:not([data-category-image="' + value + '"]').addClass("gallery-hidden");
            $('.boxed-list[data-category-image="' + value + '"]').removeClass("gallery-hidden");
        }
    });

    $('.filter-gallery .filter-button').on("click", function () {
        $('.filter-gallery').find('.filter-button.active').removeClass('active');
        $(this).addClass('active');
    });

    $(".menu-filter").on("click", function (e) {
        e.preventDefault();
        $('.menu-filter.active').removeClass('active');
        $(this).addClass('active');
        var $container = $(this).closest('.boxed-list');
        if ($(this).data('filter') == 'grid') {
            $container.find('.menu-grid-view').show();
            $container.find('.menu-list-view').hide();
        } else {
            $container.find('.menu-list-view').show();
            $container.find('.menu-grid-view').hide();
        }

    });

    /*
    * Add Order
    */
    $(document).on('click', ".add-extras", function (e) {
        e.preventDefault();

        $('#add-order-button').prop('disabled', false);
        var $item = $(this).closest('.ajax-item-listing'),
            item_id = $item.data('id'),
            name = $item.data('name'),
            description = $item.data('description'),
            price = $item.data('price'),
            amount = $item.data('amount'),
            order_price = Number(amount);

        $('#add-extras .menu_title').html(name);
        $('#add-extras .menu_desc').html(description);
        $('#add-extras .menu_price').html(price);
        $('#add-extras .menu_price').data('price', amount);
        $('#order-price').html(formatPrice(amount));
        $('#menu-order-quantity').val(1);

        /* Variants */
        var $variant_wrapper = $('#menu-variants');
        $variant_wrapper.html('');
        var variant_options = TOTAL_MENUS[item_id].variant_options || [];

        if (variant_options.length == 0) {
            $variant_wrapper.hide();
        } else {
            $variant_wrapper.show();
        }

        var js_variants = TOTAL_MENUS[item_id].variants || [],
            selected_variant = null,
            all_variants = [],
            available_variant_options = [];

        /* Variant */
        $.each(js_variants, function (key, item) {
            var variants_key = '';

            $.each(item['options'], function (variant_option_id, variant_option_key) {
                available_variant_options.push(variant_option_id + '-' + variant_option_key);
                variants_key += variant_option_id + '-' + variant_option_key + '-';
            });

            all_variants.push({
                id: item['id'],
                price: item['price'],
                key: variants_key
            });
        });

        for (var i in variant_options) {
            if (variant_options.hasOwnProperty(i)) {
                var $options_tpl = $(
                    '<div class="menu-data">' +
                    '<div class="section-headline margin-bottom-12">' +
                    '<h5 class="variant-option-title"></h5>' +
                    '</div>' +
                    '<div class="menu-variant-options">' +
                    '<div class="d-flex flex-column menu-variant-option">' +
                    /* Variant Radio options will come here */
                    '</div>' +
                    '</div>' +
                    '</div>');

                $options_tpl.find('.variant-option-title').html(variant_options[i].title);
                $options_tpl.data('id', variant_options[i].id);

                /* Variant Options */
                $.each(variant_options[i].options, function (key, item) {


                    var radio_key = variant_options[i].id + '-' + key;

                    if (available_variant_options.includes(radio_key)) {
                        var $radio_options = $(
                            `<div><div class="radio"><input id="radio-2" name="radio" type="radio" value=""><label for="radio-2"><span class="radio-label"></span> <span class="variant-option"></span></label></div></div>`
                        );

                        $radio_options.find('.radio input').attr('id', 'radio-' + radio_key);
                        $radio_options.find('.radio input').attr('value', radio_key);
                        $radio_options.find('.radio input').attr('name', 'variant-radio-' + variant_options[i].id);
                        $radio_options.find('.radio input').data('option-id', variant_options[i].id);
                        $radio_options.find('.radio input').data('option-key', key);
                        $radio_options.find('label').attr('for', 'radio-' + radio_key);
                        $radio_options.find('.variant-option').html(item);

                        $options_tpl.find('.menu-variant-option').append($radio_options);
                    }


                });

                $options_tpl.find('.radio input').on('click', function () {
                    var $radio = $(this);
                    let selected_element_item_option_id = $radio.data('option-id');
                    let selected_element_item_option_key = $radio.data('option-key');

                    /* Go through all variants */
                    $('input[name^="variant-radio-"]').each(function (key, item_option_element) {
                        var $item_option_element = $(item_option_element);
                        /* Avoid the already selected and the parents */
                        if (
                            (
                                $item_option_element.data('option-id') == selected_element_item_option_id
                                && $item_option_element.data('option-key') == selected_element_item_option_key
                            )
                            ||
                            (
                                selected_element_item_option_id > $item_option_element.data('option-id')
                            )
                        ) {
                            // nothing.
                        } else {

                            /* Deselect radio button */
                            $item_option_element.prop('checked', false);

                        }
                    });

                    let available_item_options = [];

                    /* Verify all the selected buttons */
                    let selected_potential_item_variant = '';

                    $('input[name^="variant-radio-"]:checked').each(function (key, item) {
                        selected_potential_item_variant += `${item.value}-`;
                    });

                    $.each(js_variants, function (key, item) {
                        let potential_item_variant = '';
                        let triggered = false;

                        $.each(item['options'], function (variant_option_id, variant_option_key) {
                            potential_item_variant += `${variant_option_id}-${variant_option_key}-`;

                            if (
                                selected_potential_item_variant == potential_item_variant
                                && selected_element_item_option_id == variant_option_id
                                && selected_element_item_option_key == variant_option_key
                            ) {
                                triggered = true;
                                return;
                            }

                            if (triggered) {
                                available_item_options.push(`${variant_option_id}-${variant_option_key}`);
                            }

                        });
                    });

                    /* Go through all variants */
                    $('input[name^="variant-radio-"]').each(function (key, item_option_element) {
                        var $item_option_element = $(item_option_element);
                        /* Verify if we can have the element active */
                        if ($item_option_element.data('option-id') > selected_element_item_option_id) {

                            /* Remove the disabled state as it can be potentially used */
                            $item_option_element.prop('disabled', false);
                            $item_option_element.parent('.radio').css('opacity', 1);

                            if (!available_item_options.includes($item_option_element.val())) {

                                /* Disable radio button */
                                $item_option_element.prop('disabled', true);
                                $item_option_element.parent('.radio').css('opacity', 0.5);

                            }
                        }
                    });

                    /* Check the clicked input */
                    $radio.prop('checked', true);

                    /* Reset the quantity */
                    $('#menu-order-quantity').val(1);

                    /* Verify all the selected buttons */
                    let potential_item_variant = '';

                    $('input[name^="variant-radio-"]:checked').each(function (key, element) {
                        potential_item_variant += `${element.value}-`;
                    });

                    let found_item_variant = all_variants.find(element => {
                        return element.key == potential_item_variant
                    });

                    /* Display the price in the listing and modify it */
                    if (found_item_variant) {
                        selected_variant = found_item_variant;

                        $('#add-extras .menu_price').html(formatPrice(found_item_variant.price));
                        $('#add-extras .menu_price').data('price', found_item_variant.price);
                        $('#add-order-button').prop('disabled', false);

                        calculateOrderPrice();
                    }

                    /* Make sure the add-to-cart is disabled if a variant is not found */
                    else {
                        selected_variant = null;
                        $('#add-extras .menu_price').html(formatPrice(order_price));
                        $('#add-extras .menu_price').data('price', order_price);
                        $('#add-order-button').prop('disabled', true);

                        calculateOrderPrice();
                    }

                });

                $variant_wrapper.append($options_tpl);
                $variant_wrapper.find('.radio input').first().trigger('click');
            }
        }

        /* Extra */
        var $extra_wrapper = $('#menu-extra-items');
        $extra_wrapper.html('');
        var extras = TOTAL_MENUS[item_id].extras || [];

        if (extras.length == 0) {
            $('.menu-extra-wrapper').hide();
        } else {
            $('.menu-extra-wrapper').show();
        }
        for (var i in extras) {
            if (extras.hasOwnProperty(i)) {
                var $extra_tpl = $(
                    '<div class="d-flex menu-extra-item">' +
                    '<div class="checkbox">' +
                    '<input type="checkbox" id="chekcbox1">' +
                    '<label for="chekcbox1">' +
                    '<span class="checkbox-icon"></span> <span class="extra-item-title"></span>' +
                    '</label>' +
                    '</div>' +
                    '<strong class="margin-left-auto extra-item-price"></strong>' +
                    '</div>');

                $extra_tpl.find('.checkbox input').attr('id', 'checkbox' + extras[i].id);
                $extra_tpl.find('label').attr('for', 'checkbox' + extras[i].id);
                $extra_tpl.find('.extra-item-title').html(extras[i].title);
                $extra_tpl.find('.extra-item-price').html(formatPrice(extras[i].price));
                $extra_tpl.data('price', extras[i].price);
                $extra_tpl.data('id', extras[i].id);

                $extra_tpl.find('.checkbox input').on('change', function () {
                    $('#menu-order-quantity').val(1);
                    calculateOrderPrice();
                });
                $extra_wrapper.append($extra_tpl);
            }
        }

        $('#menu-order-quantity-decrease').off().on('click', function (e) {
            var quatity = Number($('#menu-order-quantity').val()) - 1;
            if (quatity == 0) {
                quatity = 1;
            }
            $('#menu-order-quantity').val(quatity);
            calculateOrderPrice();
        });
        $('#menu-order-quantity-increase').off().on('click', function (e) {
            $('#menu-order-quantity').val(Number($('#menu-order-quantity').val()) + 1);
            calculateOrderPrice();
        });

        $('#add-order-button').off().on('click', function (e) {
            calculateOrderPrice();
            var price = $('#order-price').html();
            var order_data = JSON.parse(localStorage.getItem('quickqr_order'));

            // this order's extras
            var extras = [];
            $('.menu-extra-item').each(function () {
                if ($(this).find('.checkbox input').is(':checked')) {
                    extras.push({
                        'id': $(this).data('id'),
                        'name': $(this).find('.extra-item-title').html(),
                        'price': $(this).data('price')
                    });
                }
            });

            var item = {
                'id': item_id,
                'order_price': price,
                'item_name': name,
                'item_price': $('#add-extras .menu_price').data('price'),
                'extras': extras,
                'variants': selected_variant ? selected_variant.id : null,
                'quantity': $('#menu-order-quantity').val()
            };
            item['item_key'] = md5(JSON.stringify(item));

            /* Check if we should add it to the cart or update the quantity of an already existing item in the cart */
            let item_already_added = order_data.findIndex(element => element.item_key == item['item_key']);

            if (item_already_added == -1) {
                order_data.push(item);
            } else {
                order_data[item_already_added].quantity++;
            }

            localStorage.setItem('quickqr_order', JSON.stringify(order_data));

            $('#view-order-wrapper').show();
            $.magnificPopup.close();
        });

        $.magnificPopup.open({
            items: {
                src: '#add-extras',
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

    /*
    * View Order
    */
    $('#view-order-button').on('click', function (e) {
        var order_data = JSON.parse(localStorage.getItem('quickqr_order'));
        var $order_items_wrapper = $('.your-order-items'),
            $order_total_selector = $('.order-total').find('.your-order-price'),
            order_total = 0;

        $('.your-order-content').show();
        $('.order-success-message').hide();

        function generateViewOrder() {
            var order_total = 0;
            $order_items_wrapper.html('');
            for (var i in order_data) {
                if (order_data.hasOwnProperty(i)) {
                    var order = order_data[i],
                        price = Number(order.item_price),
                        quantity = Number(order.quantity),
                        extras = order.extras,
                        extra_total = 0;

                    var $order_tpl = $('<div class="your-order-item">' +
                        '<div class="menu_detail">' +
                        '<h4 class="menu_post">' +
                        '<a href="javascript:void(0)" class="item-delete"><i class="icon-feather-trash-2 margin-right-5"></i></a>' +
                        '<span class="menu_title"></span>' +
                        '<span class="menu_price"></span>' +
                        '</h4>' +
                        '</div>' +
                        '<div class="menu-data menu-extra-wrapper">' +
                        '</div>' +
                        '</div>');

                    var title = order.item_name + (quantity > 1 ? ' &times; ' + quantity : '');
                    var variant_title = '';
                    if (order.variants) {
                        variant_title = getVariantsTitle(order.id, order.variants);
                        variant_title = `<small>(${variant_title})</small>`;
                    }

                    $order_tpl.data('cart_id', i);
                    $order_tpl.find('.menu_title').html(title + ' ' + variant_title);
                    $order_tpl.find('.menu_price').html(formatPrice(price * quantity));

                    for (var j in extras) {
                        if (extras.hasOwnProperty(j)) {
                            var extra = extras[j],
                                extra_price = Number(extra.price);

                            var $extra_tpl = $('<div class="d-flex menu-extra-item">' +
                                '<a href="javascript:void(0)" class="item-extra-delete"><i class="icon-feather-trash-2 margin-right-5"></i></a>' +
                                '<span class="extra-item-title"></span>' +
                                '<strong class="margin-left-auto extra-item-price"></strong>' +
                                '</div>');

                            $extra_tpl.data('extra_cart_id', j);
                            $extra_tpl.find('.extra-item-title').html(extra.name);
                            $extra_tpl.find('.extra-item-price').html(formatPrice(extra_price * quantity));

                            var $extra_delete = $extra_tpl.find('.item-extra-delete');
                            $extra_delete.data('price', extra_price * quantity);
                            $extra_delete.data('key', j);

                            $extra_delete.on('click', function () {
                                var cart_key = $(this).closest('.your-order-item').data('cart_id');
                                var extra_cart_key = $(this).closest('.menu-extra-item').data('extra_cart_id');
                                order_data[cart_key]['extras'].splice(extra_cart_key, 1);

                                localStorage.setItem('quickqr_order', JSON.stringify(order_data));
                                generateViewOrder();

                            });

                            extra_total += extra_price;
                            $order_tpl.find('.menu-extra-wrapper').append($extra_tpl);
                        }
                    }
                    var this_item_total = (extra_total + price) * quantity;
                    order_total += this_item_total;

                    var $delete = $order_tpl.find('.item-delete');
                    $delete.on('click', function () {
                        var cart_key = $(this).closest('.your-order-item').data('cart_id');
                        order_data.splice(cart_key, 1);

                        localStorage.setItem('quickqr_order', JSON.stringify(order_data));
                        generateViewOrder();

                    });
                    $order_items_wrapper.append($order_tpl);

                }
            }

            $order_total_selector.html(formatPrice(order_total));
            if (order_total == 0) {
                $('#view-order-wrapper').hide();
                $.magnificPopup.close();
            }
        }

        generateViewOrder();
        $.magnificPopup.open({
            items: {
                src: '#your-order',
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

    /*
    * Call the waiter
    */
    $('#call-the-waiter-btn').on('click', function () {
        $.magnificPopup.open({
            items: {
                src: '#call-waiter-box',
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

    $("#call-waiter-form").on("submit", function (e) {
        e.preventDefault();
        var $form = $(this),
            $btn = $form.find('button');

        $btn.addClass('button-progress').prop('disabled', true);

        $.ajax({
            type: "POST",
            url: $form.attr('action'),
            data: $form.serializeArray(),
            dataType: 'json',
            success: function (response) {
                $btn.removeClass('button-progress').prop('disabled', false);
                $.magnificPopup.close();
            }
        });
    });

    /*
    * Ordering type
    */
    $("#ordering-type").on("change", function (e) {
        let ordering_type = $(this).val();
        if (ordering_type == 'on-table') {
            $('#table-number-field').show();
            $('#phone-number-field').hide();
            $('#address-field').hide();
        } else if (ordering_type == 'takeaway') {
            $('#table-number-field').hide();
            $('#phone-number-field').show();
            $('#address-field').hide();
        } else if (ordering_type == 'delivery') {
            $('#table-number-field').hide();
            $('#phone-number-field').show();
            $('#address-field').show();
        }
    }).trigger('change');

    if ($("#ordering-type").find('option').length == 1) {
        $("#ordering-type").closest('.ordering-type-div').hide();
    }

    /*
    * pay via
    */
    $("#pay_via").on("change", function (e) {
        let pay_via = $(this).val();
        if (pay_via == 'pay_on_counter') {
            $('#submit-order-button span').html(LANG_SEND_ORDER);
        } else if (pay_via == 'pay_online') {
            $('#submit-order-button span').html(LANG_PAY_NOW);
        }
    });

    /*
    * Send Order
    */
    $("#send-order-form").on("submit", function (e) {
        e.preventDefault();
        var order_data = JSON.parse(localStorage.getItem('quickqr_order')),
            items = [],
            $form = $(this),
            $btn = $form.find('button'),
            $form_error = $form.find('.form-error'),
            $data = $form.serializeArray();

        for (var i in order_data) {
            if (order_data.hasOwnProperty(i)) {
                items.push(order_data[i]);
            }
        }
        $data.push({name: 'items', value: JSON.stringify(items)});

        $form_error.slideUp();
        $btn.addClass('button-progress').prop('disabled', true);
        $.ajax({
            type: "POST",
            url: $form.attr('action'),
            data: $data,
            dataType: 'json',
            success: function (response) {
                $btn.removeClass('button-progress').prop('disabled', false);
                if (response.success) {
                    // clear order data
                    localStorage.setItem('quickqr_order', '[]');
                    $('#view-order-wrapper').hide();

                    if (response.message != '' && response.message != null) {
                        location.href = response.message;
                    } else {
                        $('.your-order-content').slideUp();
                        $('.order-success-message').slideDown();

                        if (response.whatsapp_url != '' && response.whatsapp_url != null) {
                            // send to whatsapp
                            location.href = response.whatsapp_url;
                        }
                    }

                } else {
                    $form.find('.form-error').html(response.message).slideDown();
                }
            }
        });
    });

    /* on lang change */
    $('.user-lang-switcher').on('click', '.dropdown-menu li', function (e) {
        e.preventDefault();
        var lang = $(this).data('lang');
        var code = $(this).data('code');
        if (lang != null) {
            var res = lang.substr(0, 2);
            $('#selected_lang').html(res.toUpperCase());
            $.cookie('Quick_lang', lang, {path: '/'});
            $.cookie('Quick_user_lang', lang, {path: '/'});
            $.cookie('Quick_user_lang_code', code, {path: '/'});
            location.reload();
        }
    });
    var code = $.cookie('Quick_user_lang_code');
    if (code != null) {
        $('.user-lang-switcher .filter-option').html(code.toUpperCase());
    }

    function calculateOrderPrice() {
        var amount = Number($('#add-extras .menu_price').data('price'));
        var extra = 0;
        $('.menu-extra-item').each(function () {
            if ($(this).find('.checkbox input').is(':checked')) {
                extra += Number($(this).data('price'));
            }
        });
        $('#order-price').html(formatPrice((amount + extra) * Number($('#menu-order-quantity').val())));
    }

    /* Get variant title for order view */
    function getVariantsTitle(item_id, variant_id) {
        var variant_options = TOTAL_MENUS[item_id].variant_options || [],
            js_variants = TOTAL_MENUS[item_id].variants || [],
            variant_title = [];

        var variant = js_variants.find(element => {
            return element.id == variant_id
        });

        $.each(variant['options'], function (variant_option_id, variant_option_key) {
            var variant_option = variant_options.find(element => {
                return element.id == variant_option_id
            });
            variant_title.push(variant_option['options'][variant_option_key]);
        });

        return variant_title.join(', ');
    }

    function formatPrice(price) {
        return (CURRENCY_LEFT == 1 ? CURRENCY_SIGN + ' ' : '') + price + (CURRENCY_LEFT == 0 ? ' ' + CURRENCY_SIGN : '');
    }
})(jQuery);