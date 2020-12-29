<?php


namespace LegalWeb\Cloud\Plugin;


use LegalWeb\Cloud\Model\ModuleConfig;
use LegalWeb\Cloud\Exceptions\ConfigurationException;
use Magento\Config\Model\Config\Structure\Data as StructureData;
use Psr\Log\LoggerInterface;

class AdjustConfigFields
{
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        LoggerInterface $logger,
        ModuleConfig $moduleConfig
    )
    {
        $this->moduleConfig = $moduleConfig;
        $this->logger       = $logger;
    }

    public function beforeMerge(StructureData $object, array $config)
    {

        if (!isset($config['config']['system']['sections']['legalweb_cloud'])) {
            return [$config];
        }

        try {
            $guid = $this->moduleConfig->getGuid();

            if ($guid) {
                unset($config['config']['system']['sections']['legalweb_cloud']['children']['general']['children']['license']);
            }

        } catch (ConfigurationException $e) {
            // No guid set - don't show information group
            unset($config['config']['system']['sections']['legalweb_cloud']['children']['information']);
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        return [$config];
    }
}
