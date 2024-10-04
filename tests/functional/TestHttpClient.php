<?php

declare(strict_types=1);

namespace Eventjet\Test\Functional\OpenAi;

use LogicException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use function count;
use function explode;
use function implode;
use function sprintf;

/**
 * @phpstan-type Matcher callable(RequestInterface): (true | string)
 */
final class TestHttpClient implements ClientInterface
{
    /** @var list<array{Matcher, ResponseInterface, positive-int}> */
    private array $map = [];

    /**
     * @param Matcher ...$matchers
     * @return Matcher
     */
    public static function and(callable ...$matchers): callable
    {
        return static function (RequestInterface $request) use ($matchers): true|string {
            $issues = [];
            $ok = 0;
            foreach ($matchers as $matcher) {
                $result = $matcher($request);
                if ($result === true) {
                    $ok++;
                    continue;
                }
                $issues[] = $result;
            }
            if ($issues === []) {
                return true;
            }
            return sprintf("And: %s of %s matchers failed:\n%s", $ok, count($matchers), self::indent(implode("\n", $issues)));
        };
    }

    /**
     * @return Matcher
     */
    public static function url(string $expected): callable
    {
        return static function (RequestInterface $request) use ($expected): true|string {
            $url = (string)$request->getUri();
            if ($url !== $expected) {
                return sprintf('Expected URL %s, got %s', $expected, $url);
            }
            return true;
        };
    }

    /**
     * @return Matcher
     */
    public static function path(string $expected): callable
    {
        return static function (RequestInterface $request) use ($expected): true|string {
            $path = $request->getUri()->getPath();
            if ($path !== $expected) {
                return sprintf('Expected path %s, got %s', $expected, $path);
            }
            return true;
        };
    }

    /**
     * @return Matcher
     */
    public static function method(string $string): callable
    {
        return static function (RequestInterface $request) use ($string): true|string {
            $method = $request->getMethod();
            if ($method !== $string) {
                return sprintf('Expected method %s, got %s', $string, $method);
            }
            return true;
        };
    }

    /**
     * @return Matcher
     */
    public static function header(string $header, string $expected): callable
    {
        return static function (RequestInterface $request) use ($header, $expected): true|string {
            $actual = $request->getHeaderLine($header);
            if ($actual !== $expected) {
                return sprintf('Expected header %s: %s, got %s', $header, $expected, $actual);
            }
            return true;
        };
    }

    private static function indent(string $string): string
    {
        $lines = explode("\n", $string);
        foreach ($lines as &$line) {
            $line = "  $line";
        }
        return implode("\n", $lines);
    }

    /**
     * @param array<int, string> $issues
     */
    private static function formatIssues(array $issues): string
    {
        $formatted = [];
        foreach ($issues as $index => $issue) {
            $formatted[] = sprintf("Response #%d:\n%s", $index, $issue);
        }
        return implode("\n\n", $formatted);
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        if ($this->map === []) {
            throw new LogicException('No responses mapped');
        }
        $matchingIndices = [];
        $issues = [];
        foreach ($this->map as $index => [$matcher]) {
            $result = $matcher($request);
            if ($result === true) {
                $matchingIndices[] = $index;
                continue;
            }
            $issues[$index] = $result;
        }
        if ($matchingIndices === []) {
            throw new LogicException(
                sprintf("No matching response found\n\n%s", self::formatIssues($issues)),
            );
        }
        if (count($matchingIndices) > 1) {
            throw new LogicException(
                sprintf('Expected exactly one matching response, but found %d', count($matchingIndices)),
            );
        }
        $index = $matchingIndices[0];
        $response = $this->map[$index][1];
        if ($this->map[$index][2] > 1) {
            /** @psalm-suppress PropertyTypeCoercion False positive: It can't become less than 1 here */
            $this->map[$index][2]--;
        } else {
            unset($this->map[$index]);
        }
        return $response;
    }

    /**
     * @param Matcher $matcher
     * @param positive-int $maxMatches
     */
    public function map(callable $matcher, ResponseInterface $response, int $maxMatches = 1): void
    {
        $this->map[] = [$matcher, $response, $maxMatches];
    }

    public function isExhausted(): bool
    {
        return $this->map === [];
    }
}
