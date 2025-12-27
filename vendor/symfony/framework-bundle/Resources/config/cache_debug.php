<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Component\DependencyInjection\Loader\Configurator;

use OmniIconDeps\Symfony\Bundle\FrameworkBundle\CacheWarmer\CachePoolClearerCacheWarmer;
use OmniIconDeps\Symfony\Component\Cache\DataCollector\CacheDataCollector;
return static function (ContainerConfigurator $container) {
    $container->services()->set('data_collector.cache', CacheDataCollector::class)->public()->tag('data_collector', ['template' => '@WebProfiler/Collector/cache.html.twig', 'id' => 'cache', 'priority' => 275])->set('cache_pool_clearer.cache_warmer', CachePoolClearerCacheWarmer::class)->args([service('cache.system_clearer'), ['cache.validator', 'cache.serializer']])->tag('kernel.cache_warmer', ['priority' => 64]);
};
