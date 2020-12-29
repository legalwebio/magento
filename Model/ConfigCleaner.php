<?php


namespace LegalWeb\Cloud\Model;


use LegalWeb\Cloud\Model\ConfigRepository;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;

class ConfigCleaner
{
    /**
     * @var ConfigRepository
     */
    private $configRepository;
    /**
     * @var CollectionFactory
     */
    private $configDataCollectionFactory;

    public function __construct(
        ConfigRepository $configRepository,
        CollectionFactory $configDataCollectionFactory
    )
    {
        $this->configRepository            = $configRepository;
        $this->configDataCollectionFactory = $configDataCollectionFactory;
    }

    public function cleanByGuid(string $guid): ConfigCleaner
    {
        $configCollection = $this->configDataCollectionFactory->create()
            ->addFieldToFilter('path', ModuleConfig::XML_PATH_GUID)
            ->addFieldToFilter('value', $guid);

        if ($configCollection->getItems()) {
            return $this;
        }

        $this->configRepository->deleteByGuid($guid);

        return $this;
    }
}
