<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Component\Routing\Matcher\Dumper;

use OmniIconDeps\Symfony\Component\Routing\RouteCollection;
/**
 * MatcherDumper is the abstract class for all built-in matcher dumpers.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class MatcherDumper implements MatcherDumperInterface
{
    public function __construct(private RouteCollection $routes)
    {
    }
    public function getRoutes(): RouteCollection
    {
        return $this->routes;
    }
}
