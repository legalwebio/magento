<?php


namespace LegalWeb\Cloud\Block;


use Exception;
use LegalWeb\Cloud\Model\ConfigProvider;
use LegalWeb\Cloud\Model\ContentResolver;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use RuntimeException;


/**
 * @method string|null getConfigurationPath()
 * @method ConfigurationContent setConfigurationPath(string $configurationPath)
 */
class ConfigurationContent extends Template implements BlockInterface, IdentityInterface
{

    public const CONFIG_PATH_CONTRACT_CHECKOUT            = 'services/contractcheckout';
    public const CONFIG_PATH_CONTRACT_WITHDRAWAL          = 'services/contractwithdrawal';
    public const CONFIG_PATH_CONTRACT_WITHDRAWAL_DIGITAL = 'services/contractwithdrawaldigital';
    public const CONFIG_PATH_CONTRACT_WITHDRAWAL_SERVICE = 'services/contractwithdrawalservice';
    public const CONFIG_PATH_DATA_PRIVACY_STATEMENT      = 'services/dpstatement';
    public const CONFIG_PATH_IMPRINT                      = 'services/imprint';
    public const CONFIG_PATH_CONTRACT_TERMS               = 'services/contractterms';

    /**
     * @var ContentResolver
     */
    protected $contentResolver;
    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    public function __construct(
        ContentResolver $contentResolver,
        ConfigProvider $configProvider,
        Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->configProvider  = $configProvider;
        $this->contentResolver = $contentResolver;
    }

    public function getIdentities()
    {

        try {
            $config = $this->configProvider->getConfig();

            if ($config instanceof IdentityInterface) {
                return $config->getIdentities();
            }
        } catch (Exception $e) {
            $this->_logger->critical($e);
        }

        return [];
    }

    protected function _toHtml()
    {
        try {
            return $this->getContentHtml();
        } catch (Exception $e) {
            $this->_logger->critical($e);
        }

        return '';
    }

    private function getContentHtml(): string
    {
        $configurationPath = $this->getConfigurationPath();

        if (!$configurationPath || strpos($configurationPath, 'services/') !== 0) {
            throw new RuntimeException('Invalid configuration path');
        }

        return $this->contentResolver->getHtml(
            $this->configProvider->getConfig()->getConfigData(),
            $configurationPath
        );
    }
}
