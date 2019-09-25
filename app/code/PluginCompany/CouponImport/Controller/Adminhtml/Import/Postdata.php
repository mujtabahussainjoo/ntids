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
namespace PluginCompany\CouponImport\Controller\Adminhtml\Import;

/**
 * Class Postdata
 * @package PluginCompany\CouponImport\Controller\Adminhtml\Import
 */
class Postdata extends ImportAbstract
{

    /**
     * Get array of coupons from user submitted data
     *
     * @return array
     */
    protected function getCouponArray()
    {
        return explode("\n", $this->getCouponPostData());
    }

    /**
     * Returns submitted coupon form data
     *
     * @return array
     */
    private function getCouponPostData()
    {
        return $this->getRequest()->getParam('coupons');
    }
}
