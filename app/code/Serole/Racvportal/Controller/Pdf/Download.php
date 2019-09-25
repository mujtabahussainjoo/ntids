<?php
   namespace Serole\Racvportal\Controller\Pdf;

   class Download extends \Serole\Racvportal\Controller\Cart\Ajax{

       public function execute(){
           $parms = $this->getRequest()->getParams();
           if($parms['incrementid']){
               $file = $this->helper->getRootDir().'/neatideafiles/pdf/'.$parms['incrementid'].'.pdf';
               header('Content-Description: File Transfer');
               header('Content-Type: application/pdf');
               header("Content-Type: application/force-download");
               header('Content-Disposition: attachment; filename=' . urlencode(basename($file)));
               // header('Content-Transfer-Encoding: binary');
               header('Expires: 0');
               header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
               header('Pragma: public');
               header('Content-Length: ' . filesize($file));
               ob_clean();
               flush();
               readfile($file);
               exit;
           }
       }
   }