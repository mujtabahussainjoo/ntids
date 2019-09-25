<?php

namespace Wizkunde\WebSSO\Block\Adminhtml\Server\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('server_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Server Information'));
    }

    /**
     * @return mixed
     */
    protected function _beforeToHtml()
    {

        $this->addTab(
            'server_info',
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock(
                    'Wizkunde\WebSSO\Block\Adminhtml\Server\Edit\Tab\Info'
                )->toHtml(),
                'active' => true
            ]
        );

        $connectionTypes = ['saml2' => 'SAML2', 'oauth2' => 'OAuth2'];

        foreach ($connectionTypes as $name => $type) {
            $this->addTab(
                $name . '_info',
                [
                    'label' => $type,
                    'title' => $type,
                    'content' => $this->getLayout()->createBlock(
                        'Wizkunde\WebSSO\Connection\\' . $type . '\Form\Tab'
                    )->toHtml(),
                    'active' => false,
                    'class' => 'protocol-tab protocol-tab-' . $name
                ]
            );
        }

        $this->addTab(
            'mapping_info',
            [
                'label' => __('Mappings'),
                'title' => __('Mappings'),
                'content' => $this->getLayout()->createBlock(
                    'Wizkunde\WebSSO\Block\Adminhtml\Server\Edit\Tab\Mapping'
                )->toHtml(),
                'active' => false
            ]
        );

        return parent::_beforeToHtml();
    }
}
