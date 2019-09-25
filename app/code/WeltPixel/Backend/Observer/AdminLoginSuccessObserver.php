<?php
namespace WeltPixel\Backend\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * AdminLoginSuccessObserver observer
 *
 */
class AdminLoginSuccessObserver implements ObserverInterface
{
    /**
     * @var \WeltPixel\Backend\Helper\License
     */
    protected $wpHelper;

    /**
     * @param \WeltPixel\Backend\Helper\License $wpHelper
     */
    public function __construct(
        \WeltPixel\Backend\Helper\License $wpHelper
    ) {
        $this->wpHelper = $wpHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->wpHelper->checkAndUpdate();
        $this->wpHelper->updMdsInf();
        return $this;
    }
}
