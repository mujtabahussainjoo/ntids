var config = {
    paths: {
        'owlcarousel': 'Infortis_Infortis/js/jquery/jquery.owlcarousel.min',
        'colorbox': 'Infortis_Infortis/js/jquery/jquery.colorbox.min', // Deprecated
        'uaccordion': 'Infortis_Infortis/js/jquery/jquery.uaccordion.min',
        'enquire': 'Infortis_Infortis/js/enquire'
    },
    shim: {
        'colorbox': {
            'deps': ['jquery']
        },
        'owlcarousel': {
            'deps': ['jquery']
        },
        'uaccordion': {
            'deps': ['jquery']
        }
    }
};
