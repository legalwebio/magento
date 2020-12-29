<?php


namespace LegalWeb\Cloud\Api;


interface SignedConfigLoaderInterface
{
    /**
     * @param string $signature
     * @return bool
     */
    public function refreshWithSignature(string $signature): bool;
}
