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
 * @package   mirasvit/module-core
 * @version   1.2.75
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Core\Block\Adminhtml\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use Mirasvit\Core\Api\Service\ValidationServiceInterface;
use Mirasvit\Core\Model\ModuleFactory;
use Mirasvit\Core\Model\Module;

class Modules extends Field
{
    /**
     * @var ModuleFactory
     */
    protected $moduleFactory;
    /**
     * @var ValidationServiceInterface
     */
    private $validationService;

    public function __construct(
        ValidationServiceInterface $validationService,
        ModuleFactory $moduleFactory,
        Context $context,
        array $data = []
    ) {
        $this->validationService = $validationService;
        $this->moduleFactory = $moduleFactory;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('config/form/field/modules.phtml');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * @return \Mirasvit\Core\Model\Module[]
     */
    public function getModules()
    {
        $modules = [];

        foreach ($this->moduleFactory->create()->getInstalledModules() as $moduleName) {
            $module = $this->moduleFactory->create()
                ->load($moduleName);

            if ($module->getModuleName() || $module->getName()) {
                $modules[] = $module;
            }
        }

        usort($modules, function ($a, $b) {
            return strcmp($b->getName(), $a->getName());
        });

        return $modules;
    }

    /**
     * Check whether validator available for that module or not.
     *
     * @param Module $module
     *
     * @return bool
     */
    public function isValidationAvailable(Module $module)
    {
        foreach ($this->validationService->getValidators() as $validator) {
            if ($module->getModuleName() == $validator->getModuleName()
                || in_array($validator->getModuleName(), $module->getRequiredModuleNames($module->getModuleName()))
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get validation URL for given module.
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('mstcore/validator/');
    }
}
