<?php


namespace LegalWeb\Cloud\Block\Adminhtml\System\Config;


use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Button
 * @package LegalWeb\Cloud\Block\Adminhtml\System\Config
 *
 * @method string|null getButtonUrl()
 * @method string|null getButtonLabel()
 * @method string|null getTarget()
 * @method Button setTarget(string $target)
 */
class Button extends Field
{
    protected $_template = 'LegalWeb_Cloud::system/config/button.phtml';

    public function render(AbstractElement $element)
    {
        $element->unsScope();

        return parent::render($element);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $url          = $this->getUrl($originalData['button_url']);
        $this->addData([
            'button_label' => $originalData['button_label'],
            'button_url'   => $url,
            'html_id'      => $element->getHtmlId(),
        ]);

        if ($url == 'https://legalweb.io/?aff=4538') {
            $this->setTarget('_blank');
        }

        return $this->_toHtml();
    }
}
