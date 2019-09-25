require([
    'jquery'
], function ($) {
    'use strict';

    if (typeof notificationCheckUrl !== 'undefined' && notificationInterval > 0) {
        window.setInterval(checkTickets, notificationInterval);
    }

    function checkTickets() {
        new $.ajax(notificationCheckUrl, {
            method: 'get',
            success: function (data) {
                if (data) {
                    //notificationCheckUrl = data.url;
                    if (data.messages.length) {
                        for (var i=0; i < data.messages.length; i++) {
                            notifyMe(data.messages[i]);
                        }
                    }
                }
            }
        });
    }

    function notifyMe(message) {
        // Let's check if the browser supports notifications
        if (!("Notification" in window)) {
            alert("This browser does not support desktop notification");
        }

        // Let's check whether notification permissions have already been granted
        else if (Notification.permission === "granted") {
            // If it's okay let's create a notification
            showNotification(message);
        }

        // Otherwise, we need to ask the user for permission
        else if (Notification.permission !== 'denied') {
            Notification.requestPermission(function (permission) {
                // If the user accepts, let's create a notification
                if (permission === "granted") {
                    showNotification(message);
                }
            });
        }
    }

    function showNotification(message) {
        if (typeof message.title != 'undefined') {
            var notification = new Notification(message.message, {
                'icon': notificationIcon
            });
            notification.onclick = function (event) {
                event.preventDefault();
                window.open(message.url, '_blank');
            }
        }
    }
});