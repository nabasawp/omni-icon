<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Component\HttpKernel\DataCollector;

use OmniIconDeps\Symfony\Component\ErrorHandler\Exception\FlattenException;
use OmniIconDeps\Symfony\Component\HttpFoundation\Request;
use OmniIconDeps\Symfony\Component\HttpFoundation\Response;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class ExceptionDataCollector extends DataCollector
{
    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        if (null !== $exception) {
            $this->data = ['exception' => FlattenException::createWithDataRepresentation($exception)];
        }
    }
    public function hasException(): bool
    {
        return isset($this->data['exception']);
    }
    public function getException(): \Exception|FlattenException
    {
        return $this->data['exception'];
    }
    public function getMessage(): string
    {
        return $this->data['exception']->getMessage();
    }
    public function getCode(): int
    {
        return $this->data['exception']->getCode();
    }
    public function getStatusCode(): int
    {
        return $this->data['exception']->getStatusCode();
    }
    public function getTrace(): array
    {
        return $this->data['exception']->getTrace();
    }
    public function getName(): string
    {
        return 'exception';
    }
}
