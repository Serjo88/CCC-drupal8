(function ($) {
    Drupal.behaviors.builder = {
        attach: function (context, settings) {

            //update ckeditor
            $('.builder-element-submit-button').on('mouseover', function () {
                $('.cke_button__source.cke_button_on').each(function () {
                    $(this).click();
                });
            });


            var selected_class = 'builder-ui-element-selected';
            $('.builder-ui .builder-action-links-wrapper').hover(function () {
                    $this = $(this);
                    var $element = $this.closest('.builder-element');
                    $element.addClass(selected_class);
                }, function () {
                    $('.builder-ui').find('.builder-element').removeClass(selected_class);
                }
            );


            // disable link click in UI
            $('.builder-ui .builder-element-children-wrapper a, .builder-ui .contextual-links a').click(function (event) {
                event.preventDefault();
            });
            // sort elements

            /** Sort element by row */

            $('.builder-ui .builder-elements-wrapper').sortable({
                forcePlaceholderSize: true,
                handle: '.icon-move',
                placeholder: "ui-state-highlight",
                distance: 0.5,
                cursor: 'move',
                items: '.builder-element.builder-element-type-row',
                cursorAt: {top: 20, left: 16},
                start: function () {

                    $('builder-ui').addClass('builder-ui-sorting-started');
                },
                update: function (event, ui) {
                    builder_sortable($(this).parents('.builder-ui'));
                },
                stop: function (event, ui) {
                    $('builder-ui').removeClass('builder-ui-sorting-started');
                }
            });


            /**
             * Sort columns inside row
             */

            $('.builder-ui .builder-element-type-row .builder-element-children-wrapper').sortable({
                forcePlaceholderSize: true,
                handle: '.icon-move',
                placeholder: "ui-state-highlight",
                distance: 0.5,
                cursor: 'move',
                items: '.builder-element.builder-element-type-column',
                cursorAt: {top: 20, left: 16},
                helper: "clone",
                start: function () {
                    $('builder-ui').addClass('builder-ui-sorting-started');
                },
                stop: function (event, ui) {
                    $('builder-ui').removeClass('builder-ui-sorting-started');
                },
                update: function (event, ui) {
                    builder_sortable($(this).parents('.builder-ui'));
                },
                over: function (event, ui) {
                    ui.placeholder.css({maxWidth: ui.item.width()});
                    ui.placeholder.css({height: ui.item.height()});
                    ui.placeholder.css({float: 'left'});
                    ui.placeholder.addClass('.builder-ui-column');
                    var type = ui.item.attr('data-type');
                    var parent_type = ui.placeholder.parent().closest('.builder-element').attr('data-type');

                    if (!builder_element_sortable_check(type, parent_type)) {

                        ui.placeholder.addClass('.builder-ui-hidden-placeholder');
                    }
                }

            });


            /**
             *
             * Sort children elements
             */
            $('.builder-element-type-column .builder-element-children-wrapper').sortable({
                forcePlaceholderSize: true,
                placeholder: "ui-state-highlight",
                forceHelperSize: false,
                connectWith: ".builder-element-type-column .builder-element-children-wrapper",
                handle: '.icon-move',
                items: '.builder-element',
                distance: 3,
                scroll: true,
                scrollSensitivity: 100,
                cursor: 'move',
                cursorAt: {top: 20, left: 16},
                tolerance: 'intersect',

                start: function () {

                    $('builder-ui').addClass('builder-ui-sorting-started');
                },
                stop: function (event, ui) {
                    $('builder-ui').removeClass('builder-ui-sorting-started');

                    // console.log(builder_element_sortable_check(ui.item));
                    var type = ui.item.attr('data-type');
                    var parent_type = ui.item.parent().closest('.builder-element').attr('data-type');
                    if (!builder_element_sortable_check(type, parent_type)) {

                        $(this).sortable('cancel');
                    }

                },
                update: function (event, ui) {


                    builder_sortable($(this).parents('.builder-ui'));
                },
                over: function (event, ui) {
                    ui.placeholder.css({maxWidth: ui.placeholder.parent().width()});

                    var type = ui.item.attr('data-type');
                    var parent_type = ui.placeholder.parent().closest('.builder-element').attr('data-type');

                    if (type == 'column') {
                        ui.placeholder.css({maxWidth: ui.item.width()});
                        ui.placeholder.css({width: '20px'});
                        ui.placeholder.css({height: ui.item.height()});
                        ui.placeholder.css({float: 'left'});
                        ui.placeholder.addClass('.builder-ui-column');

                        if (!builder_element_sortable_check(type, parent_type)) {

                            ui.placeholder.addClass('.builder-ui-hidden-placeholder');
                            ui.helper.addClass('.builder-ui-hidden-placeholder');

                        }
                    }

                    if (!builder_element_sortable_check(type, parent_type)) {

                        ui.placeholder.addClass('.builder-ui-hidden-placeholder');
                    }
                }

            });


            // filter element

            jQuery.expr[':'].Contains = function (a, i, m) {
                return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
            };

            // create and add the filter form to the header
            var $input = $('#builder-elements-filter');
            var $list = $input.parents('.builder-ui-element-link').find('ul');

            $input.change(function () {
                    var filter = $(this).val();
                    if (filter) {
                        // this finds all links in a list that contain the input,
                        // and hide the ones not containing the input while showing the ones that do
                        $list.find("a:not(:Contains(" + filter + "))").parent().hide();
                        $list.find("a:Contains(" + filter + ")").parent().show();
                    } else {
                        $list.find("li").show();
                    }
                    return false;
                })
                .keyup(function () {
                    // fire the above change event after every letter
                    $(this).change();
                });


            // your custom js here

        }
    };

    function builder_element_sortable_check(type, parent_type) {


        if (type == 'column' && parent_type !== 'row') {
            return false;
        }
        if (type == 'row' && parent_type !== 'column') {

            return false;
        }
        if (type !== 'column' && type !== 'row' && parent_type !== 'column') {
            return false;

        }

        return true;

    }

    function builder_sortable(builder) {

        // Update weight for element.
        var $elements = [];
        var builder_bid = builder.attr('data-bid');
        builder.find('.builder-element').each(function () {
            $this = $(this);
            var $element = [];
            $element.push($this.attr('data-id'));
            var parent = 0;
            if ($this.parent().closest('.builder-element').length) {
                parent = $this.parent().closest('.builder-element').attr('data-id');
            }

            $element.push(parent);
            $elements.push($element);

        });


        var builder_data = {elements: $elements};
        var builder_url = drupalSettings.path.baseUrl + 'builder/sortable/' + builder_bid;

        $.ajax({
            data: builder_data,
            type: 'POST',
            url: builder_url
        });

        $('.quickedit-toolbar .action-save').attr('aria-hidden', false);

    }


})(jQuery);