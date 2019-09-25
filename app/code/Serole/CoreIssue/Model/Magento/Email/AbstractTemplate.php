<?php

namespace Serole\CoreIssue\Model\Magento\Email;


class AbstractTemplate extends \Magento\Email\Model\AbstractTemplate
{
	public function setForcedArea($templateId)
	{
		if (!isset($this->area)) {
			$this->area = $this->emailConfig->getTemplateArea($templateId);
		}
		return $this;
	}
	public function getFilterFactory(){
		//do nothing//
		}
	public function getType(){
		//do nothing//
		}
}
	
	