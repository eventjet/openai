<?php

declare(strict_types=1);

namespace Eventjet\OpenAi\Dto;

enum Model: string
{
    case O1Preview = 'o1-preview';
    case O1Preview20240912 = 'o1-preview-2024-09-12';
    case O1Mini = 'o1-mini';
    case O1Mini20240912 = 'o1-mini-2024-09-12';
    case Gpt4o = 'gpt-4o';
    case Gpt4o20240806 = 'gpt-4o-2024-08-06';
    case Gpt4o20240513 = 'gpt-4o-2024-05-13';
    case Chatgpt4oLatest = 'chatgpt-4o-latest';
    case Gpt4oMini = 'gpt-4o-mini';
    case Gpt4oMini20240718 = 'gpt-4o-mini-2024-07-18';
    case Gpt4Turbo = 'gpt-4-turbo';
    case Gpt4Turbo20240409 = 'gpt-4-turbo-2024-04-09';
    case Gpt40125Preview = 'gpt-4-0125-preview';
    case Gpt4TurboPreview = 'gpt-4-turbo-preview';
    case Gpt41106Preview = 'gpt-4-1106-preview';
    case Gpt4VisionPreview = 'gpt-4-vision-preview';
    case Gpt4 = 'gpt-4';
    case Gpt40314 = 'gpt-4-0314';
    case Gpt40613 = 'gpt-4-0613';
    case Gpt432k = 'gpt-4-32k';
    case Gpt432k0314 = 'gpt-4-32k-0314';
    case Gpt432k0613 = 'gpt-4-32k-0613';
    case Gpt35Turbo = 'gpt-3.5-turbo';
    case Gpt35Turbo16k = 'gpt-3.5-turbo-16k';
    case Gpt35Turbo0301 = 'gpt-3.5-turbo-0301';
    case Gpt35Turbo0613 = 'gpt-3.5-turbo-0613';
    case Gpt35Turbo1106 = 'gpt-3.5-turbo-1106';
    case Gpt35Turbo0125 = 'gpt-3.5-turbo-0125';
    case Gpt35Turbo16k0613 = 'gpt-3.5-turbo-16k-0613';
}
