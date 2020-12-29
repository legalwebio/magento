<?php


namespace LegalWeb\Cloud\Model;


use LegalWeb\Cloud\Exceptions\ConfigurationException;
use LegalWeb\Cloud\Exceptions\InvalidSignatureException;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Encryption\Helper\Security;
use Magento\Framework\UrlInterface;

class CallbackUrl
{
    /**
     * @var Encryptor
     */
    private $encryptor;
    /**
     * @var UrlInterface
     */
    private $url;

    public function __construct(
        UrlInterface $url,
        Encryptor $encryptor
    )
    {
        $this->encryptor = $encryptor;
        $this->url       = $url;
    }


    public function create(): string
    {
        $url = trim($this->url->getUrl(), '/');
        return $url . '/rest/V1/legal-web-cloud/refresh/' . $this->getSignatureHash();
    }

    private function getSignatureHash(): string
    {
        $key = $this->getSignatureValue();

        return $this->encryptor->hash(
            $key
        );
    }

    private function getSignatureValue(): string
    {
        $keys = explode('\n', $this->encryptor->exportKeys());

        if (!$keys) {
            throw new ConfigurationException(__('No encryption key set.'));
        }

        return $keys[0];
    }

    public function validateSignature(string $signature): void
    {
        $isValid = Security::compareStrings(
            $this->getSignatureHash(),
            $signature
        );

        if ($isValid) {
            return;
        }

        throw new InvalidSignatureException(__('Invalid signature provided'));
    }


}
