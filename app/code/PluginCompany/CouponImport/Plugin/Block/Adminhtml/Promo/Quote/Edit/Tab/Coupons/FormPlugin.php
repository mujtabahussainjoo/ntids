<?php
/**
 * Created by:  Milan Simek
 * Company:     Plugin Company
 *
 * LICENSE: http://plugin.company/docs/magento-extensions/magento-extension-license-agreement
 *
 * YOU WILL ALSO FIND A PDF COPY OF THE LICENSE IN THE DOWNLOADED ZIP FILE
 *
 * FOR QUESTIONS AND SUPPORT
 * PLEASE DON'T HESITATE TO CONTACT US AT:
 *
 * SUPPORT@PLUGIN.COMPANY
 */
namespace PluginCompany\CouponImport\Plugin\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons;

class FormPlugin
{
    /**
     * Adds a fieldset legend to the coupon generation section
     *
     * @param $subject
     * @param $form
     */
    public function beforeSetForm($subject, $form)
    {
        $infoFieldset = $form->getElement('information_fieldset');
        $infoFieldset
            ->setLegend(__('Generate Coupon Codes'))
            ->setClass('fieldset-small')
        ;
    }
}
