<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Bundle\FrameworkBundle\CacheWarmer;

use OmniIconDeps\Psr\Container\ContainerInterface;
use OmniIconDeps\Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use OmniIconDeps\Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use OmniIconDeps\Symfony\Component\Routing\RouterInterface;
use OmniIconDeps\Symfony\Contracts\Service\ServiceSubscriberInterface;
/**
 * Generates the router matcher and generator classes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class RouterCacheWarmer implements CacheWarmerInterface, ServiceSubscriberInterface
{
    /**
     * As this cache warmer is optional, dependencies should be lazy-loaded, that's why a container should be injected.
     */
    public function __construct(private ContainerInterface $container)
    {
    }
    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        if (!$buildDir) {
            return [];
        }
        $router = $this->container->get('router');
        if ($router instanceof WarmableInterface) {
            return $router->warmUp($cacheDir, $buildDir);
        }
        throw new \LogicException(\sprintf('The router "%s" cannot be warmed up because it does not implement "%s".', get_debug_type($router), WarmableInterface::class));
    }
    public function isOptional(): bool
    {
        return \true;
    }
    public static function getSubscribedServices(): array
    {
        return ['router' => RouterInterface::class];
    }
}
