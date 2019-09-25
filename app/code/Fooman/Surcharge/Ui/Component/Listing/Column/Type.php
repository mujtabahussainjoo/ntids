<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Ui\Component\Listing\Column;

use Fooman\Surcharge\Model\TypeFactory;
use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Type extends Column
{

    /**
     * @var TypeFactory
     */
    private $typeFactory;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        TypeFactory $typeFactory,
        array $components,
        array $data
    ) {
        $this->typeFactory = $typeFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * @param array $item
     *
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $typeInstance = $this->typeFactory->get($item['type']);

        if (empty($typeInstance)) {
            return __('Type not installed: %1', $item['type']);
        }

        return $typeInstance->getLabel();
    }
}
