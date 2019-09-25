<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Model;

class Surcharge extends \Magento\Framework\Model\AbstractModel implements \Fooman\Surcharge\Api\SurchargeInterface
{
    const CODE = 'fooman_surcharge';

    /**
     * @var \Fooman\Surcharge\Model\TypeFactory
     */
    private $typeFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Fooman\Surcharge\Model\TypeFactory $typeFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->typeFactory = $typeFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init(\Fooman\Surcharge\Model\ResourceModel\Surcharge::class);
    }

    /**
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     *
     * @return \Fooman\Totals\Api\Data\QuoteAddressTotalInterface[]
     */
    public function collect(\Magento\Quote\Api\Data\CartInterface $quote)
    {
        return $this->getTypeInstance()->calculate($this, $quote);
    }

    public function getTypeInstance()
    {
        return $this->typeFactory->get($this->getType());
    }

    public function getTypeId()
    {
        return $this->getType() . $this->getId();
    }

    public function getType()
    {
        return $this->getData('type');
    }

    public function getId()
    {
        return $this->getData('id');
    }

    public function getDescription()
    {
        return $this->getData('description');
    }
}
