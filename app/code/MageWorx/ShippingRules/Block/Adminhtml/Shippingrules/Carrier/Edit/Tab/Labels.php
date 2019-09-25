<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\Carrier\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\EditTabLabels;
use MageWorx\ShippingRules\Model\Carrier;
use MageWorx\ShippingRules\Api\CarrierRepositoryInterface;
use MageWorx\ShippingRules\Ui\DataProvider\Carrier\Form\Modifier\AbstractModifier as CarrierModifier;

/**
 * Class Labels
 */
class Labels extends EditTabLabels
{
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;

    /**
     * Initialize dependencies.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param CarrierRepositoryInterface $carrierRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        CarrierRepositoryInterface $carrierRepository,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->carrierRepository = $carrierRepository;
        $this->dataFormPart = CarrierModifier::FORM_NAME;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var Carrier $carrier */
        $carrier = $this->resolveCarrier();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('carrier_');

        if (!$this->_storeManager->isSingleStoreMode()) {
            $labels = $carrier->getStoreLabels();
            $this->_createStoreSpecificFieldset($form, $labels);
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Resolve carrier entity loaded or empty
     *
     * @return Carrier
     */
    private function resolveCarrier()
    {
        /** @var Carrier $carrier */
        $carrier = $this->_coreRegistry->registry(Carrier::CURRENT_CARRIER);
        if ($carrier) {
            return $carrier;
        }

        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $carrier = $this->carrierRepository->getById($id);
        } else {
            $carrier = $this->carrierRepository->getEmptyEntity();
        }

        return $carrier;
    }
}
