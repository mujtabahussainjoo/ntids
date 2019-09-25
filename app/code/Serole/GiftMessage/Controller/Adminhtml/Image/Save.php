<?php
namespace Serole\GiftMessage\Controller\Adminhtml\Image;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;

class Save extends \Magento\Backend\App\Action
{

    private $coreRegistry = null;


    private $resultPageFactory;


    private $backSession;


    protected $fileSystem;


    protected $uploaderFactory;


    protected $allowedExtensions = ['jpeg','jpg','png'];


    protected  $fileId = 'image';
    /*image is the name of the image fileupload field name in form*/


    protected  $giftImage;


    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        Filesystem $fileSystem,
        \Serole\GiftMessage\Model\Image $giftImage,
        UploaderFactory $uploaderFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->backSession = $context->getSession();
        $this->fileSystem = $fileSystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->giftImage = $giftImage;
        parent::__construct($context);
    }


    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Serole_GiftMessage::save');
    }


    public function execute(){
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $id = $this->getRequest()->getParam('id');
            $rootPath = $this->getRootPath();
            $giftMessageImageFolderName = 'giftimagestemplates';
            $directoryPath = $rootPath.$giftMessageImageFolderName;
            if(!file_exists($directoryPath)){
                mkdir($directoryPath,0777,true);
                chmod($directoryPath,0777);
            }
            try {
                 if ($id) {
                    $giftMessageObj = $this->giftImage->load($id);
                 }
                 if (!empty($_FILES)) {
                     if($_FILES['image']['name']) {
                         $uploader = $this->uploaderFactory->create(['fileId' => $this->fileId])
                             ->setAllowCreateFolders(true)
                             ->setAllowedExtensions($this->allowedExtensions)
                             ->addValidateCallback('validate', $this, 'validateFile');
                         if (!$uploader->save($directoryPath)) {
                             $this->messageManager->addError(
                                 __('File cannot be saved to path not exist' . $directoryPath)
                             );
                         }
                     }
                 }
                 if (!$id) {
                    $giftMessageObj = $this->giftImage;
                 }
                $giftMessageObj->setEmailtemplateid($data['emailtemplateid']);
                if (!empty($_FILES)) {
                    if($_FILES['image']['name']) {
                        $giftMessageObj->setImage($uploader->getUploadedFileName());
                    }
                }
                    $giftMessageObj->save();
                    $this->messageManager->addSuccess(__('Saved Sucessfully'));
            }catch (\Exception $e){
                $this->messageManager->addError(
                    __($e->getMessage())
                );
            }

        }else{
            $this->messageManager->addError(
                __('Some thing went wrong')
            );
        }
        $this->_redirect('*/*/');
    }

    public function getRootPath(){
        return $this->fileSystem
            ->getDirectoryWrite(DirectoryList::ROOT)
            ->getAbsolutePath('/');
    }
}
