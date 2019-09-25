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


// @codingStandardsIgnoreFile
/**
 * This file is part of the EmailReplyParser package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

/**
 * @author William Durand <william.durand1@gmail.com>
 */
final class Fragment
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var bool
     */
    private $isHidden;

    /**
     * @var bool
     */
    private $isSignature;

    /**
     * @var bool
     */
    private $isQuoted;

    public function __construct($content, $isHidden, $isSignature, $isQuoted)
    {
        $this->content = $content;
        $this->isHidden = $isHidden;
        $this->isSignature = $isSignature;
        $this->isQuoted = $isQuoted;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return $this->isHidden;
    }

    /**
     * @return bool
     */
    public function isSignature()
    {
        return $this->isSignature;
    }

    /**
     * @return bool
     */
    public function isQuoted()
    {
        return $this->isQuoted;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return '' === str_replace("\n", '', $this->getContent());
    }

    public function __toString()
    {
        return $this->getContent();
    }
}
