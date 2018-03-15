(function ($) {

    $(function () {

        var feedback = $('.feedback');
        var main_class = $('body').find('main').attr('class');

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

    });

})(jQuery);