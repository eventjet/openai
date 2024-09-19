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
 */
final readonly class CreateChatCompletionRequest
{
    /**
     * @param non-empty-list<ChatCompletionRequestMessage> $messages
     */
    public function __construct(
        public Model|string $model,
        public array $messages,
    ) {
    }
}
