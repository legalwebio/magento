<?php


namespace LegalWeb\Cloud\Model\ResourceModel;


use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Config extends AbstractDb
{
    const ID_FIELD_NAME = 'config_id';


    protected function _construct()
    {
        $this->_init(
            'legalweb_cloud_config',
            self::ID_FIELD_NAME
        );
    }
}
