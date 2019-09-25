<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Method;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\ShippingRules\Api\MethodRepositoryInterface as MethodRepository;
use Magento\Framework\Controller\Result\JsonFactory;
use MageWorx\ShippingRules\Api\Data\MethodInterface;
use MageWorx\ShippingRules\Model\Carrier\Method;

class InlineEdit extends Action
{
    /** @var MethodRepository  */
    protected $methodRepository;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param MethodRepository $methodRepository
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        MethodRepository $methodRepository,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->methodRepository = $methodRepository;
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

        foreach (array_keys($postItems) as $methodId) {
            /** @var Method $method */
            $method = $this->methodRepository->getById($methodId);
            try {
                $methodData = $postItems[$methodId];
                $extendedMethodData = $method->getData();
                $this->setMethodData($method, $extendedMethodData, $methodData);
                $this->methodRepository->save($method);
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithMethodId($method, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithMethodId($method, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithMethodId(
                    $method,
                    __('Something went wrong while saving the method.')
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
     * Add method id to error message
     *
     * @param MethodInterface $method
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithMethodId(MethodInterface $method, $errorText)
    {
        return '[Method ID: ' . $method->getEntityId() . '] ' . $errorText;
    }

    /**
     * Set method data
     *
     * @param Method $method
     * @param array $extendedMethodData
     * @param array $methodData
     * @return $this
     */
    public function setMethodData(Method $method, array $extendedMethodData, array $methodData)
    {
        $method->setData(array_merge($method->getData(), $extendedMethodData, $methodData));

        return $this;
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_ShippingRules::carrier');
    }
}
