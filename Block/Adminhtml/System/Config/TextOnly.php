<?php


namespace LegalWeb\Cloud\Block\Adminhtml\System\Config;


use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class TextOnly extends Field
{
    protected function _getElementHtml(AbstractElement $element)
    {
        return "<strong>{$element->getData('value')}</strong>";
    }
}
