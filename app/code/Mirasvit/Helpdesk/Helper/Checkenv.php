<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Helper;

class Checkenv extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->context = $context;
        parent::__construct($context);
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Gateway $gateway
     *
     * @return string
     */
    public function checkGateway($gateway)
    {
        $result = [];
        $ports = ['gmail.com' => 80, $gateway->getHost() => $gateway->getPort()];
        foreach ($ports as $host => $port) {
            $connection = @fsockopen($host, $port);
            if (is_resource($connection)) {
                $result[] = $host.':'.$port.' '.'('.getservbyport($port, 'tcp').') is open.';
                fclose($connection);
            } else {
                $result[] = $host.':'.$port.' is closed.';
            }
        }

        return implode("\n; ", $result);
    }
}
