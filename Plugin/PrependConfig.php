<?php


namespace LegalWeb\Cloud\Plugin;


use Exception;
use LegalWeb\Cloud\Block\Config;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Page\Config\RendererInterface;
use Psr\Log\LoggerInterface;

class PrependConfig
{
    /**
     * @var LayoutInterface
     */
    private $layout;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        LoggerInterface $logger,
        LayoutInterface $layout
    )
    {
        $this->layout = $layout;
        $this->logger = $logger;
    }


    public function afterRenderHeadContent(RendererInterface $subject, $result)
    {

        try {
            $block = $this->layout->createBlock(Config::class);
            $block->setTemplate('LegalWeb_Cloud::config.phtml');
            $result = $block->toHtml() . $result;
        } catch (Exception $e) {
            $this->logger->critical($e);
        }

        return $result;
    }
}
