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



namespace Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Grid\Renderer;

class Highlight extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Extended
{
    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $this->_value = $this->_getValue($row);
        $this->_text = parent::render($row);

        if ($this->getColumn()->getIndex() == 'color') { //we are in the list of statuses or priorities
            $color = $this->_value;
        } else {
            $code = str_replace('_id', '', $this->getColumn()->getIndex()).'_color';
            $color = '';
            if (isset($row[$code])) {
                $color = $row[$code];
            }
        }

        return "<span class='$color'>$this->_text</span>";
    }

    /**
     * @return object
     */
    public function renderCss()
    {
        return $this->getColumn()->getIndex();
    }
}
