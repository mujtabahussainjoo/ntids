<?php
namespace Serole\Racparkpasses\Controller\Adminhtml\racparkpass;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;


class Save extends \Magento\Backend\App\Action
{
    protected $_serialize;
	
    /**
     * @param Action\Context $context
     */
    public function __construct(
				 Action\Context $context,
				 \Magento\Framework\Serialize\Serializer\Json $serialize
			)
    {
		$this->_serialize = $serialize;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        
        
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            $model = $this->_objectManager->create('Serole\Racparkpasses\Model\Racparkpass');

            $id = $this->getRequest()->getParam('item_id');
            if ($id) {
				if (isset($data['veh_reg_2']) && $data['veh_reg_2']!=''){
					$addVehReg2 = true;
				} else {
					$addVehReg2 = false;
				}
                $item = $model->load($id);
				$options = $this->_serialize->unserialize($model->getProductOptions());
                $updateValues = array();
				if ($options['options']){
					
					foreach($options['options'] as $idx=>$option){
						
						if ($option['label'] == 'Vehicle Registration Number'
							&& $option['value']!=$data['veh_reg_1']){
							$updateValues[$idx] = $data['veh_reg_1'];
							
						} else if ($option['label'] == '2nd Vehicle Registration Number'){
							if ($option['value']!=$data['veh_reg_2']){
								$updateValues[$idx] = $data['veh_reg_2'];							
							}
							$addVehReg2 = false;							
							
						} else if ($option['label'] == 'Park Pass start date' && $option['value']!= $data['start_date']){						
							$updateValues[$idx] = $data['start_date'];
	            		}
					}
					
					$optionsUpdated = false;
					
					foreach($updateValues as $idx=>$value){
										
						
						$option = $options['options'][$idx];
						
						$option['value'] = $value;
						$option['print_value'] = $value;
						$option['option_value'] = $value;
						
						$options['options'][$idx] = $option;
						$optionsUpdated = true;
					}
					
					// If a 2nd Vehicle has been added - but one didn't exist before
					// add it to the order item's array 
					//TO do when live(need to change the id of the option)
					if ($addVehReg2 && isset($data['veh_reg_2'])){
						if ($model->getSku() == 'RACAPP'){
							$optionValueId = 64;
						} else {
							$optionValueId = 53;
						}

						$options['options'][]=array(
							'label' 		=> '2nd Vehicle Registration Number',
							'value' 		=> $data['veh_reg_2'],
							'print_value' 	=> $data['veh_reg_2'],
							'option_id' 	=> $optionValueId,
							'option_type' 	=> 'field',
							'option_value' 	=> $data['veh_reg_2'],
							'custom_view'	=> ''
						);
						$optionsUpdated = true;
					}
								
					if ($optionsUpdated){	
					
						$model->setProductOptions($this->_serialize->serialize($options));
						
						$model->save();
						
					} 
				}
            }
			
			
           // $model->setData($data);

            try {
                //$model->save();
                $this->messageManager->addSuccess(__('The Rac park pass data has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['item_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Racparkpass.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['item_id' => $this->getRequest()->getParam('item_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}