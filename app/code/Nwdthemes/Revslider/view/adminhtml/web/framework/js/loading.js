define(
    'loading',
    ['jquery', 'themepunchTools'],
    function(jQuery, punchgs) {

        /* SHOW A WAIT FOR PROGRESS */
        var showWaitAMinute = function(obj) {
            var wm = jQuery('#waitaminute');
            // CHANGE TEXT
            if (obj.text != undefined) {
                switch (obj.text) {
                    case "progress1":

                        break;
                    default:

                        wm.html('<div class="waitaminute-message"><i class="eg-icon-emo-coffee"></i><br>'+obj.text+'</div>');
                        break;
                }
            }

            if (obj.delay!=undefined) {

                punchgs.TweenLite.to(wm,0.3,{autoAlpha:1,ease:punchgs.Power3.easeInOut});
                punchgs.TweenLite.set(wm,{display:"block"});

                setTimeout(function() {
                    punchgs.TweenLite.to(wm,0.3,{autoAlpha:0,ease:punchgs.Power3.easeInOut,onComplete:function() {
                        punchgs.TweenLite.set(wm,{display:"block"});
                    }});
                },obj.delay)
            }

            // SHOW IT
            if (obj.fadeIn !== undefined) {
                punchgs.TweenLite.to(wm,obj.fadeIn/1000,{autoAlpha:1,ease:punchgs.Power3.easeInOut});
                punchgs.TweenLite.set(wm,{display:"block"});
            }

            // HIDE IT
            if (obj.fadeOut !== undefined) {
                punchgs.TweenLite.to(wm,obj.fadeOut/1000,{autoAlpha:0,ease:punchgs.Power3.easeInOut,onComplete:function() {
                        punchgs.TweenLite.set(wm,{display:"block"});
                    }});
            }

        }

        return showWaitAMinute;
    }
);