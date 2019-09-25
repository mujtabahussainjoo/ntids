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


namespace Mirasvit\Helpdesk\Ui\Form\Field;

use Mirasvit\Helpdesk\Api\Data\FieldInterface;
use Mirasvit\Helpdesk\Helper\Storeview;
use Mirasvit\Helpdesk\Model\ResourceModel\Field\CollectionFactory;
use Mirasvit\Helpdesk\Model\ResourceModel\Field;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\RequestInterface;

class DataProvider extends \Mirasvit\Helpdesk\Ui\Form\DataProvider
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Storeview $helpdeskStoreview,
        Field $fieldResource,
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
        $this->fieldResource      = $fieldResource;
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
            'helpdesk/field/save',
            [
                'id'    => (int) $this->request->getParam('id'),
                'store' => (int) $this->request->getParam('store'),
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
        /** @var \Mirasvit\Helpdesk\Model\Field $field */
        foreach ($this->getCollection() as $field) {
            $this->fieldResource->afterLoad($field);

            $data[$field->getId()] = $field->getData();

            $storeId = (int) $this->request->getParam('store');
            $field->setStoreId($storeId);

            $name  = $this->helpdeskStoreview->getStoreViewValue($field, FieldInterface::KEY_NAME);
            $data[$field->getId()][FieldInterface::KEY_NAME] = $name;
            $name  = $this->helpdeskStoreview->getStoreViewValue($field, FieldInterface::KEY_DESCRIPTION);
            $data[$field->getId()][FieldInterface::KEY_DESCRIPTION] = $name;
            $name  = $this->helpdeskStoreview->getStoreViewValue($field, FieldInterface::KEY_VALUES);
            $data[$field->getId()][FieldInterface::KEY_VALUES] = $name;

            $field->unsetData('store_id');
        }

        return $data;
    }
}
