;(function ($, window, document, undefined) {

    $.widget("infortis.stickyheader", {

        options: {
            stickyThreshold: 770
            , cartBlockSelector: '#minicart'
        }

        , isSticky: false
        , isSuspended: false
        , headerContainer: undefined
        , stickyContainer: undefined
        , compareBlock: undefined
        , cartBlock: undefined
        , navHolderBlock1: undefined
        , navHolderBlock2: undefined
        , stickyContainerOffsetTop: 55 //Position of the bottom edge of the sticky container relative to the viewport
        , requiredRecalculation: false //Flag: required recalculation of the position of the bottom edge of the sticky container

        , _create: function() {
            this._initPlugin();
        }

        , _initPlugin: function(customOptions)
        {
            var _self = this;

            //Initialize plugin basic properties
            this.headerContainer = this.element;
            this.stickyContainer = $('.sticky-container'); //Important: by default it's the same element as headerContainer
            this.compareBlock = $('#mini-compare');
            this.cartBlock = $(this.options.cartBlockSelector);
            this.navHolderBlock1 = $('#nav-holder2'); //"#nav-holder1" is reserved for search box
            this.navHolderBlock2 = $('#nav-holder3');
            this.cartMarkerRegular = $('#mini-cart-marker-regular');
            this.compareMarkerRegular = $('#mini-compare-marker-regular');

            //After initializing plugin basic properties
            this.hookToActivatedDeactivated(); //Important: call before activateSticky is called
            this.calculateStickyContainerOffsetTop();
            this.applySticky();
            this.hookToScroll();
            this.hookToResize();

            if (this.options.stickyThreshold > 0)
            {
                enquire.register('(max-width: ' + (this.options.stickyThreshold - 1) + 'px)', {
                    match: function() {
                        _self.suspendSticky();
                    },
                    unmatch: function() {
                        _self.unsuspendSticky();
                    }
                });
            }

        } //end: _initPlugin

        , calculateStickyContainerOffsetTop: function()
        {
            //Calculate the position of the bottom edge of the sticky container relative to the viewport
            this.stickyContainerOffsetTop = this.stickyContainer.offset().top + this.stickyContainer.outerHeight();

            //Important: disable flag
            this.requiredRecalculation = false;
        }

        , applySticky: function()
        {
            if (this.isSuspended) return;

            //If recalculation required
            if (this.requiredRecalculation)
            {
                //Important: recalculate only when header is not sticky
                if (!this.isSticky)
                {
                    this.calculateStickyContainerOffsetTop();
                }
            }

            var viewportOffsetTop = $(window).scrollTop();

            if (viewportOffsetTop > this.stickyContainerOffsetTop)
            {
                if (!this.isSticky)
                {
                    this.activateSticky();
                }
            }
            else
            {
                if (this.isSticky)
                {
                    this.deactivateSticky();
                }
            }
        }

        , activateSticky: function()
        {
            var stickyContainerHeight = this.stickyContainer.outerHeight();
            var originalHeaderHeight = this.headerContainer.css('height');

            //Compensate the change of the header height after the sticky container was removed from its normal position
            this.headerContainer.css('height', originalHeaderHeight);

            //Important: trigger event just before making the header sticky
            //this.print('trigger: activate-STICKY-header'); ///
            $(document).trigger("activate-sticky-header");

            //Make the header sticky
            this.headerContainer.addClass('sticky-header');
            this.isSticky = true;

            //Effect
            this.stickyContainer.css('margin-top', '-' + stickyContainerHeight + 'px').animate({'margin-top': '0'}, 200, 'easeOutCubic');
            //this.stickyContainer.css('opacity', '0').animate({'opacity': '1'}, 300, 'easeOutCubic');
        }

        , deactivateSticky: function()
        {
            //Remove the compensation of the header height change
            this.headerContainer.css('height', '');

            this.headerContainer.removeClass('sticky-header');
            this.isSticky = false;

            //this.print('trigger: deactivate-STICKY-header'); ///
            $(document).trigger("deactivate-sticky-header");
        }

        , suspendSticky: function()
        {
            this.isSuspended = true;

            //Deactivate sticky header.
            //Important: call method only when sticky header is actually active.
            if (this.isSticky)
            {
                this.deactivateSticky();
            }
        }

        , unsuspendSticky: function()
        {
            this.isSuspended = false;

            //Activate sticky header.
            //Important: call applySticky instead of activateSticky to check if activation is needed.
            this.applySticky();
        }

        , hookToScroll: function()
        {
            var _self = this;

            $(window).on("scroll", function(e) {
                _self.applySticky();
            }); //end: on event
        }

        , hookToScrollDeferred: function()
        {
            var _self = this;

            var windowScrollTimeout;
            $(window).on("scroll", function() {
                clearTimeout(windowScrollTimeout);
                windowScrollTimeout = setTimeout(function() {
                    _self.applySticky();
                }, 50);
            }); //end: on event
        }

        , hookToResize: function()
        {
            var _self = this;

            $(window).on('themeResize', function(e) {

                //Require recalculation
                _self.requiredRecalculation = true;

                //Remove the compensation of the header height change
                _self.headerContainer.css('height', '');
            }); //end: on event
        }

        , hookToActivatedDeactivated: function()
        {
            var _self = this;

            //When sticky header was activated
            $(document).on('activate-sticky-header', function(e) {

                if (_self.cartBlock.parent().hasClass('nav-holder') === false)
                {
                    _self.navHolderBlock1.prepend(_self.cartBlock);
                    _self.cartBlock.data('moved', true);
                }

                if (_self.compareBlock.parent().hasClass('nav-holder') === false)
                {
                    _self.navHolderBlock2.prepend(_self.compareBlock);
                    _self.compareBlock.data('moved', true);
                }
            }); //end: on event

            //When sticky header was deactivated
            $(document).on('deactivate-sticky-header', function(e) {

                //Move the block back to the regular position but only:
                // - if the block was moved there dynamically by this script (block is marked with 'moved' data)
                // - if the block is inside a holder

                if (_self.cartBlock.data('moved') && _self.cartBlock.parent().hasClass('nav-holder') === true)
                {
                    _self.cartMarkerRegular.after(_self.cartBlock);
                }

                if (_self.compareBlock.data('moved') && _self.compareBlock.parent().hasClass('nav-holder') === true)
                {
                    _self.compareMarkerRegular.after(_self.compareBlock);
                }

            }); //end: on event
        }

        // , print: function(msg) {
        //     console.log(msg);
        // }

    }); //end: widget

})(jQuery, window, document);
