<?php

declare(strict_types=1);

namespace Eventjet\OpenAi;

use Eventjet\Json\DecodeError;
use Eventjet\Json\Json;
use Eventjet\OpenAi\Dto\CreateChatCompletionRequest;
use Eventjet\OpenAi\Dto\CreateChatCompletionResponse;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use RuntimeException;
use SensitiveParameter;

use function json_encode;
use function sprintf;

use const JSON_THROW_ON_ERROR;

final readonly class OpenAi
{
    private const BASE_URL = 'https://api.openai.com/v1';

    public function __construct(
        private ClientInterface $httpClient,
        #[SensitiveParameter] private string $apiKey,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
    ) {
    }

    private static function buildUrl(string $path): string
    {
        return self::BASE_URL . $path;
    }

    public function createChatCompletion(CreateChatCompletionRequest $request): CreateChatCompletionResponse
    {
        $request = $this->requestFactory->createRequest('POST', self::buildUrl('/chat/completions'))
            ->withHeader('Authorization', 'Bearer ' . $this->apiKey)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Accept', 'application/json')
            ->withBody($this->streamFactory->createStream(json_encode($request, JSON_THROW_ON_ERROR)));
        $response = $this->httpClient->sendRequest($request);
        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException(sprintf('Failed to create chat completion (HTTP %d)', $response->getStatusCode()));
        }
        $completion = Json::decode((string)$response->getBody(), CreateChatCompletionResponse::class);
        if ($completion instanceof DecodeError) {
            throw $completion;
        }
        return $completion;
    }
}
