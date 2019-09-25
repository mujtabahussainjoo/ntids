define([
    'underscore',
    'ko',
    'uiComponent',
    'Magento_Ui/js/lib/collapsible',
    'jquery',
    'Mirasvit_Helpdesk/js/lib/jquery.MultiFile'
], function (_, ko, Component, Collapsible, $) {
    'use strict';

    /*!
     * ----------------------------------------------------------------------------
     * "THE BEER-WARE LICENSE" (Revision 42):
     * <jevin9@gmail.com> wrote this file. As long as you retain this notice you
     * can do whatever you want with this stuff. If we meet some day, and you think
     * this stuff is worth it, you can buy me a beer in return. Jevin O. Sewaruth
     * ----------------------------------------------------------------------------
     *
     * Autogrow Textarea Plugin Version v3.0
     * http://www.technoreply.com/autogrow-textarea-plugin-3-0
     *
     * THIS PLUGIN IS DELIVERD ON A PAY WHAT YOU WHANT BASIS. IF THE PLUGIN WAS USEFUL TO YOU, PLEASE CONSIDER BUYING THE PLUGIN HERE :
     * https://sites.fastspring.com/technoreply/instant/autogrowtextareaplugin
     *
     * Date: October 15, 2012
     */

    $.fn.autoGrow = function(options) {
        return this.each(function() {
            var settings = jQuery.extend({
                extraLine: true
            }, options);

            var createMirror = function(textarea) {
                jQuery(textarea).after('<div class="autogrow-textarea-mirror"></div>');
                return jQuery(textarea).next('.autogrow-textarea-mirror')[0];
            };

            var sendContentToMirror = function (textarea) {
                mirror.innerHTML = String(textarea.value)
                        .replace(/&/g, '&amp;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/ /g, '&nbsp;')
                        .replace(/\n/g, '<br />') +
                    (settings.extraLine? '.<br/>.' : '')
                ;
                if (jQuery(mirror).height() < 150) {
                    return;
                }
                if (jQuery(textarea).height() != jQuery(mirror).height())
                    jQuery(textarea).height(jQuery(mirror).height());
            };

            var growTextarea = function () {
                sendContentToMirror(this);
            };

            // Create a mirror
            var mirror = createMirror(this);

            // Style the mirror
            mirror.style.display = 'none';
            //mirror.style.wordWrap = 'break-word';
            mirror.style.whiteSpace = 'normal';
            mirror.style.padding = jQuery(this).css('padding');
            mirror.style.width = jQuery(this).css('width');
            mirror.style.fontFamily = jQuery(this).css('font-family');
            mirror.style.fontSize = jQuery(this).css('font-size');
            mirror.style.lineHeight = jQuery(this).css('line-height');

            // Style the textarea
            this.style.overflow = "hidden";
            this.style.minHeight = this.rows+"em";

            // Bind the textarea's event
            this.onkeyup = growTextarea;

            // Fire the event for text already present
            sendContentToMirror(this);

        });
    };

    return Component.extend({
        replyType: ko.observable('public'),
        defaults: {
            template: 'Mirasvit_Helpdesk/reply-area'
        },

        initialize: function () {
            this._super();
            this._bind();

            return this;
        },
        _bind: function () {
            var defaultClasses = '';
            $('body').on('helpdesk-switch-reply-type', function (e, v) {
                var textarea = $('[data-field=helpdesk-reply-field] textarea');
                if (!defaultClasses) {
                    defaultClasses = textarea.attr('class');
                }
                textarea.attr('class', defaultClasses + ' ' + v);
            });
            $('body').on('helpdesk-insert-quick-response', function (e, body) {
                if(typeof tinyMCE != 'undefined' && tinyMCE.activeEditor != null &&
                    document.getElementsByClassName('mceEditor').length) {
                    tinyMCE.activeEditor.setContent(tinyMCE.activeEditor.getContent() + body);

                } else {
                    var textarea = $('[data-field=helpdesk-reply-field] textarea');
                    var val = textarea.val();
                    if (val != '') {
                        val = val + "\n"
                    }
                    textarea.val(val + body);
                }
            });
        },
        afterFileInputRender: function () {
            $('.multi').MultiFile();

            var $replyArea = $('[data-field=helpdesk-reply-field] textarea');
            setInterval(function() {
                updateSaveBtn();
            }, 500);

            var updateSaveBtn = function () {
                var saveButton = $('#save-split-button-save-button,#save-split-button-button,#save-split-button-close-button');
                var editButton = $('#save-split-button-save-continue-button,#save-split-button-edit-button');

                if ($replyArea.val() == '') {
                    saveButton.html('Save');
                    editButton.html('Save & Continue Edit');
                } else {
                    saveButton.html('Save & Send Message');
                    editButton.html('Save, Send & Continue Edit');
                }
            };

            setTimeout(function() {
                updateTextarea();
                updateWysiwyg(); // wysiwyg does not show from first time
                var $replyArea = $('[data-field=helpdesk-reply-field] textarea');
                $($replyArea).autoGrow();
            }, 500);

            var updateTextarea = function () {
                $('body').trigger('helpdesk-switch-reply-type', $('[data-field="reply_type"]').val());
            };

            var updateWysiwyg = function () {
                if (!$('#reply_parent').length && $('#togglereply').length) {
                    $('#togglereply').click();
                }
            };
        }
    });
});
