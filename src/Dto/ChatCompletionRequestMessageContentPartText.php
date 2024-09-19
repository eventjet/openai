<?php

declare(strict_types=1);

namespace Eventjet\OpenAi\Dto;

final readonly class ChatCompletionRequestMessageContentPartText
{
    /**
     * @param 'text' $type
     */
    public function __construct(public string $type, public string $text)
    {
    }
}
