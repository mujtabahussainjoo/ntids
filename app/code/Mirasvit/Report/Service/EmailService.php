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
 * @package   mirasvit/module-report
 * @version   1.3.52
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Report\Service;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Mirasvit\Report\Api\Data\EmailInterface;
use Mirasvit\Report\Api\Repository\Email\BlockRepositoryInterface;
use Mirasvit\Report\Api\Repository\EmailRepositoryInterface;
use Mirasvit\Report\Api\Service\EmailServiceInterface;

class EmailService implements EmailServiceInterface
{
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var EmailRepositoryInterface
     */
    protected $emailRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        TransportBuilder $transportBuilder,
        EmailRepositoryInterface $emailRepository,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->emailRepository  = $emailRepository;
        $this->scopeConfig      = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function send(EmailInterface $email)
    {
        $vars = [
            'email'  => $email,
            'blocks' => "",
        ];

        $definedReports = $this->emailRepository->getReports();

        $blocks = $email->getBlocks();
        if (null === $blocks) {
            $blocks = [];
        }
        foreach ($blocks as $data) {
            if (isset($data['identifier'])) {
                $identifier = $data['identifier'];

                foreach ($definedReports as $report) {
                    if ($report['value'] == $identifier) {
                        /** @var BlockRepositoryInterface $repo */
                        $repo = $report['repository'];

                        $vars['blocks'] .= $repo->getContent($identifier, $data);
                    }
                }
            }
        }

        $emails = explode(',', $email->getRecipient());

        foreach ($emails as $mail) {
            if (!trim($mail)) {
                continue;
            }

            /** @var \Magento\Framework\Mail\Transport $transport */
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('report_email')
                ->setTemplateOptions([
                    'area'  => FrontNameResolver::AREA_CODE,
                    'store' => 0,
                ])
                ->setTemplateVars($vars)
                ->setFrom([
                    'name'  => $this->scopeConfig->getValue('trans_email/ident_general/name'),
                    'email' => $this->scopeConfig->getValue('trans_email/ident_general/email'),
                ])
                ->addTo($mail)
                ->getTransport();

            $transport->sendMessage();
        }
    }
}