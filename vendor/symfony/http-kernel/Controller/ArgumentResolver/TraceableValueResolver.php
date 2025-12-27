<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Component\HttpKernel\Controller\ArgumentResolver;

use OmniIconDeps\Symfony\Component\HttpFoundation\Request;
use OmniIconDeps\Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use OmniIconDeps\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use OmniIconDeps\Symfony\Component\Stopwatch\Stopwatch;
/**
 * Provides timing information via the stopwatch.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class TraceableValueResolver implements ValueResolverInterface
{
    public function __construct(private ValueResolverInterface $inner, private Stopwatch $stopwatch)
    {
    }
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $method = $this->inner::class . '::' . __FUNCTION__;
        $this->stopwatch->start($method, 'controller.argument_value_resolver');
        yield from $this->inner->resolve($request, $argument);
        $this->stopwatch->stop($method);
    }
}
