(function ($) {

    $(function () {

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
            document.body.scrollTop = 0; // For Chrome, Safari and Opera
            document.documentElement.scrollTop = 0; // For IE and Firefox
        }

        var body = $('body');

        body.on('click', '.back-to-top', function () {
            topFunction();
        });

    });

})(jQuery);