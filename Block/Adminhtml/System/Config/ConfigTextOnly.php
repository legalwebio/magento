<?php


namespace LegalWeb\Cloud\Block\Adminhtml\System\Config;


use Exception;
use LegalWeb\Cloud\Model\Config;
use LegalWeb\Cloud\Model\ConfigRepository;
use LegalWeb\Cloud\Model\ModuleConfig;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

abstract class ConfigTextOnly extends TextOnly
{
    /**
     * @var ConfigRepository
     */
    private $configRepository;
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    public function __construct(
        ModuleConfig $moduleConfig,
        ConfigRepository $configRepository,
        Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->configRepository = $configRepository;
        $this->moduleConfig     = $moduleConfig;
    }

    abstract public function getValue(Config $config): string;

    protected function _getElementHtml(AbstractElement $element)
    {
        try {
            $this->setValue($element);
        } catch (Exception $e) {
            $this->_logger->critical($e);
        }

        return parent::_getElementHtml($element);
    }

    private function setValue(AbstractElement $element): void
    {
        $guid   = $this->moduleConfig->getGuid($element->getData('scope'), $element->getData('scope_id') ?: null);
        $config = $this->configRepository->getByGuid($guid);

        $element->setData('value', $this->getValue($config));
    }
}
