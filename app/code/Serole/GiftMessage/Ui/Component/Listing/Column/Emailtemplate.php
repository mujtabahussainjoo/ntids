<?php

namespace Serole\GiftMessage\Ui\Component\Listing\Column;


class Emailtemplate extends \Magento\Ui\Component\Listing\Columns\Column
{

    protected $emailTemplate;


    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Email\Model\Template $emailTemplate,
        array $components = [],
        array $data = []
    ) {
        $this->emailTemplate = $emailTemplate;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }


    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['emailtemplateid'])) {
                    $emailTemplateData = $this->emailTemplate->load($item['emailtemplateid']);
                    $item['emailtemplateid'] = $emailTemplateData->getTemplateSubject();
                }
            }
        }
        return $dataSource;
    }
}
