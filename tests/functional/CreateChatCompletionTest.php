<?php

declare(strict_types=1);

namespace Eventjet\Test\Functional\OpenAi;

use Eventjet\Json\DecodeError;
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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use RuntimeException;

use function json_encode;
use function str_replace;
use function time;

use const JSON_THROW_ON_ERROR;

final class CreateChatCompletionTest extends TestCase
{
    private string $apiKey;
    private OpenAi $openAi;
    private Http $http;
    private ResponseFactoryInterface&StreamFactoryInterface $httpFactory;

    /**
     * @return iterable<string, array{string}>
     */
    public static function nonStringLiteralFields(): iterable
    {
        $response = <<<'JSON'
            {
                "id": "myid",
                "object": "chat.completion",
                "created": 1792061063,
                "model": "gpt-6-luna",
                "choices": [
                    {
                        "index": 0,
                        "message": {"role": "assistant", "content": null, "refusal": null},
                        "finish_reason": "stop"
                    }
                ]
            }
            JSON;
        yield 'finish_reason' => [str_replace('"stop"', '1', $response)];
        yield 'object' => [str_replace('"chat.completion"', '1', $response)];
        yield 'role' => [str_replace('"assistant"', '1', $response)];
    }

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

        $this->expectExceptionMessageIsOrContains('Failed to create chat completion (HTTP 500)');
        $this->expectException(RuntimeException::class);

        $this->openAi->createChatCompletion(self::completionRequest());
    }

    public function testAcceptsChoicesWithoutLogprobs(): void
    {
        $this->http->map(
            Http::path('/v1/chat/completions'),
            $this->httpFactory->createResponse()->withBody($this->httpFactory->createStream(<<<'JSON'
                {
                    "id": "myid",
                    "object": "chat.completion",
                    "created": 1792061063,
                    "model": "gpt-6-luna",
                    "choices": [
                        {
                            "index": 0,
                            "message": {"role": "assistant", "content": "I'm fine.", "refusal": null},
                            "finish_reason": "stop"
                        }
                    ]
                }
                JSON)),
        );

        $response = $this->openAi->createChatCompletion(self::completionRequest());

        self::assertSame('I\'m fine.', $response->choices[0]->message->content);
    }

    public function testKeepsValuesOutsideTheDocumentedLiterals(): void
    {
        $this->http->map(
            Http::path('/v1/chat/completions'),
            $this->httpFactory->createResponse()->withBody($this->httpFactory->createStream(<<<'JSON'
                {
                    "id": "myid",
                    "object": "chat.completion.v2",
                    "created": 1792061063,
                    "model": "gpt-6-luna",
                    "choices": [
                        {
                            "index": 0,
                            "message": {"role": "model", "content": "I'm fine.", "refusal": null},
                            "finish_reason": "something_new"
                        }
                    ]
                }
                JSON)),
        );

        $response = $this->openAi->createChatCompletion(self::completionRequest());

        self::assertSame('chat.completion.v2', $response->object);
        self::assertSame('model', $response->choices[0]->message->role);
        self::assertSame('something_new', $response->choices[0]->finish_reason);
    }

    #[DataProvider('nonStringLiteralFields')]
    public function testRejectsNonStringValuesForLiteralFields(string $response): void
    {
        $this->http->map(
            Http::path('/v1/chat/completions'),
            $this->httpFactory->createResponse()->withBody($this->httpFactory->createStream($response)),
        );

        $this->expectException(DecodeError::class);

        $this->openAi->createChatCompletion(self::completionRequest());
    }

    public function testThrowsWhenResponseDoesNotMatchTheExpectedShape(): void
    {
        $this->http->map(
            Http::path('/v1/chat/completions'),
            $this->httpFactory->createResponse()->withBody($this->httpFactory->createStream('{"id": "myid"}')),
        );

        $this->expectException(DecodeError::class);

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
