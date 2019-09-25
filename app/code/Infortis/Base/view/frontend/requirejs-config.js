var config = {
    paths: {
        'smartheader': 'Infortis_Base/js/smartheader',
        'stickyheader': 'Infortis_Base/js/stickyheader',
        'qtycontrol': 'Infortis_Base/js/qtycontrol',
        'expandingsearch': 'Infortis_Base/js/expandingsearch'
    },
    shim: {
        'smartheader': {
            deps: ['jquery', 'jquery/ui', 'enquire']
        },
        'stickyheader': {
            deps: ['jquery', 'jquery/ui', 'enquire']
        },
        'qtycontrol': {
            deps: ['jquery', 'jquery/ui']
        },
        'expandingsearch': {
            deps: ['jquery', 'jquery/ui']
        }
    }
};
