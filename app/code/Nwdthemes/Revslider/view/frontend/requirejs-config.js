var config = {
    paths: {
        touchSwipe:             'Nwdthemes_Revslider/public/assets/js/tools/TouchSwipe',
        themepunchGS:           'Nwdthemes_Revslider/public/assets/js/tools/themepunch_gs',
        TweenLite:              'Nwdthemes_Revslider/public/assets/js/tools/TweenLite',
        TimelineLite:           'Nwdthemes_Revslider/public/assets/js/tools/TimelineLite',
        EasePack:               'Nwdthemes_Revslider/public/assets/js/tools/easing/EasePack',
        CSSPlugin:              'Nwdthemes_Revslider/public/assets/js/tools/CSSPlugin',
        SplitText:              'Nwdthemes_Revslider/public/assets/js/tools/SplitText',
        waitForImages:          'Nwdthemes_Revslider/public/assets/js/tools/waitForImages',
        themepunchTools:        'Nwdthemes_Revslider/public/assets/js/jquery.themepunch.tools.min',
        themepunchRevolution:   'Nwdthemes_Revslider/public/assets/js/jquery.themepunch.revolution.min',
        vimeoPlayer:            'Nwdthemes_Revslider/public/assets/js/vimeo.player.min'
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