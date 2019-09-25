<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Model\CustomerStatistic;

use Magento\Framework\Session\SessionManager;

/**
 * Class Manager
 * @package Aheadworks\Rbslider\Model\CustomerStatistic
 */
class Manager
{
    /**
     * Lifetime for slides
     */
    const LIFETIME_SLIDE_ACTION = 86400; //24h

    /**
     * @var array
     */
    private $slidesArray;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @param SessionManager $sessionManager
     */
    public function __construct(
        SessionManager $sessionManager
    ) {
        $this->sessionManager = $sessionManager;
    }

    /**
     * Get slides from session
     *
     * @return array
     */
    private function getSlidesAction()
    {
        if (null === $this->slidesArray) {
            $this->slidesArray = $this->sessionManager->getData();

            if (is_array($this->slidesArray) && count($this->slidesArray)) {
                // Check and remove old slides from array
                foreach ($this->slidesArray as $key => $expireTime) {
                    if ($expireTime <= time()) {
                        unset($this->slidesArray[$key]);
                    }
                }
            } else {
                $this->slidesArray = [];
            }
        }

        return $this->slidesArray;
    }

    /**
     * Is set slide name in session
     *
     * @param string $name
     * @return bool
     */
    public function isSetSlideAction($name)
    {
        $slidesArray = $this->getSlidesAction();
        if (is_array($slidesArray) && isset($slidesArray[$name])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add name and expire time to array
     *
     * @param string $name
     * @return void
     */
    public function addSlideAction($name)
    {
        $this->slidesArray[$name] = self::LIFETIME_SLIDE_ACTION + time();
    }

    /**
     * Save data in session
     *
     * @return $this
     */
    public function save()
    {
        $this->sessionManager->setData($this->slidesArray);

        return $this;
    }
}
