<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\DataObject;

class Logger extends DataObject
{

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var array
     */
    protected $story;

    /**
     * @var DataObject[]
     */
    protected $info;

    /**
     * @var int
     */
    protected $currentId;

    /**
     * @param CheckoutSession $checkoutSession
     * @param array $data
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        $data = []
    ) {
        parent::__construct($data);
        $this->checkoutSession = $checkoutSession;
        if ($this->checkoutSession->getData('story')) {
            $this->story = unserialize($this->checkoutSession->getData('story'));
        }
    }

    /**
     * Get story for the current quote
     *
     * @return array
     */
    public function getStory()
    {
        return $this->story;
    }

    /**
     * Get last info from story
     *
     * @return array
     */
    public function getInfo()
    {
        return isset($this->story[count($this->story) - 1]) ? $this->story[count($this->story) - 1] : null;
    }

    /**
     * @return DataObject
     */
    public function createNewInfo($methodCode)
    {
        if (!$this->currentId) {
            if (!count($this->story)) {
                $this->currentId = 0;
            } else {
                $this->currentId = count($this->story);
            }
        }


        if (empty($this->story[$this->currentId][$methodCode])) {
            $this->story[$this->currentId][$methodCode] = [];
        }

        return $this->story[$this->currentId][$methodCode];
    }

    public function saveInfo($methodCode, $info)
    {
        $this->story[$this->currentId][$methodCode] = $info;
        $this->checkoutSession->setData('story', serialize($this->story));
    }
}
