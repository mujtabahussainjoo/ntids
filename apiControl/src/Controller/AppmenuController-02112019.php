<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\CommonController;

/*
//use Magento\Framework\App\Bootstrap;
//include('../../app/bootstrap.php');
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
//print_r($storeManager);
echo $baseUrl= $storeManager->getStore()->getBaseUrl(); 
*/
class AppmenuController
{
    public function __construct(){
    	
    }
	
	/*
	 * Customer login function
	 * 
	 * request param  string  $email
	 * request param  string  $password
	 * 
	 * return json response
	 * */
	public function getMenuOptions(CommonController $common){
		
		$frgtpwdata = array();		
	
		$userData = array();
		$category_data = array();
		$result = array();
		
		$catDetails = $common->getCurl($userData, $common->api_url().'categories/41','GET',$common->admin_token());
		//$category_data = json_decode(json_encode($common->getCurl($userData,$common->api_url().'categories/41','GET',$common->admin_token())));
		
		if($catDetails->children != ''){
			$subCats = explode(',',$catDetails->children);
			$home = array('230');
			$newArr = array_unique(array_merge($home,$subCats));
		}
		
		if(is_array($newArr) && !empty($newArr)){
			$i = 0;
			foreach($newArr as $subCat){
				$j = $i + 1;
				$category_data[] = $common->getCurl($userData,$common->api_url().'categories/'.$subCat,'GET',$common->admin_token());
				//print_r($category_data);
				if($category_data[$i]->include_in_menu == 1){
					$result[$i]['title'] = $category_data[$i]->name;
					$result[$i]['order'] = $j;
					$result[$i]['id'] = $category_data[$i]->id;
					foreach($category_data[$i]->custom_attributes as $cat_att){
						if($cat_att->attribute_code == 'url_key' && $cat_att->value =='home-mobile-app1'){
							$result[$i]['page_slug'] = 'home-mobile-app.html';
							$result[$i]['slug'] = 'home-mobile-app';
							$result[$i]['url'] = $common->store_url().'home-mobile-app.html';
							$result[$i]['menu_id'] = $j;
						} else if($cat_att->attribute_code == 'url_key'){
							//$result[$i]['slug'] = $cat_att->value;
							$result[$i]['slug'] = 'cinemas';
							$result[$i]['page_slug'] = $cat_att->value.'.html';
							$result[$i]['url'] = $common->store_url().$cat_att->value.'.html';
							$result[$i]['menu_id'] = $j;
						}
					}
					$i++;
				}
				
			}
		}
		//print_r($result); 
		//exit;
		if($result){
			$json = '{ "success" : "1", "data" : '.json_encode($result).', "message" : "Menu"}';
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "Menu"}';
		}
		return $this->SendResponse ( $json );
		
		
		
