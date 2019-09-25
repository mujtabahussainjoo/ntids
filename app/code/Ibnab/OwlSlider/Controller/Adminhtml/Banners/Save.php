<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ibnab\OwlSlider\Controller\Adminhtml\Banners;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Ibnab\OwlSlider\Model\Sliders
     */
    protected $bannerFactory;
    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $adapterFactory;
    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploader;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezoneInterface;
    public function __construct(Action\Context $context,\Ibnab\OwlSlider\Model\BannersFactory $bannerFactory,\Magento\Framework\Image\AdapterFactory $adapterFactory,\Magento\MediaStorage\Model\File\UploaderFactory $uploader,\Magento\Framework\Filesystem $filesystem,\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface)
    {
        //$this->dataProcessor = $dataProcessor;
        $this->bannerFactory = $bannerFactory;
        $this->adapterFactory = $adapterFactory;
        $this->uploader = $uploader;
        $this->filesystem = $filesystem;
        $this->timezoneInterface = $timezoneInterface;
        parent::__construct($context);
    } 

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ibnab_OwlSlider::owlslider_banners_save');
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
            //$data = $this->dataProcessor->filter($data);
            $model = $this->bannerFactory->create();

            $id = $this->getRequest()->getParam('banner_id');
            if ($id) {
                $model->load($id);
            }
            if (isset($_FILES['image']) && isset($_FILES['image']['name']) && strlen($_FILES['image']['name'])) {
                /*
                 * Save image upload
                 */
                try {
                    $uploader = $this->uploader->create(
                        ['fileId' => 'image']
                    );
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);

                    $imageAdapter = $this->adapterFactory->create();

                    $uploader->addValidateCallback('image', $imageAdapter, 'validateUploadFile');
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);

                    $mediaDirectory = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                    $result = $uploader->save(
                        $mediaDirectory->getAbsolutePath(\Ibnab\OwlSlider\Model\Banners::BASE_MEDIA_PATH)
                    );
                    $data['image'] = \Ibnab\OwlSlider\Model\Banners::BASE_MEDIA_PATH.$result['file'];
                } catch (\Exception $e) {
                    if ($e->getCode() == 0) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
            } else {
                if (isset($data['image']) && isset($data['image']['value'])) {
                    if (isset($data['image']['delete'])) {
                        $data['image'] = null;
                        $data['delete_image'] = true;
                    } elseif (isset($data['image']['value'])) {
                        $data['image'] = $data['image']['value'];
                    } else {
                        $data['image'] = null;
                    }
                }
            }
            /** @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate */
            $localeDate = $this->timezoneInterface;
            $data['start_time'] = $localeDate->date($data['startTime'])->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i');
            $data['end_time'] = $localeDate->date($data['endTime'])->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i');
            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved this banner.'));
                $this->_session->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['banner_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Banner.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['banner_id' => $this->getRequest()->getParam('sbanner_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
