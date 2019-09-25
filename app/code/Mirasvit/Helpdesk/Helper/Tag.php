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

class Tag extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Mirasvit\Helpdesk\Model\TagFactory
     */
    protected $tagFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Tag\CollectionFactory
     */
    protected $tagCollectionFactory;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Helpdesk\Model\TagFactory                          $tagFactory
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory
     * @param \Magento\Framework\App\Helper\Context                        $context
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\TagFactory $tagFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->context = $context;
        $this->tagFactory = $tagFactory;
        $this->tagCollectionFactory = $tagCollectionFactory;
        $this->context = $context;
        parent::__construct($context);
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     * @param string|int[]                    $tags
     * @return void
     */
    public function addTags($ticket, $tags)
    {
        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }
        $ticket->loadTagIds();
        $tagIds = $ticket->getTagIds();
        foreach ($tags as $tagName) {
            if (!$tag = $this->getTag($tagName)) {
                continue;
            }
            array_push($tagIds, $tag->getId());
        }
        $ticket->setTagIds(array_unique($tagIds));
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     * @param string|int[]                    $tags
     * @return void
     */
    public function removeTags($ticket, $tags)
    {
        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }
        $tagIds = $ticket->getTagIds();
        foreach ($tags as $tagName) {
            if (!$tag = $this->getTag($tagName)) {
                continue;
            }
            if (($key = array_search($tag->getId(), $tagIds)) !== false) {
                unset($tagIds[$key]);
            }
        }
        $ticket->setTagIds($tagIds);
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     * @param string|int[]                    $tags
     * @return void
     */
    public function setTags($ticket, $tags)
    {
        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }
        $tagIds = [];
        foreach ($tags as $tagName) {
            $tag = $this->getTag($tagName);
            if (!$tag) {
                continue;
            }
            $tagIds[] = $tag->getId();
        }
        $ticket->setTagIds($tagIds);
    }

    /**
     * @param string $tagName
     *
     * @return $this|bool|\Mirasvit\Helpdesk\Model\Tag
     */
    public function getTag($tagName)
    {
        $tagName = trim($tagName);
        if (!$tagName) {
            return false;
        }
        $collection = $this->tagCollectionFactory->create()
            ->addFieldToFilter('name', $tagName);
        if ($collection->count()) {
            $tag = $collection->getFirstItem();
        } else {
            $tag = $this->tagFactory->create()->setName($tagName)->save();
        }

        return $tag;
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     *
     * @return string
     */
    public function getTagsAsString($ticket)
    {
        $ticket->loadTagIds();
        if (count($ticket->getTagIds()) == 0) {
            return '';
        }

        $collection = $this->tagCollectionFactory->create()
                        ->addFieldToFilter('tag_id', $ticket->getTagIds());
        $arr = [];
        foreach ($collection as $tag) {
            $arr[] = $tag->getName();
        }

        return implode(', ', $arr);
    }
}
