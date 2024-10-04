<?php

declare(strict_types=1);

namespace Eventjet\OpenAi\Dto;

final readonly class ResponseFormatJsonObject
{
    /**
     * @param 'json_object' $type
     */
    public function __construct(public string $type)
    {
    }
}
