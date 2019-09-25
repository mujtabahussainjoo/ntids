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
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Helper;

class Attachment extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Mirasvit\Helpdesk\Model\AttachmentFactory
     */
    protected $attachmentFactory;

    /**
     * @param \Mirasvit\Helpdesk\Model\Config            $config
     * @param \Mirasvit\Helpdesk\Model\AttachmentFactory $attachmentFactory
     * @param \Magento\Framework\Module\Dir\Reader       $moduleReader
     * @param \Magento\Framework\App\Helper\Context      $context
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\Config $config,
        \Mirasvit\Helpdesk\Model\AttachmentFactory $attachmentFactory,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->attachmentFactory = $attachmentFactory;
        $this->moduleReader      = $moduleReader;
        $this->config            = $config;
        $this->context           = $context;

        parent::__construct($context);
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Message $message
     * @return void
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveAttachments($message)
    {
        if (!isset($_FILES['attachment']['name'])) {
            return;
        }
        $maxSize = (int) ($this->fileUploadMaxSize() / 1000000);
        $i = 0;

        $errors = [];
        foreach ($_FILES['attachment']['name'] as $name) {
            // echo $name;
            if ($name == '') {
                continue;
            }
            if ($_FILES['attachment']['tmp_name'][$i] == '') {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("Can't upload file %1 . Max allowed upload size is %2 MB.", $name, $maxSize)
                );
            }
            //@fixme - need to check for max upload size and alert error
            $body = file_get_contents(addslashes($_FILES['attachment']['tmp_name'][$i]));
            //create and save attachment
            $attachment = $this->attachmentFactory->create()
                ->setName($name)
                ->setType(strtoupper($_FILES['attachment']['type'][$i]))
                ->setSize($_FILES['attachment']['size'][$i])
                ->setMessageId($message->getId())
                ->setBody($body);

            if ($attachment->getIsAllowed() === 0) {
                $extensions = implode(', ', $this->config->getGeneralAllowedAttachments());

                $errors[] = __('Allows only this file types: %1', $extensions);
                continue;
            }
            ++$i;
        }
        if ($errors) {
            throw new \Magento\Framework\Exception\LocalizedException($errors[0]);
        }
    }

    /**
     * @param string $filename
     * @param string $mimetype
     * @return bool
     */
    public function isAllowedExtension($filename, $mimetype)
    {
        $allowedExtensions = $this->config->getGeneralAllowedAttachments();
        if (!$allowedExtensions) {
            return true;
        }

        $result = false;
        $parts  = explode('.', $filename);
        $types  = require($this->moduleReader->getModuleDir('etc', 'Mirasvit_Helpdesk').'/mime_types.php');
        if ($parts) {
            $extension = strtolower(end($parts));
            if (in_array($extension, $allowedExtensions) && $types[$extension] == $mimetype) {
                $result = true;
            }
        } else {
            foreach ($allowedExtensions as $extension) {
                if ($types[trim($extension)] == $mimetype) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Get max upload size in bytes.
     *
     * @return float
     */
    private function fileUploadMaxSize()
    {
        static $maxSize = -1;
        if ($maxSize < 0) {
            $maxSize = $this->parseSize(ini_get('post_max_size'));
            $uploadMax = $this->parseSize(ini_get('upload_max_filesize'));
            if ($uploadMax > 0 && $uploadMax < $maxSize) {
                $maxSize = $uploadMax;
            }
        }
        return $maxSize;
    }

    /**
     * @param int $size
     *
     * @return float
     */
    private function parseSize($size)
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
    }
}
