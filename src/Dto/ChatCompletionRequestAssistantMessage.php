<?php

declare(strict_types=1);

namespace Eventjet\OpenAi\Dto;

final readonly class ChatCompletionRequestAssistantMessage
{
    /**
     * @param 'assistant' $role
     */
    public function __construct(public string $role)
    {
    }
}
