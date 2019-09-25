<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/23/2016
 * Time: 1:46 PM
 */

namespace PL\Competition\Controller\Competition;

class SaveCompetitor extends \PL\Competition\Controller\Competition{
    

    public function execute(){
        $post = $this->getRequest()->getPostValue();

        if (!$post) {
            $this->_redirect($this->_competitionHelper->getPrefix());
            return;
        }

        try {


            $error = false;

            if (!\Zend_Validate::is(trim($post['firstname']), 'NotEmpty')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim($post['lastname']), 'NotEmpty')) {
                $error = true;
            }
            if($this->_competitionHelper->isDob()){
                if (!\Zend_Validate::is(trim($post['dob']), 'NotEmpty')) {
                    $error = true;
                }
            }

            if (!\Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim($post['comment']), 'NotEmpty')) {
                $error = true;
            }
            /*
            if (!\Zend_Validate::is(trim($post['terms']), 'NotEmpty')) {
                $error = true;
            }
            */

            if ($error) {
                throw new \Exception();
            }
            //die('stop 8');
            $competitionId = $this->getRequest()->getParam('id',0);
            $competitor = $this->_competitorFactory->create()->load($post['email'],'email');
           // print"<pre>"; print_r($competitor->getData()); exit;
            if(!$competitor->getCompetitorId()){
                $competitor->setFirstname($post['firstname']);
                $competitor->setLastname($post['lastname']);
                if($this->_competitionHelper->isDob()){
                    $competitor->setDob($post['dob']);
                }
                $competitor->setEmail($post['email']);
                $competitor->setComment($post['comment']);
                $competitor->setCompetitionId($competitionId);
                $competitor->save();
            }

            $subscriber = $this->subscriberFactory->create()->loadByEmail($post['email']);
           // print"<pre>"; print_r($subscriber->getData()); exit();
            if(!$subscriber->getId() || $subscriber->getStatus()== \Magento\Newsletter\Model\Subscriber::STATUS_NOT_ACTIVE || $subscriber->getStatus()== \Magento\Newsletter\Model\Subscriber::STATUS_UNSUBSCRIBED){
                $storeId = $this->storeManager->getStore()->getId();
                $websiteId = $this->storeManager->getStore()->getWebsiteId();
                $subscriber->setStatus(\Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED);
                $subscriber->setSubscriberEmail($post['email']);
                $subscriber->setSubscriberConfirmCode($subscriber->RandomSequence());
                $subscriber->setWebsiteId($websiteId);
                $subscriber->setStoreId($storeId);

                $customer = $this->customerFactory->create();
                $customer->setStoreId($storeId);
                $customer->setWebsiteId($websiteId);
                $customer->loadByEmail($post['email']);

                if($customer->getId()){
                    $subscriber->setCustomerId($customer->getId());
                }
            }
            $subscriber->save();

            $this->_redirect($this->_redirect->getRefererUrl());
            return;


        }catch (\Exception $e) {
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
                //die('stop 7'.$e->getMessage());
                $this->_redirect($this->_competitionHelper->getPrefix());
                return;
            }
    }
}