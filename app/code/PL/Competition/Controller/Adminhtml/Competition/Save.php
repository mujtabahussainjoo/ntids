<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 1:26 PM
 */


namespace PL\Competition\Controller\Adminhtml\Competition;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \PL\Competition\Controller\Adminhtml\Competition{

    public function execute(){
        $post = $this->getRequest()->getPost();
        if ($post) {
            $model = $this->_competitionFactory->create();
            $id = $this->getRequest()->getParam('entity_id');

            if ($id) {
                $model->load($id);
            }
            $formData = $this->getRequest()->getParam('competition');


            $image = $this->getRequest()->getFiles('image');
            if($image && $image['name']!=""){
                $formData['image'] = $this->_uploadImage('image');
            }else{
                if (isset($formData['image']) && isset($formData['image']['value'])) {
                    if (isset($formData['image']['delete'])) {
                        $formData['image'] = null;
                        $formData['delete_image'] = true;
                    } elseif (isset($formData['image']['value'])) {
                        $formData['image'] = $formData['image']['value'];
                    } else {
                        $formData['image'] = null;
                    }
                }
            }

            $logo = $this->getRequest()->getFiles('logo');
            if($logo && $logo['name']!=""){
                $formData['logo'] = $this->_uploadImage('logo');
            }else{
                if (isset($formData['logo']) && isset($formData['logo']['value'])) {
                    if (isset($formData['logo']['delete'])) {
                        $formData['logo'] = null;
                        $formData['delete_logo'] = true;
                    } elseif (isset($formData['logo']['value'])) {
                        $formData['logo'] = $formData['logo']['value'];
                    } else {
                        $formData['logo'] = null;
                    }
                }
            }

            $thumb_1 = $this->getRequest()->getFiles('thumb_1');
            if($thumb_1 && $thumb_1['name']!=""){
                $formData['thumb_1'] = $this->_uploadImage('thumb_1');
            }else{
                if (isset($formData['thumb_1']) && isset($formData['thumb_1']['value'])) {
                    if (isset($formData['thumb_1']['delete'])) {
                        $formData['thumb_1'] = null;
                        $formData['delete_thumb_1'] = true;
                    } elseif (isset($formData['thumb_1']['value'])) {
                        $formData['thumb_1'] = $formData['thumb_1']['value'];
                    } else {
                        $formData['thumb_1'] = null;
                    }
                }
            }
            $thumb_2 = $this->getRequest()->getFiles('thumb_2');
            if($thumb_2 && $thumb_2['name']!=""){
                $formData['thumb_2'] = $this->_uploadImage('thumb_2');
            }else{
                if (isset($formData['thumb_2']) && isset($formData['thumb_2']['value'])) {
                    if (isset($formData['thumb_2']['delete'])) {
                        $formData['thumb_2'] = null;
                        $formData['delete_thumb_2'] = true;
                    } elseif (isset($formData['thumb_2']['value'])) {
                        $formData['thumb_2'] = $formData['thumb_2']['value'];
                    } else {
                        $formData['thumb_2'] = null;
                    }
                }
            }
            $thumb_3 = $this->getRequest()->getFiles('thumb_3');
            if($thumb_3 && $thumb_3['name']!=""){
                $formData['thumb_3'] = $this->_uploadImage('thumb_3');
            }else{
                if (isset($formData['thumb_3']) && isset($formData['thumb_3']['value'])) {
                    if (isset($formData['thumb_3']['delete'])) {
                        $formData['thumb_3'] = null;
                        $formData['delete_thumb_3'] = true;
                    } elseif (isset($formData['thumb_3']['value'])) {
                        $formData['thumb_3'] = $formData['thumb_3']['value'];
                    } else {
                        $formData['thumb_3'] = null;
                    }
                }
            }
            $thumb_4 = $this->getRequest()->getFiles('thumb_4');
            if($thumb_4 && $thumb_4['name']!=""){
                $formData['thumb_4'] = $this->_uploadImage('thumb_4');
            }else{
                if (isset($formData['thumb_4']) && isset($formData['thumb_4']['value'])) {
                    if (isset($formData['thumb_4']['delete'])) {
                        $formData['thumb_4'] = null;
                        $formData['delete_thumb_4'] = true;
                    } elseif (isset($formData['thumb_4']['value'])) {
                        $formData['thumb_4'] = $formData['thumb_4']['value'];
                    } else {
                        $formData['thumb_4'] = null;
                    }
                }
            }
            $thumb_5 = $this->getRequest()->getFiles('thumb_5');
            if($thumb_5 && $thumb_5['name']!=""){
                $formData['thumb_5'] = $this->_uploadImage('thumb_5');
            }else{
                if (isset($formData['thumb_5']) && isset($formData['thumb_5']['value'])) {
                    if (isset($formData['thumb_5']['delete'])) {
                        $formData['thumb_5'] = null;
                        $formData['delete_thumb_5'] = true;
                    } elseif (isset($formData['thumb_5']['value'])) {
                        $formData['thumb_5'] = $formData['thumb_5']['value'];
                    } else {
                        $formData['thumb_5'] = null;
                    }
                }
            }


            //print"<pre>"; print_r($formData); exit;

            $model->setData($formData);
            try{
                $model->save();
                $this->messageManager->addSuccess(__('The Competition has been saved.'));
                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['entity_id' => $model->getId(), '_current' => true]);
                    return;
                }
                // Go to grid page
                $this->_redirect('*/*/');
                return;
            }catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
            $this->_getSession()->setFormData($formData);
            $this->_redirect('*/*/edit', ['entity_id' => $id]);
        }
    }

    protected function _uploadImage($image){
        try{
            $uploader = $this->_objectManager->create(
                'Magento\MediaStorage\Model\File\Uploader',
                ['fileId' => $image]
            );
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);

            /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
            $imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
            $uploader->addValidateCallback($image, $imageAdapter, 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);

            $result = $uploader->save(
                $mediaDirectory->getAbsolutePath(\PL\Competition\Model\Competition::BASE_MEDIA_PATH)
            );

            $file_name = \PL\Competition\Model\Competition::BASE_MEDIA_PATH.$result['file'];
            return $file_name;

        }catch (\Exception $e) {
            if ($e->getCode() == 0) {
                $this->messageManager->addError($e->getMessage());
            }
        }


    }


}