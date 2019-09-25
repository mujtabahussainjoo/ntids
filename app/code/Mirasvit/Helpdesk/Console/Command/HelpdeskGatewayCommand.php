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



namespace Mirasvit\Helpdesk\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\ObjectManagerFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Mirasvit\Helpdesk\Helper\Checkenv;

/**
 * Command for executing cron jobs
 */
class HelpdeskGatewayCommand extends Command
{
    const INPUT_GATEWAY_ID = 'gateway_id';
    const INPUT_MODE = 'mode';

    /**
     * Constructor
     *
     * @param ObjectManagerFactory $objectManagerFactory
     */
    public function __construct(
        Checkenv $helpdeskCheckenv,
        ScopeConfigInterface $scopeConfig,
        ObjectManagerFactory $objectManagerFactory
    ) {
        $this->objectManagerFactory = $objectManagerFactory;
        $this->scopeConfig = $scopeConfig;
        $this->helpdeskCheckenv = $helpdeskCheckenv;

        parent::__construct();
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setName('mirasvit:helpdesk:gateway')
            ->setDescription('Check gateway');

        $this->addArgument(
            self::INPUT_GATEWAY_ID,
            InputArgument::REQUIRED,
            'Gateway ID'
        );
        $this->addArgument(
            self::INPUT_MODE,
            InputArgument::OPTIONAL,
            'Possible value: silent'
        );
        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument(self::INPUT_GATEWAY_ID);
        $mode = $input->getArgument(self::INPUT_MODE);

        $omParams = $_SERVER;
        $omParams[StoreManager::PARAM_RUN_CODE] = 'admin';
        $omParams[Store::CUSTOM_ENTRY_POINT_PARAM] = true;
        $objectManager = $this->objectManagerFactory->create($omParams);

        $objectManager->get('Magento\Framework\App\State')->setAreaCode('frontend');
        /** @var \Mirasvit\Helpdesk\Helper\Fetch $fetchHelper */
        $fetchHelper = $objectManager->get('\Mirasvit\Helpdesk\Helper\Fetch');
        /** @var \Mirasvit\Helpdesk\Model\Gateway $gatewayModel */
        $gatewayModel = $objectManager->get('\Mirasvit\Helpdesk\Model\Gateway');
        $gateway = $gatewayModel->load($id);

        $emails = [];
        $message = '';
        try {
            $fetchHelper->connect($gateway);
            $mailbox = $fetchHelper->getMailbox();
            $emails = $mailbox->getMessages();
            $fetchHelper->close();
        } catch (\Mirasvit_Ddeboer_Imap_Exception_AuthenticationFailedException $e) {
            $message = $e->getMessage() . ' ('.$this->helpdeskCheckenv->checkGateway($gateway).')';
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $message = $e->getMessage();
        }
        if (!$mode || $mode != 'silent') {
            $output->writeln('<info>' . "Number of emails:" . count($emails) . '</info>');
            $output->writeln('Show first 10 emails');
            $index = 0;
            /** @var \Mirasvit_Ddeboer_Imap_Message $email */
            foreach ($emails as $email) {
                /* output the email header information */
                $output->writeln(' - ' . ++$index);
                if ($email->isSeen()) {
                    $output->writeln('<info>read</info>');
                } else {
                    $output->writeln('<error>unread</error>');
                }
                $output->writeln($email->getSubject() . " | " . $email->getFrom() . " | ");

                if ($index > 10) {
                    break;
                }
            }
        }
        if ($message) {
            $output->writeln('Error. ' . $message);
        } else {
            $output->writeln('Done');
        }

        return;
    }
}
