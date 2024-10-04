<?php

declare(strict_types=1);

namespace Eventjet\OpenAi\Dto;

/**
 * @phpstan-type ChatCompletionRequestToolMessageContentPart ChatCompletionRequestMessageContentPartText
 */
final readonly class ChatCompletionRequestToolMessage
{
    /**
     * @param 'tool' $role
     * @param string | non-empty-list<ChatCompletionRequestToolMessageContentPart> $content
     */
    public function __construct(public string $role, public string|array $content, public string $tool_call_id)
    {
    }
}
