<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\ExtendedZone\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Data\Form;
use MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\EditTabLabels;
use MageWorx\ShippingRules\Model\ExtendedZone;
use MageWorx\ShippingRules\Model\ExtendedZoneFactory;
use MageWorx\ShippingRules\Ui\DataProvider\ExtendedZone\Form\Modifier\AbstractModifier as ExtendedZoneModifier;

/**
 * Class Labels
 */
class Labels extends EditTabLabels
{
    /**
     * @var ExtendedZoneFactory
     */
    private $zoneFactory;

    /**
     * Initialize dependencies.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param ExtendedZoneFactory $zoneFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        ExtendedZoneFactory $zoneFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->zoneFactory = $zoneFactory;
        $this->dataFormPart = ExtendedZoneModifier::FORM_NAME;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Form
     */
    protected function _prepareForm()
    {
        /** @var ExtendedZone $zone */
        $zone = $this->_coreRegistry->registry(ExtendedZone::REGISTRY_KEY);
        if (!$zone) {
            $id = $this->getRequest()->getParam('id');
            $zone = $this->zoneFactory->create();
            $zone->getResource()->load($zone, $id);
        }

        /** @var Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('extendedzone_');

        if (!$this->_storeManager->isSingleStoreMode()) {
            $labels = $zone->getStoreLabels();
            $this->_createStoreSpecificFieldset($form, $labels);
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
