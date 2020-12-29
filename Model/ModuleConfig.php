<?php


namespace LegalWeb\Cloud\Model;


use LegalWeb\Cloud\Exceptions\NoGuidException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class ModuleConfig
{
    public const XML_PATH_GUID        = 'legalweb_cloud/general/guid';
    public const XML_PATH_AUTO_UPDATE = 'legalweb_cloud/auto_update/status';
    public const XML_PATH_EMAIL       = 'legalweb_cloud/auto_update/email';

    public const XML_PATH_PAGE_IMPRINT_ID                      = 'legalweb_cloud/pages/imprint_id';
    public const XML_PATH_PAGE_DATA_PRIVACY_ID                 = 'legalweb_cloud/pages/data_privacy_id';
    public const XML_PATH_PAGE_CONTRACT_CHECKOUT_ID            = 'legalweb_cloud/pages/contract_checkout_id';
    public const XML_PATH_PAGE_CONTRACT_WITHDRAWAL_ID          = 'legalweb_cloud/pages/contract_withdrawal_id';
    public const XML_PATH_PAGE_CONTRACT_WITHDRAWAL_DIGITAL_ID = 'legalweb_cloud/pages/contract_withdrawal_digital_id';
    public const XML_PATH_PAGE_CONTRACT_WITHDRAWAL_SERVICE_ID = 'legalweb_cloud/pages/contract_withdrawal_service_id';
    public const XML_PATH_PAGE_CONTRACT_TERMS_ID              = 'legalweb_cloud/pages/contract_terms_id';
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    )
    {
        $this->scopeConfig  = $scopeConfig;
        $this->storeManager = $storeManager;
    }


    public function getGuid(string $scope = ScopeInterface::SCOPE_STORES, int $scopeId = null): string
    {
        $value = $this->getScopeConfigValue(self::XML_PATH_GUID, $scope, $scopeId);

        if (!$value) {
            throw new NoGuidException();
        }

        return $value;
    }

    public function autoUpdateEnabled(string $scope = ScopeInterface::SCOPE_STORES, int $scopeId = null): bool
    {
        return (bool)$this->getScopeConfigValue(self::XML_PATH_AUTO_UPDATE, $scope, $scopeId);
    }

    public function getEmailRecipient(string $scope = ScopeInterface::SCOPE_STORES, int $scopeId = null): string
    {
        return $this->getScopeConfigValue(self::XML_PATH_EMAIL, $scope, $scopeId) ?: '';
    }


    private function getScopeConfigValue(string $path, string $scope = ScopeInterface::SCOPE_STORES, int $scopeId = null): ?string
    {
        if ($scopeId === null) {
            $scopeId = $this->storeManager->getStore()->getId();
        }

        return $this->scopeConfig->getValue(
            $path,
            $scope,
            $scopeId
        );
    }

    public function getImprintPageId(): ?int
    {
        return $this->getScopeConfigValue(self::XML_PATH_PAGE_IMPRINT_ID) ?: null;
    }

    public function getDataPrivacyPageId(): ?int
    {
        return $this->getScopeConfigValue(self::XML_PATH_PAGE_DATA_PRIVACY_ID) ?: null;
    }

    public function getContractCheckoutPageId(): ?int
    {
        return $this->getScopeConfigValue(self::XML_PATH_PAGE_CONTRACT_CHECKOUT_ID) ?: null;
    }

    public function getContractWithdrawalPageId(): ?int
    {
        return $this->getScopeConfigValue(self::XML_PATH_PAGE_CONTRACT_WITHDRAWAL_ID) ?: null;
    }

    public function getContractWithdrawalDigitalPageId(): ?int
    {
        return $this->getScopeConfigValue(self::XML_PATH_PAGE_CONTRACT_WITHDRAWAL_DIGITAL_ID) ?: null;
    }

    public function getContractWithdrawalServicePageId(): ?int
    {
        return $this->getScopeConfigValue(self::XML_PATH_PAGE_CONTRACT_WITHDRAWAL_SERVICE_ID) ?: null;
    }

    public function getContractTermsPageId(): ?int
    {
        return $this->getScopeConfigValue(self::XML_PATH_PAGE_CONTRACT_TERMS_ID) ?: null;
    }
}
