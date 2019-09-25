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



namespace Mirasvit\Kb\Block\Article\ArticleList;

use Mirasvit\Kb\Model\Article\ArticleList\Toolbar as ToolbarModel;

/**
 * Article list toolbar.
 */
class Toolbar extends \Magento\Framework\View\Element\Template
{
    /**
     * Articles collection.
     *
     * @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    protected $collection = null;

    /**
     * List of available order fields.
     *
     * @var array
     */
    protected $availableOrder = null;

    /**
     * List of available view types.
     *
     * @var array
     */
    protected $availableMode = [];

    /**
     * Is enable View switcher.
     *
     * @var bool
     */
    protected $enableViewSwitcher = true;

    /**
     * Is Expanded.
     *
     * @var bool
     */
    protected $isExpanded = true;

    /**
     * Default Order field.
     *
     * @var string
     */
    protected $orderField = null;

    /**
     * Default direction.
     *
     * @var string
     */
    protected $direction = \Mirasvit\Kb\Helper\ArticleList::DEFAULT_SORT_DIRECTION;

    /**
     * @var bool
     */
    protected $paramsMemorizeAllowed = true;

    /**
     * Catalog session.
     *
     * @var \Magento\Catalog\Model\Session
     */
    protected $catalogSession;

    /**
     * @var ToolbarModel
     */
    protected $toolbarModel;

