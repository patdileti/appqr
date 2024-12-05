(function ($) {
    "use strict";

    /* Category */
    $('.add-category').on('click', function (e) {
        e.preventDefault();

        $('#add-category-form').trigger('reset');

        $.magnificPopup.open({
            items: {
                src: '#add-category-dialog',
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

    $('.edit-category').on('click', function (e) {
        e.stopPropagation();
        e.preventDefault();

        let $edit_category_form = $('#edit-category-form');
        $edit_category_form.find('[name="id"]').val($(this).data('catid'));
        $edit_category_form.find('[name="name"]').val($(this).closest('.dashboard-box').find('.category-display-name').html());

        $.magnificPopup.open({
            items: {
                src: '#edit-category-dialog',
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

    /* Sub Category */
    $('.add-sub-category').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        let $add_sub_category_form = $('#add-sub-category-form');
        $add_sub_category_form.trigger('reset');
        $add_sub_category_form.find('[name="parent"]').val($(this).data('catid'));

        $.magnificPopup.open({
            items: {
                src: '#add-sub-category-dialog',
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

    $('.edit-sub-category').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        let $edit_sub_category_form = $('#edit-sub-category-form');
        $edit_sub_category_form.find('[name="name"]').val($(this).closest('.dashboard-box').find('.category-display-name').html());
        $edit_sub_category_form.find('[name="id"]').val($(this).data('catid'));
        $edit_sub_category_form.find('[name="parent"]').val($(this).data('parent')).selectpicker('refresh');

        $.magnificPopup.open({
            items: {
                src: '#edit-sub-category-dialog',
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

    /* Menu Item */
    let $add_menu_item_form = $('#add-menu-item-form');
    let $edit_menu_item_form = $('#edit-menu-item-form');
    let $add_image_menu_form = $('#add-image-menu-form');
    let $edit_image_menu_form = $('#edit-image-menu-form');

    $('.add-menu-item').on('click', function (e) {
        e.stopPropagation();
        e.preventDefault();

        $add_menu_item_form.trigger('reset');
        $add_menu_item_form.find('[name="category_id"]').val($(this).data('catid'));
        $add_menu_item_form.find('img').attr('src', DEFAULT_IMAGE_URL);

        $.magnificPopup.open({
            items: {
                src: '#add-menu-item-dialog',
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

    $('.edit-menu-item').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        let data = $(this).data('data');

        $edit_menu_item_form.trigger('reset');
        $edit_menu_item_form.find('[name="id"]').val(data.id);
        $edit_menu_item_form.find('[name="category_id"]').val(data.category_id).selectpicker('refresh');
        $edit_menu_item_form.find('[name="name"]').val(data.name);
        $edit_menu_item_form.find('[name="description"]').val(data.description);
        $edit_menu_item_form.find('[name="price"]').val(data.price);
        $edit_menu_item_form.find('[name="type"]').val(data.type).selectpicker('refresh');
        $edit_menu_item_form.find('img').attr('src', storageurl + '/menu/' + data.image);
        $edit_menu_item_form.find('[name="active"]').prop('checked', data.active == '1' ? true : false);

        if($edit_menu_item_form.find('[name="allergies[]"]').length){
            $edit_menu_item_form.find('[name="allergies[]"]').val(data.allergies?.split(',')).selectpicker('refresh');
        }

        $.magnificPopup.open({
            items: {
                src: '#edit-menu-item-dialog',
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

    $('.edit-image-menu-item').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        let data = $(this).data('data');

        $edit_image_menu_form.trigger('reset');
        $edit_image_menu_form.find('[name="id"]').val(data.id);
        $edit_image_menu_form.find('[name="name"]').val(data.name);
        $edit_image_menu_form.find('img').attr('src', storageurl + '/menu/' + data.image);
        $edit_image_menu_form.find('[name="active"]').prop('checked', data.active == '1' ? true : false);

        $.magnificPopup.open({
            items: {
                src: '#edit-image-menu-dialog',
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

    let formSubmit = function (e) {
        e.preventDefault();

        let $form = $(this);
        $form.find('button').addClass('button-progress').prop('disabled', true);
        $.ajax({
            type: "POST",
            url: $form.attr('action'),
            data: new FormData($form.get(0)),
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    quick_alert(response.message);
                    location.reload();
                } else {
                    quick_alert(response.message, 'error');
                }
                $form.find('button').removeClass('button-progress').prop('disabled', false);
            }
        });
        return false;
    }
    $add_menu_item_form.on('submit', formSubmit);
    $edit_menu_item_form.on('submit', formSubmit);
    $add_image_menu_form.on('submit', formSubmit);
    $edit_image_menu_form.on('submit', formSubmit);

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

    $('.show-hide-submit-field').on('click', function (e) {
        $(this).siblings('.submit-field').slideToggle();
        $(this).find('.plus-minus').html(
            $(this).find('.plus-minus').html() === '+' ? '-' : '+'
        );
    });

})(jQuery);