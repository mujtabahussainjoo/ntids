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



namespace Mirasvit\Helpdesk\Model;

use Magento\Framework\DataObject\IdentityInterface;

/**
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Attachment\Collection|
 * \Mirasvit\Helpdesk\Model\Attachment[] getCollection()
 * @method \Mirasvit\Helpdesk\Model\Attachment load(int $id)
 * @method bool getIsMassDelete()
 * @method \Mirasvit\Helpdesk\Model\Attachment setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method \Mirasvit\Helpdesk\Model\Attachment setIsMassStatus(bool $flag)
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Attachment getResource()
 * @method int getSize()
 * @method \Mirasvit\Helpdesk\Model\Attachment setSize(int $param)
 * @method int getMessageId()
 * @method \Mirasvit\Helpdesk\Model\Attachment setMessageId(int $param)
 * @method int getEmailId()
 * @method \Mirasvit\Helpdesk\Model\Attachment setEmailId(int $param)
 * @method string getStorage()
 * @method \Mirasvit\Helpdesk\Model\Attachment setStorage(string $param)
 * @method \Mirasvit\Helpdesk\Model\Attachment setName(string $param)
 * @method string getType()
 * @method \Mirasvit\Helpdesk\Model\Attachment setType(string $param)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $param)
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $param)
 * @method $this setExternalId(string $param)
 */
class Attachment extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'helpdesk_attachment';

    /**
     * @var string
     */
    protected $_cacheTag = 'helpdesk_attachment';

    /**
     * @var string
     */
    protected $_eventPrefix = 'helpdesk_attachment';

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * @var \Mirasvit\Helpdesk\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Helpdesk\Helper\StringUtil
     */
    protected $helpdeskString;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlManager;

    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $backendUrlManager;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Model\ResourceModel\AbstractResource
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Data\Collection\AbstractDb
     */
    protected $resourceCollection;

    /**
     * @param \Mirasvit\Helpdesk\Model\Config                         $config
     * @param \Mirasvit\Helpdesk\Helper\Attachment                    $attachmentHelper
     * @param \Mirasvit\Helpdesk\Helper\StringUtil                    $helpdeskString
     * @param \Magento\Framework\UrlInterface                         $urlManager
     * @param \Magento\Backend\Model\Url                              $backendUrlManager
     * @param \Magento\Framework\Filesystem                           $filesystem
     * @param \Magento\Framework\Model\Context                        $context
     * @param \Magento\Framework\Registry                             $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection
     * @param array                                                   $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\Config $config,
        \Mirasvit\Helpdesk\Helper\Attachment $attachmentHelper,
        \Mirasvit\Helpdesk\Helper\StringUtil $helpdeskString,
        \Magento\Framework\UrlInterface $urlManager,
        \Magento\Backend\Model\Url $backendUrlManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->config             = $config;
        $this->attachmentHelper   = $attachmentHelper;
        $this->helpdeskString     = $helpdeskString;
        $this->urlManager         = $urlManager;
        $this->backendUrlManager  = $backendUrlManager;
        $this->filesystem         = $filesystem;
        $this->context            = $context;
        $this->registry           = $registry;
        $this->resource           = $resource;
        $this->resourceCollection = $resourceCollection;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Construct.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Helpdesk\Model\ResourceModel\Attachment');
    }

    /**
     * @param bool $emptyOption
     *
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    /**
     * @return string
     */
    public function getBackendUrl()
    {
        return $this->backendUrlManager->getUrl('helpdesk/ticket/attachment', ['id' => $this->getId()]);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->urlManager->getUrl('helpdesk/ticket/attachment', ['id' => $this->getExternalId()]);
    }

    /**
     * Depending of storage type, recovers body of attachment either from database or from file system.
     *
     * @return string
     */
    public function getBody()
    {
        if ($this->getStorage() == Config::ATTACHMENT_STORAGE_FS) {
            return file_get_contents($this->getExternalPath());
        }

        return $this->getData('body');
    }

    /**
     * Depending of storage type, stores body of attachment either in database or in file system.
     * @param string $decodedContent
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setBody($decodedContent)
    {
        if (!$this->validateBodyBeforeSave($decodedContent)) {
            if ($this->config->getGeneralAttachmentStorage() == Config::ATTACHMENT_STORAGE_FS) {
                $this->setStorage(Config::ATTACHMENT_STORAGE_FS);
            } else {
                $this->setStorage(Config::ATTACHMENT_STORAGE_DB);
            }
            $this->setIsAllowed(0);
            $this->setName(__('! Blocked file - ')->getText().$this->getName());

            $this->getResource()->save($this);

            return $this;
        }
        if ($this->config->getGeneralAttachmentStorage() == Config::ATTACHMENT_STORAGE_FS) {
            try {
                if (!file_exists(dirname($this->getExternalPath()))) {
                    mkdir(dirname($this->getExternalPath()), 0777, true);
                }
                $attachFile = fopen($this->getExternalPath(), 'w');
                fwrite($attachFile, $decodedContent);
                fclose($attachFile);
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__(
                    "Can't write to {$this->getAttachmentFolderPath()}.
                    Please, check that folder exists and webserver/cron has permissions to write into this folder."
                ));
            }
            $this->setStorage(Config::ATTACHMENT_STORAGE_FS);
        } else {
            $this->setData('body', $decodedContent);
            $this->setStorage(Config::ATTACHMENT_STORAGE_DB);
        }
        $this->getResource()->save($this);

        return $this;
    }

    /**
     * @param string $content
     * @return bool
     */
    private function validateBodyBeforeSave($content)
    {
        if (!file_exists(dirname($this->getExternalPath()))) {
            mkdir(dirname($this->getExternalPath()), 0777, true);
        }
        $filename = $this->getExternalPath().'_tmp';
        $attachFile = fopen($filename, 'w');
        fwrite($attachFile, $content);
        fclose($attachFile);

        $result = $this->attachmentHelper->isAllowedExtension($this->getName(), mime_content_type($filename));

        unlink($filename);

        return $result;
    }

    /**
     * @return string
     */
    public function getAttachmentFolderPath()
    {
        return $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
            ->getAbsolutePath().'helpdesk/attachments/';
    }

    /**
     * Returns attachment path in the filesystem.
     *
     * @return string
     */
    public function getExternalPath()
    {
        $hashCode = $this->getExternalId();

        return $this->getAttachmentFolderPath().
        substr($hashCode, 0, 1).
        '/'.
        substr($hashCode, 1, 2).
        '/'.
        $this->getExternalId();
    }

    /**
     * Get attachment id.
     * If id is empty, we generate it.
     *
     * @return string
     */
    public function getExternalId()
    {
        if (!$this->getData('external_id')) {
            $id = md5(time().$this->helpdeskString->generateRandNum(10));
            $this->setData('external_id', $id);
        }

        return $this->getData('external_id');
    }

    /**
     * @return string
     */
    public function getName()
    {
        //in some cases attachment can have empty name.
        if ($this->getData('name')) {
            return $this->getData('name');
        }

        return 'noname';
    }

    /**
     * If store attachment in filesystem is selected deletes corresponding file as well.
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeDelete()
    {
        if ($this->getStorage() == Config::ATTACHMENT_STORAGE_FS) {
            @unlink($this->getExternalPath());
        }

        return parent::beforeDelete();
    }

    /************************/
}
