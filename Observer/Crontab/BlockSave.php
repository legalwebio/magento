<?php


namespace LegalWeb\Cloud\Observer\Crontab;

use Exception;
use LegalWeb\Cloud\Model\ConfigLoader;
use LegalWeb\Cloud\Model\ModuleConfig;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\Value;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;

class BlockSave implements ObserverInterface
{
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;
    /**
     * @var Emulation
     */
    private $appEmulation;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Emulation $appEmulation,
        ModuleConfig $moduleConfig,
        TransportBuilder $transportBuilder,
        LoggerInterface $logger
    )
    {
        $this->moduleConfig     = $moduleConfig;
        $this->transportBuilder = $transportBuilder;
        $this->appEmulation     = $appEmulation;
        $this->logger           = $logger;
    }


    public function execute(Observer $observer)
    {

        try {
            $guidValue = $observer->getData('guid_value');
            $transport = $observer->getData('transport');

            if (!$guidValue instanceof Value || !$transport instanceof DataObject) {
                return;
            }

            $autoUpdateEnabled = $this->moduleConfig->autoUpdateEnabled($guidValue->getScope(), $guidValue->getScopeId());
            $shouldSave = $transport->getData(ConfigLoader::TRANSPORT_KEY_SHOULD_SAVE);

            if (!$shouldSave || $autoUpdateEnabled) {
                return;
            }

            $transport->setData(
                ConfigLoader::TRANSPORT_KEY_SHOULD_SAVE,
                false
            );

            $recipient = $this->moduleConfig->getEmailRecipient($guidValue->getScope(), $guidValue->getScopeId());
            if ($recipient) {
                $this->sendEmail($recipient);
            }

        } catch (Exception $e) {
            $this->logger->critical($e);
        }
    }


    private function sendEmail(string $recipient): void
    {

        try {
            $this->appEmulation->startEnvironmentEmulation(
                Store::DEFAULT_STORE_ID,
                Area::AREA_ADMINHTML,
                true
            );

            $this->transportBuilder
                ->addTo($recipient)
                ->setTemplateIdentifier('legalweb_cloud_config_update_available')
                ->setFromByScope('general')
                ->setTemplateOptions(['area' => Area::AREA_ADMINHTML, 'store' => Store::DEFAULT_STORE_ID])
                ->setTemplateVars([])
                ->getTransport()
                ->sendMessage();
        } finally {
            $this->appEmulation->stopEnvironmentEmulation();
        }
    }
}
