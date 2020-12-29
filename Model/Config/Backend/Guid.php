<?php


namespace LegalWeb\Cloud\Model\Config\Backend;


use LegalWeb\Cloud\Model\ConfigCleaner;
use LegalWeb\Cloud\Model\ConfigLoader;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Guid extends Value
{
    /**
     * @var ConfigLoader
     */
    private $configLoader;
    /**
     * @var ConfigCleaner
     */
    private $configCleaner;

    public function __construct(
        ConfigCleaner $configCleaner,
        ConfigLoader $configLoader,
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
        $this->configLoader  = $configLoader;
        $this->configCleaner = $configCleaner;
    }


    public function afterSave()
    {
        $result = parent::afterSave();

        if ($this->isValueChanged()) {
            $this->handleGuidUpdate();
        }


        return $result;
    }

    private function handleGuidUpdate(): void
    {
        if ($this->getValue()) {
            $this->configLoader->refresh();
        }

        if ($this->getOldValue()) {
            $this->configCleaner->cleanByGuid($this->getOldValue());
        }
    }

    public function afterDelete()
    {
        $result = parent::afterDelete();

        if ($this->getOldValue()) {
            $this->configCleaner->cleanByGuid($this->getOldValue());
        }

        return $result;
    }
}
