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


namespace Mirasvit\Helpdesk\Ui\Form\Rule;

use Mirasvit\Helpdesk\Api\Data\RuleInterface;
use Mirasvit\Helpdesk\Helper\Storeview;
use Mirasvit\Helpdesk\Model\ResourceModel\Rule\CollectionFactory;
use Mirasvit\Helpdesk\Model\ResourceModel\Rule;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\RequestInterface;

class DataProvider extends \Mirasvit\Helpdesk\Ui\Form\DataProvider
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Storeview $helpdeskStoreview,
        Rule $ruleResource,
        CollectionFactory $collectionFactory,
        UrlInterface $url,
        RequestInterface $request,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->helpdeskStoreview  = $helpdeskStoreview;
        $this->ruleResource = $ruleResource;
        $this->collection         = $collectionFactory->create();
        $this->url                = $url;
        $this->request            = $request;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigData()
    {
        $config = parent::getConfigData();

        $config['submit_url'] = $this->url->getUrl(
            'helpdesk/rule/save',
            [
                'id'    => (int) $this->request->getParam('id'),
            ]
        );

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = [];
        /** @var \Mirasvit\Helpdesk\Model\Rule $rule */
        foreach ($this->getCollection() as $rule) {
            $this->ruleResource->afterLoad($rule);

            $data[$rule->getId()] = $rule->getData();
        }
        return $data;
    }
}
