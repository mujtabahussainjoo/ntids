<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\Rate\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\EditTabLabels;
use MageWorx\ShippingRules\Model\Carrier\Method\Rate;
use MageWorx\ShippingRules\Model\Carrier\Method\RateFactory;
use MageWorx\ShippingRules\Ui\DataProvider\Rate\Form\Modifier\AbstractModifier as RateModifier;
use MageWorx\ShippingRules\Api\RateRepositoryInterface;
use Magento\Framework\Data\Form;

/**
 * Class Labels
 */
class Labels extends EditTabLabels
{
    /**
     * @var RateRepositoryInterface
     */
    protected $rateRepository;

    /**
     * Initialize dependencies.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param RateRepositoryInterface $rateRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        RateRepositoryInterface $rateRepository,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->rateRepository = $rateRepository;
        $this->dataFormPart = RateModifier::FORM_NAME;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var Rate $rate */
        $rate = $this->getRateEntity();
        /** @var Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rate_');

        if (!$this->_storeManager->isSingleStoreMode()) {
            $labels = $rate->getStoreLabels();
            $this->_createStoreSpecificFieldset($form, $labels);
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface|Rate
     */
    private function getRateEntity()
    {
        $rate = $this->_coreRegistry->registry(Rate::CURRENT_RATE);
        if ($rate) {
            return $rate;
        }
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $rate = $this->rateRepository->getById($id);
        } else {
            $rate = $this->rateRepository->getEmptyEntity();
        }

        return $rate;
    }
}
