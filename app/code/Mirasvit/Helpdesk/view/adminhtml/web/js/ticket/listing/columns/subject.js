define([
    'Magento_Ui/js/grid/columns/column',
    'underscore',
    'ko'
], function (Column, _, ko) {
    'use strict';
    
    return Column.extend({
        defaults: {
            bodyTmpl: 'Mirasvit_Helpdesk/ticket/listing/columns/subject'
        },
        
        initialize: function () {
            this._super();
            
            _.bindAll(this, 'onMouseOver', 'onMouseOut');
        },
        
        initObservable: function () {
            this._super();
            
            return this;
        },
        
        isQuickView: function (record) {
            if (record.isQuickView === undefined) {
                record.isQuickView = ko.observable(false);
                record.quickViewPosition = ko.observable('top');
            }
            return record.isQuickView;
        },
        
        onMouseOver: function (record, subject, event) {
            record.isQuickView(true);
            
            var mouseY = event.pageY - window.scrollY;
            var screenY = window.innerHeight;
            
            if (mouseY > screenY / 2) {
                record.quickViewPosition('top');
            } else {
                record.quickViewPosition('bottom');
            }
        },
        
        onMouseOut: function (record) {
            record.isQuickView(false);
        }
    });
});
