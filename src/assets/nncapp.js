(function ($) {

    $(function () {

        var body = $('body');
        var navbar = $('.navbar');
        var feedback = $('.feedback');
        var contact_form = $('#contact-form');
        var alert_block = body.find('.alert-block');
        var main_class = $('body').find('main').attr('class');

        if (alert_block.data('active') == true) {
            body.css('padding-top', '9rem');
            navbar.css('top', '62px');
        }

        contact_form.on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/contact-us/submit',
                type: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                success: function (response) {
                    if (response.success == 'false') {
                        contact_form.find('.response').html(response.message)
                        grecaptcha.reset();
                    } else if (response.success == 'true') {
                        contact_form.find('.response').html(response.message)
                        contact_form[0].reset();
                        grecaptcha.reset();
                    }
                }
            });
        });

        feedback.find('a').on('click', function (e) {
            e.preventDefault();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/feedback/submit',
                type: 'POST',
                dataType: 'json',
                data: {
                    class: main_class,
                    response: $(this).data('value')
                },
                success: function (response) {
                    if (response.success == 'false') {
                        feedback.hide().html('Sorry something went wrong!').fadeIn('slow');
                    } else if (response.success == 'true') {
                        feedback.hide().html('Thank you for your feedback!').fadeIn('slow');
                    }
                }
            });
        });

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
            document.body.scrollTop = 0; // For Chrome, Safari and Opera
            document.documentElement.scrollTop = 0; // For IE and Firefox
        }

        body.on('click', '.back-to-top', function () {
            topFunction();
        });

    });

})(jQuery);