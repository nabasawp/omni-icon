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

use OmniIconDeps\Symfony\Component\Lock\LockFactory;
use OmniIconDeps\Symfony\Component\Lock\Serializer\LockKeyNormalizer;
use OmniIconDeps\Symfony\Component\Lock\Store\CombinedStore;
use OmniIconDeps\Symfony\Component\Lock\Strategy\ConsensusStrategy;
return static function (ContainerConfigurator $container) {
    $container->services()->set('lock.store.combined.abstract', CombinedStore::class)->abstract()->args([abstract_arg('List of stores'), service('lock.strategy.majority')])->set('lock.strategy.majority', ConsensusStrategy::class)->set('lock.factory.abstract', LockFactory::class)->abstract()->args([abstract_arg('Store')])->call('setLogger', [service('logger')->ignoreOnInvalid()])->tag('monolog.logger', ['channel' => 'lock'])->set('serializer.normalizer.lock_key', LockKeyNormalizer::class)->tag('serializer.normalizer', ['built_in' => \true, 'priority' => -880]);
};
