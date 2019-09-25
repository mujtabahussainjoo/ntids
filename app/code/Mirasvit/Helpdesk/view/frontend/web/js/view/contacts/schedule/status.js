require([
    'jquery'
], function ($) {
    $(document).ready(function() {
        if (typeof scheduleStatusUrl == 'undefined') {
            return;
        }
        $.ajax({
            url: scheduleStatusUrl,
            data: scheduleStatusData,
            type: 'GET',
            cache: true,
            dataType: 'json',
            context: this,

            /**
             * Response handler
             * @param {Object} response
             */
            success: function (response) {
                if (response.trim()) {
                    $('.hdmx__schedule').prepend(response);
                }
            }
        });
    })
});