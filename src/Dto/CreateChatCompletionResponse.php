<?php

declare(strict_types=1);

namespace Eventjet\OpenAi\Dto;

final readonly class CreateChatCompletionResponse
{
    /**
     * @param list<Choice> $choices
     * @param 'chat.completion' | string $object
     */
    public function __construct(
        public string $id,
        public array $choices,
        public int $created,
        public string $model,
        public string $object,
    ) {
    }
}
