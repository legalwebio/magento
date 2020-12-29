<?php


namespace LegalWeb\Cloud\Model;


use LegalWeb\Cloud\Model\Config;
use LegalWeb\Cloud\Model\ConfigRepository;

class ConfigProvider
{
    /**
     * @var ConfigRepository
     */
    private $configRepository;
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        ModuleConfig $moduleConfig,
        ConfigRepository $configRepository
    )
    {
        $this->configRepository = $configRepository;
        $this->moduleConfig     = $moduleConfig;
    }

    public function getConfig(): Config
    {
        if (!$this->config) {
            $this->config = $this->configRepository->getByGuid(
                $this->moduleConfig->getGuid()
            );
        }

        return $this->config;
    }
}
