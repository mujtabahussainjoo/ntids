var config = {
    paths: {
        'mcolorpicker': 'Infortis_Infortis/js/jquery/mcolorpicker/mcolorpicker', // Deprecated
        'spectrum': 'Infortis_Infortis/js/jquery/spectrum/spectrum'
    },
    shim: {
        'mcolorpicker': {
            'deps': ['jquery'],
            'init': function(jQuery) {
                jQuery.fn.mColorPicker.defaults.imageFolder = require.toUrl('') + 'Infortis_Infortis/js/jquery/mcolorpicker/images/';
                jQuery.fn.mColorPicker.init.replace = false;
                jQuery.fn.mColorPicker.init.allowTransparency = true;
                jQuery.fn.mColorPicker.init.showLogo = false;
            }
        }
    }
};
