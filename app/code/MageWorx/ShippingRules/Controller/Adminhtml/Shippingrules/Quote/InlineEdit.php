<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Quote;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\ShippingRules\Api\RuleRepositoryInterface as RuleRepository;
use Magento\Framework\Controller\Result\JsonFactory;
use MageWorx\ShippingRules\Api\Data\RuleInterface;
use MageWorx\ShippingRules\Model\Rule;

class InlineEdit extends Action
{
    /** @var RuleRepository  */
    protected $ruleRepository;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param RuleRepository $ruleRepository
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        RuleRepository $ruleRepository,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->ruleRepository = $ruleRepository;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $ruleId) {
            /** @var Rule $rule */
            $rule = $this->ruleRepository->getById($ruleId);
            try {
                $ruleData = $postItems[$ruleId];
                $extendedRuleData = $rule->getData();
                $this->setRuleData($rule, $extendedRuleData, $ruleData);
                $this->ruleRepository->save($rule);
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithRuleId($rule, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithRuleId($rule, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithRuleId(
                    $rule,
                    __('Something went wrong while saving the rule.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add rule id to error message
     *
     * @param RuleInterface $rule
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithRuleId(RuleInterface $rule, $errorText)
    {
        return '[Rule ID: ' . $rule->getRuleId() . '] ' . $errorText;
    }

    /**
     * Set rule data
     *
     * @param Rule $rule
     * @param array $extendedRuleData
     * @param array $ruleData
     * @return $this
     */
    public function setRuleData(Rule $rule, array $extendedRuleData, array $ruleData)
    {
        $rule->setData(array_merge($rule->getData(), $extendedRuleData, $ruleData));

        return $this;
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_ShippingRules::quote');
    }
}
