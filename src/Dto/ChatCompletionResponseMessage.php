<?php

declare(strict_types=1);

namespace Eventjet\OpenAi\Dto;

final readonly class ChatCompletionResponseMessage
{
    /**
     * @param 'assistant' $role
     */
    public function __construct(
        public string|null $content,
        public string|null $refusal,
        public string $role,
    ) {
    }
}
