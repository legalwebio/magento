<?php


namespace LegalWeb\Cloud\Exceptions;


use Magento\Framework\Phrase;

class NoGuidException extends ConfigurationException
{
    public function __construct(Phrase $phrase = null, \Exception $cause = null, $code = 0)
    {
        if (!$phrase) {
            $phrase = __('There is no guid configured.');
        }

        parent::__construct($phrase, $cause, $code);
    }

}
