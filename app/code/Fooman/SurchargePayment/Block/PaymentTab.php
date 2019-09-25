<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_SurchargePayment
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\SurchargePayment\Block;

class PaymentTab extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Fooman\SurchargePayment\Model\PaymentConfig
     */
    private $paymentModelConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context      $context
     * @param \Magento\Framework\Registry                  $registry
     * @param \Magento\Framework\Data\FormFactory          $formFactory
     * @param \Fooman\SurchargePayment\Model\PaymentConfig $paymentModelConfig
     * @param array                                        $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Fooman\SurchargePayment\Model\PaymentConfig $paymentModelConfig,
        array $data = []
    ) {
        $this->paymentModelConfig = $paymentModelConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('general', ['legend' => __('Payment Surcharge Settings')]);

        $fieldset->addField('payment', 'multiselect', [
            'label' => __('Payment Methods'),
            'title' => __('Payment Methods'),
            'name' => 'payment[]',
            'required' => true,
            'values' => $this->getListOfPaymentMethodsGrouped()
        ]);

        $fieldset->addField('min', 'text', [
            'label' => __('Order Minimum'),
            'title' => __('Order Minimum'),
            'name' => 'min',
            'required' => false,
        ]);

        $fieldset->addField('max', 'text', [
            'label' => __('Order Maximum'),
            'title' => __('Order Maximum'),
            'name' => 'max',
            'required' => false,
        ]);

        $fieldset->addField('calculation_mode', 'select', [
            'label' => __('Surcharge Calculation Mode'),
            'title' => __('Surcharge Calculation Mode'),
            'name' => 'calculation_mode',
            'required' => true,
            'options' => [
                '' => __(''),
                \Fooman\Surcharge\Model\SurchargeCalculation::FIXED => __('Fixed'),
                \Fooman\Surcharge\Model\SurchargeCalculation::PERCENT => __('Percent'),
                \Fooman\Surcharge\Model\SurchargeCalculation::FIXED_PLUS_PERCENT => __('Fixed + Percent'),
                \Fooman\Surcharge\Model\SurchargeCalculation::FIXED_MINIMUM => __('Maximum of Fixed or Percent'),
            ]
        ]);

        $fieldset->addField('rate', 'text', [
            'label' => __('Surcharge %'),
            'title' => __('Surcharge %'),
            'name' => 'rate',
            'required' => true,
        ]);

        $fieldset->addField('fixed', 'text', [
            'label' => __('Surcharge Fixed Cost'),
            'title' => __('Surcharge Fixed Cost'),
            'name' => 'fixed',
            'required' => true,
        ]);

        $registry = $this->_coreRegistry->registry('fooman_surcharge');

        if ($registry) {
            $formData = json_decode($registry->getDataRule(), true);
            $form->addValues($formData);
        }

        $form->setFieldNameSuffix('payment');
        $this->setForm($form);
    }

    private function getListOfPaymentMethodsGrouped()
    {
        return $this->paymentModelConfig->getGroupedList();
    }
}
