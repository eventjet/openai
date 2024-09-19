<?php

declare(strict_types=1);

namespace Eventjet\OpenAi\Dto;

/**
 * @phpstan-type ChatCompletionRequestMessage (
 *     ChatCompletionRequestSystemMessage |
 *     ChatCompletionRequestUserMessage |
 *     ChatCompletionRequestAssistantMessage |
 *     ChatCompletionRequestToolMessage |
 *     ChatCompletionRequestFunctionMessage
 * )
 * @phpstan-type ResponseFormatText 'text'
 * @phpstan-type ResponseFormatJsonObject 'json_object'
 * @phpstan-type ResponseFormatJsonSchema array<string, mixed>
 */
final readonly class CreateChatCompletionRequest
{
    /**
     * @param non-empty-list<ChatCompletionRequestMessage> $messages
     * @param ResponseFormatText | ResponseFormatJsonObject | ResponseFormatJsonSchema $response_format
     */
    public function __construct(
        public Model|string $model,
        public array $messages,
        public string|array|null $response_format = null,
    ) {
    }
}
