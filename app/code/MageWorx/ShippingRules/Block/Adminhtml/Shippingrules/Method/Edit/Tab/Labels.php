<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\Method\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\EditTabLabels;
use MageWorx\ShippingRules\Model\Carrier\Method;
use MageWorx\ShippingRules\Api\MethodRepositoryInterface;
use MageWorx\ShippingRules\Ui\DataProvider\Method\Form\Modifier\AbstractModifier as MethodModifier;

/**
 * Class Labels
 */
class Labels extends EditTabLabels
{
    /**
     * @var MethodRepositoryInterface
     */
    private $methodRepository;

    /**
     * Initialize dependencies.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param MethodRepositoryInterface $methodRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        MethodRepositoryInterface $methodRepository,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->methodRepository = $methodRepository;
        $this->dataFormPart = MethodModifier::FORM_NAME;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return \Magento\Framework\Data\Form
     */
    protected function _prepareForm()
    {
        /** @var Method $method */
        $method = $this->resolveMethodEntity();
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('method_');

        if (!$this->_storeManager->isSingleStoreMode()) {
            $labels = $method->getStoreLabels();
            $this->_createStoreSpecificFieldset($form, $labels);
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Find corresponding Method or return a new one (empty from repo)
     *
     * @return Method
     */
    private function resolveMethodEntity()
    {
        /** @var Method $method */
        $method = $this->_coreRegistry->registry(Method::CURRENT_METHOD);
        if ($method) {
            return $method;
        }

        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $method = $this->methodRepository->getById($id);
        } else {
            $method = $this->methodRepository->getEmptyEntity();
        }

        return $method;
    }
}
