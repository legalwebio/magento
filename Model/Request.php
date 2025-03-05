<?php


namespace LegalWeb\Cloud\Model;


use Exception;
use LegalWeb\Cloud\Exceptions\ResponseException;
use Magento\Framework\HTTP\ClientFactory;
use Magento\Framework\HTTP\ClientInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Request
{
    private const API_URI = 'https://legalweb.io/api';
    private const TIMEOUT = 30;

    /**
     * @var string|null
     */
    private $guid;
    /**
     * @var ResponseFactory
     */
    private $responseFactory;
    /**
     * @var ClientFactory
     */
    private $clientFactory;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var CallbackUrl
     */
    private $callbackUrl;

    public function __construct(
        ResponseFactory $responseFactory,
        ClientFactory $clientFactory,
        SerializerInterface $serializer,
        CallbackUrl $callbackUrl
    )
    {
        $this->responseFactory = $responseFactory;
        $this->clientFactory   = $clientFactory;
        $this->serializer      = $serializer;
        $this->callbackUrl     = $callbackUrl;
    }


    public function setGuid(string $guid): Request
    {
        $this->guid = $guid;

        return $this;
    }

    public function get(): Response
    {
        $curlClient = $this->clientFactory->create();
        $curlClient->setTimeout(self::TIMEOUT);
        $curlClient->addHeader('Guid', $this->guid);
        $curlClient->addHeader('Callback', $this->callbackUrl->create());
        $curlClient->addHeader('Accept', 'application/json');
        $curlClient->setOption(CURLOPT_FOLLOWLOCATION, true);
        $curlClient->get(self::API_URI);

        $this->validateResponse($curlClient);

        return $this->responseFactory->create()
            ->setBody($curlClient->getBody());
    }

    private function validateResponse(ClientInterface $client): void
    {
        $status = $client->getStatus();
        if ($status < 200 || $status >= 400) {
            throw new ResponseException(__("Invalid HTTP status: {$status}"));
        }

        $body = $client->getBody();

        if (empty($body)) {
            throw new ResponseException(__('Empty response body'));
        }

        try {
            $data = $this->serializer->unserialize($body);
        } catch (Exception $e) {
            throw new ResponseException(__('Unable to unserialize response.'), $e);
        }

        $message = $data['message'] ?? null;

        if ($message) {
            throw new ResponseException(__($message));
        }
    }
}