		/*$parentData = $common->getCurl($userData,$common->store_url().'index.php/rest/appbt/V1/categories','GET',$common->admin_token());
		
		$i = 0;
		
		//print_r($parentData->children_data[6]->children_data); exit;
		
		foreach($parentData->children_data[26]->children_data as $category){
			$j = $i + 1;
			$category_data[] = json_decode(json_encode($common->getCurl($userData,$common->api_url().'categories/'.$category->id,'GET',$common->admin_token())));
			$result[$i]['title'] = $category_data[$i]->name;
			$result[$i]['order'] = $j;
			$result[$i]['id'] = $category_data[$i]->id;
			
			foreach($category_data[$i]->custom_attributes as $cat_att){
				//print_r($cat_att);
				//if($cat_att->attribute_code == 'url_path' && $cat_att->value =='bt-app-menu/home-mobile-app'){
				//	$result [$i] ['page_slug'] = $cat_att->value;
				//}
				if($cat_att->attribute_code == 'url_key' && $cat_att->value =='home-mobile-app'){
					$result[$i]['page_slug'] = $cat_att->value.'.html';
					$result[$i]['slug'] = $cat_att->value;
					$result[$i]['url'] = $common->store_url().'home-mobile-app.html';
					$result[$i]['menu_id'] = 1;	
				} else if($cat_att->attribute_code == 'url_key' && $cat_att->value =='promotions'){
					$result[$i]['slug'] = $cat_att->value;
					$result[$i]['url'] = $common->store_url().'promotions';
					$result[$i]['menu_id'] = $j;
				} else if($cat_att->attribute_code =='url_key' && $cat_att->value =='cinemas'){
					$result[$i]['slug'] = $cat_att->value;
					$result[$i]['url'] = $common->store_url().'cinemas';
					$result[$i]['menu_id'] = $j;
				} else if($cat_att->attribute_code =='url_key' && $cat_att->value =='favourites'){
					$result[$i]['slug'] = $cat_att->value;
					$result[$i]['url'] = $common->store_url().'favourites';
					$result[$i]['menu_id'] = $j;
				} else if($cat_att->attribute_code =='url_key' && $cat_att->value =='my-purchases'){
					$result[$i]['slug'] = 'sales/order/history/';
					$result[$i]['url'] = $common->store_url().'sales/order/history/';
					$result[$i]['menu_id'] = $j;
				} else if($cat_att->attribute_code=='url_key' && $cat_att->value=='settings'){
					$result[$i]['slug'] = 'customer/account/';
					$result[$i]['url'] = $common->store_url().'customer/account/';
					$result[$i]['menu_id'] = $j;
				} else if($cat_att->attribute_code =='url_key' && $cat_att->value =='logout'){
					$result[$i]['slug'] = $cat_att->value;
					$result[$i]['url'] = $common->store_url().'customer/account/logout/';
					$result[$i]['menu_id'] = $j;
				} else if($cat_att->attribute_code =='url_key' && $cat_att->value =='user-guide'){
					$result[$i]['slug'] = $cat_att->value;
					$result[$i]['url'] = $common->store_url().'user-guide/';
					$result[$i]['menu_id'] = $j;		
				} else {
					//$menuArray [$i] ['id'] = $category->getId ();
					//$menuArray [$i] ['slug'] = $cats->getUrlKey ();
					//$menuArray [$i] ['url'] = $category->getUrl ();
					//$menuArray [$i] ['menu_id'] = $j;
				}
			}
			$i++;
		}
		//print_r($result); 
		//exit;*/
		/*if($result){
			$json = '{ "success" : "1", "data" : '.json_encode($result).', "message" : "Menu"}';
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "Menu"}';
		}
		return $this->SendResponse ( $json );
		*/
	}

	/*
	 * Params
	 * page_slug : Page identifier
	 * Description : get CMS page content
	 */
	public function getCMSContent(CommonController $common) {
		$array = array();
		$page_slug = (isset($_REQUEST['page_slug']) && $_REQUEST['page_slug'] != '') ? $_REQUEST['page_slug'] : '';
		$accessToken = (isset($_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] != '') ? $_REQUEST['oauth_token'] : '';
		
		$searchCriteria = '?searchCriteria[filter_groups][0][filters][0][field]=identifier&searchCriteria[filter_groups][0][filters][0][value]='.trim($page_slug.'&searchCriteria[filter_groups][0][filters][0][condition_type]=eq');
		//echo $common->store_url().'cmsPage/search'.$searchCriteria;
		$parentData = $common->getCurl($array,$common->api_url().'cmsPage/search'.$searchCriteria,'GET',$common->admin_token());
		//print_r($parentData); exit;
		
		if( $page_slug != ""  ) {
			if($parentData){
				$array['content'] = $parentData->items[0]->content;
			}
			
		} 
		$json = '{ "success" : "1", "data" : [' . json_encode ( $array ) . '], "message" : ""}';
		return $this->SendResponse ( $json );
		
	}
	/*
	 * return response
	 * 
	 * request param string  $body
	 * 
	 * return json response
	 * */
    public function SendResponse($body = '', $content_type = 'text/html') {
        // header ( 'HTTP/1.1' );
        // header ( 'Content-type: ' . $content_type );
        // echo $body;
        // exit ();
        return new Response(
            $body
        );
    }
}
