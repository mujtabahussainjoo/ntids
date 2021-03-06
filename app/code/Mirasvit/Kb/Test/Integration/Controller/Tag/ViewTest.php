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
 * @package   mirasvit/module-kb
 * @version   1.0.49
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


/**
 * Tag View Test
 */
namespace Mirasvit\Kb\Controller\Tag;

/**
 * @magentoDataFixture Mirasvit/Kb/_files/tags.php
 */
class ViewTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @covers  Mirasvit\Kb\Controller\Tag\View::execute
     */
    public function testViewAction()
    {
        $this->getRequest()->setParam('id', '3');
        $this->dispatch('kbase/tag/view');
        $body = $this->getResponse()->getBody();
        $this->assertNotEmpty($body);
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertFalse($this->getResponse()->isRedirect());
        $this->assertContains('<h1>Articles by tag "tag 3"</h1>', $body);
    }
}
