;(function ($, window, document, undefined) {

    $.widget("infortis.qtycontrol", {

        options: {
            qtyButtonSelector: '.qty-button'
            , qtyButtonsWrapperSelector: '.qty-buttons-wrapper'
            , errorColor: 'red'
        }

        , inputField: undefined
        , qtyButtons: undefined

        , _create: function()
        {
            this._initPlugin();
        }

        , _initPlugin: function()
        {
            var _self = this;

            // Get quantity field
            this.inputField = this.element;

            // Insert plus/minus buttons
            this.inputField.after('<div class="qty-buttons-wrapper"><div style="margin-bottom:2px;" class="qty-button qtyincrease">+</div><div class="qty-button">-</div></div>');

            // Activate plus/minus buttons
            this.qtyButtons = this.inputField.next(this.options.qtyButtonsWrapperSelector).find(this.options.qtyButtonSelector);
            this.qtyButtons.on('click', function() {

                var $button = $(this);
                var oldValue = _self.inputField.val();
                var newVal = 0;

                if (isNaN(oldValue))
                {
                    _self.inputField.css('color', _self.options.errorColor);
                }
                else
                {
                    if ($button.hasClass('qtyincrease'))
                    {
                        newVal = _self.qtyAdd(parseFloat(oldValue), 1, 4);
                        _self.inputField.css('color', '');
                    }
                    else 
                    {
                        var candidateNewValue = _self.qtySubtract(parseFloat(oldValue), 1, 4);
                        if (oldValue > 0 && candidateNewValue > 0) 
                        {
                            newVal = candidateNewValue;
                        }
                        else
                        {
                            newVal = 0;
                            _self.inputField.css('color', _self.options.errorColor);
                        }
                    }

                    _self.inputField.val(newVal);
                }

            });

        }

        , qtyAdd: function(a, b, precision)
        {
            var x = Math.pow(10, precision || 2);
            return (Math.round(a * x) + Math.round(b * x)) / x;
        }

        , qtySubtract: function(a, b, precision)
        {
            var x = Math.pow(10, precision || 2);
            return (Math.round(a * x) - Math.round(b * x)) / x;
        }

    }); //end: widget

})(jQuery, window, document);
