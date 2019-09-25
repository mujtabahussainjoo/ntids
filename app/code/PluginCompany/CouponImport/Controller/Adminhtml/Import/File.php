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

use Magento\Framework\App\Action\Context;
use Magento\Framework\File\Csv;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\View\Result\PageFactory;
use Magento\SalesRule\Model\RuleRepository;
use PluginCompany\CouponImport\Model\Import;
use Psr\Log\LoggerInterface;

/**
 * Class File
 * @package PluginCompany\CouponImport\Controller\Adminhtml\Import
 */
class File extends ImportAbstract
{
    private $csvProcessor;

    /**
     * File constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Import $couponImporter
     * @param Data $jsonHelper
     * @param LoggerInterface $loggerInterface
     * @param RuleRepository $ruleRepository
     * @param Csv $csvProcessor
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Import $couponImporter,
        Data $jsonHelper,
        LoggerInterface $loggerInterface,
        RuleRepository $ruleRepository,
        Csv $csvProcessor
    ) {
        $this->csvProcessor = $csvProcessor;
        return parent::__construct(
            $context,
            $resultPageFactory,
            $couponImporter,
            $jsonHelper,
            $loggerInterface,
            $ruleRepository
        );
    }
    
    /**
     * Import coupons based on textarea value
     */
    public function execute()
    {
        try {
            $this->runExecute();
        } catch(\Exception $e) {
            $this->handleError($e);
        }
    }
    
    protected function runExecute()
    {
        if (!$this->isUploadedFileAllowed()) {
            $this->sendInvalidFileResponse();
            return;
        }
        parent::execute();
    }

    protected function handleError($e)
    {
        $this->generateErrorMessage();
        $this->loggerInterface->critical($e);
        $this->sendResponse();
    }

    /**
     * Check if uploaded file is plain text
     *
     * @return bool
     */
    private function isUploadedFileAllowed()
    {
        if ($this->getUploadType() == 'text/plain') {
            return true;
        }
        return false;
    }

    /**
     * retrieve coupon array from user submitted data
     *
     * @return array
     */
    protected function getCouponArray()
    {
        return $this->getUploadedCoupons();
    }

    /**
     * Get coupon array from uploaded file
     *
     * @return array
     */
    private function getUploadedCoupons()
    {
        return $this->getCouponArrayFromUploadedFile();
    }

    /**
     * @return array
     */
    private function getCouponArrayFromUploadedFile()
    {
        $coupons = $this->csvProcessor->getData($this->getUploadTmpName());
        return $this->unwrapCouponArray($coupons);
    }

    /**
     * @return mixed
     */
    private function getUploadTmpName()
    {
        return $this->getPostedFileInfo()['tmp_name'];
    }
    
    private function getPostedFileInfo()
    {
        return $this->getRequest()->getFiles()['coupon_upload'];
    }
    
    /**
     * @return mixed
     */
    private function getUploadType()
    {
        return $this->getPostedFileInfo()['type'];
    }
    
    private function unwrapCouponArray($array)
    {
        $func = function($value) {
            return $value[0];
        };
        return array_map($func, $array);
    }


    /**
     * Sends error message if file is invalid
     */
    private function sendInvalidFileResponse()
    {
        $this
            ->generateInvalidFileMessage()
            ->sendResponse();
    }

    private function generateInvalidFileMessage()
    {
        $this
            ->getMessageManager()
            ->addErrorMessage(__('File type not allowed, please select a plain text file.'));
        return $this;
    }
}
