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
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Template\Collection|
 * \Mirasvit\Helpdesk\Model\Template[] getCollection()
 * @method \Mirasvit\Helpdesk\Model\Template load(int $id)
 * @method bool getIsMassDelete()
 * @method \Mirasvit\Helpdesk\Model\Template setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method \Mirasvit\Helpdesk\Model\Template setIsMassStatus(bool $flag)
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Template getResource()
 * @method string getTemplate()
 * @method $this setTemplate(string $param)
 * @method string getName()
 * @method $this setName(string $param)
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $param)
 * @method int[] getStoreIds()
 * @method $this setStoreIds(array $param)
 */
class Template extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'helpdesk_template';

    /**
     * @var string
     */
    protected $_cacheTag = 'helpdesk_template';

    /**
     * @var string
     */
    protected $_eventPrefix = 'helpdesk_template';

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
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $storeFactory;

    /**
     * @var \Mirasvit\Core\Helper\ParseVariables
     */
    protected $mstcoreParseVariables;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $auth;

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
     * @param \Magento\Store\Model\StoreFactory                       $storeFactory
     * @param \Mirasvit\Core\Helper\ParseVariables                 $mstcoreParseVariables
     * @param \Magento\Framework\App\Config\ScopeConfigInterface      $scopeConfig
     * @param \Magento\Backend\Model\Auth                             $auth
     * @param \Magento\Framework\Model\Context                        $context
     * @param \Magento\Framework\Registry                             $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection
     * @param array                                                   $data
     */
    public function __construct(
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Mirasvit\Core\Helper\ParseVariables $mstcoreParseVariables,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->storeFactory = $storeFactory;
        $this->mstcoreParseVariables = $mstcoreParseVariables;
        $this->scopeConfig = $scopeConfig;
        $this->auth = $auth;
        $this->context = $context;
        $this->registry = $registry;
        $this->resource = $resource;
        $this->resourceCollection = $resourceCollection;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Helpdesk\Model\ResourceModel\Template');
    }

    /**
     * @param string|false $emptyOption
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    /************************/

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     *
     * @return string
     */
    public function getParsedTemplate($ticket)
    {
        $storeId = $ticket->getStoreId();
        $storeOb = $this->storeFactory->create()->load($storeId);
        if (!$name = $this->scopeConfig->getValue(
            'general/store_information/name',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $storeId
        )) {
            $name = $storeOb->getName();
        }
        $store = new \Magento\Framework\DataObject([
            'name' => $name,
            'phone' => $this->scopeConfig->getValue(
                'general/store_information/phone',
                \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                $storeId
            ),
            'address' => $this->scopeConfig->getValue(
                'general/store_information/address',
                \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                $storeId
            ),
        ]);
        /** @var \Magento\User\Model\User $user */
        $user = $this->auth->getUser();
        $result = $this->mstcoreParseVariables->parse(
            $this->getTemplate(),
            [
            'ticket' => $ticket,
            'store' => $store,
            'user' => $user,
            ],
            [],
            $store->getId()
        );

        return $result;
    }
}
