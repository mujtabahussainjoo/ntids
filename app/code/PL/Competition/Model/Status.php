<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 1:20 PM
 */
namespace PL\Competition\Model;
class Status
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;

    /**
     * get available statuses.
     *
     * @return []
     */
    public static function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Enabled')
            , self::STATUS_DISABLED => __('Disabled'),
        ];
    }
}
