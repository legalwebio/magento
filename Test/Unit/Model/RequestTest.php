<?php declare(strict_types=1);
/**
 * Copyright ©2025 André Flitsch. All rights reserved.
 * See license.md for license details.
 */

namespace LegalWeb\Cloud\Test\Unit\Model;

require_once __DIR__ . '/../Stub/ResponseFactory.php';


use LegalWeb\Cloud\Exceptions\ResponseException;
use LegalWeb\Cloud\Model\CallbackUrl;
use LegalWeb\Cloud\Model\Request;
use LegalWeb\Cloud\Model\Response;
use LegalWeb\Cloud\Model\ResponseFactory;
use Magento\Framework\HTTP\ClientFactory;
use Magento\Framework\HTTP\ClientInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\TestFramework\Unit\BaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class RequestTest extends BaseTestCase
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var MockObject|JsonSerializer
     */
    private $serializerMock;

    /**
     * @var MockObject|ClientInterface
     */
    private $clientMock;
    /**
     * @var ClientFactory|(ClientFactory&object&MockObject)|(ClientFactory&MockObject)|(object&MockObject)|MockObject
     */
    private $clientFactoryMock;
    /**
     * @var CallbackUrl|(CallbackUrl&object&MockObject)|(CallbackUrl&MockObject)|(object&MockObject)|MockObject
     */
    private $callbackUrlMock;
    /**
     * @var ResponseFactory|(ResponseFactory&object&MockObject)|(ResponseFactory&MockObject)|(object&MockObject)|MockObject
     */
    private $responseFactoryMock;

    public function testServerErrorMessageThrowsException(): void
    {
        // Mock HTTP client response status
        $this->clientMock->method('getStatus')->willReturn(200);

        // Mock HTTP client response body that triggers exception
        $responseBody = '{"message": "Server Error"}';
        $this->clientMock->method('getBody')->willReturn($responseBody);

        // Mock JsonSerializer behavior for unserialization
        $this->serializerMock
            ->method('unserialize')
            ->with($responseBody)
            ->willReturn(['message' => 'Server Error']);

        // Expect exception with the specific message
        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage('Server Error');

        // Mock the ClientFactory to return the mocked ClientInterface
        $this->clientFactoryMock
            ->method('create')
            ->willReturn($this->clientMock);

        // execute request
        $this->request->get();
        $this->fail('Exception is not thrown when an error message is returned from the legalweb server');
    }

    public function testCorrectMessageDoesNotThrowException(): void
    {
        // Mock HTTP client response status
        $this->clientMock->method('getStatus')->willReturn(200);

        // Mock HTTP client response body that does NOT trigger exception
        $responseBody = '{"data": {"key": "value"}}';
        $this->clientMock->method('getBody')->willReturn($responseBody);

        // Mock JsonSerializer behavior for unserialization
        $this->serializerMock
            ->method('unserialize')
            ->with($responseBody)
            ->willReturn(['data' => ['key' => 'value']]);

        // Mock the ClientFactory to return the mocked ClientInterface
        $this->clientFactoryMock
            ->method('create')
            ->willReturn($this->clientMock);

        $this->request->get();

        $this->addToAssertionCount(1);
    }

    protected function setUp(): void
    {
        $this->serializerMock = $this->createMock(JsonSerializer::class);
        $this->clientFactoryMock = $this->createMock(ClientFactory::class);
        $this->clientMock = $this->createMock(ClientInterface::class);
        $this->callbackUrlMock = $this->createMock(CallbackUrl::class);

        // Create a mock for the ResponseFactory
        $this->responseFactoryMock = $this->createMock(ResponseFactory::class);
        $this->responseFactoryMock->method('create')
            ->willReturn(new Response());


        $this->request = new Request(
            $this->responseFactoryMock,
            $this->clientFactoryMock,
            $this->serializerMock,
            $this->callbackUrlMock
        );
    }
}
