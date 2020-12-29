<?php


namespace LegalWeb\Cloud\Model;


class Response
{

    /**
     * @var string
     */
    private $body;

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): Response
    {
        $this->body = $body;

        return $this;
    }
}
