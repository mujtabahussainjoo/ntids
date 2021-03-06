<?php

namespace Wizkunde\WebSSO\Connection\SAML2\Plugin;

class AfterLoad
{
    private $typeModel = null;

    public function __construct(\Wizkunde\WebSSO\Connection\SAML2\Model\Type $typeModel)
    {
        $this->typeModel = $typeModel;
    }

    public function afterLoad($model)
    {
        if ($model->getId() > 0) {
            $this->typeModel->load($model->getId(), 'server_id');
            $typeData = $this->typeModel->getData();

            unset($typeData['id']);
            $model->addData(['type_saml2' => $typeData]);
        }

        return $this;
    }
}
