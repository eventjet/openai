<?php

declare(strict_types=1);

namespace Eventjet\OpenAi\Dto;

final readonly class ChatCompletionRequestMessageContentPartImage
{
    /**
     * @param 'image_url' $type
     */
    public function __construct(public string $type, public ImageUrl $image_url)
    {
    }
}
