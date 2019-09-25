<?php

/**
 * MagePrince
 * Copyright (C) 2018 Mageprince
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html
 *
 * @category MagePrince
 * @package Prince_Productattach
 * @copyright Copyright (c) 2018 MagePrince
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MagePrince
 */

namespace Serole\Productattachment\Controller\Adminhtml\productattachment;

class Docdelete extends \Magento\Backend\App\Action
{

    /** @var \Prince\Productattach\Model\FileiconFactory */

    private $dataHelper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Serole\Productattachment\Helper\Data $dataHelper
    ) {
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
    }


    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            //echo "In side the con"; exit;
            try {
                // init model and delete
                #$url = $this->dataHelper->getBaseDir().$file;
                #unlink($url);
                $model = $this->_objectManager->create('Serole\Productattachment\Model\Productattachment');
                $model->load($id);
                $model->setFile('');
                $model->save();
                $file = $model->getFile();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Fileicon.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
         // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Fileicon to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
