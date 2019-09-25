<?php

/**
* Magedelight
* Copyright (C) 2017 Magedelight <info@magedelight.com>
*
* @category Magedelight
* @package Magedelight_MembershipSubscription
* @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
* @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
* @author Magedelight <info@magedelight.com>
 * */

namespace Magedelight\MembershipSubscription\Controller\Membership;

class Plan extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
        
        $this->_view->loadLayout();

        $this->_view->renderLayout();
    }
}
