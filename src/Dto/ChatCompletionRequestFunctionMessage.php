<?php

declare(strict_types=1);

namespace Eventjet\OpenAi\Dto;

final readonly class ChatCompletionRequestFunctionMessage
{
    /**
     * @param 'function' $role
     */
    public function __construct(public string $role, public string $content, public string $name)
    {
    }
}
