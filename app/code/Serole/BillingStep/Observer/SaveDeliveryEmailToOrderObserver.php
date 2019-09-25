<?php

  namespace Serole\BillingStep\Observer;

  use Magento\Framework\Event\Observer as EventObserver;
  use Magento\Framework\Event\ObserverInterface;


  class SaveDeliveryEmailToOrderObserver implements ObserverInterface {

      /**
       * @var \Magento\Framework\ObjectManagerInterface
       */
      protected $_objectManager;

      /**
       * @param \Magento\Framework\ObjectManagerInterface $objectmanager
       */
      public function __construct(\Magento\Framework\ObjectManagerInterface $objectmanager)
      {
          $this->_objectManager = $objectmanager;
      }

      public function execute(EventObserver $observer)
      {
          $order = $observer->getOrder();
          $quoteRepository = $this->_objectManager->create('Magento\Quote\Model\QuoteRepository');
          /** @var \Magento\Quote\Model\Quote $quote */
          $quote = $quoteRepository->get($order->getQuoteId());
          $order->setDeliveryemail($quote->getDeliveryemail());
          $order->setBillingemail($quote->getBillingemail());
          return $this;
      }

  }