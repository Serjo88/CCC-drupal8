(function ($, Drupal, window) {

    /**
     * Initialise the tabs JS.
     */
    Drupal.behaviors.builderDialog = {
        attach: function (context, settings) {

            $('.details-title, .horizontal-tabs__menu li', context).on('click', function (e) {
                //$('.ui-dialog:visible').last().find(".ui-dialog-content").dialog('moveToTop');
                $(".ui-dialog-content:visible").each(function () {
                    $(this).dialog("option", "position", $(this).dialog("option", "position"));
                });
            });

        }
    };

})(jQuery, Drupal, window);