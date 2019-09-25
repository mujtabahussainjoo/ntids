<?php
namespace Serole\Cashback\Controller\Adminhtml\customerorder;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;


class Ajax extends \Magento\Backend\App\Action
{

    /**
     * @param Action\Context $context
     */
    public function __construct(Action\Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
          $customerId = $_POST['customer_id'];
    
            $cusromerData = $this->_objectManager->create('Serole\Cashback\Model\Carddetails')->getCollection()
                ->addFieldToSelect("*")
                ->addFieldToFilter("customer_id", array("eq" => $customerId))
                ->load();
				
			$option = '';
			if(isset($cusromerData) && count($cusromerData) > 0)
			{
				foreach($cusromerData as $cusromer)
				{
					$cId = $cusromer->getId();
					$cNo = $cusromer->getCardNo();
					$option = $option."<option value='$cId'>$cNo</option>";
				}
				echo $option;
			}
			else
				echo "<option value=''>Select card</option>";
    }
}