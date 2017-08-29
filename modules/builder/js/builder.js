(function ($, Drupal) {
    Drupal.behaviors.builder = {
        attach: function (context, settings) {


        }
    };


    // Builder animation element

    $.fn.extend({
        animateCss: function (animationName) {
            var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
            $(this).addClass('animated ' + animationName).one(animationEnd, function () {
                //$(this).removeClass('animated ' + animationName);
            });
        }
    });

    var $animation_elements = $('.builder-element-animation');
    var $window = $(window);

    function check_if_in_view() {
        var window_height = $window.height();
        var window_top_position = $window.scrollTop();
        var window_bottom_position = (window_top_position + window_height);

        $.each($animation_elements, function () {
            var $element = $(this);
            var $animation = $element.attr('data-animation');
            var element_height = $element.outerHeight();
            var element_top_position = $element.offset().top;
            var element_bottom_position = (element_top_position + element_height);


            //check to see if this current container is within viewport
            if ((element_bottom_position >= window_top_position ) &&
                (element_top_position <= window_bottom_position)) {
               $element.addClass('animated ' + $animation);


            } else {
                $(this).removeClass('animated ' + $animation);

            }
        });
    }

    $window.on('scroll resize', check_if_in_view);
    $window.trigger('scroll');


})(jQuery, Drupal);