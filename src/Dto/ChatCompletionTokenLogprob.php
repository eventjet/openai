<?php

declare(strict_types=1);

namespace Eventjet\OpenAi\Dto;

final readonly class ChatCompletionTokenLogprob
{
    /**
     * @param list<int>|null $bytes
     * @param list<TopLogprob> $top_logprobs
     */
    public function __construct(
        public string $token,
        public float|int $logprob,
        public array|null $bytes,
        public array $top_logprobs,
    ) {
    }
}
