<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Block\Adminhtml\Surcharge\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     * @var string
     */
    private $type;

    /**
     * @var \Fooman\Surcharge\Model\Config
     */
    private $config;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Fooman\Surcharge\Model\Config $config,
        array $data = []
    ) {
        if (isset($data['surcharge_type'])) {
            $this->type = $data['surcharge_type'];
        }
        $this->config = $config;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setId('surcharge_tabs');
        $this->setDestElementId('surcharge-edit-form');
        $this->setTitle(__('Surcharge'));
    }

    protected function _prepareLayout()
    {
        $this->addTab('general', [
            'label' => __('General'),
            'content' => $this->getLayout()
                ->createBlock(\Fooman\Surcharge\Block\Adminhtml\Surcharge\Edit\Tab\General::class)
                ->toHtml()
        ]);

        if (null !== $this->type) {
            $typeConfig = $this->config->getType($this->type);

            $this->addTab($typeConfig['type'], [
                'label' => __($typeConfig['label']),
                'content' => $this->getLayout()->createBlock($typeConfig['tab'])->toHtml()
            ]);
        }

        return parent::_prepareLayout();
    }
}
