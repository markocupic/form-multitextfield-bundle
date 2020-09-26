/**
 * Form Multitextfield Bundle for Contao CMS
 *
 * Copyright (C) 2005-2020 Marko Cupic
 *
 * @package Form Multitextfield Bundle
 * @link    https://www.github.com/markocupic/form-multitext-field-bundle
 *
 */
(function ($) {
    $(document).ready(function () {
        "use strict";

        /**
         * Add remove row event to button
         */
        $('.form-multi-text-remove-row-btn').map(function () {
            $(this).click(function (e) {
                e.preventDefault();
                let row = $(this).closest("tr");
                if ($(row).siblings('tr').length === 0) {
                    return false;
                }
                $(row).remove();
            });
        });

        /**
         * Add clone row event to button
         */
        $('.form-multi-text-clone-row-btn').map(function () {
            $(this).click(function (e) {
                e.preventDefault();
                let button = e.target;
                let master = $(button).closest("tr");
                $(master)
                .clone(true, true)// Deepclone (include events)
                .insertAfter($(master));
            });
        });

    })
})(jQuery);