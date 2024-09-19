<?php

declare(strict_types=1);

namespace Eventjet\OpenAi\Dto;

/**
 * @phpstan-type ChatCompletionRequestUserMessageContentPart (
 *     ChatCompletionRequestMessageContentPartText |
 *     ChatCompletionRequestMessageContentPartImage
 * )
 */
final readonly class ChatCompletionRequestUserMessage
{
    /**
     * @param string|non-empty-list<ChatCompletionRequestUserMessageContentPart> $content
     */
    public function __construct(public string|array $content, public string $role)
    {
    }
}
