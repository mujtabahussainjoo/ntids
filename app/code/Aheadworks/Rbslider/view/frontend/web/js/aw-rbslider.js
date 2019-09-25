/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

/**
 * Initialization widget for slider
 *
 * @method resizeBanner
 */
define([
    'jquery',
    'uikit!slideshow'
], function($, UIkit) {
    "use strict";

    $.widget('mage.awRbslider', {
        options: {
            autoplay: true,
            pauseTimeBetweenTransitions: 3000,
            slideTransitionSpeed: 500,
            isStopAnimationMouseOnBanner: true,
            animation: 'fade'
        },

        /**
         * Initialize widget
         */
        _create: function () {
            var self = this,
                slideshow;

            UIkit.on('init.uk.component', function(e, name) {
                if (name == 'slideshow') {
                    self.element.show();
                }
            });
            slideshow = UIkit.slideshow(this.element, {
                autoplay: this.options.autoplay,
                autoplayInterval: this.options.pauseTimeBetweenTransitions,
                duration: this.options.slideTransitionSpeed,
                pauseOnHover: this.options.isStopAnimationMouseOnBanner,
                animation: this.options.animation
            });
            // Rewrite slideshow resize method
            slideshow.resize = function () {
                self.resizeBanner(this);
            };
            // Disable stop animation, if click on slide navigaton or dot navigation
            this.element.on('click.uk.slideshow', '[data-uk-slideshow-item]', function(e) {
                if (slideshow.options.autoplay) {
                    slideshow.start();
                }
            });
            // Slideshow paused, if mouse cursor on slide navigaton or dot navigation
            this.element.on({
                mouseenter: function() {
                    if (slideshow.options.pauseOnHover) {
                        slideshow.hovering = true;
                    }
                },
                mouseleave: function() {
                    slideshow.hovering = false;
                }
            }, '.uk-dotnav, .uk-slidenav');
        },

        /**
         * Recalculate the width and height of the banner
         */
        resizeBanner: function(slideshow) {
            var mainContent = this.element.closest('#maincontent, .page-wrapper'),
                width,
                height = slideshow.options.height;

            // Recalculate width
            if (slideshow.slides.length) {
                width = $(slideshow.slides[0]).find('img.aw-rbslider__img').prop('naturalWidth');
                slideshow.slides.each(function () {
                    width = Math.min(width, $(this).find('img.aw-rbslider__img').prop('naturalWidth'));
                });
            }
            if (mainContent.length) {
                if (mainContent.width() < width) {
                    width = mainContent.width();
                }
                this.element.css('width', width);
            } else if ($('.page-wrapper').length) {
                // AW RBSlider compatibility
                var containerWidth = Math.floor($('.page-wrapper').width() * 0.7);
                
                if (containerWidth < width) {
                    width = containerWidth;
                }
                this.element.css('width', width);
            }
            // Recalculate height
            if (slideshow.options.height === 'auto' && slideshow.slides.length) {
                slideshow.slides.css('height', '');
                height = $(slideshow.slides[0]).height();
                slideshow.slides.each(function () {
                    height = Math.min(height, $(this).height());
                });
                slideshow.container.css('height', height);
                slideshow.slides.css('height', height);
            }
        }
    });

    return $.mage.awRbslider;
});
