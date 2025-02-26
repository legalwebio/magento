<?php


namespace LegalWeb\Cloud\Block;


use Exception;
use LegalWeb\Cloud\Exceptions\NoGuidException;
use LegalWeb\Cloud\Model\ConfigProvider;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;

class Config extends Template implements IdentityInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        SerializerInterface $serializer,
        ConfigProvider $configProvider,
        Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->configProvider  = $configProvider;
        $this->serializer      = $serializer;
    }


    private function getConfig(): \LegalWeb\Cloud\Model\Config
    {
        return $this->configProvider->getConfig();
    }

    public function getPopupJs(): string | null
    {
        return $this->getConfig()->getConfigData()->getData('services/dppopupjs');
    }

    public function getPopupCss(): string | null
    {
        return $this->getConfig()->getConfigData()->getData('services/dppopupcss');
    }

    protected function _toHtml()
    {
        try {
            return parent::_toHtml();
        } catch (NoGuidException $e) {
            // Nothing to do
        } catch (Exception $e) {
            $this->_logger->critical($e);
        }

        return '';
    }

    public function getIdentities()
    {
        try {
            $config = $this->getConfig();
            if ($config instanceof IdentityInterface) {
                return $config->getIdentities();
            }
        } catch (Exception $e) {
            $this->_logger->critical($e);
        }

        return [];
    }

    public function getSerializedGeneralConfig(): string
    {
        return $this->serializer->serialize(
            $this->getConfig()->getConfigData()->getData('services/dppopupconfig/spDsgvoGeneralConfig')
        );
    }

    public function getSerializedIntegrationConfig(): string
    {
        return $this->serializer->serialize(
            $this->getConfig()->getConfigData()->getData('services/dppopupconfig/spDsgvoIntegrationConfig')
        );
    }
}
