<?php


namespace LegalWeb\Cloud\Model;


use LegalWeb\Cloud\Model\ResourceModel\Config as ResourceModel;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;


class Config extends AbstractModel implements DataObject\IdentityInterface
{
    public const FIELD_GUID                     = 'guid';
    public const FIELD_SERIALIZED_CONFIGURATION = 'serialized_configuration';

    const CACHE_TAG = 'lwc_c';

    protected $_idFieldName = ResourceModel::ID_FIELD_NAME;

    protected $_cacheTag = self::CACHE_TAG;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var DataObject
     */
    private $configData;
    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    public function __construct(
        DataObjectFactory $dataObjectFactory,
        SerializerInterface $serializer,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );

        $this->serializer        = $serializer;
        $this->dataObjectFactory = $dataObjectFactory;
    }


    protected function _construct()
    {
        parent::_construct();

        $this->_init(ResourceModel::class);
    }

    public function getIdentities()
    {
        return [
            self::CACHE_TAG . '_' . $this->getId()
        ];
    }

    public function setGuid(string $guid): Config
    {
        $this->setData(self::FIELD_GUID, $guid);

        return $this;
    }

    public function setSerializedConfiguration(string $serializedConfiguration): Config
    {
        $this->setData(self::FIELD_SERIALIZED_CONFIGURATION, $serializedConfiguration);

        return $this;
    }

    public function getSerializedConfiguration(): string
    {
        return $this->_getData(self::FIELD_SERIALIZED_CONFIGURATION);
    }

    public function getConfigData(): DataObject
    {
        if (!$this->configData) {
            $this->configData = $this->dataObjectFactory->create([
                'data' => $this->serializer->unserialize($this->getSerializedConfiguration())
            ]);
        }
        return $this->configData;
    }
}
