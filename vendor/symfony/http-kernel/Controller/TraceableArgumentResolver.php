<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Component\HttpKernel\Controller;

use OmniIconDeps\Symfony\Component\HttpFoundation\Request;
use OmniIconDeps\Symfony\Component\Stopwatch\Stopwatch;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TraceableArgumentResolver implements ArgumentResolverInterface
{
    public function __construct(private ArgumentResolverInterface $resolver, private Stopwatch $stopwatch)
    {
    }
    public function getArguments(Request $request, callable $controller, ?\ReflectionFunctionAbstract $reflector = null): array
    {
        $e = $this->stopwatch->start('controller.get_arguments');
        try {
            return $this->resolver->getArguments($request, $controller, $reflector);
        } finally {
            $e->stop();
        }
    }
}
