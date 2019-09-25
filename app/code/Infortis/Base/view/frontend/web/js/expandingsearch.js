;(function ($, window, document, undefined) {

    $.widget("infortis.expandingsearch", {

        options: {
            searchDelayIn: 500
            , searchDelayOut: 300
        }

        , searchBlock: undefined
        , searchField: undefined
        , searchButton: undefined
        , searchOpenTimeout: undefined
        , searchCloseTimeout: undefined

        , _create: function()
        {
            this._initPlugin();
        }

        , _initPlugin: function()
        {
            this.searchBlock = this.element;
            this.searchField = this.searchBlock.find('#search');
            this.searchButton = this.searchBlock.find('#action-search');

            this._initButtonClick();
            this._initBlockMouseenter();
            this._initFieldFocusin();
            this._initFieldFocusout();
        }

        , open: function()
        {
            this.searchBlock.addClass('_active');
        }

        , openDelayed: function()
        {
            var _self = this;

            clearTimeout(_self.searchOpenTimeout);
            _self.searchOpenTimeout = setTimeout(function() {
                _self.open();
            }, _self.options.searchDelayIn);
        }

        , close: function()
        {
            this.searchBlock.removeClass('_active');
            $('#search_autocomplete').hide();
        }

        , closeDelayed: function()
        {
            var _self = this;

            clearTimeout(_self.searchCloseTimeout);
            _self.searchCloseTimeout = setTimeout(function() {
                _self.close();
            }, _self.options.searchDelayOut);
        }

        , _initFieldFocusin: function()
        {
            var _self = this;

            // Field - get focus.
            // Needed when field IS empty (so button is disabled and can't be clicked) and block is NOT active yet.
            // In that case when user clicks on the field and quickly moves the mouse out, the click would start the opening procedure
            // but the mouse out would also instantly start the closing procedure. So the block would not open.
            // This event handler will make sure to open the block.
            _self.searchField.on('focusin', function(event) {
                _self.open();
            });
        }

        , _initFieldFocusout: function()
        {
            var _self = this;

            // Field - loose focus
            _self.searchField.on('focusout', function(event) {
                _self.close();
            });
        }

        , _initButtonClick: function()
        {
            var _self = this;

            // Button - on click.
            // Needed when field is NOT empty and block is NOT active yet
            // to prevent starting the search action when field is not visible to the user.
            _self.searchButton.on('click', function(event) {
                if (_self.searchBlock.hasClass('_active') === false)
                {
                    _self.open();

                    // If block not active and field not empty, open but prevent the click
                    if ($.trim(_self.searchField.val()))
                    {
                        event.preventDefault();
                    }
                }
            });
        }

        , _initBlockMouseenter: function()
        {
            var _self = this;

            // Block - on mouseenter.
            // It's optional. It's useful when field IS empty and block is NOT active yet.
            // In this case the button is disabled by Magento so user can't open the search block by button click.
            _self.searchBlock.on('mouseenter', function(event) {

                clearTimeout(_self.searchCloseTimeout); // Clear on mouse enter to stop the closing action
                _self.openDelayed();

            }).on('mouseleave', function(event) {

                clearTimeout(_self.searchOpenTimeout); // Clear on mouse leave to stop the openning action
                if (_self.searchField.is(":focus") === false)
                {
                    _self.closeDelayed();
                }

            });
        }

    }); //end: widget

})(jQuery, window, document);
