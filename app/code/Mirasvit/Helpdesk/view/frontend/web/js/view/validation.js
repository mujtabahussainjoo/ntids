require([
    "jquery",
    'mage/validation'
], function ($) {
    $(document).ready(function () {
        $('#close_ticket_button').click(function() {
            $('textarea.message').addClass('ignore-validate')
                .removeClass('required-entry')
                .rules('remove');

            $('#close_ticket').val(1);
        });
    });
});