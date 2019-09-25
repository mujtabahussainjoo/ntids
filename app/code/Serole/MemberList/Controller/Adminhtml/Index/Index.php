<?php

namespace Serole\MemberList\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action
{
	protected $_directorylist;

	public function __construct(
		\Magento\Framework\App\Filesystem\DirectoryList $_directorylist,
		\Magento\Backend\App\Action\Context $context
	   ) 
	{
		parent::__construct($context);
		$this->_directorylist = $_directorylist;
	}

    public function execute()
    {
		if(isset($_POST) && !empty($_POST))
		{
			$dir = $this->_directorylist->getPath('var');
			
			if(isset($_FILES['fileToUpload']['name']) && $_FILES['fileToUpload']['name'] != '') {
				
				$target_dir = $dir."/customerMemberCsvFile";
				
				if (!is_dir($target_dir)) {
					mkdir($target_dir);    
					chmod($target_dir, 0755);				
				}
				
				$file_name = $_FILES["fileToUpload"]["name"];
				
				$target_file = $target_dir."/".basename($_FILES["fileToUpload"]["name"]);
				
				move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],$target_file);
				
				$rowIstMode = $_POST['csv_matches'];
				
				if(isset($_POST['webCode']))
				{
				  $storeRec = $_POST['webCode'];
				  $result = $this->uploadCSV($storeRec, $target_file, $rowIstMode);
				
				
				
					// SUCCESS!!
					$this->messageManager->addSuccess(__('CSV import complete'));
					
					$this->messageManager->addSuccess(__($result['added']. ' new members added:<br />'.$result['addedList']));
					
					$this->messageManager->addSuccess(__($result['updated']. ' existing members were updated:<br />'.$result['updatedList']));
					
					if ($result['skipped'] > 0 ){
						$this->messageManager->addSuccess(($result['skipped']. ' rows were skipped due to member number already existing:<br />'.$result['skippedList']));	
					}
					
					$this->messageManager->addSuccess(__($result['deleted']. ' members were removed:<br />'.$result['deletedList']));
					
					// ERRORS
					if ($result['missing'] > 0 ){
						$this->messageManager->addError(__($result['missing']. ' rows were ignored due to missing information (could be blank lines at the end of the csv?)'));
					}
					if ($result['dupCount'] > 0 ){
						$this->messageManager->addError(__($result['dupCount']. ' rows were ignored because of duplicates in the csv file (only the first occurrence was processed)'));
						
						$this->messageManager->addError(__('Duplicate Member Numbers in the CSV: '.$result['dupList']));
					}
				}
				else
				{
					$this->messageManager->addError(__('You must have at least one membership active store'));
				}
				
				$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
				
				return $resultRedirect->setPath('*/*/');
			}
			else
			{
				$this->messageManager->addError(__('Please select the csv file'));
				$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
				return $resultRedirect->setPath('*/*/');
			}
		}
		$this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
	}
	
	protected function uploadCSV($store, $csvFile, $mode){
		
		$MODE_IGNORE 	= '0'; 
		$MODE_UPDATE 	= '1'; 	
		$MODE_DELETE 	= '2';
	$MODE_DELETE_ALL 	= '3';

		$skipCount 		= 0;	
		$updateCount 	= 0;
		$deleteCount	= 0;
		$addCount		= 0;
		$missingCount	= 0;
		
		$skippedList = '';
		$updatedList = '';
		$deletedList = '';

		$nameOnly 		= false;
		if ($store == 'rwwa'){
			$nameOnly = true;
		}
		
		$hasGroupColumn = false;		
		if ($store == 'myfitrewards'){
			$hasGroupColumn = true;
		}

		$csv = new \Magento\Framework\File\Csv(new \Magento\Framework\Filesystem\Driver\File());
		
		$rows 	= $csv->getData($csvFile);
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$write = $resource->getConnection();
		
		// Remove where existsing member number 
		$inserts 	= [];
		$dupCheck 	= [];
		$hasDuplicates = false;
		for($i=1; $i<count($rows); $i++) {
			$memberNo = trim(strtolower($rows[$i][0]));

			if ($nameOnly){
				$memberNo = strtolower($rows[$i][1].' '.$rows[$i][2]);
			} else {
				$memberNo = trim(strtolower($rows[$i][0]));			
			}
			
			// fix up any "'" in the values
			$firstName 	= str_replace("'","''",trim(strtolower($rows[$i][1])));
			$lastName 	= str_replace("'","''",trim(strtolower($rows[$i][2])));
			if ($hasGroupColumn) {
				$group  = trim(strtolower($rows[$i][3]));
			} else {
				$group  = '';
			}

			
				
			if ($memberNo != '' && $firstName != '' && $lastName != '') {
			
				// Check for duplicates in the file
				if (isset($dupCheck[$memberNo])){
					$dupCheck[$memberNo]++;
					$hasDuplicates = true;
				} else {
					$dupCheck[$memberNo] = 1;
					$inserts[$memberNo] = '("'.$memberNo.'","'.$firstName.'","'.$lastName.'","'.$store.'","'.$group.'"),';		
					
				}
				
			} else {
				$missingCount++;
			}
		}

		$dupCount = 0;
		$dupList = '';		
		if ($hasDuplicates){
			// remove any duplicate counts of 1
			foreach ($dupCheck as $memberNo=>$dupRows){
				if ($dupRows > 1){
					$dupList.= $memberNo.'(x'.$dupRows.'), ';
					$dupCount+=($dupRows-1);
				}
			}
			
			// remove the trailing ", "
			$dupList = substr($dupList,0,strlen($dupList)-2);
		}
		
		
		$memberNumbers 		= array_keys($inserts);
		
		//$memberNumberList 	= "'".implode("','",$memberNumbers)."'";
		$memberNumberList 	= '"'.implode('","',$memberNumbers).'"';
		$addCount			= count($memberNumbers);
		
		$query = "SELECT member_number 
						FROM customer_memberlist_detail 
						WHERE store='" . $store . "' 
						AND member_number in (".$memberNumberList.")";
		$existResult	=	$write->query($query);		
		$existRows 		=	$existResult->fetchAll();
		$existCount 	=	count($existRows);

		$existRowsList = '';	
		
		// IGNORE ROWS THAT ALREADY EXIST 
		// - remove the members from the "inserts" list that already exist in the DB
		
		
		if ($mode == $MODE_IGNORE){	
			for ($i=0; $i<count($existRows); $i++){
				$inserts[trim(strtolower($existRows[$i]['member_number']))]='';
				$skippedList .= "'".trim(strtolower($existRows[$i]['member_number']))."',";
			}
			$skipCount 		= $existCount;

		// UPDATE ROWS THAT ALREADY EXIST 
		// - Add to "exists Row List" any that already exist in the DB 
		//   so that they are deleted below (and re-added)			
		} else if ($mode == $MODE_UPDATE){
			
			for ($i=0; $i<count($existRows); $i++){
				$existRowsList .= "'".trim(strtolower($existRows[$i]['member_number']))."',";
			}
			$updateCount = 	$existCount;
			$updatedList =  $existRowsList;
			
		// DELETE ROWS THAT ALREADY EXIST 
		// - Add to "exists Row List" any that already exist in the DB 
		//   AND remove from "inserts" so that they are not re-added 
		} else if ($mode == $MODE_DELETE){			
			
			for ($i=0; $i<count($existRows); $i++){
				$inserts[trim(strtolower($existRows[$i]['member_number']))]="";
				$existRowsList .= "'".trim(strtolower($existRows[$i]['member_number']))."',";
			}
			$deleteCount = 	$existCount;
			$deletedList = $existRowsList;	
		}
		
		$addCount	-=	$existCount;		
		$insertList = implode($inserts);
		$insertList = substr($insertList,0,strlen($insertList)-1);
		
		$existRowsList = substr($existRowsList,0,strlen($existRowsList)-1);
 		$query = ''; 
		if ($mode == $MODE_DELETE_ALL) {
            // $_SESSION['insertList'] = $insertList;
            // $formKey = $_GET['form_key'];
			// $URL = Mage::helper("adminhtml")->getUrl('*/adminhtml_cusmembers/uploadcsvafter');
			// $URL = $URL."?store=".$store."&key=".$formKey;
			// header('Location: '.$URL);
			// exit;
			
			//$insertList=$_SESSION['insertList'];
			//$write = Mage::getSingleton('core/resource')->getConnection('core_write');	
			$query = "SELECT * FROM customer_memberlist_detail WHERE store='" . $store . "';\n";
			$allResults	= $write->query($query)->fetchAll();	
			$file_name=$store.".csv";
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=".$file_name);
			$output = fopen("php://output", "w");
			foreach ($allResults as $results){
			  fputcsv($output,$results);
			}
			fclose($output);
			$queryDelete = "DELETE FROM customer_memberlist_detail WHERE store='". $store."';\n";
			$write->query($queryDelete);
			$query = "INSERT INTO customer_memberlist_detail(member_number,first_name,last_name,store,customer_group)VALUES ".$insertList.";";	
			$write->query($query);
			//exit;
		}
		if ($existCount > 0 && $mode != $MODE_IGNORE && $mode != $MODE_DELETE_ALL) {	
			$query = "DELETE FROM customer_memberlist_detail 
						WHERE store='" . $store . "' 
						AND member_number in (".$existRowsList.");\n";
						
			$write->query($query);
			
		}
		if ($insertList != '' && $mode != $MODE_DELETE_ALL) {
			//exit("1");			
			$query = "INSERT INTO customer_memberlist_detail 
						(member_number,first_name,last_name,store,customer_group) 
						VALUES ".$insertList.";";
						
			$write->query($query);		
						
			if ($existCount == 0){
				$addedList = $insertList;
				
			} else if (isset($inserts) && is_array($inserts) && count($inserts) > 0){
				for ($i=0; $i<count($existRows); $i++){
					$inserts[trim(strtolower($existRows[$i]['member_number']))]='';
				}
				$addedList = implode($inserts);
				$addedList = substr($addedList,0,strlen($addedList)-1);
			}
		}
		// if($query != '' && $mode != $MODE_DELETE_ALL) {
			// //exit("2");
			// $sqlResult=$write->query($query);
		// }
		
		$ret['added']		= $addCount;
		$ret['addedList']	= $insertList;
		
		$ret['skipped']		= $skipCount;
		$ret['skippedList']	= $skippedList;
		
		$ret['updated']		= $updateCount;
		$ret['updatedList']	= $updatedList;
		
		$ret['deleted']		= $deleteCount;		
		$ret['deletedList']	= $deletedList;
				
		$ret['missing']		= $missingCount;
		
		$ret['dupCount'] 	= $dupCount;
		$ret['dupList'] = $dupList;	
		
		return $ret;
  }
  
  public function _isAllowed(){
        return $this->_authorization->isAllowed('Serole_MemberList::index');
  }
}

?>