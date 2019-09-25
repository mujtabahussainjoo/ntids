<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/21/2016
 * Time: 2:20 AM
 */

namespace PL\Competition\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;

class CompetitionView extends Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    protected $_competitionHelper;

    /**
     * @param Template\Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $coreRegistry,
        \PL\Competition\Helper\Data $competitionHelper,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_competitionHelper = $competitionHelper;
        parent::__construct($context, $data);
    }


    public function getDetails()
    {
        return $this->_coreRegistry->registry('competitionData');
    }

    public function getImageUrl(\PL\Competition\Model\Competition $competition, $name='')
    {
        if($name!=""){
            return $this->_competitionHelper->getBaseUrlMedia($competition->getData($name));
        }


    }

    public function getFormActionUrl()
    {
        $id = $this->getRequest()->getParam('id',0);
        return $this->getUrl('competition/competition/saveCompetitor', ['id'=>$id,'_secure' => true]);
    }
}