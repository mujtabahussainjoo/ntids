<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-core
 * @version   1.2.75
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Core\Model\Mail\Template;

use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\Mail\Template\FactoryInterface;
use \Magento\Framework\Mail\Template\SenderResolverInterface;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder implements TransportBuilderInterface
{
    public function __construct(
        \Mirasvit\Core\Helper\Module $moduleHelper,
        FactoryInterface $templateFactory,
        MessageInterface $message,
        SenderResolverInterface $senderResolver,
        ObjectManagerInterface $objectManager,
        TransportInterfaceFactory $mailTransportFactory
    ) {
        parent::__construct($templateFactory, $message, $senderResolver, $objectManager, $mailTransportFactory);

        $this->moduleHelper = $moduleHelper;

        $this->reset();
    }
    /**
     * {@inheritdoc}
     */
    public function addAttachment(
        $body,
        $mimeType = \Zend_Mime::TYPE_OCTETSTREAM,
        $disposition = \Zend_Mime::DISPOSITION_ATTACHMENT,
        $encoding = \Zend_Mime::ENCODING_BASE64,
        $filename = null
    ) {
        if ($body instanceof \Fooman\EmailAttachments\Model\Api\AttachmentInterface &&
            $this->moduleHelper->isFoomanEmailAttachmentsEnable()
        ) {
            $mimeType    = $body->getMimeType();
            $disposition = $body->getDisposition();
            $encoding    = $body->getEncoding();
            $filename    = $this->encodedFileName($body->getFilename());
            $body        = $body->getContent();
        }

        $this->message->createAttachment($body, $mimeType, $disposition, $encoding, $filename);

        return $this;
    }
}
