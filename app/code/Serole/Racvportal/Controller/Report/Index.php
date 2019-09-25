<?php

    namespace Serole\Racvportal\Controller\Report;

    class Index extends \Serole\Racvportal\Controller\Cart\Ajax{

        public function execute(){
            return $this->resultPageFactory->create();
        }

    }