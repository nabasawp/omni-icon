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
class TraceableControllerResolver implements ControllerResolverInterface
{
    public function __construct(private ControllerResolverInterface $resolver, private Stopwatch $stopwatch)
    {
    }
    public function getController(Request $request): callable|false
    {
        $e = $this->stopwatch->start('controller.get_callable');
        try {
            return $this->resolver->getController($request);
        } finally {
            $e->stop();
        }
    }
}
