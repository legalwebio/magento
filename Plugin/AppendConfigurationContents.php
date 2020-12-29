<?php


namespace LegalWeb\Cloud\Plugin;


use Exception;
use LegalWeb\Cloud\Block\ConfigurationContent;
use LegalWeb\Cloud\Model\ModuleConfig;
use Magento\Cms\Block\Page;
use Magento\Cms\Model\Page as PageModel;
use Magento\Framework\View\LayoutInterface;
use Psr\Log\LoggerInterface;

class AppendConfigurationContents
{
    /**
     * @var LayoutInterface
     */
    private $layout;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    public function __construct(
        LayoutInterface $layout,
        LoggerInterface $logger,
        ModuleConfig $moduleConfig
    )
    {
        $this->layout       = $layout;
        $this->logger       = $logger;
        $this->moduleConfig = $moduleConfig;
    }


    public function afterToHtml(Page $subject, $html)
    {
        try {
            $html .= $this->getWidgetHtml($subject->getPage());
        } catch (Exception $e) {
            $this->logger->critical($e);
        }

        return $html;
    }

    private function getWidgetHtml(PageModel $page): string
    {
        $html    = '';
        $widgets = [
            ConfigurationContent::CONFIG_PATH_CONTRACT_CHECKOUT           => $this->moduleConfig->getContractCheckoutPageId(),
            ConfigurationContent::CONFIG_PATH_CONTRACT_WITHDRAWAL         => $this->moduleConfig->getContractWithdrawalPageId(),
            ConfigurationContent::CONFIG_PATH_CONTRACT_WITHDRAWAL_DIGITAL => $this->moduleConfig->getContractWithdrawalDigitalPageId(),
            ConfigurationContent::CONFIG_PATH_CONTRACT_WITHDRAWAL_SERVICE => $this->moduleConfig->getContractWithdrawalServicePageId(),
            ConfigurationContent::CONFIG_PATH_DATA_PRIVACY_STATEMENT      => $this->moduleConfig->getDataPrivacyPageId(),
            ConfigurationContent::CONFIG_PATH_IMPRINT                     => $this->moduleConfig->getImprintPageId(),
            ConfigurationContent::CONFIG_PATH_CONTRACT_TERMS              => $this->moduleConfig->getContractTermsPageId()
        ];

        foreach ($widgets as $configurationPath => $pageId) {

            if (!$pageId || $page->getId() != $pageId) {
                continue;
            }

            $block = $this->createConfigurationContentBlock();
            $block->setConfigurationPath($configurationPath);

            $html .= $block->toHtml();
        }

        return $html;
    }

    private function createConfigurationContentBlock(): ConfigurationContent
    {
        return $this->layout->createBlock(ConfigurationContent::class);
    }

}
