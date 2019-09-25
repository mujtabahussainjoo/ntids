<?php

namespace Infortis\Cgen\Plugin\Magento\Framework\View;

class Layout
{
    protected $request;
    
    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->request = $request;
    }

    public function aroundIsCacheable($subject, $procede)
    {
        if( $this->request->getFrontname() === 'asset' &&
            $this->request->getRoutename() == 'cgen')
        {
            return false;
        }
        return $procede();
    }
}
