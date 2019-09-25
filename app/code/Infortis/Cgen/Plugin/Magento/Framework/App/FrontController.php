<?php
namespace Infortis\Cgen\Plugin\Magento\Framework\App;
class FrontController
{
    const CACHE_PREFIX = 'CGEN_CACHE';
    protected $response;
    protected $request;
    protected $cacheManager;

    static protected $cacheHit=false;        

    public function __construct(
        \Magento\Framework\App\Response\Http $response,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\CacheInterface $cacheManager
    )
    {
        $this->response        = $response;
        $this->request         = $request;
        $this->cacheManager    = $cacheManager;
    }
    
    public function aroundDispatch(
        \Magento\Framework\App\FrontControllerInterface $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {                
        $key        = $this->getCacheKey();
        $cache_data = $this->cacheManager->load($key);
        if(!$cache_data)
        {
            return $proceed($request);
        }
                
        if(!$this->shouldCacheRequest())
        {
            return $proceed($request);
        }
        
        $etag = md5($cache_data);
        if($etag === $this->request->getHeader('ifnonematch'))
        {
            header("HTTP/1.1 304 Not Modified"); 
            exit;            
        }
        
        self::$cacheHit = true;
        return $this->response
            ->setBody($cache_data)
            ->setHeader('Content-Type', 'text/css')
            ->setHeader('Cache-Control', 'public')
            ->setHeader('ETag', $etag);
    }

    public function afterDispatch($subject, $result)
    {
        if(self::$cacheHit)
        {
            return $result;
        }
        if(!($result instanceof \Magento\Framework\App\Response\Http\Interceptor))
        {
            return $result;
        }
        
        if(!$this->shouldCacheRequest())
        {
            return $result;
        }
        
        $key  = $this->getCacheKey();
        $data = $this->response->getBody();
        $this->cacheManager->save($data, $key, [\Infortis\Cgen\Block\Asset\Css::CACHE_TAG_CGEN_ASSET_CSS]);
        return $result;
    }
    
    protected function getCacheKey()
    {
        $storeManager = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Store\Model\StoreManagerInterface');
        $cacheKeyElements = self::CACHE_PREFIX . $storeManager->getStore()->getCode() . $this->getPathInfo();

        // //TODO:
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/gen.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info("cahe key: " . self::CACHE_PREFIX .' + '. $storeManager->getStore()->getCode() .' + '. $this->getPathInfo() ); ///
        // $logger->info("store code: " . $storeManager->getStore()->getCode() ); ///
        // $logger->info("md5: " . md5($cacheKeyElements) ); ///
        // $logger->info("\n"); ///
        // //

        return md5($cacheKeyElements);
    }
        
    protected function getPathInfo()
    {
        return trim($this->request->getPathInfo(),'/');  
    }
            
    protected function shouldCacheRequest()
    {
        $path = $this->getPathInfo();
        return in_array($path, [
            'asset/dynamic/assets/m/iult/f/cfg.css']
        );
        
    }
}
