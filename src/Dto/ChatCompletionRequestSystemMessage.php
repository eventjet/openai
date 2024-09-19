<?php

declare(strict_types=1);

namespace Eventjet\OpenAi\Dto;

/**
 * @phpstan-type ChatCompletionRequestSystemMessageContentPart ChatCompletionRequestMessageContentPartText
 */
final readonly class ChatCompletionRequestSystemMessage
{
    /**
     * @param string | non-empty-list<ChatCompletionRequestSystemMessageContentPart> $content
     * @param 'system' $role
     */
    public function __construct(public string|array $content, public string $role)
    {
    }
}
