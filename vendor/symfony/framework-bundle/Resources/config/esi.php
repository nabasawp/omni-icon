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

use OmniIconDeps\Symfony\Component\HttpKernel\EventListener\SurrogateListener;
use OmniIconDeps\Symfony\Component\HttpKernel\HttpCache\Esi;
return static function (ContainerConfigurator $container) {
    $container->services()->set('esi', Esi::class)->set('esi_listener', SurrogateListener::class)->args([service('esi')->ignoreOnInvalid()])->tag('kernel.event_subscriber');
};
