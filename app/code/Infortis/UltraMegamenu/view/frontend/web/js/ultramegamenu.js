;(function ($, window, document, undefined) {

    $.widget( "infortis.ultramegamenu", {

        options: {
            mobileMenuThreshold: 770
            , mode: 0 //dual
            , itemSelector: 'li'
            , panelSelector: '.nav-panel'
            , openerSelector: '.opener'
            , vertnavTriggerSelector: undefined
            , isVerticalLayout: false
            , mobileClasses: 'nav-mobile nav-acco'
            , mobnavTriggerableClasses: 'nav-mobile-triggerable'
            , regularClasses: 'nav-regular'
            , vertnavClasses: 'nav-vert'
            , vertnavTriggerableClasses: 'nav-vert-triggerable'
            , initMobileMenuCollapsed: true
            , initVerticalMenuCollapsed: true

            //Options for dropdown
            , outermostContainer: undefined
            , fullWidthDdContainer: undefined
            , ddDelayIn: 50
            , ddDelayOut: 200
            , ddAnimationDurationIn: 50
            , ddAnimationDurationOut: 200
            , isTouchDevice: ('ontouchstart' in window) || (navigator.msMaxTouchPoints > 0)
        }

        , isMobile: false
        , bar: undefined
        , itemsList: undefined
        , panels: undefined
        , mobnavTrigger: undefined
        , vertnavTrigger: undefined
        , outermostContainerIsWindow: false
        , fullWidthDdContainerIsWindow: false

        , _create: function() {
            this._initPlugin();
        }

        , _initPlugin : function()
        {
            var _self = this;

            //Initialize plugin basic properties
            //--------------------------------------------------------------

            this.bar = this.element;
            this.itemsList = this.bar.children('ul');
            this.panels = this.bar.find(this.options.panelSelector);

            //Find mobnav trigger:
            this.mobnavTrigger = this.bar.parent().children('.mobnav-trigger'); //bar

            //Find vertnav trigger.
            //Variable indicates triggerable vertical menu in case the vertnav trigger is not next to the menu bar.
            if (this.options.vertnavTriggerSelector !== undefined)
            {
                this.vertnavTrigger = $(this.options.vertnavTriggerSelector);
            }
            else
            {
                this.vertnavTrigger = this.bar.parent().children('.vertnav-trigger'); //bar
            }
            // //Find vertnav trigger (previous code)
            // this.vertnavTrigger = this.bar.parent().children('.vertnav-trigger'); //bar

            //If mobnav trigger exists
            if (this.mobnavTrigger.length)
            {
                //Add triggerable mobile menu classes to mobile menu classes collector
                this.options.mobileClasses += ' ' + this.options.mobnavTriggerableClasses;
            }

            //If vertical layout enabled
            if (this.options.isVerticalLayout)
            {
                //Add vertical menu classes to regular menu classes collector
                this.options.regularClasses += ' ' + this.options.vertnavClasses;

                //If vertnav trigger exists
                if (this.vertnavTrigger.length)
                {
                    //Add triggerable vertical menu classes to regular menu classes collector
                    this.options.regularClasses += ' ' + this.options.vertnavTriggerableClasses;
                }
            }

            //Activate menu based on mode
            //--------------------------------------------------------------

            //Initialize menu based on menu mode
            if (this.options.mode === 0)
            {
                this._initDualMode();
            }
            else if (this.options.mode === -1)
            {
                this._initMobileMode();
            }

            //Initialize plugin secondary properties - after activating
            //--------------------------------------------------------------

            //TODO: what if outermostContainer is undefined when vert nav is enabled. Maybe in such case we need to replace this value
            //with primary header inner container.
            if (this.options.outermostContainer === undefined)
            {
                //If outermost container (for dropdowns) not defined, use the menu bar
                this.options.outermostContainer = this.bar;
            }
            else if (this.options.outermostContainer === 'window')
            {
                this.print('this.options.outermostContainer === window'); ///
                //If outermost container is the window
                this.options.outermostContainer = $(window);
                this.outermostContainerIsWindow = true;
            }

            if (this.options.fullWidthDdContainer === undefined)
            {
                //If container not defined, use the menu bar
                this.options.fullWidthDdContainer = this.bar;
            }
            else if (this.options.fullWidthDdContainer === 'window')
            {
                this.print('this.options.fullWidthDdContainer === window'); ///
                //If container is the window
                this.options.fullWidthDdContainer = $(window);
                this.fullWidthDdContainerIsWindow = true;
            }

            //Additional initialization
            //--------------------------------------------------------------

            //Header mode change: from mobile to regular
            // $(document).on('activate-regular-header', function(e, data) {
            //     //Deactivate mobile menu trigger (and collapse menu)
            //     _self.print('on activate-regular-header: fire closeMenuViaMobnavTrigger()'); ///
            //     _self.closeMenuViaMobnavTrigger();
            // }); //end: on event

            //Attach event handlers for selected events
            if (this.mobnavTrigger.length)
            {
                this.hookToMobnavTriggerClick();
            }

            if (this.vertnavTrigger.length)
            {
                this.hookToVertnavTriggerClick();
            }

            this._hookToStickyHeader();

        } //end: _initPlugin

        , _initDualMode : function()
        {
            var _self = this;

            this.itemsList.uaccordion(
                this.options.panelSelector, 
                this.options.openerSelector, 
                this.options.itemSelector
            );

            if ($(window).width() >= this.options.mobileMenuThreshold)
            {
                _self._cleanUpAfterMobileMenu(); //Required for IE8
            }

            enquire
                .register('screen and (max-width: ' + (this.options.mobileMenuThreshold - 1) + 'px)', {
                    match: function() {
                        _self._activateMobileMenu();
                    },
                    unmatch: function() {
                        _self._cleanUpAfterMobileMenu();
                    }
                })
                .register('screen and (min-width: ' + this.options.mobileMenuThreshold + 'px)', {
                    deferSetup: true,
                    setup: function() {
                        _self._cleanUpAfterMobileMenu();
                    },
                    match: function() {
                        _self._activateRegularMenu();
                    },
                    unmatch: function() {
                        _self._prepareMobileMenu();
                    }
                });
        }

        , _initMobileMode : function()
        {
            this.itemsList.uaccordion(
                this.options.panelSelector, 
                this.options.openerSelector, 
                this.options.itemSelector
            );
            this._activateMobileMenu();
        }

        , _activateMobileMenu : function()
        {
            //alert('_activateMobile'); ///
            this.print('_activateMobileMenu'); ///
            this.isMobile = true;
            this.bar.addClass(this.options.mobileClasses).removeClass(this.options.regularClasses); //bar
            //alert('_activateMobile after addClass'); ///

            //Hide and deactivate vertical menu trigger (and collapse menu)
            this.print('_activateMobileMenu: fire closeMenuViaVertnavTrigger()'); ///
            this.vertnavTrigger.hide() //Hide vertical menu trigger before deactivating
            this.closeMenuViaVertnavTrigger();

            //Deactivate mobile menu trigger
            this.closeMenuViaMobnavTrigger();

            //alert('_activateMobile before show() trigger'); ///

            //Show mobile menu trigger
            this.mobnavTrigger.show();

            this.print('trigger: activate-MOBILE-menu'); ///
            $(document).trigger("activate-mobile-menu");
        }

        , _activateRegularMenu : function() //Default state
        {
            //alert('activateRegular'); ///
            this.print('activate_REGULAR_Menu'); ///
            this.isMobile = false;
            this.bar.addClass(this.options.regularClasses).removeClass(this.options.mobileClasses); //bar

            //alert('activateRegular after addClass'); ///

            //Deactivate mobile menu trigger (and collapse menu)
            this.print('activate_REGULAR_Menu: fire closeMenuViaMobnavTrigger()'); ///
            this.mobnavTrigger.hide(); //Hide mobile menu trigger before deactivating
            this.closeMenuViaMobnavTrigger();

            //Deactivate vertical menu trigger
            this.closeMenuViaVertnavTrigger();

            //alert('activateRegular before show() trigger'); ///

            //Show vertical menu trigger
            this.vertnavTrigger.show();

            this.print('trigger: activate-REGULAR-menu'); ///
            $(document).trigger("activate-regular-menu");
        }

        , _cleanUpAfterMobileMenu : function()
        {
            this.print('_cleanUpAfterMobileMenu'); ///
            //Remove "display" modifications from all panels
            this.panels.css('display', '');
        }

        , _prepareMobileMenu : function()
        {
            this.print('_prepareMobileMenu'); ///
            //Hide all panels
            this.panels.hide();

            //Show panels of items with active class
            this.itemsList.find('.item-active').each( function() {
                $(this).children('.nav-panel').show();
            });
        }

        , openMenuViaMobnavTrigger : function()
        {
            this.print('-> openMenuViaMobnavTrigger()'); ///
            this.mobnavTrigger.addClass('active');
            this.bar.addClass('show'); //bar
        }

        , closeMenuViaMobnavTrigger : function()
        {
            this.print('x- closeMenuViaMobnavTrigger()'); ///
            this.mobnavTrigger.removeClass('active');
            this.bar.removeClass('show'); //bar
        }

        , openMenuViaVertnavTrigger : function()
        {
            this.print('---> openMenuViaVertnavTrigger()'); ///
            this.vertnavTrigger.addClass('active');
            this.bar.addClass('show'); //bar
        }

        , closeMenuViaVertnavTrigger : function()
        {
            this.print('X--- closeMenuViaVertnavTrigger()'); ///
            this.vertnavTrigger.removeClass('active');
            this.bar.removeClass('show'); //bar
        }

        , hookToMobnavTriggerClick : function()
        {
            /**
             * Add event handler for mobile menu trigger click
             */
            var _self = this;

            this.mobnavTrigger.on('click', function(e) {

                _self.print('on mobnavTrigger click'); ///
                if ($(this).hasClass('active'))
                {
                    _self.closeMenuViaMobnavTrigger();
                }
                else
                {
                    _self.openMenuViaMobnavTrigger();
                }
            }); //end: on event

            //If mobile menu should NOT be collapsed, open the menu.
            //Do this only when page loaded with mobile menu.
            this.print('hookToMobnavTriggerClick: isMobile = ' + this.isMobile + ', initCollapsed = ' + this.options.initMobileMenuCollapsed); ///
            if (this.isMobile && this.options.initMobileMenuCollapsed == false)
            {
                //this.print('hookToMobnavTriggerClick: AUTO CLICK!'); ///
                _self.openMenuViaMobnavTrigger();
            }
        }

        , hookToVertnavTriggerClick : function()
        {
            /**
             * Add event handler for vertical menu trigger click
             */
            var _self = this;

            this.vertnavTrigger.on('click', function(e) {

                _self.print('on vertnavTrigger click'); ///
                if ($(this).hasClass('active'))
                {
                    _self.closeMenuViaVertnavTrigger();
                }
                else
                {
                    _self.openMenuViaVertnavTrigger();
                }
            }); //end: on event

            //If vertical menu should NOT be collapsed, open the menu.
            //Do this only when page loaded with regular menu (not mobile menu).
            this.print('hookToVertnavTriggerClick: isMobile = ' + this.isMobile + ', initCollapsed = ' + this.options.initVerticalMenuCollapsed); ///
            if (this.isMobile == false && this.options.initVerticalMenuCollapsed == false)
            {
                //this.print('hookToVertnavTriggerClick: auto click!'); ///
                _self.openMenuViaVertnavTrigger();
            }
        }

        , enableDropdowns : function()
        {
            this.print('enableDropdowns'); ///
            this._hookToItemHoverDynamically();
        }

        , _hookToItemHoverDynamically : function()
        {
            this.print('_hookToItemHoverDynamically'); ///
            var _self = this;

            this.bar.on('mouseenter', 'li.parent.level0', function() {
                
                if (_self.isMobile == false)
                {
                    _self._showDropdown($(this));
                }

            }).on('mouseleave', 'li.parent.level0', function() {

                if (_self.isMobile == false)
                {
                    _self._hideDd($(this));
                }

            }); //end: menu top-level dropdowns

        } //end: enableDropdowns

        , _showDropdown : function(item)
        {
            this.print('_showDdHorizontal: ' + item.children('a').children('span').text() ); ///
            var _self = this;
            var menubar = this.bar;
            var dd = item.children('.nav-panel');
            var isVert = menubar.hasClass('nav-vert');

            //-----------------------------------------------
            //Calculate position of the dropdown (dropdown positioned relative to the menubar).
            var itemPos = item.position();

            //Dropdown position modifiers
            var modX = 0;
            var modY = 0;

            if (isVert)
            {
                //If vertical layout
                modX = item.outerWidth();
            }
            else
            {
                //If horizontal layout
                modY = item.height();
            }

            var ddPos = {
                left: itemPos.left + modX,
                top: itemPos.top + modY
            };
            this.print('_showDdHorizontal: itemPos.left='+ itemPos.left +', itemPos.top='+ itemPos.top +', ddPos.left='+ ddPos.left +', ddPos.top='+ ddPos.top); ///

            //-----------------------------------------------
            //If successfully retrieved the original widht of the dropdown - apply it.
            //The original width can be saved by the code which prevents dropdowns from spilling out.
            var origWidth = dd.data('original-width');
            if (origWidth !== undefined)
            {
                this.print('_showDdHorizontal: get ORIGINAL-width = '+ origWidth ); ///
                dd.width(origWidth);
            }

            //-----------------------------------------------
            //In vertical menu, move the dropdown 1px left to prevent styling issues (inaccurate positionning via JS: not pixel-perfect)
            // if (isVert)
            // {
            //     ddPos.left = ddPos.left - 1;
            // }

            //-----------------------------------------------
            //Initialize basic variables
            //outermostCon                  - the outermost container
            //outermostConWidth             - outermost container width
            //ddWidth                       - dropdown width
            //menubarOffset                 - menubar's offset RELATIVE to the outermost container
            //ddOffset                      - dropdown's offset RELATIVE to the outermost container
            //fullWidthDdCon                - full-width dropdown's container
            //manubarShiftToFullWidthDdCon  - manubar's shift RELATIVE to full-width dropdown's container

            var outermostCon = this.options.outermostContainer;
            var outermostConWidth = outermostCon.outerWidth();
            var ddWidth = dd.outerWidth();
            var fullWidthDdCon = this.options.fullWidthDdContainer;

            //Calculate menubar's offset
            var menubarOffset;
            if (this.outermostContainerIsWindow === false)
            {
                //If the outermost container is NOT the window
                menubarOffset = menubar.offset().left - outermostCon.offset().left;
                this.print('_showDdHorizontal: menubarOffset='+ menubarOffset +' = menubar.offset().left='+ menubar.offset().left  +' + outermostCon.offset().left='+ outermostCon.offset().left); ///
            }
            else
            {
                //If the outermost container is the window, calculate menubarOffset in a simple way
                menubarOffset = menubar.offset().left;
                this.print('_showDdHorizontal: menubarOffset='+ menubarOffset +' = menubar.offset().left='+ menubar.offset().left ); ///
            }

            //Calculate dropdown's offset
            var ddOffset = menubarOffset + ddPos.left;

            //-----------------------------------------------
            //If it's a full-width dropdown
            if (dd.hasClass('full-width'))
            {
                this.print('_showDdHorizontal: -- is --full-width'); ///

                if (isVert)
                {
                    //We need to make the dropdown as wide as the free space (inside the outermost container) next to the menu bar.
                    var freeSpaceWidth = outermostConWidth - (menubarOffset + menubar.outerWidth());
                    dd.width(freeSpaceWidth);

                    this.print('_showDdHorizontal: freeSpaceWidth = outermostConWidth - (menubarOffset + menubar.outerWidth())'); ///
                    this.print('_showDdHorizontal: '+ freeSpaceWidth +' = '+ outermostConWidth +' - ('+ menubarOffset +' + '+ menubar.outerWidth() +')' ); ///
                }
                else //In horizontal menu
                {
                    //Calculate manubar's shift relative to full-width dropdown's container
                    var manubarShiftToFullWidthDdCon;
                    if (this.fullWidthDdContainerIsWindow === false)
                    {
                        //If the container is NOT the window
                        manubarShiftToFullWidthDdCon = menubar.offset().left - fullWidthDdCon.offset().left;
                    }
                    else
                    {
                        //If the container is the window
                        manubarShiftToFullWidthDdCon = menubar.offset().left;
                    }

                    //In horizontal menu, if dropdown is full-width, we need to recalcualte its width and position
                    //to display it relative to full-width dropdown's container.
                    dd.width(fullWidthDdCon.outerWidth());

                    //Dropdowns are positioned relative to the menu bar. Therefore we need to use negative value of the shift.
                    ddPos.left = (-1) * manubarShiftToFullWidthDdCon;

                    this.print('FULL: manubarShiftToFullWidthDdCon='+ manubarShiftToFullWidthDdCon +', fullWidthDdCon.outerWidth()='+ fullWidthDdCon.outerWidth() ); ///
                }
            }
            else //If the dropdown is NOT full-width
            {
                this.print('_showDdHorizontal: -- NON --full-width'); ///

                //Prevent dropdowns from spilling out of the outermost container.
                //Calculate width of that part of the dropdown which sticks out of the outermost container (at the right side).
                var diffRight = (ddOffset + ddWidth) - outermostConWidth;

                this.print('_showDdHorizontal: diffRight = (ddOffset + ddWidth) - outermostConWidth == '+ diffRight ); ///
                this.print('_showDdHorizontal: ddOffset='+ ddOffset  +', ddWidth='+ ddWidth +', outermostConWidth='+ outermostConWidth ); ///

                //If the dropdown sticks out
                if (diffRight > 0)
                {
                    if (isVert)
                    {
                        //We need to make the dropdown as wide as the free space (inside the outermost container) next to the menu bar.
                        var freeSpaceWidth = outermostConWidth - (menubarOffset + menubar.outerWidth());

                        //We need to remember the original width of the dropdown and restore it next time when the dropdown is displayed
                        dd.data('original-width', ddWidth);

                        //Make the dropdown as wide as the free space
                        dd.width(freeSpaceWidth);

                        this.print('_showDdHorizontal: freeSpaceWidth = outermostConWidth - (menubarOffset + menubar.outerWidth())'); ///
                        this.print('_showDdHorizontal: '+ freeSpaceWidth +' = '+ outermostConWidth +' - ('+ menubarOffset +' + '+ menubar.outerWidth() +')' ); ///
                    }
                    else //In horizontal menu
                    {
                        this.print('_showDdHorizontal: sticks out at right side: diffRight > 0' ); ///
                        //Calculate the new (corrected) position of the dropdown.
                        //Calculate the new (corrected) offset of the dropdown.
                        var ddPosLeft_NEW = ddPos.left - diffRight;
                        var diffLeft = ddOffset - diffRight;

                        this.print('_showDdHorizontal: diffLeft = ddOffset - diffRight == '+ diffLeft ); ///

                        //Check, if the dropdown on the new position still sticks out of the outermost container at the other side (left side)
                        if (diffLeft < 0)
                        {
                            this.print('_showDdHorizontal: sticks out at the LEFT side too: diffLeft < 0' ); ///

                            //We need to remember the original width of the dropdown and restore it next time when the dropdown is displayed
                            dd.data('original-width', ddWidth);

                            // if (this.outermostContainerIsWindow === false)
                            // {
                            //     this.print('_showDdHorizontal: outermostContainerIsWindow === false' ); ///
                            // }
                            // else
                            // {
                            //     //If the outermost container is the window.
                            //     this.print('_showDdHorizontal: outermostContainerIsWindow' ); ///
                            // }

                            //Make the dropdown as wide as the outermost container
                            dd.width(outermostConWidth);

                            //Set position to align with the left edge of the outermost container.
                            //Dropdowns are positioned relative to the menu bar. Therefore we need to use negative value of the menubar's offset
                            //(which was calculated relative to the outermost container).
                            ddPos.left = (-1) * menubarOffset;
                        }
                        else
                        {
                            this.print('_showDdHorizontal: ddPosLeft_NEW [ddPos.left - diffRight] = '+ ddPosLeft_NEW ); ///

                            //If the dropdown does NOT stick out of the outermost container at the other side,
                            //apply the new corrected position of the dropdown.
                            ddPos.left = ddPosLeft_NEW;
                        }
                    } //end: in horizontal menu
                } //end: if the dropdown sticks out
            } //end: if the dropdown is NOT full-width

            //-----------------------------------------------
            dd.css({
                    'left': ddPos.left + 'px',
                    'top' : ddPos.top + 'px'
                })
                .stop(true, true)
                .delay(_self.options.ddDelayIn)
                .fadeIn(_self.options.ddAnimationDurationIn, "easeOutCubic");

        } //end: _showDropdown

        , _hideDd : function(item)
        {
            var _self = this;

            item.children(".nav-panel")
                .stop(true, true)
                .delay(_self.options.ddDelayOut)
                .fadeOut(_self.options.ddAnimationDurationOut, "easeInCubic");

        }

        , _hookToStickyHeader : function()
        {
            var _self = this;

            //When sticky header was activated
            $(document).on('activate-sticky-header', function(e) {

                //Hide dropdowns of all top-level items
                _self.itemsList.children('.nav-item--parent').each( function() {
                    $(this).children('.nav-panel').hide();
                    _self.print('instant hide'); ///
                });

            }); //end: on event
        }

        , print: function(msg) {
            //console.log(msg);
        }

    }); //end: widget

})(jQuery, window, document);
