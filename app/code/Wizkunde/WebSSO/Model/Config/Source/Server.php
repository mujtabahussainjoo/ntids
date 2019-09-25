<?php

namespace Wizkunde\WebSSO\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

use Wizkunde\WebSSO\Model\ResourceModel\Server\CollectionFactory;

class Server implements ArrayInterface
{
    /**
     * @var array
     */
    private $options;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }
    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = $this->collectionFactory->create()->toOptionArray();
        }
        return $this->options;
    }
}
