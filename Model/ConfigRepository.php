<?php


namespace LegalWeb\Cloud\Model;


use Exception;
use LegalWeb\Cloud\Model\ResourceModel\Config as ResourceModel;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ConfigRepository
{
    /**
     * @var ResourceModel
     */
    private $resource;
    /**
     * @var ConfigFactory
     */
    private $configFactory;


    public function __construct(
        ResourceModel $resource,
        ConfigFactory $configFactory
    )
    {
        $this->resource      = $resource;
        $this->configFactory = $configFactory;
    }

    public function getByGuid(string $guid): Config
    {
        $config = $this->configFactory->create();

        $this->resource->load(
            $config,
            $guid,
            Config::FIELD_GUID
        );

        if (!$config->getId()) {
            throw new NoSuchEntityException(__("No configuration for GUID '%1' found", $guid));
        }

        return $config;
    }


    public function save(Config $config): Config
    {
        try {
            $this->resource->save($config);
        } catch (Exception $e) {
            throw new CouldNotSaveException(
                __($e->getMessage()),
                $e
            );
        }

        return $config;
    }

    public function deleteByGuid(string $guid): ConfigRepository
    {
        try {
            $this->resource->delete(
                $this->getByGuid($guid)
            );
        } catch (NoSuchEntityException $exception) {
            // Configuration didn't exist in the first place
            // This might be the case if user mistyped guid
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__("Could not delete Configuration for GUID {$guid}"), $exception);
        }

        return $this;
    }


}
