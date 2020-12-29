<?php


namespace LegalWeb\Cloud\Model;


use LegalWeb\Cloud\Api\SignedConfigLoaderInterface;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use Magento\Framework\DataObject\Factory;
use Magento\Framework\Event\ManagerInterface;
use Psr\Log\LoggerInterface;

class SignedConfigLoader extends ConfigLoader implements SignedConfigLoaderInterface
{
    /**
     * @var CallbackUrl
     */
    private $callbackUrl;

    public function __construct(
        CallbackUrl $callbackUrl,
        Factory $dataObjectFactory,
        ManagerInterface $eventManager,
        LoggerInterface $logger,
        RequestFactory $requestFactory,
        CollectionFactory $configDataCollectionFactory,
        ConfigRepository $configRepository,
        ConfigFactory $configFactory
    )
    {
        parent::__construct(
            $dataObjectFactory,
            $eventManager,
            $logger,
            $requestFactory,
            $configDataCollectionFactory,
            $configRepository,
            $configFactory
        );

        $this->callbackUrl = $callbackUrl;
    }


    public function refreshWithSignature(string $signature): bool
    {
        $this->callbackUrl->validateSignature($signature);

        $this->refresh();

        return true;
    }
}
