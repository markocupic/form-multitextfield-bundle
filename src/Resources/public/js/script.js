(function ($) {
    $(document).ready(function () {
        "use strict";

        /**
         * Remove row
         */
        $('.form-multi-text-delete-btn').map(function(){
            $(this).click(function(e){
                e.preventDefault();
                $(this).closest("tr").remove();
            });
        });

        /**
         * Add row
         */
        $('.form-multi-text-add-row-btn').map(function(){
            $(this).click(function(e){
                e.preventDefault();
                let newRow = $(this).closest("tr").clone(true);

                $(newRow)
                .find(".form-multi-text-add-row-btn")
                .text($(this).data('lang-delete-row'))
                .removeClass("form-multi-text-add-row-btn")
                .addClass("form-multi-text-delete-btn")
                .closest('tr')
                .appendTo($(this).closest("tbody"))
                .find(".form-multi-text-delete-btn")
                .off("click")
                .click(function(e){
                    e.preventDefault();
                    $(this).closest("tr").remove();
                });
            });
        });

    })
})(jQuery);