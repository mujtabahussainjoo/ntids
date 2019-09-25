<?php

namespace Infortis\Ultimo\Helper;

use DateTime;

class GetNowBasedOnLocale
{
    protected $locale;
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->timezone = $timezone;
    }
    
    public function getNow()
    {
        return $this->timezone->date()->setTime('0','0','0')->format(
            'Y-M-d H:m:s');        
    }
}
