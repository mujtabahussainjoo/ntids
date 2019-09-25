define('show_premium_dialog', ['jquery'], function(jQuery) {

    return function (clicked) {
        jQuery('#rs-premium-benefits-dialog').dialog({
            width: 830,
            height: 750,
            modal: true,
            resizable: false,
            open: function (ui) {
                var dialog = jQuery(ui.target).parent(),
                    dialogtitle = dialog.find('.ui-dialog-title');

                dialog.addClass("rs-open-premium-benefits-dialog-container");
                if (!dialogtitle.hasClass("titlechanged")) {
                    dialogtitle.html("");
                    dialogtitle.append(jQuery('#rs-premium-benefits-dialog .rs-premium-benefits-dialogtitles'));
                    dialogtitle.addClass("titlechanged");
                }

                //HIDE TITLE
                jQuery('#rs-library-license-info-dialogtitle, #rs-plugin-object-library-feedback-title, #rs-wrong-purchase-code-title, #rs-plugin-update-feedback-title, #rs-plugin-download-template-feedback-title').hide();
                jQuery('#rs-premium-benefits-dialog').removeClass("nomainbg")
                //HIDE CONTENT
                jQuery('#basic_objectlibrary_license_block, #basic_premium_benefits_block').hide();

                switch (clicked) {
                    case "regsiter-to-access-update-none":
                        jQuery('#rs-plugin-update-feedback-title').show();
                        jQuery('#basic_premium_benefits_block').show();
                        break;
                    case "regsiter-to-access-store-none":
                        jQuery('#rs-plugin-download-template-feedback-title').show();
                        jQuery('#basic_premium_benefits_block').show();
                        break;
                    case "register-wrong-purchase-code":
                        jQuery('#rs-wrong-purchase-code-title').show();
                        jQuery('#basic_premium_benefits_block').show();
                        break;
                    case "register-to-acess-object-library":
                        jQuery('#rs-plugin-object-library-feedback-title').show();
                        jQuery('#basic_premium_benefits_block').show();
                        break;
                    case "licence_obect_library":
                        jQuery('#basic_objectlibrary_license_block').show();
                        jQuery('#rs-library-license-info-dialogtitle').show();
                        jQuery('#rs-premium-benefits-dialog').addClass("nomainbg");
                        break;
                }
            }
        });
    }

});
