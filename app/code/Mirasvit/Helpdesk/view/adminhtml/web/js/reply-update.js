require([
    'jquery',
    'tinymce'
], function ($) {
    'use strict';

    var updateTimer = false;
    if (typeof draftText == 'undefined') {
        draftText = '';
    }
    $('body').on('click', '.page-actions-buttons > div ul > li >span', function (){
        stopDraft();
    });
    $('body').on('click', '.page-actions-buttons > div .action-default', function (){
        stopDraft();
    });
    function stopDraft() {
        if (updateTimer) {
            window.clearInterval(updateTimer);
        }
    }
    $('#reply').val(draftText);
    var origText = $('#reply').val();

    function updateActivity() {
        if (!isAllowDraft) {
            return;
        }

        var text = -1;

        var currentText = '';
        if(tinyMCE.activeEditor) {
            currentText = tinyMCE.activeEditor.getContent();
        } else {
            currentText = $('#reply').val();
        }
        if (typeof currentText == 'undefined') {
            return;
        }
        if (currentText != origText) {
            origText = currentText;
            text = origText;
        }
        $.ajax(draftUpdateUrl, {
            method : "post",
            loaderArea: false,
            data : {ticket_id: draftTicketId, text: text},
            dataType: 'json',
            success : function(response) {
                draftText = text;
                if(response.text.indexOf('<head>') == -1) {
                    if ($('main').length) {
                        $($('main .helpdesk-message')[0]).remove();
                        $('main').prepend(response.text);
                    } else {
                        $('header').next('.messages').remove();
                        $(response.text).insertAfter('header main');
                    }
                }
                if (response.url) {
                    draftUpdateUrl = response.url;
                }
            }
        });
    }

    if (draftTicketId) {
        updateTimer = window.setInterval(updateActivity, draftDelayPeriod);
    }

    tinyMCE.onAddEditor.add(function(obj, editor) {
        editor.onPostRender.add(function(ed, cm) {
            if (draftText && draftText !== -1) {
                ed.setContent(draftText);
            }
        });
        //editor.onRemove.add(function(ed) {
        //    alert('close');
        //});
        //editor.onEvent.add(function(ed, e) {
        //    console.debug('Editor event occured: ' + e.target.nodeName);
        //});
    });
});