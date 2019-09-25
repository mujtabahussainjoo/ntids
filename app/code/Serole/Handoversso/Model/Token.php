<?php
namespace Serole\Handoversso\Model;

class Token extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Handoversso\Model\ResourceModel\Token');
    }
	
	public function generateToken() {
		try {
			$string = $this->str_rand(32);
		} catch (Exception $e) {
		    die("Could not generate a random string. Is our OS secure?");
		}
        
        $token=microtime().$string;
        $token = str_replace(' ','',$token);
        $token = str_replace('.','',$token);        
        
        $this->setToken($token);	
	}
	
	public function str_rand(int $length = 64){ // 64 = 32
        $length = ($length < 4) ? 4 : $length;
        return bin2hex(random_bytes(($length-($length%2))/2));
    }
}
?>