<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 12:27 PM
 */
namespace PL\Competition\Controller\Adminhtml\Competition;

class NewAction extends \PL\Competition\Controller\Adminhtml\Competition
{
    public function execute()
    {
        $this->_forward('edit');
    }
}
