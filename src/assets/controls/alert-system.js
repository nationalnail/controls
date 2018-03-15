(function ($) {

    $(function () {

        var body = $('body');
        var navbar = $('.navbar');
        var alert_block = body.find('.alert-block');

        if (alert_block.data('active') == true) {
            body.css('padding-top', '9rem');
            navbar.css('top', '62px');
        }

    });

})(jQuery);