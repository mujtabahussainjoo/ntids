<?php


namespace Serole\GiftMessage\Block\Adminhtml\Post\Renderer;

use Magento\Framework\DataObject;


class File extends \Magento\Framework\Data\Form\Element\AbstractElement
{

    private $assetRepo;


    private $helper;


    private $urlBuider;


    private $coreRegistry = null;


    protected $request;


    protected $giftImage;


    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\App\Request\Http $request,
        \Serole\Pdf\Helper\Pdf $pdfHelper,
        \Serole\GiftMessage\Model\Image $giftImage,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Registry $registry
    ) {
        $this->assetRepo = $assetRepo;
        $this->pdfHelper = $pdfHelper;
        $this->urlBuilder = $urlBuilder;
        $this->coreRegistry = $registry;
        $this->request = $request;
        $this->giftImage =$giftImage;
    }


    public function getElementHtml()
    {
        $file = '<h3>No File Uploded</h3>';
        $id = $this->request->getParam('id');
        if($id){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $giftTemplateObj = $this->giftImage->load($id);
           if($giftTemplateObj->getImage()){
                $image = $giftTemplateObj->getImage();
                if(file_exists($this->getImagePath($image))){
                    $file = "<img src='".$this->getImageurl($image)."' style='float:right' width=250 heigh=250' />
                    <a target = '_blank' href=".$this->getImageurl($image)."><div>Download File</div></a>";
                }
            }
        }
        return $file;
    }

    public function getImagePath($image){
        $giftMessageImageFolderName = 'giftimagestemplates';
        $filePath = $this->pdfHelper->getRootBaseDir().$giftMessageImageFolderName.'/'.$image;
        return $filePath;
    }

    public function getImageurl($image){
        $giftMessageImageFolderName = 'giftimagestemplates';
        $fileUrl = $this->pdfHelper->getFileUrl($giftMessageImageFolderName, $image);
        return $fileUrl;
    }
}
