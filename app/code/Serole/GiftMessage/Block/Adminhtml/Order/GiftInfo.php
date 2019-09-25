<?php

  namespace Serole\GiftMessage\Block\Adminhtml\Order;

   use Magento\Sales\Model\Order;

  class GiftInfo extends \Magento\Backend\Block\Widget {


    /**
     * Entity for editing of gift message
     *
     * @var \Magento\Eav\Model\Entity\AbstractEntity
     */
    protected $_entity;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Message factory
     *
     * @var \Magento\GiftMessage\Model\MessageFactory
     */
    protected $_messageFactory;

    /**
     * Message helper
     *
     * @var \Magento\GiftMessage\Helper\Message
     */
    protected $_messageHelper;

    protected $giftImage;

    protected $pdfHelper;

    protected $giftMessage;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\GiftMessage\Model\MessageFactory $messageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\GiftMessage\Helper\Message $messageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\GiftMessage\Model\MessageFactory $messageFactory,
        \Magento\Framework\Registry $registry,
        \Serole\GiftMessage\Model\Image $giftImage,
        \Serole\GiftMessage\Model\Message $giftMessage,
        \Serole\Pdf\Helper\Pdf $pdfHelper,
        \Magento\GiftMessage\Helper\Message $messageHelper,
        array $data = []
    ) {
        $this->_messageHelper = $messageHelper;
        $this->_coreRegistry = $registry;
        $this->_messageFactory = $messageFactory;
        $this->giftImage = $giftImage;
        $this->giftMessage = $giftMessage;
        $this->pdfHelper = $pdfHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder(){
        return $this->_coreRegistry->registry('current_order');
    }

    public function getGiftMessage($incrementId){
        $giftMessageData = $this->giftMessage->getCollection();
        $giftMessageData->addFieldToFilter('order_id',$incrementId);
        return $giftMessageData->getFirstitem()->getData();
    }

    public function getGiftImage($image){
         $imageData = $this->giftImage->getCollection();
         $imageData->addFieldToFilter('id',$image);
         return $imageData->getFirstItem()->getData();

     }

    public function getImagepath($folderName,$imageName){
        $filePath = $this->pdfHelper->getFilePath($folderName,$imageName);
        return $filePath;
    }

    public function getImageUrl($folderName,$imageName){
        $fileUrl = $this->pdfHelper->getFileUrl($folderName,$imageName);
        return $fileUrl;
    }
}
