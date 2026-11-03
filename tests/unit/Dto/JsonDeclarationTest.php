<?php

declare(strict_types=1);

namespace Eventjet\Test\Unit\OpenAi\Dto;

use Eventjet\Json\Json;
use Eventjet\OpenAi\Dto\CreateChatCompletionResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class JsonDeclarationTest extends TestCase
{
    /**
     * @return iterable<string, array{class-string}>
     */
    public static function decodedClasses(): iterable
    {
        yield 'CreateChatCompletionResponse' => [CreateChatCompletionResponse::class];
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('decodedClasses')]
    public function testDeclarationIsSupported(string $class): void
    {
        $error = Json::validateType($class);

        self::assertNull($error, $error?->getMessage() ?? '');
    }
}
