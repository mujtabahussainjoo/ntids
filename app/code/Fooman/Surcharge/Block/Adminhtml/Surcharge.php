<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Block\Adminhtml;

class Surcharge extends \Magento\Backend\Block\Widget\Container
{

    /**
     * @var \Fooman\Surcharge\Model\Config
     */
    private $config;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Fooman\Surcharge\Model\Config $config,
        array $data
    ) {
        $this->config = $config;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {

        $this->buttonList->add('add_new', [
            'id' => 'add_new_surcharge',
            'label' => __('Add Surcharge'),
            'class' => 'add',
            'button_class' => '',
            'class_name' => \Magento\Backend\Block\Widget\Button\SplitButton::class,
            'options' => $this->getAddSurchargeButtonOptions(),
        ]);

        parent::_prepareLayout();
    }

    /**
     * @return array
     */
    public function getAddSurchargeButtonOptions()
    {
        $splitButtonOptions = [];

        if ($this->config->getTypes()) {
            foreach ($this->config->getTypes() as $type) {
                $splitButtonOptions[$type['type']] = [
                    'label' => __($type['label']),
                    'onclick' => "setLocation('" . $this->getSurchargeCreateUrl($type['type']) . "')",
                    'default' => false,
                ];
            }
        }

        return $splitButtonOptions;
    }

    /**
     * @param  string $type
     *
     * @return string
     */
    public function getSurchargeCreateUrl($type)
    {
        return $this->getUrl('surcharge/manage/new', ['type' => $type]);
    }
}
