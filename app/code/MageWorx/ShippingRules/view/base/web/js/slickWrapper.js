define([
    'jquery',
    'slick'
], function ($, slick) {
    $.fn.slickWrapper = function (options, el) {
        if (options.el) {
            $(el).find(options.el).slick(options);
        } else {
            $(el).slick(options);
        }
        return this;
    };

    return $.fn.slickWrapper;
});
