(function(require){
    (function() {
        /**
         * Copyright © 2016 Magento. All rights reserved.
         * See COPYING.txt for license details.
         */

        var config = {
            "waitSeconds": 0,
            "map": {
                "*": {
                    "ko": "knockoutjs/knockout",
                    "knockout": "knockoutjs/knockout",
                    "mageUtils": "mage/utils/main",
                    "rjsResolver": "mage/requirejs/resolver"
                }
            },
            "shim": {
                "jquery-migrate": ["jquery"],
                "jquery/jquery.hashchange": ["jquery", "jquery-migrate"],
                "jquery/jstree/jquery.hotkeys": ["jquery"],
                "jquery/hover-intent": ["jquery"],
                "mage/adminhtml/backup": ["prototype"],
                "mage/captcha": ["prototype"],
                "mage/common": ["jquery"],
                "mage/new-gallery": ["jquery"],
                "mage/webapi": ["jquery"],
                "jquery/ui": ["jquery"],
                "MutationObserver": ["es6-collections"],
                "tinymce": {
                    "exports": "tinymce"
                },
                "moment": {
                    "exports": "moment"
                },
                "matchMedia": {
                    "exports": "mediaCheck"
                },
                "jquery/jquery-storageapi": {
                    "deps": ["jquery/jquery.cookie"]
                }
            },
            "paths": {
                "jquery/validate": "jquery/jquery.validate",
                "jquery/hover-intent": "jquery/jquery.hoverIntent",
                "jquery/file-uploader": "jquery/fileUploader/jquery.fileupload-fp",
                "jquery/jquery.hashchange": "jquery/jquery.ba-hashchange.min",
                "prototype": "legacy-build.min",
                "jquery/jquery-storageapi": "jquery/jquery.storageapi.min",
                "text": "mage/requirejs/text",
                "domReady": "requirejs/domReady",
                "tinymce": "tiny_mce/tiny_mce_src"
            },
            "deps": [
                "jquery-migrate"
            ],
            "config": {
                "mixins": {
                    "jquery/jstree/jquery.jstree": {
                        "mage/backend/jstree-mixin": true
                    }
                }
            }
        };

        require(['jquery'], function ($) {
            $.noConflict();
        });

        require.config(config);
    })();

    (function() {
        var config = {
            paths: {
                touchSwipe:             'TouchSwipe',
                themepunchGS:           'themepunch_gs',
                TweenLite:              'TweenLite',
                TimelineLite:           'TimelineLite',
                EasePack:               'EasePack',
                CSSPlugin:              'CSSPlugin',
                SplitText:              'SplitText',
                waitForImages:          'waitForImages',
                themepunchTools:        'jquery.themepunch.tools.min',
                themepunchRevolution:   'jquery.themepunch.revolution.min'
            },
            shim: {
                themepunchTools: {
                    deps: ['themepunchGS', 'TweenLite', 'TimelineLite', 'EasePack', 'CSSPlugin', 'SplitText', 'waitForImages', 'touchSwipe'],
                    exports: 'punchgs'
                },
                themepunchRevolution: {
                    deps: ['jquery', 'themepunchTools'],
                    exports: 'nwdjQuery'
                }
            }
        };
        require.config(config);
    })();

})(require);