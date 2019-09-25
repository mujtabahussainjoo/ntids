<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Helpdesk\Ui\Form\Department\Control;

use Mirasvit\Helpdesk\Ui\Form\Button;

class DeleteButton extends Button
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getId()) {
            $data = parent::getButtonData();
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', [self::ID_NAME => $this->getId()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonUrl()
    {
        return 'deleteConfirm(\'' . __(
            'Are you sure you want to do this?'
        ) . '\', \'' . $this->getDeleteUrl() . '\')';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return __('Delete');
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return 'delete';
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 20;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->context->getRequest()->getParam(self::ID_NAME);
    }
}
