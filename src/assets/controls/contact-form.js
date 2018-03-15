(function ($) {

    $(function () {

        var contact_form = $('#contact-form');

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

    });

})(jQuery);