    /**
     * @var \Mirasvit\Kb\Helper\ArticleList
     */
    protected $articleListHelper;

    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $postDataHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Session                   $catalogSession
     * @param ToolbarModel                                     $toolbarModel
     * @param \Magento\Framework\Url\EncoderInterface          $urlEncoder
     * @param \Mirasvit\Kb\Helper\ArticleList                  $articleListHelper
     * @param \Magento\Framework\Data\Helper\PostHelper        $postDataHelper
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Session $catalogSession,
        ToolbarModel $toolbarModel,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Mirasvit\Kb\Helper\ArticleList $articleListHelper,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        array $data = []
    ) {
        $this->catalogSession    = $catalogSession;
        $this->toolbarModel      = $toolbarModel;
        $this->urlEncoder        = $urlEncoder;
        $this->articleListHelper = $articleListHelper;
        $this->postDataHelper    = $postDataHelper;

        parent::__construct($context, $data);
    }

    /**
     * Disable list state params memorizing.
     *
     * @return $this
     */
    public function disableParamsMemorizing()
    {
        $this->_paramsMemorizeAllowed = false;

        return $this;
    }

    /**
     * Memorize parameter value for session.
     *
     * @param string      $param Parameter name.
     * @param int|string  $value Parameter value.
     *
     * @return $this
     */
    protected function _memorizeParam($param, $value)
    {
        if ($this->paramsMemorizeAllowed && !$this->catalogSession->getParamsMemorizeDisabled()) {
            $this->catalogSession->setData($param, $value);
        }

        return $this;
    }

    /**
     * Set collection to pager.
     *
     * @param \Magento\Framework\Data\Collection $collection
     *
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        $this->collection->setCurPage($this->getCurrentPage());

        // we need to set pagination only if passed value integer and more that 0
        $limit = (int) $this->getLimit();
        if ($limit) {
            $this->collection->setPageSize($limit);
        }
        if ($this->getCurrentOrder()) {
            $this->collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
        }

        return $this;
    }

    /**
     * Return products collection instance.
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Return current page from request.
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->toolbarModel->getCurrentPage();
    }

    /**
     * Get grit products sort order field.
     *
     * @return string
     */
    public function getCurrentOrder()
    {
        $order = $this->_getData('_kb_current_list_order');
        if ($order) {
            return $order;
        }

        $orders = $this->getAvailableOrders();
        $defaultOrder = $this->getOrderField();

        if (!isset($orders[$defaultOrder])) {
            $keys = array_keys($orders);
            $defaultOrder = $keys[0];
        }

        $order = $this->toolbarModel->getOrder();
        if (!$order || !isset($orders[$order])) {
            $order = $defaultOrder;
        }

        if ($order != $defaultOrder) {
            $this->_memorizeParam('sort_order', $order);
        }

        $this->setData('_kb_current_list_order', $order);

        return $order;
    }

    /**
     * Retrieve current direction.
     *
     * @return string
     */
    public function getCurrentDirection()
    {
        $dir = $this->_getData('_kb_current_list_direction');
        if ($dir) {
            return $dir;
        }

        $directions = ['asc', 'desc'];
        $dir = strtolower($this->toolbarModel->getDirection());
        if (!$dir || !in_array($dir, $directions)) {
            $dir = $this->direction;
        }

        if ($dir != $this->direction) {
            $this->_memorizeParam('sort_direction', $dir);
        }

        $this->setData('_kb_current_list_direction', $dir);

        return $dir;
    }

    /**
     * Set default Order field.
     *
     * @param string $field
     *
     * @return $this
     */
    public function setDefaultOrder($field)
    {
        $this->loadAvailableOrders();
        if (isset($this->availableOrder[$field])) {
            $this->orderField = $field;
        }

        return $this;
    }

    /**
     * Set default sort direction.
     *
     * @param string $dir
     *
     * @return $this
     */
    public function setDefaultDirection($dir)
    {
        if (in_array(strtolower($dir), ['asc', 'desc'])) {
            $this->direction = strtolower($dir);
        }

        return $this;
    }

    /**
     * Retrieve available Order fields list.
     *
     * @return array
     */
    public function getAvailableOrders()
    {
        $this->loadAvailableOrders();

        return $this->availableOrder;
    }

    /**
     * Set Available order fields list.
     *
     * @param array $orders
     *
     * @return $this
     */
    public function setAvailableOrders($orders)
    {
        $this->availableOrder = $orders;

        return $this;
    }

    /**
     * Add order to available orders.
     *
     * @param string $order
     * @param string $value
     *
     * @return $this
     */
    public function addOrderToAvailableOrders($order, $value)
    {
        $this->loadAvailableOrders();
        $this->availableOrder[$order] = $value;

        return $this;
    }

    /**
     * Remove order from available orders if exists.
     *
     * @param string $order
     *
     * @return $this
     */
    public function removeOrderFromAvailableOrders($order)
    {
        $this->loadAvailableOrders();
        if (isset($this->availableOrder[$order])) {
            unset($this->availableOrder[$order]);
        }

        return $this;
    }

    /**
     * Compare defined order field vith current order field.
     *
     * @param string $order
     *
     * @return bool
     */
    public function isOrderCurrent($order)
    {
        return $order == $this->getCurrentOrder();
    }

    /**
     * Return current URL with rewrites and additional parameters.
     *
     * @param array $params Query parameters.
     *
     * @return string
     */
    public function getPagerUrl($params = [])
    {
        $urlParams = [];
        $urlParams['_current'] = true;
        $urlParams['_escape'] = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query'] = $params;

        return $this->getUrl('*/*/*', $urlParams);
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function getPagerEncodedUrl($params = [])
    {
        return $this->urlEncoder->encode($this->getPagerUrl($params));
    }

    /**
     * Retrieve current View mode.
     *
     * @return string
     */
    public function getCurrentMode()
    {
        $mode = $this->_getData('_kb_current_list_mode');
        if ($mode) {
            return $mode;
        }
        $defaultMode = 'list';
        $mode = $this->toolbarModel->getMode();
        if (!$mode || !isset($this->availableMode[$mode])) {
            $mode = $defaultMode;
        }

        $this->setData('_kb_current_list_mode', $mode);

        return $mode;
    }

    /**
     * Compare defined view mode with current active mode.
     *
     * @param string $mode
     *
     * @return bool
     */
    public function isModeActive($mode)
    {
        return $this->getCurrentMode() == $mode;
    }

    /**
     * Retrieve available view modes.
     *
     * @return array
     */
    public function getModes()
    {
        if ($this->availableMode === []) {
            $this->availableMode = ['list' => __('List')];
        }

        return $this->availableMode;
    }

    /**
     * Set available view modes list.
     *
     * @param array $modes
     *
     * @return $this
     */
    public function setModes($modes)
    {
        $this->getModes();
        if (!isset($this->availableMode)) {
            $this->availableMode = $modes;
        }

        return $this;
    }

    /**
     * Disable view switcher.
     *
     * @return $this
     */
    public function disableViewSwitcher()
    {
        $this->enableViewSwitcher = false;

        return $this;
    }

    /**
     * Enable view switcher.
     *
     * @return $this
     */
    public function enableViewSwitcher()
    {
        $this->enableViewSwitcher = true;

        return $this;
    }

    /**
     * Is a enabled view switcher.
     *
     * @return bool
     */
    public function isEnabledViewSwitcher()
    {
        return $this->enableViewSwitcher;
    }

    /**
     * Disable Expanded.
     *
     * @return $this
     */
    public function disableExpanded()
    {
        $this->isExpanded = false;

        return $this;
    }

    /**
     * Enable Expanded.
     *
     * @return $this
     */
    public function enableExpanded()
    {
        $this->isExpanded = true;

        return $this;
    }

    /**
     * Check is Expanded.
     *
     * @return bool
     */
    public function isExpanded()
    {
        return $this->isExpanded;
    }

    /**
     * Retrieve default per page values.
     *
     * @return string (comma separated)
     */
    public function getDefaultPerPageValue()
    {
        if ($this->getCurrentMode() == 'list' && ($default = $this->getDefaultListPerPage())) {
            return $default;
        }

        return $this->articleListHelper->getDefaultLimitPerPageValue($this->getCurrentMode());
    }

    /**
     * Retrieve available limits for current view mode.
     *
     * @return array
     */
    public function getAvailableLimit()
    {
        return [10 => 10, 20 => 20, 50 => 50];
    }

    /**
     * Get specified products limit display per page.
     *
     * @return string
     */
    public function getLimit()
    {
        $limit = $this->_getData('_kb_current_limit');
        if ($limit) {
            return $limit;
        }

        $limits = $this->getAvailableLimit();
        $defaultLimit = $this->getDefaultPerPageValue();
        if (!$defaultLimit || !isset($limits[$defaultLimit])) {
            $keys = array_keys($limits);
            $defaultLimit = $keys[0];
        }

        $limit = $this->toolbarModel->getLimit();
        if (!$limit || !isset($limits[$limit])) {
            $limit = $defaultLimit;
        }

        if ($limit != $defaultLimit) {
            $this->_memorizeParam('limit_page', $limit);
        }

        $this->setData('_kb_current_limit', $limit);

        return $limit;
    }

    /**
     * @param int $limit
     *
     * @return bool
     */
    public function isLimitCurrent($limit)
    {
        return $limit == $this->getLimit();
    }

    /**
     * @return int
     */
    public function getFirstNum()
    {
        $collection = $this->getCollection();

        return $collection->getPageSize() * ($collection->getCurPage() - 1) + 1;
    }

    /**
     * @return int
     */
    public function getLastNum()
    {
        $collection = $this->getCollection();

        return $collection->getPageSize() * ($collection->getCurPage() - 1) + $collection->count();
    }

    /**
     * @return int
     */
    public function getTotalNum()
    {
        return $this->getCollection()->getSize();
    }

    /**
     * @return bool
     */
    public function isFirstPage()
    {
        return $this->getCollection()->getCurPage() == 1;
    }

    /**
     * @return int
     */
    public function getLastPageNum()
    {
        return $this->getCollection()->getLastPageNumber();
    }

    /**
     * Render pagination HTML.
     *
     * @return string
     */
    public function getPagerHtml()
    {
        $pagerBlock = $this->getChildBlock('article_list_toolbar_pager');

        if ($pagerBlock instanceof \Magento\Framework\DataObject) {
            /* @var $pagerBlock \Magento\Theme\Block\Html\Pager */
            $pagerBlock->setAvailableLimit($this->getAvailableLimit());

            $pagerBlock->setUseContainer(
                false
            )->setShowPerPage(
                false
            )->setShowAmounts(
                false
            )->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setLimit(
                $this->getLimit()
            )->setCollection(
                $this->getCollection()
            );

            return $pagerBlock->toHtml();
        }

        return '';
    }

    /**
     * Retrieve widget options in json format.
     *
     * @param array $customOptions Optional parameter for passing custom selectors from template.
     *
     * @return string
     */
    public function getWidgetOptionsJson(array $customOptions = [])
    {
        $defaultMode = 'list';
        $options = [
            'mode' => ToolbarModel::MODE_PARAM_NAME,
            'direction' => ToolbarModel::DIRECTION_PARAM_NAME,
            'order' => ToolbarModel::ORDER_PARAM_NAME,
            'limit' => ToolbarModel::LIMIT_PARAM_NAME,
            'modeDefault' => $defaultMode,
            'directionDefault' => \Mirasvit\Kb\Helper\ArticleList::DEFAULT_SORT_DIRECTION,
            'orderDefault' => $this->articleListHelper->getDefaultSortField(),
            'limitDefault' => $this->articleListHelper->getDefaultLimitPerPageValue($defaultMode),
            'url' => $this->getPagerUrl(),
        ];
        $options = array_replace_recursive($options, $customOptions);

        return json_encode(['productListToolbarForm' => $options]);
    }

    /**
     * Get order field.
     *
     * @return null|string
     */
    protected function getOrderField()
    {
        if ($this->orderField === null) {
            $this->orderField = $this->articleListHelper->getDefaultSortField();
        }

        return $this->orderField;
    }

    /**
     * Load Available Orders.
     *
     * @return $this
     */
    private function loadAvailableOrders()
    {
        if ($this->availableOrder === null) {
            $this->availableOrder = [
                'created_at' => __('Date'),
                'rating' => __('Rating'),
            ];
        }

        return $this;
    }
}
