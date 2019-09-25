/**
 * @author     Kristof Ringleff
 * @package    Fooman_SurchargePayment
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
var config = {
    config: {
        mixins: {
            'Magento_Sales/order/create/scripts': {
                'Fooman_SurchargePayment/js/reload-totals': true
            }
        }
    }
};