/************************************************
 * Template Name: QuickQR - Contactless Restaurant QR Menu Maker
 * Version: 1.0
 * Author: BYLANCER
 * Developed By: BYLANCER
 * Author URL: www.bylancer.com
 *************************************************/

(function ($) {
    "use strict";

    // remove old localstorage data
    localStorage.setItem('quickqr_order', '[]');

    // enable tooltip
    tippy('body', {
        target: '[data-tippy-placement]',
        dynamicTitle: true,
        delay: 100,
        arrow: true,
        arrowType: 'sharp',
        size: 'regular',
        duration: 200,
        animation: 'shift-away',
        animateFill: true,
        theme: 'dark',
        distance: 10,
    });

    var $menuCustomize = $('#menuCustomize'),
        $view_order_wrapper = $('#view-order-wrapper');

    $(".menu-lightbox").each(function () {
        lightGallery($(this).get(0), {
            selector: '.menu-lightbox-image',
            download: false,
            counter: false
        });
    });

    $(document).ready(function () {
        $("img.lazy-load").lazyload({
            effect: "fadeIn",
            load: function () {
                $(this).removeClass('lazy-load');
            }
        });
    });

    /* PRELOADER */
    $(window).on('load', function () {
        $(".preloading").fadeOut("slow");
    });


    // on category click
    $('.menu-category').on('click', function (e) {
        e.preventDefault();
        $('.menu-category').removeClass("active");
        $(this).addClass("active");

        $('.menu-category-item').hide();
        $('.menu-category-' + $(this).data('catid')).show();

        /* Hack for lazy load */
        $(window).resize();

        $('#sidebarleft').removeClass('active');
        $('.overlay').removeClass('active');
        $('body').removeClass('noscroll');
    });
    $('.menu-category').first().trigger('click');

    $(document).on('click', '.accordion', function (e) {
        /* Hack for lazy load */
        $(window).resize();
    });

    /* Check if the order paid */
    let current_url = new URL(window.location.href);

    if (current_url.searchParams.get('return') == 'success') {
        $('.your-order-content').slideUp();
        $('.order-success-message').slideDown();
        $('#viewOrder').addClass('active');

        current_url.searchParams.delete('return');
    } else {
        $('#sidebarleft').addClass('active');
    }
    $('.overlay').addClass('active');
    $('body').addClass('noscroll');

    $(window).scroll(function () {
        var scroll = $(window).scrollTop();
        if (scroll > 0) {
            $(".bg-header").addClass("active");
        } else {
            $(".bg-header").removeClass("active");
        }
    });

    /* SIDE NAVIGATION */
    $('#dismiss, .overlay').on('click', function () {
        $(this).parents('.sidenav').removeClass('active');
        $('#viewOrder').removeClass('active');
        $('#sidebarleft').removeClass('active');
        $('#call-waiter-box').removeClass('active');
        $('.overlay').removeClass('active');
        $menuCustomize.removeClass('active');
        $('body').removeClass('noscroll');
    });

    $('#sidebarleftbutton').on('click', function () {
        $('.overlay').addClass('active');
        $('body').addClass('noscroll');
        $('#sidebarleft').addClass('active');
    });

    $('#call-the-waiter-btn').on('click', function () {
        $('.overlay').addClass('active');
        $('body').addClass('noscroll');
        $('#call-waiter-box').addClass('active');
    });

    $('#viewOrderBtn').on('click', function () {
        var order_data = JSON.parse(localStorage.getItem('quickqr_order'));
        var $order_items_wrapper = $('.your-order-items'),
            $order_total_selector = $('.your-order-price');

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

                    var $order_tpl = $('<div class="section-menu">' +
                        '<div class="menu-item list">' +
                        '<div class="badge only"><i class="fa fa-circle"></i></div>' +
                        '<div class="menu-content">' +
                        '<div class="menu-detail">' +
                        '<div class="menu-title">' +
                        '<h4></h4>' +
                        '<div class="menu-price"></div>' +
                        '</div>' +
                        '<div class="add-menu">' +
                        '<div class="add-btn add-item-btn">' +
                        '<div class="wrapper h-100">' +
                        '<div class="addition menu-order-quantity-decrease">' +
                        '<i class="icon-feather-minus"></i>' +
                        '</div>' +
                        '<div class="count">' +
                        '<span class="num menu-order-quantity">1</span>' +
                        '</div>' +
                        '<div class="addition menu-order-quantity-increase">' +
                        '<i class="icon-feather-plus"></i>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<span class="price menu_price"></span>' +
                        '</div>' +
                        '</div>' +
                        '<div class="menu-extra-wrapper"></div>' +
                        '</div>' +
                        '</div>' +
                        '</div>');

                    var title = order.item_name;

                    var variant_title = '';
                    if (order.variants) {
                        variant_title = getVariantsTitle(order.id, order.variants);
                        variant_title = `<small>(${variant_title})</small>`;
                    }

                    $order_tpl.data('cart_id', i);
                    $order_tpl.find('h4').html(title + ' ' + variant_title);
                    $order_tpl.find('.badge').addClass(TOTAL_MENUS[order.id].type);
                    $order_tpl.find('.menu-price').html(formatPrice(price));
                    $order_tpl.find('.menu-order-quantity').html(quantity);
                    $order_tpl.find('.menu_price').html(formatPrice(price * quantity));

                    for (var j in extras) {
                        if (extras.hasOwnProperty(j)) {
                            var extra = extras[j],
                                extra_price = Number(extra.price);

                            var $extra_tpl = $('<div class="menu-detail">' +
                                '<div class="menu-title">' +
                                '<a href="javascript:void(0)" class="item-extra-delete"><i class="icon-feather-trash-2 m-r-5"></i></a>' +
                                '<h4></h4>' +
                                '</div>' +
                                '<div>' +
                                '<span class="price menu_price"></span>' +
                                '</div>' +
                                '</div>');

                            $extra_tpl.data('extra_cart_id', j);
                            $extra_tpl.find('h4').html(extra.name + ' <span class="menu-price">' + formatPrice(extra_price) + '</span>');
                            $extra_tpl.find('.menu_price').html(formatPrice(extra_price * quantity));

                            var $extra_delete = $extra_tpl.find('.item-extra-delete');
                            $extra_delete.on('click', function () {
                                var cart_key = $(this).closest('.section-menu').data('cart_id');
                                var extra_cart_key = $(this).closest('.menu-detail').data('extra_cart_id');
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

                    $order_tpl.find('.menu-order-quantity-decrease').off().on('click', function (e) {
                        e.stopPropagation();
                        var $item = $(this).closest('.section-menu');
                        var $quantity = $item.find('.menu-order-quantity');
                        var quantity = Number($quantity.text()) - 1;
                        var cart_key = $item.data('cart_id');
                        var $menu_item = $('.section-menu[data-id="' + order_data[cart_key]['id'] + '"]');
                        if (quantity == 0) {
                            order_data.splice(cart_key, 1);
                        } else {
                            $menu_item.find('.menu-order-quantity').text(quantity);
                            order_data[cart_key]['quantity'] = quantity;
                        }

                        localStorage.setItem('quickqr_order', JSON.stringify(order_data));
                        generateViewOrder();
                    });
                    $order_tpl.find('.menu-order-quantity-increase').off().on('click', function (e) {
                        e.stopPropagation();
                        var $item = $(this).closest('.section-menu');
                        var $quantity = $item.find('.menu-order-quantity');
                        var cart_key = $item.data('cart_id');
                        var $menu_item = $('.section-menu[data-id="' + order_data[cart_key]['id'] + '"]');
                        var quantity = Number($quantity.text()) + 1;

                        $menu_item.find('.menu-order-quantity').text(quantity);
                        order_data[cart_key]['quantity'] = quantity;
                        localStorage.setItem('quickqr_order', JSON.stringify(order_data));

                        generateViewOrder();
                    });
                    $order_items_wrapper.append($order_tpl);

                }
            }
            $order_total_selector.html(formatPrice(order_total));
            if (order_total == 0) {
                $('.overlay').trigger('click');
            }
            manageViewOrder();
        }

        generateViewOrder();
        $('#viewOrder').addClass('active');
        $('.overlay').addClass('active');
        $('body').addClass('noscroll');
    });

    // add menu to order
    $(document).on('click', '.add-item-to-order', function (e) {
        e.preventDefault();

        $('#add-order-button').prop('disabled', false);
        var $item = $(this).closest('.section-menu'),
            item_id = $item.data('id'),
            name = $item.data('name'),
            description = $item.data('description'),
            price = $item.data('price'),
            amount = $item.data('amount'),
            order_price = Number(amount),
            extras = TOTAL_MENUS[$item.data('id')].extras || [];

        $menuCustomize.find('h4').html(name);
        $menuCustomize.find('.customize-item-description').html(description);
        $menuCustomize.data('price', amount);
        $('#order-price').html(formatPrice(amount));
        $('#menu-order-quantity').text(1);

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
                    '<div>' +
                    '<div class="section">' +
                    '<div class="extras-heading">' +
                    '<div class="title"></div>' +
                    '</div>' +
                    '<div class="menu-variant-option">' +
                    /* Variant Radio options will come here */
                    '</div>' +
                    '</div>' +
                    '<div class="line-separate mt-0"></div>' +
                    '</div>');

                $options_tpl.find('.title').html(variant_options[i].title);
                $options_tpl.data('id', variant_options[i].id);

                /* Variant Options */
                $.each(variant_options[i].options, function (key, item) {
                    var radio_key = variant_options[i].id + '-' + key;

                    if (available_variant_options.includes(radio_key)) {
                        var $radio_options = $(
                            '<div class="extras menu-extra-item">' +
                            '<label for="checkbox0" class="extra-item-title mb-0"></label>' +
                            '<div class="d-flex align-items-center">' +
                            '<div class="custom-control custom-radio mr-sm-2">' +
                            '<input type="radio" name="variant" class="custom-control-input" id="checkbox0">' +
                            '<label class="custom-control-label" for="checkbox0"></label>' +
                            '</div>' +
                            '</div>' +
                            '</div>'
                        );


                        $radio_options.find('input').attr('id', 'radio-' + radio_key);
                        $radio_options.find('input').attr('value', radio_key);
                        $radio_options.find('input').attr('name', 'variant-radio-' + variant_options[i].id);
                        $radio_options.find('input').data('option-id', variant_options[i].id);
                        $radio_options.find('input').data('option-key', key);
                        $radio_options.find('label').attr('for', 'radio-' + radio_key);
                        $radio_options.find('.extra-item-title').html(item);
                    }

                    $options_tpl.find('.menu-variant-option').append($radio_options);
                });

                $options_tpl.find('.menu-extra-item input').on('click', function () {
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
                            $item_option_element.parents('.menu-extra-item').css('opacity', 1);

                            if (!available_item_options.includes($item_option_element.val())) {

                                /* Disable radio button */
                                $item_option_element.prop('disabled', true);
                                $item_option_element.parents('.menu-extra-item').css('opacity', 0.5);

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

                        $menuCustomize.data('price', found_item_variant.price);
                        $('#add-order-button').prop('disabled', false);

                        calculateOrderPrice();
                    }

                    /* Make sure the add-to-cart is disabled if a variant is not found */
                    else {
                        selected_variant = null;
                        $menuCustomize.data('price', order_price);
                        $('#add-order-button').prop('disabled', true);

                        calculateOrderPrice();
                    }

                });

                $variant_wrapper.append($options_tpl);
                $variant_wrapper.find('.menu-extra-item input').first().trigger('click');
            }
        }

        /* Extras */
        var $extra_wrapper = $('#customize-extras');
        $extra_wrapper.html('');

        if (extras.length == 0) {
            $('.menu-extra-wrapper').hide();
        } else {
            $('.menu-extra-wrapper').show();
        }

        for (var i in extras) {
            if (extras.hasOwnProperty(i)) {
                var $extra_tpl = $(
                    '<div class="extras menu-extra-item">' +
                    '<span class="extra-item-title"></span>' +
                    '<div class="d-flex align-items-center">' +
                    '<span class="mr-2 extra-item-price"></span>' +
                    '<div class="custom-control custom-checkbox mr-sm-2">' +
                    '<input type="checkbox" class="custom-control-input" id="customControl">' +
                    '<label class="custom-control-label" for="customControl"></label>' +
                    '</div>' +
                    '</div>' +
                    '</div>');

                $extra_tpl.find('.custom-control-input').attr('id', 'checkbox' + extras[i].id);
                $extra_tpl.find('label').attr('for', 'checkbox' + extras[i].id);
                $extra_tpl.find('.extra-item-title').html(extras[i].title);
                $extra_tpl.find('.extra-item-price').html(formatPrice(extras[i].price));
                $extra_tpl.data('price', extras[i].price);
                $extra_tpl.data('id', extras[i].id);

                $extra_tpl.find('.custom-control-input').on('change', function () {
                    $('#menu-order-quantity').text(1);
                    calculateOrderPrice();

                });
                $extra_wrapper.append($extra_tpl);
            }
        }

        $menuCustomize.find('.menu-order-quantity-decrease').off().on('click', function (e) {
            e.stopPropagation();
            var quatity = Number($('#menu-order-quantity').text()) - 1;
            if (quatity == 0) {
                quatity = 1;
            }
            $('#menu-order-quantity').text(quatity);
            calculateOrderPrice();
        });
        $menuCustomize.find('.menu-order-quantity-increase').off().on('click', function (e) {
            e.stopPropagation();
            $('#menu-order-quantity').text(Number($('#menu-order-quantity').text()) + 1);
            calculateOrderPrice();
        });

        $('#add-order-button').off().on('click', function (e) {
            calculateOrderPrice();
            var price = $('#order-price').html();
            var order_data = JSON.parse(localStorage.getItem('quickqr_order'));

            // this order's extras
            var extras = [];
            $('.menu-extra-item').each(function () {
                if ($(this).find('.custom-checkbox .custom-control-input').is(':checked')) {
                    extras.push({
                        'id': $(this).data('id'),
                        'name': $(this).find('.extra-item-title').html(),
                        'price': $(this).data('price')
                    });
                }
            });

            var item = {
                'id': item_id,
                'item_name': name,
                'item_price': $menuCustomize.data('price'),
                'extras': extras,
                'variants': selected_variant ? selected_variant.id : null,
                'quantity': $('#menu-order-quantity').text()
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

            manageViewOrder();
            $('.overlay').trigger('click');
        });


        $menuCustomize.addClass('active');
        $('.overlay').addClass('active');
        $('body').addClass('noscroll');

        manageViewOrder();
    });

    /*
    * Ordering type
    */
    $("#ordering-type").on("change", function (e) {
        let ordering_type = $(this).val();
        if (ordering_type == 'on-table') {
            $('#table-number-field').slideDown();
            $('#phone-number-field').slideUp();
            $('#address-field').slideUp();
        } else if (ordering_type == 'takeaway') {
            $('#table-number-field').slideUp();
            $('#phone-number-field').slideDown();
            $('#address-field').slideUp();
        } else if (ordering_type == 'delivery') {
            $('#table-number-field').slideUp();
            $('#phone-number-field').slideDown();
            $('#address-field').slideDown();
        }
        /*if($("#pay_via").val() == 'pay_online'){
            $('#phone-number-field').slideDown();
        }*/
    }).trigger('change');

    if ($("#ordering-type").find('option').length == 1) {
        $("#ordering-type").closest('.section').hide();
    }

    /*
    * pay via
    */
    $("#pay_via").on("change", function (e) {
        let pay_via = $(this).val();
        if (pay_via == 'pay_on_counter') {
            $('#submit-order-button').html(LANG_SEND_ORDER);
            /*if($("#ordering-type").val() == 'on-table'){
                $('#phone-number-field').slideUp();
            }*/

        } else if (pay_via == 'pay_online') {
            $('#submit-order-button').html(LANG_PAY_NOW);
            //$('#phone-number-field').slideDown();
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
                    manageViewOrder();

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
                    $form.find('.form-error').html(response.message).slideDown().focus();
                }
            }
        });
    });

    /*
    * Call the waiter
    */
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
                $('#dismiss').trigger('click');
            }
        });
    });

    /* on lang change */
    $('.user-lang-switcher').on('click', '.dropdown-menu a', function (e) {
        e.preventDefault();
        var lang = $(this).data('lang');
        var code = $(this).data('code');
        if (lang != null) {
            var res = lang.substr(0, 2);
            $('#selected_lang').html(res);
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

    function manageViewOrder() {
        var order_data = JSON.parse(localStorage.getItem('quickqr_order'));
        var price = 0,
            quantity = 0;
        for (var i in order_data) {
            if (order_data.hasOwnProperty(i)) {
                var extra_price = 0,
                    extras = order_data[i]['extras'];
                for (var j in extras) {
                    if (extras.hasOwnProperty(j)) {
                        extra_price += Number(extras[j]['price']);
                    }
                }
                price += (Number(order_data[i]['item_price']) + extra_price) * Number(order_data[i]['quantity']);
                quantity += Number(order_data[i]['quantity']);
            }
        }
        $('#view-order-quantity').html(quantity);
        $('#view-order-price').html(formatPrice(price));
        if (quantity > 0) {
            $view_order_wrapper.show();
        } else {
            $view_order_wrapper.hide();
        }
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

    function calculateOrderPrice() {
        var amount = Number($menuCustomize.data('price'));
        var extra = 0;
        $('.menu-extra-item').each(function () {
            if ($(this).find('.custom-checkbox .custom-control-input').is(':checked')) {
                extra += Number($(this).data('price'));
            }
        });
        $('#order-price').html(formatPrice((amount + extra) * Number($('#menu-order-quantity').text())));
    }
})(jQuery);


//Set Waves
Waves.attach('.components li a', ['waves-block']);
Waves.init();

$(document).ready(function () {
    var submitIcon = $('.searchbox-icon');
    var inputBox = $('.searchbox-input');
    var searchBox = $('.searchbox');
    var isOpen = false;
    submitIcon.click(function () {
        if (isOpen == false) {
            searchBox.addClass('searchbox-open');
            inputBox.focus();
            isOpen = true;
        } else {
            searchBox.removeClass('searchbox-open');
            inputBox.focusout();
            isOpen = false;
        }
    });
    submitIcon.mouseup(function () {
        return false;
    });
    searchBox.mouseup(function () {
        return false;
    });
    $(document).mouseup(function () {
        if (isOpen == true) {
            $('.searchbox-icon').css('display', 'block');
            submitIcon.click();
        }
    });
});

function buttonUp() {
    var inputVal = $('.searchbox-input').val();
    inputVal = $.trim(inputVal).length;
    if (inputVal !== 0) {
        $('.searchbox-icon').css('display', 'none');
    } else {
        $('.searchbox-input').val('');
        $('.searchbox-icon').css('display', 'block');
    }
}

function inlineBG() {
    $(".single-page-header").each(function () {
        var attrImageBG = $(this).attr('data-background-image');
        if (attrImageBG !== undefined) {
            $(this).append('<div class="background-image-container"></div>');
            $('.background-image-container').css('background-image', 'url(' + attrImageBG + ')');
        }
    });
}

inlineBG();
$(".intro-search-field").each(function () {
    var bannerLabel = $(this).children("label").length;
    if (bannerLabel > 0) {
        $(this).addClass("with-label");
    }
});

