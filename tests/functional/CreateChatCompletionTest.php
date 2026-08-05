<?php

declare(strict_types=1);

namespace Eventjet\Test\Functional\OpenAi;

use Eventjet\OpenAi\Dto\ChatCompletionRequestSystemMessage;
use Eventjet\OpenAi\Dto\ChatCompletionRequestUserMessage;
use Eventjet\OpenAi\Dto\ChatCompletionResponseMessage;
use Eventjet\OpenAi\Dto\Choice;
use Eventjet\OpenAi\Dto\CreateChatCompletionRequest;
use Eventjet\OpenAi\Dto\CreateChatCompletionResponse;
use Eventjet\OpenAi\Dto\Model;
use Eventjet\OpenAi\OpenAi;
use Eventjet\Test\Functional\OpenAi\TestHttpClient as Http;
use GuzzleHttp\Psr7\HttpFactory;
use Override;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use RuntimeException;

use function json_encode;
use function time;

use const JSON_THROW_ON_ERROR;

final class CreateChatCompletionTest extends TestCase
{
    private string $apiKey;
    private OpenAi $openAi;
    private Http $http;
    private ResponseFactoryInterface&StreamFactoryInterface $httpFactory;

    private static function completionRequest(): CreateChatCompletionRequest
    {
        return new CreateChatCompletionRequest(Model::Gpt4oMini, [
            new ChatCompletionRequestSystemMessage('You are a helpful assistant.', 'system'),
            new ChatCompletionRequestUserMessage('Hello, how are you?', 'user'),
        ]);
    }

    public function testHappyPath(): void
    {
        $this->http->map(Http::path('/v1/chat/completions'), $this->completionHttpResponse());

        $response = $this->openAi->createChatCompletion(self::completionRequest());

        self::assertTrue($this->http->isExhausted());
        self::assertCount(1, $response->choices);
    }

    public function testRequestHeaders(): void
    {
        $this->http->map(
            Http::and(
                Http::path('/v1/chat/completions'),
                Http::header('Authorization', 'Bearer ' . $this->apiKey),
                Http::header('Content-Type', 'application/json'),
                Http::header('Accept', 'application/json'),
            ),
            $this->completionHttpResponse(),
        );

        $this->openAi->createChatCompletion(self::completionRequest());

        self::assertTrue($this->http->isExhausted());
    }

    public function testUrl(): void
    {
        $this->http->map(Http::url('https://api.openai.com/v1/chat/completions'), $this->completionHttpResponse());

        $this->openAi->createChatCompletion(self::completionRequest());

        self::assertTrue($this->http->isExhausted());
    }

    public function testMethod(): void
    {
        $this->http->map(
            Http::and(Http::path('/v1/chat/completions'), Http::method('POST')),
            $this->completionHttpResponse(),
        );

        $this->openAi->createChatCompletion(self::completionRequest());

        self::assertTrue($this->http->isExhausted());
    }

    public function testThrowsWhenStatusCodeIsNot200(): void
    {
        $this->http->map(Http::path('/v1/chat/completions'), $this->httpFactory->createResponse(500));

        $this->expectExceptionMessage('Failed to create chat completion (HTTP 500)');
        $this->expectException(RuntimeException::class);

        $this->openAi->createChatCompletion(self::completionRequest());
    }

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->httpFactory = new HttpFactory();
        $this->apiKey = 'myapikey';
        $this->http = new Http();
        $this->openAi = new OpenAi($this->http, $this->apiKey, $this->httpFactory, $this->httpFactory);
    }

    private function completionHttpResponse(): ResponseInterface
    {
        return $this->httpFactory->createResponse()->withBody(
            $this->httpFactory->createStream(
                json_encode(
                    new CreateChatCompletionResponse(
                        'myid',
                        [
                            new Choice(
                                'stop',
                                0,
                                new ChatCompletionResponseMessage(
                                    'I\'m fine.',
                                    null,
                                    'assistant',
                                ),
                                null,
                            ),
                        ],
                        time(),
                        'gpt-4o-mini',
                        'chat.completion',
                    ),
                    JSON_THROW_ON_ERROR,
                ),
            ),
        );
    }
}
