;(function ($, window, document, undefined) {

    $.widget("infortis.smartheader", {

        options: {
            mobileHeaderThreshold: 770
            , searchBlockSelector: '#header-search'
            , compareBlockSelector: '#mini-compare'
            , cartBlockSelector: '#minicart'
            , cartDropdownContentSelector: '#header-cart'
            , accountLinksBlockSelector: '#header-account'
            , dropdownBlockClass: 'dropdown-block'
            , dropdownBlockActiveClass: 'active open'
        }

        , rootContainer: undefined
        , mainMenuBlock: undefined
        , mainMenuMarkerMobile: undefined
        , mainMenuMarkerRegular: undefined
        , searchBlock: undefined
        , compareBlock: undefined
        , cartBlock: undefined
        , cartDropdownContent: undefined
        , accountLinksBlock: undefined

        , _create: function()
        {
            this._initPlugin();
        }

        , _initPlugin: function()
        {
            var _self = this;

            // Initialize plugin basic properties
            this.rootContainer = this.element;
            this.searchBlock = $(this.options.searchBlockSelector);
            // this.compareBlock = $(this.options.compareBlockSelector);
            this.cartBlock = $(this.options.cartBlockSelector);
            this.cartDropdownContent = $(this.options.cartDropdownContentSelector);
            this.accountLinksBlock = $(this.options.accountLinksBlockSelector);

            // Activate header mode
            enquire
                .register('screen and (max-width: ' + (this.options.mobileHeaderThreshold - 1) + 'px)', {
                    //deferSetup: true,
                    // setup: function() {
                    //     _self.rootContainer.addClass('header-mobile').removeClass('header-regular').show();
                    // },
                    match: function() {
                        _self._activateMobileHeader();
                    }
                })
                .register('screen and (min-width: ' + this.options.mobileHeaderThreshold + 'px)', {
                    //deferSetup: true,
                    // setup: function() {
                    //     _self.rootContainer.addClass('header-regular').removeClass('header-mobile').show();
                    // },
                    match: function() {
                        _self._activateRegularHeader();
                    }
                });

            // Rest of the initialization needs to be deferred so it's done on document ready
            $(document).ready(function() {
                _self._deferredInit();
            }); //end: on document ready

        } //end: _initPlugin

        , _deferredInit: function()
        {
            var _self = this;

            // Initialize plugin properties
            this._evalMenu();

            // Move elements to their positions
            enquire
                .register('screen and (max-width: ' + (this.options.mobileHeaderThreshold - 1) + 'px)', {
                    deferSetup: true,
                    setup: function() {

                        // Move main menu to mobile position
                        if (_self.mainMenuBlock !== undefined)
                        {
                            _self.mainMenuMarkerMobile.after(_self.mainMenuBlock);
                        }

                    }
                })
                .register('screen and (min-width: ' + this.options.mobileHeaderThreshold + 'px)', {
                    deferSetup: true,
                    setup: function() {

                        // Move main menu to regular position
                        if (_self.mainMenuBlock !== undefined)
                        {
                            _self.mainMenuMarkerRegular.after(_self.mainMenuBlock);
                        }

                    }
                });

        } //end: _deferredInit

        , _evalMenu: function()
        {
            // Check which menu is the main menu on the page
            var menu2   = $('#mainmenu2');
            var menu    = $('#mainmenu');
            if (menu2.length)
            {
                this.mainMenuBlock = menu2;
                this.mainMenuMarkerRegular = $('#nav-marker-regular2');
            }
            else if (menu.length)
            {
                this.mainMenuBlock = menu;
                this.mainMenuMarkerRegular = $('#nav-marker-regular');
            }

            this.mainMenuMarkerMobile = $('#nav-marker-mobile');
        }

        , _activateMobileHeader: function()
        {
            //this.print('trigger: activate-mobile-header'); ///
            this.rootContainer.addClass('header-mobile').removeClass('header-regular');
            $(document).trigger("activate-mobile-header");
            this._moveElementsToMobilePosition();
        }

        , _activateRegularHeader: function()
        {
            //this.print('trigger: activate-regular-header'); ///
            this.rootContainer.addClass('header-regular').removeClass('header-mobile');
            $(document).trigger("activate-regular-header");
            this._moveElementsToRegularPosition();
        }

        , _moveElementsToMobilePosition: function()
        {
            $('#mini-cart-marker-mobile').after(this.cartBlock);
            $('#search-marker-mobile').after(this.searchBlock);
            // $('#mini-compare-marker-mobile').after(this.compareBlock);
            $('#account-links-marker-mobile').after(this.accountLinksBlock);

            // Move main menu
            if (this.mainMenuBlock !== undefined)
            {
                this.mainMenuMarkerMobile.after(this.mainMenuBlock);
            }

            // Reset active state
            $('.skip-active').removeClass('skip-active');
            
            // Disable dropdowns

            // TODO: it may be good idea to disable the dropdowns before moving the dropdown blocks
            this.cartBlock.removeClass(this.options.dropdownBlockClass);
            // this.compareBlock.removeClass(this.options.dropdownBlockClass);

            // Remove "display: block". Otherwise the block would be visible before the skip link is clicked to open the block.
            this.cartDropdownContent.css('display', '');
            // $('#header-compare').css('display', '');
        }

        , _moveElementsToRegularPosition: function()
        {
            $('#mini-cart-marker-regular').after(this.cartBlock);
            $('#search-marker-regular').after(this.searchBlock);
            // $('#mini-compare-marker-regular').after(this.compareBlock);
            $('#account-links-marker-regular').after(this.accountLinksBlock);

            // Move main menu
            if (this.mainMenuBlock !== undefined)
            {
                this.mainMenuMarkerRegular.after(this.mainMenuBlock);
            }

            // Reset active state
            $('.skip-active').removeClass('skip-active');

            // Enable dropdowns
            this.cartBlock.addClass(this.options.dropdownBlockClass);
            this.cartBlock.removeClass(this.options.dropdownBlockActiveClass);
            // this.compareBlock.addClass(this.options.dropdownBlockClass);

            // Check whether dropdown content box is a widget (whether dropdownDialog widget was already initialized).
            // If not, we can't set "display: block" again. When widget will be initialized,
            // it will automatically set "display: block", so we don't need to do this.
            if (this.cartDropdownContent.hasClass('ui-widget-content'))
            {
                // Set again "display: block", it is needed by the mini cart script. See #10.
                this.cartDropdownContent.css('display', 'block');
                // $('#header-compare').css('display', 'block');
            }
        }

        // , print: function(msg)
        // {
        //     console.log(msg);
        // }

    }); //end: widget

})(jQuery, window, document);
