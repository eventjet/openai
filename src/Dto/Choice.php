<?php

declare(strict_types=1);

namespace Eventjet\OpenAi\Dto;

final readonly class Choice
{
    /**
     * @param 'stop' | 'length' | 'tool_calls' | 'content_filter' | 'function_call' | string $finish_reason
     */
    public function __construct(
        public string $finish_reason,
        public int $index,
        public ChatCompletionResponseMessage $message,
        public Logprobs|null $logprobs = null,
    ) {
    }
}
