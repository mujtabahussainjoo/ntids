define('themepunchTools', [], function() {

    var punchgs = window.GreenSockGlobals;

    // Sandboxing Greensock
    try{
        window.GreenSockGlobals = null;
        window._gsQueue = null;
        window._gsDefine = null;
    
        delete(window.GreenSockGlobals);
        delete(window._gsQueue);
        delete(window._gsDefine);
    } catch(e) {}
    
    try{
        window.GreenSockGlobals = punchgsSandbox.oldgs;
        window._gsQueue = punchgsSandbox.oldgs_queue;
    } catch(e) {}
        
    // for compatibilty with add-on scripts
    window['punchgs'] = punchgs;

    return punchgs;
});