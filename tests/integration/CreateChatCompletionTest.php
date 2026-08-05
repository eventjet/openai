<?php

declare(strict_types=1);

namespace Eventjet\Test\Integration\OpenAi;

use Eventjet\OpenAi\Dto\ChatCompletionRequestSystemMessage;
use Eventjet\OpenAi\Dto\ChatCompletionRequestUserMessage;
use Eventjet\OpenAi\Dto\CreateChatCompletionRequest;
use Eventjet\OpenAi\Dto\Model;
use Eventjet\OpenAi\OpenAi;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Override;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

use function getenv;
use function is_string;

final class CreateChatCompletionTest extends TestCase
{
    private OpenAi $openAi;
    private ResponseFactoryInterface&StreamFactoryInterface $httpFactory;

    public function testHappyPath(): void
    {
        $response = $this->openAi->createChatCompletion(new CreateChatCompletionRequest(Model::Gpt4oMini, [
            new ChatCompletionRequestSystemMessage(
                'You are a not-so-smart AI and respond to any user message with "42" (without quotes)',
                'system',
            ),
            new ChatCompletionRequestUserMessage('Hello, how are you?', 'user'),
        ]));

        self::assertCount(1, $response->choices);
        self::assertSame('42', $response->choices[0]->message->content);
    }

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->httpFactory = new HttpFactory();
        $apiKey = getenv('OPENAI_API_KEY');
        if (!is_string($apiKey)) {
            self::markTestSkipped('Integration tests require OPENAI_API_KEY environment variable to be set');
        }
        $http = new Client();
        $this->openAi = new OpenAi($http, $apiKey, $this->httpFactory, $this->httpFactory);
    }
}
