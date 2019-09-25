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


namespace Mirasvit\Helpdesk\Block\Adminhtml\Schedule\Edit\Form\Field;

use Magento\Framework\Escaper;
use Magento\Framework\Data\Form\Element;

class Schedule extends Element\AbstractElement
{
    /**
     * @var \Mirasvit\Helpdesk\Helper\Schedule
     */
    protected $helper;
    /**
     * @var string
     */
    protected $formName = 'schedule_edit_form';

    /**
     * @param \Mirasvit\Helpdesk\Helper\Schedule                   $helper
     * @param \Magento\Framework\Locale\ListsInterface             $localeLists
     * @param Element\Factory                                      $factoryElement
     * @param Element\CollectionFactory                            $factoryCollection
     * @param Escaper                                              $escaper
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param array                                                $data
     */
    public function __construct(
        \Mirasvit\Helpdesk\Helper\Schedule $helper,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        Element\Factory $factoryElement,
        Element\CollectionFactory $factoryCollection,
        Escaper $escaper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        $data = []
    ) {
        $this->localeLists = $localeLists;
        $this->localeDate = $localeDate;
        $this->helper = $helper;

        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->setType('time');
    }

    /**
     * Get the name
     *
     * @param string $suffix
     *
     * @return string
     */
    public function getName($suffix = '')
    {
        $name = parent::getName();
        $name .= $suffix;
        if (strpos($name, '[]') === false) {
            $name .= '[]';
        }
        return $name;
    }

    const FROM = 'time_from';
    const TO = 'time_to';

    /**
     * Get the element as HTML
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getElementHtml()
    {
        $this->addClass('select admin__control-select');

        $weekDays = $this->getValue();

        $html = '<div class="admin__field-control">';
        $html .= '<input type="hidden" id="' . $this->getHtmlId() . '" ' . $this->_getUiId() .
            ' data-form-part="' . $this->formName . '"/>';
        foreach ($this->helper->getWeekDays() as $weekdayId => $day) {
            $html .= '<div>';
            $html .= $this->renderDay($weekdayId, !$weekDays[$weekdayId]->isClosed());
            $html .= $this->renderHours($weekdayId, $weekDays, self::FROM);
            $html .= '&nbsp;—&nbsp;';
            $html .= $this->renderHours($weekdayId, $weekDays, self::TO);
            $html .= '</div>';
        }
        $html .= $this->getAfterElementHtml();
        $html .= '</div>';

        return $html;
    }

    /**
     * Get the HTML attributes
     *
     * @return string[]
     */
    public function getHtmlAttributes()
    {
        return [
            'title',
            'class',
            'style',
            'onclick',
            'onchange',
            'disabled',
            'size',
            'tabindex',
            'data-form-part',
            'data-role',
            'data-action'
        ];
    }

    /**
     * @param int    $weekdayId
     * @param array  $weekDays
     * @param string $part
     * @return string
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function renderHours($weekdayId, $weekDays, $part)
    {
        $scheduleTime = "00:00";
        if (isset($weekDays[$weekdayId])) {
            if ($part == self::FROM) {
                $scheduleTime = $weekDays[$weekdayId]->getTimeFrom();
            } else {
                $scheduleTime = $weekDays[$weekdayId]->getTimeTo();
            }
        }

        $html = '<select name="' . $this->getName('[' . $weekdayId . '][' . $part . ']') . '"' .
            $this->serialize($this->getHtmlAttributes()) . $this->_getUiId('hour') .
            'data-form-part="' . $this->formName . '">' . "\n";
        for ($i = 0; $i < 24; $i++) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT); //in 24 hours format
            for ($j = 0; $j < 60; $j += 10) {
                $minute = str_pad($j, 2, '0', STR_PAD_LEFT);
                $time = $hour . ':'.$minute;
                $label = $this->localeDate->formatDateTime(
                    new \DateTime($time, new \DateTimeZone('UTC')),
                    \IntlDateFormatter::NONE,
                    \IntlDateFormatter::SHORT,
                    null,
                    'UTC'
                );
                $html .= '<option value="' . $time . '" ' .
                    ($scheduleTime == $time ? 'selected="selected"' : '') .
                    '>' . $label . '</option>';
            }

        }
        $html .= '</select>' . "\n";

        return $html;
    }

    /**
     * @param int  $value
     * @param bool $checked
     * @return string
     */
    public function renderDay($value, $checked = false)
    {
        $workingDays = $this->helper->getWeekDays();

        return '' .
        '<input type="checkbox" id="' . $this->getHtmlId() . '_day_' . $value . '" ' .
        ($checked ? 'checked="checked"' : '') .
        'name="' . parent::getName() . '_day[' . $value . ']" value="' . $value . '" ' .
        'class="checkbox admin__control-checkbox"' .
        'data-form-part="' . $this->formName . '" ' .
        'data-ui-id="form-element-' . parent::getName() . '_day" >&nbsp;' .
        '<label class="label working-day" for="' . $this->getHtmlId() . '_day_' . $value . '">' .
        '<span>' . $workingDays[$value] . '</span>' .
        '</label>&nbsp;';
    }
}
