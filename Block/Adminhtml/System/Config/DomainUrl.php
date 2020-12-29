<?php


namespace LegalWeb\Cloud\Block\Adminhtml\System\Config;


use LegalWeb\Cloud\Model\Config;

class DomainUrl extends ConfigTextOnly
{
    public function getValue(Config $config): string
    {
        return $config->getConfigData()->getData('domain/domain_url');
    }
}
