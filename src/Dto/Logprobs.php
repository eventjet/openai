<?php

declare(strict_types=1);

namespace Eventjet\OpenAi\Dto;

final readonly class Logprobs
{
    /**
     * @param list<ChatCompletionTokenLogprob>|null $content
     * @param list<ChatCompletionTokenLogprob>|null $refusal
     */
    public function __construct(public array|null $content, public array|null $refusal)
    {
    }
}
