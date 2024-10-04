<?php

declare(strict_types=1);

namespace Eventjet\OpenAi\Dto;

final readonly class TopLogprob
{
    /**
     * @param list<int> | null $bytes
     */
    public function __construct(
        public string $token,
        public float|int $logprob,
        public array|null $bytes,
    ) {
    }
}
