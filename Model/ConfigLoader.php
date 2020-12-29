<?php


namespace LegalWeb\Cloud\Model;


use Exception;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use Magento\Framework\App\Config\Value;
use Magento\Framework\DataObject\Factory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class ConfigLoader
{
    public const TRANSPORT_KEY_SHOULD_SAVE = 'should_save';

    /**
     * @var CollectionFactory
     */
    private $configDataCollectionFactory;
    /**
     * @var RequestFactory
     */
    private $requestFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ManagerInterface
     */
    private $eventManager;
    /**
     * @var ConfigRepository
     */
    private $configRepository;
    /**
     * @var ConfigFactory
     */
    private $configFactory;
    /**
     * @var Factory
     */
    private $dataObjectFactory;

    public function __construct(
        Factory $dataObjectFactory,
        ManagerInterface $eventManager,
        LoggerInterface $logger,
        RequestFactory $requestFactory,
        CollectionFactory $configDataCollectionFactory,
        ConfigRepository $configRepository,
        ConfigFactory $configFactory
    )
    {
        $this->configDataCollectionFactory = $configDataCollectionFactory;
        $this->requestFactory              = $requestFactory;
        $this->logger                      = $logger;
        $this->eventManager                = $eventManager;
        $this->configRepository            = $configRepository;
        $this->configFactory               = $configFactory;
        $this->dataObjectFactory           = $dataObjectFactory;
    }


    public function refresh(): ConfigLoader
    {
        $this->configDataCollectionFactory->create()
            ->addFieldToFilter('path', ModuleConfig::XML_PATH_GUID)
            ->each(function (Value $value) {
                try {
                    $this->requestConfigForValue($value);
                } catch (Exception $e) {
                    $this->logger->critical($e);
                }
            });

        return $this;
    }

    private function requestConfigForValue(Value $value): void
    {
        $guid     = $value->getValue();
        $response = $this->requestFactory
            ->create()
            ->setGuid($guid)
            ->get();

        if (!$this->shouldSaveResponse($response, $value)) {
            return;
        }

        $this->saveConfiguration($guid, $response);
    }

    private function saveConfiguration(string $guid, Response $response): void
    {

        try {
            $config = $this->configRepository->getByGuid($guid);
        } catch (NoSuchEntityException $e) {
            $config = $this->configFactory->create()
                ->setGuid($guid);
        }

        $config->setSerializedConfiguration($response->getBody());

        $this->configRepository->save($config);
    }

    private function shouldSaveResponse(Response $response, Value $value): bool
    {
        $transport = $this->dataObjectFactory->create();

        $transport->setData(
            self::TRANSPORT_KEY_SHOULD_SAVE,
            !$this->configValueExists($value->getValue(), $response)
        );

        $this->eventManager->dispatch('legalweb_cloud_should_save_configuration', [
            'guid_value' => $value,
            'transport'  => $transport
        ]);

        return $transport->getData(self::TRANSPORT_KEY_SHOULD_SAVE);
    }


    private function configValueExists(string $guid, Response $response): bool
    {
        try {
            $config = $this->configRepository->getByGuid($guid);
            return $config->getSerializedConfiguration() == $response->getBody();
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

}
