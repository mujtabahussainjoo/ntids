<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Serializer;

class SerializeJson
{
    /**
     * Serialize value
     *
     * @param $value
     *
     * @return string
     * @throws \Exception
     */
    public function serialize($value)
    {
        $output = json_encode($value);
        if ($output === false) {
            throw new \Exception('Unable to serialize value.');
        }

        return $output;
    }

    /**
     * Un-serialize value
     *
     * @param $json
     *
     * @return mixed
     * @throws \Exception
     */
    public function unserialize($json)
    {
        $output = json_decode($json, true);
        if (json_last_error() !== 0) {
            throw new \Exception('Unable to unserialize value.');
        }

        return $output;
    }
}