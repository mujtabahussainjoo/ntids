<?php

namespace Infortis\Base\Helper;

class GetNowBasedOnLocale
{
    protected $timezone;

    protected $date;

    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->timezone = $timezone;
        $this->date = $date;
    }
    
    public function getNowTimeStamp()
    {
        return $this->timezone->scopeTimeStamp();
    }

    public function getNowDate()
    {
        return $this->date->date('Y-m-d H:i:s', $this->getNowTimeStamp());
    }

    /**
     * @deprecated
     */
    public function getNow()
    {
        return $this->getNowDate();
        // return $this->timezone->date()->setTime('0','0','0')->format('Y-m-d H:i:s');
    }
}
