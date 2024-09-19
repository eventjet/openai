<?php

declare(strict_types=1);

namespace Eventjet\OpenAi\Dto;

final readonly class ImageUrl
{
    public function __construct(public string $url)
    {
    }
}
